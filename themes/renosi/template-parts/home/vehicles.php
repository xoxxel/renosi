<?php
/**
 * Home: vehicle brands grid — fastest path to "parts for my car".
 *
 * @package renosi
 */

if ( ! renosi_get_vehicle_tree() ) {
	return;
}
$renosi_all = renosi_vehicles_page_url();
?>
<section class="section" aria-labelledby="home-vehicles-title">
	<div class="container">
		<div class="section-head">
			<h2 id="home-vehicles-title" class="section-title"><?php echo esc_html( renosi_opt( 'home_vehicles_title' ) ); ?></h2>
			<?php if ( $renosi_all ) : ?>
				<a class="section-link" href="<?php echo esc_url( $renosi_all ); ?>"><?php esc_html_e( 'همه‌ی خودروها', 'renosi' ); ?> <?php renosi_the_icon( 'chevron-left', 16 ); ?></a>
			<?php endif; ?>
		</div>
		<?php renosi_vehicle_brand_grid( 12 ); ?>
	</div>
</section>
