class Config {
  constructor() {

    this.config = envato_elements_admin;
    this.stateData = {};

  }

  get = ( key ) => {
    return 'undefined' !== typeof this.config[ key ] ? this.config[ key ] : false;
  };

  state = ( key, value ) => {
    if ( 'undefined' !== typeof value ) {
      this.stateData[ key ] = value;
      return value;
    } else {
      return 'undefined' !== typeof this.stateData[ key ] ? this.stateData[ key ] : false;
    }
  };
}

export let config = new Config();
