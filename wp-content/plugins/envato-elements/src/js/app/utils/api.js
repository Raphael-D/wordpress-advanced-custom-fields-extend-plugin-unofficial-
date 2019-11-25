import $ from 'jquery';
import { config } from './config';

class API {
  constructor() {
    this.localCache = {};
  }

  post = ( endpoint, args ) => {

    return new Promise( ( resolve, reject ) => {

      if ( !args ) {
        args = {};
      }
      args[ '_wpnonce' ] = config.get( 'api_nonce' );

      $.ajax( {
        url: config.get( 'api_url' ) + endpoint,
        method: 'POST',
        dataType: 'json',
        data: args,
      } ).done( ( json ) => {
        if ( json && 'undefined' !== typeof json.success && 'undefined' === typeof json.error && !json.success ) {
          json.error = true;
        }
        resolve( json );
      } ).fail( ( jqXHR, textStatus, errorThrown ) => {
        let response = {};
        try {
          response = JSON.parse( jqXHR.responseText );
        } catch ( e ) {

        }
        if ( Object.keys( response ).length === 0 ) {
          response = {
            error: 'Sorry something went wrong. ' + jqXHR.responseText
          };
        }
        reject( response );
      } ).always( () => {} );

    } );
  };

}

export let api = new API();
