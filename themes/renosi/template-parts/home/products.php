<?php
/**
 * Home: product rail.
 *
 * Args: title, atts (shortcode attributes), url, class.
 *
 * @package renosi
 */

$renosi_args = wp_parse_args(
	$args,
	array(
		'title' => '',
		'atts'  => 'limit="8" columns="4"',
		'url'   => '',
		'class' => '',
	)
);

$renosi_html = renosi_products_html( $renosi_args['atts'] );
if ( ! $renosi_html ) {
	return;
}
?>
<section class="section home-products <?php echo esc_attr( $renosi_args['class'] ); ?>">
	<div class="container">
		<?php renosi_section_head( $renosi_args['title'], $renosi_args['url'] ); ?>
		<div class="product-rail">
			<?php echo $renosi_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
