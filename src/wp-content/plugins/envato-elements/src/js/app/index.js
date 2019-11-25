/* global window, document */
import createHistory from 'history/createBrowserHistory';
import $ from 'jquery';
// pages:
import { header } from './pages/header';
import { plugins } from './pages/plugins';
import { kitCollections } from './pages/collections';
import { template } from './utils/template';
import { importer } from './utils/importer';
import { helper } from './utils/helper';
import { modal } from './utils/modal';
import { notifications } from './utils/notifications';
import { scroller } from './utils/scroller';
import select2 from 'select2';

class Admin {
  constructor() {
    this.items = [];
    this.history = null;
    this.unlisten = null;
  };

  pageLoaded = () => {
    scroller.setScrollHolder( window );
    this.items.push( modal );
    this.items.push( header );
    this.items.push( template );
    this.items.push( helper );
    if ( envato_elements_admin.license_activated ) {
      this.items.push( kitCollections );
      this.items.push( importer );
      this.items.push( notifications );
    }
    for ( var item of this.items ) {
      item.pageLoaded && item.pageLoaded();
    }
    this.navigationListen();
    this.clickListen();
  };

  pluginPageLoaded = () => {
    scroller.setScrollHolder( window );
    this.items.push( modal );
    this.items.push( plugins );
    for ( var item of this.items ) {
      item.pageLoaded && item.pageLoaded();
    }
  };

  magicButtonDone = false;
  magicButton = ( $contentHolder, $scrollHolder ) => {
    if ( !this.magicButtonDone ) {
      const modalContent = template.getDom( 'tmpl-envato-elements__magic', {} );
      $contentHolder.append( modalContent );
      scroller.setScrollHolder( $scrollHolder );
      importer.setMode( 'insert' );
      this.items.push( modal );
      this.items.push( header );
      this.items.push( template );
      this.items.push( helper );
      if ( envato_elements_admin.license_activated ) {
        this.items.push( kitCollections );
        this.items.push( importer );
        this.items.push( notifications );
      }
      for ( var item of this.items ) {
        item.pageLoaded && item.pageLoaded();
      }
      this.clickListen();
      this.magicButtonDone = true;
    }
    const navVars = {
      navType: 'main-category',
      categorySlug: 'elementor-blocks',
      collectionId: false,
      templateId: false,
      searchParams: {},
      searchParamsURI: '',
    };
    this.navigationEvent( navVars, 'click' );
  };

  clickListen = () => {
    const t = this;
    $( 'body' ).off( 'change.envato-elements' ).on( 'change.envato-elements', '.envato-elements--action-dropdown', ( event ) => {
      let $butt = $( event.target ).find( ':selected' );
      if ( $butt.data( 'nav-top' ) ) {
        window.scrollTo( 0, 0 );
      }
      const searchParams = this.getSearchParams( $butt.data( 'search' ) );
      const navVars = {
        navType: $butt.data( 'nav-type' ),
        categorySlug: $butt.data( 'category-slug' ),
        collectionId: $butt.data( 'collection-id' ),
        templateId: $butt.data( 'template-id' ),
        searchParams: searchParams,
        searchParamsURI: JSON.stringify( searchParams ),
      };
      t.navigationEvent( navVars, 'click' );
      return false;
    } );
    $( 'body' ).off( 'click.envato-elements' ).on( 'click.envato-elements', '.envato-elements--action', function ( event ) {
      return false;
    } ).on( 'change', '.envato-elements__chktoggle-input', function ( event ) {
      $( '.envato-elements__chktoggle-input' ).each( function () {
        if ( event && event.target && event.target !== this ) {
          $( this ).prop( 'checked', false );
        }
      } );
      return false;
    } ).off( 'mousedown.envato-elements' ).on( 'mousedown.envato-elements', '.envato-elements--action', ( event ) => {

      //if (event && event.target && !($(event.target).is('a') || $(event.target).parents('a').length || $(event.target).parents('.os-scrollbar').length)) {
      if ( event && event.target && !$( event.target ).parents( '.os-scrollbar' ).length ) {

        switch ( event.which ) {
          case 1:
            // left mouse button
            event.preventDefault();
            let $butt = $( event.target );
            if ( !$butt.data( 'nav-type' ) && $butt.parent().data( 'nav-type' ) ) {
              $butt = $butt.parent();
            }
            if ( $butt.data( 'nav-top' ) ) {
              window.scrollTo( 0, 0 );
            }
            const searchParams = this.getSearchParams( $butt.data( 'search' ) );
            const navVars = {
              navType: $butt.data( 'nav-type' ),
              categorySlug: $butt.data( 'category-slug' ),
              collectionId: $butt.data( 'collection-id' ),
              templateId: $butt.data( 'template-id' ),
              searchParams: searchParams,
              searchParamsURI: JSON.stringify( searchParams ),
            };
            t.navigationEvent( navVars, 'click' );
            return false;
        }

      }
    } );
  };

  navigationListen = () => {

    this.history = createHistory( {
      //basename: get('admin_base') // not required if we're just chaing the `?` query args.
    } );

    const location = this.history.location;
    this.triggerBrowserNavEvent( location, 'init' );

    this.unlisten = this.history.listen( ( location, action ) => {
      this.triggerBrowserNavEvent( location, action );
    } );

    // this.unlisten()

  };

  getSearchParams = ( buttonParams ) => {
    const query = location.search.substring( 1 );
    let querySearch = this.getQueryVariable( query, 'search' );
    let queryCalculated = {};
    if ( querySearch ) {
      queryCalculated = JSON.parse( decodeURIComponent( querySearch ) );
      if ( !queryCalculated ) {
        queryCalculated = {};
      }
    }
    // Override our URL provided query string from buttons:
    if ( buttonParams ) {
      if ( 'undefined' !== typeof buttonParams.pg ) {
        queryCalculated.pg = buttonParams.pg;
      }
      if ( 'undefined' !== typeof buttonParams.filters ) {
        queryCalculated.filters = buttonParams.filters;
      }
    }
    return queryCalculated;
  };

  triggerBrowserNavEvent = ( location, action ) => {
    modal.closeModal();

    const query = location.search.substring( 1 );
    const searchParams = this.getSearchParams();
    const navVars = {
      location: location,
      page: this.getQueryVariable( query, 'page' ),
      navType: 'category', // template
      categorySlug: this.getQueryVariable( query, 'category' ),
      collectionId: this.getQueryVariable( query, 'collection_id' ),
      templateId: this.getQueryVariable( query, 'template_id' ),
      searchParams: searchParams,
      searchParamsURI: JSON.stringify( searchParams ),
    };
    if ( navVars.templateId ) {
      navVars.navType = 'template';
    } else if ( navVars.collectionId ) {
      navVars.navType = 'collection';
    } else if ( !navVars.categorySlug && navVars.page === 'envato-elements' ) {
      navVars.categorySlug = Object.keys( envato_elements_admin.categories )[ 0 ];
    }

    this.navigationEvent( navVars, action );
  };

  navigationEvent = ( navVars, action ) => {
    // console.log(`action ${action} `);
    // console.log(navVars);
    if ( 'PUSH' !== action ) {
      let $select2elements = $( '.js-envato-elements__select2' );
      if ( $select2elements.length > 0 ) {
        select2( true, $ );
        $select2elements.select2( 'close' );
      }
      for ( var item of this.items ) {
        item.navigationChange && item.navigationChange( this, navVars, action );
      }
    }
  };

  getQueryVariable = ( query, variable, defaultValue ) => {
    const vars = query.split( '&' );
    for ( var i = 0; i < vars.length; i++ ) {
      var pair = vars[ i ].split( '=' );
      if ( decodeURIComponent( pair[ 0 ] ) === variable ) {
        return decodeURIComponent( pair[ 1 ] );
      }
    }
    return defaultValue;
  };
}

window.ElementsAdmin = new Admin();
