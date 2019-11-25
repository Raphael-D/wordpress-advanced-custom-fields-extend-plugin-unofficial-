<?php
/**
 * Envato Elements:
 *
 * Little helper to figure out what links to generate.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Little helper to figure out what links to generate.
 *
 * @since 0.1.0
 */
class Linker extends Base {

	public function get_edit_link( $post_id, $context = false ) {
		// We try to figure out what type of page this is.
		$elementor_test = get_post_meta( $post_id, '_elementor_data', true );
		if ( $elementor_test && class_exists( '\Elementor\Plugin' ) ) {
			return \Elementor\Plugin::$instance->documents->get( $post_id )->get_edit_url();
		}

		return get_permalink( $post_id );
	}

}
