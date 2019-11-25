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
 * License registration and management.
 *
 * @since 0.0.2
 */
class License extends Base {

	const PAGE_SLUG = 'envato-elements-activation';

	const ERROR_TRANSIENT = 'envato-elements-license-message-error';

	const SUBSCRIPTION_TOKEN_OPTION = 'elements_token';
	const SUBSCRIPTION_API_CACHE = 3600; // Cache results locally this long.
	const SUBSCRIPTION_INACTIVE = 'inactive';
	const SUBSCRIPTION_FREE = 'free';
	const SUBSCRIPTION_PAID = 'paid';

	/**
	 * License constructor.
	 */
	public function __construct() {
		add_action( 'admin_action_envato_elements_registration', [ $this, 'envato_elements_registration' ] );
		add_action( 'admin_action_envato_elements_deactivate', [ $this, 'envato_elements_deactivate' ] );

		// Add the license key to all API requests.
		add_filter( 'envato_elements_api_body_args', [ $this, 'filter_api_body_args' ] );

	}

	/**
	 * Called when the user visits our menu item without registering.
	 * Displays the welcome screen.
	 */
	public function admin_menu_open() {
		$this->content = $this->render_template( 'license/welcome.php' );
		$this->header  = $this->render_template( 'header.php' );
		echo $this->render_template( 'wrapper.php' );  // WPCS: XSS ok.
	}

	/**
	 * Gets the current license code.
	 *
	 * @return string Code
	 */
	public function get_license_code() {
		$license_code = Options::get_instance()->get( 'license_code' );
		if ( ! $license_code ) {
			// Transition legacy license codes over to new format..
			$codes   = get_option( 'envato_elements_license_code' );
			$user_id = get_current_user_id();
			if ( $codes && $user_id && ! empty( $codes[ $user_id ] ) ) {
				Options::get_instance()->set( 'license_code', $codes[ $user_id ] );
				$license_code = $codes[ $user_id ];
				unset( $codes[ $user_id ] );
				if ( ! count( $codes ) ) {
					delete_option( 'envato_elements_license_code' );
				} else {
					update_option( 'envato_elements_license_code', $codes );
				}
			}
		}

		return $license_code;
	}

	/**
	 * Sets current license code.
	 *
	 * @param string $license_code Code to save.
	 */
	public function set_license_code( $license_code ) {
		Options::get_instance()->set( 'license_code', $license_code );
	}

	/**
	 * Works out if user has registered.
	 *
	 * @return bool
	 */
	public function is_activated() {
		return ! ! $this->get_license_code();
	}

	/**
	 * Finds the Elements subscription status from the local cache or the API call.
	 *
	 * @return int
	 */
	public function subscription_status( $retry = false ) {
		$elements_token = Options::get_instance()->get( self::SUBSCRIPTION_TOKEN_OPTION );

		if ( ! $elements_token || ! is_array( $elements_token ) || empty( $elements_token['time'] ) || empty( $elements_token['status'] ) || empty( $elements_token['token'] ) ) {
			// No token entered.
			return self::SUBSCRIPTION_INACTIVE;
		} else if ( ! $retry && ( $elements_token['time'] > time() || $elements_token['time'] < time() - self::SUBSCRIPTION_API_CACHE ) ) {
			// Local token cache expired, fetch a new one.
			$this->verify_elements_token();

			return $this->subscription_status( true );

		} else {
			// Local token cache is valid.
			return $elements_token['status'];
		}
	}


	/**
	 * Calls the Elements API token to verify the users status.
	 *
	 * @return bool|array
	 */
	public function verify_elements_token( $token = false, $clear_on_error = false ) {

		if ( $token ) {
			// Set temporary token to override anything stored locally.
			Elements_API::get_instance()->set_token( $token );
		}
		$result = Elements_API::get_instance()->api_call( '/extensions/user_info' );
		if ( ! is_wp_error( $result ) && is_array( $result ) && ! empty( $result['subscription_status'] ) ) {
			// We've got a successful result from the API, cache it locally.
			$token_storage = [
				'valid'  => true,
				'token'  => Elements_API::get_instance()->get_token(),
				'time'   => time(),
				'status' => $result['subscription_status'],
			];

			API::get_instance()->api_call( 'v2/elements/connected/' . $result['subscription_status'], [] );

			Options::get_instance()->set( self::SUBSCRIPTION_TOKEN_OPTION, $token_storage );

			return $token_storage;
		} else {
			if ( $clear_on_error ) {
				Options::get_instance()->set( self::SUBSCRIPTION_TOKEN_OPTION, false );
			} else {
				$token_storage = [
					'valid'  => false,
					'token'  => Elements_API::get_instance()->get_token(),
					'time'   => time(),
					'status' => 'error',
				];
				Options::get_instance()->set( self::SUBSCRIPTION_TOKEN_OPTION, $token_storage );
			}

			if ( is_wp_error( $result ) && is_array( $result->errors ) && is_array( $result->error_data ) ) {
				$error_status  = key( $result->errors );
				$error_message = current( $result->errors[ $error_status ] );
				$error_data    = $result->error_data[ $error_status ];

				return [
					'valid'         => false,
					'error_status'  => $error_status,
					'error_message' => $error_message,
					'error_data'    => $error_data,
					'error_code'    => ! empty( $error_data['code'] ) ? $error_data['code'] : false,
				];
			}
		}

		return false;
	}

	/**
	 * Filter API body arguments on every outbound request to Envato server.
	 * Allows us to add the users API key to all API requests so we can verify clients.
	 *
	 * @param array $body_args API args.
	 *
	 * @return array
	 */
	public function filter_api_body_args( $body_args ) {
		$license_code = $this->get_license_code();

		if ( ! empty( $license_code ) ) {
			$body_args['license_code'] = $license_code;
		}

		return $body_args;
	}

	/**
	 * Handles the form registration from the Welcome screen.
	 */
	public function envato_elements_registration() {
		check_admin_referer( 'envato_elements_signup' );

		$email = ! empty( $_POST['email_address'] ) ? filter_var( wp_unslash( $_POST['email_address'] ), FILTER_SANITIZE_EMAIL ) : false; // WPCS: input var ok.
		if ( empty( $_POST['condition_terms'] ) ) { // WPCS: input var ok.
			wp_safe_redirect( add_query_arg( 'registration', 'terms', Plugin::get_instance()->get_url() ) );
		} elseif ( $email ) {
			// Activate email against this install.
			$activation_result = API::get_instance()->api_call(
				'v1/activate', [
					'email'            => $email,
					'condition_terms'  => ! empty( $_POST['condition_terms'] ) ? 1 : 0, // WPCS: input var ok.
					'condition_emails' => ! empty( $_POST['condition_emails'] ) ? 1 : 0, // WPCS: input var ok.
				]
			);
			if ( $activation_result && ! is_wp_error( $activation_result ) && ! empty( $activation_result['license_code'] ) ) {
				$this->set_license_code( $activation_result['license_code'] );
				wp_safe_redirect( add_query_arg( 'registration', 'success', Plugin::get_instance()->get_url() ) );
			} else {
				$license_message_error = esc_html__( 'There was an error with the request, please try again.', 'envato-elements' );
				if ( is_wp_error( $activation_result ) ) {
					$error_message = $activation_result->get_error_message( $activation_result->get_error_code() );
					if ( is_array( $error_message ) && isset( $error_message['message'] ) ) {
						$error_message = $error_message['message'];
					}
					if ( ! $error_message ) {
						$api_error_codes = Elements_API::get_instance()->extract_errors( $activation_result );
						if ( ! empty( $api_error_codes['error_message']['message'] ) ) {
							$error_message = $api_error_codes['error_message']['message'];
						}
					}
					/* Translators: The HTTP error message */
					$license_message_error = sprintf( esc_html__( 'There was an error with the request: %s If this error continues please contact the hosting provider or %s for assistance.', 'envato-elements' ), '<br /><strong>' . esc_html( $error_message ) . '</strong><br />', '<a href="mailto:extensions@envato.com">Extensions Support</a>' );

				}
				set_transient( self::ERROR_TRANSIENT, $license_message_error, 300 );
				wp_safe_redirect( add_query_arg( 'registration', 'error', Plugin::get_instance()->get_url() ) );
			}
		} else {
			wp_safe_redirect( add_query_arg( 'registration', 'failure', Plugin::get_instance()->get_url() ) );
		}

	}

	/**
	 * Deactivate their local license.
	 */
	public function envato_elements_deactivate() {
		// check_admin_referer( 'deactivate' ); // todo: uncomment this when we go live, it's currently good for easy testing. /wp-admin/admin.php?action=envato_elements_deactivate.
		$this->set_license_code( '' );

		delete_transient( self::ERROR_TRANSIENT );
		delete_transient( 'envato-elements-notices' );
		delete_transient( 'envato-elements-notifications' );

		delete_option( 'envato_elements_tracker_notice' );
		delete_option( 'envato_elements_version' );
		delete_option( 'envato_elements_install_time' );
		delete_option( '_envato_elements_installed_time' );
		delete_option( 'envato_elements_license_code' );
		delete_option( 'envato_elements_options' );
		delete_option( 'envato_elements_photo_imports' );
		wp_clear_scheduled_hook( 'envato_elements_cron' );
		wp_safe_redirect( add_query_arg( 'registration', 'reset', Plugin::get_instance()->get_url() ) );

	}

	/**
	 * When the plugin upgrades we clear any transient notices.
	 *
	 * @since 0.1.2
	 */
	public function activation() {
		delete_transient( self::ERROR_TRANSIENT );
	}

}
