<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor icon list widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.0.0
 */
class aThemes_Testimonials extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve icon list widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'athemes-testimonials';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve icon list widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'aThemes: Testimonials', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve icon list widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the icon list widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'sydney-elements' ];
	}

	/**
	 * Register icon list widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_testimonials',
			[
				'label' => __( 'Testimonials', 'elementor' ),
			]
		);

		$this->add_control(
			'testimonials_list',
			[
				'label' => __( 'Features list', 'elementor' ),
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'name' 			=> __( 'John Doe', 'elementor' ),
						'position' 		=> __( 'Manager', 'elementor' ),
						'testimonial' 	=> __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla id purus neque. Curabitur pulvinar elementum neque in dictum. Sed non lectus nec tortor iaculis tincidunt.', 'elementor' ),				
					],
					[
						'name' 			=> __( 'James Stevens', 'elementor' ),
						'position' 		=> __( 'Manager', 'elementor' ),
						'testimonial' 	=> __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla id purus neque. Curabitur pulvinar elementum neque in dictum. Sed non lectus nec tortor iaculis tincidunt.', 'elementor' ),
					],
				],
				'fields' => [
					[
						'name' => 'image',
						'label' => __( 'Client photo', 'elementor' ),
						'type' => Controls_Manager::MEDIA,
						'label_block' => true,
						'placeholder' => __( 'Client name', 'elementor' ),
					],					
					[
						'name' => 'name',
						'label' => __( 'Client name', 'elementor' ),
						'type' => Controls_Manager::TEXT,
						'label_block' => true,
						'placeholder' => __( 'Client name', 'elementor' ),
						'default' => __( 'John Doe', 'elementor' ),
					],
					[
						'name' => 'position',
						'label' => __( 'Client position', 'elementor' ),
						'type' => Controls_Manager::TEXT,
						'label_block' => true,
						'placeholder' => __( 'Client position', 'elementor' ),
						'default' => __( 'Manager', 'elementor' ),
					],
					[
						'name' => 'testimonial',
						'label' => __( 'Testimonial', 'elementor' ),
						'type' => Controls_Manager::WYSIWYG,
						'label_block' => true,
						'placeholder' => __( 'Testimonial', 'elementor' ),
						'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla id purus neque. Curabitur pulvinar elementum neque in dictum. Sed non lectus nec tortor iaculis tincidunt.', 'elementor' ),
					],

				],
			]
		);


		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		//General styles
		$this->start_controls_section(
			'section_general_style',
			[
				'label' => __( 'General', 'elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'general_color',
			[
				'label' 	=> __( 'Color', 'elementor' ),
				'type' 		=> Controls_Manager::COLOR,
				'default'	=> '#e64e4e',
				'selectors' => [
					'{{WRAPPER}} .widget_sydney_testimonials .fa-quote-left' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-theme .owl-controls .owl-page.active span,{{WRAPPER}} .owl-theme .owl-controls.clickable .owl-page:hover span' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .owl-theme .owl-controls .owl-page:hover span,{{WRAPPER}} .owl-theme .owl-controls .owl-page.active span' => 'border-color: {{VALUE}};',					
				],
			]
		);

		$this->end_controls_section();
		//End name styles	

		//Name styles
		$this->start_controls_section(
			'section_name_style',
			[
				'label' => __( 'Name', 'elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'name_color',
			[
				'label' 	=> __( 'Color', 'elementor' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .roll-testimonials .testimonial-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'name_typography',
				'selector' 	=> '{{WRAPPER}} .roll-testimonials .testimonial-name',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
		//End name styles	

		//Position styles
		$this->start_controls_section(
			'section_position_style',
			[
				'label' => __( 'Position', 'elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'position_color',
			[
				'label' 	=> __( 'Color', 'elementor' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .roll-testimonials .testimonial-position' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'position_typography',
				'selector' 	=> '{{WRAPPER}} .roll-testimonials .testimonial-position',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
		//End position styles	

		//Position styles
		$this->start_controls_section(
			'section_testimonial_style',
			[
				'label' => __( 'Testimonial', 'elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'testimonial_color',
			[
				'label' 	=> __( 'Color', 'elementor' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .roll-testimonials .whisper' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'testimonial_typography',
				'selector' 	=> '{{WRAPPER}} .roll-testimonials .whisper',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
		//End position styles	

	}

	/**
	 * Render icon list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();

		?>

		<div class="widget_sydney_testimonials">
			<i class="fa fa-quote-left"></i>
			<div class="roll-testimonials" data-autoplay="5000">
				<?php foreach ( $settings['testimonials_list'] as $index => $item ) : ?>
                    <div class="customer">
                        <blockquote class="whisper"><?php echo wp_kses_post( $item['testimonial'] ); ?></blockquote>                               
                        <?php if ( $item['image']['url'] ) : ?>
                        <div class="avatar">
                            <img src="<?php echo esc_url( $item['image']['url'] ); ?>"/>
                        </div>
                    	<?php endif; ?>
                        <div class="name">
                        	<div class="testimonial-name"><?php echo esc_html( $item['name'] ); ?></div>
                        	<span class="testimonial-position"><?php echo esc_html( $item['position'] ); ?></span>
                        </div>
                    </div>
				<?php endforeach; ?>
			</div>
		</div>	

		<?php
	}

	/**
	 * Render icon list widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new aThemes_Testimonials() );