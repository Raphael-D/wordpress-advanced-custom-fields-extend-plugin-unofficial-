import $ from 'jquery';
import { clearAllBodyScrollLocks, disableBodyScroll } from 'body-scroll-lock';
import { importer } from './importer';

class Scroller {
  constructor() {
    this.scrollHolder = null;
    this.scrollPoint = 0;
  }

  setScrollHolder = ( item ) => {
    this.scrollHolder = item;
  };
  rememberScrollPoint = () => {
    this.scrollPoint = $( this.scrollHolder ).scrollTop();
  };
  restoreScrollPoint = () => {
    $( this.scrollHolder ).scrollTop( this.scrollPoint );
  };
  scrollTo = ( x, y ) => {
    $( this.scrollHolder ).scrollTop( y );
  };

  disableScroll = ( element ) => {
    if ( importer.importMode === 'insert' ) {
      $( this.scrollHolder ).addClass( '--locked' );
    } else {
      disableBodyScroll( element );
    }
  };

  enableScroll = () => {
    if ( importer.importMode === 'insert' ) {
      $( this.scrollHolder ).removeClass( '--locked' );
    } else {
      clearAllBodyScrollLocks();
    }
  };

}

export let scroller = new Scroller();
