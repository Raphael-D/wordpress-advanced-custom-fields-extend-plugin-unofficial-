<?php
/**
 * Envato Elements:
 *
 * Inspired by Media Sync plugin from dtbaker
 *
 * @package Envato/Envato_Elements
 * @since 1.0.0
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Deep photo integration with WP media library
 *
 * @since 1.0.0
 */
class Deep_Photos extends Base {


	/**
	 * Deep constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'load_custom_wp_admin_scripts' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'load_custom_wp_admin_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_custom_wp_admin_scripts' ], 100 );
	}


	public function load_custom_wp_admin_scripts(){
		if ( License::get_instance()->is_activated() ) {
			wp_enqueue_script( 'elements-deep', ENVATO_ELEMENTS_URI . 'assets/js/elements-deep.min.js', [ 'jquery' ], ENVATO_ELEMENTS_VER, true );
			wp_enqueue_style( 'elements-deep', ENVATO_ELEMENTS_URI . 'assets/css/elements-deep.min.css', [], ENVATO_ELEMENTS_VER );
			Plugin::get_instance()->admin_page_assets_react();
		}
	}

}

