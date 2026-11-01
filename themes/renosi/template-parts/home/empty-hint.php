<?php
/**
 * Home: admin-only hint shown while the store has no products.
 * Visitors never see this box.
 *
 * @package renosi
 */

$renosi_has_wc = class_exists( 'WooCommerce' );
?>
<section class="section">
	<div class="container">
		<div class="setup-hint">
			<div>
				<h2 class="setup-hint__title">
					<?php echo $renosi_has_wc ? esc_html__( 'فروشگاه هنوز محصولی ندارد', 'renosi' ) : esc_html__( 'ووکامرس نصب نیست', 'renosi' ); ?>
				</h2>
				<p>
					<?php
					echo $renosi_has_wc
						? esc_html__( 'بخش‌های دسته‌بندی، پیشنهادهای ویژه، تازه‌ترین‌ها و پرفروش‌ترین‌ها بعد از افزودن محصول خودکار نمایش داده می‌شوند. این کادر فقط برای مدیر سایت دیده می‌شود.', 'renosi' )
						: esc_html__( 'برای فعال شدن فروشگاه، سبد خرید و جستجوی خودرو، افزونه‌ی ووکامرس را نصب کنید. این کادر فقط برای مدیر سایت دیده می‌شود.', 'renosi' );
					?>
				</p>
			</div>
			<div class="setup-hint__actions">
				<a class="button button--primary" href="<?php echo esc_url( admin_url( 'themes.php?page=renosi-setup' ) ); ?>"><?php esc_html_e( 'راه‌اندازی سریع / محتوای نمونه', 'renosi' ); ?></a>
				<?php if ( $renosi_has_wc ) : ?>
					<a class="button button--secondary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>"><?php esc_html_e( 'افزودن محصول', 'renosi' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
