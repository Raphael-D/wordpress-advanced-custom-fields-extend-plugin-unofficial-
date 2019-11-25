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
 * Collection registration and management.
 *
 * @since 0.0.2
 */
class Required_Plugin extends Base {

	public function __construct() {
	}

	private $_current_plugins = [];

	public $category_plugins = [
		'elementor'      => [
			'file'        => 'elementor/elementor.php',
			'slug'        => 'elementor',
			'min_version' => '2.5.15',
			'name'        => 'Elementor',
		],
		'elementor-blocks'      => [
			'file'        => 'elementor/elementor.php',
			'slug'        => 'elementor',
			'min_version' => '2.5.15',
			'name'        => 'Elementor',
		],
		'elementor-pro'      => [
			'file'        => 'elementor-pro/elementor-pro.php',
			'slug'        => 'elementor-pro',
			'min_version' => '2.5.3',
			'name'        => 'Elementor Pro',
		],
		'beaver-builder' => [
			'file'        => 'beaver-builder-lite-version/fl-builder.php',
			'slug'        => 'beaver-builder-lite-version',
			'min_version' => '2.1.1.3',
			'name'        => 'Beaver Builder',
		],
	];

	public function get_plugin_status( $plugin_slug, $plugin_details ) {

		//return 'install'; // test no plugins installed.

		// If beaver-builder-lite-version is required, we do a special check for the Beaver Builder class to handle Agency mode (varying slugs)
		if ( $plugin_slug === 'beaver-builder-lite-version' && class_exists( '\FLBuilderLoader' ) ) {
			return 'activated';
		}

		if ( empty( $plugin_details['file'] ) ) {
			return 'error';
		}

		if ( ! $this->_current_plugins ) {
			$active_plugins          = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );
			if ( ! is_array( $active_plugins ) ) {
				$active_plugins = [];
			}
			if ( ! is_array( $active_sitewide_plugins ) ) {
				$active_sitewide_plugins = [];
			}
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$active_plugins                   = array_merge( $active_plugins, array_keys( $active_sitewide_plugins ) );
			$this->_current_plugins['active'] = $active_plugins;
			$this->_current_plugins['all']    = get_plugins();
		}

		// This covers when the user is running Elementor in a custom slug dir (i.e. beta version)
		if ( $plugin_slug === 'elementor' && class_exists( '\Elementor\Plugin' ) && ! in_array( 'elementor/elementor.php', $this->_current_plugins['active'], true ) ) {
			foreach ( $this->_current_plugins['active'] as $active_plugin_slug ) {
				$all_plugins_details = $this->_current_plugins['all'][ $active_plugin_slug ];
				if ( $all_plugins_details['TextDomain'] === 'elementor' ) {
					$this->_current_plugins['active'][]                       = 'elementor/elementor.php';
					$this->_current_plugins['all']['elementor/elementor.php'] = $all_plugins_details;
				}
			}
		}
		// Same with Elementor Pro
		if ( $plugin_slug === 'elementor-pro' && class_exists( '\ElementorPro\Plugin' ) && ! in_array( 'elementor-pro/elementor-pro.php', $this->_current_plugins['active'], true ) ) {
			foreach ( $this->_current_plugins['active'] as $active_plugin_slug ) {
				$all_plugins_details = $this->_current_plugins['all'][ $active_plugin_slug ];
				if ( $all_plugins_details['TextDomain'] === 'elementor-pro' ) {
					$this->_current_plugins['active'][]                               = 'elementor-pro/elementor-pro.php';
					$this->_current_plugins['all']['elementor-pro/elementor-pro.php'] = $all_plugins_details;
				}
			}
		}

		if ( in_array( $plugin_details['file'], $this->_current_plugins['active'], true ) ) {
			$state = 'activated';
			// check it's the required min version.
			if ( ! empty( $plugin_details['min_version'] ) ) {
				if (
					isset( $this->_current_plugins['all'][ $plugin_details['file'] ] ) &&
					! empty( $this->_current_plugins['all'][ $plugin_details['file'] ]['Version'] ) &&
					version_compare( $this->_current_plugins['all'][ $plugin_details['file'] ]['Version'], $plugin_details['min_version'], '<' )
				) {
					$state = 'update';
				}
			}
		} else {
			$state = 'install';
			foreach ( array_keys( $this->_current_plugins['all'] ) as $plugin ) {
				if ( strpos( $plugin, basename( $plugin_details['file'] ) ) !== false ) {
					$state = 'deactivated';
				}
			}
		}

		return $state;
	}

	/**
	 *
	 * This checks if the requested plugins are available locally.
	 * We check plugin slug and minimum version number.
	 * This method is called against every single template
	 *
	 * @param $api_required_plugins
	 * @param $category_slug
	 *
	 * @return array
	 */
	public function get_missing_plugins( $api_required_plugins, $category_slug ) {

		// Calculate our required plugins based on the category and any specific API requirements.
		$required_plugins = [];
		if ( $category_slug && isset( $this->category_plugins[ $category_slug ] ) ) {
			$required_plugins[ $this->category_plugins[ $category_slug ]['slug'] ] = $this->category_plugins[ $category_slug ];
		}
		if ( $api_required_plugins ) {
			foreach ( $api_required_plugins as $plugin_slug => $plugin_details ) {
				if ( empty( $plugin_details['slug'] ) ) {
					$plugin_details['slug'] = $plugin_slug;
				}
				$required_plugins[ $plugin_slug ] = $plugin_details;
			}
		}

		// If elementor-pro and elementor are required, we only prompt for Elementor Pro
		if ( isset( $required_plugins['elementor'] ) && isset( $required_plugins['elementor-pro'] ) ) {
			unset( $required_plugins['elementor'] );
		}

		$missing_plugins = [];
		foreach ( $required_plugins as $plugin_slug => $plugin_details ) {
			$plugin_status = $this->get_plugin_status( $plugin_slug, $plugin_details );
			// todo: network mode URLs below.
			switch ( $plugin_status ) {
				case 'deactivated':
					$notice         = [];
					$notice['name'] = $plugin_details['name'];
					// Don't use wp_nonce_url due to &amp; and react
					$notice['url']  = add_query_arg( '_wpnonce', wp_create_nonce( 'activate-plugin_' . $plugin_details['file'] ), admin_url( 'plugins.php?action=activate&plugin=' . $plugin_details['file'] ) );
					$notice['text'] = 'Activate ' . $plugin_details['name'] . ' Plugin';
					$notice['slug'] = $plugin_details['slug'];
					if ( ! empty( $plugin_details['min_version'] ) ) {
						$notice['min_version'] = $plugin_details['min_version'];
					}
					$notice['type']    = $plugin_status;
					$missing_plugins[] = $notice;
					break;
				case 'update':
					$notice         = [];
					$notice['name'] = $plugin_details['name'];
					$notice['url']  = admin_url( 'plugins.php' );
					$notice['text'] = 'Update ' . $plugin_details['name'] . ' Plugin';
					$notice['slug'] = $plugin_details['slug'];
					if ( ! empty( $plugin_details['min_version'] ) ) {
						$notice['min_version'] = $plugin_details['min_version'];
					}
					$notice['type']    = $plugin_status;
					$missing_plugins[] = $notice;
					break;
				case 'install':
					$notice         = [];
					$notice['name'] = $plugin_details['name'];
					// Don't use wp_nonce_url due to &amp; and react
					$notice['url']  = ! empty( $plugin_details['url'] ) ? $plugin_details['url'] : add_query_arg( '_wpnonce', wp_create_nonce( 'install-plugin_' . $plugin_slug ), admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ) );
					$notice['text'] = ! empty( $plugin_details['install_text'] ) ? $plugin_details['install_text'] : 'Install ' . $plugin_details['name'] . ' Plugin';
					$notice['slug'] = $plugin_details['slug'];
					if ( ! empty( $plugin_details['min_version'] ) ) {
						$notice['min_version'] = $plugin_details['min_version'];
					}
					$notice['type']    = $plugin_status;
					$missing_plugins[] = $notice;
					break;
				case 'activated':
					break;
			}
		}

		return $missing_plugins;

	}


}
