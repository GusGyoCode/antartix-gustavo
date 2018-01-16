<?php
defined('ABSPATH') or die();


class Basement_Carousel_Settings {

    private static $instance = null;

    public function __construct() {

        add_action( 'add_meta_boxes', array( &$this, 'generate_carousel_param_meta_box' ) );
    }

    public static function init() {
        self::instance();
    }

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new Basement_Carousel_Settings();
        }
        return self::$instance;
    }


    /**
     * Register Meta Box
     */
    public function generate_carousel_param_meta_box(){
        add_meta_box(
            'carousel_parameters_meta_box',
            __( 'Parameters', BASEMENT_CAROUSEL_TEXTDOMAIN ),
            array( &$this, 'render_carousel_param_meta_box' ),
            'carousel',
            'normal',
            'core'
        );
    }


    /**
     * Render Meta Box Parameters
     */
    public function render_carousel_param_meta_box(){
        $view  = new Basement_Carousel_Plugin();
        $view->load_views( $this->carousel_settings_generate(), array('carousel-param-meta-box') );
    }


    /**
     * Generate Panel With Carousel Settings
     *
     * @param array $config
     * @return array
     */
    public function carousel_settings_generate( $config = array() ) {
        global $post;

        $config[ 'carousel_settings_row' ] = array(
            'title' => __( 'Style settings for carousel row', BASEMENT_CAROUSEL_TEXTDOMAIN ),
            'short_title' => 'Row settings',
            'fa' => 'fa-paint-brush',
            'active' => true,
            'blocks' => array(
                array(
                    'type' => 'dom',
                    'title' => __( 'Row stretch', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Select stretching options for row and content.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->stretch_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Extra css classes', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Adds extra CSS classes to carousel classes list.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->classes_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Padding', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Adds padding to carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->padding_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Margin', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Adds margin to carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->margin_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Border', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Adds border to carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->border_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Border radius', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Adds border radius to carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->border_radius_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Background', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->background_inputs()
                )
            )
        );

        $config[ 'carousel_settings_general' ] = array(
            'title' => __( 'General settings carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
            'short_title' => 'Carousel settings',
            'fa' => 'fa-sliders',
            'active' => false,
            'blocks' => array(
                array(
                    'type' => 'dom',
                    'title' => __( 'Height carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Sets the height of the carousel. Use integer value w/o "px".', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->height_inputs()
                ),
	            array(
		            'type' => 'dom',
		            'title' => __( 'Swipe', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		            'description' => __( 'Sets whether the carousel should scroll via swiping gestures (on touch-devices only).', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		            'input' => $this->swipe()
	            ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Auto', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Determines whether the carousel should scroll automatically or not.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->auto_inputs()
                ),
                /*array(
                    'type' => 'dom',
                    'title' => __( 'Align', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Whether and how to align the items inside a fixed width/height.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->align_inputs()
                ),*/
                array(
                    'type' => 'dom',
                    'title' => __( 'Width carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The width of the carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->width_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Cookie', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Determines whether the carousel should start at its last viewed position.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->cookie_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Responsive', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Determines whether the carousel should be responsive. If yes, the items will be resized to fill the carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->responsive_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Direction', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The direction to scroll the carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->direction_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Circular', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Determines whether the carousel should be circular.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->circular_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Pause On Hover', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Determines whether the timeout between transitions should be paused "onMouseOver" (only applies when the carousel scrolls automatically).', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->pause_hover_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Effects', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Indicates which effect to use for the transition.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->effects_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Easing', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Indicates which easing function to use for the transition. jQuery.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->easing_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Duration', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Determines the duration of the transition in milliseconds. (Default 500ms).', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->duration_inputs()
                )
            )
        );

        $config[ 'carousel_settings_items' ] = array(
            'title' => __( 'Setting items carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
            'short_title' => 'Items settings',
            'fa' => 'fa-clone',
            'active' => false,
            'blocks' => array(
                /*array(
                        'title' => __( 'Height', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                        'description' => __( 'The height of the items.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                        'inputs' => array(
                                array(
                                        'type' => 'select',
                                        'name' => '_basement_meta_carousel_item_height',
                                        'id' => '_basement_meta_carousel_item_height',
                                        'current_value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_height', true ),
                                        'values' => array(
                                                'variable' => __('Variable', BASEMENT_CAROUSEL_TEXTDOMAIN),
                                                '100%' => __('Auto', BASEMENT_CAROUSEL_TEXTDOMAIN)
                                        )
                                )
                        )
                ),*/
                array(
                    'type' => 'dom',
                    'title' => __( 'Height', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The height of the items. Use only integer value w/o "px" or "%".', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->height_items_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Width', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The width of the items. Use integer value w/o "px".', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->width_items_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Visible', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The number of visible items.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->visible_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Min', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The number of min visible items.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->visible_inputs_min()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Max', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The number of max visible items.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->visible_inputs_max()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Start', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The nth item to start the carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->start_inputs()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Scroll Items', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'The number of items to scroll.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->scroll_inputs()
                ),
	            array(
		            'type' => 'dom',
		            'title' => __( 'Padding Items', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		            'description' => __( 'Sets the top/bottom padding for item.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		            'input' => $this->item_paddings()
	            )
            )
        );

        $config[ 'carousel_settings_dots_and_arrows' ] = array(
            'title' => __( 'Setting arrows &amp; dots', BASEMENT_CAROUSEL_TEXTDOMAIN ),
            'short_title' => 'Arrows &amp; Dots settings',
            'fa' => 'fa-arrows-h',
            'active' => false,
            'blocks' => array(
                array(
                    'type' => 'dom',
                    'title' => __( 'Layout', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'An exemplary display of arrows and points in the carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->layout_builder()
                ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Dots settings', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Sets the style, color, size and position of the dots.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->dots_builder()
                ),
	            array(
		            'type' => 'dom',
		            'title' => __( 'Dots visibility', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		            'description' => __( 'Sets the dots visibility for different resolutions.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		            'input' => $this->dots_visibility()
	            ),
                array(
                    'type' => 'dom',
                    'title' => __( 'Arrows settings', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'description' => __( 'Sets the style, color, size and position of the arrows.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'input' => $this->arrow_builder()
                ),
	            array(
		            'type' => 'dom',
		            'title' => __( 'Arrow visibility', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		            'description' => __( 'Sets the arrows visibility for different resolutions.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		            'input' => $this->arrows_visibility()
	            )
            )
        );

        return $config;
    }


    /**
     * Height carousel items
     */
    protected function height_items_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $column = $container->appendChild( $dom->createElement( 'div' ) );


        $height = array(
            'variable' => __('Auto', BASEMENT_CAROUSEL_TEXTDOMAIN),
            'js_basement_fixed_item_height' => __('Fixed', BASEMENT_CAROUSEL_TEXTDOMAIN)
        );

        $select_unit = new Basement_Form_Input_Select(array(
            'name' => '_basement_meta_carousel_item_height',
            'id' => '_basement_meta_carousel_item_height',
            'current_value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_height', true ),
            'values' => $height
        ));
        $column->appendChild($dom->importNode( $select_unit->create(), true  ));

        $separator = $column->appendChild( $dom->createElement( 'div' ) );
        $separator->setAttribute('style','height:15px;');


        $column = $container->appendChild( $dom->createElement( 'div' ) );
        $column->setAttribute( 'class', 'z_horizontal-list' );
        $column->setAttribute ('style', 'margin-bottom:0px;display:none;');
        $column->setAttribute ('id', 'js_basement_fixed_item_height');

        $input = new Basement_Form_Input(array(
            'label_text' =>  'Height',
            'name' => '_basement_meta_carousel_item_fixed_height',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_fixed_height', true )
        ));
        $column->appendChild($dom->importNode( $input->create(), true ));

        return $dom->saveHTML($container);
    }


    /**
     * Width inputs
     */
    protected function width_items_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_min-block' );

        $input = new Basement_Form_Input(array(
            'name' => '_basement_meta_carousel_item_width',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_width', true )
        ));
        $container->appendChild($dom->importNode( $input->create(), true  ));


        return $dom->saveHTML($container);
    }


    /**
     * Min visible elements
     */
    protected function visible_inputs_min() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_min-block' );

        $input = new Basement_Form_Input(array(
            'name' => '_basement_meta_carousel_item_visible_min',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_visible_min', true )
        ));
        $container->appendChild($dom->importNode( $input->create(), true  ));


        return $dom->saveHTML($container);
    }


    /**
     * Max visible elements
     */
    protected function visible_inputs_max() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_min-block' );

        $input = new Basement_Form_Input(array(
            'name' => '_basement_meta_carousel_item_visible_max',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_visible_max', true )
        ));
        $container->appendChild($dom->importNode( $input->create(), true  ));


        return $dom->saveHTML($container);
    }


	/**
	 * Swipe settings
	 */
	protected function swipe() {
		global $post;

		$select_params = array(
			'meta_name' => '_basement_meta_carousel_swipe',
			'values' => array(
				'disable' => __('Disable', BASEMENT_CAROUSEL_TEXTDOMAIN),
				'enable' => __('Enable', BASEMENT_CAROUSEL_TEXTDOMAIN)
			)
		);

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$select = new Basement_Form_Input_Select(array(
			'name' => $select_params['meta_name'],
			'id' => $select_params['meta_name'],
			'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
			'values' => $select_params['values']
		));
		$container->appendChild($dom->importNode( $select->create(), true  ));

		return $dom->saveHTML($container);
	}



    /**
     * Width settings
     */
    protected function width_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_width',
            'values' => array(
                'auto' => __('Auto', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'variable' => __('Variable', BASEMENT_CAROUSEL_TEXTDOMAIN),
                '100%' => __('100%', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
	 * Arrows settings
	 */
    protected function arrow_builder() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_horizontal-list' );
        $container->setAttribute( 'id', 'js-arrowSettings' );

        $dots_settings = array(
            'Type' => array(
                'settings' => array(
                    'nope' => __( 'Nope', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'wobg' => __( 'W/o background', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'bg' => __( 'With background', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'type',
                'disable' => false,
                'current_value' => 'wobg',
                'meta_name' => '_basement_meta_carousel_arrow_type'
            ),
            'Color' => array(
                'settings' => array(
                    'light' => __( 'Light', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'standart' => __( 'Standart', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'dark' => __( 'Dark', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'color-arrows',
                'disable' => false,
                'current_value' => 'standart',
                'meta_name' => '_basement_meta_carousel_arrow_color'
            ),
            'Size' => array(
                'settings' => array(
                    'small' => __( 'Small', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'medium' => __( 'Medium', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'large' => __( 'Large', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'size-arrows',
                'disable' => false,
                'current_value' => 'medium',
                'meta_name' => '_basement_meta_carousel_arrow_size'
            ),
            'Position' => array(
                'settings' => array(
                    'inside' => __( 'Inside', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'outside' => __( 'Outside', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'inrow' => __( 'In row', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'position',
                'disable' => false,
                'current_value' => 'inside',
                'meta_name' => '_basement_meta_carousel_arrow_position'
            ),
            'Vertical Position' => array(
                'settings' => array(
                    'top' => __( 'Top', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'bottom' => __( 'Bottom', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'side' => __( 'Side', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'y',
                'disable' => false,
                'current_value' => 'side',
                'meta_name' => '_basement_meta_carousel_arrow_position_vertical'
            ),
            'Horizontal Position' => array(
                'settings' => array(
                    'left' => __( 'Left', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'center' => __( 'Center', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'right' => __( 'Right', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'x',
                'disable' => true,
                'current_value' => 'center',
                'meta_name' => '_basement_meta_carousel_arrow_position_horizontal'
            )
        );

        foreach( $dots_settings as $title => $settings ) {

            $column = $container->appendChild( $dom->createElement( 'div' ) );

            $value = get_post_meta( $post->ID, $settings['meta_name'], true );

            $atts = array(
                'data-control' => $settings['control']
            );

            if( $settings['disable'] ) {
                $atts['disabled'] = 'disabled';
            }



            $select_unit = new Basement_Form_Input_Radio_Group( array(
                'label_text' => $title,
                'name' => $settings['meta_name'],
                'id' => $settings['meta_name'],
                'current_value' => empty( $value ) ? $settings['current_value'] : $value,
                'values' => $settings['settings'],
                'attributes' => $atts
            ) );



            $column->appendChild($dom->importNode( $select_unit->create(), true  ));
        }

        return $dom->saveHTML($container);
    }


    /**
     * Dots settings
     */
    protected function dots_builder() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_horizontal-list' );
        $container->setAttribute( 'id', 'js-dotsSettings' );

        $dots_settings = array(
            'Type' => array(
                'settings' => array(
                    'nope' => __( 'Nope', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'dots' => __( 'Dots', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'number' => __( 'Numbers', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'type',
                'current_value' => 'dots',
                'meta_name' => '_basement_meta_carousel_dots_type'
            ),
            'Color' => array(
                'settings' => array(
                    'light' => __( 'Light', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'standart' => __( 'Standart', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'dark' => __( 'Dark', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'color-dots',
                'current_value' => 'standart',
                'meta_name' => '_basement_meta_carousel_dots_color'
            ),
            'Size' => array(
                'settings' => array(
                    'small' => __( 'Small', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'medium' => __( 'Medium', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'large' => __( 'Large', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'size-dots',
                'current_value' => 'medium',
                'meta_name' => '_basement_meta_carousel_dots_size'
            ),
            'Position' => array(
                'settings' => array(
                    'inside' => __( 'Inside', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'outside' => __( 'Outside', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'position',
                'current_value' => 'inside',
                'meta_name' => '_basement_meta_carousel_dots_position'
            ),
            'Vertical Position' => array(
                'settings' => array(
                    'top' => __( 'Top', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'bottom' => __( 'Bottom', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'y',
                'current_value' => 'bottom',
                'meta_name' => '_basement_meta_carousel_dots_position_vertical'
            ),
            'Horizontal Position' => array(
                'settings' => array(
                    'left' => __( 'Left', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'center' => __( 'Center', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                    'right' => __( 'Right', BASEMENT_CAROUSEL_TEXTDOMAIN )
                ),
                'control' => 'x',
                'current_value' => 'center',
                'meta_name' => '_basement_meta_carousel_dots_position_horizontal'
            )
        );

        foreach( $dots_settings as $title => $settings ) {

            $column = $container->appendChild( $dom->createElement( 'div' ) );

            $value = get_post_meta( $post->ID, $settings['meta_name'], true );

            $select_unit = new Basement_Form_Input_Radio_Group(array(
                'label_text' => $title,
                'name' => $settings['meta_name'],
                'id' => $settings['meta_name'],
                'current_value' => empty( $value ) ? $settings['current_value'] : $value,
                'values' => $settings['settings'],
                'attributes' => array(
                    'data-control' => $settings['control']
                )
            ));
            $column->appendChild($dom->importNode( $select_unit->create(), true  ));
        }

        return $dom->saveHTML($container);
    }


	/**
	 * Dots visibility
	 */
    protected function dots_visibility() {
	    global $post;

	    $dom = new DOMDocument( '1.0', 'UTF-8' );

	    $container = $dom->appendChild( $dom->createElement( 'div' ) );
	    $container->setAttribute( 'class', 'z_horizontal-list' );

	    $vosibility = array(
	    	'lg' => __('Large devices',BASEMENT_CAROUSEL_TEXTDOMAIN),
		    'md' => __('Medium devices',BASEMENT_CAROUSEL_TEXTDOMAIN),
		    'sm' => __('Small devices',BASEMENT_CAROUSEL_TEXTDOMAIN),
		    'xs' => __('Extra small devices',BASEMENT_CAROUSEL_TEXTDOMAIN)
	    );

	    foreach ($vosibility as $screen => $label ) {

		    $column = $container->appendChild( $dom->createElement( 'div' ) );
			$option = '_basement_meta_carousel_dots_' . $screen;

		    $value = get_post_meta( $post->ID, $option, true );
		    $select = new Basement_Form_Input_Select( array(
		    	'label_text' => $label,
			    'values'  => array(
				    ''  => __( '&mdash; Select &mdash;', BASEMENT_CAROUSEL_TEXTDOMAIN ),
				    'dots-visible-' . $screen => __( 'Show', BASEMENT_CAROUSEL_TEXTDOMAIN ),
				    'dots-hidden-' . $screen  => __( 'Hide', BASEMENT_CAROUSEL_TEXTDOMAIN )
			    ),
			    'name' => $option,
			    'id' => $option,
			    'current_value' => $value
		    ) );

		    $column->appendChild($dom->importNode( $select->create(), true  ));
	    }

	    return $dom->saveHTML($container);
    }


	/**
	 * Arrows visibility
	 */
	protected function arrows_visibility() {
		global $post;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'z_horizontal-list' );

		$vosibility = array(
			'lg' => __('Large devices',BASEMENT_CAROUSEL_TEXTDOMAIN),
			'md' => __('Medium devices',BASEMENT_CAROUSEL_TEXTDOMAIN),
			'sm' => __('Small devices',BASEMENT_CAROUSEL_TEXTDOMAIN),
			'xs' => __('Extra small devices',BASEMENT_CAROUSEL_TEXTDOMAIN)
		);

		foreach ($vosibility as $screen => $label ) {

			$column = $container->appendChild( $dom->createElement( 'div' ) );
			$option = '_basement_meta_carousel_arrows_' . $screen;

			$value = get_post_meta( $post->ID, $option, true );
			$select = new Basement_Form_Input_Select( array(
				'label_text' => $label,
				'values'  => array(
					''  => __( '&mdash; Select &mdash;', BASEMENT_CAROUSEL_TEXTDOMAIN ),
					'arrows-visible-' . $screen => __( 'Show', BASEMENT_CAROUSEL_TEXTDOMAIN ),
					'arrows-hidden-' . $screen  => __( 'Hide', BASEMENT_CAROUSEL_TEXTDOMAIN )
				),
				'name' => $option,
				'id' => $option,
				'current_value' => $value
			) );

			$column->appendChild($dom->importNode( $select->create(), true  ));
		}

		return $dom->saveHTML($container);
	}



	/**
     * Arrows/Dots Mini Preview
     */
    protected function layout_builder() {

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div') );
        $container->setAttribute( 'class', 'layout-wrapper-builder' );

        $row = $container->appendChild( $dom->createElement( 'div' ) );
        $row->setAttribute( 'class', 'layout-row-builder' );

        $carousel = $row->appendChild( $dom->createElement( 'div' ) );
        $carousel->setAttribute( 'class', 'layout-carousel-builder' );

        $dots = $carousel->appendChild( $dom->createElement( 'div' ) );
        $dots->setAttribute( 'class', 'layout-dots-builder' );

        $nav_dots = $dots->appendChild( $dom->createElement( 'ul' ) );
        $nav_dots->setAttribute( 'class', 'layout-nav-builder' );
        $i1 = $nav_dots->appendChild( $dom->createElement( 'li' ) );
        $i2 = $nav_dots->appendChild( $dom->createElement( 'li' ) );
        $i3 = $nav_dots->appendChild( $dom->createElement( 'li' ) );

        $arrows = $carousel->appendChild( $dom->createElement( 'div' ) );
        $arrows->setAttribute( 'class', 'layout-arrows-builder' );

        $left = $arrows->appendChild( $dom->createElement( 'i' ) );
        $left->setAttribute( 'class', 'layout-arrow-left' );

        $right = $arrows->appendChild( $dom->createElement( 'i' ) );
        $right->setAttribute( 'class', 'layout-arrow-right' );


        $dots_arrow = $carousel->appendChild( $dom->createElement( 'div' ) );
        $dots_arrow->setAttribute( 'class', 'layout-dots-arrows-builder none' );


        $left = $dom->importNode( $left, true );
        $prev = $dots_arrow->appendChild( $left->cloneNode() );

        $nav_dots = $dom->importNode( $nav_dots, true );
        $nav = $dots_arrow->appendChild( $nav_dots->cloneNode(TRUE) );

        $right = $dom->importNode( $right, true );
        $next = $dots_arrow->appendChild( $right->cloneNode() );

        return $dom->saveHTML($container);
    }


    /**
     * Auto settings
     */
    protected function auto_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_auto',
            'values' => array(
                'false' => __('No', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'true' => __('Yes', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Align settings
     */
    protected function align_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_align',
            'values' => array(
                '' => __('Nope', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'center' => __('Center', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'left' => __('Left', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'right' => __('Right', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Cookie settings
     */
    protected function cookie_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_cookie',
            'values' => array(
                'false' => __('No', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'true' => __('Yes', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Responsive settings
     */
    protected function responsive_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_responsive',
            'values' => array(
                'true' => __('Yes', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'false' => __('No', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Direction settings
     */
    protected function direction_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_direction',
            'values' => array(
                'left' => __('Left', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'right' => __('Right', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Circular settings
     */
    protected function circular_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_circular',
            'values' => array(
                'true' => __('Yes', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'false' => __('No', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Pause on Hover settings
     */
    protected function pause_hover_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_pause',
            'values' => array(
                'false' => __('No', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'true' => __('Yes', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Effects settings
     */
    protected function effects_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_effects',
            'values' => array(
	            'fade' => __('Fade', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'crossfade' => __('Crossfade', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'scroll' => __('Scroll', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'none' => __('None', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'directscroll' => __('Directscroll', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'cover' => __('Cover', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'cover-fade' => __('Cover-fade', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'uncover' => __('Uncover', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'uncover-fade' => __('Uncover-fade', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Easing settings
     */
    protected function easing_inputs() {
        global $post;

        $select_params = array(
            'meta_name' => '_basement_meta_carousel_easing',
            'values' => array(
                'swing' => __('Swing', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'linear' => __('Linear', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'quadratic' => __('Quadratic', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'cubic' => __('Cubic', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'elastic' => __('Elastic', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select = new Basement_Form_Input_Select(array(
            'name' => $select_params['meta_name'],
            'id' => $select_params['meta_name'],
            'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
            'values' => $select_params['values']
        ));
        $container->appendChild($dom->importNode( $select->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Duration settings
     */
    protected function duration_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_min-block' );

        $input = new Basement_Form_Input(array(
            'name' => '_basement_meta_carousel_duration',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_duration', true )
        ));
        $container->appendChild($dom->importNode( $input->create(), true  ));


        return $dom->saveHTML($container);
    }


    /**
     * Visible settings
     */
    protected function visible_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_min-block' );

        $input = new Basement_Form_Input(array(
            'name' => '_basement_meta_carousel_item_visible',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_visible', true )
        ));
        $container->appendChild($dom->importNode( $input->create(), true  ));


        return $dom->saveHTML($container);
    }


    /**
     * Start settings
     */
    protected function start_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_min-block' );

        $input = new Basement_Form_Input(array(
            'name' => '_basement_meta_carousel_item_start',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_start', true )
        ));
        $container->appendChild($dom->importNode( $input->create(), true  ));


        return $dom->saveHTML($container);
    }


    /**
     * Scroll settings
     */
    protected function scroll_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_min-block' );

        $input = new Basement_Form_Input(array(
            'name' => '_basement_meta_carousel_item_scroll',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_scroll', true )
        ));
        $container->appendChild($dom->importNode( $input->create(), true  ));


        return $dom->saveHTML($container);
    }


	/**
	 * Item Paddings
	 */
	protected function item_paddings() {
		global $post;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'z_horizontal-list' );


		$div1 = $container->appendChild( $dom->createElement( 'div' ) );

		$input = new Basement_Form_Input(array(
			'label_text' =>  __('Top', BASEMENT_TEXTDOMAIN),
			'name' => '_basement_meta_carousel_item_padding_top',
			'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_padding_top', true )
		));
		$div1->appendChild($dom->importNode( $input->create(), true  ));



		$div2 = $container->appendChild( $dom->createElement( 'div' ) );

		$input = new Basement_Form_Input(array(
			'label_text' =>  __('Bottom', BASEMENT_TEXTDOMAIN),
			'name' => '_basement_meta_carousel_item_padding_bottom',
			'value' => get_post_meta( $post->ID, '_basement_meta_carousel_item_padding_bottom', true )
		));
		$div2->appendChild($dom->importNode( $input->create(), true  ));


		return $dom->saveHTML($container);
	}


    /**
     * Extra classes
     */
    protected function classes_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $input = new Basement_Form_Input(array(
            'name' => '_basement_meta_carousel_class',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_class', true )
        ));
        $container->appendChild($dom->importNode( $input->create(), true  ));


        return $dom->saveHTML($container);
    }


    /**
     * Stretch type
     */
    protected function stretch_inputs() {
        global $post;

        $stretch = array(
            'meta_name' => '_basement_meta_carousel_base_stretch',
            'values' => array(
                '' => __('Nope', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'strow' => __('Stretch row', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'strow_cont' => __('Stretch row and content', BASEMENT_CAROUSEL_TEXTDOMAIN)
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $select_stretch = new Basement_Form_Input_Select(array(
            'name' => $stretch['meta_name'],
            'id' => $stretch['meta_name'],
            'current_value' => get_post_meta( $post->ID, $stretch['meta_name'], true ),
            'values' => $stretch['values']
        ));
        $container->appendChild($dom->importNode( $select_stretch->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Settings padding (for panel settings)
     */
    protected function padding_inputs() {
        global $post;

        $padding = array(
            array(
                'title' => __( 'Left', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'left'
            ),
            array(
                'title' => __( 'Top', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'top'
            ),
            array(
                'title' => __( 'Right', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'right'
            ),
            array(
                'title' => __( 'Bottom', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'bottom'
            )
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_horizontal-list' );

        foreach($padding as $padding_value => $item) {

            $column = $container->appendChild( $dom->createElement( 'div' ) );

            $input = new Basement_Form_Input(array(
                'label_text' =>  $item['title'],
                'name' => '_basement_meta_carousel_padding_' . $item['position'],
                'value' => get_post_meta( $post->ID, '_basement_meta_carousel_padding_' . $item['position'], true )
            ));
            $column->appendChild($dom->importNode( $input->create(), true  ));

        }

        return $dom->saveHTML($container);
    }


    /**
     * Settings border (for panel settings)
     */
    protected function border_inputs() {
        global $post;

        $border_position = array(
            array(
                'title' => __( 'Left', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'left'
            ),
            array(
                'title' => __( 'Top', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'top'
            ),
            array(
                'title' => __( 'Right', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'right'
            ),
            array(
                'title' => __( 'Bottom', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'bottom'
            )
        );

        $border_settings = array(
            'unit' => array(
                'title' => __('Unit', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'border_unit' => array(
                    '' => __('Nope', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'px' => __('px', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'em' => __('em', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'pt' => __('pt', BASEMENT_CAROUSEL_TEXTDOMAIN),
                ),
                'meta_name' => '_basement_meta_carousel_border_unit'
            ),
            'style' => array(
                'title' => __('Style', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'border_type' => array(
                    '' => __('Nope', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'dotted' => __('Dotted', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'dashed' => __('Dashed', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'solid' => __('Solid', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'double' => __('Double', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'groove' => __('Groove', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'ridge' => __('Ridge', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'inset' => __('Inset', BASEMENT_CAROUSEL_TEXTDOMAIN),
                    'outset' => __('Outset', BASEMENT_CAROUSEL_TEXTDOMAIN)
                ),
                'meta_name' => '_basement_meta_carousel_border_style'
            ),
            'color' => array(
                'title' => __('Color', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'border_color' => 'transparent',
                'meta_name' => '_basement_meta_carousel_border_color'
            )
        );


        $dom = new DOMDocument( '1.0', 'UTF-8' );


        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $line1 = $container->appendChild( $dom->createElement( 'div' ) );
        $line1->setAttribute( 'class', 'z_horizontal-list' );

        foreach($border_position as $position_value => $item) {
            $column = $line1->appendChild( $dom->createElement( 'div' ) );



            $input = new Basement_Form_Input(array(
                'label_text' =>  $item['title'],
                'name' => '_basement_meta_carousel_border_' . $item['position'],
                'value' => get_post_meta( $post->ID, '_basement_meta_carousel_border_' . $item['position'], true )
            ));
            $column->appendChild($dom->importNode( $input->create(), true  ));
        }

        $line2 = $container->appendChild( $dom->createElement( 'div' ) );
        $line2->setAttribute( 'class', 'z_horizontal-list' );

        foreach($border_settings as $settings_value => $item) {


            $column = $line2->appendChild( $dom->createElement( 'div' ) );


            if($settings_value === 'unit') {
                $select_unit = new Basement_Form_Input_Select(array(
                    'label_text' => $item['title'],
                    'name' => $item['meta_name'],
                    'id' => $item['meta_name'],
                    'current_value' => get_post_meta( $post->ID, $item['meta_name'], true ),
                    'values' => $item['border_unit']
                ));
                $column->appendChild($dom->importNode( $select_unit->create(), true  ));
            }


            if($settings_value === 'style') {
                $select_style = new Basement_Form_Input_Select(array(
                    'label_text' => $item['title'],
                    'name' => $item['meta_name'],
                    'id' => $item['meta_name'],
                    'current_value' => get_post_meta( $post->ID, $item['meta_name'], true ),
                    'values' => $item['border_type']
                ));
                $column->appendChild($dom->importNode( $select_style->create(), true  ));

            }

            if($settings_value === 'color') {
                $colorpicker = new Basement_Form_Input_Colorpicker(array(
                    'label_text' => $item['title'],
                    'name' => '_basement_meta_carousel_border_color',
                    'value' => get_post_meta( $post->ID, $item['meta_name'], true )
                ));
                $column->appendChild ( $dom->importNode( $colorpicker->create(), true  ) );
            }
        }

        return $dom->saveHTML($container);
    }


    /**
     * Settings margin (for panel settings)
     */
    protected function margin_inputs() {
        global $post;

        $margin = array(
            array(
                'title' => __( 'Left', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'left'
            ),
            array(
                'title' => __( 'Top', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'top'
            ),
            array(
                'title' => __( 'Right', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'right'
            ),
            array(
                'title' => __( 'Bottom', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'bottom'
            ),
        );

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_horizontal-list' );


        foreach($margin as $margin_value => $item) {

            $column = $container->appendChild( $dom->createElement( 'div' ) );


            $input = new Basement_Form_Input(array(
                'label_text' =>  $item['title'],
                'name' => '_basement_meta_carousel_margin_' . $item['position'],
                'value' => get_post_meta( $post->ID, '_basement_meta_carousel_margin_' . $item['position'], true )
            ));
            $column->appendChild($dom->importNode( $input->create(), true  ));

        }

        return $dom->saveHTML($container);
    }


    /**
     * Settings border-radius (for panel settings)
     */
    protected function border_radius_inputs() {
        global $post;

        $border_radius_position = array(
            array(
                'title' => __( 'Left', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'left'
            ),
            array(
                'title' => __( 'Top', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'top'
            ),
            array(
                'title' => __( 'Right', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'right'
            ),
            array(
                'title' => __( 'Bottom', BASEMENT_CAROUSEL_TEXTDOMAIN ),
                'position' => 'bottom'
            ),
        );

        $border_radius_settings = array(
            'title' => __('Unit', BASEMENT_CAROUSEL_TEXTDOMAIN),
            'border_unit' => array(
                '' => __('Nope', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'px' => __('px', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'em' => __('em', BASEMENT_CAROUSEL_TEXTDOMAIN),
                'pt' => __('pt', BASEMENT_CAROUSEL_TEXTDOMAIN),
            ),
            'meta_name' => '_basement_meta_carousel_border_radius_unit'
        );


        $dom = new DOMDocument( '1.0', 'UTF-8' );


        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'z_horizontal-list' );

        foreach($border_radius_position as $position_value => $item) {
            $column = $container->appendChild( $dom->createElement( 'div' ) );


            $input = new Basement_Form_Input(array(
                'label_text' =>  $item['title'],
                'name' => '_basement_meta_carousel_border_radius_' . $item['position'],
                'value' => get_post_meta( $post->ID, '_basement_meta_carousel_border_radius_' . $item['position'], true )
            ));
            $column->appendChild($dom->importNode( $input->create(), true  ));
        }


        $column = $container->appendChild( $dom->createElement( 'div' ) );

        $select_unit = new Basement_Form_Input_Select(array(
            'label_text' => $border_radius_settings['title'],
            'name' => $border_radius_settings['meta_name'],
            'id' => $border_radius_settings['meta_name'],
            'current_value' => get_post_meta( $post->ID, $border_radius_settings['meta_name'], true ),
            'values' => $border_radius_settings['border_unit']
        ));
        $column->appendChild($dom->importNode( $select_unit->create(), true  ));

        return $dom->saveHTML($container);
    }


    /**
     * Background
     */
    protected function background_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $background = new Basement_Form_Input_Carousel_Background(array(
            'name_attr_part' => '_basement_meta_carousel_background',
            'options' => get_post_meta( $post->ID, '_basement_meta_carousel_background', true )
        ));
        $container->appendChild($dom->importNode( $background->create(), true  ));


        return $dom->saveHTML($container);
    }


    /**
     * Settings height carousel (for panel settings)
     */
    protected function height_inputs() {
        global $post;

        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );
        $column = $container->appendChild( $dom->createElement( 'div' ) );


        $height = array(
            'variable' => __('Auto', BASEMENT_CAROUSEL_TEXTDOMAIN),
            'auto' => __('At the highest slide', BASEMENT_CAROUSEL_TEXTDOMAIN),
            'js_basement_fixed_height' => __('Fixed', BASEMENT_CAROUSEL_TEXTDOMAIN)
        );

        $select_unit = new Basement_Form_Input_Select(array(
            'name' => '_basement_meta_carousel_height',
            'id' => '_basement_meta_carousel_height',
            'current_value' => get_post_meta( $post->ID, '_basement_meta_carousel_height', true ),
            'values' => $height
        ));
        $column->appendChild($dom->importNode( $select_unit->create(), true  ));

        $separator = $column->appendChild( $dom->createElement( 'div' ) );
        $separator->setAttribute('style','height:15px;');


        $column = $container->appendChild( $dom->createElement( 'div' ) );
        $column->setAttribute( 'class', 'z_horizontal-list' );
        $column->setAttribute ('style', 'margin-bottom:0px;display:none;');
        $column->setAttribute ('id', 'js_basement_fixed_height');

        $input = new Basement_Form_Input(array(
            'label_text' =>  'Height (in px)',
            'name' => '_basement_meta_carousel_fixed_height',
            'value' => get_post_meta( $post->ID, '_basement_meta_carousel_fixed_height', true )
        ));
        $column->appendChild($dom->importNode( $input->create(), true ));

        return $dom->saveHTML($container);
    }

}
Basement_Carousel_Settings::init();