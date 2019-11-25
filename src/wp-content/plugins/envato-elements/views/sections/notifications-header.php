<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="envato-elements__notifications-wrapper">
	<div class="envato-elements__notifications envato-elements__chktoggle">

		<input type="checkbox" name="envato-elements__chktoggle-notifications" class="envato-elements__chktoggle-input" id="envato-elements__chktoggle-notifications" value="1">
		<label for="envato-elements__chktoggle-notifications" class="envato-elements__chktoggle-trigger js-envato-elements__notification-trigger envato-elements__notifications-trigger"
			<?php if ( count( $unseen_notifications ) > 0 ) { ?> data-unseen-notifications="<?php echo filter_var( json_encode( $unseen_notifications ), FILTER_SANITIZE_SPECIAL_CHARS ); ?>" <?php } ?>>
			Updates
			<?php if ( count( $unseen_notifications ) > 0 ) { ?>
				<span class="envato-elements__header-menu-label"><?php echo count( $unseen_notifications ); ?></span>
			<?php } ?>
		</label>

		<div class="envato-elements__notifications-list envato-elements__chktoggle-content">
			<div class="envato-elements__chktoggle-content-inner">
				<ul class="envato-elements__notifications_list">
					<?php foreach ( $notifications as $notification ) { ?>
						<li class="envato-elements__notifications_item">
							<div class="envato-elements__notifications_item__date">
								<?php echo esc_html( $notification['date'] ); ?>
							</div>
							<!-- <div class="envato-elements__notifications_item__categories">
								<?php echo esc_html( $notification['categories'] ); ?>
							</div> -->
							<div class="envato-elements__notifications_item__title">
								<?php echo esc_html( $notification['title'] ); ?>
							</div>
							<div class="envato-elements__notifications_item__content">
								<?php echo wp_kses_post( $notification['content'] ); ?>
							</div>
						</li>
					<?php } ?>
					<!--<li class="envato-elements__notifications_item envato-elements__notifications_item--cta">
						<a href="https://elements.envato.com/?utm_source=extensions&utm_medium=referral&utm_campaign=elements_extensions_updates" target="_blank" class="elements-cta">Check out Envato Elements</a>
					</li>-->
				</ul>
			</div>
		</div>

	</div>
</div>
