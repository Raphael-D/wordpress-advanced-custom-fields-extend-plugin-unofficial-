<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script id="tmpl-envato-elements__magic" type="text/x-handlebars-template">
	<div class="envato-elements__wrapper envato-elements__wrapper--fixed">
		<div class="envato-elements__header">
			<div class="envato-elements__header-logo">
				<a href="<?php echo esc_url( Envato_Elements\Plugin::get_instance()->get_url() ); ?>" target="_blank"><img src="<?php echo esc_url( ENVATO_ELEMENTS_URI . 'assets/images/envato-elements-template-kits.svg' ); ?>" alt="Envato Elements"></a>
			</div>
			<nav class="envato-elements__header-menu">
				<ul class="envato-elements__header-menuwrap">
					<?php
					if ( Envato_Elements\License::get_instance()->is_activated() ) {
						?>
						<li class="envato-elements__header-menuitem">
							<a href="#"
								class="envato-elements__header-menulink envato-elements--action envato-elements__header-menulink--current"
								data-nav-top="top"
								data-nav-type="main-category"
								data-category-slug="elementor-blocks"
							>Blocks</a>
						</li>
						<li class="envato-elements__header-menuitem">
							<a href="#"
								class="envato-elements__header-menulink envato-elements--action"
								data-nav-top="top"
								data-nav-type="main-category"
								data-category-slug="elementor"
							>Template Kits</a>
						</li>
						<?php
					}
					?>
				</ul>
			</nav>
			<?php
			//\Envato_Elements\Notifications::get_instance()->header_nav();
			//			<div class="envato-elements__importer-wrapper">
			//			</div>
			?>
			<div class="envato-elements__modal-closewrap">
				<button class="envato-elements__modal-closebtn js-modal-close">
					<i class="eicon-close" aria-hidden="true" title="Close"></i>
				</button>
			</div>
		</div>
		<div class="envato-elements__content">
			<div class="envato-elements__modal-holder"></div>
			<div class="envato-elements__content-dynamic js-envato-elements-content">
			</div>
		</div>
	</div>
</script>