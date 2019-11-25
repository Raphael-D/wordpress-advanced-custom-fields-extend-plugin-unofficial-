<?php
/**
 * Envato Elements: Global Notices
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Notices registration and management.
 *
 * @since 0.0.2
 */
class Notices extends Base {

	const NOTICE_TRANSIENT = 'envato-elements-notices';

	/**
	 * If there is a global message then we lock the UI.
	 *
	 * @var bool
	 */
	public $ui_disabled = false;

	/**
	 * @var array
	 */
	private $messages = [];

	public function __construct() {
		$this->messages = get_transient( self::NOTICE_TRANSIENT );
		if ( ! $this->messages ) {
			$this->messages = [];
		}
		$this->messages = [];
		$this->ui_disabled = ! ! count( $this->messages );
		parent::__construct();
	}

	/**
	 *
	 * We look for any messages or notifications from our API response.
	 * These messages are stored in a global transient and then displayed to the user on next page load.
	 *
	 * @param $api_response array
	 * @param $api_endpoint string
	 */
	public function sniff_api_response_for_messages( $api_response, $api_endpoint ) {
		if ( $api_response && ! is_wp_error( $api_response ) && ! empty( $api_response['global_message'] ) ) {
			$this->inject_global_message( $api_response['global_message'] );
		}
	}

	/**
	 * @param $message
	 */
	public function inject_global_message( $message, $title = false ) {
		$this->messages[ md5( $message ) ] = $message;
		$this->ui_disabled                 = true;
		set_transient( self::NOTICE_TRANSIENT, $this->messages, 3600 );
	}

	/**
	 *
	 */
	public function print_global_notices() {
		if ( count( $this->messages ) ) {
			echo $this->render_template(
				'notices/global.php', [
					'messages' => $this->messages,
				]
			);
		}
	}

	/**
	 * When the plugin upgrades we clear any transient notices.
	 *
	 * @since 0.0.9
	 */
	public function activation() {
		delete_transient( self::NOTICE_TRANSIENT );
		$this->messages    = [];
		$this->ui_disabled = false;
	}

}
