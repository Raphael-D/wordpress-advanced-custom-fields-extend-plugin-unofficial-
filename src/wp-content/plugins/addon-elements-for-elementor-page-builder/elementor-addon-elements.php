<?php
/**
 * Plugin Name: Elementor Addon Elements
 * Description: Add new elements to Elementor page builder plugin.
 * Plugin URI: https://www.elementoraddons.com/elements-addon-elements/
 * Author: WebTechStreet
 * Version: 1.5.3
 * Author URI: https://webtechstreet.com/
 *
 * Text Domain: wts-eae
 * @package WTS_EAE
 */
define( 'EAE_FILE', __FILE__ );
define( 'EAE_URL', plugins_url( '/', __FILE__ ) );
define( 'EAE_PATH', plugin_dir_path( __FILE__ ) );
define( 'EAE_SCRIPT_SUFFIX', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
define( 'EAE_VERSION', '1.5.3');


if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}

if(!function_exists('is_plugin_active')){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

require_once 'inc/bootstrap.php';