<?php
/**
 * Envato Elements:
 *
 * This manages connection to the Envato Elements API with tokens.
 *
 * @package Envato/Envato_Elements
 * @since 0.1.7
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * API controller for Envato Elements API calls
 *
 * @since 0.1.7
 */
class Elements_API extends Base {

	/**
	 * This is the list of API endpoints for the plugin.
	 *
	 * @var string
	 *
	 * @since 0.1.7
	 */

	private $token = '';
	private $api_hostname = '';

	public function __construct() {
		parent::__construct();
		$this->api_hostname = defined( 'ELEMENTS_API_HOSTNAME' ) ? ELEMENTS_API_HOSTNAME : 'https://api.extensions.envato.com';
	}

	public function set_token( $token = false ) {
		if ( ! $token ) {
			$elements_token = Options::get_instance()->get( License::SUBSCRIPTION_TOKEN_OPTION );
			if ( $elements_token && ! empty( $elements_token['token'] ) ) {
				$token = $elements_token['token'];
			}
		}
		$this->token = $token;
	}

	public function get_token() {
		return $this->token;
	}

	public function get_extension_id() {
		return License::get_instance()->get_license_code();
	}

	private function encode_url_parameter( $parameter ) {
		$parameter = html_entity_decode( $parameter, ENT_QUOTES | ENT_XML1, 'UTF-8' );
		$parameter = str_replace( '#', '', $parameter );

		return urlencode( $parameter );
	}

	public function get_token_url() {
		$extension_description = trim( Options::get_instance()->get( 'project_name', get_bloginfo( 'name' ) ) );
		if ( strlen( $extension_description ) > 0 ) {
			$extension_description .= ' (' . get_home_url() . ')';
		} else {
			$extension_description = get_home_url();
		}
		$extension_description = substr( $extension_description, 0, 254 );

		return $this->api_hostname . "/extensions/begin_activation?extension_id=" . $this->get_extension_id() . "&extension_type=envato-wordpress&extension_description=" . $this->encode_url_parameter( $extension_description );
	}

	/**
	 *
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	public function api_call( $endpoint, $method = 'GET', $body_args = [] ) {

		if ( ! $this->token ) {
			$this->set_token();
		}
		$http_args = [
			'sslverify'  => false, // Some hosts require this unfortunately :(
			'user-agent' => 'Mozilla/5.0 (Envato Elements ' . ENVATO_ELEMENTS_VER . ';) ' . home_url(),
			'timeout'    => 15,
			'headers'    => [ 'Extensions-Extension-Id' => $this->get_extension_id() ]
		];
		if ( $this->token ) {
			$http_args['headers']['Extensions-Token'] = $this->token;
		}


		if ( $method == 'GET' ) {
			$response = wp_remote_get( $this->api_hostname . $endpoint, $http_args );
		} else {
			$http_args['headers']['Content-Type'] = 'application/json';
			$http_args['body']                    = json_encode( $body_args );
			$http_args['data_format']             = 'body';
			$response                             = wp_remote_post( $this->api_hostname . $endpoint, $http_args );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		$response_code = wp_remote_retrieve_response_code( $response );

//		$response_code = 401;$data = [ 'error' => [ 'code'=>'token_expired', 'message'=>'errormsg' ] ];
//		$response_code = 403;$data = [ 'error' => [ 'code'=>'download_forbidden', 'message'=>'errormsg' ] ];
//		$response_code = 404;$data = [ 'error' => [ 'code'=>'item_not_found', 'message'=>'errormsg' ] ];
//		$response_code = 503;$data = 'Unavailable';

		if ( 200 !== (int) $response_code && 201 !== (int) $response_code ) {
			// format our error response data into something easier to parse
			return new \WP_Error( $response_code, $data && ! empty( $data['message'] ) ? $data['message'] : __( 'HTTP Error', 'envato-elements' ), $data && ! empty( $data['error'] ) ? $data['error'] : $data );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', '', [
				__( 'An error occurred, please try again', 'envato-elements' ),
				var_export( wp_remote_retrieve_body( $response ), true )
			] );
		}

		return $data;
	}


	public function extract_errors( $data ) {
		if ( is_wp_error( $data ) && is_array( $data->errors ) && is_array( $data->error_data ) ) {
			$error_status            = key( $data->errors );
			$error_message           = ! empty( $data->errors[ $error_status ] ) ? current( $data->errors[ $error_status ] ) : false;
			$error_data              = ! empty( $data->error_data[ $error_status ] ) ? $data->error_data[ $error_status ] : false;
			$result                  = [];
			$result['error_status']  = $error_status;
			$result['error_message'] = $error_message;
			$result['error_data']    = $error_data;
			$result['error_code']    = ! empty( $error_data['code'] ) ? $error_data['code'] : false;

			return $result;
		}
	}
}
