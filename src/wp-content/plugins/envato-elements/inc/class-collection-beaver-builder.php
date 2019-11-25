<?php
/**
 * Envato Elements: Beaver Builder
 *
 * BB template display/import.
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
class Collection_Beaver_Builder extends Collection {

	public function __construct() {
		parent::__construct();
		$this->category = 'beaver-builder';
	}


	public function install_remote_template( $collection_id, $template_id ) {

		$local_collection       = new CPT_Kits();
		$local_collection_cache = $local_collection->get_pending_import( $collection_id, $template_id );
		if ( $local_collection_cache && $local_collection_cache->post_parent ) {
			// we have a parent associated post, proceed with import.
			$import_result = $local_collection->perform_import(
				$local_collection_cache->ID, [
					'post_meta' => [
						'_fl_meta_custom' => 'envato',
					],
				]
			);

			$this->process_scheduled_page_inserts( $local_collection_cache->ID );

			delete_post_meta( $local_collection_cache->ID, 'elements_import_lock' );

			return $import_result;
		}

		return false;

	}

	public function edit_post_link( $post_id ) {
		return add_query_arg( 'fl_builder', '', get_permalink( $post_id ) );
	}


}
