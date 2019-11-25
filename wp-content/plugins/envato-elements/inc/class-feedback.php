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
 * Feedback registration and management.
 *
 * @since 0.0.2
 */
class Feedback extends Base {

	/**
	 * Feedback constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'admin_action_envato_elements_feedback', [ $this, 'envato_elements_feedback' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_feedback_dialog_scripts' ] );
	}

	/**
	 * Enqueue feedback dialog scripts.
	 *
	 * Registers the feedback dialog scripts and enqueues them.
	 *
	 * @since 0.1.2
	 * @access public
	 */
	public function enqueue_feedback_dialog_scripts() {
		if ( ! in_array( get_current_screen()->id, [ 'plugins', 'plugins-network' ], true ) ) {
			return;
		}

		add_action( 'admin_footer', [ $this, 'print_deactivate_feedback_dialog' ] );

		Plugin::get_instance()->admin_page_assets();
	}


	/**
	 * Print deactivate feedback dialog.
	 *
	 * Display a dialog box to ask the user why he deactivated Elementor.
	 *
	 * Fired by `admin_footer` filter.
	 *
	 * @since 0.1.2
	 * @access public
	 */
	public function print_deactivate_feedback_dialog() {
		$deactivate_reasons = [
			'imported_all_templates_needed'  => [
				'title'             => __( 'I\'ve imported all of the templates I need for now', 'envato-elements' ),
				'input_placeholder' => '',
			],
			'couldnt_find_suitable_template' => [
				'title'             => __( 'I couldn\'t find any suitable templates', 'envato-elements' ),
				'input_placeholder' => __( 'Please let us know what kind of templates your looking for', 'envato-elements' ),
			],
			'didnt_like_templates'           => [
				'title'             => __( 'I didn\'t like the templates', 'envato-elements' ),
				'input_placeholder' => 'Could you please explain why?',
			],
			'couldnt_get_it_to_work'         => [
				'title'             => __( 'I couldn\'t get the plugin to work', 'envato-elements' ),
				'input_placeholder' => '',
			],
			'other'                          => [
				'title'             => __( 'Other', 'envato-elements' ),
				'input_placeholder' => __( 'Please share the reason', 'envato-elements' ),
			],
		];

		?>

		<div class="envato-elements__modal-holder"></div>

		<script id="tmpl-envato-elements__plugin-feedback" type="text/x-handlebars-template">
			<section class="envato-elements__modal envato-elements__modal--plugin-feedback">
				<div class="envato-elements__modal-inner">
					<div class="envato-elements__modal-inner-bg">
						<header class="envato-elements__modal-header">
							<h3>Quick Feedback</h3>
							<button class="envato-elements__modal-close"></button>
						</header>
						<section class="envato-elements__modal-content">
							<div class="envato-elements-notice">
								<h2>Please share why you are deactivating Envato Elements:</h2>
								<ul>
									<?php foreach ( $deactivate_reasons as $deactivate_reason => $deactivate_options ) { ?>
										<li>
											<input id="elements-deact-<?php echo esc_attr( $deactivate_reason ); ?>" type="radio" name="elements_deactivation_reason" value="<?php echo esc_attr( $deactivate_reason ); ?>"/>
											<label for="elements-deact-<?php echo esc_attr( $deactivate_reason ); ?>"><?php echo esc_html( $deactivate_options['title'] ); ?></label>
											<?php if ( ! empty( $deactivate_options['input_placeholder'] ) ) : ?>
												<div class="elements-deact-text">
													<input type="text" name="elements_deactivation_reason_<?php echo esc_attr( $deactivate_reason ); ?>" placeholder="<?php echo esc_attr( $deactivate_options['input_placeholder'] ); ?>"/>
												</div>
											<?php endif; ?>
										</li>
									<?php } ?>
								</ul>
							</div>
							<div class="envato-elements__disaable-buttons">
								<button class="envato-elements__disable-submit">Submit &amp; Deactivate</button>
								<a href="{{skip}}" class="envato-elements__disable-skip">Skip &amp; Deactivate</a>
							</div>
					</div>
				</div>
			</section>
		</script>
		<script>
      jQuery( function () {
        window.ElementsAdmin && window.ElementsAdmin.pluginPageLoaded();
      } );
		</script>
		<?php
	}


	/**
	 * Check if a given request has access to update a setting
	 *
	 * @param\ WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|bool
	 *
	 * @since 0.1.0
	 */
	public function rest_permission_check( $request ) {
		return true;
	}

	/**
	 * This registers all our WP REST API endpoints for the react front end
	 *
	 * @param $namespace
	 *
	 * @since 0.1.0
	 *
	 */
	public function init_rest_endpoints( $namespace ) {

		$endpoints = [
			'/feedback/deactivation'    => [
				\WP_REST_Server::CREATABLE => 'rest_feedback_deactivation',
			],
			'/feedback/elements_connect_skip' => [
				\WP_REST_Server::CREATABLE => 'rest_feedback_elements_connect_skip',
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
	 * Record a plugin disabled status.
	 *
	 * @param \WP_REST_Request $request The ID numbers of notifications read.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 * @since 0.1.0
	 */
	public function rest_feedback_deactivation( $request ) {
		$result = API::get_instance()->api_call(
			'v1/statistics/feedback', [
				'feedback'    => 'plugin_deactivate',
				'answer'      => $request->get_param( 'answer' ),
				'answer_text' => $request->get_param( 'answer_text' ),
			]
		);

		return new \WP_REST_Response( $result, 200 );
	}

	/**
	 * Record a plugin disabled status.
	 *
	 * @param \WP_REST_Request $request The ID numbers of notifications read.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 * @since 0.1.0
	 */
	public function rest_feedback_elements_connect_skip( $request ) {
		$result = API::get_instance()->api_call(
			'v1/statistics/feedback', [
				'feedback' => 'elements_connect_skip',
				'answer'   => $request->get_param( 'answer' ),
			]
		);

		return new \WP_REST_Response( $result, 200 );
	}


	/**
	 * Send page view track (only if they agree to terms with valid license)
	 *
	 * @param $page
	 * @param string $data
	 */
	public function page_view( $page, $data = '' ) {
		if ( License::get_instance()->is_activated() ) {
			API::get_instance()->api_call(
				'v1/statistics/page_view', [
					'page' => $page,
					'data' => $data,
				]
			);
		}
	}


	public function envato_elements_feedback() {
		check_admin_referer( 'feedback' );

		switch ( $_GET['answer'] ) {
			case 'yes':
				update_option( 'envato_elements_feedback_photos', 'yes' );
				API::get_instance()->api_call(
					'v1/statistics/feedback', [
						'feedback' => 'photos',
						'answer'   => 'yes',
					]
				);
				break;
			case 'no':
				update_option( 'envato_elements_feedback_photos', 'no' );
				API::get_instance()->api_call(
					'v1/statistics/feedback', [
						'feedback' => 'photos',
						'answer'   => 'no',
					]
				);
				break;
		}
		wp_safe_redirect( admin_url( 'admin.php?page=envato-elements&category=photos' ) );

	}

	public function generate_form( $type = '' ) {

		$url_yes = wp_nonce_url(
			add_query_arg(
				[
					'action'   => 'envato_elements_feedback',
					'feedback' => 'photos',
					'answer'   => 'yes',
				], admin_url( 'admin.php' )
			), 'feedback'
		);

		$url_no = wp_nonce_url(
			add_query_arg(
				[
					'action'   => 'envato_elements_feedback',
					'feedback' => 'photos',
					'answer'   => 'no',
				], admin_url( 'admin.php' )
			), 'feedback'
		);

		ob_start();
		?>
		<section class="envato-elements__modal">
			<div class="envato-elements__modal-inner">
				<div class="envato-elements__modal-inner-bg">
					<?php if ( get_option( 'envato_elements_feedback_photos' ) ) { ?>
						<h3 class="envato-elements__feedback-question">Thank you for your feedback.</h3>
						<h3 class="envato-elements__feedback-question">We will let you know when Photos become available.</h3>
					<?php } else { ?>
						<h3
							class="envato-elements__feedback-question">Would having access to 500,000 Envato Elements Photos from within
							WordPress be useful to you?</h3>
						<div class="envato-elements__feedback-answers-wrap">
							<a href="<?php echo esc_url( $url_yes ); ?>"><span><img
										src="<?php echo ENVATO_ELEMENTS_URI . 'assets/images/thumbs-up.svg'; ?>"> </span><br>Yes</a>
							<a href="<?php echo esc_url( $url_no ); ?>"><span><img
										src="<?php echo ENVATO_ELEMENTS_URI . 'assets/images/thumbs-down.svg'; ?>"></span><br>No</a>
						</div>
					<?php } ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

}
