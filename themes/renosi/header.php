<?php
/**
 * Site header.
 *
 * @package renosi
 */

$renosi_has_wc = class_exists( 'WooCommerce' );
$renosi_phone  = renosi_opt( 'phone' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'رفتن به محتوا', 'renosi' ); ?></a>

	<?php if ( renosi_opt( 'topbar_show' ) ) : ?>
		<div class="topbar">
			<div class="container topbar__inner">
				<p class="topbar__text"><?php echo esc_html( renosi_opt( 'topbar_text' ) ); ?></p>
				<div class="topbar__end">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'topbar',
							'container'      => false,
							'menu_class'     => 'topbar__menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
					<?php if ( $renosi_phone ) : ?>
						<a class="topbar__phone" href="<?php echo esc_url( renosi_tel( $renosi_phone ) ); ?>"><?php renosi_the_icon( 'phone', 14 ); ?><span dir="ltr"><?php echo esc_html( $renosi_phone ); ?></span></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<header id="masthead" class="site-header">
		<div class="container header-main">
			<button type="button" class="icon-button header-menu-toggle" data-drawer-open="menu-drawer" aria-controls="menu-drawer" aria-expanded="false" aria-label="<?php esc_attr_e( 'باز کردن منو', 'renosi' ); ?>">
				<?php renosi_the_icon( 'menu', 22 ); ?>
			</button>

			<?php renosi_site_branding(); ?>

			<div class="header-search">
				<?php get_template_part( 'template-parts/header/search-form' ); ?>
			</div>

			<div class="header-actions">
				<?php if ( $renosi_phone ) : ?>
					<a class="header-contact" href="<?php echo esc_url( renosi_tel( $renosi_phone ) ); ?>">
						<?php renosi_the_icon( 'headset', 22 ); ?>
						<span class="header-contact__text">
							<small><?php esc_html_e( 'مشاوره و خرید تلفنی', 'renosi' ); ?></small>
							<strong dir="ltr"><?php echo esc_html( $renosi_phone ); ?></strong>
						</span>
					</a>
				<?php endif; ?>
				<?php if ( $renosi_has_wc ) : ?>
					<a class="header-action header-account" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'حساب کاربری', 'renosi' ); ?>">
						<?php renosi_the_icon( 'user', 22 ); ?>
						<span class="header-action__label">
							<?php echo is_user_logged_in() ? esc_html__( 'حساب من', 'renosi' ) : esc_html__( 'ورود | ثبت‌نام', 'renosi' ); ?>
						</span>
					</a>
					<?php renosi_header_cart_button(); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="header-nav">
			<div class="container header-nav__inner">
				<?php if ( $renosi_has_wc ) : ?>
					<?php get_template_part( 'template-parts/header/categories-menu' ); ?>
				<?php endif; ?>
				<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'منوی اصلی', 'renosi' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'menu',
							'depth'          => 2,
							'fallback_cb'    => 'renosi_menu_fallback',
						)
					);
					?>
				</nav>
				<?php if ( $renosi_has_wc ) : ?>
					<div class="mycar-pill" data-mycar hidden>
						<a data-mycar-link href="#"><?php renosi_the_icon( 'car', 16 ); ?><span><?php esc_html_e( 'خودروی من:', 'renosi' ); ?></span> <strong data-mycar-name></strong></a>
						<button type="button" data-mycar-clear aria-label="<?php esc_attr_e( 'حذف خودروی من', 'renosi' ); ?>"><?php renosi_the_icon( 'close', 14 ); ?></button>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<div id="site-content" class="site-content">
