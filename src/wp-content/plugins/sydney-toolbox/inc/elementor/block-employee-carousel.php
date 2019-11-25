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
class aThemes_Employee_Carousel extends Widget_Base {

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
		return 'athemes-employee-carousel';
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
		return __( 'aThemes: Employee carousel', 'elementor' );
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
		return 'eicon-person';
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
			'section_timeline',
			[
				'label' => __( 'Employee Carousel', 'elementor' ),
			]
		);

		$this->add_control(
			'employee_list',
			[
				'label' => __( 'Employee list', 'elementor' ),
				'type' => Controls_Manager::REPEATER,
				'default' => [				
				],
				'fields' => [	
					[
						'name' => 'image',
						'label' => __( 'Choose Image', 'elementor' ),
						'type' => Controls_Manager::MEDIA,
					],
					[
						'name' => 'person',
						'label' => __( 'Employee name', 'elementor' ),
						'type' => Controls_Manager::TEXT,
						'label_block' => true,
						'placeholder' => __( 'Employee name', 'elementor' ),
						'default' => __( 'John Doe', 'elementor' ),
					],
					[
						'name' => 'position',
						'label' => __( 'Position', 'elementor' ),
						'type' => Controls_Manager::TEXT,
						'default' => __( 'General Manager', 'elementor' ),
						'placeholder' => __( 'Enter the position', 'elementor' ),
						'label_block' => true,
					],		
					[
						'name' => 'facebook',
						'label' => __( 'Facebook link', 'elementor' ),
						'type' => Controls_Manager::URL,
						'placeholder' => __( 'https://your-link.com', 'elementor' ),
					],									
					[
						'name' => 'twitter',
						'label' => __( 'Twitter link', 'elementor' ),
						'type' => Controls_Manager::URL,
						'placeholder' => __( 'https://your-link.com', 'elementor' ),
					],
					[
						'name' => 'linkedin',
						'label' => __( 'Linkedin link', 'elementor' ),
						'type' => Controls_Manager::URL,
						'placeholder' => __( 'https://your-link.com', 'elementor' ),
					],
					[
						'name' => 'link',
						'label' => __( 'Link (for person\'s name)', 'elementor' ),
						'type' => Controls_Manager::URL,
						'placeholder' => __( 'https://your-link.com', 'elementor' ),
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
			'event_title_color',
			[
				'label' 	=> __( 'Color', 'elementor' ),
				'type' 		=> Controls_Manager::COLOR,
				'default'	=> '',
				'selectors' => [
					'{{WRAPPER}} .roll-team .team-item .team-pop, {{WRAPPER}} .owl-theme .owl-controls .owl-page.active span, {{WRAPPER}} .owl-theme .owl-controls.clickable .owl-page:hover span' => 'background-color: {{VALUE}};',	
					'{{WRAPPER}} .owl-theme .owl-controls .owl-page span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
		//End general styles	

		//Employee name styles
		$this->start_controls_section(
			'section_employee_name_style',
			[
				'label' => __( 'Employee name', 'elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'employee_name_color',
			[
				'label' 	=> __( 'Color', 'elementor' ),
				'type' 		=> Controls_Manager::COLOR,
				'default'	=> '',
				'selectors' => [
					'{{WRAPPER}} .roll-team .team-content .name, {{WRAPPER}} .roll-team .team-content .name a' => 'color: {{VALUE}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'employee_name_typography',
				'selector' 	=> '{{WRAPPER}} .roll-team .team-content .name',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
		//End event date styles	


		//Employee position styles
		$this->start_controls_section(
			'section_employee_position_style',
			[
				'label' => __( 'Employee position', 'elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'employee_position_color',
			[
				'label' 	=> __( 'Color', 'elementor' ),
				'type' 		=> Controls_Manager::COLOR,
				'default'	=> '',
				'selectors' => [
					'{{WRAPPER}} .roll-team .team-content .pos' => 'color: {{VALUE}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'employee_position_typography',
				'selector' 	=> '{{WRAPPER}} .roll-team .team-content .pos',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
		//End employee position styles	
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
		$settings 	= $this->get_settings();
		?>

		<div class="roll-team carousel owl-carousel" data-widgetid="employees-<?php //echo $this->get_id(); ?>">
			<?php foreach ( $settings['employee_list'] as $index => $item ) : ?>
				<?php //Get the custom field values
					$name 	  = $item['person'];
					$position = $item['position'];
					$facebook = $item['facebook']['url'];
					$twitter  = $item['twitter']['url'];
					$linkedin   = $item['linkedin']['url'];
					$link     = $item['link']['url'];
				?>
			<div class="team-item">
			    <div class="team-inner">
			        <div class="pop-overlay">
			            <div class="team-pop">
			                <div class="team-info">
								<div class="name"><?php echo esc_html( $name ); ?></div>
								<div class="pos"><?php echo esc_html($position); ?></div>
								<ul class="team-social">
									<?php if ($facebook != '') : ?>
										<li><a class="facebook" href="<?php echo esc_url($facebook); ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
									<?php endif; ?>
									<?php if ($twitter != '') : ?>
										<li><a class="twitter" href="<?php echo esc_url($twitter); ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
									<?php endif; ?>
									<?php if ($linkedin != '') : ?>
										<li><a class="linkedin" href="<?php echo esc_url($linkedin); ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li>
									<?php endif; ?>
								</ul>
			                </div>
			            </div>
			        </div>
					<?php
					if ( ! empty( $item['image']['url'] ) ) { ?>
					<div class="avatar">
						<img src="<?php echo esc_url( $item['image']['url'] ); ?>"/>
					</div>
					<?php
					}
					?>
			    </div>
			    <div class="team-content">
			        <div class="name">
			        	<?php if ( $link == '' ) : ?>
			        		<?php echo esc_html( $name ); ?>
			        	<?php else : ?>
			        		<a href="<?php echo esc_url($link); ?>"><?php echo esc_html( $name ); ?></a>
			        	<?php endif; ?>
			        </div>
			        <div class="pos"><?php echo esc_html( $position ); ?></div>
			    </div>
			</div><!-- /.team-item -->

			<?php endforeach; ?>
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
Plugin::instance()->widgets_manager->register_widget_type( new aThemes_Employee_Carousel() );