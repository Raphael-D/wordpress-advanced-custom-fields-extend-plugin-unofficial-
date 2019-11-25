<?php
namespace WTS_EAE\Modules\DualButton\Widgets;

use Elementor\Controls_Manager;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class DualButton extends EAE_Widget_Base {
	public function get_name() {
		return 'eae-dual-button';
	}

	public function get_title() {
		return __( 'EAE - Dual Button', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icons eae-dual-button';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'General', 'wts-eae' )
			]
		);


		$this->add_control(
			'button_layout',
			[
				'label'   => __( 'Layout', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'horizontal' => __( 'Horizontal', 'wts-eae' ),
					'vertical'   => __( 'Vertical', 'wts-eae' ),
				],
				'default' => 'horizontal',
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'     => __( 'Alignment', 'wts-eae' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'fa fa-align-center',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-dual-button-main-wrapper' => 'justify-content: {{VALUE}}'
				],
				'default'   => 'center',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button1',
			[
				'label' => __( 'Button 1', 'wts-eae' )
			]
		);

		$this->add_control(
			'button1_text',
			[
				'label'       => __( 'Text', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Button 1', 'wts-eae' ),
				'placeholder' => __( 'Button 1', 'wts-eae' ),
			]
		);

		$this->add_control(
			'button1_link',
			[
				'label'       => __( 'Link', 'wts-eae' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'wts-eae' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'button1_icon',
			[
				'label'       => __( 'Icon', 'wts-eae' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'default'     => '',
			]
		);

		$this->add_control(
			'button1_icon_align',
			[
				'label'     => __( 'Icon Position', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => [
					'before' => __( 'Before', 'wts-eae' ),
					'after'  => __( 'After', 'wts-eae' ),
				],
				'condition' => [
					'button1_icon!' => '',
				],
			]
		);

		$this->add_control(
			'button1_icon_spacing',
			[
				'label'     => __( 'Icon Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-button-1.icon-before .eae-db-icon-wrapper' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .eae-button-1.icon-after .eae-db-icon-wrapper'  => 'margin-left: {{SIZE}}px;',
				],
				'condition' => [
					'button1_icon!' => '',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_button2',
			[
				'label' => __( 'Button 2', 'wts-eae' )
			]
		);

		$this->add_control(
			'button2_text',
			[
				'label'       => __( 'Text', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Button 2', 'wts-eae' ),
				'placeholder' => __( 'Button 2', 'wts-eae' ),
			]
		);

		$this->add_control(
			'button2_link',
			[
				'label'       => __( 'Link', 'wts-eae' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'wts-eae' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'button2_icon',
			[
				'label'       => __( 'Icon', 'wts-eae' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'default'     => '',
			]
		);

		$this->add_control(
			'button2_icon_align',
			[
				'label'     => __( 'Icon Position', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => [
					'before' => __( 'Before', 'wts-eae' ),
					'after'  => __( 'After', 'wts-eae' ),
				],
				'condition' => [
					'button2_icon!' => '',
				],
			]
		);

		$this->add_control(
			'button2_icon_spacing',
			[
				'label'     => __( 'Icon Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-button-2.icon-before .eae-db-icon-wrapper' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .eae-button-2.icon-after .eae-db-icon-wrapper'  => 'margin-left: {{SIZE}}px;',
				],
				'condition' => [
					'button2_icon!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_separator',
			[
				'label' => __( 'Separator', 'wts-eae' )
			]
		);

		$this->add_control(
			'separator_text',
			[
				'label'       => __( 'Text', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'OR', 'wts-eae' ),
				'placeholder' => __( 'OR', 'wts-eae' ),
			]
		);

		$this->add_control(
			'separator_icon',
			[
				'label'       => __( 'Icon', 'wts-eae' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'default'     => '',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'General', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_animation',
			[
				'label'   => __( 'Animation', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'none'    => __( 'None', 'wts-eae' ),
					'style_1' => __( 'Style 1', 'wts-eae' ),
					'style_2' => __( 'Style 2', 'wts-eae' ),
					'style_3' => __( 'Style 3', 'wts-eae' ),
					'style_4' => __( 'Style 4', 'wts-eae' ),
					'style_5' => __( 'Style 5', 'wts-eae' ),
					'style_6' => __( 'Style 6', 'wts-eae' ),
					'style_7' => __( 'Style 7', 'wts-eae' ),
					'style_8' => __( 'Style 8', 'wts-eae' ),
				],
				'prefix_class' => 'animation-',
				'render_type'   => 'template',
				'default' => 'none',
			]
		);

		$this->add_control(
			'button_spacing',
			[
				'label'     => __( 'Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-dual-button-wrapper.button-style-horizontal .eae-button-1-wrapper' => 'margin-right: calc({{SIZE}}px/2);',
					'{{WRAPPER}} .eae-dual-button-wrapper.button-style-horizontal .eae-button-2-wrapper' => 'margin-left: calc({{SIZE}}px/2);',
					'{{WRAPPER}} .eae-dual-button-wrapper.button-style-vertical .eae-button-1-wrapper'   => 'margin-bottom: calc({{SIZE}}px/2);',
					'{{WRAPPER}} .eae-dual-button-wrapper.button-style-vertical .eae-button-2-wrapper'   => 'margin-top: calc({{SIZE}}px/2);',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-button-1-wrapper,{{WRAPPER}} .eae-button-2-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eae-dual-button-wrapper',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button1',
			[
				'label' => __( 'Button 1', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .eae-button-1-wrapper',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'wts-eae' ),
			]
		);

		$this->add_control(
			'button1_color',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eae-button-1-wrapper .eae-button-1' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button1_icon_color',
			[
				'label'     => __( 'Icon Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eae-button-1-wrapper .eae-db-icon-wrapper' => 'color: {{VALUE}};',
				],
				'scheme'    => [
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button1_background_color',
				'label'    => __( 'Background Color', 'wts-eae' ),
				'selector' => '{{WRAPPER}} .eae-button-1-wrapper',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'wts-eae' ),
			]
		);

		$this->add_control(
			'button1_text_hover_color',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-button-1-wrapper:hover .eae-button-1  ' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button1_icon_hover_color',
			[
				'label'     => __( 'Icon Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-button-1-wrapper:hover .eae-db-icon-wrapper' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button1_background_color_hover',
				'label'    => __( 'Background Color', 'wts-eae' ),
				'selector' => '{{WRAPPER}}.animation-none .eae-button-1-wrapper:hover,{{WRAPPER}} .eae-button-1-wrapper:hover:before,{{WRAPPER}} .eae-button-1-wrapper:before',
			]
		);

		$this->add_control(
			'button1_border_hover_color',
			[
				'label'     => __( 'Border Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button1_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-button-1-wrapper:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'button1_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'    => 1,
							'right'  => 1,
							'bottom' => 1,
							'left'   => 1,
							'unit'   => 'px'
						],
					],
					'color'  => [
						'default' => '#0c0c0c',
					]
				],
				'selector'       => '{{WRAPPER}} .eae-button-1-wrapper',
				'separator'      => 'before',
			]
		);

		$this->add_control(
			'button1_border_radius',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-button-1-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.animation-none .eae-button-1-wrapper:hover,{{WRAPPER}} .eae-button-1-wrapper:hover:before,{{WRAPPER}} .eae-button-1-wrapper:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'button1_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-dual-button-wrapper .eae-button-1-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button2',
			[
				'label' => __( 'Button 2', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => ' typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .eae-button-2-wrapper',
			]
		);

		$this->start_controls_tabs( 'tabs_button2_style' );

		$this->start_controls_tab(
			'tab_button2_normal',
			[
				'label' => __( 'Normal', 'wts-eae' ),
			]
		);

		$this->add_control(
			'button2_color',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eae-button-2-wrapper .eae-button-2' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button2_icon_color',
			[
				'label'     => __( 'Icon Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eae-button-2-wrapper .eae-db-icon-wrapper' => 'color: {{VALUE}};',
				],
				'scheme'    => [
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button2_background_color',
				'label'    => __( 'Background Color', 'wts-eae' ),
				'selector' => '{{WRAPPER}} .eae-button-2-wrapper',
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button2_hover',
			[
				'label' => __( 'Hover', 'wts-eae' ),
			]
		);

		$this->add_control(
			'button2_text_hover_color',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-button-2-wrapper:hover .eae-button-2' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button2_icon_hover_color',
			[
				'label'     => __( 'Icon Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-button-2-wrapper:hover .eae-db-icon-wrapper' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button2_background_color_hover',
				'label'    => __( 'Background Color', 'wts-eae' ),
				'selector' => '{{WRAPPER}}.animation-none .eae-button-2-wrapper:hover,{{WRAPPER}} .eae-button-2-wrapper:hover:before,{{WRAPPER}} .eae-button-2-wrapper:before',
			]
		);

		$this->add_control(
			'button2_border_hover_color',
			[
				'label'     => __( 'Border Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button2_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-button-2-wrapper:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'button2_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'    => 1,
							'right'  => 1,
							'bottom' => 1,
							'left'   => 1,
							'unit'   => 'px'
						],
					],
					'color'  => [
						'default' => '#0c0c0c',
					]
				],
				'selector'       => '{{WRAPPER}} .eae-button-2-wrapper',
				'separator'      => 'before',
			]
		);

		$this->add_control(
			'button2_border_radius',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-button-2-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.animation-none .eae-button-2-wrapper:hover,{{WRAPPER}} .eae-button-2-wrapper:hover:before,{{WRAPPER}} .eae-button-2-wrapper:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'button2_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-dual-button-wrapper .eae-button-2s-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_separator',
			[
				'label' => __( 'Separator', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'separator_icon_size',
			[
				'label'     => __( 'Size', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => '40',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-button-separator-wrapper .eae-button-separator'                          => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-style-horizontal .eae-button-separator-wrapper .eae-button-separator' => 'top:50%; right: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .button-style-vertical .eae-button-separator-wrapper .eae-button-separator'   => 'left: calc(50% - {{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'separator_typography',
				'label'    => __( 'Typography', 'wts-eae' ),
				'selector' => '{{WRAPPER}} .eae-button-separator-wrapper .eae-button-separator',
			]
		);

		$this->add_control(
			'separator_icon_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eae-button-separator-wrapper .eae-button-separator' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'separator_background_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-button-separator-wrapper .eae-button-separator' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'separator_border',
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} .eae-button-separator-wrapper .eae-button-separator',
			]
		);

		$this->add_control(
			'separator_border_radius',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-button-separator-wrapper .eae-button-separator' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'separator_box_shadow',
				'selector' => '{{WRAPPER}} .eae-button-separator-wrapper .eae-button-separator',
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'button1', 'class', 'eae-button-1-wrapper' );
		if ( ! empty( $settings['button1_link']['url'] ) ) {
			$this->add_render_attribute( 'button1', 'href', $settings['button1_link']['url'] );

			if ( ! empty( $settings['button1_link']['is_external'] ) ) {
				$this->add_render_attribute( 'button1', 'target', '_blank' );
			}
		}

		$this->add_render_attribute( 'button2', 'class', 'eae-button-2-wrapper' );
		if ( ! empty( $settings['button2_link']['url'] ) ) {
			$this->add_render_attribute( 'button2', 'href', $settings['button2_link']['url'] );

			if ( ! empty( $settings['button2_link']['is_external'] ) ) {
				$this->add_render_attribute( 'button2', 'target', '_blank' );
			}
		}

		$this->add_render_attribute( 'button1_inner', 'class', 'eae-button-1' );
		$this->add_render_attribute( 'button1_inner', 'class', 'icon-' . $settings['button1_icon_align'] );

		$this->add_render_attribute( 'button2_inner', 'class', 'eae-button-2' );
		$this->add_render_attribute( 'button2_inner', 'class', 'icon-' . $settings['button2_icon_align'] );

		$this->add_render_attribute( 'wrapper', 'class', 'eae-dual-button-wrapper' );
		$this->add_render_attribute( 'wrapper', 'class', 'button-style-' . $settings['button_layout'] );

		if ( $settings['button_animation'] !== 'none' ) {
			if ( $settings['button_animation'] == 'style_1' ) {
				$this->add_render_attribute( 'button1', 'class', 'eae-sweep-left' );
				$this->add_render_attribute( 'button2', 'class', 'eae-sweep-right' );
			} else if ( $settings['button_animation'] == 'style_2' ) {
				$this->add_render_attribute( 'button1', 'class', 'eae-sweep-right' );
				$this->add_render_attribute( 'button2', 'class', 'eae-sweep-left' );
			} else if ( $settings['button_animation'] == 'style_3' ) {
				$this->add_render_attribute( 'button1', 'class', 'eae-bounce-left' );
				$this->add_render_attribute( 'button2', 'class', 'eae-bounce-right' );
			} else if ( $settings['button_animation'] == 'style_4' ) {
				$this->add_render_attribute( 'button1', 'class', 'eae-bounce-right' );
				$this->add_render_attribute( 'button2', 'class', 'eae-bounce-left' );
			} else if ( $settings['button_animation'] == 'style_5' ) {
				$this->add_render_attribute( 'button1', 'class', 'eae-sweep-top' );
				$this->add_render_attribute( 'button2', 'class', 'eae-sweep-bottom' );
			} else if ( $settings['button_animation'] == 'style_6' ) {
				$this->add_render_attribute( 'button1', 'class', 'eae-sweep-bottom' );
				$this->add_render_attribute( 'button2', 'class', 'eae-sweep-top' );
			} else if ( $settings['button_animation'] == 'style_7' ) {
				$this->add_render_attribute( 'button1', 'class', 'eae-bounce-top' );
				$this->add_render_attribute( 'button2', 'class', 'eae-bounce-bottom' );
			} else if ( $settings['button_animation'] == 'style_8' ) {
				$this->add_render_attribute( 'button1', 'class', 'eae-bounce-bottom' );
				$this->add_render_attribute( 'button2', 'class', 'eae-bounce-top' );
			}
		}
		?>
        <div class="eae-dual-button-main-wrapper">
            <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
                <div class="eae-button1">
                    <a <?php echo $this->get_render_attribute_string( 'button1' ); ?>>
                        <div <?php echo $this->get_render_attribute_string( 'button1_inner' ); ?>>
							<?php if ( $settings['button1_icon'] !== '' ) { ?>
                                <div class="eae-db-icon-wrapper">
                                    <i class="<?php echo $settings['button1_icon'] ?>"></i>
                                </div>
							<?php } ?>
                            <div class="eae-button-text"><?php echo $settings['button1_text']; ?></div>
                        </div>
                    </a>

					<?php if ( $settings['separator_icon'] !== '' || $settings['separator_text'] !== '' ) { ?>
                        <span class="eae-button-separator-wrapper">
                                <span class="eae-button-separator">
                                    <span>
                                <?php
                                if ( $settings['separator_icon'] !== '' ) {
	                                echo '<i class="' . $settings['separator_icon'] . '"></i>';
                                }
                                if ( $settings['separator_text'] !== '' ) {
	                                echo $settings['separator_text'];
                                }
                                ?>
                                    </span>
                                </span>
                            </span>
					<?php } ?>
                </div>
                <div class="eae-button2">
                    <a <?php echo $this->get_render_attribute_string( 'button2' ); ?>>
                        <div <?php echo $this->get_render_attribute_string( 'button2_inner' ); ?>>
							<?php if ( $settings['button2_icon'] !== '' ) { ?>
                                <div class="eae-db-icon-wrapper">
                                    <i class="<?php echo $settings['button2_icon'] ?>"></i>
                                </div>
							<?php } ?>
                            <div class="eae-button-text"><?php echo $settings['button2_text']; ?></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
		<?php
	}

	public function _content_template() {

		?>
        <#
        view.addRenderAttribute( 'button1', 'class', 'eae-button-1-wrapper' );
        if ( settings['button1_link']['url'] !== '' ) {
        view.addRenderAttribute( 'button1', 'href', settings['button1_link']['url'] );

        if ( settings['button1_link']['is_external'] !== '' ) {
        view.addRenderAttribute( 'button1', 'target', '_blank' );
        }
        }

        view.addRenderAttribute( 'button2', 'class', 'eae-button-2-wrapper' );
        if ( settings['button2_link']['url'] !== '' ) {
        view.addRenderAttribute( 'button2', 'href', settings['button2_link']['url'] );

        if ( settings['button2_link']['is_external'] !== '' ) {
        view.addRenderAttribute( 'button2', 'target', '_blank' );
        }
        }

        view.addRenderAttribute( 'button1_inner', 'class', 'eae-button-1' );
        view.addRenderAttribute( 'button1_inner', 'class', 'icon-' + settings['button1_icon_align'] );

        view.addRenderAttribute( 'button2_inner', 'class', 'eae-button-2' );
        view.addRenderAttribute( 'button2_inner', 'class', 'icon-' + settings['button2_icon_align'] );

        view.addRenderAttribute( 'wrapper', 'class', 'eae-dual-button-wrapper' );
        view.addRenderAttribute( 'wrapper', 'class', 'button-style-' + settings['button_layout'] );

        if ( settings['button_animation'] !== 'none' ) {
        if ( settings['button_animation'] == 'style_1' ) {
        view.addRenderAttribute( 'button1', 'class', 'eae-sweep-left' );
        view.addRenderAttribute( 'button2', 'class', 'eae-sweep-right' );
        } else if ( settings['button_animation'] == 'style_2' ) {
        view.addRenderAttribute( 'button1', 'class', 'eae-sweep-right' );
        view.addRenderAttribute( 'button2', 'class', 'eae-sweep-left' );
        } else if ( settings['button_animation'] == 'style_3' ) {
        view.addRenderAttribute( 'button1', 'class', 'eae-bounce-left' );
        view.addRenderAttribute( 'button2', 'class', 'eae-bounce-right' );
        } else if ( settings['button_animation'] == 'style_4' ) {
        view.addRenderAttribute( 'button1', 'class', 'eae-bounce-right' );
        view.addRenderAttribute( 'button2', 'class', 'eae-bounce-left' );
        } else if ( settings['button_animation'] == 'style_5' ) {
        view.addRenderAttribute( 'button1', 'class', 'eae-sweep-top' );
        view.addRenderAttribute( 'button2', 'class', 'eae-sweep-bottom' );
        } else if ( settings['button_animation'] == 'style_6' ) {
        view.addRenderAttribute( 'button1', 'class', 'eae-sweep-bottom' );
        view.addRenderAttribute( 'button2', 'class', 'eae-sweep-top' );
        } else if ( settings['button_animation'] == 'style_7' ) {
        view.addRenderAttribute( 'button1', 'class', 'eae-bounce-top' );
        view.addRenderAttribute( 'button2', 'class', 'eae-bounce-bottom' );
        } else if ( settings['button_animation'] == 'style_8' ) {
        view.addRenderAttribute( 'button1', 'class', 'eae-bounce-bottom' );
        view.addRenderAttribute( 'button2', 'class', 'eae-bounce-top' );
        }
        }
        #>


        <div class="eae-dual-button-main-wrapper">
            <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
            <div class="eae-button1">
                <a {{{ view.getRenderAttributeString( 'button1' ) }}}>
                <div {{{ view.getRenderAttributeString( 'button1_inner' ) }}}>
                <# if ( settings['button1_icon'] !== '' ) { #>
                <div class="eae-db-icon-wrapper">
                    <i class="{{{ settings['button1_icon'] }}}"></i>
                </div>
                <# } #>
                <div class="eae-button-text">{{{ settings['button1_text'] }}}</div>
            </div>
            </a>

            <# if ( settings['separator_icon'] !== '' || settings['separator_text'] !== '' ) { #>
            <span class="eae-button-separator-wrapper">
                                <span class="eae-button-separator">
                                    <span>
                                <#
                                if ( settings['separator_icon'] !== '' ) {
                                        #>
	                                <i class="{{{ settings['separator_icon'] }}}"></i>
                                <# }
                                if ( settings['separator_text'] !== '' ) {
                                        #>
	                                {{{ settings['separator_text'] }}}
                                <# } #>
                                    </span>
                                </span>
                            </span>
            <# } #>
        </div>
        <div class="eae-button2">
            <a {{{ view.getRenderAttributeString( 'button2' ) }}}>
                <div {{{ view.getRenderAttributeString( 'button2_inner' ) }}}>
                    <# if ( settings['button2_icon'] !== '' ) { #>
                    <div class="eae-db-icon-wrapper">
                        <i class="{{{ settings['button2_icon'] }}}"></i>
                    </div>
                    <# } #>
                    <div class="eae-button-text">{{{ settings['button2_text'] }}}</div>
                </div>
            </a>
        </div>
        </div>
        </div>
		<?php
	}
}