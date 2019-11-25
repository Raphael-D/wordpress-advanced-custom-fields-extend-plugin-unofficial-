import { config } from '../utils/config';
import $ from 'jquery';
import { template } from '../utils/template';
import { api } from '../utils/api';
import { templateCache } from './template';

import ObjectCache from '../utils/objectCache';
import { error } from '../utils/error';
import { scroller } from '../utils/scroller';
import OverlayScrollbars from 'overlayscrollbars';
import { lazyLoader } from '../utils/lazyLoader';

export let collectionCache = new ObjectCache( ( cache, settings, parent ) => {
  const key = settings && settings.collectionId ? settings.collectionId : 'loading';
  if ( typeof cache[ key ] === 'undefined' ) {
    cache[ key ] = new Collection();
  }
  if ( parent ) {
    cache[ key ].setParent( parent );
  }
  cache[ key ].updateData( settings );
  return cache[ key ];
} );

class Collection {
  constructor( { collectionId = false } = {} ) {
    // We define `this.data` variables so that handlebars can read this object out and loop over them.
    this.data = {
      collectionId: collectionId,
      categorySlug: '',
      pageTitle: 'Items',
      collectionName: 'loading',
      collectionUrl: '#',
      templates: [], // json data from summary view display.
      templateObjects: [],
    };

    this.$dom = null;
    this.$container = null;
    this.itemScroller = null;
  }

  setParent = ( parent ) => {
    this.parent = parent;
  };

  getReplaceData = () => {
    let data = this.data;
    if ( this.parent ) {
      data = Object.assign( {}, this.parent.getReplaceData(), data );
    }
    return data;
  };

  updateData = ( apiData ) => {
    Object.assign( this.data, this.data, apiData );
  };

  getDataFromAPI = () => {
    return new Promise( ( resolve, reject ) => {
      api.post( 'collection/' + this.data.categorySlug + '', { 'collection_id': this.data.collectionId } )
        .then(
          ( json ) => {
            if ( json && json.data ) {
              this.updateData( json.data );
              resolve();
            } else {
              error.displayError( 'Collection API Error', json && typeof json.error !== 'undefined' ? json.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
              reject();
            }
          },
          ( err ) => {
            if ( err && typeof err.code !== 'undefined' && err.code === 'rest_cookie_invalid_nonce' ) {
              error.displayError( 'API Token Expired', 'Refreshing please wait...' );
              setTimeout( function () { window.location.reload(); }, 500 );
            } else {
              error.displayError( 'Collection API Error', typeof err.error !== 'undefined' ? err.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
            }
            reject();
          }
        );
    } );
  };

  renderCollectionFromAPI = () => {
    return new Promise( ( resolve, reject ) => {
      template.pageLoading();
      if ( config.state( 'requestedCollectionId' ) !== this.data.collectionId ) {
        this.getDataFromAPI()
          .then( () => {
              let $main = template.renderMainDom( 'tmpl-envato-elements__collections', this.getReplaceData() );
              let $collectionsHolder = $main.find( '.envato-elements__collections-content' );
              this.$dom = template.getDom( 'tmpl-envato-elements__collections-single', this.getReplaceData() );
              $collectionsHolder.append( this.$dom );
              let $templatesSummary = this.$dom.find( '.envato-elements__collections-templates' );
              for ( var c of this.data.templates ) {
                if ( typeof c.collectionId === 'undefined' ) {
                  c.collectionId = this.data.collectionId;
                }
                const collectionTemplate = templateCache.getItem( c, this );
                collectionTemplate.renderTemplateDom( $templatesSummary );
              }

              this.contentInnerScrollInit();
              template.mainRenderFinished();
              resolve();
            },
            () => {
              reject();
            } );
      } else {
        template.mainRenderFinished();
        resolve();
      }
    } );

  };

  // This puts the entire kit onto the page. Blocks will just be a thumb, others will be full kit with horizontal scroll.
  renderSummaryDom = ( $container ) => {
    this.$container = $container;
    this.$dom = template.getDom( 'tmpl-envato-elements__collections-single', this.getReplaceData() );
    this.$container.append( this.$dom );
    let $templatesSummary = this.$dom.find( '.envato-elements__collections-templates' );
    for ( var c of this.data.templates ) {
      if ( typeof c.collectionId === 'undefined' ) {
        c.collectionId = this.data.collectionId;
      }
      const collectionTemplate = templateCache.getItem( c, this );
      collectionTemplate.renderTemplateDom( $templatesSummary );
    }

    this.contentInnerScrollInit();
  };

  contentInnerScrollInit = () => {
    if ( 'undefined' !== typeof this.data.categorySlug ) {
      switch ( this.data.categorySlug ) {
        case 'elementor':
        case 'beaver-builder':
          this.horizontalScrollInit();
          break;
        case 'elementor-blocks':
          // No horizontal scroll on the main summary view.
          break;
      }
    }
  };

  // Horizontal scroll is used for Elementor and Beaver Builder kits (but not blocks)
  horizontalScrollInit = () => {
    let $element = this.$dom.find( '.envato-elements__collections-scroller' );
    if ( this.itemScroller ) {
      if ( $element.hasClass( 'os-host' ) ) {
        // scrollbars still active.
        return;
      }
      this.itemScroller.destroy();
      this.itemScroller = null;
    }
    this.itemScroller = OverlayScrollbars( $element, {
      className: 'os-theme-thick-dark',
      overflowBehavior: {
        y: 'hidden'
      },
      scrollbars: {
        clickScrolling: false, // This is a bit buggy.
        autoHide: 'never'
      },
      callbacks: {
        onScroll: () => {
          lazyLoader.checkVisible();
        }
      }
    } );

    this.$dom.find( '.js-envato-elements__thumb-scroll' ).each( function () {
      // thumbs are 300px wide. rendered into 200x100 boxes.
      let thumbHeight = parseInt( $( this ).data( 'thumb-height' ) );
      if ( thumbHeight > 0 ) {
        // work out rendered size.
        thumbHeight = Math.ceil( thumbHeight * (2 / 3) );
        if ( thumbHeight > 100 ) {
          let thumbSections = (thumbHeight - 100) / 100;
          this.style.WebkitTransitionDuration = this.style.transitionDuration = (Math.round( (thumbSections * 1.25) * 1000 ) / 1000) + 's';
          $( this ).data( 'hover-animation-speed', this.style.WebkitTransitionDuration );
        }
      }
    } ).hover( function () {
      this.style.WebkitTransitionDuration = this.style.transitionDuration = '0.6s';
    }, function () {
      this.style.WebkitTransitionDuration = this.style.transitionDuration = $( this ).data( 'hover-animation-speed' );
    } );

  };

  verticalScrollInit = () => {
    let $element = this.$dom.find( '.envato-elements__collections-scroller' );

    if ( this.itemScroller ) {
      if ( $element.hasClass( 'os-host' ) ) {
        // scrollbars still active.
        return;
      }
      this.itemScroller.destroy();
      this.itemScroller = null;
    }

    this.itemScroller = OverlayScrollbars( $element, {
      className: 'os-theme-thick-dark',
      overflowBehavior: {
        x: 'hidden'
      },
      scrollbars: {
        clickScrolling: false, // This is a bit buggy.
        autoHide: 'never'
      },
      callbacks: {
        onScroll: () => {
          lazyLoader.checkVisible();
        }
      }
    } );

    lazyLoader.checkVisible();
  };

  openDetailView = ( templateId, justImportedTemplateId ) => {
    let currentTemplate = null;
    for ( var c of this.data.templates ) {
      if ( typeof c.collectionId === 'undefined' ) {
        c.collectionId = this.data.collectionId;
      }
      const collectionTemplate = templateCache.getItem( c, this );
      if ( justImportedTemplateId && justImportedTemplateId === collectionTemplate.data.templateId ) {
        // Refresh the thumbnail so the "imported" labels are added.
        collectionTemplate.updateTemplateDom();
      }
      if ( !currentTemplate && (templateId === true || templateId === collectionTemplate.data.templateId) ) {
        currentTemplate = collectionTemplate;
      }
    }
    if ( currentTemplate ) {
      let autoScrollToPos = false;
      if ( 'undefined' !== typeof this.data.categorySlug ) {
        switch ( this.data.categorySlug ) {
          case 'elementor':
          case 'beaver-builder':
            autoScrollToPos = true;
            break;
          case 'elementor-blocks':
            // We want to enable a vertical scrollbar when opening the detail view here.
            this.verticalScrollInit();
            break;
        }
      }

      this.$dom.addClass( 'envato-elements__collections-single--open' );
      this.$dom.parents( '.envato-elements__collections-content' ).first().addClass( 'envato-elements__collections-content--open' );
      scroller.disableScroll( this.$dom.get( 0 ) );
      setTimeout( () => {
        // Resize after disable scroll so fixed boxes sort themselves out.
        $( window ).trigger( 'resize' );
      }, 50 );
      scroller.scrollTo( 0, this.$dom[ 0 ].offsetTop - 15 );
      // This interval is required when we open a box at the bottom of the page, page grows so we have to keep up with new dom pos.
      let scrollToBox = setInterval( () => {
        scroller.scrollTo( 0, this.$dom[ 0 ].offsetTop - 15 );
      }, 50 );
      setTimeout( () => {
        clearInterval( scrollToBox );
        // Resize after disable scroll so fixed boxes sort themselves out.
        $( window ).trigger( 'resize' );
        // set the min-height after css animation stops.
        this.$dom.addClass( 'envato-elements__collections-single--opened' );
      }, 500 );
      const $detailContainer = this.$dom.find( '.envato-elements__collections-single-detail' );
      currentTemplate.renderHighlightedDom( $detailContainer );
      // Auto scroll to the position of this item.
      if ( this.itemScroller && autoScrollToPos ) {
        this.itemScroller.scroll( currentTemplate.$dom, 400 );
      }
    }

  };

  closeDetailView = () => {
    scroller.enableScroll();
    if ( this.itemScroller ) {
      this.itemScroller.scroll( { x: 0 }, 150 );
    }
    $( '.envato-elements__collection-template-cell--active' ).removeClass( 'envato-elements__collection-template-cell--active' );
    if ( this.$dom ) {
      this.$dom.removeClass( 'envato-elements__collections-single--open' )
        .removeClass( 'envato-elements__collections-single--opened' );
      this.$dom.parents( '.envato-elements__collections-content' ).first().removeClass( 'envato-elements__collections-content--open' );
    }
  };

}