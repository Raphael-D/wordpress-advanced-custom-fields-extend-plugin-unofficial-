<?php
/**
 * Envato Elements: About Page
 *
 * Handles the display of the about page.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.7
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Feedback registration and management.
 *
 * @since 0.0.2
 */
class Section extends Base {

	const PAGE_SLUG = ENVATO_ELEMENTS_SLUG . '-section';

	public $sections = [];

	/**
	 * Feedback constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'parent_file', [ $this, 'override_wordpress_submenu' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		$this->sections = [
			'about'    => [
				'title'             => __( 'About', 'envato-elements' ),
				'side_nav_DISABLED' => [
					'title' => __( 'About', 'envato-elements' )
				],
			],
			'photos'   => [
				'title'    => __( 'Photos', 'envato-elements' ),
				'category' => 'photos',
				'side_nav' => [
					'title' => __( 'Photos', 'envato-elements' )
				],
			],
			'settings' => [
				'title'    => __( 'Settings', 'envato-elements' ),
				'category' => 'settings',
				'side_nav' => [
					'title' => __( 'Settings', 'envato-elements' )
				],
			],
		];
	}

	/**
	 * Sets up the admin menu options.
	 *
	 * This actually creates a hidden menu option so we can use the `page` uri slug without adding a new left nav option.
	 *
	 * @since 0.0.2
	 * @access public
	 */
	public function admin_menu() {

		// Hidden slug so we can have our `section=` bits below.
		$page = add_submenu_page(
			ENVATO_ELEMENTS_SLUG . '-hidden',
			__( 'Elements', 'envato-elements' ),
			__( 'Elements', 'envato-elements' ),
			'edit_posts',
			self::PAGE_SLUG,
			[ $this, 'admin_menu_open' ]
		);
		add_action( 'admin_print_scripts-' . $page, [ Plugin::get_instance(), 'admin_page_assets' ] );

		if ( License::get_instance()->is_activated() ) {
			foreach ( $this->sections as $section_slug => $section_details ) {
				if ( ! empty( $section_details['side_nav'] ) ) {
					$page = add_submenu_page(
						ENVATO_ELEMENTS_SLUG,
						__( 'Envato Elements', 'envato-elements' ),
						$section_details['side_nav']['title'],
						'edit_posts',
						! empty( $section_details['category'] ) ? Plugin::PAGE_SLUG . '#/' . $section_details['category'] : self::PAGE_SLUG . '&section=' . $section_slug,
						[ $this, 'admin_menu_open' ]
					);
					add_action( 'admin_print_scripts-' . $page, [ Plugin::get_instance(), 'admin_page_assets' ] );
				}
			}
		}

	}

	/**
	 * Gets the URL to display this section page.
	 *
	 * @return string
	 */
	public function get_url( $section = false ) {
		return admin_url( 'admin.php?page=' . self::PAGE_SLUG . ( $section ? '&section=' . $section : '' ) );
	}

	/**
	 * We override the "submenu_file" WordPress global so that the correct submenu is highlighted when on our custom admin page.
	 *
	 * @param string $this_parent_file Current parent file for menu rendering.
	 *
	 * @return string
	 */
	public function override_wordpress_submenu( $this_parent_file ) {
		global $plugin_page, $submenu_file;
		if ( is_admin() && ! empty( $_GET['page'] ) && $_GET['page'] === self::PAGE_SLUG ) {
			$plugin_page = ENVATO_ELEMENTS_SLUG;
			if ( ! empty( $_GET['section'] ) && isset( $this->sections[ $_GET['section'] ] ) ) {
				$submenu_file = self::PAGE_SLUG . '&section=' . $_GET['section'];
			}
		}

		return $this_parent_file;
	}

	/**
	 * Called when the user visits our the about page.
	 */
	public function admin_menu_open() {

		if ( isset( $_GET['section'] ) ) {
			switch ( $_GET['section'] ) {
				case 'about':

					$has_viewed_section = Options::get_instance()->get( 'view_section_statistics' );
					if ( ! $has_viewed_section ) {
						$has_viewed_section = [];
					}
					$has_viewed_section['about'] = true;
					Options::get_instance()->set( 'view_section_statistics', $has_viewed_section );

					Feedback::get_instance()->page_view( 'about' );
					$this->content = $this->render_template( 'sections/about.php' );
					$this->header  = $this->render_template( 'collections/header.php' );
					echo $this->render_template( 'wrapper.php' );  // WPCS: XSS ok.
					break;
				case 'settings':
					$this->content = $this->render_template( 'sections/settings.php' );
					$this->header  = $this->render_template( 'collections/header.php' );
					echo $this->render_template( 'wrapper.php' );  // WPCS: XSS ok.
					break;
			}
		}

	}

	/**
	 * Outputs the top right header nav markup.
	 */
	public function header_nav() {
		/*  $has_viewed_section = Options::get_instance()->get( 'view_section_statistics' );
		 if ( ! $current && ( ! $has_viewed_section || empty( $has_viewed_section['about'] ) ) ) { ?>
			<span class="envato-elements__header-menu-label">New</span>
		<?php }*/

		foreach ( $this->sections as $section_slug => $section_details ) {
			if ( ! empty( $section_details['top_nav'] ) ) {
				$current = ! empty( $_GET['page'] ) && $_GET['page'] === self::PAGE_SLUG && ! empty( $_GET['section'] ) && $_GET['section'] == $section_slug;
				?>
				<li class="envato-elements__header-menuitem">
					<a href="<?php echo esc_url( add_query_arg( 'section', $section_slug, $this->get_url() ) ); ?>"
						class="envato-elements__header-menulink<?php echo $current ? ' envato-elements__header-menulink--current' : ''; ?>">
						<?php echo esc_html( $section_details['top_nav']['title'] ); ?>
						<?php ?>
					</a>
				</li>
				<?php
			}
		}
	}

}
