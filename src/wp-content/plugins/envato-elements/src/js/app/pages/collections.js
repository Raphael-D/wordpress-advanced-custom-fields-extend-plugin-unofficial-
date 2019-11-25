import { config } from '../utils/config';
import { collectionsCache } from '../objects/collections';
import { collectionCache } from '../objects/collection';
import { clearAllBodyScrollLocks } from 'body-scroll-lock';

class KitCollections {
  constructor() {
    this.categories = config.get( 'categories' );
  }

  pageLoaded = () => {
  };

  loadCategoryItems = ( navType, categorySlug, searchParams, callback ) => {

    const category = this.categories[ categorySlug ];
    const collections = collectionsCache.getItem( {
      category: category,
      pageTitle: category.page_title,
      categorySlug: categorySlug,
      searchParams: searchParams,
    } );

    collections.closeCollections();
    if ( config.state( 'requestedCategorySlug' ) === categorySlug && 'collection-close' === navType ) {
      return;
    }
    config.state( 'requestedCategorySlug', categorySlug );
    collections.renderPageFromAPI()
      .then( () => {
        if ( callback && 'function' === typeof callback ) {
          callback();
        }
      } )
      .catch( () => {
      } );

  };

  loadCollectionItems = ( navType, categorySlug, searchParams, collectionId, templateId ) => {

    const category = this.categories[ categorySlug ];

    if ( config.state( 'requestedCategorySlug' ) !== categorySlug ) {
      this.loadCategoryItems( navType, categorySlug, searchParams, () => {
        this.loadCollectionItems( navType, categorySlug, searchParams, collectionId, templateId );
      } );
      return;
    }
    // We already have the loaded category DOM on page from above.
    const collections = collectionsCache.getItem( {
      category: category,
      pageTitle: category.page_title,
      categorySlug: categorySlug,
      searchParams: searchParams,
    } );
    config.state( 'requestedCollectionId', collectionId );
    config.state( 'requestedTemplateId', templateId );
    switch ( navType ) {
      case 'collection':
        collections.openCollection( collectionId );
        break;
      case 'template':
        // Highlight individual template.
        collections.openCollection( collectionId, templateId );
        break;
      case 'photo':
        // Highlight individual photo
        collections.openCollection( collectionId, templateId );
        break;
    }

  };

  navigationChange = ( admin, query, action ) => {

    if ( query && query.categorySlug && action && query.navType ) {
      if ( 'undefined' !== typeof this.categories[ query.categorySlug ] ) {

        console.log(query.navType);
        switch ( query.navType ) {
          case 'main-category':
            // Loading the main category page with all the small thumbs.
            clearAllBodyScrollLocks();
            'POP' !== action && admin.history && admin.history.push( '?' + config.get( 'admin_slug' ) + '&category=' + query.categorySlug, 'history' );
            this.loadCategoryItems( query.navType, query.categorySlug, {} );
            break;
          case 'category':
          case 'collection-close':
            // Inside pages with pagination and search
            clearAllBodyScrollLocks();
            'POP' !== action && admin.history && admin.history.push( '?' + config.get( 'admin_slug' ) + '&category=' + query.categorySlug + '&search=' + query.searchParamsURI, 'history' );
            this.loadCategoryItems( query.navType, query.categorySlug, query.searchParams );
            break;
          case 'collection':
            // Clicked into a collection (default showing the first thumb).
            'POP' !== action && admin.history && admin.history.push( '?' + config.get( 'admin_slug' ) + '&category=' + query.categorySlug + '&collection_id=' + query.collectionId + '&search=' + query.searchParamsURI, 'history' );
            this.loadCollectionItems( query.navType, query.categorySlug, query.searchParams, query.collectionId );
            break;
          case 'template':
            // Choosing a specific item within a collection.
            'POP' !== action && admin.history && admin.history.push( '?' + config.get( 'admin_slug' ) + '&category=' + query.categorySlug + '&collection_id=' + query.collectionId + '&template_id=' + query.templateId + '&search=' + query.searchParamsURI, 'history' );
            this.loadCollectionItems( query.navType, query.categorySlug, query.searchParams, query.collectionId, query.templateId );
            break;
          case 'photo':
            // Choosing a specific photo
            'POP' !== action && admin.history && admin.history.push( '?' + config.get( 'admin_slug' ) + '&category=' + query.categorySlug + '&template_id=' + query.templateId + '&search=' + query.searchParamsURI, 'history' );
            this.loadCollectionItems( query.navType, query.categorySlug, query.searchParams, query.collectionId, query.templateId );
            break;
        }

      }
    }

  };

}

export let kitCollections = new KitCollections();
