<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>


<div class="envato-elements-notice envato-elements-notice--global">
	<?php if ( $messages && is_array( $messages ) ) { ?>
	<ul>
			<?php
			foreach ( $messages as $message ) {
				?>
		<li><?php echo wp_kses_post( $message ); ?></li>
				<?php
			}
			?>
	</ul>
	<?php } ?>
</div>
