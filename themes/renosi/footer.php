<?php
/**
 * Site footer, drawers and mobile bottom navigation.
 *
 * @package renosi
 */

$renosi_has_wc = class_exists( 'WooCommerce' );
?>
	</div><!-- #site-content -->

	<footer id="colophon" class="site-footer">
		<div class="container footer-grid">
			<div class="footer-col footer-col--about">
				<?php renosi_site_branding(); ?>
				<p class="footer-about"><?php echo esc_html( renosi_opt( 'about' ) ); ?></p>
				<?php renosi_social_links(); ?>
			</div>

			<?php foreach ( array( 'footer-1', 'footer-2' ) as $renosi_loc ) : ?>
				<?php if ( has_nav_menu( $renosi_loc ) ) : ?>
					<nav class="footer-col" aria-label="<?php echo esc_attr( wp_get_nav_menu_name( $renosi_loc ) ); ?>">
						<h2 class="footer-title"><?php echo esc_html( wp_get_nav_menu_name( $renosi_loc ) ); ?></h2>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => $renosi_loc,
								'container'      => false,
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
							)
						);
						?>
					</nav>
				<?php elseif ( 'footer-1' === $renosi_loc && $renosi_has_wc ) : ?>
					<nav class="footer-col" aria-label="<?php esc_attr_e( 'دسترسی سریع', 'renosi' ); ?>">
						<h2 class="footer-title"><?php esc_html_e( 'دسترسی سریع', 'renosi' ); ?></h2>
						<ul class="footer-menu">
							<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'فروشگاه', 'renosi' ); ?></a></li>
							<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'حساب کاربری', 'renosi' ); ?></a></li>
							<li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'سبد خرید', 'renosi' ); ?></a></li>
						</ul>
					</nav>
				<?php endif; ?>
			<?php endforeach; ?>

			<div class="footer-col footer-col--contact" id="contact">
				<h2 class="footer-title"><?php esc_html_e( 'ارتباط با ما', 'renosi' ); ?></h2>
				<ul class="contact-list">
					<?php if ( renosi_opt( 'phone' ) ) : ?>
						<li><?php renosi_the_icon( 'phone', 18 ); ?><a href="<?php echo esc_url( renosi_tel( renosi_opt( 'phone' ) ) ); ?>" dir="ltr"><?php echo esc_html( renosi_opt( 'phone' ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( renosi_opt( 'mobile' ) ) : ?>
						<li><?php renosi_the_icon( 'whatsapp', 18 ); ?><a href="<?php echo esc_url( renosi_tel( renosi_opt( 'mobile' ) ) ); ?>" dir="ltr"><?php echo esc_html( renosi_opt( 'mobile' ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( renosi_opt( 'email' ) ) : ?>
						<li><?php renosi_the_icon( 'mail', 18 ); ?><a href="mailto:<?php echo esc_attr( antispambot( renosi_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( renosi_opt( 'email' ) ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( renosi_opt( 'address' ) ) : ?>
						<li><?php renosi_the_icon( 'pin', 18 ); ?><span><?php echo esc_html( renosi_opt( 'address' ) ); ?></span></li>
					<?php endif; ?>
					<?php if ( renosi_opt( 'hours' ) ) : ?>
						<li><?php renosi_the_icon( 'clock', 18 ); ?><span><?php echo esc_html( renosi_opt( 'hours' ) ); ?></span></li>
					<?php endif; ?>
				</ul>
			</div>

			<?php if ( is_active_sidebar( 'footer-badges' ) ) : ?>
				<div class="footer-col footer-col--badges">
					<?php dynamic_sidebar( 'footer-badges' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="footer-bottom">
			<div class="container footer-bottom__inner">
				<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> — <?php echo esc_html( renosi_opt( 'copyright' ) ); ?></p>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php // Mobile menu drawer. ?>
<div id="menu-drawer" class="drawer drawer--side drawer--start" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منو', 'renosi' ); ?>" tabindex="-1" hidden>
	<div class="drawer__head">
		<?php renosi_site_branding(); ?>
		<button type="button" class="icon-button" data-drawer-close aria-label="<?php esc_attr_e( 'بستن منو', 'renosi' ); ?>"><?php renosi_the_icon( 'close', 22 ); ?></button>
	</div>
	<div class="drawer__body">
		<div class="drawer__search"><?php get_template_part( 'template-parts/header/search-form' ); ?></div>
		<?php if ( $renosi_has_wc ) : ?>
			<div class="mycar-pill" data-mycar hidden>
				<a data-mycar-link href="#"><?php renosi_the_icon( 'car', 16 ); ?><span><?php esc_html_e( 'خودروی من:', 'renosi' ); ?></span> <strong data-mycar-name></strong></a>
				<button type="button" data-mycar-clear aria-label="<?php esc_attr_e( 'حذف خودروی من', 'renosi' ); ?>"><?php renosi_the_icon( 'close', 14 ); ?></button>
			</div>
		<?php endif; ?>
		<nav aria-label="<?php esc_attr_e( 'منوی موبایل', 'renosi' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-menu',
					'depth'          => 2,
					'fallback_cb'    => 'renosi_menu_fallback',
				)
			);
			?>
		</nav>
		<?php
		if ( $renosi_has_wc ) :
			$renosi_mcats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => 0,
					'hide_empty' => false,
					'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
					'orderby'    => 'menu_order',
				)
			);
			if ( ! is_wp_error( $renosi_mcats ) && $renosi_mcats ) :
				?>
				<div class="drawer__section" id="drawer-categories">
					<h2 class="drawer__label"><?php esc_html_e( 'دسته‌بندی قطعات', 'renosi' ); ?></h2>
					<ul class="mobile-cats">
						<?php foreach ( $renosi_mcats as $renosi_mcat ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $renosi_mcat ) ); ?>"><span><?php echo esc_html( $renosi_mcat->name ); ?></span><?php renosi_the_icon( 'chevron-left', 16 ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php
			endif;
		endif;
		?>
		<?php if ( renosi_opt( 'phone' ) ) : ?>
			<a class="help-line" href="<?php echo esc_url( renosi_tel( renosi_opt( 'phone' ) ) ); ?>">
				<?php renosi_the_icon( 'headset', 20 ); ?>
				<span><?php esc_html_e( 'مشاوره و خرید تلفنی', 'renosi' ); ?> <strong dir="ltr"><?php echo esc_html( renosi_opt( 'phone' ) ); ?></strong></span>
			</a>
		<?php endif; ?>
	</div>
</div>

<?php if ( $renosi_has_wc ) : ?>
	<?php // Mini-cart drawer. ?>
	<div id="cart-drawer" class="drawer drawer--side drawer--end" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'سبد خرید', 'renosi' ); ?>" tabindex="-1" hidden>
		<div class="drawer__head">
			<h2 class="drawer__title"><?php esc_html_e( 'سبد خرید', 'renosi' ); ?> <span class="drawer-cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span></h2>
			<button type="button" class="icon-button" data-drawer-close aria-label="<?php esc_attr_e( 'بستن سبد خرید', 'renosi' ); ?>"><?php renosi_the_icon( 'close', 22 ); ?></button>
		</div>
		<div class="drawer__body drawer__body--cart">
			<div class="widget_shopping_cart_content">
				<?php
				if ( WC()->cart ) {
					woocommerce_mini_cart();
				}
				?>
			</div>
		</div>
	</div>

	<?php if ( renosi_opt( 'mobile_bottom_nav' ) ) : ?>
		<nav class="bottom-nav" aria-label="<?php esc_attr_e( 'ناوبری سریع', 'renosi' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'is-active' : ''; ?>"><?php renosi_the_icon( 'home', 22 ); ?><span><?php esc_html_e( 'خانه', 'renosi' ); ?></span></a>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" data-drawer-open="menu-drawer" data-drawer-focus="#drawer-categories" class="<?php echo ( is_shop() || is_product_taxonomy() ) ? 'is-active' : ''; ?>"><?php renosi_the_icon( 'grid', 22 ); ?><span><?php esc_html_e( 'دسته‌ها', 'renosi' ); ?></span></a>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" data-drawer-open="menu-drawer" data-drawer-focus=".drawer__search input"><?php renosi_the_icon( 'search', 22 ); ?><span><?php esc_html_e( 'جستجو', 'renosi' ); ?></span></a>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-drawer-open="cart-drawer" class="bottom-nav__cart <?php echo is_cart() ? 'is-active' : ''; ?>"><?php renosi_the_icon( 'bag', 22 ); ?><span class="cart-count<?php echo ( WC()->cart && WC()->cart->get_cart_contents_count() ) ? '' : ' is-empty'; ?>"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span><span><?php esc_html_e( 'سبد', 'renosi' ); ?></span></a>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="<?php echo is_account_page() ? 'is-active' : ''; ?>"><?php renosi_the_icon( 'user', 22 ); ?><span><?php esc_html_e( 'حساب', 'renosi' ); ?></span></a>
		</nav>
	<?php endif; ?>
<?php endif; ?>

<div class="drawer-overlay" data-drawer-close hidden></div>

<?php wp_footer(); ?>
</body>
</html>
