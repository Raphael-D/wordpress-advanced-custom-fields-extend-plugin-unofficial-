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
 * API controller for updates, stats and content.
 *
 * @since 0.0.2
 */
class API extends Base {

	/**
	 * This is the list of API endpoints for the plugin.
	 * This plugin communicates with this endpoint to get plugin content and to import content.
	 *
	 * We include a second API endpoint because some hosts have out dated TLS libraries and do not support the
	 * ECDHE TLS ciphers from the first endpoint. If connects to the first API endpoint fail then it the plugin will
	 * fall back to our backup API endpoint.
	 *
	 * @var array
	 *
	 * @since 0.0.9
	 */
	private $api_endpoint = [
		'https://wp.envatoextensions.com/wp-json/elements-content/',
		'https://bob2cnnvzm-flywheel.netdna-ssl.com/wp-json/elements-content/',
	];

	public function __construct() {
		if ( defined( 'ENVATO_ELEMENTS_API_ENDPOINT' ) ) {
			$this->api_endpoint = ENVATO_ELEMENTS_API_ENDPOINT;
		}
		parent::__construct();
	}

	/**
	 *
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	public function api_call( $endpoint, $body_args = [], $force = false ) {

		// We add the endpoints we can happily cache here.
		$cache_endpoints = apply_filters(
			'envato_elements_api_cache_endpoints', [
				'v1/collections',
				'v1/version',
				'v1/collection',
				'v1/notifications',
				'v2/blocks',
			]
		);

		$body_args = apply_filters( 'envato_elements_api_body_args', $body_args );

		if ( defined( 'ENVATO_ELEMENTS_DEV' ) && ENVATO_ELEMENTS_DEV ) {
			// Disable the local cache:
			$force = true;
			// Set a dev API flag:
			$body_args['envato_elements_dev'] = true;
		}

		if ( defined( 'ENVATO_ELEMENTS_LOADING_FIX' ) ) {
			$force = true;
		}

		$cache_key = 'envato_elements_' . md5( serialize( [ $this->api_endpoint, $endpoint, $body_args ] ) );
		$data      = false;

		if ( in_array( $endpoint, $cache_endpoints, true ) && ! $force ) {
			// check if cache exists.
			$data = get_transient( $cache_key );
			if ( $data ) {
				return $data;
			}
		}

		$api_retry = 0;
		while ( $api_retry <= 3 ) {
			if ( is_array( $this->api_endpoint ) ) {
				$url = $this->api_endpoint[ $api_retry % count( $this->api_endpoint ) ] . $endpoint;
			} else {
				$url = $this->api_endpoint . $endpoint;
			}

			$response = wp_remote_post(
				$url, [
					'sslverify' => false, // Some hosts require this unfortunately :(
					'user-agent' => 'Mozilla/5.0 (Envato Elements ' . ENVATO_ELEMENTS_VER . ';) ' . home_url(),
					'timeout' => 10,
					'body' => $body_args,
				]
			);
			if ( ! is_wp_error( $response ) ) {
				break;
			}
			$api_retry ++;
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

//		echo wp_remote_retrieve_body( $response );exit;
		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		$data = $this->filter_api_response( $data, $endpoint );

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, $data && ! empty( $data['message'] ) ? $data : __( 'HTTP Error', 'envato-elements' ) );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', '', [
				__( 'An error occurred, please try again', 'envato-elements' ),
				var_export( wp_remote_retrieve_body( $response ), true )
			] );
		}

		if ( in_array( $endpoint, $cache_endpoints, true ) && ! $force ) {
			set_transient( $cache_key, $data, 60 * 10 );
		}

		return $data;
	}

	/**
	 *
	 * @param $data
	 * @param $endpoint
	 *
	 * @return mixed
	 */
	public function filter_api_response( $data, $endpoint ) {

		Notices::get_instance()->sniff_api_response_for_messages( $data, $endpoint );

		return $data;
	}


}
