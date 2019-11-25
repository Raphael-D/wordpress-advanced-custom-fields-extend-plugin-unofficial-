import $ from 'jquery';
import { config } from '../utils/config';

class Header {
  constructor() {
    this.$fixedHeader = null;
  };

  pageLoaded = () => {
    this.$fixedHeader = $( '.envato-elements__wrapper--fixed' );
    this.setFixedHeader();
    this.setSubNavOverflow();
    $( window ).resize( () => {
      this.setFixedHeader();
      this.setSubNavOverflow();
    } );
  };

  setFixedHeader = () => {
    if ( this.$fixedHeader && this.$fixedHeader.length ) {
      this.$fixedHeader.find( '.envato-elements__header' )
        .width( this.$fixedHeader.width() )
        .addClass( 'envato-elements__header--set' );
    }
  };

  setSubNavOverflow = () => {
    let $subnav = $( '.envato-elements__collections-header__subnav' );
    if ( $subnav.length ) {
      let hasmore = false;
      let maxInnerWidth = $subnav.width() - 300;
      let childWidth = 0;
      let moreCounter = 0;
      let $moreHolder = $( '.envato-elements__collections-header__subnav-more-item .envato-elements__chktoggle-content-inner' );
      $moreHolder.empty();
      $subnav.find( '.envato-elements__collections-header__subnav-link--hidden' ).removeClass( 'envato-elements__collections-header__subnav-link--hidden' );
      $subnav.children().width( function ( i, w ) { childWidth += w; } );
      $subnav.removeClass( 'envato-elements__collections-header__subnav--hasmore' );
      $( $subnav.find( '.envato-elements__collections-header__subnav-link' ).get().reverse() ).each( function () {
        if ( childWidth >= maxInnerWidth && !$( this ).hasClass( 'envato-elements__collections-header__subnav-link--current' ) && !$( this ).hasClass( 'envato-elements__collections-header__subnav-link--hidden' ) ) {
          // we can safely shift this item over to the 'more' block.
          childWidth -= $( this ).width();
          $moreHolder.append( $( this ).clone() );
          $( this ).addClass( 'envato-elements__collections-header__subnav-link--hidden' );
          moreCounter++;
        }
      } );

      if ( moreCounter > 0 ) {
        $subnav.addClass( 'envato-elements__collections-header__subnav--hasmore' );
      }
    }
  };

  navigationChange = ( admin, query, action ) => {
    if ( query && query.categorySlug ) {
      $( '.envato-elements__header-menulink--current' ).removeClass( 'envato-elements__header-menulink--current' );
      $( '.envato-elements__header-menulink' ).each( function () {
        if ( $( this ).data( 'category-slug' ) == query.categorySlug || ($( this ).data( 'category-slugs' ) && $.inArray( query.categorySlug, $( this ).data( 'category-slugs' ) ) > 0) ) {
          $( this ).addClass( 'envato-elements__header-menulink--current' );
        }
      } );
    }
  };

}

export let header = new Header();