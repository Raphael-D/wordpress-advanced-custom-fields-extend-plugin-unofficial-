<?php

/**
 * The 404page block editor plugin class
 *
 * @since  9
 */
 
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The block editor plugin class
 */
if ( !class_exists( 'PP_404Page_BlockEditor' ) ) {
  
  class PP_404Page_BlockEditor {
    
    /**
     * reference to core class
     *
     * @since  9
     * @var    object
     * @access private
     */
    private $_core;
    
    
    /**
	   * Initialize the class
     *
     * @since 9
     * @access public
     */
    public function __construct( $_core ) {
      
      $this->_core = $_core;
      
      $this->init();
      
    }
    
    
    /**
	   * Do Init
     *
     * @since 9
     * @access private
     */
    private function init() {

      add_action( 'admin_head', array( $this, 'admin_style' ) );
    
    }
    
    
    /**
	   * Add Block Editor Style to Header if currently edited page is a custom 404 error page
     *
     * @since 9
     * @access public
     */
    public function admin_style() {
      
      if ( $this->is_gutenberg_editing() ) {
      
        ?>
        <style type="text/css">
          .edit-post-layout__content:before { content: "<?php esc_html_e( 'You are currently editing your custom 404 error page', '404page'); ?>"; background-color: #333; color: #FFF; padding: 8px; font-size: 16px; display: block };
        </style>
        <?php
        
      }
      
		}
    
    
    /**
	   * Is the 404 page edited in gutenberg editor?
     *
     * @since 9
     * @access private
     */
    private function is_gutenberg_editing() {
      
      // Is the current screen the page edit screen and is a custom 404 error page defined?
      if ( get_current_screen()->id == 'page' && $this->_core->get_id() > 0 ) {
        
        // Is the block editor active for pages and is the classic editor not loaded?
        if ( function_exists( 'use_block_editor_for_post_type' ) && use_block_editor_for_post_type( 'page' ) && ! isset( $_GET['classic-editor'] ) ) {
        
          global $post;
        
          $all404pages = $this->_core->get_all_page_ids();
        
          // Is the currently edited page a custom 404 error page?
          if ( in_array( $post->ID, $all404pages  ) ) {
      
            return true;
            
          }
          
        }
        
      }
      
      return false;
      
    }

  }
  
}

?>