<?php
/**
 * Renosi functions and definitions
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

define( 'RENOSI_VERSION', '1.4.0' );
define( 'RENOSI_DIR', get_template_directory() );
define( 'RENOSI_URI', get_template_directory_uri() );

require RENOSI_DIR . '/inc/icons.php';
require RENOSI_DIR . '/inc/setup.php';
require RENOSI_DIR . '/inc/customizer.php';
require RENOSI_DIR . '/inc/enqueue.php';
require RENOSI_DIR . '/inc/template-tags.php';
require RENOSI_DIR . '/inc/template-functions.php';
require RENOSI_DIR . '/inc/vehicles.php';
require RENOSI_DIR . '/inc/setup-wizard.php';
require RENOSI_DIR . '/inc/seo.php';
require RENOSI_DIR . '/inc/performance.php';

if ( class_exists( 'WooCommerce' ) ) {
	require RENOSI_DIR . '/inc/woocommerce.php';
}
