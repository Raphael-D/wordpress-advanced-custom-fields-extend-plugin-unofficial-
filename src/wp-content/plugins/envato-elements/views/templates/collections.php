<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script id="tmpl-envato-elements__collections" type="text/x-handlebars-template">

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
			{{#if meta.filters.industry}}
			<h4 class="envato-elements__collections-header__subnav-title">
				Browse by Industry:
			</h4>
			<a href="#"
				data-nav-type="category"
				data-category-slug="{{categorySlug}}"
				data-search='{ "pg": 1, "filters": null }'
				class="envato-elements--action envato-elements__collections-header__subnav-link {{#if searchParams.filters}} {{else}}envato-elements__collections-header__subnav-link--current{{/if}}"
			>All<span class="envato-elements__collections-header__subnav-link-count">({{all_results}})</span></a>
			{{#each meta.filters.industry}}
			<a href="#"
				data-nav-type="category"
				data-category-slug="{{../categorySlug}}"
				data-search='{ "pg": 1, "filters": { "industry": "{{@key}}" } }'
				class="envato-elements--action envato-elements__collections-header__subnav-link  {{#if_eq @key ../searchParams.filters.industry }}envato-elements__collections-header__subnav-link--current{{/if_eq}}"
			>{{name}} <span class="envato-elements__collections-header__subnav-link-count">({{count}})</span></a>
			{{/each}}
			{{/if}}
			<div class="envato-elements__chktoggle envato-elements__collections-header__subnav-more">
				<input type="checkbox" name="envato-elements__chktoggle-navmore" class="envato-elements__chktoggle-input" id="envato-elements__chktoggle-navmore" value="1">
				<label for="envato-elements__chktoggle-navmore" class="envato-elements__chktoggle-trigger">
					More
				</label>
				<div class="envato-elements__collections-header__subnav-more-item envato-elements__chktoggle-content">
					<div class="envato-elements__chktoggle-content-inner">
					</div>
				</div>
			</div>
		</div>
		{{/if}}
		{{/if}}
		<section class="envato-elements__collections-content">
		</section>
		{{#if show_coming_soon}}
		<div class="envato-elements__coming-soon">
			We'll be adding more Template Kits soon, watch this space!
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

<script id="tmpl-envato-elements__collections-single" type="text/x-handlebars-template">

	<article class="envato-elements__collections-single">
		<section class="envato-elements__collections-single-detail">
		</section>
		<section class="envato-elements__collections-single-summary">
			<header>
				<h3 class="envato-elements__collections-single-title{{#if features.new}} envato-elements__collections-single-title--new{{/if}}">{{collectionName}}</h3>
				<span class="envato-elements__collections-single-count">{{templates.length}} templates in this {{contentTypeName}}</span>
			</header>
			<section class="envato-elements__collections-scroller">
				<div class="envato-elements__collections-templates">

				</div>
			</section>
		</section>
	</article>

</script>
