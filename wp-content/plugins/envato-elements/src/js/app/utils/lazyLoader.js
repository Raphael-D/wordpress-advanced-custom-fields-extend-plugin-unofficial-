import $ from 'jquery';

class LazyLoader {
  constructor() {
    this.lazyObjects = [];
    document.addEventListener( 'resize', this.checkVisible, true );
    document.addEventListener( 'scroll', this.checkVisible, true );
  };

  throttle = ( callback, limit ) => {
    var wait = false;                  // Initially, we're not waiting
    return function () {               // We return a throttled function
      if ( !wait ) {                   // If we're not waiting
        callback.call();           // Execute users function
        wait = true;               // Prevent future invocations
        setTimeout( function () {   // After a period of time
          wait = false;          // And allow future invocations
          callback.call();           // Execute users function
        }, limit );
      }
    };
  };

  addLazy = ( object ) => {
    this.lazyObjects.push( object );
  };

  checkVisibleCallback = () => {

    const pageTop = $( window ).scrollTop();
    const pageBottom = pageTop + $( window ).height();
    const pageWidth = $( window ).width();

    for ( var i in this.lazyObjects ) {
      if ( this.lazyObjects.hasOwnProperty( i ) ) {
        if ( !this.lazyObjects[ i ] || !document.body.contains( this.lazyObjects[ i ].get( 0 ) ) ) {
          delete(this.lazyObjects[ i ]);
        } else if ( this.lazyObjects[ i ].hasClass( '--lazyloaded' ) ) {
          delete(this.lazyObjects[ i ]);
        } else if ( this.lazyObjects[ i ] ) {
          const elementTop = this.lazyObjects[ i ].offset().top;
          const elementLeft = this.lazyObjects[ i ].offset().left;
          const elementRight = elementLeft + this.lazyObjects[ i ].width();
          const elementBottom = elementTop + this.lazyObjects[ i ].height();
          if (
            elementTop <= pageBottom &&
            elementBottom >= pageTop &&
            elementRight > 0 &&
            elementLeft < pageWidth
          ) {
            this.lazyObjects[ i ].find( '[data-src]' ).each( function () {
              $( this ).css( 'background-image', 'url("' + $( this ).data( 'src' ) + '")' ).addClass( '--lazyloaded' );
            } );
            delete(this.lazyObjects[ i ]);
          }
        }
      }
    }
  };

  checkVisible = this.throttle( this.checkVisibleCallback, 150 );

}

export let lazyLoader = new LazyLoader();