<?php
/*
Plugin Name: Delete Custom Fields
Description: Ever have one erroneously entered custom field name confuse all of your users and you just can't figure out how to get rid of it? Delete Custom Fields will let you delete every instance of a custom field from your site. 
Version: 0.3.1
License: GPL version 2 or any later version
Author: Sam Margulies
Author URI: http://belabor.org/

Copyright 2011  Sam Margulies  (email : sam@belabor.org)

***
Progress display and ajax functionality heavily influenced by Viper007Bond's awesome Regenerate Thumbnails plugin.

*/

class Delete_Custom_Fields {

	static $instance;
	var $menu_id;
	
	function __construct() {
		self::$instance =& $this;
		// add page to tools menu 
		add_action('admin_menu', 					array( &$this, 'add_menu') );
		add_filter('plugin_action_links', 			array( &$this, 'plugin_action_links'), 10, 2);
		add_action( 'wp_ajax_deletecustomfield',  	array( &$this, 'ajax_delete_field' ) );
		add_action( 'admin_enqueue_scripts',		array( &$this, 'admin_enqueues' ) );

	}
	
	function add_menu() {
		$this->menu_id = add_management_page( 'Delete Custom Fields', 'Delete Custom Fields', 'manage_options', 'delete-custom-fields', array( &$this, 'admin_page' ) );
	}
	
	// Enqueue the needed Javascript and CSS
	function admin_enqueues( $hook_suffix ) {
		if ( $hook_suffix != $this->menu_id )
			return;

		// WordPress 3.1 vs older version compatibility
		if ( wp_script_is( 'jquery-ui-widget', 'registered' ) )
			wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'js/jquery.ui.progressbar.min.js', __FILE__ ), array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.8.6' );
		else
			wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'js/jquery.ui.progressbar.min.1.7.2.js', __FILE__ ), array( 'jquery-ui-core' ), '1.7.2' );

		wp_enqueue_style( 'delete-custom-fields-styles', plugins_url( 'css/delete-custom-fields.css', __FILE__ ), array(), '1.7.2' );
	}
	
	function get_all_meta_keys( $include_hidden = false ) {
		global $wpdb;		
		$limit = 100;
		$include_hidden = ($include_hidden) ? "" : "HAVING meta_key NOT LIKE '\_%'";
		$keys = $wpdb->get_col( "
				SELECT meta_key
				FROM $wpdb->postmeta
				GROUP BY meta_key
				$include_hidden
				ORDER BY meta_key
				LIMIT $limit" );
		return $keys;
	}
	
	function get_all_posts_for_meta_key( $key ) {
	
		@ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
		
		$custom_value_query = new WP_Query( array(
			'post_type' => 'any',
			'nopaging' => true,
			'ignore_sticky_posts' => true,
			'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
			'meta_query' => array(
				array(
					'key' => $key
				)
			)
		) );
		
		if( ! $custom_value_query->have_posts() ) { return false; }
		
		$output = array();
		
		while ( $custom_value_query->have_posts() ) : $custom_value_query->the_post();
		
			$output[] = get_the_ID();
			
		endwhile;
		
		return $output;
	}
	
	function remove_all_meta_with_key( $key ) {
	
		$posts = $this->get_all_posts_for_meta_key( $key );
		
		if( ! $posts ) { return true; }
		
		foreach( $posts as $post ) {
			delete_post_meta($post, $key);
		}
		
		return ! $this->get_all_posts_for_meta_key( $key );
	}
	
	function admin_page( ) {
		if( ! current_user_can('manage_options') )  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		

	    echo '<div class="wrap">';
	
	    // header
	
	    echo "<h2>" . __( 'Delete Custom Fields', 'delete-custom-fields' ) . "</h2>";
	    echo '<div class="narrow">';
	    
	    echo '<div id="message" class="error fade hidden"></div>';

		echo '<noscript><div class="error"><p><strong>' . __( 'You must enable Javascript in order to proceed!', 'delete-custom-fields'  ) . '</strong></p></div></noscript>';

		// see if we have some fields to delete
				
		if ( ! empty( $_REQUEST['custom-field-to-delete'] ) ) {
		
			check_admin_referer( 'delete_custom_fields' );
			
			$custom_field = esc_attr( $_REQUEST['custom-field-to-delete'] );
		 	
		 	echo '<p>' . __( "Please be patient while the fields are deleted. This can take a while if the field is used in many posts or if your server is slow. Do not navigate away from this page until this script is done or the fields will not be deleted. You will be notified via this page when the process is complete.", 'delete-custom-fields' ) . '</p>';

			$text_goback = sprintf( __( 'To go back to the previous page, <a href="%s">click here</a>.', 'delete-custom-fields' ), admin_url( 'tools.php?page=delete-custom-fields' ) );
			$text_failures = sprintf( __( 'All done! %1$s custom fields were successfully deleted in %2$s seconds and there were %3$s failure(s). To try deleting the post fields again, <a href="%4$s">click here</a>. %5$s', 'delete-custom-fields' ), "' + dcf_successes + '", "' + dcf_totaltime + '", "' + dcf_failures + '", esc_url( wp_nonce_url( admin_url( 'tools.php?page=delete-custom-fields' ), 'delete-custom-fields' ) . '&custom-field-to-delete=' ) . $custom_field, $text_goback );
			$text_nofailures = sprintf( __( 'All done! %1$s custom fields were successfully deleted in %2$s seconds and there were 0 failures. %3$s', 'delete-custom-fields' ), "' + dcf_successes + '", "' + dcf_totaltime + '", $text_goback );
			$text_noposts = sprintf( __( 'No posts were found with the custom field "%1$s". %2$s', 'delete-custom-fields' ), $custom_field, $text_goback );

		 	?>						
		
			<div id="regenthumbs-bar">
					<div id="regenthumbs-bar-percent"></div>
				</div>

		
			<p><input type="button" class="button hide-if-no-js" name="regenthumbs-stop" id="regenthumbs-stop" value="<?php _e( 'Abort Resizing Images', 'delete-custom-fields'  ) ?>" /></p>
		
			<h3 class="title"><?php _e( 'Debugging Information', 'delete-custom-fields'  ) ?></h3>
		
			<p>
				<?php printf( __( 'Total posts: %s', 'delete-custom-fields'  ), '<span id="dcf-debug-postcount">0</span>' ); ?><br />
				<?php printf( __( 'Post fields deleted: %s', 'delete-custom-fields'  ), '<span id="dcf-debug-successcount">0</span>' ); ?><br />
				<?php printf( __( 'Deletion errors: %s', 'delete-custom-fields'  ), '<span id="dcf-debug-failurecount">0</span>' ); ?>
			</p>
			<div class="debug-container">
			<ol id="regenthumbs-debuglist">
				<li class="hidden"></li>
			</ol>	
			</div>
				
			<script type="text/javascript">
			// <![CDATA[
				jQuery(document).ready(function($){
				
					var i;
					var dcf_ids = [];
					var dcf_key = '<?php echo $custom_field; ?>'; 
					var dcf_total = 0;
					var dcf_successes = 0;
					var dcf_failures = 0;
					var dcf_failed_list = '';
					var dcf_timestart = new Date().getTime();
					var dcf_continue = true;
					var dcf_count = 1;
					
					// Create the progress bar
					$("#regenthumbs-bar").progressbar();
					$("#regenthumbs-bar-percent").html( "0%" );
		
					// Stop button
					$("#regenthumbs-stop").click(function() {
						dcf_continue = false;
						$('#regenthumbs-stop').val("Stopping...");
					});
		
					// Clear out the empty list element that's there for HTML validation purposes
					$("#regenthumbs-debuglist li").remove();
					

					// Called after each deletion. Updates debug information and the progress bar.
					function dcfUpdateStatus( id, success, response ) {
						$("#regenthumbs-bar").progressbar( "value", ( dcf_count / dcf_total ) * 100 );
						$("#regenthumbs-bar-percent").html( Math.round( ( dcf_count / dcf_total ) * 1000 ) / 10 + "%" );

						if ( success ) {
							dcf_successes = dcf_successes + 1;
							$("#dcf-debug-successcount").html(dcf_successes);
							$("#regenthumbs-debuglist").prepend("<li value='" + dcf_count + "'>" + response.success + "</li>");
						}
						else {
							dcf_failures = dcf_failures + 1;
							dcf_failed_list = dcf_failed_list + ',' + id;
							$("#dcf-debug-failurecount").html(dcf_failures);
							$("#regenthumbs-debuglist").prepend("<li value='" + dcf_count + "'>" + response.error + "</li>");
						}
						
						dcf_count = dcf_count + 1;

					}
		
					// Called when all fields have been processed. Shows the results and cleans up.
					function dcfFinishUp() {
						dcf_timeend = new Date().getTime();
						dcf_totaltime = Math.round( ( dcf_timeend - dcf_timestart ) / 1000 );

						$('#regenthumbs-stop').hide();
		
						if ( dcf_failures > 0 ) {
							dcf_resulttext = '<?php echo $text_failures; ?>';
						} else if (dcf_total == 0) {
							dcf_resulttext = '<?php echo $text_noposts ?>';
						} else {
							dcf_resulttext = '<?php echo $text_nofailures; ?>';
							$("#message").removeClass('error').addClass('updated');
						}
		
						$("#message").html("<p><strong>" + dcf_resulttext + "</strong></p>").slideDown('fast');
					}
		
					// Delete a specified field via AJAX
					function deleteField( id ) {
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: { action: "deletecustomfield", id: id, key: dcf_key },
							success: function( response ) {
								if ( response.success ) {
									dcfUpdateStatus( id, true, response );
								}
								else {
									dcfUpdateStatus( id, false, response );
								}
		
								if ( dcf_ids.length && dcf_continue ) {
									deleteField( dcf_ids.shift() );
								}
								else {
									dcfFinishUp();
								}
							},
							error: function( response ) {
								dcfUpdateStatus( id, false, response );
		
								if ( dcf_ids.length && dcf_continue ) {
									deleteField( dcf_ids.shift() );
								} 
								else {
									dcfFinishUp();
								}
							}
						});
					}
					
					function loadPostIds() {
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: { action: "deletecustomfield", request: 'get_posts', key: dcf_key },
							success: function( response ) {
								if ( response.success ) {
									dcf_ids = response.ids;
									dcf_total = dcf_ids.length;
									$("#dcf-debug-postcount").html(dcf_total);
									if(  dcf_ids.length && dcf_continue ) {
										deleteField( dcf_ids.shift() );
									}
								}
								else {
									console.log( "nothing to do" );
									dcfFinishUp();
								}
							},
							error: function( response ) {
								console.log( "couldn't get posts" );
								dcfFinishUp();
							}
						});
					}
					loadPostIds();
				});
			// ]]>
			</script>
			<?php	
 		} 
 		// no submission, show the settings form
 		else {
			    
	    ?>
		<p>
		<?php _e( "This form will <strong>permanently delete</strong> custom fields you select along with any content associated with them. Before using this form, please <strong>make sure that you are not deleting a custom field used by your theme or plugins</strong>; just because you did not explicitly enter or create a custom field does not mean that it does not hold information used by your theme or plugins. " ) ?>
		</p>
				
		<form name="delete-custom-fields" method="post">
		
		<?php wp_nonce_field( "delete_custom_fields") ?>
		
		<label for="custom-field-to-delete"><?php _e("Custom Fields to Delete", "delete-custom-fields" ); ?> 
		
		<select name="custom-field-to-delete" id="custom-field-to-delete">
			<option disabled="disabled"><?php _e("Select a Field", "delete-custom-fields"); ?></option>
			
			<?php
			
			$show_hidden = ( isset( $_GET['show-hidden'] ) ) ? true : false;
			
			$custom_fields = Delete_Custom_Fields::get_all_meta_keys( $show_hidden );
			
			foreach($custom_fields as $field) {
				echo "<option value='$field'>$field</option>";
			}
			
			?>
				
		</select>
		
		</label>
		
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Delete Permanently') ?>" />
		<?php if($show_hidden) { ?>		
			<a class="button" href="<?php echo admin_url('tools.php?page=delete-custom-fields'); ?>"><?php esc_attr_e('Hide Hidden Fields'); ?></a>
		<?php } else { ?>
			<a class="button" href="<?php echo admin_url('tools.php?page=delete-custom-fields&show-hidden=true'); ?>"><?php esc_attr_e('Show Hidden Fields'); ?></a>
		<?php } ?>
		</p>
		
		</form>
		<?php } /* end if form submitted */ ?>
		
		</div> <!-- .narrow -->
		
		</div> <!-- .wrap -->
		<?php
	}
	
	function plugin_action_links($links, $file) {

	    if( $file == plugin_basename(__FILE__) ) {
	    
			$settings_link = '<a href="' . admin_url('tools.php?page=delete-custom-fields') . '">Manage</a>';
	        $links = array_merge( array( $settings_link ), $links );
	    }
	
	    return $links;
	} 
	
	// delete a custom field for one post ID (via AJAX)
	function ajax_delete_field() {
		@error_reporting( 0 ); // Don't break the JSON result

		header( 'Content-type: application/json' );
		
		if ( !current_user_can( 'manage_options' ) )
			$this->die_json_error_msg( $id, __( "Your user account doesn't have permission to delete custom fields.", 'delete-custom-fields' ) );
		
		if( isset( $_REQUEST['id'] ) )
			$id = (int) $_REQUEST['id'];
			
		$key = esc_attr( $_REQUEST['key'] );
		
		// if there is no post id, retreive all post ids
		if( isset( $_REQUEST['request'] ) && $_REQUEST['request'] == 'get_posts' ) {
			$ids = $this->get_all_posts_for_meta_key( $key );
			
			if( !empty($ids) ) {
				die( json_encode( array( 'success' => __( 'true', 'delete-custom-fields' ), 'ids' => $ids ) ) );
			} else {
				die( json_encode( array( 'error' => sprintf( __( 'No posts found for custom field &quot;%1$s&quot.', 'delete-custom-fields' ), $key ) ) ) );
			}
		}
		
		if ( ! delete_post_meta($id, $key) )
			$this->die_json_error_msg( $id, 'Custom field not found.' );

		die( json_encode( array( 'success' => sprintf( __( '&quot;%1$s&quot; was deleted for post &quot;%2$s&quot; (ID %3$s).', 'delete-custom-fields' ), $key, esc_html( get_the_title( $id ) ), $id ) ) ) );

	}
	
	// Helper to make a JSON error message
	function die_json_error_msg( $id, $message ) {
		die( json_encode( array( 'error' => sprintf( __( 'Removing &quot;%1$s&quot; (ID %2$s) failed. The error message was: %3$s', 'delete-custom-fields' ), esc_html( get_the_title( $id ) ), $id, $message ) ) ) );
	}

}

// Bootstrap
new Delete_Custom_Fields;

?>