<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>


<div class="envato-elements__welcome">
	<div class="envato-elements__welcome-inner">
		<form action="<?php echo esc_url( admin_url( 'admin.php?action=envato_elements_registration' ) ); ?>" method="POST">
			<?php wp_nonce_field( 'envato_elements_signup' ); ?>
			<?php
			if ( ! empty( $_GET['registration'] ) ) {
				echo '<div class="envato-elements-notice envato-elements-notice--signup">';
				switch ( $_GET['registration'] ) {
					case 'reset':
						echo '<p>' . esc_html__( 'Successfully reset, please register again below.', 'envato-elements' ) . ' </p>';
						break;
					case 'success':
						echo '<p>' . esc_html__( 'Successfully registered', 'envato-elements' ) . ' </p>';
						break;
					case 'error':
						$error_message = get_transient( \Envato_Elements\License::ERROR_TRANSIENT );
						if ( $error_message ) {
							echo '<p>' . $error_message . ' </p>';
						} else {
							echo '<p>' . esc_html__( 'There was an error with the request, please try again.', 'envato-elements' ) . ' </p>';
						}
						break;
					case 'terms':
						echo '<p>' . esc_html__( 'Please agree to the Terms & Conditions in order to continue.', 'envato-elements' ) . ' </p>';
						break;
					case 'failure':
						echo '<p>' . esc_html__( 'Activation failed, please ensure a valid email address is entered.', 'envato-elements' ) . ' </p>';
						break;
				}
				echo '</div>';
			}
			?>
			<img src="<?php echo esc_url( ENVATO_ELEMENTS_URI . 'assets/images/welcome-2.svg' ); ?>" alt="Welcome"
				width="200"/>
			<p>Thanks for trying out the official</p>
			<h2>Envato Elements WordPress Plugin</h2>
			<p>Enter your email address &amp; accept our terms to continue</p>
			<div>
				<?php
				$current_user  = wp_get_current_user();
				$current_email = $current_user->user_email;
				if ( strpos( $current_email, '@flywheel.local' ) || strpos( $current_email, '@admin.com' ) || strpos( $current_email, '@test.com' ) || strpos( $current_email, '@example.com' ) || strpos( $current_email, '@localhost' ) ) {
					$current_email = '';
				}
				?>
				<input type="email" name="email_address" value="<?php echo esc_attr( $current_email ); ?>"
					data-cy="email_address" placeholder="Email Address">
			</div>
			<div class="envato-elements__welcome-checkboxes">
				<label>
					<input type="checkbox" name="condition_terms" data-cy="condition_terms" value="1" required>
					<span>
			I agree to the
			<a href="https://elements.envato.com/user-terms" target="_blank" rel="noreferrer noopener">Envato Elements Terms</a>
		  </span>
				</label>
				<label>
					<input type="checkbox" name="condition_emails" data-cy="condition_emails" value="1">
					<span>
						I’d like to opt-in to promo emails from Envato &amp; I understand my interactions with these emails will be recorded.
					</span>
				</label>
			</div>
			<div>
				<input type="submit" name="sign_up" id="sign_up" class="button-primary" data-cy="submit_button"
					value="<?php esc_attr_e( 'Continue', 'envato-elements' ); ?>"/>
			</div>
		</form>
	</div>
</div>
