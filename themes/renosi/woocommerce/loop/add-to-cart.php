<?php
/**
 * Loop add-to-cart button.
 *
 * Same as WooCommerce's template, but the aria-describedby target gets a unique ID so the
 * same product can appear in several sections of one page (valid HTML, no duplicate IDs).
 *
 * @package renosi
 * @version 9.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$renosi_desc_id   = wp_unique_id( 'atc-desc-' . $product->get_id() . '-' );
$aria_describedby = isset( $args['aria-describedby_text'] ) ? sprintf( 'aria-describedby="%s"', esc_attr( $renosi_desc_id ) ) : '';

echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	'woocommerce_loop_add_to_cart_link',
	sprintf(
		'<a href="%s" %s data-quantity="%s" class="%s" %s>%s</a>',
		esc_url( $product->add_to_cart_url() ),
		$aria_describedby,
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
		esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		esc_html( $product->add_to_cart_text() )
	),
	$product,
	$args
);
?>
<?php if ( isset( $args['aria-describedby_text'] ) ) : ?>
	<span id="<?php echo esc_attr( $renosi_desc_id ); ?>" class="screen-reader-text"><?php echo esc_html( $args['aria-describedby_text'] ); ?></span>
<?php endif; ?>
