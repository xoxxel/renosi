<?php
/**
 * 404.
 *
 * @package renosi
 */

get_header();
?>
<main id="primary" class="site-main">
	<div class="container page-shell">
		<section class="empty-state empty-state--404">
			<p class="empty-state__code">404</p>
			<h1 class="empty-state__title"><?php esc_html_e( 'این صفحه پیدا نشد', 'renosi' ); ?></h1>
			<p><?php esc_html_e( 'ممکن است آدرس تغییر کرده باشد. نام یا کد فنی قطعه را جستجو کنید.', 'renosi' ); ?></p>
			<div class="empty-state__search"><?php get_template_part( 'template-parts/header/search-form' ); ?></div>
			<div class="empty-state__actions">
				<a class="button button--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به خانه', 'renosi' ); ?></a>
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
					<a class="button button--secondary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'فروشگاه', 'renosi' ); ?></a>
				<?php endif; ?>
			</div>
		</section>
	</div>
</main>
<?php
get_footer();
