<?php
/**
 * Hooks that enhance templates.
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function renosi_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
	if ( renosi_opt( 'sticky_header' ) ) {
		$classes[] = 'has-sticky-header';
	}
	if ( renosi_opt( 'mobile_bottom_nav' ) ) {
		$classes[] = 'has-bottom-nav';
	}
	$has_sidebar = is_active_sidebar( 'sidebar-1' ) && ( is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date() );
	if ( $has_sidebar ) {
		$classes[] = 'has-sidebar';
	}
	return $classes;
}
add_filter( 'body_class', 'renosi_body_classes' );

/**
 * Pingback header.
 */
function renosi_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'renosi_pingback_header' );

/**
 * Theme colour for mobile browsers.
 */
function renosi_meta_theme_color() {
	echo '<meta name="theme-color" content="#ffffff">' . "\n";
}
add_action( 'wp_head', 'renosi_meta_theme_color', 2 );

/**
 * Excerpt length.
 *
 * @return int
 */
function renosi_excerpt_length() {
	return 22;
}
add_filter( 'excerpt_length', 'renosi_excerpt_length' );

/**
 * Excerpt more.
 *
 * @return string
 */
function renosi_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'renosi_excerpt_more' );

/**
 * Remove "Category:" etc. prefixes from archive titles.
 *
 * @param string $prefix Prefix.
 * @return string
 */
function renosi_archive_title_prefix( $prefix ) {
	return '';
}
add_filter( 'get_the_archive_title_prefix', 'renosi_archive_title_prefix' );

/**
 * Fallback for the primary menu when none is assigned.
 *
 * @param array $args Menu args.
 */
function renosi_menu_fallback( $args ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		echo '<ul class="' . esc_attr( $args['menu_class'] ) . '"><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'خانه', 'renosi' ) . '</a></li></ul>';
		return;
	}
	echo '<ul class="' . esc_attr( $args['menu_class'] ) . '">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'خانه', 'renosi' ) . '</a></li>';
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'فروشگاه', 'renosi' ) . '</a></li>';
	}
	echo '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( '+ ساخت منو', 'renosi' ) . '</a></li>';
	echo '</ul>';
}

/**
 * Add a chevron to menu items that have children.
 *
 * @param string   $title Title.
 * @param WP_Post  $item  Item.
 * @param stdClass $args  Args.
 * @param int      $depth Depth.
 * @return string
 */
function renosi_menu_item_chevron( $title, $item, $args, $depth ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location && 0 === $depth && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		$title .= renosi_icon( 'chevron-down', 14, 'menu-chevron' );
	}
	return $title;
}
add_filter( 'nav_menu_item_title', 'renosi_menu_item_chevron', 10, 4 );

/**
 * Wrap embeds for responsive behaviour handled by core; widen tag cloud.
 *
 * @param array $args Args.
 * @return array
 */
function renosi_tag_cloud_args( $args ) {
	$args['smallest'] = 13;
	$args['largest']  = 13;
	$args['unit']     = 'px';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'renosi_tag_cloud_args' );
add_filter( 'woocommerce_product_tag_cloud_widget_args', 'renosi_tag_cloud_args' );
