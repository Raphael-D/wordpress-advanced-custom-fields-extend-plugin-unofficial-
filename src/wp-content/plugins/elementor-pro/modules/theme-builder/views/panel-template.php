<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<script type="text/template" id="tmpl-elementor-publish">
	<# if ( screens.length > 1 ) { #>
		<div id="elementor-publish__tabs">
			<# screens.forEach( function( screen ) { #>
				<div class="elementor-publish__tab" data-screen="{{ screen.name }}">
					<div class="elementor-publish__tab__image">
						<img src="{{ screen.image }}">
					</div>
					<div class="elementor-publish__tab__content">
						<div class="elementor-publish__tab__title">{{{ screen.title }}}</div>
						<div class="elementor-publish__tab__description">{{{ screen.description }}}</div>
					</div>
				</div>
			<# } ); #>
		</div>
	<# } #>
	<div id="elementor-publish__screen"></div>
</script>

<script type="text/template" id="tmpl-elementor-theme-builder-conditions-view">
	<div class="elementor-template-library-blank-icon">
		<img src="<?php echo ELEMENTOR_PRO_MODULES_URL; ?>theme-builder/assets/images/conditions-tab.svg">
	</div>
	<div class="elementor-template-library-blank-title">{{{ elementorPro.translate( 'conditions_title' ) }}}</div>
	<div class="elementor-template-library-blank-message">{{{ elementorPro.translate( 'conditions_description' ) }}}</div>
	<div id="elementor-theme-builder-conditions">
		<div id="elementor-theme-builder-conditions-controls"></div>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-theme-builder-conditions-repeater-row">
	<div class="elementor-theme-builder-conditions-repeater-row-controls"></div>
	<div class="elementor-repeater-row-tool elementor-repeater-tool-remove">
		<i class="eicon-close" aria-hidden="true"></i>
		<span class="elementor-screen-only"><?php esc_html_e( 'Remove this item', 'elementor-pro' ); ?></span>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-theme-builder-button-preview">
	<i class="fa fa-eye tooltip-target" aria-hidden="true"  data-tooltip="<?php esc_attr_e( 'Preview Changes', 'elementor-pro' ); ?>"></i>
	<span class="elementor-screen-only">
		<?php esc_attr_e( 'Preview Changes', 'elementor-pro' ); ?>
	</span>
	<div class="elementor-panel-footer-sub-menu-wrapper">
		<div class="elementor-panel-footer-sub-menu">
			<div id="elementor-panel-footer-theme-builder-button-preview-settings" class="elementor-panel-footer-sub-menu-item">
				<i class="fa fa-wrench" aria-hidden="true"></i>
				<span class="elementor-title"><?php esc_html_e( 'Settings', 'elementor-pro' ); ?></span>
			</div>
			<div id="elementor-panel-footer-theme-builder-button-open-preview" class="elementor-panel-footer-sub-menu-item">
				<i class="fa fa-external-link" aria-hidden="true"></i>
				<span class="elementor-title"><?php esc_html_e( 'Preview', 'elementor-pro' ); ?></span>
			</div>
		</div>
	</div>
</script>
