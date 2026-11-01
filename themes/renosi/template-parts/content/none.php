<?php
/**
 * Nothing found.
 *
 * @package renosi
 */

?>
<section class="empty-state">
	<div class="empty-state__icon"><?php renosi_the_icon( 'search', 40 ); ?></div>
	<h2 class="empty-state__title"><?php esc_html_e( 'چیزی پیدا نشد', 'renosi' ); ?></h2>
	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
		<p>
			<?php
			printf(
				/* translators: %s: new post link */
				esc_html__( 'آماده‌ی انتشار اولین نوشته هستید؟ %s', 'renosi' ),
				'<a href="' . esc_url( admin_url( 'post-new.php' ) ) . '">' . esc_html__( 'شروع کنید', 'renosi' ) . '</a>'
			);
			?>
		</p>
	<?php elseif ( is_search() ) : ?>
		<p><?php esc_html_e( 'با کلمات دیگری جستجو کنید یا کد فنی قطعه را وارد کنید.', 'renosi' ); ?></p>
		<div class="empty-state__search"><?php get_search_form(); ?></div>
	<?php else : ?>
		<p><?php esc_html_e( 'به نظر می‌رسد محتوایی در اینجا وجود ندارد.', 'renosi' ); ?></p>
		<div class="empty-state__search"><?php get_search_form(); ?></div>
	<?php endif; ?>
</section>
