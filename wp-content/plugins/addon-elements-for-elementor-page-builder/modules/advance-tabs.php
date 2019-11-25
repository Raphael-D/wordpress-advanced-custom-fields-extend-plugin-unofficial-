<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Widget_Advance_Tabs extends Widget_Base {

	public function get_name() {
		return 'wts-advance-tabs';
	}

	public function get_title() {
		return __( 'EAE - Advance Tabs', 'wts-eae' );
	}

	public function get_icon() {
		return 'eicon-divider wts-eae-pe';
	}


	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_script_depends() {
		return [ 'eae-stickyanything' ];
	}

	protected function _register_controls() {

	    $this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'Tabs', 'wts-eae' )
			]
		);
		$this->add_control(
			'tab_position',
			[
				'label' => __( 'Tab Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' => __( 'Top', 'wts-eae' ),
					'left' => __( 'Left', 'wts-eae' ),
				],
				'default' => 'top',
				'prefix_class' => 'eae-nav-pos-',
			]
		);

		$this->add_control(
			'tab_Sticky',
			[
				'label' => __( 'Sticky Tab', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'options' => [
					'yes' => __( 'Yes', 'wts-eae' ),
					'no' => __( 'No', 'wts-eae' ),
				],
				'default' => 'no',
				//'prefix_class' => 'eae-nav-sticky-',

			]
		);

		$this->add_control(
			'scroll_offset',
			[
				'label' => __( 'Scroll Offset', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
                'default' => 20,
				'condition' => [
					'tab_Sticky' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control('tab_title',
				[
					'label' => __( 'Tab Title', 'wts-eae' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'Tab Title', 'wts-eae' ),
					'placeholder' => __( 'Tab Title', 'wts-eae' ),
					'label_block' => true,
				]
		);

		$repeater->add_control(
			'tab_element',
			[
				'label' => __( 'Tab Element', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'none' => [
						'title' => __( 'None', 'wts-eae' ),
						'icon' => 'fa fa-ban',
					],
					'image' => [
						'title' => __( 'Image', 'wts-eae' ),
						'icon' => 'fa fa-picture-o',
					],
					'icon' => [
						'title' => __( 'Icon', 'wts-eae' ),
						'icon' => 'fa fa-star',

					],
				],
				'default' => 'icon',
			]
		);

		$repeater->add_control('tab_image',
			[
				'label' => __( 'Choose Image', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'tab_element' => 'image',
				],
				'show_label' => true,
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'tab_image_size', // Actually its `image_size`
				'default' => 'thumbnail',
				'condition' => [
					'tab_element' => 'image',
					'tab_image[id]!' => '',
				],
			]
		);

		$repeater->add_control(
			'tab_icon',
			[
				'label' => __( 'Icon', 'wts-eae' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-star',
				'condition' => [
					'tab_element' => 'icon',
				],
			]
		);

		$repeater->add_control('tab_content',
			[
				'label' => __( 'Content', 'wts-eae' ),
				'default' => __( 'Tab Content', 'wts-eae' ),
				'placeholder' => __( 'Tab Content', 'wts-eae' ),
				'type' => Controls_Manager::WYSIWYG,
				'show_label' => true,
			]
		);

		$this->add_control(
			'eae_tabs',
			[
				'label' => __( 'Tabs Items', 'wts-eae' ),
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'tab_title' => __( 'Adv Tab 1', 'wts-eae' ),
						'tab_content' => __( 'Advance responsive tab', 'wts-eae' ),
					],
					[
						'tab_title' => __( 'Adv Tab 2', 'wts-eae' ),
						'tab_content' => __( 'Advance responsive tab', 'wts-eae' ),
					],
				],
				'show_label'	=> true,
				'fields'	=> array_values($repeater->get_controls()),
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'tab_style',
            [
                 'label'    =>  __('General', 'wts-eae'),
                 'tab'      => Controls_Manager::TAB_STYLE
            ]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_title_border',
				'selector' => '{{WRAPPER}} .eae-tab-nav li',
			]
		);

		$this->add_control(
			'bord_separator',
			[
				'label'     => __('', 'wts-eae'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'advance_tabs_menu_style' );


		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label' => __( 'Normal', 'wts-eae' ),
			]
		);


		$this->add_control(
            'tab_color',
            [
                'label'     => __('Color', 'wts-eae'),
                'type'      => Controls_Manager::COLOR,
                'scheme'    => [
                                'type'  => Scheme_Color::get_type(),
                                'value' => Scheme_Color::COLOR_1
                            ],
                 'selectors'    => [
                                '{{WRAPPER}} .eae-tab-nav li span' => 'color: {{VALUE}}'
                            ]
            ]
		);

		$this->add_control(
            'icon_color',
            [
                'label'     => __('Icon Color', 'wts-eae'),
                'type'      => Controls_Manager::COLOR,
                'scheme'    => [
                                'type'  => Scheme_Color::get_type(),
                                'value' => Scheme_Color::COLOR_1
                            ],
                 'selectors'    => [
                                '{{WRAPPER}} .eae-tab-nav li i' => 'color: {{VALUE}}'
                            ]
            ]
		);

		$this->add_control(
            'tab_background_color',
            [
                'label'     => __('Background Color', 'wts-eae'),
                'type'      => Controls_Manager::COLOR,
                 'selectors'    => [
                                '{{WRAPPER}} .eae-tab-nav li' => 'background-color: {{VALUE}}'
                            ]
            ]
		);
		$this->add_control(
			'tab_border_color',
			[
				'label'     => __('Border Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'selectors'    => [
					'{{WRAPPER}} .eae-tab-nav li' => 'border-color: {{VALUE}}'
				]
			]
		);

		$this->add_responsive_control(
			'tab_title_border_radius',
			[
				'label' => __( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .eae-tab-nav li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this -> end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label' => __( 'hover/active', 'wts-eae' ),
			]
		);


		$this->add_control(
			'tab_color_hover',
			[
				'label'     => __('Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1
				],
				'selectors'    => [
					'{{WRAPPER}} .eae-tab-nav li:hover span, {{WRAPPER}} .eae-tabs nav li.tab-current span' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label'     => __('Icon Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2
				],
				'selectors'    => [
					'{{WRAPPER}} .eae-tab-nav li:hover i, {{WRAPPER}} .eae-tabs nav li.tab-current i' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'tab_background_color_hover',
			[
				'label'     => __('Background Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'selectors'    => [
					'{{WRAPPER}} .eae-tab-nav li:hover, {{WRAPPER}} .eae-tabs nav li.tab-current' => 'background-color: {{VALUE}}'
				]
			]
		);
		$this->add_control(
			'tab_border_color_hover',
			[
				'label'     => __('Border Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'selectors'    => [
					'{{WRAPPER}} .eae-tab-nav li:hover' => 'border-color: {{VALUE}}'
				]
			]
		);


		$this->add_responsive_control(
			'tab_title_border_radius_hover',
			[
				'label' => __( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .eae-tab-nav li:hover, {{WRAPPER}} .eae-tabs nav li.tab-current ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this -> end_controls_tab();

		$this -> end_controls_tabs();

		$this->add_control(
			'tab_head',
			[
				'label'     => __('', 'wts-eae'),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);

		$this->add_control(
			'separator_color',
			[
				'label'     => __('Separator Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1
				],
				'selectors'    => [
					'{{WRAPPER}}.eae-nav-pos-top nav li.tab-current::before' => 'background: {{VALUE}}',
					'{{WRAPPER}}.eae-nav-pos-top nav li.tab-current::after' => 'background: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'separator_height',
			[
				'label' => __( 'Separator Height', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.eae-nav-pos-top nav li.tab-current::before' => 'height: {{SIZE}}px',
					'{{WRAPPER}}.eae-nav-pos-top nav li.tab-current::after' => 'height: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'nav_width',
			[
				'label' => __( 'Tab Width', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'%' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.eae-nav-pos-left  .eae-tabs nav' => 'width: calc({{SIZE}}% - 1%);',
					'{{WRAPPER}}.eae-nav-pos-left .eae-content' => 'width: calc(100% - {{SIZE}}% - 1%);',
					//'{{WRAPPER}}.eae-nav-pos-top  .eae-tabs nav li' => 'width: {{SIZE}}%;',
				],
			]
		);

		$this->add_control(
			'show_text_mobile',
			[
				'label' => __( 'Title on Mobile', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => __( 'Yes', 'wts-eae' ),
					'no' => __( 'No', 'wts-eae' ),
				],
				'default' => 'no',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .eae-tab-nav li span',
			]

		);


		$this->add_responsive_control(
			'tab_title_align',
			[
				'label' => __( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-tabs nav' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 6,
                        'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-tab-nav li a>:first-child' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eae-tab-nav li a>:first-child ' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
            'tab_content_style',
            [
                 'label'    =>  __('Content', 'wts-eae'),
                 'tab'      => Controls_Manager::TAB_STYLE
            ]
		);

		$this->add_responsive_control(
			'tab_text_align',
			[
				'label' => __( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'wts-eae' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-content section' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eae-content section' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
				'selector'  => '{{WRAPPER}} .eae-content section'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_content_border',
				'selector' => '{{WRAPPER}} .eae-content section',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'tab_content_border_radius',
			[
				'label' => __( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .eae-content section' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings();
		//print_r($this->get_id());
		//echo "<pre>";print_r($settings['eae_tabs']);echo "</pre>";

?>

        <?php if(count($settings['eae_tabs'])){
			?>
			<div id="advance_tabs" class="eae-tabs text-mobile-<?php echo $settings['show_text_mobile'] ?>" data-sticky-menu="<?php echo "eae-nav-sticky-".$settings['tab_Sticky'] ?>" data-scroll-offset="<?php echo $settings['scroll_offset'] ?>" >
                <nav class="eae-tab-nav">
                    <ul>
                        <?php
                        $a_style="";
                        $var1 = 1;
                            foreach ($settings['eae_tabs'] as $tab){
                                if($var1 == 1)
                                {
                                 $cont ="class='tab-current'";
                                }
                                else{
                                     $cont ="";
                                }
                               $var1 ++;
                                ?>
                                    <li <?php echo $cont; ?> ><a href="<?php echo '#'.$tab['_id']; ?> "  >
                                    <?php
                                        if($tab['tab_element']== "none")
                                            $a_style="";
                                        if($tab['tab_element']== "icon")
                                            $a_style = '<i class="'.$tab['tab_icon'].'"></i>';
                                        if($tab['tab_element']== "image")
                                            $a_style = '<img src="'.$tab['tab_image']['url'].'"></img>';

                                        echo $a_style;
                                        ?>
                                      <span><?php echo $tab['tab_title']; ?></span></a></li>
                                <?php
                            }

                        ?>
                    </ul>
                </nav>
                <div class="eae-content">
                    <?php
                            $var = 1;
                            foreach ($settings['eae_tabs'] as $index => $tab){
	                            $eae_tab_content_key = $this->get_repeater_setting_key( 'tab_content', 'eae_tabs', $index );
                                if($var == 1)
                               {
	                               $this->add_render_attribute( $eae_tab_content_key, [
		                                   'class' => [ 'content-current'],
		                                ] );
                               }
                               $var++;

	                            $this->add_inline_editing_attributes( $eae_tab_content_key, 'advanced' );

	                            ?>

                                <section id="<?php echo "#".$tab['_id']; ?>" <?php echo $this->get_render_attribute_string( $eae_tab_content_key); ?> >
                            <?php
                                echo $tab['tab_content'];
                                ?>
                                </section>

                     <?php
                            }
                     ?>

                </div>
            </div>
			<?php
		}
	}

        protected function _content_template() {
        ?>
	        <# if(settings.eae_tabs){ #>
            <div id="advance_tabs" class="eae-tabs text-mobile-{{{settings.show_text_mobile }}}" data-sticky-menu="eae-nav-sticky-{{{settings.tab_Sticky}}}" data-scroll-offset="{{{settings.scroll_offset}}}" >
                <nav class="eae-tab-nav">
                    <ul>
                        <#

                        var var1 = 1;
                        var cont ="";
                        _.each( settings.eae_tabs, function( tab, index ) {
                        var a_style="";
                        if(var1 == 1)
                        {
                        cont ="class='tab-current'";
                        }
                        else{
                        cont ="";
                        }
                        var1 ++;
                        #>
                        <li {{{cont}}} ><a href="{{{'#'+settings.eae_tabs._id }}} "  >
			                    <#
                                view.addRenderAttribute('iconclass','class',tab.tab_icon);
			                    if(tab.tab_element == "none")
				                    a_style="";
                                if(tab.tab_element == "icon")
                                a_style = '<i class="'+ tab.tab_icon + '"></i>';
                                if(tab.tab_element== "image")
                                a_style = '<img src="+ tab.tab_image.url +"/>';
                                #>

                                {{{a_style}}}
                                <span>{{{ tab.tab_title}}}</span></a></li>
                       <#  } ); #>

                    </ul>
                </nav>

                <div class="eae-content">
		            <#
		            var var2 = 1;
                    var cont ="";
                    _.each( settings.eae_tabs, function( tab, index ) {
                    var eae_tab_content_key = view.getRepeaterSettingKey( 'tab_content', 'eae_tabs', index );

                        if(var2 == 1)
			            {
                            view.addRenderAttribute( eae_tab_content_key, {
                            'class': 'content-current',
                            });
			            }
			            else{
				            cont ="";
			            }
			            var2++;
                        view.addInlineEditingAttributes( eae_tab_content_key, 'advanced' );

			            #>
                        <section id="#{{{tab._id}}}" {{{ view.getRenderAttributeString(eae_tab_content_key)}}}>
                            {{{tab.tab_content}}}
                        </section>

			            <#
		            });
		            #>

                </div>
            </div>
            <# } #>
	<?php
	}


}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Advance_Tabs() );