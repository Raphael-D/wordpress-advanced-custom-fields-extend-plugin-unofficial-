import { config } from '../utils/config';
import $ from 'jquery';
import { modal } from '../utils/modal';
import { api } from '../utils/api';

class PluginsPage {
  constructor() {
  };

  pageLoaded = () => {
    this.$deactivateButton = $( '#the-list' ).find( '[data-slug="envato-elements"] span.deactivate a' );
    this.$deactivateButton.on( 'click', ( event ) => {
      event.preventDefault();
      // show modal
      modal.closeModal();
      modal.openModal( 'tmpl-envato-elements__plugin-feedback', {
        skip: this.$deactivateButton.attr( 'href' ),
      } );
      return false;
    } );
    $( 'body' ).on( 'click', '.envato-elements__disable-submit', ( event ) => {
      event.preventDefault();
      let $butt = $( '.envato-elements__disable-submit' );
      $butt.width( $butt.width() ).text( 'Loading...' ).prop( 'disabled', true );
      let answer = $( 'input[name=\'elements_deactivation_reason\']:checked' ).val();
      api.post( 'feedback/deactivation', {
        answer: answer,
        answer_text: $( 'input[name=\'elements_deactivation_reason_' + answer + '\']' ).val(),
      } )
        .then(
          ( json ) => {
          },
          ( err ) => {
          }
        )
        .finally( () => {
          window.location.href = this.$deactivateButton.attr( 'href' );
        } );
      return false;
    } );
  };

}

export let plugins = new PluginsPage();

