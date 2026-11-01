<?php
/**
 * WooCommerce integration.
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme support.
 */
function renosi_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 450,
			'single_image_width'    => 800,
			'product_grid'          => array(
				'default_rows'    => 4,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 5,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'renosi_woocommerce_setup' );

/**
 * Square, uncropped-looking thumbnails: let WooCommerce crop 1:1.
 */
add_filter(
	'woocommerce_get_image_size_gallery_thumbnail',
	function ( $size ) {
		return array(
			'width'  => 160,
			'height' => 160,
			'crop'   => 1,
		);
	}
);

/*
 * -------------------------------------------------------------------------
 * Layout wrappers
 * -------------------------------------------------------------------------
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );

/**
 * Does the current shop page show the filter sidebar?
 *
 * @return bool
 */
function renosi_shop_has_sidebar() {
	return ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) ) && is_active_sidebar( 'sidebar-shop' );
}

/**
 * Open wrappers.
 */
function renosi_wc_wrapper_start() {
	$layout = renosi_shop_has_sidebar() ? 'shop-layout shop-layout--sidebar' : 'shop-layout';
	echo '<div class="container page-shell">';
	woocommerce_breadcrumb();
	if ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) ) {
		get_template_part( 'template-parts/shop/archive-header' );
	}
	echo '<div class="' . esc_attr( $layout ) . '">';
	if ( renosi_shop_has_sidebar() ) {
		?>
		<aside id="filters-drawer" class="shop-sidebar drawer drawer--side drawer--start" aria-label="<?php esc_attr_e( 'فیلتر محصولات', 'renosi' ); ?>" tabindex="-1">
			<div class="drawer__head">
				<h2 class="drawer__title"><?php esc_html_e( 'فیلترها', 'renosi' ); ?></h2>
				<button type="button" class="icon-button" data-drawer-close aria-label="<?php esc_attr_e( 'بستن', 'renosi' ); ?>"><?php renosi_the_icon( 'close', 20 ); ?></button>
			</div>
			<div class="drawer__body">
				<?php dynamic_sidebar( 'sidebar-shop' ); ?>
			</div>
		</aside>
		<?php
	}
	echo '<main id="primary" class="site-main">';
}
add_action( 'woocommerce_before_main_content', 'renosi_wc_wrapper_start', 10 );

/**
 * SEO content block below the product grid (term description / shop page content).
 */
function renosi_archive_seo_content() {
	if ( is_paged() || ( function_exists( 'renosi_seo_is_filtered' ) && renosi_seo_is_filtered() ) ) {
		return; // Avoid duplicate content on paginated archives.
	}
	$html = '';
	if ( is_product_taxonomy() ) {
		$html = term_description();
	} elseif ( is_shop() ) {
		$shop = get_post( wc_get_page_id( 'shop' ) );
		if ( $shop && '' !== trim( $shop->post_content ) ) {
			$html = apply_filters( 'the_content', $shop->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		}
	}
	if ( ! $html ) {
		return;
	}
	?>
	<section class="seo-content" data-collapse>
		<div class="seo-content__body entry-content"><?php echo wp_kses_post( wc_format_content( $html ) ); ?></div>
		<button type="button" class="seo-content__toggle" data-collapse-toggle hidden aria-expanded="false">
			<span data-more><?php esc_html_e( 'بیشتر بخوانید', 'renosi' ); ?></span>
			<span data-less hidden><?php esc_html_e( 'بستن', 'renosi' ); ?></span>
			<?php renosi_the_icon( 'chevron-down', 16 ); ?>
		</button>
	</section>
	<?php
}
add_action( 'woocommerce_after_main_content', 'renosi_archive_seo_content', 5 );

/**
 * Title for vehicle archives: «قطعات یدکی X».
 *
 * @param string $title Title.
 * @return string
 */
function renosi_wc_page_title( $title ) {
	if ( is_tax( 'renosi_vehicle' ) ) {
		return renosi_vehicle_title( get_queried_object() );
	}
	return $title;
}
add_filter( 'woocommerce_page_title', 'renosi_wc_page_title' );

/**
 * "Filter by vehicle" select for category / brand / shop archives.
 *
 * @param WP_Term|string $active Active vehicle term.
 */
function renosi_vehicle_filter_form( $active = '' ) {
	$tree = renosi_get_vehicle_tree();
	if ( ! $tree ) {
		return;
	}
	$action = is_product_taxonomy() ? get_term_link( get_queried_object() ) : wc_get_page_permalink( 'shop' );
	if ( is_wp_error( $action ) ) {
		return;
	}
	$current = $active instanceof WP_Term ? $active->slug : '';
	?>
	<form class="vehicle-filter" method="get" action="<?php echo esc_url( $action ); ?>" data-autosubmit>
		<label class="vehicle-filter__label" for="vehicle-filter"><?php renosi_the_icon( 'car', 16 ); ?><?php esc_html_e( 'فقط قطعات خودروی من:', 'renosi' ); ?></label>
		<div class="select-wrap">
			<select id="vehicle-filter" name="vehicle">
				<option value=""><?php esc_html_e( 'همه‌ی خودروها', 'renosi' ); ?></option>
				<?php foreach ( $tree as $node ) : ?>
					<optgroup label="<?php echo esc_attr( $node['term']->name ); ?>">
						<option value="<?php echo esc_attr( $node['term']->slug ); ?>" <?php selected( $current, $node['term']->slug ); ?>>
							<?php
							/* translators: %s: brand. */
							printf( esc_html__( 'همه‌ی مدل‌های %s', 'renosi' ), esc_html( $node['term']->name ) );
							?>
						</option>
						<?php foreach ( $node['models'] as $model ) : ?>
							<option value="<?php echo esc_attr( $model->slug ); ?>" <?php selected( $current, $model->slug ); ?>><?php echo esc_html( $model->name ); ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
		// phpcs:disable WordPress.Security.NonceVerification
		foreach ( array( 'orderby', 'express' ) as $keep ) {
			if ( ! empty( $_GET[ $keep ] ) ) {
				echo '<input type="hidden" name="' . esc_attr( $keep ) . '" value="' . esc_attr( sanitize_text_field( wp_unslash( $_GET[ $keep ] ) ) ) . '">';
			}
		}
		// phpcs:enable
		?>
		<noscript><button type="submit" class="button button--sm"><?php esc_html_e( 'اعمال', 'renosi' ); ?></button></noscript>
	</form>
	<?php
}

/*
 * -------------------------------------------------------------------------
 * Express delivery (3-hour Tehran)
 * -------------------------------------------------------------------------
 */

/**
 * Checkbox in Product data → Shipping.
 */
function renosi_express_field() {
	woocommerce_wp_checkbox(
		array(
			'id'          => '_renosi_express',
			'label'       => __( 'ارسال ۳ ساعته تهران', 'renosi' ),
			'description' => __( 'نمایش بدج «ارسال ۳ ساعته رایگان تهران» برای این محصول', 'renosi' ),
		)
	);
}
add_action( 'woocommerce_product_options_shipping', 'renosi_express_field' );

/**
 * Save the checkbox.
 *
 * @param WC_Product $product Product.
 */
function renosi_express_save( $product ) {
	// phpcs:ignore WordPress.Security.NonceVerification -- WooCommerce verifies the product save nonce.
	$product->update_meta_data( '_renosi_express', isset( $_POST['_renosi_express'] ) ? 'yes' : 'no' );
}
add_action( 'woocommerce_admin_process_product_object', 'renosi_express_save' );

/**
 * Quick edit / bulk edit support.
 */
function renosi_express_bulk_field() {
	?>
	<label class="alignleft">
		<span class="title"><?php esc_html_e( 'ارسال ۳ ساعته', 'renosi' ); ?></span>
		<span class="input-text-wrap">
			<select class="renosi_express" name="_renosi_express_bulk">
				<option value=""><?php esc_html_e( '— بدون تغییر —', 'renosi' ); ?></option>
				<option value="yes"><?php esc_html_e( 'بله', 'renosi' ); ?></option>
				<option value="no"><?php esc_html_e( 'خیر', 'renosi' ); ?></option>
			</select>
		</span>
	</label>
	<?php
}
add_action( 'woocommerce_product_bulk_edit_end', 'renosi_express_bulk_field' );

/**
 * Save bulk edit.
 *
 * @param WC_Product $product Product.
 */
function renosi_express_bulk_save( $product ) {
	// phpcs:ignore WordPress.Security.NonceVerification
	$value = isset( $_REQUEST['_renosi_express_bulk'] ) ? sanitize_key( wp_unslash( $_REQUEST['_renosi_express_bulk'] ) ) : '';
	if ( in_array( $value, array( 'yes', 'no' ), true ) ) {
		$product->update_meta_data( '_renosi_express', $value );
		$product->save();
	}
}
add_action( 'woocommerce_product_bulk_edit_save', 'renosi_express_bulk_save' );

/**
 * Does the product qualify for express delivery?
 *
 * @param WC_Product $product Product.
 * @return bool
 */
function renosi_is_express( $product ) {
	if ( ! $product || ! renosi_opt( 'express_enable' ) || ! $product->is_in_stock() ) {
		return false;
	}
	$id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	if ( renosi_opt( 'express_all' ) ) {
		return 'no' !== get_post_meta( $id, '_renosi_express', true );
	}
	return 'yes' === get_post_meta( $id, '_renosi_express', true );
}

/**
 * Express badge markup.
 *
 * @param string $class Extra class.
 * @return string
 */
function renosi_express_badge( $class = '' ) {
	return '<span class="express-badge ' . esc_attr( $class ) . '">' . renosi_icon( 'bolt', 14 ) . '<span>' . esc_html( renosi_opt( 'express_label' ) ) . '</span></span>';
}

/**
 * Express box under the price on single product.
 */
function renosi_single_express() {
	global $product;
	if ( ! renosi_is_express( $product ) ) {
		return;
	}
	?>
	<div class="express-box">
		<span class="express-box__icon"><?php renosi_the_icon( 'bolt', 22 ); ?></span>
		<div>
			<strong><?php echo esc_html( renosi_opt( 'express_label' ) ); ?></strong>
			<?php if ( renosi_opt( 'express_note' ) ) : ?>
				<p><?php echo esc_html( renosi_opt( 'express_note' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'renosi_single_express', 12 );

/**
 * Filter shop loops by ?express=1.
 *
 * @param WP_Query $q Query.
 */
function renosi_express_query( $q ) {
	if ( empty( $_GET['express'] ) || ! renosi_opt( 'express_enable' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$meta = (array) $q->get( 'meta_query' );
	if ( renosi_opt( 'express_all' ) ) {
		$meta[] = array(
			'relation' => 'OR',
			array( 'key' => '_renosi_express', 'compare' => 'NOT EXISTS' ),
			array( 'key' => '_renosi_express', 'value' => 'no', 'compare' => '!=' ),
		);
	} else {
		$meta[] = array( 'key' => '_renosi_express', 'value' => 'yes' );
	}
	$meta[] = array( 'key' => '_stock_status', 'value' => 'instock' );
	$q->set( 'meta_query', $meta );
}
add_action( 'woocommerce_product_query', 'renosi_express_query' );

/**
 * "Only 3-hour delivery" toggle in the toolbar.
 */
function renosi_express_toggle() {
	if ( ! renosi_opt( 'express_enable' ) ) {
		return;
	}
	$on  = ! empty( $_GET['express'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$url = $on ? remove_query_arg( array( 'express', 'paged' ) ) : add_query_arg( 'express', '1', remove_query_arg( 'paged' ) );
	$url = preg_replace( '#/page/\d+/?#', '/', $url );
	printf(
		'<a class="express-toggle%1$s" href="%2$s" rel="nofollow"%3$s>%4$s<span>%5$s</span></a>',
		$on ? ' is-on' : '',
		esc_url( $url ),
		$on ? ' aria-current="true"' : '',
		renosi_icon( 'bolt', 16 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'فقط ارسال ۳ ساعته', 'renosi' )
	);
}
add_action( 'woocommerce_before_shop_loop', 'renosi_express_toggle', 25 );

/**
 * Empty results while filtered: offer to clear the filters.
 */
function renosi_no_products_actions() {
	// phpcs:disable WordPress.Security.NonceVerification
	$links = array();
	if ( ! empty( $_GET['express'] ) ) {
		$links[] = array( __( 'نمایش همه (بدون فیلتر ارسال ۳ ساعته)', 'renosi' ), remove_query_arg( array( 'express', 'paged' ) ) );
	}
	if ( ! empty( $_GET['vehicle'] ) ) {
		$links[] = array( __( 'نمایش برای همه‌ی خودروها', 'renosi' ), remove_query_arg( array( 'vehicle', 'paged' ) ) );
	}
	// phpcs:enable
	if ( ! $links ) {
		return;
	}
	echo '<div class="empty-filters">';
	foreach ( $links as $link ) {
		echo '<a class="button button--secondary button--sm" href="' . esc_url( $link[1] ) . '">' . esc_html( $link[0] ) . '</a>';
	}
	echo '</div>';
}
add_action( 'woocommerce_no_products_found', 'renosi_no_products_actions', 20 );

/**
 * Close wrappers.
 */
function renosi_wc_wrapper_end() {
	echo '</main></div></div>';
}
add_action( 'woocommerce_after_main_content', 'renosi_wc_wrapper_end', 10 );

/**
 * Vehicle breadcrumbs: replace the taxonomy label crumb with the vehicles directory page.
 *
 * @param array $crumbs Crumbs.
 * @return array
 */
function renosi_wc_breadcrumb_crumbs( $crumbs ) {
	if ( ! is_tax( 'renosi_vehicle' ) && ! is_singular( 'product' ) ) {
		return $crumbs;
	}
	$tax   = get_taxonomy( 'renosi_vehicle' );
	$label = $tax ? $tax->labels->name : '';
	foreach ( $crumbs as $i => $crumb ) {
		if ( $label && $crumb[0] === $label ) {
			$url = renosi_vehicles_page_url();
			if ( $url ) {
				$crumbs[ $i ] = array( __( 'برندهای خودرو', 'renosi' ), $url );
			} else {
				unset( $crumbs[ $i ] );
			}
		}
	}
	return array_values( $crumbs );
}
add_filter( 'woocommerce_get_breadcrumb', 'renosi_wc_breadcrumb_crumbs' );

/**
 * Breadcrumb markup.
 *
 * @param array $args Args.
 * @return array
 */
function renosi_wc_breadcrumb_args( $args ) {
	$args['delimiter']   = '<span class="breadcrumbs__sep" aria-hidden="true">/</span>';
	$args['wrap_before'] = '<nav class="breadcrumbs woocommerce-breadcrumb" aria-label="' . esc_attr__( 'مسیر صفحه', 'renosi' ) . '">';
	$args['wrap_after']  = '</nav>';
	$args['home']        = _x( 'خانه', 'breadcrumb', 'renosi' );
	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'renosi_wc_breadcrumb_args' );

/*
 * -------------------------------------------------------------------------
 * Shop loop
 * -------------------------------------------------------------------------
 */
add_filter(
	'loop_shop_per_page',
	function () {
		return 16;
	}
);

add_filter(
	'loop_shop_columns',
	function () {
		return renosi_shop_has_sidebar() ? 3 : 4;
	}
);

/**
 * Toolbar wrapper (result count + ordering + mobile filter button).
 */
function renosi_shop_toolbar_open() {
	echo '<div class="shop-toolbar">';
	if ( renosi_shop_has_sidebar() ) {
		printf(
			'<button type="button" class="button button--secondary button--sm shop-toolbar__filter" data-drawer-open="filters-drawer">%1$s<span>%2$s</span></button>',
			renosi_icon( 'filter', 18 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html__( 'فیلترها', 'renosi' )
		);
	}
}
add_action( 'woocommerce_before_shop_loop', 'renosi_shop_toolbar_open', 15 );

/**
 * Close toolbar.
 */
function renosi_shop_toolbar_close() {
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'renosi_shop_toolbar_close', 35 );

// The theme renders product cards itself (woocommerce/content-product.php).
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

/**
 * Discount percentage for a product.
 *
 * @param WC_Product $product Product.
 * @return int
 */
function renosi_discount_percent( $product ) {
	if ( ! $product || ! $product->is_on_sale() ) {
		return 0;
	}
	$max = 0;
	if ( $product->is_type( 'variable' ) ) {
		foreach ( $product->get_visible_children() as $child_id ) {
			$child = wc_get_product( $child_id );
			if ( ! $child ) {
				continue;
			}
			$regular = (float) $child->get_regular_price();
			$sale    = (float) $child->get_sale_price();
			if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
				$max = max( $max, round( ( $regular - $sale ) / $regular * 100 ) );
			}
		}
	} else {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
			$max = round( ( $regular - $sale ) / $regular * 100 );
		}
	}
	return (int) $max;
}

/**
 * Sale flash with percentage.
 *
 * @param string     $html    HTML.
 * @param WP_Post    $post    Post.
 * @param WC_Product $product Product.
 * @return string
 */
function renosi_sale_flash( $html, $post, $product ) {
	$percent = renosi_discount_percent( $product );
	/* translators: %s: discount percentage. */
	$label = $percent ? sprintf( __( '%s٪ تخفیف', 'renosi' ), number_format_i18n( $percent ) ) : __( 'فروش ویژه', 'renosi' );
	return '<span class="onsale badge badge--sale">' . esc_html( $label ) . '</span>';
}
add_filter( 'woocommerce_sale_flash', 'renosi_sale_flash', 10, 3 );

/**
 * Product badges for cards.
 *
 * @param WC_Product $product Product.
 */
function renosi_product_badges( $product ) {
	echo '<div class="product-card__badges">';
	if ( $product->is_on_sale() ) {
		woocommerce_show_product_loop_sale_flash();
	}
	if ( ! $product->is_in_stock() ) {
		echo '<span class="badge badge--muted">' . esc_html__( 'ناموجود', 'renosi' ) . '</span>';
	} elseif ( $product->is_featured() ) {
		echo '<span class="badge badge--accent">' . esc_html__( 'منتخب', 'renosi' ) . '</span>';
	}
	echo '</div>';
}

/**
 * Primary category name.
 *
 * @param int $product_id Product ID.
 * @return WP_Term|null
 */
function renosi_primary_cat( $product_id ) {
	$terms = get_the_terms( $product_id, 'product_cat' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}
	$default = (int) get_option( 'default_product_cat' );
	foreach ( $terms as $term ) {
		if ( (int) $term->term_id !== $default ) {
			return $term;
		}
	}
	return $terms[0];
}

/**
 * Loop add-to-cart args: compact button.
 *
 * @param array      $args    Args.
 * @param WC_Product $product Product.
 * @return array
 */
function renosi_loop_add_to_cart_args( $args, $product ) {
	$args['class'] = trim( $args['class'] . ' button--block' );
	return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'renosi_loop_add_to_cart_args', 10, 2 );

/**
 * Descriptive loop buttons: append the product name for screen readers & crawlers
 * («افزودن به سبد — لنت ترمز…» instead of a generic «اطلاعات بیشتر»).
 *
 * @param string     $html    Link HTML.
 * @param WC_Product $product Product.
 * @return string
 */
function renosi_loop_button_label( $html, $product ) {
	$name = '<span class="screen-reader-text"> — ' . esc_html( $product->get_name() ) . '</span>';
	return preg_replace( '#</a>\s*$#', $name . '</a>', trim( $html ) );
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'renosi_loop_button_label', 20, 2 );

/**
 * Shorter add-to-cart label in loops.
 *
 * @param string     $text    Text.
 * @param WC_Product $product Product.
 * @return string
 */
function renosi_loop_add_to_cart_text( $text, $product ) {
	if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
		return __( 'افزودن به سبد', 'renosi' );
	}
	if ( ! $product->is_in_stock() ) {
		return __( 'ناموجود — مشاهده‌ی قطعه', 'renosi' );
	}
	return $text;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'renosi_loop_add_to_cart_text', 10, 2 );

/*
 * -------------------------------------------------------------------------
 * Single product
 * -------------------------------------------------------------------------
 */

/**
 * Category eyebrow above the title.
 */
function renosi_single_eyebrow() {
	$cat = renosi_primary_cat( get_the_ID() );
	if ( $cat ) {
		echo '<a class="eyebrow product-eyebrow" href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
	}
}
add_action( 'woocommerce_single_product_summary', 'renosi_single_eyebrow', 4 );

/**
 * SKU / brand line under title.
 */
function renosi_single_meta_line() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$items = array();
	if ( wc_product_sku_enabled() && $product->get_sku() ) {
		$items[] = '<span class="spec-pill">' . esc_html__( 'کد فنی:', 'renosi' ) . ' <strong class="sku" dir="ltr">' . esc_html( $product->get_sku() ) . '</strong></span>';
	}
	if ( taxonomy_exists( 'product_brand' ) ) {
		$brands = get_the_terms( $product->get_id(), 'product_brand' );
		if ( $brands && ! is_wp_error( $brands ) ) {
			$items[] = '<span class="spec-pill">' . esc_html__( 'برند:', 'renosi' ) . ' <a href="' . esc_url( get_term_link( $brands[0] ) ) . '">' . esc_html( $brands[0]->name ) . '</a></span>';
		}
	}
	if ( $product->is_in_stock() ) {
		$items[] = '<span class="spec-pill spec-pill--success">' . renosi_icon( 'check', 14 ) . esc_html__( 'موجود در انبار', 'renosi' ) . '</span>';
	}
	if ( $items ) {
		echo '<div class="product-specline">' . implode( '', $items ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'woocommerce_single_product_summary', 'renosi_single_meta_line', 6 );

/**
 * Compatible vehicles chips in summary.
 */
function renosi_single_vehicles_summary() {
	$terms = get_the_terms( get_the_ID(), 'renosi_vehicle' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return;
	}
	$models = array_filter(
		$terms,
		function ( $t ) {
			return (int) $t->parent > 0;
		}
	);
	if ( ! $models ) {
		$models = $terms;
	}
	$shown = array_slice( $models, 0, 6 );
	echo '<div class="product-fitment">';
	echo '<p class="product-fitment__title">' . renosi_icon( 'car', 18 ) . esc_html__( 'مناسب برای:', 'renosi' ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<div class="chip-list">';
	foreach ( $shown as $term ) {
		echo '<a class="chip" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
	}
	$rest = count( $models ) - count( $shown );
	if ( $rest > 0 ) {
		/* translators: %s: number of more vehicles. */
		echo '<a class="chip chip--ghost" href="#tab-title-vehicles">' . esc_html( sprintf( __( '+ %s خودروی دیگر', 'renosi' ), number_format_i18n( $rest ) ) ) . '</a>';
	}
	echo '</div></div>';
}
add_action( 'woocommerce_single_product_summary', 'renosi_single_vehicles_summary', 25 );

/**
 * Trust list after the add-to-cart form.
 */
function renosi_single_trust() {
	$items = array(
		array( 'shield', renosi_opt( 'feature_1_title' ), renosi_opt( 'feature_1_text' ) ),
		array( 'truck', renosi_opt( 'feature_2_title' ), renosi_opt( 'feature_2_text' ) ),
		array( 'refresh', renosi_opt( 'feature_3_title' ), renosi_opt( 'feature_3_text' ) ),
	);
	echo '<ul class="trust-list">';
	foreach ( $items as $item ) {
		if ( ! $item[1] ) {
			continue;
		}
		printf(
			'<li>%1$s<span><strong>%2$s</strong><small>%3$s</small></span></li>',
			renosi_icon( $item[0], 22 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( $item[1] ),
			esc_html( $item[2] )
		);
	}
	echo '</ul>';

	$phone = renosi_opt( 'phone' );
	if ( $phone ) {
		printf(
			'<a class="help-line" href="%1$s">%2$s<span>%3$s <strong dir="ltr">%4$s</strong></span></a>',
			esc_url( renosi_tel( $phone ) ),
			renosi_icon( 'headset', 20 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html__( 'درباره‌ی تطابق قطعه سؤال دارید؟', 'renosi' ),
			esc_html( $phone )
		);
	}
}
add_action( 'woocommerce_single_product_summary', 'renosi_single_trust', 35 );

/**
 * Wrap gallery + summary so the sticky gallery never overlaps tabs below.
 */
function renosi_single_main_open() {
	echo '<div class="product-main">';
}
add_action( 'woocommerce_before_single_product_summary', 'renosi_single_main_open', 0 );

/**
 * Close the wrapper (runs right after the summary column).
 */
function renosi_single_main_close() {
	echo '</div>';
}
add_action( 'woocommerce_after_single_product_summary', 'renosi_single_main_close', 0 );

// Tab panels keep a real H2 (correct heading outline for SEO/a11y) but it is visually hidden — the tab label shows it.
add_filter(
	'woocommerce_product_description_heading',
	function () {
		return __( 'توضیحات محصول', 'renosi' );
	}
);
add_filter(
	'woocommerce_product_additional_information_heading',
	function () {
		return __( 'مشخصات فنی', 'renosi' );
	}
);

/**
 * Persian tab labels + custom reviews panel.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function renosi_tabs_labels( $tabs ) {
	global $product;
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = __( 'توضیحات', 'renosi' );
	}
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title']    = __( 'مشخصات فنی', 'renosi' );
		$tabs['additional_information']['priority'] = 12;
	}
	if ( isset( $tabs['reviews'] ) && $product ) {
		$count                       = $product->get_review_count();
		$tabs['reviews']['title']    = $count ? sprintf( /* translators: %s: count */ __( 'نظرات کاربران (%s)', 'renosi' ), number_format_i18n( $count ) ) : __( 'نظرات کاربران', 'renosi' );
		$tabs['reviews']['callback'] = 'renosi_reviews_tab';
	}
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'renosi_tabs_labels', 98 );

/**
 * Reviews panel: rating summary + WooCommerce reviews template.
 */
function renosi_reviews_tab() {
	global $product;
	$count   = $product ? (int) $product->get_review_count() : 0;
	$average = $product ? (float) $product->get_average_rating() : 0;
	if ( wc_review_ratings_enabled() ) {
		$counts = $product->get_rating_counts();
		?>
		<div class="review-summary">
			<div class="review-summary__score">
				<strong><?php echo esc_html( $count ? number_format_i18n( $average, 1 ) : '—' ); ?></strong>
				<span class="review-summary__stars"><?php echo wc_get_rating_html( $average ? $average : 0, $count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="review-summary__count">
					<?php
					echo $count
						? esc_html( sprintf( /* translators: %s: count */ __( 'از %s نظر', 'renosi' ), number_format_i18n( $count ) ) )
						: esc_html__( 'هنوز نظری ثبت نشده', 'renosi' );
					?>
				</span>
			</div>
			<ul class="review-summary__bars">
				<?php for ( $star = 5; $star >= 1; $star-- ) : ?>
					<?php
					$n   = isset( $counts[ $star ] ) ? (int) $counts[ $star ] : 0;
					$pct = $count ? round( $n / $count * 100 ) : 0;
					?>
					<li>
						<span class="review-summary__label"><?php echo esc_html( number_format_i18n( $star ) ); ?> ★</span>
						<span class="review-summary__track"><span style="width:<?php echo esc_attr( $pct ); ?>%"></span></span>
						<span class="review-summary__n"><?php echo esc_html( number_format_i18n( $n ) ); ?></span>
					</li>
				<?php endfor; ?>
			</ul>
			<?php if ( comments_open() ) : ?>
				<a class="button button--secondary review-summary__cta" href="#review_form"><?php esc_html_e( 'ثبت نظر', 'renosi' ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}
	comments_template();
}

/**
 * Reviews list title.
 *
 * @param string $title Title.
 * @param int    $count Count.
 * @return string
 */
function renosi_reviews_title( $title, $count ) {
	return $count ? esc_html__( 'دیدگاه خریداران', 'renosi' ) : esc_html__( 'دیدگاه‌ها', 'renosi' );
}
add_filter( 'woocommerce_reviews_title', 'renosi_reviews_title', 10, 2 );

/**
 * Initials avatar for reviews — no external Gravatar request (faster, works where Gravatar is blocked).
 *
 * @param WP_Comment $comment Comment.
 */
function renosi_review_avatar( $comment ) {
	$name    = trim( (string) $comment->comment_author );
	$initial = $name ? ( function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 ) ) : '؟';
	echo '<span class="review-avatar" aria-hidden="true">' . esc_html( $initial ) . '</span>';
}
remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );
add_action( 'woocommerce_review_before', 'renosi_review_avatar', 10 );

add_filter(
	'woocommerce_product_related_products_heading',
	function () {
		return __( 'قطعات مرتبط', 'renosi' );
	}
);
add_filter(
	'woocommerce_product_upsells_products_heading',
	function () {
		return __( 'پیشنهاد ما برای این قطعه', 'renosi' );
	}
);
add_filter(
	'woocommerce_product_cross_sells_products_heading',
	function () {
		return __( 'ممکن است به این‌ها هم نیاز داشته باشید', 'renosi' );
	}
);

/**
 * Persian labels for the review form.
 *
 * @param array $args Comment form args.
 * @return array
 */
function renosi_review_form_args( $args ) {
	$args['title_reply']  = __( 'نظر خود را درباره‌ی این قطعه بنویسید', 'renosi' );
	$args['label_submit'] = __( 'ثبت نظر', 'renosi' );
	$args['class_submit'] = 'submit button';
	$map                  = array(
		'>Name'                      => '>' . __( 'نام', 'renosi' ),
		'>Email'                     => '>' . __( 'ایمیل', 'renosi' ),
		'>Your rating'               => '>' . __( 'امتیاز شما', 'renosi' ),
		'>Your review'               => '>' . __( 'متن نظر', 'renosi' ),
		'>Rate&hellip;'              => '>' . __( 'امتیاز دهید…', 'renosi' ),
		'>Perfect<'                  => '>' . __( 'عالی', 'renosi' ) . '<',
		'>Good<'                     => '>' . __( 'خوب', 'renosi' ) . '<',
		'>Average<'                  => '>' . __( 'متوسط', 'renosi' ) . '<',
		'>Not that bad<'             => '>' . __( 'ضعیف', 'renosi' ) . '<',
		'>Very poor<'                => '>' . __( 'خیلی بد', 'renosi' ) . '<',
	);
	foreach ( array( 'comment_field' ) as $key ) {
		if ( isset( $args[ $key ] ) ) {
			$args[ $key ] = strtr( $args[ $key ], $map );
		}
	}
	if ( ! empty( $args['fields'] ) ) {
		foreach ( $args['fields'] as $k => $html ) {
			$args['fields'][ $k ] = strtr( $html, $map );
		}
	}
	return $args;
}
add_filter( 'woocommerce_product_review_comment_form_args', 'renosi_review_form_args', 20 );

/**
 * "Compatible vehicles" tab.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function renosi_vehicle_tab( $tabs ) {
	$terms = get_the_terms( get_the_ID(), 'renosi_vehicle' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$tabs['vehicles'] = array(
			'title'    => __( 'خودروهای سازگار', 'renosi' ),
			'priority' => 15,
			'callback' => 'renosi_vehicle_tab_content',
		);
	}
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'renosi_vehicle_tab' );

/**
 * Tab content: vehicles grouped by brand.
 */
function renosi_vehicle_tab_content() {
	$terms  = get_the_terms( get_the_ID(), 'renosi_vehicle' );
	$groups = array();
	foreach ( $terms as $term ) {
		$parent_id = $term->parent ? $term->parent : $term->term_id;
		if ( ! isset( $groups[ $parent_id ] ) ) {
			$parent               = get_term( $parent_id, 'renosi_vehicle' );
			$groups[ $parent_id ] = array(
				'name'   => $parent && ! is_wp_error( $parent ) ? $parent->name : '',
				'models' => array(),
			);
		}
		if ( $term->parent ) {
			$groups[ $parent_id ]['models'][] = $term;
		}
	}
	echo '<h2 class="screen-reader-text">' . esc_html__( 'خودروهای سازگار با این قطعه', 'renosi' ) . '</h2>';
	echo '<div class="fitment-table">';
	foreach ( $groups as $group ) {
		echo '<div class="fitment-table__row"><div class="fitment-table__brand">' . esc_html( $group['name'] ) . '</div><div class="chip-list">';
		if ( $group['models'] ) {
			foreach ( $group['models'] as $model ) {
				echo '<a class="chip" href="' . esc_url( get_term_link( $model ) ) . '">' . esc_html( $model->name ) . '</a>';
			}
		} else {
			echo '<span class="chip chip--ghost">' . esc_html__( 'همه‌ی مدل‌ها', 'renosi' ) . '</span>';
		}
		echo '</div></div>';
	}
	echo '</div>';
	echo '<p class="fitment-note">' . esc_html__( 'پیش از خرید، کد فنی قطعه را با قطعه‌ی فعلی خودرو مطابقت دهید یا با پشتیبانی تماس بگیرید.', 'renosi' ) . '</p>';
}

/**
 * Related / upsell products: 4 per row.
 *
 * @param array $args Args.
 * @return array
 */
function renosi_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'renosi_related_products_args' );
add_filter(
	'woocommerce_upsell_display_args',
	function ( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}
);
add_filter(
	'woocommerce_cross_sells_columns',
	function () {
		return 4;
	}
);

/**
 * Richer Product schema for auto parts: brand, MPN, condition and compatible vehicles.
 *
 * @param array      $markup  Markup.
 * @param WC_Product $product Product.
 * @return array
 */
function renosi_product_schema( $markup, $product ) {
	if ( $product->get_sku() ) {
		$markup['mpn'] = $product->get_sku();
	}
	if ( taxonomy_exists( 'product_brand' ) && empty( $markup['brand'] ) ) {
		$brands = get_the_terms( $product->get_id(), 'product_brand' );
		if ( $brands && ! is_wp_error( $brands ) ) {
			$markup['brand'] = array(
				'@type' => 'Brand',
				'name'  => $brands[0]->name,
			);
		}
	}
	if ( isset( $markup['offers'][0] ) && is_array( $markup['offers'][0] ) ) {
		$markup['offers'][0]['itemCondition'] = 'https://schema.org/NewCondition';
	}
	$vehicles = get_the_terms( $product->get_id(), 'renosi_vehicle' );
	if ( $vehicles && ! is_wp_error( $vehicles ) ) {
		$fits = array();
		foreach ( $vehicles as $vehicle ) {
			$name = $vehicle->name;
			if ( $vehicle->parent ) {
				$parent = get_term( $vehicle->parent, 'renosi_vehicle' );
				if ( $parent && ! is_wp_error( $parent ) && false === strpos( $name, $parent->name ) ) {
					$name = $parent->name . ' ' . $name;
				}
			}
			$fits[] = array(
				'@type' => 'Car',
				'name'  => $name,
				'url'   => get_term_link( $vehicle ),
			);
		}
		$markup['isAccessoryOrSparePartFor'] = $fits;
	}
	return $markup;
}
add_filter( 'woocommerce_structured_data_product', 'renosi_product_schema', 10, 2 );

/**
 * Mobile sticky buy bar on single products.
 */
function renosi_sticky_buy_bar() {
	if ( ! is_product() ) {
		return;
	}
	$product = wc_get_product( get_queried_object_id() );
	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}
	?>
	<div class="buy-bar" data-buy-bar hidden>
		<div class="buy-bar__info">
			<span class="buy-bar__title"><?php echo esc_html( $product->get_name() ); ?></span>
			<span class="buy-bar__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
		</div>
		<button type="button" class="button button--primary" data-buy-bar-action><?php echo esc_html( $product->is_type( 'simple' ) ? __( 'افزودن به سبد', 'renosi' ) : __( 'انتخاب گزینه‌ها', 'renosi' ) ); ?></button>
	</div>
	<?php
}
add_action( 'wp_footer', 'renosi_sticky_buy_bar', 5 );

/*
 * -------------------------------------------------------------------------
 * Search: include SKU in product search
 * -------------------------------------------------------------------------
 */

/**
 * Match products (and parents of variations) by SKU in front-end search.
 *
 * @param string   $search Search SQL.
 * @param WP_Query $query  Query.
 * @return string
 */
function renosi_search_by_sku( $search, $query ) {
	global $wpdb;
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() || ! $search ) {
		return $search;
	}
	$post_type = $query->get( 'post_type' );
	if ( 'product' !== $post_type && ! ( is_array( $post_type ) && in_array( 'product', $post_type, true ) ) ) {
		return $search;
	}
	$term = trim( (string) $query->get( 's' ) );
	if ( '' === $term ) {
		return $search;
	}
	$fa   = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	$en   = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$term = str_replace( $fa, $en, $term );
	$like = '%' . $wpdb->esc_like( $term ) . '%';

	$lookup = $wpdb->prefix . 'wc_product_meta_lookup';
	$clause = $wpdb->prepare(
		"{$wpdb->posts}.ID IN ( SELECT l.product_id FROM {$lookup} l WHERE l.sku LIKE %s ) OR {$wpdb->posts}.ID IN ( SELECT p.post_parent FROM {$wpdb->posts} p INNER JOIN {$lookup} l2 ON l2.product_id = p.ID WHERE p.post_type = 'product_variation' AND l2.sku LIKE %s )", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$like,
		$like
	);

	$replaced = preg_replace( '/^\s*AND\s*\(/', ' AND ( ' . $clause . ' OR ', $search, 1 );
	return $replaced ? $replaced : $search;
}
add_filter( 'posts_search', 'renosi_search_by_sku', 10, 2 );

/*
 * -------------------------------------------------------------------------
 * Cart fragments (header badge)
 * -------------------------------------------------------------------------
 */

/**
 * Header cart count fragment.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function renosi_cart_fragments( $fragments ) {
	$count = WC()->cart->get_cart_contents_count();
	$fragments['span.cart-count'] = '<span class="cart-count' . ( $count ? '' : ' is-empty' ) . '">' . esc_html( $count ) . '</span>';
	$fragments['span.drawer-cart-count'] = '<span class="drawer-cart-count">' . esc_html( $count ) . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'renosi_cart_fragments' );

/**
 * Product search form markup used by the WooCommerce widget.
 *
 * @return string
 */
function renosi_product_search_form() {
	ob_start();
	get_template_part( 'template-parts/header/search-form' );
	return ob_get_clean();
}
add_filter( 'get_product_search_form', 'renosi_product_search_form' );

/**
 * Quantity: plus/minus buttons around inputs.
 */
function renosi_qty_minus() {
	echo '<button type="button" class="qty-btn qty-btn--minus" aria-label="' . esc_attr__( 'کاهش تعداد', 'renosi' ) . '" tabindex="-1">' . renosi_icon( 'minus', 16 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'woocommerce_before_quantity_input_field', 'renosi_qty_minus' );

/**
 * Plus button.
 */
function renosi_qty_plus() {
	echo '<button type="button" class="qty-btn qty-btn--plus" aria-label="' . esc_attr__( 'افزایش تعداد', 'renosi' ) . '" tabindex="-1">' . renosi_icon( 'plus', 16 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'woocommerce_after_quantity_input_field', 'renosi_qty_plus' );

/**
 * Render a WooCommerce products shortcode and return '' when it has no products.
 *
 * @param string $atts Shortcode attributes.
 * @return string
 */
function renosi_products_html( $atts ) {
	$html = do_shortcode( '[products ' . $atts . ']' );
	return ( false === strpos( $html, '<li' ) ) ? '' : $html;
}

/**
 * Remove the default WooCommerce wrapper on the empty-cart page for cleaner markup;
 * add an icon above the message.
 */
function renosi_empty_cart_icon() {
	echo '<div class="empty-state__icon">' . renosi_icon( 'bag', 40 ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'woocommerce_cart_is_empty', 'renosi_empty_cart_icon', 5 );
