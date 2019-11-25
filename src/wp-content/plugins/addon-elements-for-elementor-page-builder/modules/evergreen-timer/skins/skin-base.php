<?php

namespace WTS_EAE\Modules\EvergreenTimer\Skins;

use WTS_EAE\Plugin;
use Elementor\Controls_Manager;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use WTS_EAE\Controls\Group\Group_Control_Icon;
use Elementor\Utils;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;

abstract class Skin_Base extends Elementor_Skin_Base {

	protected function _register_controls_actions() {
		add_action( 'elementor/element/eae-evergreen-timer/egt_skins/before_section_end', [
			$this,
			'register_controls'
		] );
		add_action( 'elementor/element/eae-evergreen-timer/egt_skins/after_section_end', [
			$this,
			'register_items_control'
		] );
		add_action( 'elementor/element/eae-evergreen-timer/egt_skins/after_section_end', [
			$this,
			'register_style_controls'
		] );
	}

	public function register_items_control( Widget_Base $widget ) {

		$this->start_controls_section(
			'general_style',
			[
				'label' => __( 'General', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'wrapper_bg_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-time-wrapper' => 'background-color: {{VALUE}}'
				]
			]
		);
		$this->add_control(
			'wrapper_align',
			[
				'label'     => __( 'Alignment', 'wts-eae' ),
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
					'{{WRAPPER}} .eae-evergreen-wrapper' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-time-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			[
				'label' => __( 'Title', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .egt-title' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'wts-eae' ),
				'selector' => '{{WRAPPER}} .egt-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'time_style',
			[
				'label' => __( 'Time Digit', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'time_num_typography',
				'label'    => __( 'Typography', 'wts-eae' ),
				'selector' => '{{WRAPPER}} .egt-time',
			]
		);

		$this->add_control(
			'time_num_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .egt-time' => 'color: {{VALUE}} !important;'
				]
			]
		);
		$this->add_control(
			'time_num_bg_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .egt-time' => 'background-color: {{VALUE}} !important;'
				]
			]
		);

		$this->add_responsive_control(
			'time_num_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .egt-time' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'time_text_style',
			[
				'label' => __( 'Time Label', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'time_text_typography',
				'label'    => __( 'Typography', 'wts-eae' ),
				'selector' => '{{WRAPPER}} .egt-time-text',
			]
		);
		$this->add_control(
			'time_text_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .egt-time-text' => 'color: {{VALUE}}'
				]
			]
		);
		$this->add_control(
			'time_text_bg_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .egt-time-text' => 'background-color: {{VALUE}}'
				]
			]
		);

		$this->add_responsive_control(
			'time_text_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .egt-time-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	public function register_style_controls() {
		//$this->bpel_eg_style_section();
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;
	}

	public function get_egt_interval($settings){
		if($settings['countdown_type'] !== 'countdown'){
			$minutes = empty( $settings['egt_minutes'] ) ? 0 : ( $settings['egt_minutes'] * 60 );
			$hours = empty( $settings['egt_hours'] ) ? 0 : ( $settings['egt_hours'] * 60 * 60 );

			$egt_interval = $hours + $minutes  ;
		}
		else
		{
			$egt_interval = $settings['timer_date'];
		}


		return $egt_interval;
	}

	public function eae_get_egt_actions($settings) {
		$actions = $settings['action_after_expire'];

		if ( empty( $actions ) || ! is_array( $actions) ) {
			return false;
		}

		$exp_actions = [];

		foreach ( $actions as $exp_action ) {
			$action_to_run = [ 'type' => $exp_action ];
			if ( 'redirect' === $exp_action ) {
				if ( empty( $settings['redirect_url_expire']['url'] ) ) {
					continue;
				}
				$action_to_run['redirect_url'] = $settings['redirect_url_expire']['url'];
			}
			$exp_actions[] = $action_to_run;
		}
		return $exp_actions;
	}
}