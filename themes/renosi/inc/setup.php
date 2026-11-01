<?php
/**
 * Theme setup: supports, menus, widget areas.
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme supports and menus.
 */
function renosi_setup() {
	load_theme_textdomain( 'renosi', RENOSI_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	add_editor_style( array( 'assets/css/editor.css' ) );

	add_image_size( 'renosi-card', 640, 400, true );

	register_nav_menus(
		array(
			'primary'  => esc_html__( 'منوی اصلی', 'renosi' ),
			'topbar'   => esc_html__( 'منوی نوار بالا', 'renosi' ),
			'footer-1' => esc_html__( 'فوتر — ستون اول', 'renosi' ),
			'footer-2' => esc_html__( 'فوتر — ستون دوم', 'renosi' ),
		)
	);
}
add_action( 'after_setup_theme', 'renosi_setup' );

/**
 * Content width.
 */
function renosi_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'renosi_content_width', 820 );
}
add_action( 'after_setup_theme', 'renosi_content_width', 0 );

/**
 * Widget areas.
 */
function renosi_widgets_init() {
	$defaults = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array_merge(
			$defaults,
			array(
				'name'        => esc_html__( 'سایدبار وبلاگ', 'renosi' ),
				'id'          => 'sidebar-1',
				'description' => esc_html__( 'ابزارک‌های نوشته‌ها و برگه‌های وبلاگ.', 'renosi' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$defaults,
			array(
				'name'          => esc_html__( 'سایدبار فروشگاه (فیلترها)', 'renosi' ),
				'id'            => 'sidebar-shop',
				'description'   => esc_html__( 'فیلترهای محصولات در صفحه‌ی فروشگاه و دسته‌بندی‌ها. در موبایل به صورت پنل کشویی نمایش داده می‌شود.', 'renosi' ),
				// Head/body wrappers give every filter a consistent, collapsible card.
				'before_widget' => '<section id="%1$s" class="widget filter-card %2$s"><div class="filter-card__body">',
				'after_widget'  => '</div></section>',
				'before_title'  => '</div><div class="filter-card__head"><h2 class="widget-title">',
				'after_title'   => '</h2></div><div class="filter-card__body">',
			)
		)
	);

	register_sidebar(
		array_merge(
			$defaults,
			array(
				'name'          => esc_html__( 'فوتر — نمادهای اعتماد', 'renosi' ),
				'id'            => 'footer-badges',
				'description'   => esc_html__( 'کد نماد اعتماد الکترونیکی (اینماد)، ساماندهی و ... را با ابزارک «HTML سفارشی» اینجا قرار دهید.', 'renosi' ),
				'before_widget' => '<div id="%1$s" class="badge-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="screen-reader-text">',
				'after_title'   => '</h2>',
			)
		)
	);
}
add_action( 'widgets_init', 'renosi_widgets_init' );
