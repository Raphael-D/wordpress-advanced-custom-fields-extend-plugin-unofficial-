<?php

/**
 * The 404page admin plugin class
 *
 * @since  10
 */
 
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin plugin class
 */
if ( !class_exists( 'PP_404Page_Admin' ) ) {
  
  class PP_404Page_Admin {
    
    /**
     * reference to core class
     *
     * @since  10
     * @var    object
     * @access private
     */
    private $_core;
    
    
    /**
     * reference to settings class
     *
     * @since  10
     * @var    object
     * @access private
     */
    private $_settings;
    
    
    /**
     * admin handle
     *
     * @since  10
     * @var    object
     * @access private
     */
    private $admin_handle;
    
    
    /**
	   * Initialize the class
     *
     * @since 10
     * @access public
     */
    public function __construct( $_core, $_settings ) {
      
      $this->_core = $_core;
      $this->_settings = $_settings;
      
      $this->init();
      
    }
    
    
    /**
	   * Do Init
     *
     * @since 10
     * @access private
     */
    private function init() {

      add_action( 'admin_init', array( $this, 'admin_init' ) );
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );
      add_action( 'admin_head', array( $this, 'admin_style' ) );
      add_filter( 'plugin_action_links_' . plugin_basename( $this->_core->get_plugin_file() ), array( $this, 'add_settings_links' ) ); 
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );
      add_action( 'admin_notices', array( $this, 'admin_notices' ) );
      add_action( 'wp_ajax_pp_404page_dismiss_admin_notice', array( $this, 'dismiss_admin_notice' ) );
    
    }
    
    
    /**
     * init admin 
     * moved to PP_404Page_Admin in v 10
     */
    function admin_init() {
      
      $this->_settings->set_method();
      
      
      add_settings_section( '404page-settings', null, null, '404page_settings_section' );
      add_settings_section( '404page-settings', null, null, '404page_settings_section_advanced' );
      register_setting( '404page_settings', '404page_page_id' );
      register_setting( '404page_settings', '404page_hide' );
      register_setting( '404page_settings', '404page_method', array( $this, 'handle_method' ) );
      register_setting( '404page_settings', '404page_fire_error' );
      register_setting( '404page_settings', '404page_force_error' );
      register_setting( '404page_settings', '404page_no_url_guessing' );
      register_setting( '404page_settings', '404page_http410_if_trashed' );
      add_settings_field( '404page_settings_404page', esc_html__( 'Page to be displayed as 404 page', '404page' ) . '&nbsp;<a class="dashicons dashicons-editor-help" href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#settings_select_page' ) . '"></a>' , array( $this, 'admin_404page' ), '404page_settings_section', '404page-settings', array( 'label_for' => '404page_page_id' ) );
      add_settings_field( '404page_settings_hide', '' , array( $this, 'admin_hide' ), '404page_settings_section_advanced', '404page-settings', array( 'label_for' => '404page_hide' ) );
      add_settings_field( '404page_settings_fire', '' , array( $this, 'admin_fire404' ), '404page_settings_section_advanced', '404page-settings', array( 'label_for' => '404page_fire_error' ) );
      add_settings_field( '404page_settings_force', '' , array( $this, 'admin_force404' ), '404page_settings_section_advanced', '404page-settings', array( 'label_for' => '404page_force_error' ) );
      add_settings_field( '404page_settings_noguess', '' , array( $this, 'admin_noguess' ), '404page_settings_section_advanced', '404page-settings', array( 'label_for' => '404page_no_url_guessing' ) );
      add_settings_field( '404page_settings_http410', '' , array( $this, 'admin_http410' ), '404page_settings_section_advanced', '404page-settings', array( 'label_for' => '404page_http410_if_trashed' ) );
      add_settings_field( '404page_settings_method', '', array( $this, 'admin_method' ), '404page_settings_section_advanced', '404page-settings', array( 'label_for' => '404page_method' ) );
    }
    
    
    /**
     * handle the method setting
     * moved to PP_404Page_Admin in v 10
     */
    function handle_method( $method ) {
      
      if ( null === $method ) {
        
        $method = 'STD';
        
      }
      
      return $method;
      
    }
    
    
    /**
     * handle the settings field hide
     * moved to PP_404Page_Admin in v 10
     */
    function admin_hide() {
      
      echo '<p class="toggle"><input type="checkbox" id="404page_hide" name="404page_hide" value="1"' . checked( true, $this->_settings->get_hide(), false ) . '/>';
      echo '<label for="404page_hide" class="check"></label>' . esc_html__( 'Hide the selected page from the Pages list', '404page' ) . '&nbsp;<a class="dashicons dashicons-editor-help" href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#settings_hide_page' ) . '"></a><br />';
      echo '<span class="dashicons dashicons-info"></span>&nbsp;' . esc_html__( 'For Administrators the page is always visible.', '404page' ) . '</p><div class="clear"></div>';
      
    }
    
    
    /**
     * handle the settings field fire 404 error
     * moved to PP_404Page_Admin in v 10
     */
    function admin_fire404() {
      
      echo '<p class="toggle"><input type="checkbox" id="404page_fire_error" name="404page_fire_error" value="1"' . checked( true, $this->_settings->get_fire_error(), false ) . '/>';
      echo '<label for="404page_fire_error" class="check"></label>' . esc_html__( 'Send an 404 error if the page is accessed directly by its URL', '404page' ) . '&nbsp;<a class="dashicons dashicons-editor-help" href="' . esc_url ( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#settings_fire_404' ) . '"></a><br />';
      echo '<span class="dashicons dashicons-info"></span>&nbsp;' . esc_html__( 'Uncheck this if you want the selected page to be accessible.', '404page' );
      
      if ( function_exists( 'wpsupercache_activate' ) ) {
        
        echo '<br /><span class="dashicons dashicons-warning"></span>&nbsp;<strong>' . esc_html__( 'WP Super Cache Plugin detected', '404page' ) . '</strong>. ' . __ ( 'If the page you selected as 404 error page is in cache, always a HTTP code 200 is sent. To avoid this and send a HTTP code 404 you have to exlcude this page from caching', '404page' ) . ' (<a href="' . admin_url( 'options-general.php?page=wpsupercache&tab=settings#rejecturi' ) . '">' . esc_html__( 'Click here', '404page' ) . '</a>).<br />(<a href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#wp_super_cache' ) . '">' . esc_html__( 'Read more', '404page' ) . '</a>)';
        
      }
      
      echo '</p><div class="clear"></div>';
      
    }
    
    
    /**
     * handle the settings field to force an 404 error
     * moved to PP_404Page_Admin in v 10
     */
    function admin_force404() {
      
      echo '<p class="toggle"><input type="checkbox" id="404page_force_error" name="404page_force_error" value="1"' . checked( true, $this->_settings->get_force_error(), false ) . '/>';
      echo '<label for="404page_force_error" class="check warning"></label>' . esc_html__( 'Force 404 error after loading page', '404page' ) . '&nbsp;<a class="dashicons dashicons-editor-help" href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#settings_force_404' ) .'"></a>&nbsp;<a class="dashicons dashicons-video-alt3" href="https://youtu.be/09OOCbFLfnI" data-lity></a><br />';
      echo '<span class="dashicons dashicons-warning"></span>&nbsp;' . esc_html__( 'Generally this is not needed. It is not recommended to activate this option, unless it is necessary. Please note that this may cause problems with your theme.', '404page' ) . '</p><div class="clear"></div>';
      
    }
    
    
    /**
     * handle the settings field to stop URL guessing
     * moved to PP_404Page_Admin in v 10
     */
    function admin_noguess() {
      
      echo '<p class="toggle"><input type="checkbox" id="404page_no_url_guessing" name="404page_no_url_guessing" value="1"' . checked( true, $this->_settings->get_no_url_guessing(), false ) . '/>';
      echo '<label for="404page_no_url_guessing" class="check warning"></label>' . esc_html__( 'Disable URL autocorrection guessing', '404page' ) . '&nbsp;<a class="dashicons dashicons-editor-help" href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#settings_stop_guessing' ) . '"></a>&nbsp;<a class="dashicons dashicons-video-alt3" href="https://youtu.be/H0EdtFcAGl4" data-lity></a><br />';
      echo '<span class="dashicons dashicons-warning"></span>&nbsp;' . esc_html__( 'This stops WordPress from URL autocorrection guessing. Only activate, if you are sure about the consequences.', '404page' ) . '</p><div class="clear"></div>';
    
    }
    
    
    /**
     * handle the settings field to send an http 410 error in case the object is trashed
     * @since 3.2
     * moved to PP_404Page_Admin in v 10
     */
    function admin_http410() {
      
      echo '<p class="toggle"><input type="checkbox" id="404page_http410_if_trashed" name="404page_http410_if_trashed" value="1"' . checked( true, $this->_settings->get_http410_if_trashed(), false ) . '/>';
      echo '<label for="404page_http410_if_trashed" class="check"></label>' . esc_html__( 'Send an HTTP 410 error instead of HTTP 404 in case the requested object is in trash', '404page' ) . '&nbsp;<a class="dashicons dashicons-editor-help" href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#settings_maybe_send_http410' ) .'"></a>&nbsp;<a class="dashicons dashicons-video-alt3" href="https://youtu.be/O5xPM0BMZxM" data-lity></a><br />';
      echo '<span class="dashicons dashicons-info"></span>&nbsp;' . esc_html__( 'Check this if you want to inform search engines that the resource requested is no longer available and will not be available again so it can be removed from the search index immediately.', '404page' );
    
    }
    
    
    /**
     * handle the settings field method
     * moved to PP_404Page_Admin in v 10
     */
    function admin_method() {

      if ( $this->_settings->get_native() || defined( 'CUSTOMIZR_VER' ) || defined( 'ICL_SITEPRESS_VERSION' ) ) {
        
        $dis = ' disabled="disabled"';
        
      } else {
        
        $dis = '';
      }
      
      echo '<p class="toggle"><input type="checkbox" id="404page_method" name="404page_method" value="CMP"' . checked( 'CMP', $this->_settings->get_method(), false ) . $dis . '/>';
      echo '<label for="404page_method" class="check"></label>' . esc_html__( 'Activate Compatibility Mode', '404page' ) . '&nbsp;<a class="dashicons dashicons-editor-help" href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#settings_operating_method' ) . '"></a>&nbsp;<a class="dashicons dashicons-video-alt3" href="https://youtu.be/wqSepDyQeqY" data-lity></a><br />';
      echo '<span class="dashicons dashicons-info"></span>&nbsp;';
      
      if ( $this->_settings->get_native() ) {
        
        esc_html_e( 'This setting is not available because the Theme you are using natively supports the 404page plugin.', '404page' );
        echo ' (<a href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#native_mode' ) . '">' . esc_html__( 'Read more', '404page' ) . '</a>)';
      
      } elseif ( defined( 'CUSTOMIZR_VER' ) ) {
      
        esc_html_e( 'This setting is not availbe because the 404page Plugin works in Customizr Compatibility Mode.', '404page' );
        echo ' (<a href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#special_modes' ) .'">' . esc_html__( 'Read more', '404page' ) . '</a>)';
      
      } elseif ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
      
        esc_html_e( 'This setting is not availbe because the 404page Plugin works in WPML Mode.', '404page' );
        echo ' (<a href="' . esc_url( 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug() . '/#special_modes' ) . '">' . esc_html__( 'Read more', '404page' ) . '</a>)';
        
      } else {
                
        esc_html_e( 'If you are using a theme or plugin that modifies the WordPress Template System, the 404page plugin may not work properly. Compatibility Mode maybe can fix the problem. Activate Compatibility Mode only if you have any problems.', '404page' );
     
      }
      
      echo '</p><div class="clear"></div>';

    }
    
    
    /**
     * create the menu entry
     * moved to PP_404Page_Admin in v 10
     */
    function admin_menu() {
      $this->admin_handle = add_theme_page ( esc_html__( '404 Error Page', "404page" ), esc_html__( '404 Error Page', '404page' ), 'manage_options', '404pagesettings', array( $this, 'show_admin' ) );
    }
    
    
    /**
     * add admin css to header
     * moved to PP_404Page_Admin in v 10
     */
    function admin_style() {
      
      if ( $this->_core->get_id() > 0 ) {
        
        echo '<style type="text/css">';
        
        foreach ( $this->_core->get_all_page_ids() as $pid ) {
          
          echo '#the-list #post-' . $pid . ' .column-title .row-title:before { content: "404"; background-color: #333; color: #FFF; display: inline-block; padding: 0 5px; margin-right: 10px; }';
          
        }
        
        echo '</style>';
        
      }
      
    }
    
    
    /**
     * handle the settings field page id
     * moved to PP_404Page_Admin in v 10
     */
    function admin_404page() {
      
      if ( $this->_core->get_id() < 0 ) {
        
        echo '<div class="error form-invalid" style="line-height: 3em">' . esc_html__( 'The page you have selected as 404 page does not exist anymore. Please choose another page.', '404page' ) . '</div>';
      }
      
      wp_dropdown_pages( array( 'name' => '404page_page_id', 'id' => 'select404page', 'echo' => 1, 'show_option_none' => esc_html__( '&mdash; NONE (WP default 404 page) &mdash;', '404page'), 'option_none_value' => '0', 'selected' => $this->_core->get_id() ) );
      
      echo '<div id="404page_edit_link" style="display: none">' . get_edit_post_link( $this->_core->get_id() )  . '</div>';
      echo '<div id="404page_test_link" style="display: none">' . get_site_url() . '/404page-test-' . md5( rand() ) . '</div>';
      echo '<div id="404page_current_value" style="display: none">' . $this->_core->get_id() . '</div>';
      echo '<p class="submit"><input type="button" name="edit_404_page" id="edit_404_page" class="button secondary" value="' . esc_html__( 'Edit Page', '404page' ) . '" />&nbsp;<input type="button" name="test_404_page" id="test_404_page" class="button secondary" value="' . esc_html__( 'Test 404 error', '404page' ) . '" /></p>';
      
    }
    
    
    /**
     * add admin css file
     * moved to PP_404Page_Admin in v 10
     */
    function admin_css() {
      
      if ( get_current_screen()->id == $this->admin_handle ) {
        
        wp_enqueue_style( '404pagelity', $this->_core->get_asset_file( 'css', 'lity.min.css' ) );
        wp_enqueue_style( 'pp-admin-page', $this->_core->get_asset_file( 'css', 'pp-admin-page-v2.css' ) );
        wp_enqueue_style( '404pagecss', $this->_core->get_asset_file( 'css', '404page-ui.css' ) );
        
      }
      
    }
    
    
    /**
     * add admin js files
     * moved to PP_404Page_Admin in v 10
     */
    function admin_js() {
    
      wp_enqueue_script( '404pagejs', $this->_core->get_asset_file( 'js', '404page.js' ), 'jquery', $this->_core->get_plugin_version(), true );
      
      // since 10.4
      wp_localize_script( '404pagejs', 'pp_404page_security', array( 'securekey' => $this->get_nonce() ) );
      
      if ( get_current_screen()->id == $this->admin_handle ) {
        
        wp_enqueue_script( '404page-ui', $this->_core->get_asset_file( 'js', '404page-ui.js' ), 'jquery', $this->_core->get_plugin_version(), true );
        wp_enqueue_script( '404page-lity', $this->_core->get_asset_file( 'js', 'lity.min.js' ), 'jquery', $this->_core->get_plugin_version(), true );
      
      }
      
    }
   
   
    /**
     * show admin page
     * moved to PP_404Page_Admin in v 10
     */
    function show_admin() {
      
      if ( !current_user_can( 'manage_options' ) )  {
        
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
        
      }
      
      require_once( plugin_dir_path( $this->_core->get_plugin_file() ) . '/inc/admin/404page-admin-page.php' );
      
    }
    
    
    /**
     * show the nav icons
     * @since 6
     * moved to PP_404Page_Admin in v 10
     */
    function show_nav_icons( $icons ) {
       
      foreach ( $icons as $icon ) {
         
        echo '<a href="' . esc_url( $icon['link'] ) . '" title="' . $icon['title'] . '"><span class="dashicons ' . $icon['icon'] . '"></span><span class="text">' . $icon['title'] . '</span></a>';
         
      }
      
    }
    
    
    /**
     * show admin notices
     * moved to PP_404Page_Admin in v 10
     */
    function admin_notices() {
      
      // @since 8
      // show update notice
      
      /**
       * no notice in version 10 
       *
       *
       * if ( current_user_can( 'manage_options' ) && get_user_meta( get_current_user_id(), 'pp-404page-update-notice-v9', true ) != 'dismissed' ) {
       *   ?>
       *   <div class="notice is-dismissible pp-404page-admin-notice" id="pp-404page-update-notice-v9">
       *     <p><img src="<?php echo $this->_core->get_asset_file( 'img', '/pluginicon.png' ); ?>" style="width: 48px; height: 48px; float: left; margin-right: 20px" /><strong><?php esc_html_e( 'What\'s new in Version 9?', '404page' ); ?></strong><br /><?php esc_html_e( 'Display a note in Block Editor Gutenberg if the currently edited page is the custom 404 error page.', '404page' ); ?><br />[<a href="https://wordpress.org/plugins/404page/#developers"><?php esc_html_e( 'Changelog', '404page' ); ?></a>]<div class="clear"></div></p>
       *   </div>
       *   <?php
       * }
       *
       */
      
      // invite to follow me
      if ( current_user_can( 'manage_options' ) && get_user_meta( get_current_user_id(), 'pp-404page-admin-notice-1', true ) != 'dismissed' ) {
        ?>
        <div class="notice is-dismissible pp-404page-admin-notice" id="pp-404page-admin-notice-1">
          <p><img src="<?php echo $this->_core->get_asset_file( 'img', '/pluginicon.png' ); ?>" style="width: 48px; height: 48px; float: left; margin-right: 20px" /><strong><?php esc_html_e( 'Do you like the 404page plugin?', '404page' ); ?></strong><br /><?php esc_html_e( 'Follow me:', '404page' ); ?> <a class="dashicons dashicons-facebook-alt" href="https://www.facebook.com/petersplugins" title="<?php esc_html_e( 'Authors facebook Page', '404page' ); ?>"></a><div class="clear"></div></p>
        </div>
        <?php
      }
      
      // ask for rating
      // in 30 days at the earliest
      if ( ! get_option( 'pp-404page-admin-notice-2-start' ) ) {
        update_option( 'pp-404page-admin-notice-2-start', time() + 30 * 24 * 60 * 60 );
      }
      if ( get_option( 'pp-404page-admin-notice-2-start' ) <= time() ) {
        if ( current_user_can( 'manage_options' ) && get_user_meta( get_current_user_id(), 'pp-404page-admin-notice-2', true ) != 'dismissed' ) {
          ?>
          <div class="notice is-dismissible pp-404page-admin-notice" id="pp-404page-admin-notice-2">
            <p><img src="<?php echo $this->_core->get_asset_file( 'img', 'pluginicon.png' ); ?>" style="width: 48px; height: 48px; float: left; margin-right: 20px" /><?php esc_html_e( 'If you like the 404page plugin please support my work with giving it a good rating so that other users know it is helpful for you. Thanks.', '404page' ); ?><br /><a href="<?php echo esc_url( 'https://wordpress.org/support/plugin/' . $this->_core->get_plugin_slug() . '/reviews/#new-post' ); ?>" title="<?php esc_html_e( 'Please rate plugin', '404page' ); ?>"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a><div class="clear"></div></p>
          </div>
          <?php
        }
      }
            
    }
    
    
    /**
     * dismiss an admin notice
     * moved to PP_404Page_Admin in v 10
     */
    function dismiss_admin_notice() {
      
      // since 10.4 check nonce
      if ( $this->check_nonce() ) {
      
        if ( isset( $_POST['pp_404page_dismiss_admin_notice'] ) ) {
          
          // since 10.4 check value
          if (strpos( $_POST['pp_404page_dismiss_admin_notice'], 'pp-404page-admin-notice-') === 0 ) {
        
            update_user_meta( get_current_user_id(), sanitize_key( $_POST['pp_404page_dismiss_admin_notice'] ), 'dismissed' );
          
          }
          
        }
        
      }
      
      wp_die();
      
    }
    
    
    /**
     * add links to plugins table
     * moved to PP_404Page_Admin in v 10
     */
    function add_settings_links( $links ) {
      
      return array_merge( $links, array( '<a href="' . admin_url( 'themes.php?page=404pagesettings' ) . '" title="' . esc_html__( 'Settings', '404page' ) . '">' . esc_html__( 'Settings', '404page' ) . '</a>', '<a href="' . esc_url( 'https://wordpress.org/support/plugin/' . $this->_core->get_plugin_slug() . '/reviews/' ) . '" title="' . esc_html__( 'Please rate plugin', '404page' ) . '">' . esc_html__( 'Please rate plugin', '404page' ) . '</a>' ) );
      
    }
    
    
    /**
     * show the videos
     *
     * @since  7
     * @access private
     *
     * moved to PP_404Page_Admin in v 10
     */
    private function show_videos() {
     
      $videos = array(
        array( 'id' => 'HygoFMwdIuY', 'title' => 'A brief introduction', 'img' => '404page-brief-intro' ),
        array( 'id' => '9rL9LbYiSJk', 'title' => 'A quick Overview over the Advanced Settings', 'img' => '404page-advanced-settings-quick-overview' ),
        array( 'id' => '09OOCbFLfnI', 'title' => 'The Advanced Setting "Force 404 error after loading page" explained', 'img' => '404page_advanced_force_404' ),
        array( 'id' => 'H0EdtFcAGl4', 'title' => 'The Advanced Setting "Disable URL Autocorrecton Guessing" explained', 'img' => '404page_advanced_url_guessing' ),
        array( 'id' => 'O5xPM0BMZxM', 'title' => 'Send HTTP Status Code 410 for trashed objects', 'img' => '404page_advanced_410_trashed_objects' ),
        array( 'id' => 'wqSepDyQeqY', 'title' => 'Compatibility Mode explained', 'img' => '404page_advanced_compatibility_mode' )
      );
      
      foreach( $videos as $video ) {
        
        echo '<a href="' . esc_url( 'https://youtu.be/' . $video['id'] ) . '" title="' . $video['title'] . '" data-lity><div><img src="' . $this->_core->get_asset_file( 'img/videos', $video['img'] . '.png' ) . '" title="' . $video['title'] . '" alt="' . $video['title'] . '"></div></a>';
        
      }
     
    }
    
    
    /**
     * create nonce
     *
     * @since  10.4
     * @access private
     * @return string Nonce
     */
    private function get_nonce() {
      
      return wp_create_nonce( 'pp_404page_dismiss_admin_notice' );
      
    }
    
    
    /**
     * check nonce
     *
     * @since  10.4
     * @access private
     * @return boolean
     */
    private function check_nonce() {
      
      return check_ajax_referer( 'pp_404page_dismiss_admin_notice', 'securekey', false );
      
    }

  }
  
}

?>