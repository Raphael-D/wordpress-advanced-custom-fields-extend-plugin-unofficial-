<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>


<script id="tmpl-envato-elements__error-modal" type="text/x-handlebars-template">
	<section class="envato-elements__modal envato-elements__modal--error">
		<div class="envato-elements__modal-inner">
			<div class="envato-elements__modal-inner-bg">
				<header class="envato-elements__modal-header">
					<h3>Error: {{title}} </h3>
					<button class="envato-elements__modal-close"></button>
				</header>
				<section class="envato-elements__modal-content">
					<div class="envato-elements-notice envato-elements-notice--error">
						<p>{{{message}}} </p>
					</div>
		  {{#if reactivate}}
					<p>Please try to <a href="" onclick="window.location.reload();return false;">refresh the page</a> or <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?action=envato_elements_deactivate' ), 'deactivate' ) ); ?>">re-activate</a> the plugin.</p>
		  {{/if}}
			</div>
		</div>
	</section>
</script>
