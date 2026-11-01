<?php
/**
 * Template tags.
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Site logo or name.
 */
function renosi_site_branding() {
	echo '<div class="site-branding">';
	if ( has_custom_logo() ) {
		the_custom_logo();
	} else {
		$tag = 'p'; // The page's own H1 lives in the content (hero / page title).
		printf(
			'<%1$s class="site-title"><a href="%2$s" rel="home"><span class="site-title__mark" aria-hidden="true">%3$s</span><span class="site-title__text">%4$s</span></a></%1$s>',
			esc_attr( $tag ),
			esc_url( home_url( '/' ) ),
			renosi_icon( 'part', 22 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( get_bloginfo( 'name' ) )
		);
	}
	echo '</div>';
}

/**
 * Telephone href from a human-readable number.
 *
 * @param string $number Number.
 * @return string
 */
function renosi_tel( $number ) {
	$fa  = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	$en  = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$num = str_replace( $fa, $en, $number );
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $num );
}

/**
 * Social links list.
 *
 * @param string $class Wrapper class.
 */
function renosi_social_links( $class = 'social-links' ) {
	$networks = array(
		'instagram' => array( __( 'اینستاگرام', 'renosi' ), 'instagram' ),
		'telegram'  => array( __( 'تلگرام', 'renosi' ), 'telegram' ),
		'whatsapp'  => array( __( 'واتساپ', 'renosi' ), 'whatsapp' ),
		'eitaa'     => array( __( 'ایتا', 'renosi' ), 'chat' ),
		'aparat'    => array( __( 'آپارات', 'renosi' ), 'play' ),
	);
	$items = '';
	foreach ( $networks as $key => $net ) {
		$url = renosi_opt( 'social_' . $key );
		if ( ! $url ) {
			continue;
		}
		$items .= sprintf(
			'<li><a href="%1$s" target="_blank" rel="noopener nofollow" aria-label="%2$s">%3$s</a></li>',
			esc_url( $url ),
			esc_attr( $net[0] ),
			renosi_icon( $net[1], 20 )
		);
	}
	if ( $items ) {
		echo '<ul class="' . esc_attr( $class ) . '">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Section heading with an optional "view all" link.
 *
 * @param string $title Title.
 * @param string $url   Link URL.
 * @param string $label Link label.
 */
function renosi_section_head( $title, $url = '', $label = '' ) {
	echo '<div class="section-head">';
	echo '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
	if ( $url ) {
		printf(
			'<a class="section-link" href="%1$s">%2$s %3$s</a>',
			esc_url( $url ),
			esc_html( $label ? $label : __( 'مشاهده‌ی همه', 'renosi' ) ),
			renosi_icon( 'chevron-left', 16 ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
	echo '</div>';
}

if ( ! function_exists( 'renosi_posted_on' ) ) :
	/**
	 * Post date.
	 */
	function renosi_posted_on() {
		printf(
			'<span class="posted-on">%1$s<time class="entry-date published" datetime="%2$s">%3$s</time></span>',
			renosi_icon( 'clock', 15 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);
	}
endif;

if ( ! function_exists( 'renosi_posted_by' ) ) :
	/**
	 * Post author.
	 */
	function renosi_posted_by() {
		if ( '' === trim( (string) get_the_author() ) ) {
			return;
		}
		printf(
			'<span class="byline">%1$s<a class="url fn n" href="%2$s">%3$s</a></span>',
			renosi_icon( 'user', 15 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}
endif;

if ( ! function_exists( 'renosi_entry_footer' ) ) :
	/**
	 * Categories & tags.
	 */
	function renosi_entry_footer() {
		if ( 'post' !== get_post_type() ) {
			return;
		}
		$cats = get_the_category_list( '' );
		$tags = get_the_tag_list( '', '' );
		if ( $cats || $tags ) {
			echo '<div class="entry-terms">';
			if ( $cats ) {
				echo '<div class="chip-list">' . $cats . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( $tags ) {
				echo '<div class="chip-list chip-list--muted">' . $tags . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</div>';
		}
		edit_post_link( esc_html__( 'ویرایش', 'renosi' ), '<span class="edit-link">', '</span>' );
	}
endif;

/**
 * Numbered pagination.
 */
function renosi_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 1,
			'prev_text' => renosi_icon( 'chevron-right', 18 ) . '<span class="screen-reader-text">' . esc_html__( 'قبلی', 'renosi' ) . '</span>',
			'next_text' => renosi_icon( 'chevron-left', 18 ) . '<span class="screen-reader-text">' . esc_html__( 'بعدی', 'renosi' ) . '</span>',
		)
	);
}

/**
 * Breadcrumbs for non-shop pages (with BreadcrumbList JSON-LD).
 */
function renosi_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		return; // WooCommerce prints its own (with structured data).
	}
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'مسیر صفحه', 'renosi' ) . '">', '</nav>' );
		return;
	}
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		rank_math_the_breadcrumbs();
		return;
	}

	$crumbs = array( array( __( 'خانه', 'renosi' ), home_url( '/' ) ) );

	if ( is_home() ) {
		$blog     = get_option( 'page_for_posts' );
		$crumbs[] = array( $blog ? get_the_title( $blog ) : __( 'وبلاگ', 'renosi' ), '' );
	} elseif ( is_singular( 'post' ) ) {
		$blog = get_option( 'page_for_posts' );
		if ( $blog ) {
			$crumbs[] = array( get_the_title( $blog ), get_permalink( $blog ) );
		}
		$cats = get_the_category();
		if ( $cats ) {
			$crumbs[] = array( $cats[0]->name, get_category_link( $cats[0] ) );
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$crumbs[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_search() ) {
		$crumbs[] = array( __( 'جستجو', 'renosi' ), '' );
	} elseif ( is_404() ) {
		$crumbs[] = array( __( 'صفحه پیدا نشد', 'renosi' ), '' );
	} elseif ( is_archive() ) {
		$crumbs[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
	}

	$sep   = '<span class="breadcrumbs__sep" aria-hidden="true">/</span>';
	$parts = array();
	foreach ( $crumbs as $crumb ) {
		$parts[] = $crumb[1] ? '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>' : '<span aria-current="page">' . esc_html( $crumb[0] ) . '</span>';
	}
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'مسیر صفحه', 'renosi' ) . '">' . implode( $sep, $parts ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	if ( function_exists( 'renosi_breadcrumb_jsonld' ) ) {
		$last = count( $crumbs ) - 1;
		if ( ! $crumbs[ $last ][1] ) {
			$crumbs[ $last ][1] = is_singular() ? get_permalink() : '';
		}
		renosi_breadcrumb_jsonld( $crumbs );
	}
}

/**
 * Header cart button (refreshed via WooCommerce fragments).
 */
function renosi_header_cart_button() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}
	$count = WC()->cart->get_cart_contents_count();
	?>
	<a class="header-action header-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-drawer-open="cart-drawer" aria-label="<?php esc_attr_e( 'سبد خرید', 'renosi' ); ?>">
		<?php renosi_the_icon( 'bag', 22 ); ?>
		<span class="cart-count<?php echo $count ? '' : ' is-empty'; ?>"><?php echo esc_html( $count ); ?></span>
	</a>
	<?php
}

/**
 * Decorative brake disc illustration (used in default slides).
 *
 * @return string
 */
function renosi_disc_svg() {
	$holes = '';
	for ( $i = 0; $i < 36; $i++ ) {
		$a      = deg2rad( $i * 10 );
		$r      = ( 0 === $i % 2 ) ? 128 : 108;
		$holes .= sprintf( '<circle cx="%.1f" cy="%.1f" r="4.2"/>', 200 + cos( $a ) * $r, 200 + sin( $a ) * $r );
	}
	$lugs = '';
	for ( $i = 0; $i < 5; $i++ ) {
		$a     = deg2rad( -90 + $i * 72 );
		$lugs .= sprintf( '<circle cx="%.1f" cy="%.1f" r="7"/>', 200 + cos( $a ) * 40, 200 + sin( $a ) * 40 );
	}
	return '<svg class="slide-art slide-art--disc" viewBox="0 0 400 400" aria-hidden="true" focusable="false">
		<defs>
			<radialGradient id="rDisc" cx="50%" cy="42%" r="62%"><stop offset="0" stop-color="#f4f4f6"/><stop offset=".7" stop-color="#bdbdc5"/><stop offset="1" stop-color="#7f7f8a"/></radialGradient>
			<radialGradient id="rHub" cx="45%" cy="40%" r="70%"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#8a8a94"/></radialGradient>
		</defs>
		<ellipse cx="200" cy="384" rx="140" ry="10" fill="#000" opacity=".3"/>
		<circle cx="200" cy="200" r="168" fill="url(#rDisc)"/>
		<circle cx="200" cy="200" r="150" fill="none" stroke="#fff" stroke-opacity=".45" stroke-width="1.5"/>
		<g fill="#5a5a64" fill-opacity=".6">' . $holes . '</g>
		<circle cx="200" cy="200" r="72" fill="#a6a6ae"/><circle cx="200" cy="200" r="64" fill="url(#rHub)"/>
		<g fill="#3a3a42">' . $lugs . '</g>
		<circle cx="200" cy="200" r="20" fill="#e8e8ed" stroke="#6e6e78" stroke-width="2"/>
		<path d="M318 64a190 190 0 0 1 58 120l-46 6a144 144 0 0 0-44-92Z" fill="var(--r-accent-vivid)"/>
		<path d="M326 92a150 150 0 0 1 30 62" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="5" stroke-linecap="round"/>
	</svg>';
}
