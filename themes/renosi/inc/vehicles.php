<?php
/**
 * Vehicle compatibility taxonomy & "find parts by vehicle" form.
 *
 * Structure: brand (parent term) → model (child term), e.g. «رنو» → «تندر ۹۰».
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the vehicle taxonomy for products.
 */
function renosi_register_vehicle_taxonomy() {
	if ( ! post_type_exists( 'product' ) && ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$labels = array(
		'name'              => __( 'خودروهای سازگار', 'renosi' ),
		'singular_name'     => __( 'خودرو', 'renosi' ),
		'menu_name'         => __( 'خودروها', 'renosi' ),
		'search_items'      => __( 'جستجوی خودرو', 'renosi' ),
		'all_items'         => __( 'همه‌ی خودروها', 'renosi' ),
		'parent_item'       => __( 'برند خودرو', 'renosi' ),
		'parent_item_colon' => __( 'برند خودرو:', 'renosi' ),
		'edit_item'         => __( 'ویرایش خودرو', 'renosi' ),
		'update_item'       => __( 'به‌روزرسانی خودرو', 'renosi' ),
		'add_new_item'      => __( 'افزودن خودرو / برند جدید', 'renosi' ),
		'new_item_name'     => __( 'نام خودرو', 'renosi' ),
		'not_found'         => __( 'خودرویی یافت نشد.', 'renosi' ),
	);
	register_taxonomy(
		'renosi_vehicle',
		array( 'product' ),
		array(
			'labels'            => $labels,
			'description'       => __( 'برند را به عنوان مادر و مدل را به عنوان زیرمجموعه تعریف کنید.', 'renosi' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'show_in_nav_menus' => true,
			'query_var'         => 'vehicle',
			'rewrite'           => array(
				'slug'         => 'vehicle',
				'with_front'   => false,
				'hierarchical' => true,
			),
		)
	);
}
add_action( 'init', 'renosi_register_vehicle_taxonomy', 20 );

/**
 * Flush rewrite rules once after theme activation so /vehicle/ URLs work.
 */
function renosi_after_switch_theme() {
	renosi_register_vehicle_taxonomy();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'renosi_after_switch_theme' );

/**
 * Brands (parent terms) with their models and optional types (3rd level).
 *
 * Returns [ brand_id => [ 'term' => WP_Term, 'models' => WP_Term[], 'types' => [ model_id => WP_Term[] ] ] ].
 *
 * @return array
 */
function renosi_get_vehicle_tree() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$cache = array();
	if ( ! taxonomy_exists( 'renosi_vehicle' ) ) {
		return $cache;
	}
	$terms = get_terms(
		array(
			'taxonomy'   => 'renosi_vehicle',
			'hide_empty' => false,
			'orderby'    => 'name',
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		return $cache;
	}
	$by_id = array();
	foreach ( $terms as $term ) {
		$by_id[ $term->term_id ] = $term;
	}
	foreach ( $terms as $term ) {
		if ( 0 === (int) $term->parent ) {
			$cache[ $term->term_id ] = array(
				'term'   => $term,
				'models' => array(),
				'types'  => array(),
			);
		}
	}
	foreach ( $terms as $term ) {
		if ( $term->parent && isset( $cache[ $term->parent ] ) ) {
			$cache[ $term->parent ]['models'][] = $term;
		}
	}
	foreach ( $terms as $term ) {
		if ( ! $term->parent || isset( $cache[ $term->parent ] ) || ! isset( $by_id[ $term->parent ] ) ) {
			continue;
		}
		$model    = $by_id[ $term->parent ];
		$brand_id = (int) $model->parent;
		if ( isset( $cache[ $brand_id ] ) ) {
			$cache[ $brand_id ]['types'][ $model->term_id ][] = $term;
		}
	}
	return $cache;
}

/**
 * Compact JSON of the vehicle tree for the dependent selects.
 *
 * @return string
 */
function renosi_vehicle_tree_json() {
	$out = array();
	foreach ( renosi_get_vehicle_tree() as $node ) {
		$models = array();
		foreach ( $node['models'] as $model ) {
			$types = array();
			if ( ! empty( $node['types'][ $model->term_id ] ) ) {
				foreach ( $node['types'][ $model->term_id ] as $type ) {
					$types[] = array( 's' => $type->slug, 'n' => $type->name );
				}
			}
			$models[] = array( 's' => $model->slug, 'n' => $model->name, 't' => $types );
		}
		$out[] = array( 's' => $node['term']->slug, 'n' => $node['term']->name, 'm' => $models );
	}
	return wp_json_encode( $out, JSON_UNESCAPED_UNICODE );
}

/**
 * The current vehicle path (brand / model / type slugs) when on a vehicle archive.
 *
 * @return array
 */
function renosi_current_vehicle_path() {
	$path = array( '', '', '' );
	if ( ! is_tax( 'renosi_vehicle' ) ) {
		return $path;
	}
	$term  = get_queried_object();
	$chain = array_reverse( get_ancestors( $term->term_id, 'renosi_vehicle', 'taxonomy' ) );
	$chain[] = $term->term_id;
	foreach ( array_slice( $chain, 0, 3 ) as $i => $id ) {
		$t = get_term( $id, 'renosi_vehicle' );
		if ( $t && ! is_wp_error( $t ) ) {
			$path[ $i ] = $t->slug;
		}
	}
	return $path;
}

/**
 * Vehicle finder: numbered steps (brand → model → type) + part-number search.
 *
 * @param string $variant 'hero' | 'compact'.
 */
function renosi_vehicle_finder( $variant = 'hero' ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$tree = renosi_get_vehicle_tree();
	$path = renosi_current_vehicle_path();
	$uid  = esc_attr( $variant );
	if ( $tree ) :
		?>
		<form class="finder finder--<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" data-finder data-tree="<?php echo esc_attr( renosi_vehicle_tree_json() ); ?>" data-path="<?php echo esc_attr( wp_json_encode( $path ) ); ?>">
			<input type="hidden" name="renosi_finder" value="1">
			<div class="finder__step" data-step="1">
				<span class="finder__num" aria-hidden="true">۱</span>
				<label class="screen-reader-text" for="finder-brand-<?php echo $uid; // phpcs:ignore ?>"><?php esc_html_e( 'برند خودرو', 'renosi' ); ?></label>
				<select id="finder-brand-<?php echo $uid; // phpcs:ignore ?>" name="brand" data-finder-brand required>
					<option value=""><?php esc_html_e( 'برند خودرو', 'renosi' ); ?></option>
					<?php foreach ( $tree as $node ) : ?>
						<option value="<?php echo esc_attr( $node['term']->slug ); ?>" <?php selected( $path[0], $node['term']->slug ); ?>><?php echo esc_html( $node['term']->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="finder__step" data-step="2">
				<span class="finder__num" aria-hidden="true">۲</span>
				<label class="screen-reader-text" for="finder-model-<?php echo $uid; // phpcs:ignore ?>"><?php esc_html_e( 'مدل خودرو', 'renosi' ); ?></label>
				<select id="finder-model-<?php echo $uid; // phpcs:ignore ?>" name="model" data-finder-model disabled>
					<option value=""><?php esc_html_e( 'مدل خودرو', 'renosi' ); ?></option>
				</select>
			</div>
			<div class="finder__step" data-step="3">
				<span class="finder__num" aria-hidden="true">۳</span>
				<label class="screen-reader-text" for="finder-type-<?php echo $uid; // phpcs:ignore ?>"><?php esc_html_e( 'تیپ و موتور', 'renosi' ); ?></label>
				<select id="finder-type-<?php echo $uid; // phpcs:ignore ?>" name="type" data-finder-type disabled>
					<option value=""><?php esc_html_e( 'تیپ و موتور (اختیاری)', 'renosi' ); ?></option>
				</select>
			</div>
			<button type="submit" class="button button--accent button--lg finder__submit"><?php esc_html_e( 'جستجوی قطعات', 'renosi' ); ?></button>
		</form>
		<?php
	elseif ( current_user_can( 'manage_product_terms' ) ) :
		?>
		<p class="finder__hint">
			<?php
			printf(
				/* translators: %s: admin link */
				esc_html__( 'راهنمای مدیر: برای فعال شدن انتخاب خودرو، از %s برندها، مدل‌ها (زیرمجموعه‌ی برند) و در صورت نیاز تیپ/موتور (زیرمجموعه‌ی مدل) را تعریف کنید.', 'renosi' ),
				'<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=renosi_vehicle&post_type=product' ) ) . '">' . esc_html__( 'محصولات ← خودروها', 'renosi' ) . '</a>'
			);
			?>
		</p>
		<?php
	endif;
}

/**
 * Part-number (SKU) search block.
 */
function renosi_part_number_search() {
	$id = wp_unique_id( 'pn-' );
	?>
	<form class="pn-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="pn-search__label" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'جستجو با کد فنی یا نام قطعه', 'renosi' ); ?></label>
		<div class="pn-search__row">
			<input type="search" id="<?php echo esc_attr( $id ); ?>" name="s" placeholder="<?php esc_attr_e( 'مثلاً 7701208265 یا لنت جلو', 'renosi' ); ?>" autocomplete="off" required>
			<input type="hidden" name="post_type" value="product">
			<button type="submit" class="button"><?php esc_html_e( 'جستجو', 'renosi' ); ?></button>
		</div>
		<p class="pn-search__help"><?php esc_html_e( 'کد فنی معمولاً روی بدنه یا جعبه‌ی قطعه‌ی فعلی درج شده است.', 'renosi' ); ?></p>
	</form>
	<?php
}

/**
 * Turn finder submissions into clean archive URLs.
 */
function renosi_finder_redirect() {
	if ( empty( $_GET['renosi_finder'] ) || ! function_exists( 'wc_get_page_permalink' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification
	$type  = isset( $_GET['type'] ) ? sanitize_title( wp_unslash( $_GET['type'] ) ) : '';
	$model = isset( $_GET['model'] ) ? sanitize_title( wp_unslash( $_GET['model'] ) ) : '';
	$brand = isset( $_GET['brand'] ) ? sanitize_title( wp_unslash( $_GET['brand'] ) ) : '';
	$cat   = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : '';
	$q     = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	// phpcs:enable

	$vehicle = $type ? $type : ( $model ? $model : $brand );
	$url     = '';

	if ( $q ) {
		$url = add_query_arg(
			array_filter(
				array(
					's'           => $q,
					'post_type'   => 'product',
					'vehicle'     => $vehicle,
					'product_cat' => $cat,
				)
			),
			home_url( '/' )
		);
	} elseif ( $vehicle ) {
		$term = get_term_by( 'slug', $vehicle, 'renosi_vehicle' );
		if ( $term ) {
			$url = get_term_link( $term );
			if ( $cat && ! is_wp_error( $url ) ) {
				$url = add_query_arg( 'product_cat', $cat, $url );
			}
		}
	} elseif ( $cat ) {
		$term = get_term_by( 'slug', $cat, 'product_cat' );
		if ( $term ) {
			$url = get_term_link( $term );
		}
	}

	if ( ! $url || is_wp_error( $url ) ) {
		$url = wc_get_page_permalink( 'shop' );
	}
	wp_safe_redirect( $url );
	exit;
}
add_action( 'template_redirect', 'renosi_finder_redirect', 1 );

/*
 * -------------------------------------------------------------------------
 * Vehicle brand logo (term image)
 * -------------------------------------------------------------------------
 */

/**
 * Logo field on the "add term" screen.
 */
function renosi_vehicle_add_logo_field() {
	?>
	<div class="form-field term-logo-wrap">
		<label><?php esc_html_e( 'لوگوی برند', 'renosi' ); ?></label>
		<?php renosi_vehicle_logo_picker( 0 ); ?>
		<p class="description"><?php esc_html_e( 'برای برندها (مادر) لوگو بگذارید؛ ترجیحاً PNG یا SVG با پس‌زمینه‌ی شفاف، مربعی.', 'renosi' ); ?></p>
	</div>
	<?php
}
add_action( 'renosi_vehicle_add_form_fields', 'renosi_vehicle_add_logo_field' );

/**
 * Logo field on the "edit term" screen.
 *
 * @param WP_Term $term Term.
 */
function renosi_vehicle_edit_logo_field( $term ) {
	?>
	<tr class="form-field term-logo-wrap">
		<th scope="row"><label><?php esc_html_e( 'لوگو / تصویر', 'renosi' ); ?></label></th>
		<td><?php renosi_vehicle_logo_picker( (int) get_term_meta( $term->term_id, 'thumbnail_id', true ) ); ?></td>
	</tr>
	<?php
}
add_action( 'renosi_vehicle_edit_form_fields', 'renosi_vehicle_edit_logo_field' );

/**
 * Media picker markup.
 *
 * @param int $id Attachment ID.
 */
function renosi_vehicle_logo_picker( $id ) {
	$src = $id ? wp_get_attachment_image_url( $id, 'thumbnail' ) : '';
	?>
	<div class="renosi-logo-picker">
		<img src="<?php echo esc_url( $src ); ?>" alt="" style="width:72px;height:72px;object-fit:contain;border:1px solid #ddd;border-radius:8px;background:#fff;<?php echo $src ? '' : 'display:none;'; ?>">
		<input type="hidden" name="renosi_vehicle_logo" value="<?php echo esc_attr( $id ? $id : '' ); ?>">
		<p>
			<button type="button" class="button renosi-logo-select"><?php esc_html_e( 'انتخاب تصویر', 'renosi' ); ?></button>
			<button type="button" class="button-link-delete renosi-logo-remove" <?php echo $src ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'حذف', 'renosi' ); ?></button>
		</p>
	</div>
	<?php
	wp_nonce_field( 'renosi_vehicle_logo', 'renosi_vehicle_logo_nonce' );
}

/**
 * Save the logo.
 *
 * @param int $term_id Term ID.
 */
function renosi_vehicle_save_logo( $term_id ) {
	if ( ! isset( $_POST['renosi_vehicle_logo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['renosi_vehicle_logo_nonce'] ) ), 'renosi_vehicle_logo' ) ) {
		return;
	}
	$id = isset( $_POST['renosi_vehicle_logo'] ) ? absint( $_POST['renosi_vehicle_logo'] ) : 0;
	if ( $id ) {
		update_term_meta( $term_id, 'thumbnail_id', $id );
	} else {
		delete_term_meta( $term_id, 'thumbnail_id' );
	}
}
add_action( 'created_renosi_vehicle', 'renosi_vehicle_save_logo' );
add_action( 'edited_renosi_vehicle', 'renosi_vehicle_save_logo' );

/**
 * Media library script on the vehicle term screens.
 *
 * @param string $hook Admin page.
 */
function renosi_vehicle_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) || ! isset( $_GET['taxonomy'] ) || 'renosi_vehicle' !== $_GET['taxonomy'] ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script(
		'media-editor',
		"jQuery(function($){
			$(document).on('click','.renosi-logo-select',function(e){
				e.preventDefault(); var wrap=$(this).closest('.renosi-logo-picker');
				var frame=wp.media({title:'لوگو',button:{text:'انتخاب'},library:{type:'image'},multiple:false});
				frame.on('select',function(){var a=frame.state().get('selection').first().toJSON();
					wrap.find('input[type=hidden]').val(a.id);
					wrap.find('img').attr('src',(a.sizes&&a.sizes.thumbnail?a.sizes.thumbnail.url:a.url)).show();
					wrap.find('.renosi-logo-remove').show();});
				frame.open();
			});
			$(document).on('click','.renosi-logo-remove',function(e){
				e.preventDefault(); var wrap=$(this).closest('.renosi-logo-picker');
				wrap.find('input[type=hidden]').val(''); wrap.find('img').hide(); $(this).hide();
			});
			$(document).ajaxComplete(function(ev,xhr,s){ if(s.data && s.data.indexOf('action=add-tag')>-1){ $('.renosi-logo-picker img').hide(); $('.renosi-logo-picker input[type=hidden]').val(''); } });
		});"
	);
}
add_action( 'admin_enqueue_scripts', 'renosi_vehicle_admin_assets' );

/*
 * -------------------------------------------------------------------------
 * Front-end helpers
 * -------------------------------------------------------------------------
 */

/**
 * Logo (or monogram fallback) for a vehicle term.
 *
 * @param WP_Term $term Term.
 * @param string  $size Image size.
 * @return string
 */
function renosi_vehicle_logo( $term, $size = 'thumbnail' ) {
	$id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( ! $id && $term->parent ) {
		$id = (int) get_term_meta( $term->parent, 'thumbnail_id', true );
	}
	if ( $id ) {
		return wp_get_attachment_image(
			$id,
			$size,
			false,
			array(
				'class'   => 'vehicle-logo__img',
				'alt'     => $term->name,
				'loading' => 'lazy',
			)
		);
	}
	$source = $term;
	if ( $term->parent ) {
		$parent = get_term( $term->parent, 'renosi_vehicle' );
		if ( $parent && ! is_wp_error( $parent ) ) {
			$source = $parent;
		}
	}
	$letters = function_exists( 'mb_substr' ) ? mb_substr( $source->name, 0, 1 ) : substr( $source->name, 0, 1 );
	return '<span class="vehicle-logo__mono" aria-hidden="true">' . esc_html( $letters ) . '</span>';
}

/**
 * Number of products assigned to a term including its children.
 *
 * @param WP_Term $term Term.
 * @return int
 */
function renosi_vehicle_count( $term ) {
	$count    = (int) $term->count;
	$children = get_terms(
		array(
			'taxonomy'   => 'renosi_vehicle',
			'parent'     => $term->term_id,
			'hide_empty' => false,
			'fields'     => 'all',
		)
	);
	if ( ! is_wp_error( $children ) ) {
		foreach ( $children as $child ) {
			$count += (int) $child->count;
		}
	}
	return $count;
}

/**
 * SEO-friendly archive title for vehicles: «قطعات یدکی تندر ۹۰».
 *
 * @param WP_Term $term Term.
 * @return string
 */
function renosi_vehicle_title( $term ) {
	$name = $term->name;
	if ( $term->parent ) {
		$parent = get_term( $term->parent, 'renosi_vehicle' );
		if ( $parent && ! is_wp_error( $parent ) && $parent->parent && false === strpos( $name, $parent->name ) ) {
			$name = $parent->name . ' ' . $name; // Type: «پژو ۲۰۶ تیپ ۵».
		}
	}
	/* translators: %s: vehicle name. */
	return sprintf( __( 'قطعات یدکی %s', 'renosi' ), $name );
}

/**
 * Hero side panel: brand tabs → model links.
 */
function renosi_vehicle_side_panel() {
	$tree = renosi_get_vehicle_tree();
	if ( ! $tree ) {
		return;
	}
	$tree = array_slice( $tree, 0, 8, true );
	?>
	<aside class="vehicle-panel" aria-labelledby="vehicle-panel-title" data-tabs>
		<div class="vehicle-panel__head">
			<h2 id="vehicle-panel-title" class="vehicle-panel__title"><?php renosi_the_icon( 'car', 20 ); ?><?php esc_html_e( 'انتخاب سریع خودرو', 'renosi' ); ?></h2>
			<?php $renosi_all = renosi_vehicles_page_url(); ?>
			<?php if ( $renosi_all ) : ?>
				<a class="vehicle-panel__all" href="<?php echo esc_url( $renosi_all ); ?>"><?php esc_html_e( 'همه', 'renosi' ); ?></a>
			<?php endif; ?>
		</div>
		<div class="vehicle-panel__brands" role="tablist" aria-label="<?php esc_attr_e( 'برند خودرو', 'renosi' ); ?>">
			<?php $i = 0; foreach ( $tree as $node ) : ?>
				<button type="button" class="vehicle-panel__brand<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" id="vp-tab-<?php echo esc_attr( $node['term']->term_id ); ?>" aria-controls="vp-panel-<?php echo esc_attr( $node['term']->term_id ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" data-tab>
					<span class="vehicle-logo vehicle-logo--sm"><?php echo renosi_vehicle_logo( $node['term'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><?php echo esc_html( $node['term']->name ); ?></span>
				</button>
			<?php $i++; endforeach; ?>
		</div>
		<?php $i = 0; foreach ( $tree as $node ) : ?>
			<div class="vehicle-panel__models" role="tabpanel" id="vp-panel-<?php echo esc_attr( $node['term']->term_id ); ?>" aria-labelledby="vp-tab-<?php echo esc_attr( $node['term']->term_id ); ?>" <?php echo 0 === $i ? '' : 'hidden'; ?>>
				<ul>
					<?php foreach ( $node['models'] as $model ) : ?>
						<li>
							<a href="<?php echo esc_url( get_term_link( $model ) ); ?>">
								<span><?php echo esc_html( $model->name ); ?></span>
								<?php renosi_the_icon( 'chevron-left', 16 ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<a class="vehicle-panel__brand-link" href="<?php echo esc_url( get_term_link( $node['term'] ) ); ?>">
					<?php
					/* translators: %s: brand name. */
					printf( esc_html__( 'همه‌ی قطعات %s', 'renosi' ), esc_html( $node['term']->name ) );
					?>
					<?php renosi_the_icon( 'arrow-left', 16 ); ?>
				</a>
			</div>
		<?php $i++; endforeach; ?>
	</aside>
	<?php
}

/**
 * URL of the page using the "vehicle brands" template (if any).
 *
 * @return string
 */
function renosi_vehicles_page_url() {
	static $url = null;
	if ( null !== $url ) {
		return $url;
	}
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => 'page-templates/vehicles.php', // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	$url = $pages ? get_permalink( $pages[0] ) : '';
	return $url;
}

/**
 * Grid of vehicle brands (used on home + vehicles page).
 *
 * @param int $limit Max brands (0 = all).
 */
function renosi_vehicle_brand_grid( $limit = 0 ) {
	$tree = renosi_get_vehicle_tree();
	if ( ! $tree ) {
		return;
	}
	if ( $limit ) {
		$tree = array_slice( $tree, 0, $limit, true );
	}
	echo '<ul class="vehicle-grid">';
	foreach ( $tree as $node ) {
		$term  = $node['term'];
		$count = renosi_vehicle_count( $term );
		?>
		<li>
			<a class="vehicle-tile" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
				<span class="vehicle-logo"><?php echo renosi_vehicle_logo( $term, 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="vehicle-tile__name"><?php echo esc_html( $term->name ); ?></span>
				<span class="vehicle-tile__meta">
					<?php
					/* translators: 1: models count, 2: products count. */
					printf( esc_html__( '%1$s مدل · %2$s قطعه', 'renosi' ), esc_html( number_format_i18n( count( $node['models'] ) ) ), esc_html( number_format_i18n( $count ) ) );
					?>
				</span>
			</a>
		</li>
		<?php
	}
	echo '</ul>';
}

/**
 * On a type (3rd-level) archive, also list parts assigned to its parent model:
 * a part that fits «پژو ۲۰۶» fits every «پژو ۲۰۶» type unless stated otherwise.
 *
 * @param WP_Query $q Query.
 */
function renosi_vehicle_type_tax_query( $q ) {
	if ( is_admin() || ! $q->is_main_query() || empty( $q->tax_query->queries ) ) {
		return;
	}
	foreach ( $q->tax_query->queries as $i => $clause ) {
		if ( ! is_array( $clause ) || empty( $clause['taxonomy'] ) || 'renosi_vehicle' !== $clause['taxonomy'] || empty( $clause['terms'] ) ) {
			continue;
		}
		$field = isset( $clause['field'] ) ? $clause['field'] : 'term_id';
		$terms = (array) $clause['terms'];
		if ( 1 !== count( $terms ) ) {
			continue;
		}
		$term = get_term_by( $field, $terms[0], 'renosi_vehicle' );
		if ( ! $term || ! $term->parent ) {
			continue;
		}
		$parent = get_term( $term->parent, 'renosi_vehicle' );
		if ( ! $parent || is_wp_error( $parent ) || ! $parent->parent ) {
			continue; // Only for 3rd level (brand › model › type).
		}
		$q->tax_query->queries[ $i ]['terms']            = array( $term->term_id, $parent->term_id );
		$q->tax_query->queries[ $i ]['field']            = 'term_id';
		$q->tax_query->queries[ $i ]['include_children'] = false;
	}
}
add_action( 'parse_tax_query', 'renosi_vehicle_type_tax_query' );
