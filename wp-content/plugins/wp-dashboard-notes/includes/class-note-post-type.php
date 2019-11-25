<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Note_Post_Type.
 *
 * Register and handle post type registration.
 *
 * @class		Note_Post_Type
 * @version		1.0.0
 * @package		WP Dashboard Notes
 * @author		Jeroen Sormani
 */
class Note_Post_Type {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register post type
		add_action( 'init', array( $this, 'register_post_type' ) );

	}


	/**
	 * Register post type.
	 *
	 * Register and set settings for post type 'note'.
	 *
	 * @since 1.0.0
	 */
	public function register_post_type() {

		$labels = array(
			'name'               => __( 'Notes', 'wp-dashboard-notes' ),
			'singular_name'      => __( 'Note', 'wp-dashboard-notes' ),
			'add_new'            => __( 'Add New', 'wp-dashboard-notes' ),
			'add_new_item'       => __( 'Add New Note', 'wp-dashboard-notes' ),
			'edit_item'          => __( 'Edit Note', 'wp-dashboard-notes' ),
			'new_item'           => __( 'New Note', 'wp-dashboard-notes' ),
			'view_item'          => __( 'View Note', 'wp-dashboard-notes' ),
			'search_items'       => __( 'Search Notes', 'wp-dashboard-notes' ),
			'not_found'          => __( 'No Notes', 'wp-dashboard-notes' ),
			'not_found_in_trash' => __( 'No Notes found in Trash', 'wp-dashboard-notes' ),
		);

		register_post_type( 'note', array(
			'label'           => 'note',
			'show_ui'         => false,
			'show_in_menu'    => false,
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'rewrite'         => array( 'slug'         => 'notes' ),
			'_builtin'        => false,
			'query_var'       => true,
			'supports'        => array( 'title', 'editor' ),
			'labels'          => $labels,
		) );

	}


}

// Backwards compatibility
$GLOBALS['wpdn_post_type'] = WP_Dashboard_Notes()->post_type;
