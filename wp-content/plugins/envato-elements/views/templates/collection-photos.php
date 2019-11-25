<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script id="tmpl-envato-elements__collections--photos" type="text/x-handlebars-template">

	<div class="envato-elements__collections envato-elements__collections--{{categorySlug}}">
		<header class="envato-elements__photos-header">
			<input type="text" name="search" value=""> <input type="button" name="search" value="seach"> [search stuff here]
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


<script id="tmpl-envato-elements__photo-wrap" type="text/x-handlebars-template">
	<div class="envato-elements__photo-wrap {{#if photoImported}} imported{{/if}}">
		<a href="{{photoUrl}}"
			data-nav-type="photo"
			data-category-slug="{{categorySlug}}"
			data-template-id="{{humane_id}}"
			class="envato-elements__collection-photo-thumb envato-elements--action"
			data-src="{{imageThumb.src}}"
			style="width: {{imageThumb.gridWidth}}px; height: {{imageThumb.gridHeight}}px; "
		></a>
		<div class="envato-elements__photo-title">{{title}}</div>
	</div>
</script>


<script id="tmpl-envato-elements__photo-preview" type="text/x-handlebars-template">
	<div class="envato-elements__photo-preview">
		<section class="envato-elements__photo-detail-thumbnail">
			<div class="envato-elements__template-features">
				{{#if photoImported}}
				<span class="envato-elements__template-feature envato-elements__template-feature--installed">Imported</span>
				{{/if}}
			</div>
			{{#if imageLarge.src}}
			<div class="envato-elements__photo-preview-placeholder">
				<img
					src="{{imageLarge.src}}"
					width="{{imageLarge.width}}"
					height="{{imageLarge.height}}"
					class="envato-elements__photo-preview-large-img"
					onload="this.parentNode.className = this.parentNode.className + ' --loaded';"
				/>
				<div class="envato-elements__photo-preview-placeholder-img-wrap">
					<img
						src="{{imageThumb.src}}"
						width="{{largeThumb.width}}"
						height="{{largeThumb.height}}"
						class="envato-elements__photo-preview-placeholder-img"
					/>
				</div>
			</div>
			{{else}}
			<div class="envato-elements__photo-preview-placeholder"></div>
			{{/if}}
		</section>
		<section class="envato-elements__photo-detail-actions">
			<button type="button"
				data-nav-type="collection-close"
				data-category-slug="{{categorySlug}}"
				class="envato-elements__photo-preview-close envato-elements--action"></button>

			<h3 class="envato-elements__collection-preview-title">{{title}}</h3>
			<hr>

			<div class="envato-elements__collection-template-option--help-text">
				<p>Import this photo to make it available in your WordPress media library for future use.
				</p>
			</div>

			<button
				data-nav-type="import-template"
				data-category-slug="{{categorySlug}}"
				data-collection-id="{{collectionId}}"
				data-template-id="{{humane_id}}"
				class="button envato-elements-import-button envato-elements--action button-primary"
			>Import Photo<span></span></button>


		</section>
	</div>
</script>
