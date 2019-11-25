import $ from 'jquery';
import { template } from '../utils/template';
import { lazyLoader } from '../utils/lazyLoader';

import ObjectCache from '../utils/objectCache';
import { scroller } from '../utils/scroller';
import { importer } from '../utils/importer';
import OverlayScrollbars from 'overlayscrollbars';

export let templateCache = new ObjectCache( ( cache, settings, parent ) => {
  if ( settings && 'undefined' === typeof settings.collectionId ) {
    console.log( 'Failed to find template collection' );
    console.log( settings );
  }
  const key = (settings && settings.collectionId ? settings.collectionId : 'loading') + (settings && settings.templateId ? settings.templateId : 'loading');
  if ( 'undefined' === typeof cache[ key ] ) {
    cache[ key ] = new Template( settings );
  }
  if ( parent ) {
    cache[ key ].setParent( parent );
  }
  cache[ key ].updateData( settings );
  return cache[ key ];
} );

export default class Template {
  constructor( { templateId = false } = {} ) {

    this.data = {
      templateId: templateId,
      templateName: 'loading',
      templateUrl: '#',
      previewThumb: '#',
      previewThumbAspect: '100%'
    };

    this.$dom = null;
    this.lastScrollPos = 0;
    this.lastImageMarginTop = 0;
    this.verticalScroll = null;
    this.$blockPreview = null;
  }

  setParent = ( parent ) => {
    if ( parent ) {
      this.parent = parent;
    }
  };

  updateData = ( apiData ) => {
    Object.assign( this.data, this.data, apiData );
  };

  getReplaceData = () => {
    let data = this.data;
    if ( this.parent ) {
      data = Object.assign( {}, this.parent.getReplaceData(), {
        importMode: importer.importMode
      }, data );
    }
    return data;
  };

  updateTemplateDom = () => {
    if ( this.$dom ) {
      let newOne = template.getDom( 'tmpl-envato-elements__collection-template-cell', this.getReplaceData() );
      this.$dom.replaceWith( newOne );
      this.$dom = newOne;
      lazyLoader.addLazy( this.$dom );
      lazyLoader.checkVisibleCallback();
    }
    if ( this.$blockPreview ) {
      this.loadBlockPreview( this.parent.$dom );
    }
  };

  renderTemplateDom = ( $container ) => {
    this.$dom = template.getDom( 'tmpl-envato-elements__collection-template-cell', this.getReplaceData() );
    $container.append( this.$dom );
    lazyLoader.addLazy( this.$dom );
  };

  renderHighlightedDom = ( $detailContainer ) => {
    $( '.envato-elements__collection-template-cell--active' ).removeClass( 'envato-elements__collection-template-cell--active' );
    this.$dom.addClass( 'envato-elements__collection-template-cell--active' );
    let $preview = template.getDom( 'tmpl-envato-elements__collection-preview', this.getReplaceData() );
    $detailContainer.empty().append( $preview );
    if ( this.verticalScroll ) {
      this.verticalScroll.destroy();
      this.verticalScroll = null;
    }
    this.verticalScroll = OverlayScrollbars( $detailContainer.find( '.envato-elements__collection-detail-thumbnail' ), {
      className: 'os-theme-thick-dark',
      overflowBehavior: {
        x: 'hidden'
      },
      scrollbars: {
        autoHide: 'never'
      }
    } );
  };

  loadBlockPreview = ( $dom ) => {
    scroller.rememberScrollPoint();
    $( '.envato-elements__block-preview' ).remove();
    this.$blockPreview = template.getDom( 'tmpl-envato-elements__block-preview', this.getReplaceData() );
    $dom.addClass( '--block-visible' );
    this.$blockPreview.insertBefore( $dom );
    setTimeout( () => {
      // Resize after disable scroll so fixed boxes sort themselves out.
      $( window ).trigger( 'resize' );
    }, 50 );
  };

  closeBlockPreview = ( $dom ) => {
    $dom.removeClass( '--block-visible' );
    $( '.envato-elements__block-preview' ).remove();
    if ( this.$blockPreview ) {
      scroller.restoreScrollPoint();
    }
    this.$blockPreview = null;
    setTimeout( () => {
      // Resize after disable scroll so fixed boxes sort themselves out.
      $( window ).trigger( 'resize' );
    }, 50 );
  };

}
