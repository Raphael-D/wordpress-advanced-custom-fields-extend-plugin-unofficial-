<div class="wrap pp-admin-page-wrapper" id="pp-404page-settings">
  <h1>
    <span><?php echo esc_html( $this->_core->get_plugin_name() ); ?></span>
    <nav>
      <?php $this->show_nav_icons( array(
        array( 
          'link'  => 'https://wordpress.org/support/plugin/' . $this->_core->get_plugin_slug() . '/reviews/',
          'title' => __( 'Please rate Plugin', '404page' ),
          'icon'  => 'dashicons-star-filled'
        ),
        array( 
          'link'  => 'https://wordpress.org/plugins/' . $this->_core->get_plugin_slug(),
          'title' => __( 'WordPress.org Plugin Page', '404page' ),
          'icon'  => 'dashicons-wordpress'
        ),
        array( 
          'link'  => 'https://petersplugins.com/docs/' . $this->_core->get_plugin_slug(),
          'title' => __( 'Plugin Doc', '404page' ),
          'icon'  => 'dashicons-book-alt'
        ),
        array( 
          'link'  => 'https://wordpress.org/support/plugin/' . $this->_core->get_plugin_slug(),
          'title' => __( 'Support', '404page' ),
          'icon'  => 'dashicons-editor-help'
        ),
        array( 
          'link'  => 'https://petersplugins.com/',
          'title' => __( 'Authors Website', '404page' ),
          'icon'  => 'dashicons-admin-home'
        ),
        array( 
          'link'  => 'https://www.facebook.com/petersplugins/',
          'title' => __( 'Authors Facebook Page', '404page' ),
          'icon'  => 'dashicons-facebook-alt'
        )
        
      ) ); ?>
    </nav>
  </h1>
    <?php settings_errors(); ?>
    
    <div class="postbox">
      <div class="inside">
                
        <form method="POST" action="options.php">
                      
          <h2><?php esc_html_e( 'General', '404page' ); ?></h2>
          <?php settings_fields( '404page_settings' ); ?>
          <?php do_settings_sections( '404page_settings_section' ); ?>
          <div id="pp-settings-advanced">
            <h2><?php esc_html_e( 'Advanced', '404page' ); ?></h2>
            <?php do_settings_sections( '404page_settings_section_advanced' ); ?>
          </div>
          <?php submit_button(); ?>
          
        </form>
            
      </div>
    </div>
    
    <div class="postbox">
      <div class="inside">
        <div id="pp-404page-videos">
            <h2><?php esc_html_e( 'Watch the Explainer Videos', '404page' ); ?></h2>
            <?php $this->show_videos(); ?>
          </div>
      </div>
    </div>
</div>