<?php
/**
 * Envato Elements: Elementor
 *
 * Elementor template display/import.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Collection registration and management.
 *
 * @since 0.0.2
 */
class Collection_Elementor extends Collection {

	public function __construct() {
		parent::__construct();
		$this->category = 'elementor';
		add_filter( 'elementor/utils/is_post_type_support', [ $this, 'elementor_post_type_support' ], 10, 3 );
	}

	public function elementor_post_type_support( $is_supported, $post_id, $post_type ) {
		if ( $post_id && CPT_Kits::get_instance()->cpt_slug === $post_type ) {
			return true;
		}

		return $is_supported;
	}

	public function trashed_post( $post_id = false ) {

	}

	public function install_remote_template( $collection_id, $template_id, $options = [] ) {

		$local_collection       = new CPT_Kits();
		$local_collection_cache = $local_collection->get_pending_import( $collection_id, $template_id );
		if ( $local_collection_cache && $local_collection_cache->post_parent ) {
			// we have a parent associated post, proceed with import.
			$import_settings = [
				'post_meta' => [
					'_elementor_template_type' => 'page',
					'_wp_page_template'        => 'elementor_canvas',
					'_elements_collection_id'  => $collection_id,
					'_elements_template_id'    => $template_id,
				],
			];
			// We import it twice. First into our local CPT collection (used only for Beaver really)
			$import_result = $local_collection->perform_import( $local_collection_cache->ID, $import_settings );

			// Check if the user has requested a Library import as well.
			$import_type = get_post_meta( $local_collection_cache->ID, 'import_type', true );
			if ( $import_type && 'direct' === $import_type && $import_result && ! is_wp_error( $import_result ) && ! empty( $import_result['post_id'] ) ) {

			} elseif ( $import_type && $import_result && ! is_wp_error( $import_result ) && ! empty( $import_result['post_id'] ) ) {


				$template = get_post_meta( $local_collection_cache->ID, 'template_data', true );
				if ( $template && ! empty( $template['import'] ) ) {
					// Sweet, we've got a copy of this in our Kit CPT, we'll just clone this over to Elementor
					$post = get_post( $import_result['post_id'] );
					if ( $post ) {

						$local_collection_post_parent = get_post( $local_collection_cache->post_parent );
						$args                         = array(
							'comment_status' => $post->comment_status,
							'ping_status'    => $post->ping_status,
							'post_author'    => get_current_user_id(),
							'post_content'   => $post->post_content,
							'post_excerpt'   => $post->post_excerpt,
							'post_name'      => 'elements-' . $post->post_name,
							'post_parent'    => 0,
							'post_password'  => '',
							'post_status'    => 'publish',
							'post_title'     => ! empty( $options['skip_title'] ) ? $template['import']['post_title'] : $local_collection_post_parent->post_title . ' > ' . $template['import']['post_title'],
							'post_type'      => 'elementor_library',
							'to_ping'        => $post->to_ping,
							'menu_order'     => $post->menu_order,
						);

						/*
						 * insert the post by wp_insert_post() function
						 */
						$new_post_id = wp_insert_post( $args );
						// remove default metadata (Elementor adds a default template type of 'page')
						$default_meta = get_post_meta( $new_post_id );
						if ( $default_meta ) {
							foreach ( $default_meta as $default_meta_key => $default_meta_val ) {
								delete_post_meta( $new_post_id, $default_meta_key );
							}
						}

						if ( $new_post_id && ! is_wp_error( $new_post_id ) ) {

							// Duplicate all post meta values
							$old_post_meta_keys = get_post_custom_keys( $import_result['post_id'] );
							if ( $old_post_meta_keys && is_array( $old_post_meta_keys ) && ! is_wp_error( $old_post_meta_keys ) ) {
								foreach ( $old_post_meta_keys as $meta_key ) {
									$meta_values = get_post_custom_values( $meta_key, $import_result['post_id'] );
									foreach ( $meta_values as $meta_value ) {
										$meta_value = maybe_unserialize( $meta_value );
										add_post_meta( $new_post_id, $meta_key, wp_slash( $meta_value ) );
									}
								}
							}

							update_post_meta( $new_post_id, '_source_kit_id', $import_result['post_id'] ); // Required to figure out imported state.
							wp_set_object_terms( $new_post_id, ! empty( $options['elementor_library_type'] ) ? $options['elementor_library_type'] : 'page', 'elementor_library_type' );
							$importer = new Import();
							$importer->elementor_post( $new_post_id );

							// We have to give this imported post a special type of meta
							$elementor_type = get_post_meta( $new_post_id, '_elementor_template_type', true );
							if ( ! $elementor_type || $elementor_type == 'post' ) {
								$elementor_type = 'page';
							}
							if ( $elementor_type ) {
								update_post_meta( $new_post_id, '_elementor_template_type', $elementor_type );
								wp_set_object_terms( $new_post_id, $elementor_type, 'elementor_library_type' );
							}

							$import_result['source_post_id'] = $import_result['post_id'];
							$import_result['post_id']        = $new_post_id;

						}
					}
				}
			}

			// Process any scheduled page inserts too
			$this->process_scheduled_page_inserts( $local_collection_cache->ID );

			delete_post_meta( $local_collection_cache->ID, 'elements_import_lock' );

			return $import_result;
		}

		return false;

	}

	/**
	 * @param $collection
	 *
	 * @return mixed
	 */
	public function filter_installed_status( $collection, $search = [] ) {

		if ( ! empty( $search['elementor'] ) && $search['elementor'] === 'free' ) {
			// User only wants to see free templates. Remove here.
			foreach ( $collection['templates'] as $id => $template ) {
				if ( ! empty( $template['templateFeatures'] ) && isset( $template['templateFeatures']['elementor-pro'] ) ) {
					unset( $collection['templates'][ $id ] );
				}
			}
			$collection['templates'] = array_values( $collection['templates'] );
		}

		$imported_templates = CPT_Kits::get_instance()->get_imported_templates();
		if ( ! empty( $collection['templates'] ) && class_exists( '\Elementor\Plugin' ) ) {
			foreach ( $collection['templates'] as $id => $template ) {
				foreach ( $imported_templates as $imported_template ) {
					if ( $imported_template['categorySlug'] === $this->category && $imported_template['collectionId'] === $collection['collectionId'] && $imported_template['templateId'] === $template['templateId'] ) {
						// we find the my library entry instead of our custom CPT ID.
						if ( $imported_template['imported'] ) {
							// Todo: store global query of all library `_source_kit_id` items so we don't query on each one.
							$cc_args               = [
								'posts_per_page' => - 1,
								'post_type'      => 'elementor_library',
								'meta_key'       => '_source_kit_id',
								'meta_value'     => $imported_template['ID'],
							];
							$has_elementor_library = false;
							$cc_query              = new \WP_Query( $cc_args );
							if ( $cc_query->have_posts() ) {
								$posts = $cc_query->get_posts();
								$post  = current( $posts );
								if ( $post && $post->ID ) {
									$imported_template['ID'] = $post->ID;
									$has_elementor_library   = true;
								}
							}

							if ( $has_elementor_library ) {
								$collection['templates'][ $id ]['itemImported']    = true;
								$collection['templates'][ $id ]['itemImportedUrl'] = $this->edit_post_link( $imported_template['ID'] );
//								$collection['templates'][ $id ]['templateInstalleText'] = Category::get_instance()->get_current( $this->category )->edit_button;
							}
						}
						// We also return the "Inserted Template" details so our template can choose to display this information.
						if ( ! empty( $imported_template['inserted'] ) ) {
							$collection['templates'][ $id ]['templateInserted'] = $imported_template['inserted'];
						}
					}
				}
			}
		}

		return $collection;
	}

	public function edit_post_link( $post_id ) {
		return Linker::get_instance()->get_edit_link( $post_id );
	}

	/**
	 * @param $local_template_id
	 *
	 * @return int
	 */
	public function process_scheduled_page_inserts( $local_template_id ) {

		$created_page_id = 0;

		$cpt_kits       = new CPT_Kits();
		$local_template = get_post( $local_template_id );
		if ( $local_template && $local_template->ID && $local_template->post_type === $cpt_kits->cpt_slug && 'publish' === $local_template->post_status ) {
			// get the elementor template contents first:
			$source_document = \Elementor\Plugin::$instance->documents->get( $local_template->ID );
			$source_elements = $source_document->get_elements_raw_data();

			// Find out what destinations we have to inject this data into.
			$insert_history = get_post_meta( $local_template_id, 'insert_history', true );
			if ( ! is_array( $insert_history ) || ! $insert_history ) {
				$insert_history = [];
			}
			foreach ( $insert_history as $key => $val ) {
				if ( ! $val['completed'] ) {

					if ( ! empty( $val['destination_post_id'] ) ) {

						if ( 'create-page' === $val['insert_type'] ) {
							update_post_meta( $val['destination_post_id'], '_wp_page_template', 'elementor_header_footer' );
						}

						$destination_post     = get_post( $val['destination_post_id'] );
						$destination_document = \Elementor\Plugin::$instance->documents->get( $val['destination_post_id'] );
						$data                 = $destination_document->get_data();
						if ( ! isset( $data['settings'] ) ) {
							$data['settings'] = [];
						}
						if ( empty( $data['settings']['post_status'] ) ) {
							$data['settings']['post_status'] = $destination_post->post_status;
						}
						$destination_elements = $destination_document->get_elements_raw_data();
						if ( ! $destination_document->is_built_with_elementor() ) {
							$destination_elements = \Elementor\Plugin::$instance->db->_get_new_editor_from_wp_editor( $val['destination_post_id'] );
							update_post_meta( $val['destination_post_id'], '_elementor_edit_mode', true );
						}
						foreach ( $source_elements as $source_element ) {
							$destination_elements[] = $source_element;
						}
						if ( $destination_elements ) {
							$data['elements'] = $destination_elements;
							$destination_document->save( $data );
							$insert_history[ $key ]['completed'] = true;
							$created_page_id                     = $val['destination_post_id'];
						}
					}
				}
			}
			update_post_meta( $local_template_id, 'insert_history', $insert_history );
		}

		return $created_page_id;
	}

}
