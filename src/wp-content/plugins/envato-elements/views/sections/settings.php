<?php

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div>

	<?php
	$elements_token = Options::get_instance()->get( License::SUBSCRIPTION_TOKEN_OPTION );
	echo '<pre>'.print_r($elements_token,true).'</pre>';

	if ( License::get_instance()->is_activated() ) { ?>

		<?php $subscription_status = License::get_instance()->subscription_status();
		switch ( $subscription_status ) {
			case License::SUBSCRIPTION_FREE:
				?>
				Thank you, you have a free Elements subscription account.
				<?php
				break;
			case License::SUBSCRIPTION_PAID:
				?>
				Thank you, you have a paid Elements subscription account.
				<?php
				break;
			case License::SUBSCRIPTION_INACTIVE:
			default:
				?>
				Elements Subscription Status is: <?php echo $subscription_status; ?>
				<?php
				break;
		}
		?>
	<form action="<?php echo esc_url( admin_url( 'admin.php?action=envato_elements_api_token' ) ); ?>" method="POST">
		<?php wp_nonce_field( 'envato_elements_api_token' ); ?>
		Enter New API Token: <input type="text" name="api_token" value=""> <input type="submit" name="go" value="Submit">
	</form>
		<p>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?action=envato_elements_deactivate' ), 'deactivate' ) ); ?>">Deactivate Website</a>
		</p>
	<?php } else {
		?>
		<p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . ENVATO_ELEMENTS_SLUG ) ); ?>">Please activate plugin</a>
		</p>
		<?php
	} ?>

</div>
