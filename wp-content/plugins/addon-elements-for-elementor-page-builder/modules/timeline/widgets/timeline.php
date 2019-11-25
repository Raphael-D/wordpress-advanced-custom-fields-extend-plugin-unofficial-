<?php

namespace WTS_EAE\Modules\Timeline\Widgets;

use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
use WTS_EAE\Classes\Post_Helper;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use WTS_EAE\Controls\Hover_Transition;
use WTS_EAE\Controls\Group\Group_Control_Icon;
use WTS_EAE\Modules\Timeline\Skins;

class Timeline extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-timeline';
	}

	public function get_title() {
		return __( 'EAE - Timeline', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icons eae-timeline';
	}
	protected function _register_skins() {
		$this->add_skin( new Skins\Skin_1( $this ) );
		$this->add_skin( new Skins\Skin_2( $this ) );
		$this->add_skin( new Skins\Skin_3( $this ) );
		$this->add_skin( new Skins\Skin_4( $this ) );
	}

	protected $_has_template_content = false;

	protected function _register_controls() {

		$this->start_controls_section(
			'tl_skins',
			[
				'label' => __( 'Skins', 'wts-eae' ),
			]
		);


		$this->add_control(
			'data_source',
			[
				'label'   => __( 'Source', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'custom' => __( 'Custom', 'wts-eae' ),
					'post'   => __( 'Post', 'wts-eae' ),
				],
				'default' => 'custom',
			]
		);


		$this->end_controls_section();

		$this->eae_timeline_content_section();

		$post_helper = new Post_Helper();

		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'wts-eae' ),
				'condition' => [
					'data_source' => 'post',
				],

			]
		);

		$post_helper->query_controls( $this );


		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_element',
			[
				'label' => __( 'Post Element', 'wts-eae' ),
				'condition' => [
					'data_source' => 'post',
				],

			]
		);

		$this->title_controls();
		$this->date_controls();
		$this->image_controls();
		$this->excerpt_controls();
		$this->read_more_controls();

		$this->end_controls_section();
	}

	function eae_timeline_content_section() {


		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'timeline_items_tab' );

		$repeater->start_controls_tab(
			'content',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);
		$repeater->add_control(
			'item_date',
			[
				'label'   => __( 'Date', 'wts-eae' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'February 2, 2014',
			]
		);

		$repeater->add_control(
			'item_link',
			[
				'label'   => __( 'Link', 'wts-eae' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '#',
					'is_external' => '',
				],

			]
		);

		$repeater->add_control(
			'item_title_text',
			[
				'label'       => __( 'Title', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'This is the heading', 'wts-eae' ),
				'placeholder' => __( 'Enter your title', 'wts-eae' ),
			]
		);

		$repeater->add_group_control(
			Group_Control_Icon::get_type(),
			[
				'name'  => 'item_icon',
				'label' => 'Icon'
			]
		);

		$repeater->add_control(
			'item_content',
			[
				'label'       => __( 'Content', 'wts-eae' ),
				'type'        => Controls_Manager::WYSIWYG,
				'placeholder' => __( 'Content', 'wts-eae' ),
				'default'     => __( 'Add some nice text here.', 'wts-eae' ),
			]
		);

		$repeater->add_control(
			'item_title_size',
			[
				'label'   => __( 'Title HTML Tag', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default' => 'h3',
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'media',
			[
				'label' => __( 'Media', 'wts-eae' ),
			]
		);
		$repeater->add_control( 'item_content_image',
			[
				'label'      => __( 'Choose Image', 'wts-eae' ),
				'type'       => Controls_Manager::MEDIA,
				'default'    => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'show_label' => true,
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'item_content_image_size', // Actually its `image_size`
				'default' => 'medium_large',
			]
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'style',
			[
				'label' => __( 'Style', 'wts-eae' ),
			]
		);
		$repeater->add_control(
			'tl_custom_image_style',
			[
				'label'        => __( 'Custom Image Style', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);
		$repeater->add_control(
			'image_align',
			[
				'label'     => __( 'Alignment', 'wts-eae' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'row'         => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'fa fa-align-left',
					],
					'column'      => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'fa fa-align-center',
					],
					'row-reverse' => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'column',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-tl-item-content' => 'flex-direction: {{VALUE}}',
				],
				'condition' => [
					'tl_custom_image_style' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'image_width',
			[
				'label'     => __( 'Size', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-tl-item-image'                         => 'width: {{SIZE}}%',
					'{{WRAPPER}} {{CURRENT_ITEM}}.image-position-column .eae-tl-content'      => 'width: 100%',
					'{{WRAPPER}} {{CURRENT_ITEM}}.image-position-row .eae-tl-content'         => 'width: calc(100% - {{SIZE}}%)',
					'{{WRAPPER}} {{CURRENT_ITEM}}.image-position-row-reverse .eae-tl-content' => 'width: calc(100% - {{SIZE}}%)',
				],
				'condition' => [
					'tl_custom_image_style' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'image_spacing',
			[
				'label'     => __( 'Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.image-position-column .eae-tl-item-image'      => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.image-position-row .eae-tl-item-image'         => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}.image-position-row-reverse .eae-tl-item-image' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'tl_custom_image_style' => 'yes'
				]
			]
		);
		$repeater->add_control(
			'image_radius',
			[
				'label'      => __( 'Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-tl-item-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'tl_custom_image_style' => 'yes'
				]
			]
		);


		$this->get_repeater_icon_styles( $repeater );

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->start_controls_section(
			'timeline',
			[
				'label'     => __( 'Timeline', 'wts-eae' ),
				'condition' => [
					'data_source' => 'custom',
				],
			]
		);


		$this->add_control(
			'timeline_items',
			[
				'label'      => __( 'Items', 'wts-eae' ),
				'type'       => Controls_Manager::REPEATER,
				'show_label' => true,
				'default'    => [
					[
						'item_date'       => __( 'February 2, 2014', 'wts-eae' ),
						'item_title_text' => __( 'MASTER CLEANSE BESPOKE', 'wts-eae' ),
						'item_content'    => __( 'IPhone tilde pour-over, sustainable cred roof party occupy master cleanse. Godard vegan heirloom sartorial flannel raw denim +1. Sriracha umami meditation, listicle chambray fanny pack blog organic Blue Bottle.', 'wts-eae' ),
					],
					[
						'item_date'       => __( 'March 11, 2014', 'wts-eae' ),
						'item_title_text' => __( 'ORGANIC BLUE BOTTLE', 'wts-eae' ),
						'item_content'    => __( 'Godard vegan heirloom sartorial flannel raw denim +1 umami gluten-free hella vinyl. Viral seitan chillwave, before they sold out wayfarers selvage skateboard Pinterest messenger bag.', 'wts-eae' ),
					],
					[
						'item_date'       => __( 'November 15, 2014', 'wts-eae' ),
						'item_title_text' => __( 'TWEE DIY KALE', 'wts-eae' ),
						'item_content'    => __( 'Twee DIY kale chips, dreamcatcher scenester mustache leggings trust fund Pinterest pickled. Williamsburg street art Odd Future jean shorts cold-pressed banh mi DIY distillery Williamsburg.', 'wts-eae' ),
					],
				],
				'fields'     => array_values( $repeater->get_controls() ),
			]
		);
		$this->end_controls_section();

	}

	public function title_controls(){

		$this->add_control(
			'title_heading',
			[
				'label' => __('Heading', 'wts-eae'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => __('Show Title', 'wts-eae'),
				'type'  => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on' => __( 'Yes', 'wts-eae' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'html_tag',
			[
				'label' => __('HTML Tag', 'wts-eae'),
				'type'  => Controls_Manager::SELECT,
				'options'   => [
					'h1'    =>  'H1',
					'h2'    =>  'H2',
					'h3'    =>  'H3',
					'h4'    =>  'H4',
					'h5'    =>  'H5',
					'h6'    =>  'H6',
					'div'    =>  'div',
					'span'    =>  'span',
					'p'    =>  'p',
				],
				'default'   => 'h1',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		//$this->end_popover();

		$this->add_control(
			'enable_title_link',
			[
				'label' => __( 'Enable Link', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_new_tab',
			[
				'label' => __('Open in new tab','wts-eae'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'enable_title_link' => 'yes',
					'show_title' => 'yes',
				]
			]
		);

	}

	public function image_controls(){

		$this->add_control(
			'image_heading',
			[
				'label' => __('Featured Image', 'wts-eae'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_image',
			[
				'label' => __('Show Image', 'wts-eae'),
				'type'  => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on' => __( 'Yes', 'wts-eae' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
	}
	public function excerpt_controls(){
		$this->add_control(
			'excerpt_heading',
			[
				'label' => __('Excerpt', 'wts-eae'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'enable_excerpt',
			[
				'label' => __( 'Excerpt', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on' => __( 'Yes', 'wts-eae' ),
				'return_value' => 'yes'
			]
		);

		$this->add_control(
			'excerpt_size',
			[
				'label' => __('Excerpt Length','wts-eae'),
				'type'  => Controls_Manager::NUMBER,
				'placeholder' => __('Excerpt Size','wts-eae'),
				'default' => __('15','wts-eae'),
				'condition' => [
					'enable_excerpt' => 'yes'
				],
			]
		);
	}

	public function read_more_controls(){
		$this->add_control(
			'cta_heading',
			[
				'label' => __('Call To Action', 'wts-eae'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'enable_cta',
			[
				'label' => __( 'Call To Action', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on' => __( 'Yes', 'wts-eae' ),
				'return_value' => 'yes'
			]
		);

		$this->add_control(
			'cta_text',
			[
				'label' => __('Excerpt Length','wts-eae'),
				'type'  => Controls_Manager::TEXT,
				'default' => __('Read More','wts-eae'),
				'condition' => [
					'enable_cta' => 'yes'
				],
			]
		);
	}

	public function date_controls(){
		$this->add_control(
			'date_heading',
			[
				'label' => __('Date', 'wts-eae'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => __('Show Date', 'wts-eae'),
				'type'  => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on' => __( 'Yes', 'wts-eae' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'post_date_format',
			[
				'label'   => __( 'Date Format', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'F j, Y g:i a' => date('F j, Y g:i a'),
					'F j, Y' => date( 'F j, Y' ),
					'F, Y' => date( 'F, Y' ),
					'g:i a' => date( 'g:i a' ),
					'g:i:s a' => date( 'g:i:s a' ),
					'l, F jS, Y' => date( 'l, F jS, Y' ),
					'M j, Y @ G:i' => date( 'M j, Y @ G:i' ),
					'Y/m/d \a\t g:i A' => date( 'Y/m/d \a\t g:i A' ),
					'Y/m/d \a\t g:ia' => date( 'Y/m/d \a\t g:ia' ),
					'Y/m/d g:i:s A' => date( 'Y/m/d g:i:s A' ),
					'Y/m/d' => date( 'Y/m/d' ),
					'Y-m-d \a\t g:i A' => date( 'Y-m-d \a\t g:i A' ),
					'Y-m-d \a\t g:ia' => date( 'Y-m-d \a\t g:ia' ),
					'Y-m-d g:i:s A' => date( 'Y-m-d g:i:s A' ),
					'Y-m-d' => date( 'Y-m-d' ),
					'custom' => __( 'Custom', 'wts-eae' ),
					'default' => __( 'Default', 'wts-eae' )
				],
				'default' => 'F j, Y',
				'condition'   => [
					'show_date' => 'yes'
				],
				'description' => '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"> Click here</a> for documentation on date and time formatting.'
			]
		);

		$this->add_control(
			'post_date_format_custom',
			[
				'label'       => __( 'Custom Format', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'd/m/Y', 'wts-eae' ),
				'placeholder' => __( 'Enter Date Format', 'wts-eae' ),
				'condition'   => [
					'show_date' => 'yes',
					'post_date_format' => 'custom',
				]
			]
		);
	}

	function get_repeater_icon_styles( $repeater ) {
		$helper = new Helper();
		$helper->group_icon_styles_repeater( $repeater, [
			'name'                  => 'item_icon',
			'label'                 => __( 'Icon', 'wts-eae' ),
			'primary_color'         => true,
			'secondary_color'       => true,
			'hover_primary_color'   => true,
			'hover_secondary_color' => true,
			'focus_primary_color'   => true,
			'focus_secondary_color' => true,
			'hover_animation'       => false,
			'icon_size'             => true,
			'icon_padding'          => true,
			'rotate'                => true,
			'border_style'          => true,
			'border_width'          => true,
			'border_radius'         => true,
			'tabs'                  => false,
			'custom_style_switch'   => true,
			'focus_item_class'      => 'eae-tl-item-focused',
		] );
	}
}