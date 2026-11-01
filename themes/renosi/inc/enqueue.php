<?php
/**
 * Styles & scripts.
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Darken / lighten a hex color.
 *
 * @param string $hex    Hex color.
 * @param float  $amount -1..1 (negative darkens).
 * @return string
 */
function renosi_shade( $hex, $amount ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$out = '#';
	for ( $i = 0; $i < 3; $i++ ) {
		$c = hexdec( substr( $hex, $i * 2, 2 ) );
		$c = $amount < 0 ? $c * ( 1 + $amount ) : $c + ( 255 - $c ) * $amount;
		$out .= str_pad( dechex( (int) round( max( 0, min( 255, $c ) ) ) ), 2, '0', STR_PAD_LEFT );
	}
	return $out;
}

/**
 * @font-face rules for the bundled Vazirmatn font.
 *
 * @return string
 */
function renosi_font_face_css() {
	$base = RENOSI_URI . '/assets/fonts/';
	// One variable file covers every weight (Persian + Latin).
	$css = "@font-face{font-family:'Vazirmatn';src:url('{$base}Vazirmatn-Variable.woff2') format('woff2');font-weight:100 900;font-style:normal;font-display:swap;}";
	if ( renosi_opt( 'persian_digits' ) ) {
		// Tiny digit-only faces (≈4 KB each) override 0–9 with Persian numerals.
		// A separate family listed first in the font stack, so weight matching is exact.
		$weights = array(
			400 => 'Regular',
			500 => 'Medium',
			600 => 'SemiBold',
			700 => 'Bold',
			800 => 'ExtraBold',
		);
		foreach ( $weights as $w => $name ) {
			$css .= "@font-face{font-family:'Vazirmatn Digits';src:url('{$base}Vazirmatn-Digits-{$name}.woff2') format('woff2');font-weight:{$w};font-style:normal;font-display:swap;unicode-range:U+0030-0039;}";
		}
		$css .= ":root{--r-font:'Vazirmatn Digits','Vazirmatn',system-ui,-apple-system,'Segoe UI',Tahoma,sans-serif;}";
	}
	return $css;
}

/**
 * Inline CSS custom properties driven by the customizer.
 *
 * @return string
 */
function renosi_dynamic_css() {
	$accent = sanitize_hex_color( renosi_opt( 'accent_color' ) );
	if ( ! $accent ) {
		$accent = '#c94f0a';
	}
	// Accessible accent (white text ≥ 4.5:1) for buttons; a brighter tone for purely decorative marks.
	$vivid = ( '#c94f0a' === strtolower( $accent ) ) ? '#f26a1b' : $accent;
	$css  = renosi_font_face_css();
	$css .= ':root{';
	$css .= '--r-accent:' . $accent . ';';
	$css .= '--r-accent-hover:' . renosi_shade( $accent, -0.14 ) . ';';
	$css .= '--r-accent-soft:' . renosi_shade( $accent, 0.92 ) . ';';
	$css .= '--r-accent-vivid:' . $vivid . ';';
	$css .= '}';
	return $css;
}

/**
 * URL of an asset, preferring the minified build when it is up to date.
 * If you edit the readable file, it becomes newer than the .min file and is used automatically.
 *
 * @param string $path Relative path, e.g. 'assets/css/main.css'.
 * @return string
 */
function renosi_asset( $path ) {
	if ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
		$min = preg_replace( '/\.(css|js)$/', '.min.$1', $path );
		$src = RENOSI_DIR . '/' . $path;
		$mf  = RENOSI_DIR . '/' . $min;
		if ( file_exists( $mf ) && ( ! file_exists( $src ) || filemtime( $mf ) >= filemtime( $src ) ) ) {
			return RENOSI_URI . '/' . $min;
		}
	}
	return RENOSI_URI . '/' . $path;
}

/**
 * Is this a WooCommerce screen (needs the full shop stylesheets)?
 *
 * @return bool
 */
function renosi_is_wc_page() {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}
	return is_woocommerce() || is_cart() || is_checkout() || is_account_page() || ( is_search() && 'product' === get_query_var( 'post_type' ) );
}

/**
 * Outside shop pages (home, blog, plain pages) WooCommerce's own stylesheets are not
 * needed — the theme styles product cards and the mini-cart itself. Saves ~18 KB of
 * render-blocking CSS on the home page.
 *
 * @param array $styles Styles.
 * @return array
 */
function renosi_wc_enqueue_styles( $styles ) {
	if ( is_admin() || renosi_is_wc_page() ) {
		return $styles;
	}
	global $post;
	if ( $post instanceof WP_Post && ( has_shortcode( $post->post_content, 'woocommerce_cart' ) || has_shortcode( $post->post_content, 'woocommerce_checkout' ) || has_shortcode( $post->post_content, 'woocommerce_my_account' ) ) ) {
		return $styles;
	}
	return array();
}
add_filter( 'woocommerce_enqueue_styles', 'renosi_wc_enqueue_styles' );

/**
 * Front-end assets.
 */
function renosi_scripts() {
	$ver = RENOSI_VERSION;

	wp_enqueue_style( 'renosi-main', renosi_asset( 'assets/css/main.css' ), array(), $ver );
	wp_add_inline_style( 'renosi-main', renosi_dynamic_css() );

	if ( class_exists( 'WooCommerce' ) ) {
		$deps = array( 'renosi-main' );
		foreach ( array( 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen' ) as $handle ) {
			if ( wp_style_is( $handle, 'enqueued' ) ) {
				$deps[] = $handle;
			}
		}
		wp_enqueue_style( 'renosi-woocommerce', renosi_asset( 'assets/css/woocommerce.css' ), $deps, $ver );
		if ( renosi_is_wc_page() ) {
			wp_enqueue_style( 'renosi-woocommerce-pages', renosi_asset( 'assets/css/woocommerce-pages.css' ), array( 'renosi-woocommerce' ), $ver );
		}
	}

	wp_enqueue_script( 'renosi-theme', renosi_asset( 'assets/js/theme.js' ), array(), $ver, true );
	wp_script_add_data( 'renosi-theme', 'strategy', 'defer' );

	if ( class_exists( 'WooCommerce' ) ) {
		// Keep the header cart badge & mini-cart drawer in sync everywhere.
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'renosi_scripts', 20 );

/**
 * Preload the primary font file.
 */
function renosi_preload_font() {
	$file = 'Vazirmatn-Variable.woff2';
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( RENOSI_URI . '/assets/fonts/' . $file )
	);
}
add_action( 'wp_head', 'renosi_preload_font', 1 );

/**
 * Block editor: font + accent.
 */
function renosi_editor_assets() {
	wp_add_inline_style( 'wp-block-library', renosi_dynamic_css() );
}
add_action( 'enqueue_block_editor_assets', 'renosi_editor_assets' );

/**
 * Pass the font-face into the editor iframe as well.
 *
 * @param array $settings Editor settings.
 * @return array
 */
function renosi_block_editor_settings( $settings ) {
	$settings['styles'][] = array( 'css' => renosi_dynamic_css() );
	return $settings;
}
add_filter( 'block_editor_settings_all', 'renosi_block_editor_settings' );

/**
 * Live preview script for the customizer.
 */
function renosi_customize_preview_js() {
	wp_enqueue_script( 'renosi-customizer', RENOSI_URI . '/assets/js/customizer.js', array( 'customize-preview' ), RENOSI_VERSION, true );
}
add_action( 'customize_preview_init', 'renosi_customize_preview_js' );
