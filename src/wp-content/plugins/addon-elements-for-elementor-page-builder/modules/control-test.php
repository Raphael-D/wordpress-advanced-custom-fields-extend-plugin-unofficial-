<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Widget_Test_Control extends Widget_Base {

	public function get_name() {
		return 'wts-testcontrol';
	}

	public function get_title() {
		return __( 'EAE - Test Control', 'wts-eae' );
	}

	public function get_icon() {
		return 'eicon-flip-box wts-eae-pe';
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'front_box_background_out',
				'types' => [ 'classic', 'gradient'],
				//'selector' => '{{WRAPPER}} .divTest',
			]
		);

		$this->add_control(
			'front_box__color',
			[
				'label' => __( 'Title', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.divTest' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'front_box_background_ind',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.divTest',
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'front_box_title_typography',
				'label' => __( 'Title Typography', 'wts-eae' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.divTest',

			]
		);

		$repeater->add_control(
			'front_box_title_color',
			[
				'label' => __( 'Title', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eae_flip_box_set',
			[
				'label' => __( 'Flip Box', 'wts-eae' ),
				'type' => Controls_Manager::REPEATER,
				'show_label'	=> true,
				'fields'	=> array_values($repeater->get_controls()),
			]
		);

	}

	protected function render( ) {
		$settings = $this->get_settings_for_display();
		//print_r($settings['eae_flip_box_set']);
		foreach ($settings['eae_flip_box_set'] as $flipbox) {
			?>
			<div class="elementor-repeater-item-<?php echo $flipbox['_id']; ?>  divTest">
				Satish
				<i>Kumar</i>
			</div>

	<?php
		}
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Test_Control() );