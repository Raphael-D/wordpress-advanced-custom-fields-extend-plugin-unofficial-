<?php

/**
 * The 404page Plugin Loader
 *
 * @since 7
 *
 **/
 
// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Load files
 */
require_once( plugin_dir_path( __FILE__ ) . '/inc/class-404page.php' );
require_once( plugin_dir_path( __FILE__ ) . '/inc/class-404page-settings.php' );

if ( is_admin() ) {
  
  // load files only if in admin
  // @since 10

  require_once( plugin_dir_path( __FILE__ ) . '/inc/class-404page-admin.php' );
  require_once( plugin_dir_path( __FILE__ ) . '/inc/class-404page-block-editor.php' );
  require_once( plugin_dir_path( __FILE__ ) . '/inc/class-404page-classic-editor.php' );
  
}


/**
 * Main Function
 */
function pp_404page() {

  return PP_404Page::getInstance( array(
    'file'    => dirname( __FILE__ ) . '/404page.php',
    'slug'    => pathinfo( dirname( __FILE__ ) . '/404page.php', PATHINFO_FILENAME ),
    'name'    => '404page - your smart custom 404 error page',
    'version' => '10.5'
  ) );
    
}



/**
 * Run the plugin
 */
pp_404page();


?>