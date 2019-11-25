<?php
/**
 * Envato Elements: Bootstrap File
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__;
		$base_dir = __DIR__;
		$len      = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 || $class === $prefix ) {
			return;
		}
		$relative_class = strtolower( substr( $class, $len + 1 ) );
		$relative_class = 'class-' . $relative_class;
		$file           = $base_dir . DIRECTORY_SEPARATOR . str_replace( [ '\\', '_' ], [ '/', '-' ], $relative_class ) . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		} else {
			die( esc_html( basename( $file ) . ' missing.' ) );
		}
	}
);


if ( ! defined( 'ENVATO_ELEMENTS_TESTS' ) ) {
	// In tests we run the instance manually.
	Plugin::get_instance();
	CPT_Kits::get_instance();
	Collection::get_instance();
	License::get_instance();
	Elementor::get_instance();
	REST::get_instance();
	Feedback::get_instance();
	Statistics::get_instance();
	Section::get_instance();
	Collection_Photos::get_instance();
	Deep_Photos::get_instance();
}
