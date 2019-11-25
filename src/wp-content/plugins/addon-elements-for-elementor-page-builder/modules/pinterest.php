<?php

namespace EAE;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} //Exit if accessed directly

class EAE_Pinterest extends Widget_Base {


	public function get_name() {
		return 'wts-pinterest';
	}

	public function get_title() {
		return __( 'EAE - Pinterest', 'wts-eae' );
	}

	public function get_icon() {
		return 'fa fa-pinterest';
	}

	public function get_categories() {
		return [ ' wts-eae' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'General', 'wts-eae' )
			]
		);

		$this->add_control(
			'embed_type',
			[
				'label'   => __( 'Embed Type', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'follow_button',
				'options' => [
					'save_button'   => __( 'Save Button', 'wts-eae' ),
					'follow_button' => __( 'Follow', 'wts-eae' ),
					'pin'           => __( 'Pin', 'wts-eae' ),
					'board'         => __( 'Board', 'wts-eae' ),
					'profile'       => __( 'Profile', 'wts-eae' )
				]
			]
		);

		$this->add_control(
			'button_type',
			[
				'label'     => __( 'Button Type', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'one_image',
				'options'   => [
					'one_image' => __( 'One Image', 'wts-eae' ),
					'any_image' => __( 'Any Image', 'wts-eae' ),
					//'hover' => __('Hover' , 'wts-eae')
				],
				'condition' => [
					'embed_type' => 'save_button',
				]
			]

		);

		$this->add_control(
			'custom_pinterest_icon',
			[
				'label'     => __( 'Custom Pinterest Icon', 'wts-eae' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [
					'embed_type' => 'save_button'
				]

			]
		);

		$this->add_control(
			'user_url',
			[
				'label'     => __( "User URL", 'wts-eae' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'https://www.pinterest.com/pinterest',
				'condition' => [
					'embed_type' => 'follow_button'
				]

			]

		);

		$this->add_control(
			'user_name',
			[
				'label'       => __( 'User Name', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'Pinterest',
				'default'     => 'Pinterest',
				'condition'   => [
					'embed_type' => 'follow_button'
				]
			]
		);

		$this->add_control(
			'round_button',
			[
				'label'        => __( 'Round Button', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'condition'    => [
					'embed_type' => 'save_button',
				]
			]
		);

		$this->add_control(
			'large_button',
			[
				'label'        => __( 'Large Button', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'condition'    => [
					'embed_type' => [ 'save_button', 'follow_button' ]
				]
			]
		);

		$this->add_control(
			'save_label',
			[
				'label'        => __( 'Show Save Label', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'condition'    => [
					'embed_type'    => 'save_button',
					'round_button!' => 'yes'
				]

			]
		);

		$this->add_control(
			'show_pin_count',
			[
				'label'     => __( 'Show Pin Count', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''       => __( 'Not Shown', 'wts-eae' ),
					'above'  => __( 'Above the Button', 'wts-eae' ),
					'beside' => __( 'Beside the Button', 'wts-eae' )
				],
				'condition' => [
					'embed_type'    => 'save_button',
					'button_type'   => 'one_image',
					'round_button!' => 'yes'
				]
			]
		);


		$this->add_control(
			'source_url',
			[
				'label'     => __( 'URL', 'wts-eae' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'https://www.flickr.com/photos/kentbrew/6851755809',
				'condition' => [
					'embed_type'  => 'save_button',
					'button_type' => 'one_image'
				]
			]

		);
		$this->add_control(
			'image_url',
			[
				'label'     => __( 'Image URL', 'wts-eae' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'https://farm8.staticflickr.com/7027/6851755809_df5b2051c9_z.jpg',
				'condition' => [
					'embed_type'  => 'save_button',
					'button_type' => 'one_image'
				]
			]

		);

		$this->add_control(
			'description',
			[
				'label'     => __( 'Description', 'wts-eae' ),
				'type'      => Controls_Manager::TEXTAREA,
				'condition' => [
					'embed_type'  => 'save_button',
					'button_type' => 'one_image'
				]
			]

		);


		$this->add_control(
			'pin_url',
			[
				'label'     => __( 'Pin Url', 'wts-eae' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'https://www.pinterest.com/pin/99360735500167749',
				'condition' => [
					'embed_type' => 'pin'
				]

			]
		);

		$this->add_control(
			'board_url',
			[
				'label'     => __( 'Pinterest Board URL', 'wts-eae' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'https://www.pinterest.com/pinterest/official-news',
				'condition' => [
					'embed_type' => 'board'
				]
			]
		);

		$this->add_control(
			'profile_url',
			[
				'label'     => __( 'Pinterest User URL', 'wts-eae' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'https://www.pinterest.com/pinterest/',
				'condition' => [
					'embed_type' => 'profile'
				]
			]
		);

		$this->add_control(
			'image_width',
			[
				'label'     => __( 'Image Width', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 80
				],
				'range'     => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
						'step' => 5
					]
				],
				'condition' => [
					'embed_type' => [ 'board', 'profile' ]
				]
			]
		);


		$this->add_control(
			'board_width',
			[
				'label'     => __( 'Board Width', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 400
				],
				'range'     => [
					'px' => [
						'min'  => 60,
						'max'  => 1300,
						'step' => 10
					]
				],
				'condition' => [
					'embed_type' => [ 'board', 'profile' ]
				]
			]
		);

		$this->add_control(
			'image_height',
			[
				'label'     => __( 'Image Height', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 240
				],
				'range'     => [
					'px' => [
						'min'  => 60,
						'max'  => 1300,
						'step' => 10
					]
				],
				'condition' => [
					'embed_type' => [ 'board', 'profile' ]
				]
			]
		);


		$this->add_control(
			'pin_size',
			[
				'label'     => __( 'Pin Size', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'small',
				'options'   => [
					'small'  => __( 'Small', 'wts-eae' ),
					'medium' => __( 'Medium', 'wts-eae' ),
					'large'  => __( 'Large', 'wts-eae' ),
				],
				'condition' => [
					'embed_type' => 'pin',
				]
			]
		);


		$this->add_control(
			'hide_description',
			[
				'label'        => __( 'Hide Description', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'condition'    => [
					'embed_type' => 'pin'
				]
			]
		);


		$this->add_control(
			'language',
			[
				'label'     => __( 'Language', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->languages(),
				'default'   => '',
				'condition' => [
					'round_button!' => 'yes',
					'embed_type'    => 'save_button'
				]
			]
		);
	}

	public function languages() {
		$languages = [
			''      => __( 'Automatic', 'wts-eae' ),
			'en'    => __( 'English', 'wts-eae' ),
			'ar'    => __( 'Arabic', 'wts-eae' ),
			'bn'    => __( 'Bengali', 'wts-eae' ),
			'cs'    => __( 'Czech', 'wts-eae' ),
			'da'    => __( 'Danish', 'wts-eae' ),
			'de'    => __( 'German', 'wts-eae' ),
			'el'    => __( 'Greek', 'wts-eae' ),
			'es'    => __( 'Spanish', 'wts-eae' ),
			'fa'    => __( 'Persian', 'wts-eae' ),
			'fi'    => __( 'Finnish', 'wts-eae' ),
			'fil'   => __( 'Filipino', 'wts-eae' ),
			'fr'    => __( 'French', 'wts-eae' ),
			'he'    => __( 'Hebrew', 'wts-eae' ),
			'hi'    => __( 'Hindi', 'wts-eae' ),
			'hu'    => __( 'Hungarian', 'wts-eae' ),
			'id'    => __( 'Indonesian', 'wts-eae' ),
			'it'    => __( 'Italian', 'wts-eae' ),
			'ja'    => __( 'Japanese', 'wts-eae' ),
			'ko'    => __( 'Korean', 'wts-eae' ),
			'msa'   => __( 'Malay', 'wts-eae' ),
			'nl'    => __( 'Dutch', 'wts-eae' ),
			'no'    => __( 'Norwegian', 'wts-eae' ),
			'pl'    => __( 'Polish', 'wts-eae' ),
			'pt'    => __( 'Portuguese', 'wts-eae' ),
			'pt-br' => __( 'Portuguese (Brazil)', 'wts-eae' ),
			'ro'    => __( 'Romania', 'wts-eae' ),
			'ru'    => __( 'Rus', 'wts-eae' ),
			'sv'    => __( 'Swedish', 'wts-eae' ),
			'th'    => __( 'Thai', 'wts-eae' ),
			'tr'    => __( 'Turkish', 'wts-eae' ),
			'uk'    => __( 'Ukrainian', 'wts-eae' ),
			'ur'    => __( 'Urdu', 'wts-eae' ),
			'vi'    => __( 'Vietnamese', 'wts-eae' ),
			'zh-cn' => __( 'Chinese (Simplified)', 'wts-eae' ),
			'zh-tw' => __( 'Chinese (Traditional)', 'wts-eae' ),
		];

		return $languages;

	}

	public function render() {
		$settings = $this->get_settings();
		//echo '<pre>'; print_r($settings); echo '</pre>';

		switch ( $settings['embed_type'] ) {

			case "save_button" :
				$this->get_save_button_html( $settings );
				break;

			case "follow_button" :
				$this->get_follow_button_html( $settings );
				break;

			case "pin" :
				$this->get_pin_html( $settings );
				break;

			case "board" :
				$this->get_board_html( $settings );
				break;

			case "profile"  :
				$this->get_profile_html( $settings );
				break;

		}
		?>
		<?php

	}

	public function get_save_button_html( $settings ) {
		if ( $settings['button_type'] == 'one_image' ) {
			$this->add_render_attribute( 'save_button', 'data-pin-do', 'buttonPin' );
			if ( $settings['round_button'] != 'yes' ) {
				$this->add_render_attribute( 'save_button', 'data-pin-count', $settings['show_pin_count'] );
			}
			$this->add_render_attribute( 'save_button', 'href', 'https://in.pinterest.com/pin/create/button/?url=' . $settings['source_url'] . '&media=' . $settings['image_url'] . '&description=' . $settings['description'] );
		}
		if ( $settings['button_type'] == 'any_image' ) {
			$this->add_render_attribute( 'save_button', 'data-pin-do', 'buttonBookmark' );
			$this->add_render_attribute( 'save_button', 'href', 'https://in.pinterest.com/pin/create/button/' );
		}
		if ( $settings['large_button'] == 'yes' ) {
			$this->add_render_attribute( 'save_button', 'data-pin-tall', 'true' );
		}
		if ( $settings['round_button'] == 'yes' ) {
			$this->add_render_attribute( 'save_button', 'data-pin-round', 'true' );
		}
		if ( $settings['save_label'] == 'yes' && $settings['round_button'] != 'yes' ) {
			$this->add_render_attribute( 'save_button', 'data-pin-save', 'true' );
		}
		if ( $settings['custom_pinterest_icon']['url'] != "" ) {
			$this->add_render_attribute( 'save_button', 'data-pin-custom', 'true' );
		}

		$this->add_render_attribute( 'save_button', 'data-pin-lang', $settings['language'] );

		?>
        <a <?php echo $this->get_render_attribute_string( 'save_button' ); ?> >
			<?php
			if ( $settings['custom_pinterest_icon']['url'] != "" ) {
				?>
                <img src="<?php echo $settings['custom_pinterest_icon']['url']; ?>" height="25"/>
				<?php
			}
			?>
        </a>
		<?php

	}

	public function get_follow_button_html( $settings ) {
		if ( $settings['embed_type'] == 'follow_button' ) {
			$this->add_render_attribute( 'follow-button', 'data-pin-do', 'buttonFollow' );
		}
		$this->add_render_attribute( 'follow-button', 'href', $settings['user_url'] );
		if ( $settings['large_button'] == 'yes' ) {
			$this->add_render_attribute( 'follow-button', 'data-pin-tall', 'true' );
		}

		?>
        <a <?php echo $this->get_render_attribute_string( 'follow-button' ) ?> ><?php echo $settings['user_name'] ?> </a>

		<?php
	}

	public function get_pin_html( $settings ) {
		if ( $settings['embed_type'] == 'pin' ) {
			$this->add_render_attribute( 'pin', 'data-pin-do', 'embedPin' );
		}
		$this->add_render_attribute( 'pin', 'href', $settings['pin_url'] );
		$this->add_render_attribute( 'pin', 'data-pin-width', $settings['pin_size'] );
		if ( $settings['hide_description'] == 'yes' ) {
			$this->add_render_attribute( 'pin', 'data-pin-terse', 'true' );
		}
		?>
        <a <?php echo $this->get_render_attribute_string( 'pin' ); ?> ></a>
		<?php


	}

	public function get_board_html( $settings ) {
		if ( $settings['embed_type'] == 'board' ) {
			$this->add_render_attribute( 'board', 'data-pin-do', 'embedBoard' );
		}

		$this->add_render_attribute( 'board', 'href', $settings['board_url'] );
		$this->add_render_attribute( 'board', 'data-pin-scale-width', $settings['image_width']['size'] );
		$this->add_render_attribute( 'board', 'data-pin-board-width', $settings['board_width']['size'] );
		$this->add_render_attribute( 'board', 'data-pin-scale-height', $settings['image_height']['size'] );
		?>
        <a <?php echo $this->get_render_attribute_string( 'board' ); ?> ></a>
		<?php
	}

	public function get_profile_html( $settings ) {
		if ( $settings['embed_type'] == 'profile' ) {
			$this->add_render_attribute( 'profile', 'data-pin-do', 'embedUser' );
		}

		$this->add_render_attribute( 'profile', 'href', $settings['profile_url'] );
		$this->add_render_attribute( 'profile', 'data-pin-scale-width', $settings['image_width']['size'] );
		$this->add_render_attribute( 'profile', 'data-pin-board-width', $settings['board_width']['size'] );
		$this->add_render_attribute( 'profile', 'data-pin-scale-height', $settings['image_height']['size'] );
		?>
        <a <?php echo $this->get_render_attribute_string( 'profile' ); ?> ></a>
		<?php
	}


}

Plugin::instance()->widgets_manager->register_widget_type( new EAE_Pinterest() );