import $ from 'jquery';
import Handlebars from 'handlebars';
import { header } from '../pages/header';
import { importer } from './importer';
import { lazyLoader } from './lazyLoader';
import select2 from 'select2';

class Template {
  constructor() {
    this.$holder = null;
    this.templates = {};
    Handlebars.registerHelper( 'if_eq', function ( a, b, opts ) {
      if ( a === b )
        return opts.fn( this );
      else
        return opts.inverse( this );
    } );
    Handlebars.registerHelper( 'page_link', function ( obj, pageNumber ) {
      obj.pg = pageNumber;
      return JSON.stringify( obj );
    } );
    Handlebars.registerHelper( {
      eq: function ( v1, v2 ) {
        return v1 === v2;
      },
      ne: function ( v1, v2 ) {
        return v1 !== v2;
      },
      lt: function ( v1, v2 ) {
        return v1 < v2;
      },
      gt: function ( v1, v2 ) {
        return v1 > v2;
      },
      lte: function ( v1, v2 ) {
        return v1 <= v2;
      },
      gte: function ( v1, v2 ) {
        return v1 >= v2;
      },
      and: function () {
        return Array.prototype.slice.call( arguments ).every( Boolean );
      },
      or: function () {
        return Array.prototype.slice.call( arguments, 0, -1 ).some( Boolean );
      }
    } );
  };

  pageLoaded = () => {
    this.$holder = $( '.js-envato-elements-content' );
  };

  getDom = ( templateID, templateData ) => {
    if ( 'undefined' !== typeof templateData.categorySlug && document.getElementById( templateID + '--' + templateData.categorySlug ) ) {
      templateID = templateID + '--' + templateData.categorySlug;
    }
    if ( typeof this.templates[ templateID ] === 'undefined' ) {
      if(typeof document.getElementById( templateID ) === 'undefined') {
        console.log( 'Missing template ' + templateID );
        return;
      }
      this.templates[ templateID ] = Handlebars.compile( document.getElementById( templateID ).innerHTML );
    }
    templateData.contentTypeName = 'Template Kit';
    return $( this.templates[ templateID ]( templateData ) );
  };

  renderMainDom = ( templateID, templateData ) => {
    let $dom = this.getDom( templateID, templateData );
    this.setMainDom( $dom );
    return $dom;
  };

  setMainDom = ( $dom ) => {
    this.$holder.empty();
    this.$holder.append( $dom );
    setTimeout( header.setFixedHeader, 100 );
    return $dom;
  };

  pageLoading = () => {
    this.$holder.addClass( 'envato-elements__content-dynamic--loading' );
  };

  pageFinishedLoading = () => {
    this.$holder.removeClass( 'envato-elements__content-dynamic--loading' );
  };

  mainRenderFinished = () => {
    this.pageFinishedLoading();
    // to stop scroll up.
    // this.$holder.css('min-height',this.$holder.height());
    importer.refreshImportList();
    lazyLoader.checkVisibleCallback();
    // setTimeout( header.setFixedHeader, 200 );
    select2( true, $ );
    $( '.js-envato-elements__select2' ).each(function(){
      $(this).select2({
        dropdownParent: $(this).parent()
      });
    });
    header.setSubNavOverflow();
  };
}

export let template = new Template();