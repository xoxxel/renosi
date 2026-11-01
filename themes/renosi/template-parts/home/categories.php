<?php
/**
 * Home: product categories grid.
 *
 * @package renosi
 */

$renosi_cats = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => false,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		'orderby'    => 'menu_order',
		'number'     => 12,
	)
);
if ( is_wp_error( $renosi_cats ) || ! $renosi_cats ) {
	return;
}
?>
<section class="section" aria-labelledby="home-cats-title">
	<div class="container">
		<div class="section-head">
			<h2 id="home-cats-title" class="section-title"><?php echo esc_html( renosi_opt( 'home_categories_title' ) ); ?></h2>
			<a class="section-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'همه‌ی محصولات', 'renosi' ); ?> <?php renosi_the_icon( 'chevron-left', 16 ); ?></a>
		</div>
		<?php
		$renosi_n    = count( $renosi_cats );
		$renosi_cols = $renosi_n <= 6 ? $renosi_n : min( 6, (int) ceil( $renosi_n / 2 ) );
		?>
		<ul class="cat-grid" style="--cat-cols: <?php echo esc_attr( max( 3, $renosi_cols ) ); ?>">
			<?php foreach ( $renosi_cats as $renosi_cat ) : ?>
				<?php $renosi_thumb = (int) get_term_meta( $renosi_cat->term_id, 'thumbnail_id', true ); ?>
				<li>
					<a class="cat-tile" href="<?php echo esc_url( get_term_link( $renosi_cat ) ); ?>">
						<span class="cat-tile__media">
							<?php
							if ( $renosi_thumb ) {
								echo wp_get_attachment_image( $renosi_thumb, 'woocommerce_thumbnail', false, array( 'alt' => '' ) );
							} else {
								renosi_the_icon( 'part', 40 );
							}
							?>
						</span>
						<span class="cat-tile__name"><?php echo esc_html( $renosi_cat->name ); ?></span>
						<?php if ( $renosi_cat->count ) : ?>
							<span class="cat-tile__count">
								<?php
								/* translators: %s: products count. */
								echo esc_html( sprintf( __( '%s محصول', 'renosi' ), number_format_i18n( $renosi_cat->count ) ) );
								?>
							</span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
