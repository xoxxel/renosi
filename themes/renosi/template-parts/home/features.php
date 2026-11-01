<?php
/**
 * Home: store features / trust strip.
 *
 * @package renosi
 */

$renosi_icons = array( 1 => 'shield', 2 => 'truck', 3 => 'refresh', 4 => 'headset' );
?>
<section class="section section--tight" aria-label="<?php esc_attr_e( 'مزیت‌های خرید از فروشگاه', 'renosi' ); ?>">
	<div class="container">
		<ul class="features">
			<?php foreach ( $renosi_icons as $renosi_i => $renosi_icon ) : ?>
				<?php
				$renosi_title = renosi_opt( "feature_{$renosi_i}_title" );
				if ( ! $renosi_title ) {
					continue;
				}
				?>
				<li class="feature">
					<span class="feature__icon"><?php renosi_the_icon( $renosi_icon, 24 ); ?></span>
					<span class="feature__body">
						<strong class="feature__title"><?php echo esc_html( $renosi_title ); ?></strong>
						<span class="feature__text"><?php echo esc_html( renosi_opt( "feature_{$renosi_i}_text" ) ); ?></span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
