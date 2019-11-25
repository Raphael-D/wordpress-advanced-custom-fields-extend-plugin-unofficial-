import $ from 'jquery';
import { api } from './api';
import { template } from './template';
import { collectionCache } from '../objects/collection';
import { config } from './config';
import { error } from './error';
import { templateCache } from '../objects/template';
import { collectionsCache } from '../objects/collections';

class Importer {
  constructor() {
    this.$holder = null;
    this.$importer = null;
    this.pendingImports = {};
    this.pendingCreates = {};
    this.importMode = 'import';
  }

  pageLoaded = () => {

    // Throw it up in the header nav menu area.
    this.$holder = $( '.envato-elements__importer-wrapper' );

    // This is the data we display in the UI. Number of current imports etc..
    this.importData = {
      imports: []
    };
    this.loadImportHistory();
  };

  setMode = (mode) => {
    this.importMode = mode;
  };

  navigationChange = ( admin, query, action ) => {
    if ( query && query.templateId && query.collectionId && query.navType && 'import-template' === query.navType ) {
      this.startBackgroundImport( query.categorySlug, query.collectionId, query.templateId );
    } else if ( query && query.templateId && query.collectionId && query.navType && 'direct-insert-template' === query.navType ) {
      this.startBackgroundDirectInsert( query.categorySlug, query.collectionId, query.templateId );
    } else if ( query && query.templateId && query.collectionId && query.navType && 'insert-template-create-page' === query.navType ) {
      this.startBackgroundInsertCreate( query.categorySlug, query.collectionId, query.templateId );
    }
  };

  startBackgroundInsertCreate = ( categorySlug, collectionId, templateId ) => {

    const $input = $( '.envato-elements__create-page-name[data-template-id="' + templateId + '"]' );
    const pageName = $input.val();
    if ( !pageName.length ) {
      error.displayError( 'Create Page', 'Please enter a valid Page Name and try again ', false );
      return;
    }

    if ( typeof this.pendingCreates[ templateId ] !== 'undefined' ) {
      return;
    }
    this.pendingCreates[ templateId ] = true;

    $( '.envato-elements-template-status[data-template-id="' + templateId + '"]' )
      .removeClass( 'envato-elements-template-status--imported' )
      .addClass( 'envato-elements-template-status--importing' );

    const $butt = $( '.envato-elements-insert-button[data-template-id="' + templateId + '"]' );
    $butt
      .html( 'Creating <span></span>' )
      .addClass( 'envato-elements-insert-button--inserting' );
    $butt
      .parents( '.envato-elements__collection-template-option' )
      .addClass( '..envato-elements__collection-template-option--inserting' );

    this.setImportStatus( 'Creating Page:', pageName );

    api.post( 'create/' + categorySlug + '/process', {
      collectionId: collectionId,
      templateId: templateId,
      insertType: 'create-page',
      pageName: pageName,
    } )
      .then(
        ( json ) => {
          this.refreshAfterImportOrInsert( collectionId, templateId, json, () => {
            this.setImportStatus( 'Page Created:', json.page_name, json.page_url );
            $butt
              .html( 'Page Created <span></span>' )
              .removeClass( 'envato-elements-insert-button--inserting' )
              .addClass( 'envato-elements-insert-button--inserted' );
          } );
        },
        ( err ) => {
          // todo, display error.
        }
      )
      .finally( () => {
        delete this.pendingCreates[ templateId ];
      } );
  };

  startBackgroundImport = ( categorySlug, collectionId, templateId ) => {
    // Check this template doesn't already exist in the pending list.
    if ( typeof this.pendingImports[ templateId ] !== 'undefined' ) {
      return;
    }
    this.pendingImports[ templateId ] = true;

    $( '.envato-elements-import-button[data-template-id="' + templateId + '"]' )
      .html( 'Importing <span></span>' )
      .removeClass( 'envato-elements-import-button--imported' )
      .addClass( 'envato-elements-import-button--importing' );

    $( '.envato-elements-template-status[data-template-id="' + templateId + '"]' )
      .removeClass( 'envato-elements-template-status--imported' )
      .addClass( 'envato-elements-template-status--importing' );

    const collectionTemplate = templateCache.getItem( { collectionId: collectionId, templateId: templateId }, this );
    this.setImportStatus( 'Importing:', collectionTemplate.data.templateName );
    api.post( 'import/' + categorySlug + '/process', {
      collectionId: collectionId,
      templateId: templateId,
      importType: 'library',
    } )
      .then(
        ( json ) => {
          this.refreshAfterImportOrInsert( collectionId, json.templateId, json, () => {
            this.setImportStatus( 'Imported:', collectionTemplate.data.templateName, json.url );
          } );
        },
        ( err ) => {

        }
      )
      .finally( () => {
        delete this.pendingImports[ templateId ];
      } );

  };

  startBackgroundDirectInsert = ( categorySlug, collectionId, templateId ) => {
    // Check this template doesn't already exist in the pending list.
    if ( typeof this.pendingImports[ templateId ] !== 'undefined' ) {
      return;
    }
    this.pendingImports[ templateId ] = true;

    $( '.envato-elements-import-button[data-template-id="' + templateId + '"]' )
      .html( 'Importing <span></span>' )
      .removeClass( 'envato-elements-import-button--imported' )
      .addClass( 'envato-elements-import-button--importing' );

    $( '.envato-elements-template-status[data-template-id="' + templateId + '"]' )
      .removeClass( 'envato-elements-template-status--imported' )
      .addClass( 'envato-elements-template-status--importing' );

    const collectionTemplate = templateCache.getItem( { collectionId: collectionId, templateId: templateId }, this );
    this.setImportStatus( 'Inserting:', collectionTemplate.data.templateName );
    api.post( 'insert/' + categorySlug + '/process', {
      collectionId: collectionId,
      templateId: templateId,
      insertType: 'direct',
    } )
      .then(
        ( json ) => {
          // We're in modal land, hide the modal and insert template calling Elementor API
          if ( typeof elementor !== 'undefined' ) {
            var model = new Backbone.Model( {
              getTitle: function getTitle() {
                return 'Test';
              },
            } );
            elementor.channels.data.trigger( 'template:before:insert', model );
            for ( var i = 0; i < json.data.content.length; i++ ) {
              elementor.getPreviewView().addChildElement( json.data.content[ i ] );
            }
            elementor.channels.data.trigger( 'template:after:insert', {} );
            window.elementsModal.hide();
          }
        },
        ( err ) => {

        }
      )
      .finally( () => {
        delete this.pendingImports[ templateId ];
      } );

  };

  getReplaceData = () => {
    return this.importData;
  };

  refreshImportList = () => {
    const $dom = template.getDom( 'tmpl-envato-elements__importer', this.getReplaceData() );
    if ( !this.$importer ) {
      this.$holder.append( $dom );
    } else {
      this.$importer.replaceWith( $dom );
    }
    this.$importer = $dom;
  };

  setThumbStates = () => {
    // apply import status to any buttons we find on the page.
    if ( 0 < this.importData.imports.length ) {
      for ( let i of this.importData.imports ) {
        if ( i.imported || typeof i.inserted !== 'undefined' && i.inserted.length > 0 ) {
          $( '.envato-elements-template-status[data-template-id="' + i.templateId + '"]' )
            .removeClass( 'envato-elements-template-status--inserting' )
            .removeClass( 'envato-elements-template-status--importing' )
            .addClass( 'envato-elements-template-status--imported' );
        }
      }
    }
  };

  loadImportHistory = () => {

    // We query the rest API to find a list of any imports that are currently underway.
    api.post( 'import/status' )
      .then(
        ( json ) => {
          if ( json && json.data ) {
            Object.assign( this.importData, this.importData, json.data );
            this.refreshImportList();
            this.setThumbStates();
          }
        },
        ( err ) => {
        }
      );
  };

  setImportStatus = ( title, message, link ) => {
    $( '.envato-elements__importer-status' )
      .html( '<span><strong>' + title + '</strong> ' + (link ? '<a href="' + link + '" target="_blank">' + message + '</a>' : message) + '</span>' )
      .addClass( '--active' );
  };

  refreshAfterImportOrInsert = ( collectionId, templateId, json, callback ) => {
    this.loadImportHistory();
    // Import succeeded. We should refresh our object cache for the collection so the 'This template installed' message appears correctly.
    if ( json && 'undefined' !== typeof json.category && json.category === 'elementor-blocks' ) {
      // We don't refresh a collection after importing a block. Refresh individual template.
      const collections = collectionsCache.getItem( {
        categorySlug: json.category,
      } );
      collections.refreshPageFromAPI()
        .then( () => {
          callback();
        } );
    } else {
      const collection = collectionCache.getItem( {
        collectionId: collectionId,
      } );
      collection.getDataFromAPI()
        .then( () => {
          if ( config.state( 'requestedTemplateId' ) === templateId ) {
            // Still looking at the same page. Refresh the template so the updated handlebars "imported" buttons appear.
            collection.openDetailView( templateId, templateId );
          } else {
            collection.openDetailView( null, templateId );
          }
          callback();
        } );
    }
  };

}

export let importer = new Importer();
