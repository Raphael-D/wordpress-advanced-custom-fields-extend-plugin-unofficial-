import { modal } from './modal';

class Helper {
  constructor() {
  }

  pageLoaded = () => {
  };

  navigationChange = ( admin, query, action ) => {
    if ( query && query.navType && 'help-modal' === query.navType ) {
      this.loadHelpModal();
    }
  };

  loadHelpModal = () => {
    modal.openModal( 'tmpl-envato-elements__help-modal', {} );
  };

}

export let helper = new Helper();
