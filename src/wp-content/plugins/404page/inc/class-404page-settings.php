<?php

/**
 * The 404page settings plugin class
 *
 * @since  7
 *
 * taken from 404page core class and outsourced to a seperate class in version 7
 */
 
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The settings plugin class
 */
if ( !class_exists( 'PP_404Page_Settings' ) ) {
  
  class PP_404Page_Settings {
    
  /**
	 * Array of settings
	 *
	 * @since  7
	 * @access protected
	 */
	protected $settings;
    
    /**
	   * Initialize the settings class
     *
     * @since 7
     */
    public function __construct() {
      
      $this->settings = array();
      
      $this->settings['404page_page_id']            = $this->get_option_404page_id();
      $this->settings['404page_hide']               = $this->get_option_404page_hide();
      $this->settings['404page_fire_error']         = $this->get_option_404page_fire_error();
      $this->settings['404page_force_error']        = $this->get_option_404page_force_error();
      $this->settings['404page_no_url_guessing']    = $this->get_option_404page_no_url_guessing();
      $this->settings['404page_http410_if_trashed'] = $this->get_option_404page_http410_if_trashed();
      $this->settings['404page_native']             = false;
      $this->settings['404page_method']             = 'STD';

    }


    /**
     * get setting - id of the 404 page
     *
     * @since  7
     * @access public
     */
    public function get_id() {
      
      return $this->settings['404page_page_id'];
      
    }
    
    
    /**
     * get setting - hide 404 page from page list
     *
     * @since  7
     * @access public
     */
    public function get_hide() {
      
      return $this->settings['404page_hide'];
      
    }
    
    
    /**
     * get setting - fire 404 error
     *
     * @since  7
     * @access public
     */
    public function get_fire_error() {
      
      return $this->settings['404page_fire_error'];
      
    }
    
    
    /**
     * get setting - force 404 error
     *
     * @since  7
     * @access public
     */
    public function get_force_error() {
      
      return $this->settings['404page_force_error'];
      
    }
    
    
    /**
     * get setting - no url guessing
     *
     * @since  7
     * @access public
     */
    public function get_no_url_guessing() {
      
      return $this->settings['404page_no_url_guessing'];
      
    }
    
    
    /**
     * get setting - http 410 if trashed
     *
     * @since  7
     * @access public
     */
    public function get_http410_if_trashed() {
      
      return $this->settings['404page_http410_if_trashed'];
      
    }
    
    
    /**
     * get setting - native support
     *
     * @since  7
     * @access public
     */
    public function get_native() {
      
      return $this->settings['404page_native'];
      
    }
    
    
    /**
     * get setting - method
     *
     * @since  7
     * @access public
     */
    public function get_method() {
      
      return $this->settings['404page_method'];
      
    }
    
    
    /**
     * set setting - native support
     *
     * @since  7
     * @access public
     */
    public function set_native( $active ) {
      
      $this->settings['404page_native'] = ( $active === true );
      
    }
    
    
    /**
     * set the method
     *
     * @since  7
     * @access public
     */
    public function set_method() {
      
      if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
        
        // WPML is active
        $this->settings['404page_method'] = 'CMP';
        
      } else {
        
        $this->settings['404page_method'] = get_option( '404page_method', 'STD' );
        
      }
      
    }
    
    
    /**
     * get the id of the 404 page option
     * returns 0 if none is defined, returns -1 if the defined page id does not exist
     *
     * @since  7
     * @access private
     */
    private function get_option_404page_id() {  
    
      $pageid = get_option( '404page_page_id', 0 );
      
      if ( $pageid != 0 ) {
        
       $page = get_post( $pageid );
        
        if ( !$page || $page->post_status != 'publish' ) {
          
          $pageid = -1;
          
        } 
        
      }
      
      return $pageid;
      
    }
    
    
    /**
     * do we have to hide the selected 404 page from the page list?
     *
     * @since  7
     * @access private
     */
    private function get_option_404page_hide() {
      
      return (bool)get_option( '404page_hide', false );
      
    }
    
    
    /**
     * do we have to fire an 404 error if the selected page is accessed directly?
     *
     * @since  7
     * @access private
     */
    private function get_option_404page_fire_error() {
      
      return (bool)get_option( '404page_fire_error', true );
      
    }
    
    
    /**
     * do we have to force the 404 error after loading the page?
     *
     * @since  7
     * @access private
     */
    private function get_option_404page_force_error() {
      
      return (bool)get_option( '404page_force_error', false );
      
    }
    
    
    /**
     * do we have to disable the URL guessing?
     *
     * @since  7
     * @access private
     */
    private function get_option_404page_no_url_guessing() {
      
      return (bool)get_option( '404page_no_url_guessing', false );
      
    }
    
    
    /**
     * do we have to send an http 410 error in case the object is in trash?
     *
     * @since  7
     * @access private
     */
    private function get_option_404page_http410_if_trashed() {
      
      return (bool)get_option( '404page_http410_if_trashed', false );
      
    }
    
    
    /**
     * get all option names
     *
     * @since  7
     * @access public
     */
    public function get_option_names() {
      
      return array_keys( $this->settings );
      
    }
    
  }
  
}

?>