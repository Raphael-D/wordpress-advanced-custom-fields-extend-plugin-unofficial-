import $ from 'jquery';
import { template } from '../utils/template';
import { lazyLoader } from '../utils/lazyLoader';

import ObjectCache from '../utils/objectCache';
import { scroller } from '../utils/scroller';
import { importer } from '../utils/importer';
import OverlayScrollbars from 'overlayscrollbars';

export let photoCache = new ObjectCache( ( cache, settings, parent ) => {
  if ( settings && 'undefined' === typeof settings.humane_id ) {
    console.log( 'Failed to find photo collection' );
    console.log( settings );
  }
  const key = (settings && settings.humane_id ? settings.humane_id : 'loading');
  if ( 'undefined' === typeof cache[ key ] ) {
    cache[ key ] = new Photo( settings );
  }
  if ( parent ) {
    cache[ key ].setParent( parent );
  }
  cache[ key ].updateData( settings );
  return cache[ key ];
} );

export default class Photo {
  constructor( { photoId = false } = {} ) {

    this.data = {
      photoId: photoId,
      photoName: 'loading',
      photoUrl: '#',
      previewThumb: '#',
      previewThumbAspect: '100%'
    };

    this.$dom = null;
    this.lastScrollPos = 0;
    this.lastImageMarginTop = 0;
    this.verticalScroll = null;
    this.$photoPreview = null;
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

  updatePhotoDom = () => {
    if ( this.$dom ) {
      let newOne = template.getDom( 'tmpl-envato-elements__photo-wrap', this.getReplaceData() );
      this.$dom.replaceWith( newOne );
      this.$dom = newOne;
      lazyLoader.addLazy( this.$dom );
      lazyLoader.checkVisibleCallback();
    }
    if ( this.$photoPreview ) {
      this.loadPhotoPreview( this.parent.$dom );
    }
  };

  renderPhotoDom = ( $container ) => {
    this.$dom = template.getDom( 'tmpl-envato-elements__photo-wrap', this.getReplaceData() );
    $container.append( this.$dom );
    lazyLoader.addLazy( this.$dom );
  };

  loadPhotoPreview = ( $dom ) => {
    scroller.rememberScrollPoint();
    $( '.envato-elements__photo-preview' ).remove();
    this.$photoPreview = template.getDom( 'tmpl-envato-elements__photo-preview', this.getReplaceData() );
    $dom.addClass( '--photo-visible' );
    this.$photoPreview.insertBefore( $dom );
    setTimeout( () => {
      // Resize after disable scroll so fixed boxes sort themselves out.
      $( window ).trigger( 'resize' );
    }, 50 );
  };

  closePhotoPreview = ( $dom ) => {
    $dom.removeClass( '--photo-visible' );
    $( '.envato-elements__photo-preview' ).remove();
    if ( this.$photoPreview ) {
      scroller.restoreScrollPoint();
    }
    this.$photoPreview = null;
    setTimeout( () => {
      // Resize after disable scroll so fixed boxes sort themselves out.
      $( window ).trigger( 'resize' );
    }, 50 );
  };

}
