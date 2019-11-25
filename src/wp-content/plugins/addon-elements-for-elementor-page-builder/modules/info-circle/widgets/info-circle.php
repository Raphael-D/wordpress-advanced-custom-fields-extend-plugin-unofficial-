<?php

namespace WTS_EAE\Modules\InfoCircle\Widgets;

use Elementor\Repeater;
use WTS_EAE\Classes\Helper;
use Elementor\Controls_Manager;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Background;
use WTS_EAE\Controls\Group\Group_Control_Icon;
use WTS_EAE\Modules\InfoCircle\Skins;
use WTS_EAE\Base\EAE_Widget_Base;

class Info_Circle extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-info-circle';
	}

	public function get_title() {
		return __( 'EAE - Info Circle', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-info-circle';
	}

	protected function _register_skins() {
		$this->add_skin( new Skins\Skin_1( $this ) );
		$this->add_skin( new Skins\Skin_2( $this ) );
		$this->add_skin( new Skins\Skin_3( $this ) );
		$this->add_skin( new Skins\Skin_4( $this ) );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'ic_skins',
			[
				'label' => __( 'Skins', 'wts-eae' ),
			]
		);

		$this->end_controls_section();

		$this->eae_infocircle_content_section();
	}

	protected $_has_template_content = false;

	function eae_infocircle_content_section() {

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'infolist_items_tab' );

		$repeater->start_controls_tab(
			'content',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);

		$repeater->add_control(
			'ic_item_title',
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
				'label' => 'Icon',
			]
		);

		$repeater->add_control(
			'ic_item_content',
			[
				'label'       => __( 'Content', 'wts-eae' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Content', 'wts-eae' ),
				'default'     => __( 'Add some nice text here.', 'wts-eae' ),
			]
		);

		$repeater->add_control(
			'ic_item_title_size',
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
			'style',
			[
				'label' => __( 'Style', 'wts-eae' ),
			]
		);
		$repeater->add_control(
			'ic_custom_style',
			[
				'label'   => __( 'Custom Content Style', 'wts-eae' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$repeater->add_control(
			'ic_custom_content_align',
			[
				'label'     => __( 'Content Alignment', 'wts-eae' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-info-circle {{CURRENT_ITEM}} .eae-info-circle-item__content' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'ic_custom_style' => 'yes'
				]
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography_ind',
				'label'     => __( 'Title Typography', 'wts-eae' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}  .eae-ic-heading',
				'condition' => [
					'ic_custom_style' => 'yes'
				]
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'content_typography_ind',
				'label'     => __( 'Content Typography', 'wts-eae' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_3,
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}  .eae-ic-description',
				'condition' => [
					'ic_custom_style' => 'yes'
				]
			]
		);


		$repeater->add_control(
			'title_color_ind',
			[
				'label'     => __( 'Title Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}  .eae-ic-heading' => 'color: {{VALUE}};',
				],
				'condition' => [
					'ic_custom_style' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'content_color_ind',
			[
				'label'     => __( 'Content Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}  .eae-ic-description' => 'color: {{VALUE}};',
				],
				'condition' => [
					'ic_custom_style' => 'yes'
				]
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'background_color_indv',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-info-circle-item__content-wrap',
				'condition' => [
					'ic_custom_style' => 'yes'
				]
			]
		);

		$this->get_repeater_icon_styles( $repeater );

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->start_controls_section(
			'info_circle',
			[
				'label' => __( 'Info Circle Items', 'wts-eae' ),
			]
		);


		$this->add_control(
			'info_circle_items',
			[
				'label'      => __( 'List Items', 'wts-eae' ),
				'type'       => Controls_Manager::REPEATER,
				'show_label' => true,
				'fields'     => array_values( $repeater->get_controls() ),
				'default'    => [
					[
						'ic_item_title'   => __( 'MASTER CLEANSE BESPOKE', 'wts-eae' ),
						'ic_item_content' => __( 'IPhone tilde pour-over, sustainable cred roof party occupy master cleanse. Godard vegan heirloom sartorial flannel raw denim +1. Sriracha umami meditation, listicle chambray fanny pack blog organic Blue Bottle.', 'wts-eae' ),
					],
					[
						'ic_item_title'   => __( 'ORGANIC BLUE BOTTLE', 'wts-eae' ),
						'ic_item_content' => __( 'Godard vegan heirloom sartorial flannel raw denim +1 umami gluten-free hella vinyl. Viral seitan chillwave, before they sold out wayfarers selvage skateboard Pinterest messenger bag.', 'wts-eae' ),
					],
					[
						'ic_item_title'   => __( 'TWEE DIY KALE', 'wts-eae' ),
						'ic_item_content' => __( 'Twee DIY kale chips, dreamcatcher scenester mustache leggings trust fund Pinterest pickled. Williamsburg street art Odd Future jean shorts cold-pressed banh mi DIY distillery Williamsburg.', 'wts-eae' ),
					],
				],
			]
		);
		$this->end_controls_section();


	}

	function get_repeater_icon_styles( $repeater ) {
		$helper = new Helper();
		$helper->group_icon_styles_repeater( $repeater, [
			'name'                  => 'item_icon',
			'label'                 => __( 'Icon', 'wts-eae' ),
			'primary_color'         => true,
			'secondary_color'       => true,
			'hover_primary_color'   => false,
			'hover_secondary_color' => false,
			'focus_primary_color'   => true,
			'focus_secondary_color' => true,
			'hover_animation'       => false,
			'icon_size'             => true,
			'icon_padding'          => true,
			'rotate'                => true,
			'border_width'          => true,
			'border_radius'         => true,
			'tabs'                  => false,
			'custom_style_switch'   => true,
			'focus_item_class'      => 'eae-active',
		] );
	}

	protected function _content_template() {
		?>
	<#
		#>
		<div class="eae-info-circle-wrapper">
			<div class="eae-info-circle" data-active-item="1" data-autoplay="{{{ settings[settings['_skin'] + '_ic_content_auto_change'] }}}"
			     data-delay="{{{ settings[settings['_skin'] + '_ic_change_duration'] }}}" style="opacity: 1;">

				<# if(settings.info_circle_items.length){

				settings.info_circle_items.forEach(infoCircle);

				function infoCircle(item,index){
					var control_name = 'item_icon';
					var icon_type = 'eae-icon-type-' + settings[settings['_skin'] + '_global_icon_type'];
					var icon_name = 'eae-icon-' + control_name;
					var icon_html = ''
					var icon_view = 'eae-icon-view-' + settings[settings['_skin'] + '_global_icon_view'];
					var icon_shape = 'eae-icon-shape-' + settings[settings['_skin'] + '_global_icon_shape'];

				if( item.item_icon_view != 'global' ){
					icon_view = 'eae-icon-view-' + item.item_icon_view;
					}

				if( item.item_icon_shape != 'global' ){
					icon_shape = 'eae-icon-shape-' + item.item_icon_shape;
					}

				if ( item.item_icon_eae_icon == 'custom') {
					if ( item.item_icon_icon_type == 'icon' ) {
					icon_html = '<i class="' + item.item_icon_icon + '"></i>';
					}
					else if ( item.item_icon_icon_type == 'image' ) {
						icon_html = '<i><img src="' + item.item_icon_image.url + '"/></i>';
						}
					else if ( item.item_icon_icon_type == 'text' ) {
						icon_html = '<i class="">' + item.item_icon_text + '</i>';
						}
				}
				else {
					if ( settings[settings['_skin'] + '_global_icon_type'] == 'icon' ) {
					icon_html = '<i class="' + settings[settings['_skin'] + '_global_icon'] + '"></i>';
					}
					else if ( settings[settings['_skin'] + '_global_icon_type'] == 'image' ) {
						icon_html = '<i><img src="' + settings[settings['_skin'] + '_global_icon_image'].url + '"/></i>';
						}
					else if ( settings[settings['_skin'] + '_global_icon_type'] == 'text' ) {
					icon_html = '<i class="">' + settings[settings['_skin'] + '_global_icon_text'] + '</i>';
					}
				}
				#>
				<div class="eae-info-circle-item elementor-repeater-item-{{{ item['_id'] }}}" style="opacity: 1;">
					<div id="{{{ item['_id'] }}}" data-id="{{{ item['_id'] }}}" class="eae-ic-icon-wrap"
					     style="opacity: 1;">
						<div class="eae-icon {{ icon_name }} {{ icon_view }} {{ icon_shape }} {{ icon_type }}">
							<div class="eae-icon-wrap">
								{{{ icon_html }}}
							</div>
						</div>
					</div>
					<div class="eae-info-circle-item__content-wrap">
						<div class="eae-info-circle-item__content">
							<h3 class="eae-ic-heading">{{{ item['ic_item_title'] }}}</h3>
							<div class="eae-ic-description">
								<p>{{{ item['ic_item_content'] }}} </p>
							</div>
						</div>
					</div>
				</div>
				<# }
				}
				#>
			</div>
		</div>
		<?php
	}
}