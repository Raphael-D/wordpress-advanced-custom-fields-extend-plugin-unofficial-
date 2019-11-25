<?php
/**
 * Envato Elements: Notifications drop down
 *
 * Handles the display of the notifications drop down from the top menu
 *
 * @package Envato/Envato_Elements
 * @since 0.1.0
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Notification registration and management.
 *
 * @since 0.1.0
 */
class Notifications extends Base {

	const NOTIFICATION_TRANSIENT = 'envato-elements-notifications';

	/**
	 * Ask the Elements API for recent notifications.
	 * Store the notification cache for half a day.
	 *
	 * @since 0.1.0
	 * @return bool|array
	 */
	public function get_notifications() {
		$notification_data = get_transient( self::NOTIFICATION_TRANSIENT );
		if ( ! $notification_data || ! is_array( $notification_data ) || ( defined( 'ENVATO_ELEMENTS_DEV' ) && ENVATO_ELEMENTS_DEV ) ) {
			$notification_data = [];
			$api_response      = API::get_instance()->api_call( 'v1/notifications' );
			if ( $api_response && ! is_wp_error( $api_response ) && ! empty( $api_response['data'] ) && is_array( $api_response['data'] ) ) {
				$notification_messages = $api_response['data'];
				foreach ( $notification_messages as $message_id => $notification_message ) {
					// Work out if any are global messages.
					if ( ! empty( $notification_message['force'] ) && ! empty( $notification_message['content'] ) ) {
						Notices::get_instance()->inject_global_message( $notification_message['content'], $notification_message['title'] );
					} else {
						$notification_data[] = $notification_message;
					}
				}
				set_transient( self::NOTIFICATION_TRANSIENT, $notification_data, 43200 );
			}
		}

		return $notification_data;
	}

	/**
	 * This method is called whenever the plugin is installed or upgraded.
	 *
	 * @since 0.1.0
	 */
	public function activation() {
		delete_transient( self::NOTIFICATION_TRANSIENT );
	}

	/**
	 * Run the daily cron job to obtain recent notifications from the API endpoint.
	 *
	 * @since 0.1.0
	 */
	public function run_cron() {
		$this->get_notifications();
	}

	/**
	 * Outputs the top right header nav markup.
	 *
	 * @since 0.1.0
	 */
	public function header_nav() {

		if ( License::get_instance()->is_activated() ) { // Confirm agreement to T&C
			$has_viewed_notifications = Options::get_instance()->get( 'viewed_notifications' );
			if ( ! is_array( $has_viewed_notifications ) ) {
				$has_viewed_notifications = [];
			}
			$current_notifications = $this->get_notifications();
			if ( $current_notifications ) {
				// figure out if anything has not been seen before.
				$unseen_notifications = [];
				foreach ( $current_notifications as $notification ) {
					if ( ! empty( $notification['id'] ) && ! isset( $has_viewed_notifications[ $notification['id'] ] ) ) {
						$unseen_notifications[] = $notification['id'];
					}
				}
				echo $this->render_template(
					'sections/notifications-header.php', [
						'unseen_notifications' => $unseen_notifications,
						'notifications'        => $current_notifications,
					]
				);
			}
		}
	}

	/**
	 * Check if a given request has access to update a setting
	 *
	 * @param\ WP_REST_Request $request Full data about the request.
	 *
	 * @since 0.1.0
	 * @return \WP_Error|bool
	 */
	public function rest_permission_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * This registers all our WP REST API endpoints for the react front end
	 *
	 * @since 0.1.0
	 *
	 * @param $namespace
	 */
	public function init_rest_endpoints( $namespace ) {

		$endpoints = [
			'/notifications/read' => [
				\WP_REST_Server::CREATABLE => 'rest_notifications_read',
			],
		];

		foreach ( $endpoints as $endpoint => $details ) {
			foreach ( $details as $method => $callback ) {
				register_rest_route(
					$namespace, $endpoint, [
						[
							'methods'             => $method,
							'callback'            => [ $this, $callback ],
							'permission_callback' => [ $this, 'rest_permission_check' ],
							'args'                => [],
						],
					]
				);
			}
		}

	}

	/**
	 * Record a notification as seen.
	 *
	 * @param \WP_REST_Request $request The ID numbers of notifications read.
	 *
	 * @since 0.1.0
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function rest_notifications_read( $request ) {
		$result                   = [
			'thanks' => false,
		];
		$has_viewed_notifications = Options::get_instance()->get( 'viewed_notifications' );
		if ( ! is_array( $has_viewed_notifications ) ) {
			$has_viewed_notifications = [];
		}
		if ( $request->get_param( 'ids' ) && is_array( $request->get_param( 'ids' ) ) ) {
			$viewed_ids = apply_filters( 'int_val', $request->get_param( 'ids' ) );
			foreach ( $viewed_ids as $viewed_id ) {
				if ( $viewed_id > 0 ) {
					$has_viewed_notifications[ $viewed_id ] = true;
				}
			}
			$result['thanks'] = true;
		}
		Options::get_instance()->set( 'viewed_notifications', $has_viewed_notifications );

		return new \WP_REST_Response( $result, 200 );

	}
}
