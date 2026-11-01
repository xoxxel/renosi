<?php
/**
 * Front-end performance: lean <head>, non-blocking scripts, right-sized images.
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove the emoji detection script & styles (modern browsers render emoji natively).
 */
function renosi_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter(
		'wp_resource_hints',
		function ( $urls, $relation ) {
			if ( 'dns-prefetch' === $relation ) {
				$urls = array_filter(
					$urls,
					function ( $url ) {
						return false === strpos( is_array( $url ) ? ( $url['href'] ?? '' ) : $url, 's.w.org' );
					}
				);
			}
			return $urls;
		},
		10,
		2
	);
}
add_action( 'init', 'renosi_disable_emoji' );

/**
 * Head cleanup: generator tag, RSD/WLW and shortlink add bytes but no SEO value.
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * Print jQuery — and every queued script that needs it — in the footer instead of <head>,
 * so nothing blocks the first render. Execution order is unchanged.
 * Turn off in the Customizer if an old plugin prints inline jQuery code inside the page.
 */
function renosi_jquery_to_footer() {
	if ( is_admin() || is_customize_preview() || ! renosi_opt( 'perf_jquery_footer' ) ) {
		return;
	}
	$scripts = wp_scripts();
	$needs   = function ( $handle, $seen = array() ) use ( &$needs, $scripts ) {
		if ( in_array( $handle, array( 'jquery', 'jquery-core' ), true ) ) {
			return true;
		}
		if ( isset( $seen[ $handle ] ) || empty( $scripts->registered[ $handle ] ) ) {
			return false;
		}
		$seen[ $handle ] = true;
		foreach ( (array) $scripts->registered[ $handle ]->deps as $dep ) {
			if ( $needs( $dep, $seen ) ) {
				return true;
			}
		}
		return false;
	};
	foreach ( array( 'jquery', 'jquery-core' ) as $handle ) {
		if ( isset( $scripts->registered[ $handle ] ) ) {
			$scripts->add_data( $handle, 'group', 1 );
		}
	}
	// Move each jQuery-dependent script together with its whole dependency chain.
	$move = function ( $handle ) use ( &$move, $scripts ) {
		if ( empty( $scripts->registered[ $handle ] ) || ! empty( $scripts->registered[ $handle ]->extra['renosi_moved'] ) ) {
			return;
		}
		$scripts->registered[ $handle ]->extra['renosi_moved'] = true;
		$scripts->add_data( $handle, 'group', 1 );
		foreach ( (array) $scripts->registered[ $handle ]->deps as $dep ) {
			$move( $dep );
		}
	};
	foreach ( (array) $scripts->queue as $handle ) {
		if ( $needs( $handle ) ) {
			$move( $handle );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'renosi_jquery_to_footer', 999 );
add_action( 'wp_print_scripts', 'renosi_jquery_to_footer', 1 );

/**
 * jquery-migrate is only needed by very old plugins.
 *
 * @param WP_Scripts $scripts Scripts.
 */
function renosi_drop_jquery_migrate( $scripts ) {
	if ( is_admin() || ! renosi_opt( 'perf_jquery_footer' ) || empty( $scripts->registered['jquery'] ) ) {
		return;
	}
	$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
}
add_action( 'wp_default_scripts', 'renosi_drop_jquery_migrate' );

/**
 * Responsive "sizes" for product cards and category tiles, so phones download
 * a small rendition instead of the full thumbnail.
 *
 * @param array        $attr       Attributes.
 * @param WP_Post      $attachment Attachment.
 * @param string|array $size       Size.
 * @return array
 */
function renosi_image_sizes_attr( $attr, $attachment, $size ) {
	if ( is_admin() ) {
		return $attr;
	}
	if ( 'woocommerce_thumbnail' === $size ) {
		$attr['sizes'] = '(min-width: 1100px) 300px, (min-width: 768px) 30vw, 46vw';
	} elseif ( 'woocommerce_single' === $size ) {
		$attr['sizes'] = '(min-width: 900px) 620px, 100vw';
	} elseif ( 'renosi-card' === $size ) {
		$attr['sizes'] = '(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw';
	}
	if ( ! isset( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'renosi_image_sizes_attr', 20, 3 );

/**
 * Generate WebP sub-sizes for new JPEG/PNG uploads (WordPress falls back to the
 * original format automatically when the server's image library lacks WebP).
 *
 * @param array $formats Mime → output mime map.
 * @return array
 */
function renosi_webp_subsizes( $formats ) {
	if ( ! renosi_opt( 'perf_webp' ) ) {
		return $formats;
	}
	$formats['image/jpeg'] = 'image/webp';
	$formats['image/png']  = 'image/webp';
	return $formats;
}
add_filter( 'image_editor_output_format', 'renosi_webp_subsizes' );
