import { modal } from './modal';
import { template } from './template';

class Error {
  constructor() {
  }

  pageLoaded = () => {
  };

  displayError = ( title, message, reactivate ) => {
    if( reactivate !== false ){
      reactivate = true;
    }
    template.pageFinishedLoading();
    modal.closeModal();
    modal.openModal( 'tmpl-envato-elements__error-modal', {
      title: title,
      message: message,
      reactivate: reactivate,
    } );

  };


}

export let error = new Error();
