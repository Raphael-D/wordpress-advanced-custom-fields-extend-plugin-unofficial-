<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script id="tmpl-envato-elements__collections--elementor-blocks" type="text/x-handlebars-template">

	<div class="envato-elements__collections envato-elements__collections--{{categorySlug}}">
		{{#if feedback}}
		{{{feedback}}}
		{{/if}}
		<header class="envato-elements__collections-header">
			<h2 class="envato-elements__collections-header__title">{{pageTitle}}</h2>
		</header>
		{{#if meta}}
		{{#if meta.filters}}
		<div class="envato-elements__collections-header__subnav">
			{{#if meta.filters.type}}
			<h4 class="envato-elements__collections-header__subnav-title">
				Browse by Type:
			</h4>
			<select class="js-envato-elements__select2 envato-elements--action-dropdown" data-filter="type">
				<option value="null" data-nav-type="category"
					data-category-slug="{{categorySlug}}"
					data-search='{ "pg": 1, "filters": null }'>All
				</option>
				{{#each meta.filters.type}}
				<option value="{{@key}}" data-nav-type="category"
					data-category-slug="{{../categorySlug}}"
					data-search='{ "pg": 1, "filters": { "type": "{{@key}}" } }' {{#if_eq @key ..
				/searchParams.filters.type }} selected{{/if_eq}}>{{name}}</option>
				{{/each}}
			</select>
			{{/if}}
		</div>
		{{/if}}
		{{/if}}
		<section class="envato-elements__collections-content">
		</section>
		{{#if show_coming_soon}}
		<div class="envato-elements__coming-soon">
			We'll be adding more Blocks soon, watch this space!
		</div>
		{{/if}}
		{{#if pagination}}
		<div class="envato-elements__pagination">
			{{#each pagination}}
			<div class="envato-elements__pagination-item{{#if pageCurrent}} envato-elements__pagination-item--current{{/if}}">
				{{#if pageCurrent}}
				{{pageLabel}}
				{{else}}
				<a href="#"
					data-nav-type="category"
					data-category-slug="{{../categorySlug}}"
					data-search='{{page_link ../searchParams pageLabel}}'
					class="envato-elements--action"
				>{{pageLabel}}</a>
				{{/if}}
			</div>
			{{/each}}
		</div>
		{{/if}}
	</div>

</script>


<script id="tmpl-envato-elements__block-wrap" type="text/x-handlebars-template">
	<div class="envato-elements__block-wrap">
		<div class="envato-elements__block-type">
			<h3>{{{title}}}</h3>
		</div>
		<div class="envato-elements__block-content">
		</div>
	</div>
</script>

<script id="tmpl-envato-elements__collection-template-cell--elementor-blocks" type="text/x-handlebars-template">
	<div class="envato-elements__collection-template-cell envato-elements__collection-blocks {{#if templateInstalled}} imported{{/if}} {{#if templateInserted.length}} imported{{/if}}">
		<div class="envato-elements__collection-template">
			<div class="envato-elements__template-features">
				{{#if templateInstalled}}
				<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
				{{else}}
				{{#if templateInserted.length}}
				<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
				{{/if}}
				{{/if}}
				{{#each templateFeatures}}
				<span class="envato-elements__template-feature envato-elements__template-feature--{{@key}}">{{small}}</span>
				{{/each}}
			</div>
			<a href="{{templateUrl}}"
				data-nav-type="template"
				data-category-slug="{{categorySlug}}"
				data-collection-id="{{collectionId}}"
				data-template-id="{{templateId}}"
				data-thumb-height="{{previewThumbHeight}}"
				class="envato-elements__collection-block-thumb envato-elements--action"
				data-src="{{previewThumb2x}}"
				style="padding-bottom: {{previewThumbAspect}};"
			></a>
			<div class="envato-elements__block-title">{{templateName}}</div>
		</div>
	</div>
</script>


<script id="tmpl-envato-elements__block-preview" type="text/x-handlebars-template">
	<div class="envato-elements__block-preview">
		<section class="envato-elements__block-detail-thumbnail">
			<div class="envato-elements__template-features">
				{{#if templateInstalled}}
				<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
				{{else}}
				{{#if templateInserted.length}}
				<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
				{{/if}}
				{{/if}}
				{{#each templateFeatures}}
				<span class="envato-elements__template-feature envato-elements__template-feature--{{@key}}">{{large}}</span>
				{{/each}}
			</div>
			{{#if largeThumb.src}}
			<div class="envato-elements__block-preview-placeholder">
				<img
					src="{{largeThumb.src}}"
					width="{{largeThumb.width}}"
					height="{{largeThumb.height}}"
					class="envato-elements__block-preview-large-img"
					onload="this.parentNode.className = this.parentNode.className + ' --loaded';"
				/>
				<div class="envato-elements__block-preview-placeholder-img-wrap">
					<img
						src="{{previewThumb2x}}"
						width="{{largeThumb.width}}"
						height="{{largeThumb.height}}"
						class="envato-elements__block-preview-placeholder-img"
					/>
				</div>
			</div>
			{{else}}
			<div class="envato-elements__block-preview-placeholder"></div>
			{{/if}}
		</section>
		<section class="envato-elements__block-detail-actions">
			<button type="button"
				data-nav-type="collection-close"
				data-category-slug="{{categorySlug}}"
				class="envato-elements__block-preview-close envato-elements--action"></button>

			<p class="envato-elements__collection-preview-label">Block:</p>
			<h3 class="envato-elements__collection-preview-title">{{templateName}}</h3>
			<hr>
			{{#if templateError}}
			<div class="envato-elements__collection-template-options">
				<div class="envato-elements-notice envato-elements-notice--warning">

					{{#each templateMissingPlugins}}
					<div class="envato-elements__collection-template-option envato-elements__collection-template-option--edit-template">

						{{#if_eq slug "elementor-pro"}}
						<img src="<?php echo esc_url( ENVATO_ELEMENTS_URI . 'assets/images/elementor-pro.png' ); ?>" width="200"/>
						<p>This Template requires Elementor Pro{{#if min_version}} version {{min_version}} or above{{/if}}. Before you can import the template you'll need to buy, install and activate
							<strong> Elementor Pro</strong>.</p>
						<a href="{{{url}}}" class="button button-primary" target="_blank">{{text}}</a>
						{{else}}
						<p>To use this template please ensure all required plugins are installed and active. </p>
						<a href="{{{url}}}" class="button button-primary">{{text}}</a>
						{{/if_eq}}
					</div>
					{{/each}}
				</div>

			</div>

			{{else}}


			<div class="envato-elements__collection-template-options">

				<div class="envato-elements__collection-template-option envato-elements__collection-template-option--edit-template">

					{{#if templateFeatures.elementor-pro}}
					<div class="envato-elements__collection-template-option--help-text">
						<p>This template includes features from</p>
						<img src="<?php echo esc_url( ENVATO_ELEMENTS_URI . 'assets/images/elementor-pro.png' ); ?>" width="200"/>
					</div>
					{{/if}}


					{{#if_eq importMode "insert"}}

					{{#if templateType.popup}}
					<div class="envato-elements__collection-template-option--help-text">
						<p>This is a popup template and cannot be imported directly to a page. Create a popup from the popups page.</p>
						<p>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=envato-elements&category=elementor-blocks' ) ); ?>&search=<?php echo htmlspecialchars( json_encode( [
								"pg"      => 1,
								"filters" => [ "type" => "popup" ]
							] ), ENT_QUOTES, 'UTF-8' ); ?>" class="button button-primary" target="_blank">Open Popup Templates</a></p>
					</div>
					{{else}}
					<button
						data-nav-type="direct-insert-template"
						data-category-slug="{{categorySlug}}"
						data-collection-id="{{collectionId}}"
						data-template-id="{{templateId}}"
						class="button envato-elements-import-button envato-elements--action button-primary"
					>Insert Block <span></span></button>
					{{/if}}
					{{else}}
					{{#if templateInstalled}}

					<div class="envato-elements__collection-template-option--help-text">
						<p>This template has been imported, it is available in your Elementor
							{{#if templateType.popup}}
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=popup&elementor_library_type=popup' ) ); ?>">Popups</a>
							{{else}}
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' ) ); ?>">Saved Templates</a>
							{{/if}}
							list for future use.
						</p>
					</div>
					<a href="{{templateInstalledURL}}" class="button button-primary" target="_blank">{{templateInstalledText}}</a>

					{{else}}

					<div class="envato-elements__collection-template-option--help-text">
						<p>Import this template to make it available in your Elementor
							{{#if templateType.popup}}
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=popup&elementor_library_type=popup' ) ); ?>">Popups</a>
							{{else}}
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' ) ); ?>">Saved Templates</a>
							{{/if}}
							list for future use.
						</p>
					</div>

					<button
						data-nav-type="import-template"
						data-category-slug="{{categorySlug}}"
						data-collection-id="{{collectionId}}"
						data-template-id="{{templateId}}"
						class="button envato-elements-import-button envato-elements--action button-primary"
					>{{templateImportText}} <span></span></button>

					{{/if}}
					{{/if_eq}}
				</div>

			</div>
			{{/if}}

		</section>
	</div>
</script>


