<?php
/**
 * Technical SEO essentials.
 *
 * Meta description, canonical, robots rules for filtered URLs, Open Graph and JSON-LD.
 * Automatically steps aside when a dedicated SEO plugin (Yoast, Rank Math, AIOSEO,
 * SEOPress, The SEO Framework) is active — only the theme-specific parts
 * (product schema extras, breadcrumbs markup, heading outline) stay on.
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a dedicated SEO plugin handling meta tags?
 *
 * @return bool
 */
function renosi_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * Plain-text, length-limited description.
 *
 * @param string $text Text/HTML.
 * @param int    $len  Max characters.
 * @return string
 */
function renosi_seo_clean( $text, $len = 160 ) {
	$text = preg_replace( '#</(p|li|h[1-6]|div|td|br)>|<br\s*/?>#i', '$0 ', strip_shortcodes( (string) $text ) );
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $text ) ) );
	if ( function_exists( 'mb_strlen' ) && mb_strlen( $text ) > $len ) {
		$text = rtrim( mb_substr( $text, 0, $len - 1 ) ) . '…';
	}
	return $text;
}

/**
 * Meta description for the current request.
 *
 * @return string
 */
function renosi_seo_description() {
	$desc = '';
	if ( is_front_page() ) {
		$desc = renosi_opt( 'hero_text' ) ? renosi_opt( 'hero_text' ) : get_bloginfo( 'description' );
	} elseif ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product ) {
			$desc = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
			if ( ! $desc ) {
				/* translators: 1: product, 2: site. */
				$desc = sprintf( __( 'خرید %1$s با ضمانت اصالت و ارسال سریع از %2$s.', 'renosi' ), $product->get_name(), get_bloginfo( 'name' ) );
			}
			if ( $product->get_sku() ) {
				/* translators: %s: SKU. */
				$desc = sprintf( __( 'کد فنی %s — ', 'renosi' ), $product->get_sku() ) . renosi_seo_clean( $desc, 400 );
			}
		}
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$desc = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
	} elseif ( is_tax( 'renosi_vehicle' ) ) {
		$term = get_queried_object();
		$desc = term_description( $term );
		if ( ! trim( wp_strip_all_tags( $desc ) ) ) {
			/* translators: 1: vehicle, 2: site. */
			$desc = sprintf( __( 'خرید قطعات یدکی %1$s؛ لنت، فیلتر، قطعات موتور، تعلیق و برقی با ضمانت اصالت، قیمت روز و ارسال سریع از %2$s.', 'renosi' ), $term->name, get_bloginfo( 'name' ) );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		$desc = term_description( $term );
		if ( ! trim( wp_strip_all_tags( $desc ) ) && isset( $term->name ) ) {
			/* translators: 1: term, 2: site. */
			$desc = sprintf( __( 'خرید %1$s با ضمانت اصالت، قیمت مناسب و ارسال سریع از فروشگاه %2$s.', 'renosi' ), $term->name, get_bloginfo( 'name' ) );
		}
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		$shop = get_post( wc_get_page_id( 'shop' ) );
		$desc = ( $shop && $shop->post_excerpt ) ? $shop->post_excerpt : ( $shop ? $shop->post_content : '' );
		if ( ! trim( wp_strip_all_tags( $desc ) ) ) {
			/* translators: %s: site. */
			$desc = sprintf( __( 'فروشگاه اینترنتی قطعات یدکی خودرو %s — جستجو بر اساس خودرو، کد فنی و دسته‌بندی.', 'renosi' ), get_bloginfo( 'name' ) );
		}
	} elseif ( is_home() ) {
		$desc = get_bloginfo( 'description' );
	}
	return renosi_seo_clean( $desc );
}

/**
 * Canonical URL for non-singular views (core handles singular).
 *
 * @return string
 */
function renosi_seo_canonical() {
	$url = '';
	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		$url  = is_wp_error( $link ) ? '' : $link;
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		$url = wc_get_page_permalink( 'shop' );
	} elseif ( is_home() ) {
		$url = get_permalink( get_option( 'page_for_posts' ) );
	} elseif ( is_author() ) {
		$url = get_author_posts_url( get_queried_object_id() );
	}
	if ( $url && is_paged() ) {
		$url = get_pagenum_link( get_query_var( 'paged' ), false );
		$url = strtok( $url, '?' );
	}
	return $url;
}

/**
 * Are we on a filtered / sorted listing that should not be indexed?
 *
 * @return bool
 */
function renosi_seo_is_filtered() {
	// phpcs:disable WordPress.Security.NonceVerification
	foreach ( array( 'orderby', 'min_price', 'max_price', 'express', 'rating_filter', 'product_view' ) as $param ) {
		if ( isset( $_GET[ $param ] ) ) {
			return true;
		}
	}
	foreach ( array_keys( $_GET ) as $key ) {
		if ( 0 === strpos( (string) $key, 'filter_' ) || 0 === strpos( (string) $key, 'query_type_' ) ) {
			return true;
		}
	}
	if ( isset( $_GET['vehicle'] ) || ( isset( $_GET['product_cat'] ) && is_tax( 'renosi_vehicle' ) ) ) {
		return true; // Combined filters duplicate the clean vehicle / category URLs.
	}
	// phpcs:enable
	return false;
}

/**
 * Robots directives.
 *
 * @param array $robots Robots.
 * @return array
 */
function renosi_seo_robots( $robots ) {
	if ( is_search() || renosi_seo_is_filtered() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	} elseif ( ! renosi_has_seo_plugin() && ! is_404() ) {
		$robots['max-image-preview'] = 'large';
	}
	return $robots;
}
add_filter( 'wp_robots', 'renosi_seo_robots', 20 );

/**
 * Best share image for the current request.
 *
 * @return string
 */
function renosi_seo_image() {
	$id = 0;
	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		$id = get_post_thumbnail_id( get_queried_object_id() );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		$id   = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ( ! $id && ! empty( $term->parent ) ) {
			$id = (int) get_term_meta( $term->parent, 'thumbnail_id', true );
		}
	}
	if ( ! $id && is_front_page() ) {
		$id = (int) renosi_opt( 'slide_1_image' );
	}
	if ( ! $id ) {
		$id = (int) get_theme_mod( 'custom_logo' );
	}
	$url = $id ? wp_get_attachment_image_url( $id, 'large' ) : '';
	if ( ! $url && get_site_icon_url() ) {
		$url = get_site_icon_url( 512 );
	}
	return $url ? $url : '';
}

/**
 * Head tags (only without an SEO plugin).
 */
function renosi_seo_head() {
	if ( renosi_has_seo_plugin() || is_404() ) {
		return;
	}
	$desc  = renosi_seo_description();
	$canon = is_singular() ? '' : renosi_seo_canonical();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : ( $canon ? $canon : home_url( add_query_arg( array() ) ) );
	$image = renosi_seo_image();
	$type  = 'website';
	if ( function_exists( 'is_product' ) && is_product() ) {
		$type = 'product';
	} elseif ( is_singular( 'post' ) ) {
		$type = 'article';
	}

	echo "\n<!-- Renosi SEO -->\n";
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $canon && ! renosi_seo_is_filtered() && ! is_search() ) {
		echo '<link rel="canonical" href="' . esc_url( $canon ) . '">' . "\n";
	}
	echo '<meta property="og:locale" content="' . esc_attr( str_replace( '-', '_', get_locale() ) ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	if ( 'product' === $type ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product && '' !== $product->get_price() ) {
			echo '<meta property="product:price:amount" content="' . esc_attr( wc_get_price_to_display( $product ) ) . '">' . "\n";
			echo '<meta property="product:price:currency" content="' . esc_attr( get_woocommerce_currency() ) . '">' . "\n";
			echo '<meta property="product:availability" content="' . ( $product->is_in_stock() ? 'instock' : 'oos' ) . '">' . "\n";
		}
	}
	if ( 'article' === $type ) {
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( DATE_W3C ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( DATE_W3C ) ) . '">' . "\n";
	}
	echo "<!-- /Renosi SEO -->\n";
}
add_action( 'wp_head', 'renosi_seo_head', 3 );

/**
 * Document title parts: «قطعات یدکی تندر ۹۰ | رنوسی».
 *
 * @param array $parts Parts.
 * @return array
 */
function renosi_seo_title_parts( $parts ) {
	if ( renosi_has_seo_plugin() ) {
		return $parts;
	}
	if ( is_tax( 'renosi_vehicle' ) ) {
		$parts['title'] = renosi_vehicle_title( get_queried_object() );
	} elseif ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product && $product->get_sku() ) {
			$parts['title'] = $product->get_name() . ' (' . $product->get_sku() . ')';
		}
	} elseif ( is_tax( 'product_cat' ) ) {
		/* translators: %s: category. */
		$parts['title'] = sprintf( __( 'خرید %s', 'renosi' ), single_term_title( '', false ) );
	}
	return $parts;
}
add_filter( 'document_title_parts', 'renosi_seo_title_parts' );

/**
 * Print a JSON-LD block.
 *
 * @param array $data Data.
 */
function renosi_jsonld( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * Organization (AutoPartsStore) + WebSite/SearchAction on the front page;
 * BlogPosting on posts.
 */
function renosi_seo_jsonld() {
	if ( renosi_has_seo_plugin() ) {
		return;
	}
	if ( is_front_page() ) {
		$same = array();
		foreach ( array( 'instagram', 'telegram', 'whatsapp', 'eitaa', 'aparat' ) as $net ) {
			if ( renosi_opt( 'social_' . $net ) ) {
				$same[] = renosi_opt( 'social_' . $net );
			}
		}
		$store = array(
			'@context'  => 'https://schema.org',
			'@type'     => 'AutoPartsStore',
			'@id'       => home_url( '/#store' ),
			'name'      => get_bloginfo( 'name' ),
			'url'       => home_url( '/' ),
			'telephone' => renosi_opt( 'phone' ),
			'email'     => renosi_opt( 'email' ),
		);
		if ( renosi_opt( 'address' ) ) {
			$store['address'] = array(
				'@type'          => 'PostalAddress',
				'streetAddress'  => renosi_opt( 'address' ),
				'addressCountry' => 'IR',
			);
		}
		$logo = (int) get_theme_mod( 'custom_logo' );
		if ( $logo ) {
			$store['logo']  = wp_get_attachment_image_url( $logo, 'full' );
			$store['image'] = $store['logo'];
		}
		if ( $same ) {
			$store['sameAs'] = $same;
		}
		renosi_jsonld( array_filter( $store ) );

		$search_target = class_exists( 'WooCommerce' ) ? home_url( '/?s={search_term_string}&post_type=product' ) : home_url( '/?s={search_term_string}' );
		renosi_jsonld(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'WebSite',
				'@id'             => home_url( '/#website' ),
				'url'             => home_url( '/' ),
				'name'            => get_bloginfo( 'name' ),
				'inLanguage'      => get_bloginfo( 'language' ),
				'publisher'       => array( '@id' => home_url( '/#store' ) ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => array(
						'@type'       => 'EntryPoint',
						'urlTemplate' => $search_target,
					),
					'query-input' => 'required name=search_term_string',
				),
			)
		);
	} elseif ( is_singular( 'post' ) ) {
		$post = get_queried_object();
		$data = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'BlogPosting',
			'headline'         => get_the_title( $post ),
			'datePublished'    => get_the_date( DATE_W3C, $post ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', $post->post_author ),
			),
			'publisher'        => array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
			),
			'mainEntityOfPage' => get_permalink( $post ),
			'description'      => renosi_seo_description(),
		);
		if ( has_post_thumbnail( $post ) ) {
			$data['image'] = get_the_post_thumbnail_url( $post, 'large' );
		}
		renosi_jsonld( $data );
	}
}
add_action( 'wp_head', 'renosi_seo_jsonld', 30 );

/**
 * BreadcrumbList for theme breadcrumbs (WooCommerce adds its own on shop pages).
 *
 * @param array $crumbs Array of [name, url].
 */
function renosi_breadcrumb_jsonld( $crumbs ) {
	if ( renosi_has_seo_plugin() || count( $crumbs ) < 2 ) {
		return;
	}
	$items = array();
	foreach ( array_values( $crumbs ) as $i => $crumb ) {
		$item = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb[0],
		);
		if ( ! empty( $crumb[1] ) ) {
			$item['item'] = $crumb[1];
		}
		$items[] = $item;
	}
	renosi_jsonld(
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		)
	);
}
