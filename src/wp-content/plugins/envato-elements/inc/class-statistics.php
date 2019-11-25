<?php
/**
 * Envato Elements:
 *
 * This package handles collecting statistics about users.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Statistics registration and management.
 *
 * @since 0.0.2
 */
class Statistics extends Base {

	/**
	 * Statistics constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'envato_elements_api_body_args', [ $this, 'filter_api_body_args' ] );
	}

	/**
	 * Filter API body arguments.
	 *
	 * @param array $body_args API args.
	 *
	 * @return array
	 */
	public function filter_api_body_args( $body_args ) {

		$body_args['statistics']            = [];
		$body_args['statistics']['version'] = ENVATO_ELEMENTS_VER;

		if ( License::get_instance()->is_activated() ) {
			// The user has agreed to terms and conditions.
			$body_args['statistics']['site_url'] = home_url();
		}

		return $body_args;
	}


}
