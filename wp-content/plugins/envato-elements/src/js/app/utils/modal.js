import $ from 'jquery';
import { template } from './template';

class Modal {
  constructor() {
    this.$modalHolder = null;
    this.$modalDom = null;
    this.modalOpen = false;
  }

  pageLoaded = () => {
    $( 'body' ).on( 'click', ( event ) => {
      if ( this.modalOpen && !$( event.target ).parents( '.envato-elements__modal-inner-bg' ).length ) {
        this.closeModal();
      }
    } )
      .on( 'click', '.envato-elements__modal-close', ( event ) => {
        event.preventDefault();
        this.closeModal();
        return false;
      } );
  };

  closeModal = () => {
    this.modalOpen = false;
    this.$modalHolder && this.$modalHolder.empty();
    $( 'body' ).removeClass( 'envato-elements--modal-open' );
  };

  openModal = (templateName, templateData) => {
    this.$modalHolder = $( '.envato-elements__modal-holder' );
    this.$modalDom = template.getDom( templateName, templateData );
    this.$modalHolder.empty();
    this.$modalHolder.append( this.$modalDom );
    this.modalOpen = true;
    $( 'body' ).addClass( 'envato-elements--modal-open' );
  };

}

export let modal = new Modal();
