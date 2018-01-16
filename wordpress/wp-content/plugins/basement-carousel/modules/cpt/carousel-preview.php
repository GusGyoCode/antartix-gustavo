<?php
defined('ABSPATH') or die();


class Basement_Carousel_Preview {

    private static $instance = null;

    public function __construct() {

        add_action( 'add_meta_boxes', array( &$this, 'generate_carousel_preview_meta_box' ) );
    }

    public static function init() {
        self::instance();
    }

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new Basement_Carousel_Preview();
        }
        return self::$instance;
    }


    /**
     * Register Meta Box
     */
    public function generate_carousel_preview_meta_box(){
        add_meta_box(
            'carousel-preview-meta-box',
            __( 'Preview', BASEMENT_CAROUSEL_TEXTDOMAIN ),
            array( &$this, 'render_carousel_preview_meta_box' ),
            'carousel',
            'normal',
            'high'
        );
    }


    /**
     * Render Meta Box Preview
     *
     * @param $post
     */
    public function render_carousel_preview_meta_box( $post ) {
        $dom = new DOMDocument( '1.0', 'UTF-8' );

        $container = $dom->appendChild( $dom->createElement( 'div' ) );

        $error = $container->appendChild( $dom->createElement( 'div', __('In the preview shows only an approximate carousel will look like!', BASEMENT_CAROUSEL_TEXTDOMAIN) ) );
        $error->setAttribute( 'class', 'custom-notice-meta-box' );


        $carousel_area = $container->appendChild( $dom->createElement( 'div' ) );
        $carousel_area->setAttribute( 'class', 'carousel-area' );

        $meta = get_post_meta($post->ID);

        $bg = array();

        foreach ($meta as $key => $val) {
            if( $key !== '_basement_meta_carousel_background' ) {
                $meta[$key] = array_shift($val);
            } else {
                $bg['_basement_meta_carousel_background'] = get_post_meta($post->ID, '_basement_meta_carousel_background', true);

            }
        }

        extract( $meta );
        extract( $bg );

        $style = '';
        $classes = array();

        /*
         * Pushed classes
         */
        if( $_basement_meta_carousel_class ) {
            $classes[] = $_basement_meta_carousel_class;
        }



        /*
         * Padding for row
         */
        if( (int)$_basement_meta_carousel_padding_left >= 0 && ($_basement_meta_carousel_padding_left || $_basement_meta_carousel_padding_left === '0') ) {
            $style .= 'padding-left:' . (int)$_basement_meta_carousel_padding_left . 'px;';
        }
        if( (int)$_basement_meta_carousel_padding_top >= 0 && ($_basement_meta_carousel_padding_top || $_basement_meta_carousel_padding_top === '0') ) {
            $style .= 'padding-top:' . (int)$_basement_meta_carousel_padding_top . 'px;';
        }
        if( (int)$_basement_meta_carousel_padding_right >= 0 && ($_basement_meta_carousel_padding_right || $_basement_meta_carousel_padding_right === '0') ) {
            $style .= 'padding-right:' . (int)$_basement_meta_carousel_padding_right . 'px;';
        }
        if( (int)$_basement_meta_carousel_padding_bottom >= 0 && ($_basement_meta_carousel_padding_bottom || $_basement_meta_carousel_padding_bottom === '0') ) {
            $style .= 'padding-bottom:' . (int)$_basement_meta_carousel_padding_bottom . 'px;';
        }


        /*
         * Border for row
         */
        if(((int)$_basement_meta_carousel_border_left || (float)$_basement_meta_carousel_border_left) && $_basement_meta_carousel_border_unit && $_basement_meta_carousel_border_style && $_basement_meta_carousel_border_color) {
            $style .= 'border-left: '.$_basement_meta_carousel_border_left.$_basement_meta_carousel_border_unit.' '.$_basement_meta_carousel_border_style.' '.$_basement_meta_carousel_border_color.';';
        }

        if(((int)$_basement_meta_carousel_border_top || (float)$_basement_meta_carousel_border_top) && $_basement_meta_carousel_border_unit && $_basement_meta_carousel_border_style && $_basement_meta_carousel_border_color) {
            $style .= 'border-top: '.$_basement_meta_carousel_border_top.$_basement_meta_carousel_border_unit.' '.$_basement_meta_carousel_border_style.' '.$_basement_meta_carousel_border_color.';';
        }

        if(((int)$_basement_meta_carousel_border_right || (float)$_basement_meta_carousel_border_right) && $_basement_meta_carousel_border_unit && $_basement_meta_carousel_border_style && $_basement_meta_carousel_border_color) {
            $style .= 'border-right: '.$_basement_meta_carousel_border_right.$_basement_meta_carousel_border_unit.' '.$_basement_meta_carousel_border_style.' '.$_basement_meta_carousel_border_color.';';
        }

        if(((int)$_basement_meta_carousel_border_bottom || (float)$_basement_meta_carousel_border_bottom) && $_basement_meta_carousel_border_unit && $_basement_meta_carousel_border_style && $_basement_meta_carousel_border_color) {
            $style .= 'border-bottom: '.$_basement_meta_carousel_border_bottom.$_basement_meta_carousel_border_unit.' '.$_basement_meta_carousel_border_style.' '.$_basement_meta_carousel_border_color.';';
        }


        /*
         * Margin for row
         */
        if( (int)$_basement_meta_carousel_margin_left || $_basement_meta_carousel_margin_left === '0' ) {
            $style .= 'margin-left:' . (int)$_basement_meta_carousel_margin_left . 'px;';
        }
        if( (int)$_basement_meta_carousel_margin_top || $_basement_meta_carousel_margin_top === '0' ) {
            $style .= 'margin-top:' . (int)$_basement_meta_carousel_margin_top . 'px;';
        }
        if( (int)$_basement_meta_carousel_margin_right || $_basement_meta_carousel_margin_right === '0' ) {
            $style .= 'margin-right:' . (int)$_basement_meta_carousel_margin_right . 'px;';
        }
        if( (int)$_basement_meta_carousel_margin_bottom || $_basement_meta_carousel_margin_bottom === '0' ) {
            $style .= 'margin-bottom:' . (int)$_basement_meta_carousel_margin_bottom . 'px;';
        }


        /*
         * Border radius for row
         */
        if(((int)$_basement_meta_carousel_border_radius_left || (int)$_basement_meta_carousel_border_radius_top || (int)$_basement_meta_carousel_border_radius_right || (int)$_basement_meta_carousel_border_radius_bottom) && $_basement_meta_carousel_border_radius_unit){

            $bl = $_basement_meta_carousel_border_radius_left;
            $bt = $_basement_meta_carousel_border_radius_top;
            $br = $_basement_meta_carousel_border_radius_right;
            $bb = $_basement_meta_carousel_border_radius_bottom;
            $u = $_basement_meta_carousel_border_radius_unit;

            if($u === 'per') {
                $u = "%%";
            }

            $style .= '-webkit-border-radius: '.(empty($bl) ? '0' : $bl).$u.' '.(empty($bt) ? '0' : $bt).$u.' '.(empty($br) ? '0' : $br).$u.' '.(empty($bb) ? '0' : $bb).$u.'; -moz-border-radius: '.(empty($bl) ? '0' : $bl).$u.' '.(empty($bt) ? '0' : $bt).$u.' '.(empty($br) ? '0' : $br).$u.' '.(empty($bb) ? '0' : $bb).$u.'; border-radius: '.(empty($bl) ? '0' : $bl).$u.' '.(empty($bt) ? '0' : $bt).$u.' '.(empty($br) ? '0' : $br).$u.' '.(empty($bb) ? '0' : $bb).$u.';';
        }


        /*
         * Background for row
         */
        if( $_basement_meta_carousel_background['image'] ) {
            $style .= 'background-image:url('.wp_get_attachment_url($_basement_meta_carousel_background['image']).');';

            if( $_basement_meta_carousel_background['attachment'] && $_basement_meta_carousel_background['attachment'] !== 'nope' ) {
                $style .= 'background-attachment: '.$_basement_meta_carousel_background['attachment'].';';
            }

            if( $_basement_meta_carousel_background['repeat'] && $_basement_meta_carousel_background['repeat'] !== 'nope' ) {
                $style .= 'background-repeat: '.$_basement_meta_carousel_background['repeat'].';';
            }

            if( $_basement_meta_carousel_background['position'] && $_basement_meta_carousel_background['position'] !== 'nope' ) {
                $style .= 'background-position: '.$_basement_meta_carousel_background['position'].';';
            }

            if( $_basement_meta_carousel_background['size'] && $_basement_meta_carousel_background['size'] !== 'nope' ) {
                $style .= 'background-size: '.$_basement_meta_carousel_background['size'].';';
            }
        }

        if($_basement_meta_carousel_background['color']) {
            $color_l1 = $this->hexToRgb($_basement_meta_carousel_background['color']);

            $alpha_l1 = 1;
            if( (float)$_basement_meta_carousel_background['opacity']) {
                $alpha_l1 = $_basement_meta_carousel_background['opacity'];
            }

            $rgba_l1 = $color_l1['red'].', '.$color_l1['green'].', '.$color_l1['blue'].', '.$alpha_l1.'';
            $style .= 'background-color: rgba('.$rgba_l1.');';
        }


        /*
         * Container
         */
        $container = $carousel_area->appendChild( $dom->createElement( 'div' ) );
        $container->setAttribute( 'class', 'preview-container');




        /*
         * Carousel row
         */
        $carousel_row = $container->appendChild( $dom->createElement( 'div' ) );
        $carousel_row->setAttribute( 'class', 'preview-carousel-row ' . implode(' ', $classes) );
        if( $_basement_meta_carousel_base_stretch ) {
            $carousel_row->setAttribute( 'data-stretch', $_basement_meta_carousel_base_stretch );
        }


        /*
         * Helper row (for stretch)
         */
        $helper_row = $container->appendChild( $dom->createElement( 'div' ) );
        $helper_row->setAttribute( 'class', 'full-width-basement' );


        if( $style ) {
            $carousel_row->setAttribute( 'style', $style );
        }


        /*
         * Params for caroufredsel
         */
        $caroufredsel_params = array ();


        if( $_basement_meta_carousel_height ) {
            if($_basement_meta_carousel_height === 'js_basement_fixed_height') {

                $caroufredsel_params['height'] = (int)$_basement_meta_carousel_fixed_height ? (int)$_basement_meta_carousel_fixed_height : 'variable';

            } else {
                $caroufredsel_params['height'] = $_basement_meta_carousel_height;
            }
        }

        if( $_basement_meta_carousel_auto ) {

            $caroufredsel_params['auto']['play'] = $_basement_meta_carousel_auto === 'true' ? true : false;

        }

        if( !empty($_basement_meta_carousel_align) ) {
            $caroufredsel_params['align'] = $_basement_meta_carousel_align;
        }


        if( $_basement_meta_carousel_cookie ) {
            $caroufredsel_params['cookie'] = $_basement_meta_carousel_cookie === 'true' ? true : false;
        }

        if( $_basement_meta_carousel_width ) {
            $caroufredsel_params['width'] = $_basement_meta_carousel_width;
        }

        if( (int)$_basement_meta_carousel_item_visible_min && (int)$_basement_meta_carousel_item_visible_min >= 0 ) {
            $caroufredsel_params['items']['visible']['min'] = (int)$_basement_meta_carousel_item_visible_min;
        }

        if( (int)$_basement_meta_carousel_item_visible_max && (int)$_basement_meta_carousel_item_visible_max >= 0 ) {
            $caroufredsel_params['items']['visible']['max'] = (int)$_basement_meta_carousel_item_visible_max;
        }


        if( $_basement_meta_carousel_responsive ) {
            if( $_basement_meta_carousel_responsive === 'true' ) {
                $caroufredsel_params['responsive'] = true;

                if(!(int)$_basement_meta_carousel_item_width) {
                    $caroufredsel_params['items']['width'] = 100;
                }

            } else {
                $caroufredsel_params['responsive'] = false;
            }
        }


        if( (int)$_basement_meta_carousel_item_width && (int)$_basement_meta_carousel_item_width >= 0 ) {
            $caroufredsel_params['items']['width'] = (int)$_basement_meta_carousel_item_width;
        }


        if( $_basement_meta_carousel_item_height ) {
            if($_basement_meta_carousel_item_height === 'js_basement_fixed_item_height') {

                $caroufredsel_params['items']['height'] = (int)$_basement_meta_carousel_item_fixed_height ? (int)$_basement_meta_carousel_item_fixed_height : 'variable';

            } else {
                $caroufredsel_params['items']['height'] = $_basement_meta_carousel_item_height;
            }
        }



        if( $_basement_meta_carousel_direction ) {
            $caroufredsel_params['direction'] = $_basement_meta_carousel_direction;
        }

        if( $_basement_meta_carousel_circular ) {
            $caroufredsel_params['circular'] = $_basement_meta_carousel_circular === 'true' ? true : false;
        }

        if( $_basement_meta_carousel_effects ) {
            $caroufredsel_params['scroll']['fx'] = $_basement_meta_carousel_effects;
        }

        if( $_basement_meta_carousel_easing ) {
            $caroufredsel_params['scroll']['easing'] = $_basement_meta_carousel_easing;
        }

        if( (int)$_basement_meta_carousel_duration && (int)$_basement_meta_carousel_duration >= 0 ) {
            $caroufredsel_params['scroll']['duration'] = (int)$_basement_meta_carousel_duration;
        }

        if( (int)$_basement_meta_carousel_item_visible && (int)$_basement_meta_carousel_item_visible >= 0 ) {
            $caroufredsel_params['items']['visible'] = (int)$_basement_meta_carousel_item_visible;
        }

        if( (int)$_basement_meta_carousel_item_start ) {
            $caroufredsel_params['items']['start'] = (int)$_basement_meta_carousel_item_start;
        }

        if( (int)$_basement_meta_carousel_item_scroll && (int)$_basement_meta_carousel_item_scroll >= 0 ) {
            $caroufredsel_params['scroll']['items'] = (int)$_basement_meta_carousel_item_scroll;
        }

        if( $_basement_meta_carousel_pause ) {
            $caroufredsel_params['scroll']['pauseOnHover'] = $_basement_meta_carousel_pause === 'true' ? true : false;
        }


        if(!empty($_basement_meta_carousel_swipe) && $_basement_meta_carousel_swipe === 'enable') {
	        $caroufredsel_params['swipe']['onTouch'] = true;
	        $caroufredsel_params['swipe']['onMouse'] = true;
        }

        $dots_params_get = array(
            'type' => $_basement_meta_carousel_dots_type ? $_basement_meta_carousel_dots_type : 'dots',
            'color' => $_basement_meta_carousel_dots_color ? $_basement_meta_carousel_dots_color : 'standart',
            'size' => $_basement_meta_carousel_dots_size ? $_basement_meta_carousel_dots_size : 'medium',
            'position' => $_basement_meta_carousel_dots_position ? $_basement_meta_carousel_dots_position : 'inside',
            'y' => $_basement_meta_carousel_dots_position_vertical ? $_basement_meta_carousel_dots_position_vertical : 'bottom',
            'x' => $_basement_meta_carousel_dots_position_horizontal ? $_basement_meta_carousel_dots_position_horizontal : 'center'
        );


        $arrows_params_get = array(
            'type' => $_basement_meta_carousel_arrow_type ? $_basement_meta_carousel_arrow_type : 'wobg',
            'color' => $_basement_meta_carousel_arrow_color ? $_basement_meta_carousel_arrow_color : 'standart',
            'size' => $_basement_meta_carousel_arrow_size ? $_basement_meta_carousel_arrow_size : 'medium',
            'position' => $_basement_meta_carousel_arrow_position ? $_basement_meta_carousel_arrow_position : 'inside',
            'y' => $_basement_meta_carousel_arrow_position_vertical ? $_basement_meta_carousel_arrow_position_vertical : 'side',
            'x'=> !empty($_basement_meta_carousel_arrow_position_horizontal) ? $_basement_meta_carousel_arrow_position_horizontal : ''
        );


        $builder_row = $carousel_row->appendChild( $dom->createElement( 'div' ) );
        $builder_row->setAttribute( 'class', 'builder-work-row' );


        $compare_array = array_intersect($dots_params_get,$arrows_params_get);


        if ( array_key_exists('position', $compare_array) && array_key_exists('y', $compare_array) && array_key_exists('x', $compare_array) ) {
            if($dots_params_get['type'] === 'dots') {
                $caroufredsel_params['pagination'] = '#preview-paginate';
            }
            $caroufredsel_params['prev'] = '#preview-prev-btn';
            $caroufredsel_params['next'] = '#preview-next-btn';

            $merge_array_dots_arrows = array(
                'dots' => $dots_params_get,
                'arrows' => $arrows_params_get
            );

            if( $arrows_params_get['type'] !== 'nope' || $dots_params_get['type'] !== 'nope' ) {
                if ( $arrows_params_get['type'] === 'nope' ) {
                    unset($merge_array_dots_arrows['arrows']);
                }
                if ( $dots_params_get['type'] === 'nope' ) {
                    unset($merge_array_dots_arrows['dots']);
                }

                if( $compare_array['y']  === 'top' ) {
                    $carousel_row->insertBefore($dom->importNode($this->generate_merge_arrow_dots($merge_array_dots_arrows), true), $carousel_row->firstChild);
                } elseif( $compare_array['y']  === 'bottom' ) {
                    $carousel_row->appendChild($dom->importNode($this->generate_merge_arrow_dots($merge_array_dots_arrows), true));
                }
            }

        } else {

            if( $dots_params_get['type'] !== 'nope' ) {

                if($dots_params_get['type'] === 'dots') {
                    $caroufredsel_params['pagination'] = '#preview-paginate';
                }

                if( $dots_params_get['y']  === 'top' ) {
                    $carousel_row->insertBefore($dom->importNode($this->generate_dots($dots_params_get), true), $carousel_row->firstChild);
                } elseif( $dots_params_get['y']  === 'bottom' ) {
                    $carousel_row->appendChild($dom->importNode($this->generate_dots($dots_params_get), true));
                }

            }

            if( $arrows_params_get['type'] !== 'nope' ) {

                $caroufredsel_params['prev'] = '#preview-prev-btn';
                $caroufredsel_params['next'] = '#preview-next-btn';


                if( $arrows_params_get['position'] === 'inrow' ) {
                    unset($arrows_params_get['x']);
                    unset($arrows_params_get['y']);

                    $builder_row->appendChild($dom->importNode($this->generate_arrows($arrows_params_get), true));
                } else {
                    if($arrows_params_get['y']  === 'side') {
                        unset($arrows_params_get['x']);

                        $carousel_area->appendChild($dom->importNode($this->generate_arrows($arrows_params_get), true));
                    } else {
                        #$arrows_params_get['x'] = $_basement_meta_carousel_arrow_position_horizontal;

                        if( $arrows_params_get['y']  === 'top' ) {
                            $carousel_row->insertBefore($dom->importNode($this->generate_arrows($arrows_params_get), true), $carousel_row->firstChild);
                        } elseif( $arrows_params_get['y']  === 'bottom' ) {
                            $carousel_row->appendChild($dom->importNode($this->generate_arrows($arrows_params_get), true));
                        }

                    }
                }

            }
        }

        $carousel = $builder_row->appendChild( $dom->createElement( 'ul' ) );
        $carousel->setAttribute( 'id', 'preview-basement-carousel' );
        $carousel->setAttribute( 'class', 'cf' );
        $carousel->setAttribute( 'data-params-preview', json_encode($caroufredsel_params) );
        if($dots_params_get['type'] === 'number') {
            $carousel->setAttribute( 'data-total', '#preview-paginate' );
        }
        /*
         * Lorem generate
         */

        $slides = array(
            'First Slide' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum has been they text ever since the 1500s, when an unknown printer took a galley.',
            'Second Slide' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem.',
            'Third Slide' => 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt.'
        );

        foreach($slides as $title => $text) {

            $item = $carousel->appendChild( $dom->createElement( 'li' ) );

            $h3 = $item->appendChild( $dom->createElement( 'h3', $title ) );

            $hr = $item->appendChild( $dom->createElement( 'hr' ) );

            $p = $item->appendChild( $dom->createElement( 'p', $text ) );
        }

        echo $dom->saveHTML();
    }


    /**
     * Merge Arrow&Dots
     *
     * @param $params
     * @return DOMNode
     */
    public function generate_merge_arrow_dots( $params ) {
        $dom = new DOMDocument( '1.0', 'UTF-8' );

        extract( $params );

        $arrow_dots = $dom->appendChild($dom->createElement('div'));

        if( $arrows ) {
            $prev_arrow = $arrow_dots->appendChild($dom->createElement('a', ''));
            $prev_arrow->setAttribute('href', '#');
            $prev_arrow->setAttribute('title', ' ');
            $prev_arrow->setAttribute('id', 'preview-prev-btn');
        }

        if( $dots && $arrows ) {
            $dots_paginate = $arrow_dots->appendChild($dom->createElement('div'));
            $dots_paginate->setAttribute('id', 'preview-paginate');
            $dots_paginate->setAttribute('class', $this->generate_classes( $dots, 'dots', array('position','x','y') ) );

            if($dots['type'] === 'number') {
                $current = $dots_paginate->appendChild( $dom->createElement( 'span', '1' ) );
                $current->setAttribute('class','preview-current');

                $symbol = $dots_paginate->appendChild( $dom->createTextNode('—') );

                $all = $dots_paginate->appendChild( $dom->createElement( 'span', '3' ) );
                $all->setAttribute('class','preview-all');
            }

        }


        if( $arrows ) {
            $next_arrow = $arrow_dots->appendChild($dom->createElement('a', ''));
            $next_arrow->setAttribute('href', '#');
            $next_arrow->setAttribute('title', ' ');
            $next_arrow->setAttribute('id', 'preview-next-btn');
        }


        if( ($arrows && $dots) || $arrows && empty($dots) ) {
            $arrow_dots->setAttribute('id', 'preview-arrows');
            $arrow_dots->setAttribute('class', 'main-preview-dots-arrow '. $this->generate_classes( $arrows, 'arrows' ));
        } elseif( empty($arrows) && $dots ) {
            $arrow_dots->setAttribute('id', 'preview-paginate');
            $arrow_dots->setAttribute('class', $this->generate_classes( $dots, 'dots' ));

            if($dots['type'] === 'number') {
                $current = $arrow_dots->appendChild($dom->createElement('span', '1'));
                $current->setAttribute('class', 'preview-current');

                $symbol = $arrow_dots->appendChild($dom->createTextNode('—'));

                $all = $arrow_dots->appendChild($dom->createElement('span', '3'));
                $all->setAttribute('class', 'preview-all');
            }
        }



        return $arrow_dots;

    }


    /**
     * Generate classes
     *
     * @param array $data
     * @param null $data_type
     * @param array $ignore_data
     * @return string
     */
    private function generate_classes( $data = array(), $data_type = null, $ignore_data = array() ) {
        $class = array();

        if( !empty($data) && !empty($data_type) ) {

            if( !empty($ignore_data) ){
                foreach ( $ignore_data as $ignore ) {
                    unset($data[$ignore]);
                }
            }

            extract($data);

            if( $data_type === 'dots' ) {

                switch ($type) {
                    case 'dots' :
                        $class[] = 'preview-type-dots';
                        break;
                    case 'number' :
                        $class[] = 'preview-type-number';
                        break;
                }


                switch ($color) {
                    case 'light' :
                        $class[] = 'preview-color-light';
                        break;
                    case 'standart' :
                        $class[] = 'preview-color-standart';
                        break;
                    case 'dark' :
                        $class[] = 'preview-color-dark';
                        break;
                }

                switch ($size) {
                    case 'small' :
                        $class[] = 'preview-size-small';
                        break;
                    case 'medium' :
                        $class[] = 'preview-size-medium';
                        break;
                    case 'large' :
                        $class[] = 'preview-size-large';
                        break;
                }


	            if ( ! empty( $position ) ) {
		            switch ( $position ) {
			            case 'inside' :
				            $class[] = 'preview-position-inside';
				            break;
			            case 'outside' :
				            $class[] = 'preview-position-outside';
				            break;
		            }
	            }

	            if(!empty($y)) {
		            switch ( $y ) {
			            case 'top' :
				            $class[] = 'preview-vertical-top';
				            break;
			            case 'bottom' :
				            $class[] = 'preview-vertical-bottom';
				            break;
		            }
	            }

	            if(!empty($x)) {
		            switch ( $x ) {
			            case 'left' :
				            $class[] = 'preview-horizontal-left';
				            break;
			            case 'center' :
				            $class[] = 'preview-horizontal-center';
				            break;
			            case 'right' :
				            $class[] = 'preview-horizontal-right';
				            break;
		            }
	            }
            }
            elseif( $data_type === 'arrows' ) {
                switch ($type) {
                    case 'wobg' :
                        $class[] = 'preview-type-nobg';
                        break;
                    case 'bg' :
                        $class[] = 'preview-type-bg';
                        break;
                }

                switch ($color) {
                    case 'light' :
                        $class[] = 'preview-color-light';
                        break;
                    case 'standart' :
                        $class[] = 'preview-color-standart';
                        break;
                    case 'dark' :
                        $class[] = 'preview-color-dark';
                        break;
                }

                switch ($size) {
                    case 'small' :
                        $class[] = 'preview-size-small';
                        break;
                    case 'medium' :
                        $class[] = 'preview-size-medium';
                        break;
                    case 'large' :
                        $class[] = 'preview-size-large';
                        break;
                }

                switch ($position) {
                    case 'inside' :
                        $class[] = 'preview-position-inside';
                        break;
                    case 'outside' :
                        $class[] = 'preview-position-outside';
                        break;
                    case 'inrow' :
                        $class[] = 'preview-position-inrow';
                        break;
                }


	            if ( ! empty( $y ) ) {
	                switch ( $y ) {
		                case 'top' :
			                $class[] = 'preview-vertical-top';
			                break;
		                case 'bottom' :
			                $class[] = 'preview-vertical-bottom';
			                break;
		                case 'side' :
			                $class[] = 'preview-vertical-side';
			                break;
	                }
                }

                if(!empty($x)) {
	                switch ( $x ) {
		                case 'left' :
			                $class[] = 'preview-horizontal-left';
			                break;
		                case 'center' :
			                $class[] = 'preview-horizontal-center';
			                break;
		                case 'right' :
			                $class[] = 'preview-horizontal-right';
			                break;
	                }
                }
            }
            elseif( $data_type === 'block_arrow_dots' ) {
                switch ($position) {
                    case 'inside' :
                        $class[] = 'block_preview-position-inside';
                        break;
                    case 'outside' :
                        $class[] = 'block_preview-position-outside';
                        break;
                }
                switch ($y) {
                    case 'top' :
                        $class[] = 'block_preview-vertical-top';
                        break;
                    case 'bottom' :
                        $class[] = 'block_preview-vertical-bottom';
                        break;
                }

                switch ($x) {
                    case 'left' :
                        $class[] = 'block_preview-horizontal-left';
                        break;
                    case 'center' :
                        $class[] = 'block_preview-horizontal-center';
                        break;
                    case 'right' :
                        $class[] = 'block_preview-horizontal-right';
                        break;
                }
            }
        }

        return implode( ' ', $class );
    }


    /**
     * Generate Arrows
     *
     * @param $params
     * @return DOMNode
     */
    public function generate_arrows( $params ) {
        $dom = new DOMDocument( '1.0', 'UTF-8' );

        extract( $params );

        $class = array();

        $class[] = 'cf';

        $arrow_row = $dom->appendChild( $dom->createElement( 'div' ) );
        $arrow_row->setAttribute( 'id', 'preview-arrows' );


        $prev_arrow = $arrow_row->appendChild( $dom->createElement( 'a', '' ) );
        $prev_arrow->setAttribute( 'href', '#' );
        $prev_arrow->setAttribute( 'title', ' ' );
        $prev_arrow->setAttribute( 'id', 'preview-prev-btn' );
        $prev_arrow->setAttribute( 'class', 'square' );

        $next_arrow = $arrow_row->appendChild( $dom->createElement( 'a', '' ) );
        $next_arrow->setAttribute( 'href', '#' );
        $next_arrow->setAttribute( 'title', ' ' );
        $next_arrow->setAttribute( 'id', 'preview-next-btn' );
        $next_arrow->setAttribute( 'class', 'square' );

        $arrow_row->setAttribute( 'class', $this->generate_classes( $params, 'arrows' ) );

        return $arrow_row;

    }


    /**
     * Generate Dots
     *
     * @param $params
     * @return DOMNode
     */
    public function generate_dots( $params ) {
        $dom = new DOMDocument( '1.0', 'UTF-8' );

        extract( $params );

        $dots = $dom->appendChild( $dom->createElement( 'div' ) );
        $dots->setAttribute( 'id', 'preview-paginate' );



        $class = array();

        switch ($type) {
            case 'dots' :
                $class[] = 'preview-type-dots';
                break;
            case 'number' :
                $class[] = 'preview-type-number';
                $current = $dots->appendChild( $dom->createElement( 'span', '1' ) );
                $current->setAttribute('class','preview-current');

                $symbol = $dots->appendChild( $dom->createTextNode('—') );

                $all = $dots->appendChild( $dom->createElement( 'span', '3' ) );
                $all->setAttribute('class','preview-all');
                break;
        }


        switch ($color) {
            case 'light' :
                $class[] = 'preview-color-light';
                break;
            case 'standart' :
                $class[] = 'preview-color-standart';
                break;
            case 'dark' :
                $class[] = 'preview-color-dark';
                break;
        }

        switch ($size) {
            case 'small' :
                $class[] = 'preview-size-small';
                break;
            case 'medium' :
                $class[] = 'preview-size-medium';
                break;
            case 'large' :
                $class[] = 'preview-size-large';
                break;
        }


        switch ($position) {
            case 'inside' :
                $class[] = 'preview-position-inside';
                break;
            case 'outside' :
                $class[] = 'preview-position-outside';
                break;
        }

        switch ($y) {
            case 'top' :
                $class[] = 'preview-vertical-top';
                break;
            case 'bottom' :
                $class[] = 'preview-vertical-bottom';
                break;
        }

        switch ($x) {
            case 'left' :
                $class[] = 'preview-horizontal-left';
                break;
            case 'center' :
                $class[] = 'preview-horizontal-center';
                break;
            case 'right' :
                $class[] = 'preview-horizontal-right';
                break;
        }


        $dots->setAttribute( 'class', implode( ' ', $class ) );

        return $dots;
    }


    /**
     * Convert hex to RGB
     *
     * @param $color
     * @return array|bool
     */
    protected function hexToRgb($color) {

        if ($color[0] == '#') {
            $color = substr($color, 1);
        }


        if (strlen($color) == 6) {
            list($red, $green, $blue) = array(
                $color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5]
            );
        } elseif (strlen($cvet) == 3) {
            list($red, $green, $blue) = array(
                $color[0]. $color[0],
                $color[1]. $color[1],
                $color[2]. $color[2]
            );
        }else{
            return false;
        }

        $red = hexdec($red);
        $green = hexdec($green);
        $blue = hexdec($blue);

        return array(
            'red' => $red,
            'green' => $green,
            'blue' => $blue
        );
    }
    
    
}
Basement_Carousel_Preview::init();