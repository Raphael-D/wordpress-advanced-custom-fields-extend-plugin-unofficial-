<?php
/**
 * Envato Elements:
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Envato Elements plugin.
 *
 * The main plugin handler class is responsible for initializing Envato Elements. The
 * class registers and all the components required to run the plugin.
 *
 * @since 0.0.2
 */
class Plugin extends Base {


	/**
	 * Initializing Envato Elements plugin.
	 *
	 * @since 0.0.2
	 * @access private
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_head', [ $this, 'admin_menu_icon' ] );
		add_action( 'plugins_loaded', [ $this, 'db_upgrade_check' ] );
		add_action( 'envato_elements_cron', [ $this, 'run_cron' ] );

	}


	/**
	 * Runs in the admin init WordPress hook and sets everything up.
	 *
	 * @since 0.0.2
	 * @access public
	 */
	public function admin_init() {

	}

	/**
	 * Runs the daily cron action.
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function run_cron() {

	}

	/**
	 * Sets up the admin menu options.
	 *
	 * @since 0.0.2
	 * @access public
	 */
	public function admin_menu() {

		if ( License::get_instance()->is_activated() ) {
			$page = add_menu_page(
				__( 'Envato Elements', 'envato-elements' ),
				__( 'Elements', 'envato-elements' ),
				'edit_posts',
				ENVATO_ELEMENTS_SLUG,
				[ Collection::get_instance(), 'admin_menu_open_react' ],
				'',
				'58.6'
			);
			add_action( 'admin_print_scripts-' . $page, [ $this, 'admin_page_assets_react' ] );

			$page = add_submenu_page(
				ENVATO_ELEMENTS_SLUG,
				__( 'Envato Elements', 'envato-elements' ),
				__( 'Template Kits', 'envato-elements' ),
				'edit_posts',
				ENVATO_ELEMENTS_SLUG,
				[ Collection::get_instance(), 'admin_menu_open_react' ] );
			add_action( 'admin_print_scripts-' . $page, [ $this, 'admin_page_assets_react' ] );

			if ( defined( 'ENVATO_ELEMENTS_DEV' ) && ENVATO_ELEMENTS_DEV ) {
				$page = add_submenu_page(
					ENVATO_ELEMENTS_SLUG,
					__( '(old)', 'envato-elements' ),
					__( '(old)', 'envato-elements' ),
					'edit_posts',
					ENVATO_ELEMENTS_SLUG . '-old',
					[ Collection::get_instance(), 'admin_menu_open' ] );
				add_action( 'admin_print_scripts-' . $page, [ $this, 'admin_page_assets' ] );
			}

		} else {
			$page = add_menu_page(
				__( 'Envato Elements', 'envato-elements' ),
				'Elements',
				'edit_posts',
				ENVATO_ELEMENTS_SLUG,
				[ License::get_instance(), 'admin_menu_open' ],
				'',
				'58.6'
			);
			add_action( 'admin_print_scripts-' . $page, [ $this, 'admin_page_assets' ] );
		}


	}


	/**
	 * Add a font based menu icon.
	 * We have to do it this way because our plugin stylesheet only runs when the admin page is active.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu_icon() {
		// Fonts directory URL.
		$fonts_dir_url = ENVATO_ELEMENTS_URI . 'assets/fonts/';

		// Create font styles.
		$style = '<style type="text/css">
				/*<![CDATA[*/
				@font-face {
					font-family: "' . ENVATO_ELEMENTS_SLUG . '";
					src:url("' . $fonts_dir_url . 'envato.eot?20180730");
					src:url("' . $fonts_dir_url . 'envato.eot?#iefix20180730") format("embedded-opentype"),
					url("' . $fonts_dir_url . 'envato.woff?20180730") format("woff"),
					url("' . $fonts_dir_url . 'envato.ttf?20180730") format("truetype"),
					url("' . $fonts_dir_url . 'envato.svg?20180730#envato") format("svg");
					font-weight: normal;
					font-style: normal;
				}
				#adminmenu .toplevel_page_' . ENVATO_ELEMENTS_SLUG . ' .menu-icon-generic div.wp-menu-image:before {
					font: normal 20px/1 "' . ENVATO_ELEMENTS_SLUG . '" !important;
					content: "\e600";
					speak: none;
					padding: 6px 0;
					height: 34px;
					width: 20px;
					display: inline-block;
					-webkit-font-smoothing: antialiased;
					-moz-osx-font-smoothing: grayscale;
					-webkit-transition: all .1s ease-in-out;
					-moz-transition:    all .1s ease-in-out;
					transition:         all .1s ease-in-out;
				}
				/*]]>*/
			</style>';

		// Remove space after colons.
		$style = str_replace( ': ', ':', $style );

		// Remove whitespace.
		echo str_replace( array( "\r\n", "\r", "\n", "\t", '	', '		', '		', '  ', '    ' ), '', $style );
	}


	public function admin_page_assets() {

		wp_enqueue_style( 'envato-elements-admin', ENVATO_ELEMENTS_URI . 'assets/css/main.min.css', [], filemtime( ENVATO_ELEMENTS_DIR . 'assets/css/main.min.css' ) );
		wp_register_script( 'envato-elements-admin', ENVATO_ELEMENTS_URI . 'assets/js/app.min.js', [], filemtime( ENVATO_ELEMENTS_DIR . 'assets/js/app.min.js' ) );
		$collections_url = Collection::get_instance()->get_url();
		$bits            = wp_parse_url( $collections_url );
		wp_localize_script(
			'envato-elements-admin', 'envato_elements_admin', [
				'api_nonce'         => wp_create_nonce( 'wp_rest' ),
//				'api_url'           => rest_url( ENVATO_ELEMENTS_SLUG . '/v1/' ),
				// Swapping from unreliable REST API over to an admin-ajax endpoint.
				'api_url'           => admin_url( 'admin-ajax.php?action=envato_elements&endpoint=' ),
				'license_activated' => License::get_instance()->is_activated(),
				'maintenance_mode'  => false, // We can prevent API calls if in maintenance mode.
				'admin_base'        => trailingslashit( dirname( $bits['path'] ) ),
				'admin_slug'        => $bits['query'],
				'collections_base'  => $bits['path'] . '?' . $bits['query'],
				'categories'        => Category::get_instance()->categories,
			]
		);
		wp_enqueue_script( 'envato-elements-admin' );

		wp_enqueue_style( 'envato-elements-google-font', 'https://fonts.googleapis.com/css?family=Rubik', 'envato-elements-admin' );

		$this->load_admin_templates();
	}

	public function admin_page_assets_react() {

		wp_enqueue_style( 'envato-elements-admin', ENVATO_ELEMENTS_URI . 'assets/react/admin.css', [], filemtime( ENVATO_ELEMENTS_DIR . 'assets/react/admin.css' ) );
		wp_register_script( 'envato-elements-react', ENVATO_ELEMENTS_URI . 'assets/react/admin.js', [], filemtime( ENVATO_ELEMENTS_DIR . 'assets/react/admin.js' ) );
		wp_localize_script( 'envato-elements-react', 'envato_elements_react', Options::get_instance()->get_public_settings() );
		wp_enqueue_script( 'envato-elements-react' );

	}

	public function load_admin_templates() {
		require_once ENVATO_ELEMENTS_DIR . 'views/templates/collections.php';
		require_once ENVATO_ELEMENTS_DIR . 'views/templates/collection.php';
		require_once ENVATO_ELEMENTS_DIR . 'views/templates/collection-blocks.php';
		require_once ENVATO_ELEMENTS_DIR . 'views/templates/collection-photos.php';
		require_once ENVATO_ELEMENTS_DIR . 'views/templates/imports.php';
		require_once ENVATO_ELEMENTS_DIR . 'views/templates/general.php';
		require_once ENVATO_ELEMENTS_DIR . 'views/templates/magic.php';
	}


	public function db_upgrade_check() {
		if ( get_option( 'envato_elements_version' ) !== ENVATO_ELEMENTS_VER ) {
			$this->activation();
		}
	}

	public function activation() {
		update_option( 'envato_elements_version', ENVATO_ELEMENTS_VER );
		if ( ! get_option( 'envato_elements_install_time' ) ) {
			update_option( 'envato_elements_install_time', time() );
		}
		if ( ! wp_next_scheduled( 'envato_elements_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'envato_elements_cron' );
		}
		Notices::get_instance()->activation();
		Notifications::get_instance()->activation();
		License::get_instance()->activation();
	}

}
