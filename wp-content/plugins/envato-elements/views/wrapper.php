<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="envato-elements__wrapper envato-elements__wrapper--fixed">
	<div class="envato-elements__header">
		<?php echo $this->header; ?>
	</div>

	<div class="envato-elements__content">
		<?php Envato_Elements\Notices::get_instance()->print_global_notices();
		//echo $this->render_template( 'notices/advertisement.php' );
		if ( ! \Envato_Elements\Notices::get_instance()->ui_disabled ) {
			?>
			<div class="envato-elements__modal-holder"></div>
			<div class="envato-elements__content-dynamic js-envato-elements-content">
				<?php echo $this->content; ?>
			</div>
		<?php } ?>
	</div>

	<div class="envato-elements__support">
		<p>
			<strong>Feedback &amp; Support: </strong> If you have any questions or feedback for the team please send an email to
			<a href="mailto:extensions@envato.com">extensions@envato.com</a>
			|
			<a href="https://elements.envato.com/user-terms" target="_blank" rel="noreferrer noopener">Terms &amp; Conditions</a>
			|
			<a href="https://envato.com/privacy" target="_blank" rel="noreferrer noopener">Privacy Policy</a>
		</p>
	</div>


</div>

<script>
  jQuery( function () {
    window.ElementsAdmin && window.ElementsAdmin.pageLoaded();
  } );
</script>
