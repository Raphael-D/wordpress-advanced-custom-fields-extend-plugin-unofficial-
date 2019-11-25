import { config } from '../utils/config';
import { template } from '../utils/template';
import { api } from '../utils/api';
import { error } from '../utils/error';
import { collectionCache } from './collection';
import { templateCache } from './template';
import { photoCache } from './photo';

import ObjectCache from '../utils/objectCache';
import { clearAllBodyScrollLocks, disableBodyScroll } from 'body-scroll-lock';
import $ from 'jquery';

export let collectionsCache = new ObjectCache( ( cache, settings ) => {
  const key = settings && settings.categorySlug ? settings.categorySlug : 'loading';
  if ( 'undefined' === typeof cache[ key ] ) {
    if ( key === 'elementor-blocks' ) {
      cache[ key ] = new CollectionsEb();
    } else if ( key === 'photos' ) {
      cache[ key ] = new CollectionsPhotos();
    } else {
      cache[ key ] = new Collections();
    }
  }
  cache[ key ].updateData( settings );
  return cache[ key ];
} );

class Collections {
  constructor() {
    this.data = {
      categorySlug: '',
      pageTitle: 'Items',
      collections: [],
      searchParams: {},
    };
    this.meta = {};

    this.currentlyOpen = null;
  }

  updateData = ( apiData ) => {
    Object.assign( this.data, this.data, apiData );
  };

  // Meta is used for things like global filters (industry)
  updateMeta = ( apiMeta ) => {
    Object.assign( this.meta, this.meta, apiMeta );
  };

  renderPageFromAPI = () => {
    return new Promise( ( resolve, reject ) => {
      template.pageLoading();
      api.post( 'collections/' + this.data.categorySlug, {
        elementsSearch: this.data.searchParams
      } )
        .then(
          ( json ) => {
            if ( json && json.data ) {

              // Reset available collections each time we do a search.
              this.data.results = [];
              this.updateData( json.data );

              if ( config.state( 'requestedCategorySlug' ) !== this.data.categorySlug ) {
                return reject();
              }

              if ( typeof json.meta !== 'undefined' ) {
                this.updateMeta( json.meta );
              }

              this.$dom = template.renderMainDom( 'tmpl-envato-elements__collections', this.getReplaceData() );
              this.$collectionsHolder = this.$dom.find( '.envato-elements__collections-content' );
              if ( this.$collectionsHolder && this.data.results && this.data.results.length ) {
                for ( var c of this.data.results ) {
                  const collection = collectionCache.getItem( c, this );
                  collection.renderSummaryDom( this.$collectionsHolder );
                }
              }

              config.state( 'currentCategorySlug', this.data.categorySlug );
              template.mainRenderFinished();
              resolve();
            } else {
              error.displayError( 'Collections Data JSON Error', json && typeof json.error !== 'undefined' ? json.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
              reject();
            }
          },
          ( err ) => {
            if ( err && typeof err.code !== 'undefined' && err.code === 'rest_cookie_invalid_nonce' ) {
              error.displayError( 'API Token Expired', 'Refreshing please wait...' );
              setTimeout( function () { window.location.reload(); }, 500 );
            } else {
              error.displayError( 'Collections Data Error', typeof err.error !== 'undefined' ? err.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
            }
            reject();
          }
        );
    } );
  };

  closeCollections = () => {
    clearAllBodyScrollLocks();
    if ( this.currentlyOpen ) {
      this.currentlyOpen.closeDetailView();
      this.currentlyOpen = null;
    }
    setTimeout( () => {
      // Resize after disable scroll so fixed boxes sort themselves out.
      $( window ).trigger( 'resize' );
    }, 50 );

  };

  openCollection = ( collectionId, templateId ) => {
    const collection = collectionCache.getItem( { collectionId: collectionId } );
    if ( collection ) {
      if ( this.currentlyOpen && (!templateId || this.currentlyOpen !== collection) ) {
        this.closeCollections();
      }
      this.currentlyOpen = collection;
      if ( templateId ) {
        collection.openDetailView( templateId );
      } else {
        collection.openDetailView( true );
      }
    }
  };

  getReplaceData = () => {
    this.data.pagination = [];
    if ( this.data.page_number && this.data.total_results && this.data.per_page ) {
      if ( this.data.total_results > this.data.per_page ) {
        for ( let pg = 0; pg < this.data.total_results / this.data.per_page; pg++ ) {
          this.data.pagination.push( {
            'pageNumber': pg,
            'pageLabel': pg + 1,
            'pageCurrent': parseInt( this.data.page_number ) === pg + 1,
          } );
        }
      }
    }
    this.data.meta = this.meta;
    return this.data;
  };

}

class CollectionsEb extends Collections {
  constructor() {
    super();
    this.currentBlock = null;
  }

  // We're removing the concept of collections from blocks.
  // So this will just open an individual template (block)
  openCollection = ( collectionId, templateId ) => {
    if ( !this.$dom ) return;
    if ( this.currentBlock ) {
      this.currentBlock.closeBlockPreview( this.$dom );
    }
    this.currentBlock = templateCache.getItem( { collectionId: collectionId, templateId: templateId }, this );
    this.currentBlock.loadBlockPreview( this.$dom );
  };

  closeCollections = () => {
    if ( this.$dom && this.currentBlock ) {
      this.currentBlock.closeBlockPreview( this.$dom );
    }
  };

  renderPageFromAPI = () => {
    return new Promise( ( resolve, reject ) => {
      template.pageLoading();
      api.post( 'collections/' + this.data.categorySlug, {
        elementsSearch: this.data.searchParams
      } )
        .then(
          ( json ) => {
            if ( json && json.data ) {

              // Reset available collections each time we do a search.
              this.data.blocks = [];
              this.updateData( json.data );

              if ( config.state( 'requestedCategorySlug' ) !== this.data.categorySlug ) {
                return reject();
              }

              if ( typeof json.meta !== 'undefined' ) {
                this.updateMeta( json.meta );
              }

              this.$dom = template.renderMainDom( 'tmpl-envato-elements__collections', this.getReplaceData() );
              this.$blocksHolder = this.$dom.find( '.envato-elements__collections-content' );
              // Update, we're grouping blocks by category.
              if ( this.$blocksHolder && this.data.blocks && this.data.blocks.length ) {
                for ( let c of this.data.blocks ) {
                  // Add wrapper for this block content.
                  const $blockWrapper = template.getDom( 'tmpl-envato-elements__block-wrap', c );
                  this.$blocksHolder.append( $blockWrapper );
                  for ( let b of c.blocks ) {
                    const block = templateCache.getItem( b, this );
                    block.renderTemplateDom( $blockWrapper.find( '.envato-elements__block-content' ) );
                  }
                }
              }

              config.state( 'currentCategorySlug', this.data.categorySlug );
              template.mainRenderFinished();
              resolve();
            } else {
              error.displayError( 'Blocks Data JSON Error', json && typeof json.error !== 'undefined' ? json.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
              reject();
            }
          },
          ( err ) => {
            if ( err && typeof err.code !== 'undefined' && err.code === 'rest_cookie_invalid_nonce' ) {
              error.displayError( 'API Token Expired', 'Refreshing please wait...' );
              setTimeout( function () { window.location.reload(); }, 500 );
            } else {
              error.displayError( 'Blocks Data Error', typeof err.error !== 'undefined' ? err.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
            }
            reject();
          }
        );
    } );
  };

  refreshPageFromAPI = () => {
    return new Promise( ( resolve, reject ) => {
      if ( !this.$blocksHolder ) return reject();
      //template.pageLoading();
      api.post( 'collections/' + this.data.categorySlug, {
        elementsSearch: this.data.searchParams
      } )
        .then(
          ( json ) => {
            if ( json && json.data ) {

              // Reset available collections each time we do a search.
              this.data.blocks = [];
              this.updateData( json.data );

              if ( config.state( 'requestedCategorySlug' ) !== this.data.categorySlug ) {
                return reject();
              }

              if ( typeof json.meta !== 'undefined' ) {
                this.updateMeta( json.meta );
              }

              if ( this.$blocksHolder && this.data.blocks && this.data.blocks.length ) {
                for ( let c of this.data.blocks ) {
                  for ( let b of c.blocks ) {
                    const block = templateCache.getItem( b, this );
                    block.updateTemplateDom();
                  }
                }
              }
              template.mainRenderFinished();
              resolve();
            } else {
              error.displayError( 'Blocks Data JSON Error', json && typeof json.error !== 'undefined' ? json.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
              reject();
            }
          },
          ( err ) => {
            if ( err && typeof err.code !== 'undefined' && err.code === 'rest_cookie_invalid_nonce' ) {
              error.displayError( 'API Token Expired', 'Refreshing please wait...' );
              setTimeout( function () { window.location.reload(); }, 500 );
            } else {
              error.displayError( 'Blocks Data Error', typeof err.error !== 'undefined' ? err.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
            }
            reject();
          }
        );
    } );
  };

}

class CollectionsPhotos extends Collections {
  constructor() {
    super();
    this.currentPhoto = null;
  }

  // We're removing the concept of collections from photos.
  // So this will just open an individual template (photo)
  openCollection = ( collectionId, humaneId ) => {
    if ( !this.$dom ) return;
    if ( this.currentPhoto ) {
      this.currentPhoto.closePhotoPreview( this.$dom );
    }
    this.currentPhoto = photoCache.getItem( { humane_id: humaneId }, this );
    this.currentPhoto.loadPhotoPreview( this.$dom );
  };

  closeCollections = () => {
    if ( this.$dom && this.currentPhoto ) {
      this.currentPhoto.closePhotoPreview( this.$dom );
    }
  };

  renderPageFromAPI = () => {
    return new Promise( ( resolve, reject ) => {
      template.pageLoading();
      api.post( 'collections/' + this.data.categorySlug, {
        elementsSearch: this.data.searchParams
      } )
        .then(
          ( json ) => {
            if ( json && json.data ) {

              // Reset available collections each time we do a search.
              this.data.photos = [];
              this.updateData( json.data );

              if ( config.state( 'requestedCategorySlug' ) !== this.data.categorySlug ) {
                return reject();
              }

              if ( typeof json.meta !== 'undefined' ) {
                this.updateMeta( json.meta );
              }

              this.$dom = template.renderMainDom( 'tmpl-envato-elements__collections', this.getReplaceData() );
              this.$photosHolder = this.$dom.find( '.envato-elements__collections-content' );
              if ( this.$photosHolder && this.data.photos && this.data.photos.length ) {
                for ( let p of this.data.photos ) {
                  const photo = photoCache.getItem( p, this );
                  photo.renderPhotoDom( this.$photosHolder );
                }
              }

              config.state( 'currentCategorySlug', this.data.categorySlug );
              template.mainRenderFinished();
              resolve();
            } else {
              error.displayError( 'Photos Data JSON Error', json && typeof json.error !== 'undefined' ? json.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
              reject();
            }
          },
          ( err ) => {
            if ( err && typeof err.code !== 'undefined' && err.code === 'rest_cookie_invalid_nonce' ) {
              error.displayError( 'API Token Expired', 'Refreshing please wait...' );
              setTimeout( function () { window.location.reload(); }, 500 );
            } else {
              error.displayError( 'Photos Data Error', typeof err.error !== 'undefined' ? err.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
            }
            reject();
          }
        );
    } );
  };

  refreshPageFromAPI = () => {
    return new Promise( ( resolve, reject ) => {
      if ( !this.$photosHolder ) return reject();
      //template.pageLoading();
      api.post( 'collections/' + this.data.categorySlug, {
        elementsSearch: this.data.searchParams
      } )
        .then(
          ( json ) => {
            if ( json && json.data ) {

              // Reset available collections each time we do a search.
              this.data.photos = [];
              this.updateData( json.data );

              if ( config.state( 'requestedCategorySlug' ) !== this.data.categorySlug ) {
                return reject();
              }

              if ( typeof json.meta !== 'undefined' ) {
                this.updateMeta( json.meta );
              }

              if ( this.$photosHolder && this.data.photos && this.data.photos.length ) {
                for ( let c of this.data.photos ) {
                  for ( let b of c.photos ) {
                    const photo = templateCache.getItem( b, this );
                    photo.updateTemplateDom();
                  }
                }
              }
              template.mainRenderFinished();
              resolve();
            } else {
              error.displayError( 'Photos Data JSON Error', json && typeof json.error !== 'undefined' ? json.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
              reject();
            }
          },
          ( err ) => {
            if ( err && typeof err.code !== 'undefined' && err.code === 'rest_cookie_invalid_nonce' ) {
              error.displayError( 'API Token Expired', 'Refreshing please wait...' );
              setTimeout( function () { window.location.reload(); }, 500 );
            } else {
              error.displayError( 'Photos Data Error', typeof err.error !== 'undefined' ? err.error : 'Sorry something went wrong. If this continues to happen please <a href="mailto:extensions@envato.com">report the bug to us</a>.' );
            }
            reject();
          }
        );
    } );
  };

}
