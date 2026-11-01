<?php
/**
 * Home: request-a-part call to action.
 *
 * @package renosi
 */

$renosi_phone  = renosi_opt( 'phone' );
$renosi_mobile = renosi_opt( 'mobile' );
$renosi_wa     = renosi_opt( 'social_whatsapp' );
?>
<section class="section">
	<div class="container">
		<div class="cta">
			<div class="cta__content">
				<span class="cta__icon"><?php renosi_the_icon( 'wrench', 26 ); ?></span>
				<h2 class="cta__title"><?php echo esc_html( renosi_opt( 'home_cta_title' ) ); ?></h2>
				<p class="cta__text"><?php echo esc_html( renosi_opt( 'home_cta_text' ) ); ?></p>
			</div>
			<div class="cta__actions">
				<?php if ( $renosi_phone ) : ?>
					<a class="button button--primary button--lg" href="<?php echo esc_url( renosi_tel( $renosi_phone ) ); ?>"><?php renosi_the_icon( 'phone', 18 ); ?><span dir="ltr"><?php echo esc_html( $renosi_phone ); ?></span></a>
				<?php endif; ?>
				<?php if ( $renosi_wa ) : ?>
					<a class="button button--secondary button--lg" href="<?php echo esc_url( $renosi_wa ); ?>" target="_blank" rel="noopener"><?php renosi_the_icon( 'whatsapp', 18 ); ?><span><?php esc_html_e( 'ارسال در واتساپ', 'renosi' ); ?></span></a>
				<?php elseif ( $renosi_mobile ) : ?>
					<a class="button button--secondary button--lg" href="<?php echo esc_url( renosi_tel( $renosi_mobile ) ); ?>"><?php renosi_the_icon( 'chat', 18 ); ?><span dir="ltr"><?php echo esc_html( $renosi_mobile ); ?></span></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
