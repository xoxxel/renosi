<?php
/**
 * Archive header for shop, product categories, vehicles, brands, tags and product search.
 * One H1 per page, contextual quick-links (internal linking) and a vehicle filter.
 *
 * @package renosi
 */

$renosi_term    = ( is_product_taxonomy() ) ? get_queried_object() : null;
$renosi_tax     = $renosi_term instanceof WP_Term ? $renosi_term->taxonomy : '';
$renosi_title   = woocommerce_page_title( false );
$renosi_eyebrow = '';
$renosi_media   = '';
$renosi_chips   = array();
$renosi_active  = 0;
$renosi_back    = null;
$renosi_total   = (int) wc_get_loop_prop( 'total' );
if ( ! $renosi_total ) {
	global $wp_query;
	$renosi_total = (int) $wp_query->found_posts;
}

if ( 'product_cat' === $renosi_tax ) {
	$renosi_eyebrow = __( 'دسته‌بندی قطعات', 'renosi' );
	$renosi_thumb   = (int) get_term_meta( $renosi_term->term_id, 'thumbnail_id', true );
	if ( $renosi_thumb ) {
		$renosi_media = wp_get_attachment_image( $renosi_thumb, 'woocommerce_thumbnail', false, array( 'alt' => $renosi_term->name, 'class' => 'archive-hero__img' ) );
	}
	$renosi_children = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $renosi_term->term_id, 'hide_empty' => true ) );
	if ( ! is_wp_error( $renosi_children ) && $renosi_children ) {
		$renosi_chips = $renosi_children;
	} elseif ( $renosi_term->parent ) {
		$renosi_back  = get_term( $renosi_term->parent, 'product_cat' );
		$renosi_sibs  = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $renosi_term->parent, 'hide_empty' => true ) );
		$renosi_chips = is_wp_error( $renosi_sibs ) ? array() : $renosi_sibs;
		$renosi_active = $renosi_term->term_id;
	}
} elseif ( 'renosi_vehicle' === $renosi_tax ) {
	$renosi_eyebrow = $renosi_term->parent ? __( 'قطعات سازگار با خودروی شما', 'renosi' ) : __( 'برند خودرو', 'renosi' );
	$renosi_media   = '<span class="vehicle-logo vehicle-logo--lg">' . renosi_vehicle_logo( $renosi_term, 'medium' ) . '</span>';
	$renosi_kids = get_terms( array( 'taxonomy' => 'renosi_vehicle', 'parent' => $renosi_term->term_id, 'hide_empty' => false ) );
	if ( $renosi_term->parent && ! is_wp_error( $renosi_kids ) && $renosi_kids ) {
		// Model with types: show its types.
		$renosi_back  = get_term( $renosi_term->parent, 'renosi_vehicle' );
		$renosi_chips = $renosi_kids;
	} elseif ( $renosi_term->parent ) {
		$renosi_back   = get_term( $renosi_term->parent, 'renosi_vehicle' );
		$renosi_sibs   = get_terms( array( 'taxonomy' => 'renosi_vehicle', 'parent' => $renosi_term->parent, 'hide_empty' => false ) );
		$renosi_chips  = is_wp_error( $renosi_sibs ) ? array() : $renosi_sibs;
		$renosi_active = $renosi_term->term_id;
	} else {
		$renosi_kids  = get_terms( array( 'taxonomy' => 'renosi_vehicle', 'parent' => $renosi_term->term_id, 'hide_empty' => false ) );
		$renosi_chips = is_wp_error( $renosi_kids ) ? array() : $renosi_kids;
	}
} elseif ( 'product_brand' === $renosi_tax ) {
	$renosi_eyebrow = __( 'برند سازنده', 'renosi' );
	$renosi_thumb   = (int) get_term_meta( $renosi_term->term_id, 'thumbnail_id', true );
	if ( $renosi_thumb ) {
		$renosi_media = '<span class="vehicle-logo vehicle-logo--lg">' . wp_get_attachment_image( $renosi_thumb, 'medium', false, array( 'alt' => $renosi_term->name, 'class' => 'vehicle-logo__img' ) ) . '</span>';
	}
} elseif ( 'product_tag' === $renosi_tax ) {
	$renosi_eyebrow = __( 'برچسب', 'renosi' );
} elseif ( is_search() ) {
	$renosi_eyebrow = __( 'جستجو', 'renosi' );
	/* translators: %s: search query. */
	$renosi_title = sprintf( __( 'نتایج جستجو برای «%s»', 'renosi' ), get_search_query() );
} else {
	$renosi_eyebrow = __( 'فروشگاه قطعات یدکی', 'renosi' );
	$renosi_cats    = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
	$renosi_chips   = is_wp_error( $renosi_cats ) ? array() : $renosi_cats;
}

// Active vehicle filter on non-vehicle archives (?vehicle=slug).
$renosi_vfilter = '';
if ( 'renosi_vehicle' !== $renosi_tax && get_query_var( 'vehicle' ) ) {
	$renosi_vterm = get_term_by( 'slug', get_query_var( 'vehicle' ), 'renosi_vehicle' );
	if ( $renosi_vterm ) {
		$renosi_vfilter = $renosi_vterm;
	}
}
?>
<header class="archive-hero<?php echo $renosi_media ? ' archive-hero--media' : ''; ?>">
	<div class="archive-hero__body">
		<?php if ( $renosi_eyebrow ) : ?>
			<p class="eyebrow"><?php echo esc_html( $renosi_eyebrow ); ?></p>
		<?php endif; ?>
		<h1 class="archive-hero__title page-title"><?php echo esc_html( $renosi_title ); ?></h1>
		<p class="archive-hero__meta">
			<?php
			/* translators: %s: number of products. */
			echo esc_html( sprintf( _n( '%s محصول', '%s محصول', $renosi_total, 'renosi' ), number_format_i18n( $renosi_total ) ) );
			?>
			<?php if ( renosi_opt( 'express_enable' ) ) : ?>
				<span class="archive-hero__sep" aria-hidden="true">·</span>
				<span class="archive-hero__express"><?php renosi_the_icon( 'bolt', 14 ); ?><?php echo esc_html( renosi_opt( 'express_label' ) ); ?></span>
			<?php endif; ?>
		</p>

		<?php if ( $renosi_chips || $renosi_back ) : ?>
			<nav class="archive-hero__chips" aria-label="<?php esc_attr_e( 'زیرمجموعه‌ها', 'renosi' ); ?>">
				<ul class="chip-list">
					<?php if ( $renosi_back && ! is_wp_error( $renosi_back ) ) : ?>
						<li><a class="chip chip--ghost" href="<?php echo esc_url( get_term_link( $renosi_back ) ); ?>">
							<?php
							/* translators: %s: parent term. */
							printf( esc_html__( 'همه‌ی %s', 'renosi' ), esc_html( $renosi_back->name ) );
							?>
						</a></li>
					<?php endif; ?>
					<?php foreach ( $renosi_chips as $renosi_chip ) : ?>
						<li><a class="chip<?php echo (int) $renosi_chip->term_id === (int) $renosi_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $renosi_chip ) ); ?>" <?php echo (int) $renosi_chip->term_id === (int) $renosi_active ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $renosi_chip->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php if ( 'renosi_vehicle' === $renosi_tax ) : ?>
			<button type="button" class="button button--secondary button--sm archive-hero__mycar" data-mycar-save data-name="<?php echo esc_attr( $renosi_term->name ); ?>" data-url="<?php echo esc_url( get_term_link( $renosi_term ) ); ?>">
				<?php renosi_the_icon( 'car', 16 ); ?><span><?php esc_html_e( 'ذخیره به عنوان خودروی من', 'renosi' ); ?></span>
			</button>
		<?php elseif ( in_array( $renosi_tax, array( 'product_cat', 'product_brand', 'product_tag' ), true ) || is_shop() ) : ?>
			<?php renosi_vehicle_filter_form( $renosi_vfilter ); ?>
		<?php endif; ?>
	</div>
	<?php if ( $renosi_media ) : ?>
		<div class="archive-hero__media"><?php echo $renosi_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<?php endif; ?>
</header>
