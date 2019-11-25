<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script id="tmpl-envato-elements__importer" type="text/x-handlebars-template">

  <div class="envato-elements__importer envato-elements__chktoggle">

		<input type="checkbox" name="envato-elements__chktoggle-importer" class="envato-elements__chktoggle-input" id="envato-elements__chktoggle-importer" value="1">
		<div class="envato-elements__importer-status">

		</div>
	  {{#if imports}}
			<label for="envato-elements__chktoggle-importer" class="envato-elements__chktoggle-trigger envato-elements__importer-trigger">
				My Activity
			</label>

		  <div class="envato-elements__importer-list envato-elements__chktoggle-content">
			  <div class="envato-elements__chktoggle-content-inner">
				  <ul>
					  {{#each imports}}
					  <li>
						  {{#if imported}}
						  Imported:
						  {{else}}
						  Importing...
						  {{/if}}
						  <a href="#"
							  data-nav-type="template"
							  data-category-slug="{{categorySlug}}"
							  data-collection-id="{{collectionId}}"
							  data-template-id="{{templateId}}"
							  class="envato-elements--action"
						  >{{name}}</a>
					  </li>
					  {{/each}}
				  </ul>
			  </div>
		  </div>
	  {{/if}}

  </div>
</script>
