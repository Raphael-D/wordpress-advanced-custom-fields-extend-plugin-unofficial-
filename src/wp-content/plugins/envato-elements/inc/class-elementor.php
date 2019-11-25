<?php
/**
 * Envato Elements:
 *
 * Elementor core integration here.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Elementor registration and management.
 *
 * @since 0.0.2
 */
class Elementor extends Base {

	const POPUP_SLUG = 'popup';

	/**
	 * Elementor constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'wp_ajax_elementor_get_template_data', [ $this, 'get_template_data' ], 1 );
		//add_filter( 'option_elementor_remote_info_library', [ $this, 'inject_elementor_popups' ], 10, 2 );
	}


	/**
	 * This filters on the Elementor category list (Stored in WP option).
	 * We add our own categories to the list if they are missing.
	 *
	 * @param $library_info
	 * @param $option
	 *
	 * @return mixed
	 */
	public function inject_elementor_popups( $library_info, $option = [] ) {
		if ( isset( $library_info['types_data']['popup']['categories'] ) && is_array( $library_info['types_data']['popup']['categories'] ) ) {
			$library_info['types_data']['popup']['categories'][] = 'envato elements';
			if ( ! empty( $library_info['templates'] ) && is_array( $library_info['templates'] ) ) {
				// append our popup templates.
				$block_manager = new Collection_Elementor_Blocks();
				$all_blocks    = $block_manager->get_all_blocks();
				\Envato_Elements\Collection_Elementor_Blocks::get_instance()->check_memory_limit();
				if ( $all_blocks && ! is_wp_error( $all_blocks ) ) {
					foreach ( $all_blocks['data'] as $template_data ) {
						if ( ! empty( $template_data['type'] ) && isset( $template_data['type'][ self::POPUP_SLUG ] ) ) {
							$template = \Envato_Elements\Collection_Elementor_Blocks::get_instance()->filter_template( $template_data, [ 'collectionId' => $template_data['collection_id'] ] );
							if ( $template ) {
								$library_info['templates'][] = [
									'id'                => $template['templateId'],
									'title'             => $template['templateName'],
									'thumbnail'         => ! empty( $template['previewThumb2x'] ) ? $template['previewThumb2x'] : $template['previewThumb'],
									'tmpl_created'      => time(),
									'author'            => 'Envato',
									'tags'              => [ 'Envato' ],
									'url'               => $template['previewUrl'],
									'type'              => 'popup',
									'subtype'           => 'envato elements',
									'menu_order'        => 0,
									'popularity_index'  => 400,
									'trend_index'       => 450,
									'is_pro'            => 1,
									'has_page_settings' => 1,
									'collection_id'     => $template_data['collection_id'],
									'source'            => 'remote',
									'date'              => time(),
									'human_date'        => date( 'Y-m-d' ),
//									'favorite'        => ! empty( $template['templateInstalled'] ),
								];
							}
						}
					}
				}

			}
		}

		return $library_info;
	}

	/**
	 * Figure out if we should enable deep Elementor integration.
	 *
	 * @return bool
	 */
	public function is_deep_integration_enabled() {
		return class_exists( '\Elementor\Plugin' ) && License::get_instance()->is_activated();
	}

	/**
	 * Load CSS for our custom Elementor modal.
	 */
	public function enqueue_editor_scripts() {
		if ( $this->is_deep_integration_enabled() ) {
			wp_enqueue_script( 'elements-elementor-modal', ENVATO_ELEMENTS_URI . 'assets/js/elementor-modal.min.js', [ 'jquery' ], ENVATO_ELEMENTS_VER );
			wp_enqueue_style( 'elements-elementor-modal', ENVATO_ELEMENTS_URI . 'assets/css/elementor-modal.min.css', [], ENVATO_ELEMENTS_VER );
			Plugin::get_instance()->admin_page_assets_react();
		}
	}


	/**
	 *
	 */
	public function get_template_data() {
		if ( ! empty( $_POST['template_id'] ) && strlen( $_POST['template_id'] ) > 20 && ! empty( $_POST['source'] ) && 'remote' === $_POST['source'] ) {
			// we're likely importing one of our templates.
			// swap out the Elementor 'remote' source with our own one so the built in ajax call will call that.
			\Elementor\Plugin::$instance->templates_manager->register_source(
				'\Elementor\TemplateLibrary\Source_Elements_Remote', [
					'id' => 'remote',
				]
			);
		}
	}

	public function has_elementor_pro(){
		static $has_elementor_pro = null;
		if($has_elementor_pro === null) {
			$elementor_pro_is_missing = Required_Plugin::get_instance()->get_missing_plugins( [], 'elementor-pro' );
			$has_elementor_pro = count( $elementor_pro_is_missing ) ? false : true;
		}
		return $has_elementor_pro;
	}


}

