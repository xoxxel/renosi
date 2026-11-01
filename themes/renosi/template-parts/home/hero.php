<?php
/**
 * Home top area: vehicle finder card (H1) + banner slider.
 *
 * @package renosi
 */

$renosi_has_wc = class_exists( 'WooCommerce' );
$renosi_shop   = $renosi_has_wc ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

// Build slides.
$renosi_slides = array();
for ( $renosi_n = 1; $renosi_n <= 5; $renosi_n++ ) {
	$renosi_img   = (int) renosi_opt( "slide_{$renosi_n}_image" );
	$renosi_title = renosi_opt( "slide_{$renosi_n}_title" );
	if ( ! $renosi_img && ! $renosi_title ) {
		continue;
	}
	$renosi_url = renosi_opt( "slide_{$renosi_n}_url" );
	if ( ! $renosi_url ) {
		$renosi_url = ( 2 === $renosi_n && $renosi_has_wc ) ? add_query_arg( 'express', '1', $renosi_shop ) : $renosi_shop;
	}
	$renosi_slides[] = array(
		'n'     => $renosi_n,
		'img'   => $renosi_img,
		'title' => $renosi_title,
		'text'  => renosi_opt( "slide_{$renosi_n}_text" ),
		'btn'   => renosi_opt( "slide_{$renosi_n}_btn" ),
		'url'   => $renosi_url,
	);
}
$renosi_help = renosi_opt( 'social_whatsapp' ) ? renosi_opt( 'social_whatsapp' ) : ( renosi_opt( 'phone' ) ? renosi_tel( renosi_opt( 'phone' ) ) : '#contact' );
?>
<section class="hero">
	<div class="container hero__grid<?php echo $renosi_slides ? '' : ' hero__grid--single'; ?>">

		<div class="hero-finder" data-finder-card>
			<div class="hero-finder__head">
				<h1 class="hero-finder__title"><?php echo esc_html( renosi_opt( 'hero_title' ) ); ?></h1>
				<?php if ( $renosi_has_wc && renosi_get_vehicle_tree() ) : ?>
					<button type="button" class="icon-button icon-button--sm" data-finder-reset aria-label="<?php esc_attr_e( 'پاک کردن انتخاب', 'renosi' ); ?>" title="<?php esc_attr_e( 'پاک کردن انتخاب', 'renosi' ); ?>"><?php renosi_the_icon( 'reset', 18 ); ?></button>
				<?php endif; ?>
			</div>

			<?php renosi_vehicle_finder( 'hero' ); ?>

			<?php if ( $renosi_has_wc ) : ?>
				<div class="hero-finder__divider" role="presentation"></div>
				<?php renosi_part_number_search(); ?>
			<?php endif; ?>

			<a class="hero-finder__help" href="<?php echo esc_url( $renosi_help ); ?>" <?php echo 0 === strpos( $renosi_help, 'http' ) ? 'target="_blank" rel="noopener"' : ''; ?>>
				<?php esc_html_e( 'خودروی خود را در فهرست پیدا نکردید؟ کارشناسان ما کمک می‌کنند', 'renosi' ); ?>
				<?php renosi_the_icon( 'chevron-left', 16 ); ?>
			</a>
		</div>

		<?php if ( $renosi_slides ) : ?>
			<div class="slider" data-slider data-autoplay="<?php echo renosi_opt( 'slider_autoplay' ) ? '6000' : '0'; ?>" role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'پیشنهادهای ویژه', 'renosi' ); ?>">
				<div class="slider__viewport">
					<div class="slider__track" data-slider-track>
						<?php foreach ( $renosi_slides as $renosi_i => $renosi_s ) : ?>
							<div class="slide slide--<?php echo esc_attr( $renosi_s['n'] ); ?><?php echo $renosi_s['img'] ? ' slide--image' : ''; ?><?php echo 0 === $renosi_i ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( '%1$s / %2$s', number_format_i18n( $renosi_i + 1 ), number_format_i18n( count( $renosi_slides ) ) ) ); ?>" <?php echo 0 === $renosi_i ? '' : 'aria-hidden="true"'; ?>>
								<?php if ( $renosi_s['img'] ) : ?>
									<?php
									echo wp_get_attachment_image(
										$renosi_s['img'],
										'full',
										false,
										array(
											'class'         => 'slide__img',
											'loading'       => 0 === $renosi_i ? 'eager' : 'lazy',
											'fetchpriority' => 0 === $renosi_i ? 'high' : 'auto',
											'sizes'         => '(min-width: 1024px) 860px, 100vw',
											'alt'           => $renosi_s['title'] ? $renosi_s['title'] : get_bloginfo( 'name' ),
										)
									);
									?>
								<?php else : ?>
									<div class="slide__art" aria-hidden="true">
										<?php
										if ( 2 === $renosi_s['n'] ) {
											echo '<span class="slide-badge">' . renosi_icon( 'bolt', 64 ) . '<span class="slide-badge__num">۳</span><span class="slide-badge__unit">' . esc_html__( 'ساعته', 'renosi' ) . '</span></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										} elseif ( 3 === $renosi_s['n'] ) {
											echo '<span class="slide-code"><span class="slide-code__bars"></span><span class="slide-code__num" dir="ltr">7701 208 265</span></span>';
										} else {
											echo renosi_disc_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										?>
									</div>
								<?php endif; ?>

								<?php if ( $renosi_s['title'] || $renosi_s['btn'] ) : ?>
									<div class="slide__content">
										<?php if ( $renosi_s['title'] ) : ?>
											<p class="slide__title"><?php echo esc_html( $renosi_s['title'] ); ?></p>
										<?php endif; ?>
										<?php if ( $renosi_s['text'] ) : ?>
											<p class="slide__text"><?php echo esc_html( $renosi_s['text'] ); ?></p>
										<?php endif; ?>
										<?php if ( $renosi_s['btn'] ) : ?>
											<a class="slide__btn" href="<?php echo esc_url( $renosi_s['url'] ); ?>" <?php echo 0 === $renosi_i ? '' : 'tabindex="-1"'; ?>><?php echo esc_html( $renosi_s['btn'] ); ?></a>
										<?php endif; ?>
									</div>
								<?php elseif ( $renosi_s['img'] && $renosi_s['url'] ) : ?>
									<a class="slide__link" href="<?php echo esc_url( $renosi_s['url'] ); ?>" <?php echo 0 === $renosi_i ? '' : 'tabindex="-1"'; ?>><span class="screen-reader-text"><?php esc_html_e( 'مشاهده', 'renosi' ); ?></span></a>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( count( $renosi_slides ) > 1 ) : ?>
					<div class="slider__controls">
						<button type="button" class="slider__arrow" data-slider-prev aria-label="<?php esc_attr_e( 'اسلاید قبلی', 'renosi' ); ?>"><?php renosi_the_icon( 'chevron-right', 18 ); ?></button>
						<div class="slider__dots" role="group" aria-label="<?php esc_attr_e( 'انتخاب اسلاید', 'renosi' ); ?>">
							<?php foreach ( $renosi_slides as $renosi_i => $renosi_s ) : ?>
								<button type="button" class="slider__dot<?php echo 0 === $renosi_i ? ' is-active' : ''; ?>" data-slider-dot="<?php echo esc_attr( $renosi_i ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: slide number */ __( 'اسلاید %s', 'renosi' ), number_format_i18n( $renosi_i + 1 ) ) ); ?>" aria-current="<?php echo 0 === $renosi_i ? 'true' : 'false'; ?>"></button>
							<?php endforeach; ?>
						</div>
						<button type="button" class="slider__arrow" data-slider-next aria-label="<?php esc_attr_e( 'اسلاید بعدی', 'renosi' ); ?>"><?php renosi_the_icon( 'chevron-left', 18 ); ?></button>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
