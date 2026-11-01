<?php
/**
 * Product categories dropdown (desktop header).
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
		'number'     => 16,
	)
);
if ( is_wp_error( $renosi_cats ) || ! $renosi_cats ) {
	return;
}
?>
<div class="cat-menu" data-dropdown>
	<button type="button" class="cat-menu__toggle" aria-expanded="false" aria-haspopup="true" data-dropdown-toggle>
		<?php renosi_the_icon( 'grid', 18 ); ?>
		<span><?php esc_html_e( 'دسته‌بندی قطعات', 'renosi' ); ?></span>
		<?php renosi_the_icon( 'chevron-down', 14 ); ?>
	</button>
	<div class="cat-menu__panel" data-dropdown-panel>
		<ul class="cat-menu__grid">
			<?php foreach ( $renosi_cats as $renosi_cat ) : ?>
				<?php
				$renosi_children = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'parent'     => $renosi_cat->term_id,
						'hide_empty' => false,
						'number'     => 6,
					)
				);
				?>
				<li class="cat-menu__item">
					<a class="cat-menu__parent" href="<?php echo esc_url( get_term_link( $renosi_cat ) ); ?>"><?php echo esc_html( $renosi_cat->name ); ?></a>
					<?php if ( ! is_wp_error( $renosi_children ) && $renosi_children ) : ?>
						<ul class="cat-menu__children">
							<?php foreach ( $renosi_children as $renosi_child ) : ?>
								<li><a href="<?php echo esc_url( get_term_link( $renosi_child ) ); ?>"><?php echo esc_html( $renosi_child->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<a class="cat-menu__all" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'مشاهده‌ی همه‌ی محصولات', 'renosi' ); ?> <?php renosi_the_icon( 'chevron-left', 14 ); ?></a>
	</div>
</div>
