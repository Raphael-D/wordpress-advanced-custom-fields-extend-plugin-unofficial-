import $ from 'jquery';
import { api } from './api';

class Notifications {
  constructor() {
  }

  pageLoaded = () => {
    $( 'body' ).on( 'click', '.js-envato-elements__notification-trigger', ( event ) => {
      this.notificationsRead( event && event.target ? $( event.target ).data( 'unseen-notifications' ) : [] );
    } );
  };

  notificationsRead = ( post_ids ) => {
    $( '.js-envato-elements__notification-trigger' ).find( '.envato-elements__header-menu-label' ).remove();
    api.post( 'notifications/read', {
      ids: post_ids
    } )
      .then(
        ( json ) => {
        },
        ( err ) => {
        }
      );
  };

}

export let notifications = new Notifications();
