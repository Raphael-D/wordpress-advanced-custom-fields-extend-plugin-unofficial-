<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPDN_Admin.
 *
 * Admin class holds all admin related functions.
 *
 * @class		WPDN_Admin
 * @version		1.0.0
 * @author		Jeroen Sormani
 */
class WPDN_Admin {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Admin bar 'add note' button
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_add_note' ) );

	}


	/**
	 * Admin bar add note.
	 *
	 * Add a item to the admin bar to add a new note.
	 *
	 * @since 1.0.7
	 *
	 * @param  $wp_admin_bar Arg that will allow one to add new items.
	 */
	public function admin_bar_add_note( &$wp_admin_bar ) {

		$screen = get_current_screen();

		// Only show on dashboard
		if ( 'dashboard' !== $screen->id ) {
			return;
		}

		$wp_admin_bar->add_menu( array(
			'id'     => 'wpdn-add-note',
			'parent' => 'top-secondary',
			'title'  => '+ ' . __( 'Add note', 'wp-dashboard-notes' ),
			'href'   => 'javascript:void(0);',
		) );

	}


}
