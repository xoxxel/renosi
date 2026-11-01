<?php
/**
 * Quick setup page: WooCommerce check + one-click demo content.
 *
 * نمایش ← راه‌اندازی رنوسی
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the admin page.
 */
function renosi_setup_menu() {
	add_theme_page(
		__( 'راه‌اندازی رنوسی', 'renosi' ),
		__( 'راه‌اندازی رنوسی', 'renosi' ),
		'manage_options',
		'renosi-setup',
		'renosi_setup_page'
	);
}
add_action( 'admin_menu', 'renosi_setup_menu' );

/**
 * Admin notice: WooCommerce missing, or the store is still empty.
 */
function renosi_admin_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_renosi-setup' === $screen->id ) {
		return;
	}
	$url = admin_url( 'themes.php?page=renosi-setup' );
	if ( ! class_exists( 'WooCommerce' ) ) {
		echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'قالب رنوسی یک قالب فروشگاهی است و برای کار به افزونه‌ی ووکامرس نیاز دارد.', 'renosi' ) . '</strong> ';
		echo '<a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'راه‌اندازی قالب', 'renosi' ) . '</a></p></div>';
		return;
	}
	if ( get_option( 'renosi_demo_imported' ) || get_option( 'renosi_setup_dismissed' ) ) {
		return;
	}
	$count = wp_count_posts( 'product' );
	if ( $count && (int) $count->publish > 0 ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'فروشگاه شما هنوز محصولی ندارد. می‌توانید با یک کلیک محتوای نمونه (محصول، دسته‌بندی، خودرو، منو و صفحه‌ی اصلی) بسازید تا ظاهر کامل قالب را ببینید.', 'renosi' ) . ' ';
	echo '<a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'راه‌اندازی سریع', 'renosi' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'renosi_admin_notices' );

/**
 * Setup page markup.
 */
function renosi_setup_page() {
	$has_wc   = class_exists( 'WooCommerce' );
	$imported = get_option( 'renosi_demo_imported' );
	$message  = isset( $_GET['renosi_msg'] ) ? sanitize_key( wp_unslash( $_GET['renosi_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<div class="wrap" style="max-width:820px">
		<h1><?php esc_html_e( 'راه‌اندازی قالب رنوسی', 'renosi' ); ?></h1>

		<?php if ( 'imported' === $message ) : ?>
			<div class="notice notice-success"><p>
				<?php esc_html_e( 'محتوای نمونه با موفقیت ساخته شد.', 'renosi' ); ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'مشاهده‌ی سایت', 'renosi' ); ?></a>
			</p></div>
		<?php elseif ( 'removed' === $message ) : ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'محصولات نمونه حذف شدند.', 'renosi' ); ?></p></div>
		<?php endif; ?>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '۱. افزونه‌ی ووکامرس', 'renosi' ); ?></h2>
			<?php if ( $has_wc ) : ?>
				<p style="color:#1d8a3a">✓ <?php esc_html_e( 'ووکامرس نصب و فعال است.', 'renosi' ); ?></p>
			<?php else : ?>
				<p><?php esc_html_e( 'برای فروشگاه، سبد خرید، دسته‌بندی‌ها و جستجوی خودرو باید ووکامرس نصب و فعال شود.', 'renosi' ); ?></p>
				<p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'نصب ووکامرس', 'renosi' ); ?></a></p>
			<?php endif; ?>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '۲. محتوای نمونه (اختیاری)', 'renosi' ); ?></h2>
			<p><?php esc_html_e( 'این گزینه موارد زیر را می‌سازد تا سایت دقیقاً شبیه دموی قالب شود. بعداً می‌توانید آن‌ها را ویرایش یا با محصولات واقعی جایگزین کنید:', 'renosi' ); ?></p>
			<ul style="list-style:disc;padding-inline-start:20px">
				<li><?php esc_html_e( 'برگه‌ی «خانه» به عنوان صفحه‌ی اصلی و برگه‌ی «مجله» برای نوشته‌ها', 'renosi' ); ?></li>
				<li><?php esc_html_e( '۸ دسته‌بندی قطعه با تصویر و ۱۲ محصول نمونه با قیمت، تخفیف و کد فنی', 'renosi' ); ?></li>
				<li><?php esc_html_e( 'برندها و مدل‌های خودرو برای جستجوی قطعه بر اساس خودرو', 'renosi' ); ?></li>
				<li><?php esc_html_e( 'منوی اصلی و فیلترهای سایدبار فروشگاه', 'renosi' ); ?></li>
			</ul>
			<?php if ( ! $has_wc ) : ?>
				<p><em><?php esc_html_e( 'ابتدا ووکامرس را نصب و فعال کنید.', 'renosi' ); ?></em></p>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block">
					<input type="hidden" name="action" value="renosi_import_demo">
					<?php wp_nonce_field( 'renosi_import_demo' ); ?>
					<?php submit_button( $imported ? __( 'ساخت دوباره‌ی محتوای نمونه', 'renosi' ) : __( 'ساخت محتوای نمونه', 'renosi' ), 'primary large', 'submit', false ); ?>
				</form>
				<?php if ( $imported ) : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-inline-start:8px" onsubmit="return confirm('<?php echo esc_js( __( 'محصولات نمونه حذف شوند؟', 'renosi' ) ); ?>');">
						<input type="hidden" name="action" value="renosi_remove_demo">
						<?php wp_nonce_field( 'renosi_remove_demo' ); ?>
						<?php submit_button( __( 'حذف محصولات نمونه', 'renosi' ), 'secondary large', 'submit', false ); ?>
					</form>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<div class="card" style="max-width:none">
			<h2><?php esc_html_e( '۳. تنظیمات ظاهری', 'renosi' ); ?></h2>
			<p><?php esc_html_e( 'رنگ برند، متن بنر، اطلاعات تماس، شبکه‌های اجتماعی و بخش‌های صفحه‌ی اصلی:', 'renosi' ); ?></p>
			<p><a class="button" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=renosi' ) ); ?>"><?php esc_html_e( 'باز کردن سفارشی‌ساز', 'renosi' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'منوها', 'renosi' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>"><?php esc_html_e( 'ابزارک‌ها', 'renosi' ); ?></a></p>
		</div>
	</div>
	<?php
}

/**
 * Sideload a bundled demo image (cached by option so it is only uploaded once).
 *
 * @param string $name File name without extension.
 * @return int Attachment ID or 0.
 */
function renosi_demo_image( $name ) {
	$map = get_option( 'renosi_demo_images', array() );
	if ( ! empty( $map[ $name ] ) && get_post( $map[ $name ] ) ) {
		return (int) $map[ $name ];
	}
	$src = RENOSI_DIR . '/assets/demo/' . $name . '.jpg';
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$tmp = wp_tempnam( $name . '.jpg' );
	if ( ! $tmp || ! copy( $src, $tmp ) ) {
		return 0;
	}
	$id = media_handle_sideload(
		array(
			'name'     => 'renosi-' . $name . '.jpg',
			'tmp_name' => $tmp,
		),
		0
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	update_post_meta( $id, '_renosi_demo', 1 );
	$map[ $name ] = $id;
	update_option( 'renosi_demo_images', $map, false );
	return (int) $id;
}

/**
 * Create (or fetch) a term.
 *
 * @param string $name     Name.
 * @param string $slug     Slug.
 * @param string $taxonomy Taxonomy.
 * @param int    $parent   Parent ID.
 * @return int
 */
function renosi_demo_term( $name, $slug, $taxonomy, $parent = 0 ) {
	$term = term_exists( $slug, $taxonomy );
	if ( ! $term ) {
		$term = wp_insert_term(
			$name,
			$taxonomy,
			array(
				'slug'   => $slug,
				'parent' => $parent,
			)
		);
	}
	return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
}

/**
 * Find or create a page by slug.
 *
 * @param string $title Title.
 * @param string $slug  Slug.
 * @return int
 */
function renosi_demo_page( $title, $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return (int) $page->ID;
	}
	return (int) wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_title'  => $title,
			'post_name'   => $slug,
			'post_status' => 'publish',
		)
	);
}

/**
 * Import handler.
 */
function renosi_import_demo() {
	if ( ! current_user_can( 'manage_options' ) || ! class_exists( 'WooCommerce' ) ) {
		wp_die( esc_html__( 'دسترسی ندارید.', 'renosi' ) );
	}
	check_admin_referer( 'renosi_import_demo' );
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}

	// Categories.
	$c          = array();
	$c['brake'] = renosi_demo_term( 'سیستم ترمز', 'brake', 'product_cat' );
	$c['pads']  = renosi_demo_term( 'لنت ترمز', 'brake-pads', 'product_cat', $c['brake'] );
	$c['discs'] = renosi_demo_term( 'دیسک ترمز', 'brake-discs', 'product_cat', $c['brake'] );
	$c['filter'] = renosi_demo_term( 'فیلترها', 'filters', 'product_cat' );
	$c['oilf']  = renosi_demo_term( 'فیلتر روغن', 'oil-filter', 'product_cat', $c['filter'] );
	$c['airf']  = renosi_demo_term( 'فیلتر هوا', 'air-filter', 'product_cat', $c['filter'] );
	$c['susp']  = renosi_demo_term( 'سیستم تعلیق', 'suspension', 'product_cat' );
	$c['engine'] = renosi_demo_term( 'قطعات موتور', 'engine', 'product_cat' );
	$c['ign']   = renosi_demo_term( 'سیستم جرقه', 'ignition', 'product_cat', $c['engine'] );
	$c['elec']  = renosi_demo_term( 'برقی و باتری', 'electrical', 'product_cat' );
	$c['oil']   = renosi_demo_term( 'روغن و مایعات', 'oils', 'product_cat' );
	$c['light'] = renosi_demo_term( 'چراغ و بدنه', 'lights', 'product_cat' );
	$c['cool']  = renosi_demo_term( 'سیستم خنک‌کاری', 'cooling', 'product_cat' );

	$cat_images = array(
		'brake'  => 'disc',
		'filter' => 'oilfilter',
		'susp'   => 'shock',
		'engine' => 'belt',
		'elec'   => 'battery',
		'oil'    => 'oil',
		'light'  => 'headlight',
		'cool'   => 'radiator',
	);
	foreach ( $cat_images as $key => $img ) {
		if ( $c[ $key ] && ! get_term_meta( $c[ $key ], 'thumbnail_id', true ) ) {
			update_term_meta( $c[ $key ], 'thumbnail_id', renosi_demo_image( $img ) );
		}
	}

	// Vehicles.
	$v    = array();
	$tree = array(
		array( 'رنو', 'renault', array( 'تندر ۹۰' => 'tondar-90', 'ساندرو' => 'sandero', 'مگان' => 'megane', 'داستر' => 'duster' ) ),
		array( 'ایران‌خودرو', 'ikco', array( 'پژو ۲۰۶' => 'peugeot-206', 'پژو ۴۰۵' => 'peugeot-405', 'دنا' => 'dena', 'سمند' => 'samand' ) ),
		array( 'سایپا', 'saipa', array( 'پراید' => 'pride', 'تیبا' => 'tiba', 'کوییک' => 'quick' ) ),
		array( 'کیا', 'kia', array( 'ریو' => 'rio', 'سراتو' => 'cerato' ) ),
	);
	foreach ( $tree as $brand ) {
		$bid = renosi_demo_term( $brand[0], $brand[1], 'renosi_vehicle' );
		foreach ( $brand[2] as $model_name => $model_slug ) {
			$v[ $model_slug ] = renosi_demo_term( $model_name, $model_slug, 'renosi_vehicle', $bid );
		}
	}
	// Optional 3rd level: type / engine.
	$types = array(
		'tondar-90'   => array( 'E2 دنده‌ای ۱.۶' => 'tondar-90-e2', 'پلاس اتوماتیک' => 'tondar-90-plus-at' ),
		'peugeot-206' => array( 'تیپ ۲' => 'peugeot-206-t2', 'تیپ ۵' => 'peugeot-206-t5', 'صندوق‌دار V8' => 'peugeot-206-sd-v8' ),
		'peugeot-405' => array( 'GLX بنزینی' => 'peugeot-405-glx', 'دوگانه‌سوز' => 'peugeot-405-cng' ),
	);
	foreach ( $types as $model_slug => $list ) {
		if ( empty( $v[ $model_slug ] ) ) {
			continue;
		}
		foreach ( $list as $type_name => $type_slug ) {
			renosi_demo_term( $type_name, $type_slug, 'renosi_vehicle', $v[ $model_slug ] );
		}
	}

	// Products: name, cat, image, regular, sale, sku, vehicles, featured.
	$products = array(
		array( 'لنت ترمز جلو تندر ۹۰', 'pads', 'pads', 1890000, 1590000, 'RN-7701208265', array( 'tondar-90', 'sandero' ), true ),
		array( 'دیسک ترمز جلو پژو ۲۰۶', 'discs', 'disc', 2450000, 0, 'IK-1640201280', array( 'peugeot-206' ), false ),
		array( 'فیلتر روغن W712', 'oilf', 'oilfilter', 420000, 365000, 'W712-75', array( 'peugeot-206', 'peugeot-405', 'samand', 'dena' ), true ),
		array( 'فیلتر هوای موتور تندر ۹۰', 'airf', 'airfilter', 380000, 0, 'RN-8200431051', array( 'tondar-90', 'sandero', 'duster' ), false ),
		array( 'شمع موتور پلاتینیوم', 'ign', 'spark', 690000, 590000, 'FR7DPP33', array( 'peugeot-206', 'pride', 'tiba', 'quick' ), false ),
		array( 'کمک فنر جلو پژو ۴۰۵', 'susp', 'shock', 3250000, 0, 'IK-313579', array( 'peugeot-405', 'samand' ), true ),
		array( 'باتری ۶۰ آمپر', 'elec', 'battery', 5900000, 5450000, 'BAT-S4005', array( 'tondar-90', 'megane', 'peugeot-405', 'rio' ), false ),
		array( 'چراغ جلو راست ساندرو', 'light', 'headlight', 4100000, 0, 'RN-260108289R', array( 'sandero' ), false ),
		array( 'تسمه تایم پژو ۲۰۶ تیپ ۵', 'engine', 'belt', 1150000, 980000, 'IK-0816H4', array( 'peugeot-206' ), false ),
		array( 'رادیاتور آب تیبا', 'cool', 'radiator', 3600000, 0, 'SP-S6110100', array( 'tiba', 'pride', 'quick' ), false ),
		array( 'روغن موتور ۱۰W40 چهار لیتری', 'oil', 'oil', 1450000, 1290000, 'OIL-10W40-4L', array(), true ),
		array( 'واتر پمپ تندر ۹۰', 'cool', 'pump', 1780000, 0, 'RN-7701478018', array( 'tondar-90', 'sandero', 'duster' ), false ),
	);

	$short = '<ul><li>کیفیت و ابعاد مطابق استاندارد قطعه‌ی اصلی</li><li>دارای ضمانت اصالت</li><li>نصب آسان بدون نیاز به تغییر</li></ul>';
	$long  = '<p>این قطعه با رعایت استانداردهای تولید و کنترل کیفیت ساخته شده و جایگزین مطمئنی برای قطعه‌ی اصلی خودرو است. پیش از خرید، کد فنی را با قطعه‌ی فعلی خودرو مطابقت دهید.</p><h3>ویژگی‌ها</h3><ul><li>عمر طولانی و عملکرد پایدار</li><li>مقاومت بالا در برابر حرارت و سایش</li><li>بسته‌بندی پلمپ کارخانه</li></ul>';

	foreach ( $products as $i => $p ) {
		if ( wc_get_product_id_by_sku( $p[5] ) ) {
			continue;
		}
		$product = new WC_Product_Simple();
		$product->set_name( $p[0] );
		$product->set_status( 'publish' );
		$product->set_regular_price( (string) $p[3] );
		if ( $p[4] ) {
			$product->set_sale_price( (string) $p[4] );
		}
		$product->set_sku( $p[5] );
		$product->set_category_ids( array_filter( array( $c[ $p[1] ] ) ) );
		$product->set_image_id( renosi_demo_image( $p[2] ) );
		$product->set_featured( $p[7] );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( 7 === $i ? 0 : 10 + $i );
		$product->set_short_description( $short );
		$product->set_description( $long );
		$product->set_total_sales( 120 - $i * 9 );
		if ( in_array( $i, array( 0, 2, 4, 6, 10, 11 ), true ) ) {
			$product->update_meta_data( '_renosi_express', 'yes' );
		}
		$id = $product->save();
		if ( $id ) {
			update_post_meta( $id, '_renosi_demo', 1 );
			if ( $p[6] ) {
				$ids = array();
				foreach ( $p[6] as $slug ) {
					if ( ! empty( $v[ $slug ] ) ) {
						$ids[] = $v[ $slug ];
					}
				}
				wp_set_object_terms( $id, $ids, 'renosi_vehicle' );
			}
		}
	}

	// SEO text for a category (shown collapsible under the product grid).
	if ( $c['brake'] && ! term_description( $c['brake'], 'product_cat' ) ) {
		wp_update_term(
			$c['brake'],
			'product_cat',
			array( 'description' => '<h2>خرید قطعات سیستم ترمز</h2><p>سیستم ترمز مهم‌ترین بخش ایمنی خودرو است. در این دسته لنت ترمز، دیسک ترمز و سایر قطعات سیستم ترمز خودروهای داخلی و خارجی را با ضمانت اصالت و قیمت روز پیدا می‌کنید. برای اطمینان از تطابق، خودروی خود را از فیلتر بالای صفحه انتخاب کنید یا کد فنی قطعه را جستجو کنید.</p><h3>چه زمانی لنت ترمز را عوض کنیم؟</h3><p>صدای جیرجیر هنگام ترمزگیری، افزایش مسافت توقف و روشن شدن چراغ هشدار، نشانه‌های رایج فرسودگی لنت هستند. به طور معمول لنت جلو هر ۲۰ تا ۳۰ هزار کیلومتر نیاز به بررسی دارد.</p><p>تمام قطعات این دسته دارای ضمانت بازگشت هستند و در تهران با ارسال ۳ ساعته رایگان تحویل داده می‌شوند.</p>' )
		);
	}

	// Pages & reading settings.
	$home = renosi_demo_page( 'خانه', 'home' );
	$blog = renosi_demo_page( 'مجله', 'blog' );
	$cars = renosi_demo_page( 'برندهای خودرو', 'vehicles' );
	update_post_meta( $cars, '_wp_page_template', 'page-templates/vehicles.php' );
	if ( 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
		update_option( 'page_for_posts', $blog );
	}

	// Primary menu (only if none assigned).
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) ) {
		$menu_id = wp_create_nav_menu( 'منوی اصلی رنوسی' );
		if ( is_wp_error( $menu_id ) ) {
			$existing = wp_get_nav_menu_object( 'منوی اصلی رنوسی' );
			$menu_id  = $existing ? $existing->term_id : 0;
		}
		if ( $menu_id ) {
			$items = wp_get_nav_menu_items( $menu_id );
			if ( ! $items ) {
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'خانه', 'menu-item-url' => home_url( '/' ), 'menu-item-status' => 'publish' ) );
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'فروشگاه', 'menu-item-object' => 'page', 'menu-item-object-id' => wc_get_page_id( 'shop' ), 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
				$parent = wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'خودروها', 'menu-item-url' => '#', 'menu-item-status' => 'publish' ) );
				foreach ( array( 'renault', 'ikco', 'saipa', 'kia' ) as $slug ) {
					$t = get_term_by( 'slug', $slug, 'renosi_vehicle' );
					if ( $t ) {
						wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $t->name, 'menu-item-object' => 'renosi_vehicle', 'menu-item-object-id' => $t->term_id, 'menu-item-type' => 'taxonomy', 'menu-item-parent-id' => $parent, 'menu-item-status' => 'publish' ) );
					}
				}
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'برندهای خودرو', 'menu-item-object' => 'page', 'menu-item-object-id' => $cars, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'مجله', 'menu-item-object' => 'page', 'menu-item-object-id' => $blog, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'تماس با ما', 'menu-item-url' => home_url( '/#contact' ), 'menu-item-status' => 'publish' ) );
			}
			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	// Shop sidebar filters (only if empty).
	$sidebars = get_option( 'sidebars_widgets', array() );
	if ( empty( $sidebars['sidebar-shop'] ) ) {
		$cats_w            = get_option( 'widget_woocommerce_product_categories', array() );
		$cats_w[90]        = array( 'title' => 'دسته‌بندی‌ها', 'count' => 1, 'hierarchical' => 1, 'hide_empty' => 1 );
		$cats_w['_multiwidget'] = 1;
		update_option( 'widget_woocommerce_product_categories', $cats_w );
		$price_w           = get_option( 'widget_woocommerce_price_filter', array() );
		$price_w[90]       = array( 'title' => 'محدوده‌ی قیمت' );
		$price_w['_multiwidget'] = 1;
		update_option( 'widget_woocommerce_price_filter', $price_w );
		$sidebars['sidebar-shop'] = array( 'woocommerce_product_categories-90', 'woocommerce_price_filter-90' );
		update_option( 'sidebars_widgets', $sidebars );
	}

	if ( function_exists( 'wc_update_product_lookup_tables' ) ) {
		wc_update_product_lookup_tables();
	}
	delete_transient( 'wc_term_counts' );
	flush_rewrite_rules();
	update_option( 'renosi_demo_imported', time() );

	wp_safe_redirect( admin_url( 'themes.php?page=renosi-setup&renosi_msg=imported' ) );
	exit;
}
add_action( 'admin_post_renosi_import_demo', 'renosi_import_demo' );

/**
 * Remove demo products & images.
 */
function renosi_remove_demo() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'دسترسی ندارید.', 'renosi' ) );
	}
	check_admin_referer( 'renosi_remove_demo' );
	$ids = get_posts(
		array(
			'post_type'      => array( 'product', 'attachment' ),
			'post_status'    => 'any',
			'meta_key'       => '_renosi_demo', // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'         => 'ids',
			'posts_per_page' => -1,
		)
	);
	foreach ( $ids as $id ) {
		if ( 'attachment' === get_post_type( $id ) ) {
			wp_delete_attachment( $id, true );
		} else {
			wp_delete_post( $id, true );
		}
	}
	delete_option( 'renosi_demo_images' );
	delete_option( 'renosi_demo_imported' );
	update_option( 'renosi_setup_dismissed', 1 );
	wp_safe_redirect( admin_url( 'themes.php?page=renosi-setup&renosi_msg=removed' ) );
	exit;
}
add_action( 'admin_post_renosi_remove_demo', 'renosi_remove_demo' );

/**
 * After activation, send the admin to the setup page once.
 */
function renosi_activation_redirect() {
	if ( is_admin() && current_user_can( 'manage_options' ) && ! wp_doing_ajax() ) {
		set_transient( 'renosi_activation_redirect', 1, 60 );
	}
}
add_action( 'after_switch_theme', 'renosi_activation_redirect' );

/**
 * Perform the activation redirect.
 */
function renosi_maybe_redirect() {
	if ( get_transient( 'renosi_activation_redirect' ) ) {
		delete_transient( 'renosi_activation_redirect' );
		if ( ! isset( $_GET['activate-multi'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_safe_redirect( admin_url( 'themes.php?page=renosi-setup' ) );
			exit;
		}
	}
}
add_action( 'admin_init', 'renosi_maybe_redirect' );
