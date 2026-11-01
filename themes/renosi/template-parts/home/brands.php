<?php
/**
 * Home: brands strip (WooCommerce Brands, product_brand taxonomy).
 *
 * @package renosi
 */

if ( ! taxonomy_exists( 'product_brand' ) ) {
	return;
}
$renosi_brands = get_terms(
	array(
		'taxonomy'   => 'product_brand',
		'hide_empty' => false,
		'parent'     => 0,
		'number'     => 12,
	)
);
if ( is_wp_error( $renosi_brands ) || ! $renosi_brands ) {
	return;
}
?>
<section class="section" aria-labelledby="home-brands-title">
	<div class="container">
		<div class="section-head">
			<h2 id="home-brands-title" class="section-title"><?php esc_html_e( 'برندهای معتبر', 'renosi' ); ?></h2>
		</div>
		<ul class="brand-strip">
			<?php foreach ( $renosi_brands as $renosi_brand ) : ?>
				<?php $renosi_thumb = (int) get_term_meta( $renosi_brand->term_id, 'thumbnail_id', true ); ?>
				<li>
					<a class="brand-tile" href="<?php echo esc_url( get_term_link( $renosi_brand ) ); ?>" title="<?php echo esc_attr( $renosi_brand->name ); ?>">
						<?php
						if ( $renosi_thumb ) {
							echo wp_get_attachment_image( $renosi_thumb, 'medium', false, array( 'alt' => esc_attr( $renosi_brand->name ) ) );
						} else {
							echo '<span>' . esc_html( $renosi_brand->name ) . '</span>';
						}
						?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
