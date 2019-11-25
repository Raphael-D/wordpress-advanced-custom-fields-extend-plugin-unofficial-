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
 * Collection registration and management.
 *
 * @since 0.0.2
 */
class CPT_Kits extends CPT {

	/**
	 * Core custom post name for these templates.
	 *
	 * @var string
	 */
	public $cpt_name = 'Imported Kit';

	/**
	 * Core custom post name for these templates.
	 *
	 * @var string
	 */
	public $cpt_slug = 'envato_kits';
	private $_collection_id = false;
	private $imported_templates = null;

	public function __construct() {
		parent::__construct();

		add_filter( 'wpseo_sitemap_exclude_post_type', [ $this, 'wpseo_sitemap_exclude_post_type' ], 10, 2 );
	}

	/**
	 * We need to manually exclude this post type from Yoast because it doesn't behave nicely.
	 *
	 * @param $exclude
	 * @param $post_type
	 *
	 * @return bool
	 *
	 * @since 0.0.9
	 */
	public function wpseo_sitemap_exclude_post_type( $exclude, $post_type ) {
		if ( $post_type === $this->cpt_slug ) {
			return true;
		}

		return $exclude;
	}

	public function seed_local_cache( $template_data ) {
		$this->_collection_id = false;
		if ( $template_data && ! empty( $template_data['data'] ) && ! empty( $template_data['data']['collection_id'] ) ) {
			$existing_cache = get_posts(
				[
					'post_type'   => $this->cpt_slug,
					'post_status' => 'publish',
					'post_parent' => 0,
					'numberposts' => - 1,
					'meta_query'  => [
						[
							'key'   => 'collection_id',
							'value' => $template_data['data']['collection_id'],
						],
					],
				]
			);
			if ( ! $existing_cache ) {
				$this->_collection_id = wp_insert_post(
					[
						'post_type'   => $this->cpt_slug,
						'post_title'  => $template_data['data']['name'],
						'post_status' => 'publish',
					]
				);
				if ( $this->_collection_id ) {
					add_post_meta( $this->_collection_id, 'collection_id', $template_data['data']['collection_id'] );
				}
			} else {
				$collection = current( $existing_cache );
				if ( $collection ) {
					$this->_collection_id = $collection->ID;
				}
			}

			if ( $this->_collection_id ) {

				// we want to import the CSS into the customizer, but only if it doesn't exist already:
				if ( ! empty( $template_data['data']['options']['custom_css'] ) ) {
					$theme_css       = '';
					$append_css      = false;
					$custom_css_post = wp_get_custom_css_post();
					$css_separator   = 'Envato Elements CSS: ' . esc_html( $template_data['data']['name'] ) . ' (' . $template_data['data']['collection_id'] . ')';
					if ( $custom_css_post && $custom_css_post->ID ) {
						$theme_css = $custom_css_post->post_content;
						if ( false === strpos( $theme_css, $css_separator ) ) {
							$append_css = true;
						}
					} else {
						$append_css = true;
					}
					if ( $append_css ) {
						$theme_css .= "\n\n/** Start $css_separator **/\n\n" . $template_data['data']['options']['custom_css'] . "\n\n/** End $css_separator **/\n\n";
						wp_update_custom_css_post( $theme_css );
					}
				}

				update_post_meta( $this->_collection_id, 'page_builder', $template_data['data']['builder'] );
				update_post_meta( $this->_collection_id, 'template_data', wp_slash( $template_data ) );
			}
		}

		return $this->_collection_id;
	}

	public function get_pending_import( $collection_id, $template_id ) {
		$existing_cache = get_posts(
			[
				'post_type'   => $this->cpt_slug,
				'post_status' => 'draft',
				'numberposts' => - 1,
				'meta_query'  => [
					[
						'key'   => 'template_id',
						'value' => $template_id,
					],
				],
			]
		);
		if ( $existing_cache ) {
			// Find existing cache that matches collection ID parent.
			foreach ( $existing_cache as $cache ) {
				if ( $cache->post_parent ) {
					$parent_collection_id = get_post_meta( $cache->post_parent, 'collection_id', true );
					if ( $parent_collection_id && $parent_collection_id == $collection_id ) {
						return $cache;
					}
				}
			}
		}

		return false;
	}

	public function get_local_template( $collection_id, $template_id ) {
		$existing_cache = get_posts(
			[
				'post_type'   => $this->cpt_slug,
				'post_status' => 'any',
				'numberposts' => - 1,
				'meta_query'  => [
					[
						'key'   => 'template_id',
						'value' => $template_id,
					],
				],
			]
		);
		if ( $existing_cache ) {
			// Find existing cache that matches collection ID parent.
			foreach ( $existing_cache as $cache ) {
				if ( $cache->post_parent ) {
					$parent_collection_id = get_post_meta( $cache->post_parent, 'collection_id', true );
					if ( $parent_collection_id && $parent_collection_id == $collection_id ) {
						return $cache;
					}
				}
			}
		}

		return false;
	}

	public function schedule_insert_template( $collection_id, $template_id, $insert_type, $options ) {
		$existing_template = $this->get_local_template( $collection_id, $template_id );
		if ( $existing_template && $existing_template->ID ) {

			$insert_history = get_post_meta( $existing_template->ID, 'insert_history', true );
			if ( ! is_array( $insert_history ) || ! $insert_history ) {
				$insert_history = [];
			}
			$destination_post_id = 0;
			switch ( $insert_type ) {
				case 'create-page':
					$page_name = ! empty( $options['page_name'] ) ? $options['page_name'] : $existing_template->post_title;
					$id        = wp_insert_post(
						[
							'post_type'    => 'page',
							'post_title'   => $page_name,
							'post_status'  => 'draft',
							'post_content' => '',
						]
					);
					if ( $id && ! is_wp_error( $id ) ) {
						$destination_post_id = $id;
						add_post_meta( $id, 'source_template_id', $existing_template->ID );
					}
					break;
				case 'existing-page':
					$destination_post_id = ! empty( $options['page_id'] ) ? (int) $options['page_id'] : 0;
					break;
			}

			if ( $destination_post_id ) {
				$check_post = get_post( $destination_post_id );
				if ( $check_post && $check_post->ID ) {
					$insert_history[] = [
						'completed'           => false,
						'destination_post_id' => $check_post->ID,
						'insert_type'         => $insert_type,
						'options'             => $options,
					];
					update_post_meta( $existing_template->ID, 'insert_history', $insert_history );

					return $existing_template->ID;
				}
			}
		}

		return false;
	}

	public function get_imported_templates() {

		if ( ! is_null( $this->imported_templates ) ) {
			return $this->imported_templates;
		}
		$this->imported_templates = [];

		foreach (
			get_posts(
				[
					'post_type'   => $this->cpt_slug,
					'post_status' => 'draft,publish',
					'numberposts' => - 1,
				]
			) as $post
		) {

			$template_data = get_post_meta( $post->ID, 'template_data', true );
			if ( $template_data && $post->post_parent ) {
				$collection_id         = get_post_meta( $post->post_parent, 'collection_id', true );
				$page_builder_category = get_post_meta( $post->post_parent, 'page_builder', true );
				if ( $collection_id && $page_builder_category ) {

					$insert_history = get_post_meta( $post->ID, 'insert_history', true );
					if ( ! is_array( $insert_history ) || ! $insert_history ) {
						$insert_history = [];
					}
					$currently_inserting = false;
					foreach ( $insert_history as $key => $val ) {
						if ( empty( $val['destination_post_id'] ) ) {
							unset( $insert_history[ $key ] );
						} else {
							$inserted_post = get_post( $val['destination_post_id'] );
							if ( ! $inserted_post || 'trash' === $inserted_post->post_status ) {
								unset( $insert_history[ $key ] );
								// todo: save this better, update this on post save maybe?
							} else {
								if ( ! $val['completed'] ) {
									$currently_inserting = true;
								}
								$insert_history[ $key ]['pageName'] = $inserted_post->post_title;
								$insert_history[ $key ]['pageUrl']  = Linker::get_instance()->get_edit_link( $inserted_post->ID, 'edit' );
							}
						}
					}

					$this->imported_templates[] = [
						'ID'           => $post->ID,
						'name'         => $post->post_title,
						'templateId'   => $template_data['template_id'],
						'collectionId' => $collection_id,
						'categorySlug' => $page_builder_category,
						'imported'     => 'publish' === $post->post_status,
						'inserting'    => $currently_inserting,
						'inserted'     => array_values( $insert_history ),
					];
				}
			}
		}

		return $this->imported_templates;
	}

	public function schedule_local_install( $template, $import_type = false ) {

		$template_id = false;
		// See if it already exists..
		if ( $this->_collection_id && $template && ! empty( $template['template_id'] ) ) {
			$existing_cache = get_posts(
				[
					'post_type'   => $this->cpt_slug,
					'post_parent' => $this->_collection_id,
					'post_status' => 'draft,publish',
					'numberposts' => - 1,
					'meta_query'  => [
						[
							'key'   => 'template_id',
							'value' => $template['template_id'],
						],
					],
				]
			);
			if ( ! $existing_cache ) {
				$template_id = wp_insert_post(
					[
						'post_type'   => $this->cpt_slug,
						'post_parent' => $this->_collection_id,
						'post_title'  => $template['name'],
						'post_status' => 'draft',
					]
				);
				if ( $template_id ) {
					add_post_meta( $template_id, 'template_id', $template['template_id'] );
				}
			} else {
				$local_template = current( $existing_cache );
				if ( $local_template ) {
					$template_id = $local_template->ID;
				}
			}

			if ( $template_id ) {
				update_post_meta( $template_id, 'template_data', wp_slash( $template ) );
				update_post_meta( $template_id, 'import_type', $import_type );
				// change the post status to draft so our template is reimported again
				wp_update_post(
					[
						'ID'          => $template_id,
						'post_status' => 'draft',
					]
				);
			}
		}

		return $template_id;

	}

	public function perform_import( $local_cache_post_id, $import_settings ) {
		$importer = Import::get_instance();
		$template = get_post_meta( $local_cache_post_id, 'template_data', true );
		if ( $template && ! empty( $template['import'] ) ) {

			// Astra compatibility: Make sure post_content is empty.
			$template['import']['post_content'] = '';

			$template['import']['Update_Existing_ID'] = $local_cache_post_id;
			$import_settings['post_type']             = $this->cpt_slug;
			$post                                     = get_post( $local_cache_post_id );
			$import_settings['transient_namespace']   = get_post_meta( $post->post_parent, 'collection_id', true );

			return $importer->import_data( $template['import'], $import_settings );
		}

	}

}
