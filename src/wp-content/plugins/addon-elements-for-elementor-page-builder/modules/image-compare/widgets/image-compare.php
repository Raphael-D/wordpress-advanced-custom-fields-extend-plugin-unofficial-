<?php
namespace WTS_EAE\Modules\ImageCompare\Widgets;

use Elementor\Controls_Manager;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ImageCompare extends EAE_Widget_Base {

	public function get_name() {
		return 'wts-ab-image';
	}

	public function get_title() {
		return __( 'EAE - After/Before Image', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icons eae-ba';
	}


	public function get_categories() {
		return [ 'wts-eae' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'image',
			[
				'label' => __( 'Image', 'wts-eae' )
			]
		);

		$this->add_control(
			'compare_style',
			[
				'label' => __( 'Compare Style', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'horizontal' => __( 'Horizontal', 'wts-eae' ),
					'vertical' => __( 'Vertical', 'wts-eae' ),
				],
				'default' => 'horizontal',
			]
		);

		$this->add_control(
			'slider_position',
			[
				'label' => __( 'Slider Position', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],

				'range' => [
					'%' => [
						'min' => 0,
						'max' => 90,
					],
				],
			]
		);

		$this->add_control(
			'slider_icon',
			[
				'label' => __( 'Icon', 'wts-eae' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-star',
			]
		);

		/*$this->add_responsive_control(
			'img_height',
			[
				'label' => __( 'Image Height', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 300,
				],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-img-comp-container11' => 'height: {{SIZE}}px;',
				],
			]
		);*/

		$this->add_control('before_image',
			[
				'label' => __( 'Before Image', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'show_label' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'before_image_size', // Actually its `image_size`
				'default' => 'medium_large',
			]
		);

		$this->add_control(
			'image_head',
			[
				'label'     => __('', 'wts-eae'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control('after_image',
			[
				'label' => __( 'After Image', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'selectors'=> [
					//'{{WRAPPER}} .eae-img-comp-overlay' => 'background-image:url({{URL}})'
				],
				'show_label' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'after_image_size', // Actually its `image_size`
				'default' => 'medium_large',
			]
		);

		$this->add_control(
			'separator_text',
			[
				'label'     => __('', 'wts-eae'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'text_before',
			[
				'label' => __( 'Before Text', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter text', 'wts-eae' ),
				'default' => __( 'BEFORE', 'wts-eae' ),
			]
		);

		$this->add_control(
			'text_after',
			[
				'label' => __( 'After Text', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter text', 'wts-eae' ),
				'default' => __( 'AFTER', 'wts-eae' ),
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'image_style',
			[
				'label' => __( 'General', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'slider_icon_color',
			[
				'label'     => __('Icon Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'type'  => Scheme_Color::get_type(),
				'selectors'    => [
					'{{WRAPPER}} .eae-slider-icon' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'slider_bg_color',
			[
				'label'     => __('Slider Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'type'  => Scheme_Color::get_type(),
				'selectors'    => [
					'{{WRAPPER}} .eae-img-comp-slider' => 'background-color: {{VALUE}} !important'
				]
			]
		);


		$this->add_control(
			'separator_alignment',
			[
				'label' => __( 'Separator Alignment', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],

				'range' => [
					'%' => [
						'min' => 0,
						'max' => 90,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mode-horizontal .eae-img-comp-slider' => 'top: {{SIZE}}% !important;',
					'{{WRAPPER}} .mode-vertical .eae-img-comp-slider' => 'left: {{SIZE}}% !important;',
				],
			]
		);

		$this->add_control(
			'slider_separator_width',
			[
				'label' => __( 'Separator Width', 'wts-eae' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 1,
				'min' => 0,
				'max' => 10,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .mode-horizontal .eae-img-comp-overlay' => ' border-right-style:solid; border-right-width: {{SIZE}}px;',
					'{{WRAPPER}} .mode-vertical .eae-img-comp-overlay' => ' border-bottom-style:solid; border-bottom-width: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
		        'label_style',
			[
				'label'    =>  __('Label', 'wts-eae'),
				'tab'      => Controls_Manager::TAB_STYLE
			]
        );

		$this->add_control(
			'label_position_horizontal',
			[
				'label' => __( 'Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' => __( 'Top', 'wts-eae' ),
					'bottom' => __( 'Bottom', 'wts-eae' ),
				],
				'condition' => [
					'compare_style' => 'horizontal',
				],
				'default' => 'top',
				'prefix_class' => 'eae-label-pos-',
				'selectors' => [
					'{{WRAPPER}}.eae-label-pos-top .eae-text-after' => 'top: 0px;left:0px',
					'{{WRAPPER}}.eae-label-pos-top .eae-text-before' => 'top: 0px;right:0px',
					'{{WRAPPER}}.eae-label-pos-bottom .eae-text-after' => 'bottom: 10px;left:0px',
					'{{WRAPPER}}.eae-label-pos-bottom .eae-text-before' => 'bottom: 10px;right:0px',
				],
			]
		);

		$this->add_control(
			'label_position_vertical',
			[
				'label' => __( 'Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' => __( 'Left', 'wts-eae' ),
					'right' => __( 'Right', 'wts-eae' ),
				],
				'condition' => [
					'compare_style' => 'vertical',
				],
				'default' => 'left',
				'prefix_class' => 'eae-label-pos-',
				'selectors' => [
					'{{WRAPPER}}.eae-label-pos-left .eae-text-after' => 'top: 0px;left:0px;',
					'{{WRAPPER}}.eae-label-pos-left .eae-text-before' => 'bottom: 0px;right:0px;',
					'{{WRAPPER}}.eae-label-pos-right .eae-text-after' => 'top: 0px;right:0px;',
					'{{WRAPPER}}.eae-label-pos-right .eae-text-before' => 'bottom: 0px; left:0px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_text_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .eae-text-after, {{WRAPPER}} .eae-text-before',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => __('Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1
				],
				'selectors'    => [
					'{{WRAPPER}} .eae-text-after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eae-text-before' => 'color: {{VALUE}}',
				]
			]
		);
		$this->add_control(
			'label_background_color',
			[
				'label'     => __('BackgroundColor', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'type'  => Scheme_Color::get_type(),
				'selectors'    => [
					'{{WRAPPER}} .eae-text-after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .eae-text-before' => 'background-color: {{VALUE}}'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'label_border',
				'label' => __( 'Box Border', 'wts-eae' ),
				'selector' =>
				        '{{WRAPPER}} .eae-text-after, {{WRAPPER}} .eae-text-before',
			]
		);



		$this->add_control(
			'label_border_radius',
			[
				'label' => __( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','%' ],
				'selectors' => [
					'{{WRAPPER}} .eae-text-after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eae-text-before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_padding',
			[
				'label' => __( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .eae-text-after' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					'{{WRAPPER}} .eae-text-before' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);
		$this->add_control(
			'label_margin',
			[
				'label' => __( 'Margin', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .eae-text-after' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					'{{WRAPPER}} .eae-text-before' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->end_controls_section();

	}


	protected function render() {
		$settings = $this->get_settings();
        //print_r($settings);
		$this->add_render_attribute('wrapper', 'class', 'eae-img-comp-container');
		$this->add_render_attribute('wrapper', 'class',  'mode-'.$settings['compare_style']);
		$this->add_render_attribute('wrapper', 'data-ab-style', $settings['compare_style']);
		$this->add_render_attribute('wrapper', 'data-slider-pos', $settings['slider_position']['size']);

		$this->add_render_attribute('icon', 'class',  'icon-'.$settings['compare_style']);
		$this->add_render_attribute('icon', 'class',  'eae-img-comp-slider');
		?>
        <figure <?php echo $this->get_render_attribute_string('wrapper'); ?> >
                <?php echo wp_get_attachment_image($settings['before_image']['id'], $settings['before_image_size_size']); ?>

            <?php if($settings['text_before'] !== "") { ?>
            <span class="eae-text-before"><?php echo  $settings['text_before']; ?></span>
            <?php }  ?>

            <div <?php echo $this->get_render_attribute_string('icon'); ?> >
                <i class="<?php echo $settings['slider_icon']; ?> eae-slider-icon" ></i>
            </div>

            <div class="eae-img-comp-img eae-img-comp-overlay">
	            <?php echo wp_get_attachment_image($settings['after_image']['id'], $settings['after_image_size_size']); ?>

                <?php if($settings['text_after'] !== "") { ?>
                <span class="eae-text-after"><?php echo  $settings['text_after']; ?></span>
            <?php } ?>
            </div>

        </figure>
        <?php
    }

	protected function _content_template() {
		?>
        <#
        view.addRenderAttribute('wrapper','class','eae-img-comp-container');
        view.addRenderAttribute('wrapper','class','mode-'+settings.compare_style);
        view.addRenderAttribute('wrapper','data-ab-style',settings.compare_style);
        view.addRenderAttribute('wrapper','data-slider-pos', settings.slider_position.size);

        view.addRenderAttribute('icon','class',settings.slider_icon);
        view.addRenderAttribute('icon','class','eae-slider-icon');
        #>

        <div {{{ view.getRenderAttributeString( 'wrapper') }}} >
            <img src="{{{ settings.before_image.url }}}" />
        <# if(settings.text_before) {#>
        <span class="eae-text-before">{{{settings.text_before}}}</span>
        <# } #>
            <div class="eae-img-comp-slider"">
            <i {{{ view.getRenderAttributeString( 'icon') }}} ></i>
        </div>

        <div class="eae-img-comp-img eae-img-comp-overlay">
            <img src="{{{ settings.after_image.url }}}" >
            <# if(settings.text_after) { #>
            <span class="eae-text-after">{{{settings.text_after}}}</span>
            <# } #>
        </div>

        </div>
		<?php
	}
}
//Plugin::instance()->widgets_manager->register_widget_type( new Widget_Compare_Image() );