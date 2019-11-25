export default class ObjectCache {
  constructor( cacheLookup ) {
    this.items = {};
    this.lookup = cacheLookup;
  }

  getItem = ( settings, parent ) => {
    return this.lookup( this.items, settings, parent );
  };

}
