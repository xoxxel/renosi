<?php
/**
 * Customizer settings.
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default option values.
 *
 * @return array
 */
function renosi_defaults() {
	return array(
		// General.
		'accent_color'     => '#c94f0a',
		'persian_digits'   => true,
		'sticky_header'    => true,
		'mobile_bottom_nav' => true,
		'perf_jquery_footer' => true,
		'perf_webp'          => true,

		// Top bar.
		'topbar_show'      => true,
		'topbar_text'      => __( 'ارسال رایگان ۳ ساعته در تهران — ارسال سریع به سراسر کشور با ضمانت اصالت', 'renosi' ),

		// Express delivery.
		'express_enable'   => true,
		'express_all'      => false,
		'express_label'    => __( 'ارسال ۳ ساعته رایگان تهران', 'renosi' ),
		'express_note'     => __( 'برای سفارش‌های ثبت‌شده تا ساعت ۱۶ در روزهای کاری، تحویل در کمتر از ۳ ساعت در تهران — رایگان.', 'renosi' ),

		// Contact.
		'phone'            => '021-12345678',
		'mobile'           => '0912-000-0000',
		'email'            => 'info@renosi.ir',
		'address'          => __( 'تهران، خیابان امین‌حضور، بازار لوازم یدکی', 'renosi' ),
		'hours'            => __( 'شنبه تا پنجشنبه، ۹ تا ۱۸', 'renosi' ),
		'about'            => __( 'رنوسی؛ فروشنده و تامین‌کننده‌ی قطعات یدکی اصلی و باکیفیت خودرو. قطعه‌ی مناسب خودروی خود را با اطمینان و قیمت منصفانه تهیه کنید.', 'renosi' ),

		// Social.
		'social_instagram' => '',
		'social_telegram'  => '',
		'social_whatsapp'  => '',
		'social_eitaa'     => '',
		'social_aparat'    => '',

		// Hero.
		'hero_show'        => true,
		'hero_title'       => __( 'خودروی خود را انتخاب کنید تا قطعات سازگار نمایش داده شود', 'renosi' ),
		'hero_text'        => __( 'فروشگاه اینترنتی قطعات یدکی خودرو؛ هزاران قطعه‌ی اصلی و استاندارد با ضمانت اصالت، قیمت شفاف و ارسال سریع.', 'renosi' ),
		'slider_autoplay'  => true,
		'slide_1_image'    => '',
		'slide_1_title'    => __( 'قطعات اصلی، با ضمانت اصالت', 'renosi' ),
		'slide_1_text'     => __( 'لنت، فیلتر، قطعات موتور و تعلیق برای خودروهای داخلی و خارجی', 'renosi' ),
		'slide_1_btn'      => __( 'مشاهده‌ی فروشگاه', 'renosi' ),
		'slide_1_url'      => '',
		'slide_2_image'    => '',
		'slide_2_title'    => __( 'ارسال ۳ ساعته رایگان در تهران', 'renosi' ),
		'slide_2_text'     => __( 'سفارش تا ساعت ۱۶، تحویل همان روز', 'renosi' ),
		'slide_2_btn'      => __( 'قطعات با ارسال فوری', 'renosi' ),
		'slide_2_url'      => '',
		'slide_3_image'    => '',
		'slide_3_title'    => __( 'قطعه را با کد فنی پیدا کنید', 'renosi' ),
		'slide_3_text'     => __( 'کد درج‌شده روی قطعه‌ی فعلی را جستجو کنید؛ نتیجه دقیق و مطمئن', 'renosi' ),
		'slide_3_btn'      => __( 'مشاوره‌ی رایگان', 'renosi' ),
		'slide_3_url'      => '#contact',
		'slide_4_image'    => '',
		'slide_4_title'    => '',
		'slide_4_text'     => '',
		'slide_4_btn'      => '',
		'slide_4_url'      => '',
		'slide_5_image'    => '',
		'slide_5_title'    => '',
		'slide_5_text'     => '',
		'slide_5_btn'      => '',
		'slide_5_url'      => '',

		// Home sections.
		'home_finder'      => true,
		'home_vehicles'    => true,
		'home_vehicles_title' => __( 'قطعات بر اساس برند خودرو', 'renosi' ),
		'home_categories'  => true,
		'home_categories_title' => __( 'دسته‌بندی قطعات', 'renosi' ),
		'home_features'    => true,
		'home_new'         => true,
		'home_new_title'   => __( 'تازه‌ترین قطعات', 'renosi' ),
		'home_sale'        => true,
		'home_sale_title'  => __( 'پیشنهادهای ویژه', 'renosi' ),
		'home_best'        => true,
		'home_best_title'  => __( 'پرفروش‌ترین‌ها', 'renosi' ),
		'home_products_count' => 8,
		'home_brands'      => true,
		'home_cta'         => true,
		'home_cta_title'   => __( 'قطعه‌ی مورد نظرتان را پیدا نکردید؟', 'renosi' ),
		'home_cta_text'    => __( 'کد فنی یا مشخصات خودرو را برای ما بفرستید؛ کارشناسان ما قطعه‌ی مناسب را برایتان تامین می‌کنند.', 'renosi' ),
		'home_blog'        => true,
		'home_blog_title'  => __( 'مجله‌ی رنوسی', 'renosi' ),

		// Features.
		'feature_1_title'  => __( 'ضمانت اصالت', 'renosi' ),
		'feature_1_text'   => __( 'تمام قطعات اصلی و دارای ضمانت', 'renosi' ),
		'feature_2_title'  => __( 'ارسال ۳ ساعته رایگان', 'renosi' ),
		'feature_2_text'   => __( 'ویژه‌ی تهران؛ سراسر کشور با پست و تیپاکس', 'renosi' ),
		'feature_3_title'  => __( '۷ روز ضمانت بازگشت', 'renosi' ),
		'feature_3_text'   => __( 'در صورت عدم تطابق قطعه', 'renosi' ),
		'feature_4_title'  => __( 'مشاوره‌ی تخصصی', 'renosi' ),
		'feature_4_text'   => __( 'پیش از خرید با ما مشورت کنید', 'renosi' ),

		// Footer.
		'copyright'        => __( 'تمامی حقوق برای فروشگاه رنوسی محفوظ است.', 'renosi' ),
	);
}

/**
 * Get a theme option with defaults.
 *
 * @param string $key Option key.
 * @return mixed
 */
function renosi_opt( $key ) {
	$defaults = renosi_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( 'renosi_' . $key, $default );
}

/**
 * Sanitize checkbox.
 *
 * @param mixed $value Value.
 * @return bool
 */
function renosi_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Register customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function renosi_customize_register( $wp_customize ) {
	$d = renosi_defaults();

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->add_panel(
		'renosi',
		array(
			'title'    => __( 'تنظیمات قالب رنوسی', 'renosi' ),
			'priority' => 20,
		)
	);

	$sections = array(
		'renosi_general'  => __( 'عمومی و رنگ‌ها', 'renosi' ),
		'renosi_header'   => __( 'سربرگ و نوار بالا', 'renosi' ),
		'renosi_hero'     => __( 'صفحه‌ی اصلی — انتخاب خودرو', 'renosi' ),
		'renosi_slider'   => __( 'صفحه‌ی اصلی — اسلایدر بنر', 'renosi' ),
		'renosi_express'  => __( 'ارسال ۳ ساعته تهران', 'renosi' ),
		'renosi_home'     => __( 'صفحه‌ی اصلی — بخش‌ها', 'renosi' ),
		'renosi_features' => __( 'مزیت‌های فروشگاه', 'renosi' ),
		'renosi_contact'  => __( 'اطلاعات تماس و شبکه‌های اجتماعی', 'renosi' ),
		'renosi_footer'   => __( 'فوتر', 'renosi' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'renosi' ) );
	}

	$add = function ( $key, $section, $label, $type = 'text', $extra = array() ) use ( $wp_customize, $d ) {
		$sanitize = 'sanitize_text_field';
		if ( 'checkbox' === $type ) {
			$sanitize = 'renosi_sanitize_checkbox';
		} elseif ( 'url' === $type ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'textarea' === $type ) {
			$sanitize = 'sanitize_textarea_field';
		} elseif ( 'number' === $type ) {
			$sanitize = 'absint';
		} elseif ( 'email' === $type ) {
			$sanitize = 'sanitize_email';
		}
		$wp_customize->add_setting(
			'renosi_' . $key,
			array(
				'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
				'sanitize_callback' => $sanitize,
			)
		);
		$wp_customize->add_control(
			'renosi_' . $key,
			array_merge(
				array(
					'label'   => $label,
					'section' => $section,
					'type'    => $type,
				),
				$extra
			)
		);
	};

	// General.
	$wp_customize->add_setting(
		'renosi_accent_color',
		array(
			'default'           => $d['accent_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'renosi_accent_color',
			array(
				'label'       => __( 'رنگ اصلی (برند)', 'renosi' ),
				'description' => __( 'برای دکمه‌ها، لینک‌ها و عناصر فعال استفاده می‌شود.', 'renosi' ),
				'section'     => 'renosi_general',
			)
		)
	);
	$add( 'persian_digits', 'renosi_general', __( 'نمایش اعداد به صورت فارسی (۱۲۳)', 'renosi' ), 'checkbox' );
	$add( 'sticky_header', 'renosi_general', __( 'سربرگ چسبان هنگام اسکرول', 'renosi' ), 'checkbox' );
	$add( 'mobile_bottom_nav', 'renosi_general', __( 'نوار ناوبری پایین صفحه در موبایل', 'renosi' ), 'checkbox' );
	$add(
		'perf_jquery_footer',
		'renosi_general',
		__( 'بهینه‌سازی سرعت: بارگذاری jQuery و اسکریپت‌های وابسته در انتهای صفحه', 'renosi' ),
		'checkbox',
		array( 'description' => __( 'سرعت بارگذاری را بهتر می‌کند. اگر افزونه‌ی قدیمی‌ای بعد از فعال‌سازی درست کار نکرد، این گزینه را خاموش کنید.', 'renosi' ) )
	);
	$add(
		'perf_webp',
		'renosi_general',
		__( 'ساخت نسخه‌های WebP برای تصاویر جدید (سبک‌تر)', 'renosi' ),
		'checkbox',
		array( 'description' => __( 'اندازه‌های کوچک تصاویری که از این به بعد بارگذاری می‌کنید (کارت محصول، گالری) با فرمت WebP ساخته می‌شوند؛ حدود ۳۰ تا ۷۰ درصد سبک‌تر. اگر سرور از WebP پشتیبانی نکند، خودکار همان JPG/PNG ساخته می‌شود.', 'renosi' ) )
	);

	// Header.
	$add( 'topbar_show', 'renosi_header', __( 'نمایش نوار بالا', 'renosi' ), 'checkbox' );
	$add( 'topbar_text', 'renosi_header', __( 'متن نوار بالا', 'renosi' ) );

	// Hero (finder).
	$add( 'hero_show', 'renosi_hero', __( 'نمایش بخش بالای صفحه (انتخاب خودرو + اسلایدر)', 'renosi' ), 'checkbox' );
	$add( 'hero_title', 'renosi_hero', __( 'عنوان کارت انتخاب خودرو (H1 صفحه‌ی اصلی)', 'renosi' ) );
	$add( 'hero_text', 'renosi_hero', __( 'توضیح کوتاه سایت (توضیحات متا برای گوگل)', 'renosi' ), 'textarea' );

	// Slider.
	$add( 'slider_autoplay', 'renosi_slider', __( 'چرخش خودکار اسلایدها (هر ۶ ثانیه)', 'renosi' ), 'checkbox' );
	for ( $n = 1; $n <= 5; $n++ ) {
		$wp_customize->add_setting(
			"renosi_slide_{$n}_image",
			array(
				'default'           => '',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				"renosi_slide_{$n}_image",
				array(
					/* translators: %d: slide number. */
					'label'       => sprintf( __( 'اسلاید %d — تصویر', 'renosi' ), $n ),
					'description' => 1 === $n ? __( 'ابعاد پیشنهادی ۱۸۰۰×۹۵۰ پیکسل. اگر متن داخل خود تصویر است، عنوان و توضیح را خالی بگذارید. اسلایدی که نه تصویر دارد نه عنوان، نمایش داده نمی‌شود.', 'renosi' ) : '',
					'section'     => 'renosi_slider',
					'mime_type'   => 'image',
				)
			)
		);
		/* translators: %d: slide number. */
		$add( "slide_{$n}_title", 'renosi_slider', sprintf( __( 'اسلاید %d — عنوان', 'renosi' ), $n ) );
		/* translators: %d: slide number. */
		$add( "slide_{$n}_text", 'renosi_slider', sprintf( __( 'اسلاید %d — توضیح', 'renosi' ), $n ) );
		/* translators: %d: slide number. */
		$add( "slide_{$n}_btn", 'renosi_slider', sprintf( __( 'اسلاید %d — متن دکمه', 'renosi' ), $n ) );
		/* translators: %d: slide number. */
		$add( "slide_{$n}_url", 'renosi_slider', sprintf( __( 'اسلاید %d — لینک (خالی = فروشگاه)', 'renosi' ), $n ) );
	}

	// Express.
	$add( 'express_enable', 'renosi_express', __( 'فعال بودن بدج ارسال ۳ ساعته', 'renosi' ), 'checkbox' );
	$add(
		'express_all',
		'renosi_express',
		__( 'نمایش برای همه‌ی محصولات موجود', 'renosi' ),
		'checkbox',
		array( 'description' => __( 'اگر خاموش باشد، فقط محصولاتی که در ویرایش محصول (تب حمل‌ونقل) گزینه‌ی «ارسال ۳ ساعته» را دارند بدج می‌گیرند.', 'renosi' ) )
	);
	$add( 'express_label', 'renosi_express', __( 'متن بدج', 'renosi' ) );
	$add( 'express_note', 'renosi_express', __( 'توضیح در صفحه‌ی محصول', 'renosi' ), 'textarea' );

	// Home sections.
	$add( 'home_vehicles', 'renosi_home', __( 'برندهای خودرو', 'renosi' ), 'checkbox' );
	$add( 'home_vehicles_title', 'renosi_home', __( 'عنوان برندهای خودرو', 'renosi' ) );
	$add( 'home_categories', 'renosi_home', __( 'دسته‌بندی‌ها', 'renosi' ), 'checkbox' );
	$add( 'home_categories_title', 'renosi_home', __( 'عنوان دسته‌بندی‌ها', 'renosi' ) );
	$add( 'home_features', 'renosi_home', __( 'مزیت‌های فروشگاه', 'renosi' ), 'checkbox' );
	$add( 'home_new', 'renosi_home', __( 'تازه‌ترین محصولات', 'renosi' ), 'checkbox' );
	$add( 'home_new_title', 'renosi_home', __( 'عنوان تازه‌ترین‌ها', 'renosi' ) );
	$add( 'home_sale', 'renosi_home', __( 'محصولات تخفیف‌دار', 'renosi' ), 'checkbox' );
	$add( 'home_sale_title', 'renosi_home', __( 'عنوان تخفیف‌دارها', 'renosi' ) );
	$add( 'home_best', 'renosi_home', __( 'پرفروش‌ترین‌ها', 'renosi' ), 'checkbox' );
	$add( 'home_best_title', 'renosi_home', __( 'عنوان پرفروش‌ترین‌ها', 'renosi' ) );
	$add(
		'home_products_count',
		'renosi_home',
		__( 'تعداد محصول در هر بخش', 'renosi' ),
		'number',
		array( 'input_attrs' => array( 'min' => 4, 'max' => 24, 'step' => 1 ) )
	);
	$add( 'home_brands', 'renosi_home', __( 'برندهای سازنده‌ی قطعه (برندهای ووکامرس)', 'renosi' ), 'checkbox' );
	$add( 'home_cta', 'renosi_home', __( 'بخش درخواست قطعه', 'renosi' ), 'checkbox' );
	$add( 'home_cta_title', 'renosi_home', __( 'عنوان درخواست قطعه', 'renosi' ) );
	$add( 'home_cta_text', 'renosi_home', __( 'متن درخواست قطعه', 'renosi' ), 'textarea' );
	$add( 'home_blog', 'renosi_home', __( 'آخرین نوشته‌های وبلاگ', 'renosi' ), 'checkbox' );
	$add( 'home_blog_title', 'renosi_home', __( 'عنوان وبلاگ', 'renosi' ) );

	// Features.
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: feature number. */
		$add( "feature_{$i}_title", 'renosi_features', sprintf( __( 'مزیت %d — عنوان', 'renosi' ), $i ) );
		/* translators: %d: feature number. */
		$add( "feature_{$i}_text", 'renosi_features', sprintf( __( 'مزیت %d — توضیح', 'renosi' ), $i ) );
	}

	// Contact.
	$add( 'phone', 'renosi_contact', __( 'تلفن ثابت', 'renosi' ) );
	$add( 'mobile', 'renosi_contact', __( 'تلفن همراه / واتساپ', 'renosi' ) );
	$add( 'email', 'renosi_contact', __( 'ایمیل', 'renosi' ), 'email' );
	$add( 'address', 'renosi_contact', __( 'نشانی', 'renosi' ), 'textarea' );
	$add( 'hours', 'renosi_contact', __( 'ساعات کاری', 'renosi' ) );
	$add( 'social_instagram', 'renosi_contact', __( 'اینستاگرام (لینک کامل)', 'renosi' ), 'url' );
	$add( 'social_telegram', 'renosi_contact', __( 'تلگرام (لینک کامل)', 'renosi' ), 'url' );
	$add( 'social_whatsapp', 'renosi_contact', __( 'واتساپ (لینک کامل)', 'renosi' ), 'url' );
	$add( 'social_eitaa', 'renosi_contact', __( 'ایتا (لینک کامل)', 'renosi' ), 'url' );
	$add( 'social_aparat', 'renosi_contact', __( 'آپارات (لینک کامل)', 'renosi' ), 'url' );

	// Footer.
	$add( 'about', 'renosi_footer', __( 'درباره‌ی فروشگاه', 'renosi' ), 'textarea' );
	$add( 'copyright', 'renosi_footer', __( 'متن کپی‌رایت', 'renosi' ) );

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => function () {
					bloginfo( 'name' );
				},
			)
		);
	}
}
add_action( 'customize_register', 'renosi_customize_register' );
