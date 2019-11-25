<?php
/**
 * Envato Elements:
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


/**
 * Template class for our CPT to store imported data separate to Elementor or Beaver Builder etc..
 *
 * @since 0.0.2
 */
class Template extends Base {

	/**
	 * Template constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	public function init() {

	}

}
