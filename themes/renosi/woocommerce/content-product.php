<?php
/**
 * Product card within loops.
 *
 * Standard WooCommerce hooks are still fired so plugins (wishlist, compare, badges…)
 * keep working; the theme removes the default callbacks and renders its own markup.
 *
 * @package renosi
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$renosi_link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
$renosi_cat  = renosi_primary_cat( $product->get_id() );
$renosi_tag  = ( is_front_page() || is_product() || is_cart() ) ? 'h3' : 'h2'; // Keep a clean heading outline.
?>
<li <?php wc_product_class( 'product-card', $product ); ?>>
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<div class="product-card__media">
		<a href="<?php echo esc_url( $renosi_link ); ?>" class="product-card__image woocommerce-LoopProduct-link woocommerce-loop-product__link" tabindex="-1" aria-hidden="true">
			<?php echo woocommerce_get_product_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<?php renosi_product_badges( $product ); ?>
		<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
	</div>

	<div class="product-card__body">
		<?php if ( $renosi_cat ) : ?>
			<span class="product-card__cat"><?php echo esc_html( $renosi_cat->name ); ?></span>
		<?php endif; ?>

		<<?php echo esc_attr( $renosi_tag ); ?> class="woocommerce-loop-product__title product-card__title">
			<a href="<?php echo esc_url( $renosi_link ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
		</<?php echo esc_attr( $renosi_tag ); ?>>
		<?php do_action( 'woocommerce_shop_loop_item_title' ); ?>

		<?php if ( wc_product_sku_enabled() && $product->get_sku() ) : ?>
			<span class="product-card__sku"><?php esc_html_e( 'کد فنی:', 'renosi' ); ?> <span dir="ltr"><?php echo esc_html( $product->get_sku() ); ?></span></span>
		<?php endif; ?>

		<?php if ( function_exists( 'renosi_is_express' ) && renosi_is_express( $product ) ) : ?>
			<?php echo renosi_express_badge( 'express-badge--card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>

		<div class="product-card__footer">
			<div class="product-card__price"><?php woocommerce_template_loop_price(); ?></div>
			<div class="product-card__cta"><?php woocommerce_template_loop_add_to_cart(); ?></div>
		</div>
	</div>

	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
</li>
