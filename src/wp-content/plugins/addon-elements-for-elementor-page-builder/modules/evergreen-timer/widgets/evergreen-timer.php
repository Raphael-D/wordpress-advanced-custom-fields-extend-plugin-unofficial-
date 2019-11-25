<?php

namespace WTS_EAE\Modules\EvergreenTimer\Widgets;

use WTS_EAE\Modules\EvergreenTimer\Skins;
use Elementor\Controls_Manager;
use Elementor\Utils;
use WTS_EAE\Base\EAE_Widget_Base;

class Evergreen_Timer extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-evergreen-timer';
	}

	public function get_title() {
		return __( 'EAE - Evergreen Timer', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icons eae-timer';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'egt_skins',
			[
				'label' => __( 'Skins', 'wts-eae' ),
			]
		);

		$this->register_common_controls();

		$this->end_controls_section();
	}

	protected function _register_skins() {
		$this->add_skin( new Skins\Skin_1( $this ) );
		$this->add_skin( new Skins\Skin_2( $this ) );
		$this->add_skin( new Skins\Skin_3( $this ) );
		//$this->add_skin( new Skins\Skin_4( $this ) );
	}
	protected $_has_template_content = false;
	public function register_common_controls(){
		$this->add_control(
			'countdown_type',
			[
				'label' => __( 'Type', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'countdown' => __( 'Countdown', 'wts-eae' ),
					'evergreen_timer' => __( 'Evergreen Timer', 'wts-eae' ),
				],
				'label_block' => true,
				'default'   => 'evergreen_timer',
			]
		);

		$this->add_control(
			'egt_expiry',
			[
				'label'       => __( 'Cookie Expire (in hours)', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'       => '24',
				'condition'   => [
					'countdown_type' => 'evergreen_timer'
				]
			]
		);

		$this->add_control(
			'timer_date',
			[
				'label' => __( 'Due Date', 'wts-eae' ),
				'type' => Controls_Manager::DATE_TIME,
				//'default' => date( 'Y-m-d H:i', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				'default' => date( 'Y-m-d H:i', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				/* translators: %s: Time zone. */
				'description' => sprintf( __( 'Date set according to your timezone: %s.', 'wts-eae' ), Utils::get_timezone_string() ),
				'condition'   => [
					'countdown_type' => 'countdown'
				]
			]
		);

		$this->add_control(
			'countdown_title',
			[
				'label' => __( 'Title', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Evergreen Title',
			]
		);
		$this->add_control(
			'egt_hours',
			[
				'label'       => __( 'Hours', 'wts-eae' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'default'       => '25',
				'condition'   => [
					'countdown_type' => 'evergreen_timer'
				]
			]
		);
		$this->add_control(
			'egt_minutes',
			[
				'label'       => __( 'Minutes', 'wts-eae' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => false,
				'default'       => '59',
				'condition'   => [
					'countdown_type' => 'evergreen_timer'
				]
			]
		);
		$this->add_control(
			'show_days',
			[
				'label' => __( 'Days', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wts-eae' ),
				'label_off' => __( 'Hide', 'wts-eae' ),
				'default' => 'yes',
			]
		);
		$this->add_control(
			'show_hours',
			[
				'label' => __( 'Hours', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wts-eae' ),
				'label_off' => __( 'Hide', 'wts-eae' ),
				'default' => 'yes',
			]
		);
		$this->add_control(
			'show_minutes',
			[
				'label' => __( 'Minutes', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wts-eae' ),
				'label_off' => __( 'Hide', 'wts-eae' ),
				'default' => 'yes',
			]
		);
		$this->add_control(
			'show_seconds',
			[
				'label' => __( 'Seconds', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wts-eae' ),
				'label_off' => __( 'Hide', 'wts-eae' ),
				'default' => 'yes',
			]
		);
		$this->add_control(
			'action_after_expire',
			[
				'label' => __( 'Action', 'wts-eae' ),
				'type' => Controls_Manager::SELECT2,
				'options' => [
					'redirect' => __( 'Redirect', 'wts-eae' ),
					'hide_parent' => __( 'Hide Parent Section', 'wts-eae' ),
					'hide' => __( 'Hide', 'wts-eae' ),
					'message' => __( 'Show Message', 'wts-eae' ),
				],
				'label_block' => true,
				'separator' => 'before',
				'multiple' => true,
			]
		);

		$this->add_control(
			'expire_message',
			[
				'label' => __( 'Message', 'wts-eae' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'action_after_expire' => 'message'
				]
			]
		);

		$this->add_control(
			'redirect_url_expire',
			[
				'label' => __( 'Redirect URL', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'separator' => 'before',
				'show_external' => false,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'action_after_expire' => 'redirect'
				]
			]
		);
	}
}