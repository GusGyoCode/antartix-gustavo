<?php
defined( 'ABSPATH' ) or die();


class WPBakeryShortCode_basement_vc_carousel extends WPBakeryShortCode {

	protected function content( $atts, $content = null ) {

		$id = $slides = '';

		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );


		$carousel        = $this->carousel_exists( $id );
		$slides_carousel = $this->slide_exists( explode( ',', $slides ) );
		$carousel_area   = '';

		if ( $carousel && $slides_carousel ) {

			$shortcodes_custom_css = '';
			foreach ($slides_carousel as $css_slide) {
				$shortcodes_custom_css .= get_post_meta( $css_slide, '_wpb_shortcodes_custom_css', true );
			}

			/*
			 * Get All Params
			 */
			$params = $this->generate_meta_data( get_post_meta( absint( $id ) ), $id );

			/*
			 * Extract params to variables
			 */
			extract( $params );

			/*
			 * Pushed classes
			 */
			$classes = array();
			if ( $class ) {
				$classes[] = $class;
			}


			/*
			 * Generate style
			 */
			$style = $this->generate_row_style( $params );


			/*
			 * Margin for wrap row
			 */
			$margin = '';
			if ( is_numeric( $margin_left ) ) {
				$margin .= 'margin-left:' . $margin_left . 'px;';
			}
			if ( is_numeric( $margin_top ) ) {
				$margin .= 'margin-top:' . $margin_top . 'px;';
			}
			if ( is_numeric( $margin_right ) ) {
				$margin .= 'margin-right:' . $margin_right . 'px;';
			}
			if ( is_numeric( $margin_bottom ) ) {
				$margin .= 'margin-bottom:' . $margin_bottom . 'px;';
			}


			/*
			 * Generate CarouFredsel Params
			 */
			$params_carousel = $this->generate_carousel_params( $params );


			$dots_params_get = array(
				'type'     => $dots_type ? $dots_type : 'dots',
				'color'    => $dots_color ? $dots_color : 'standart',
				'size'     => $dots_size ? $dots_size : 'medium',
				'position' => $dots_position ? $dots_position : 'inside',
				'y'        => $dots_position_vertical ? $dots_position_vertical : 'bottom',
				'x'        => $dots_position_horizontal ? $dots_position_horizontal : 'center'
			);


			$arrows_params_get = array(
				'type'     => $arrow_type ? $arrow_type : 'wobg',
				'color'    => $arrow_color ? $arrow_color : 'standart',
				'size'     => $arrow_size ? $arrow_size : 'medium',
				'position' => $arrow_position ? $arrow_position : 'inside',
				'y'        => $arrow_position_vertical ? $arrow_position_vertical : 'side',
				'x'        => ! empty( $arrow_position_horizontal ) ? $arrow_position_horizontal : ''
			);


			$compare_array = array_intersect( $dots_params_get, $arrows_params_get );

			$merge          = false;
			$merge_controls = '';

			$dots           = '';
			$dots_positions = $dots_params_get['position'] . ' ' . $dots_params_get['y'];
			$dots_numbers   = '';

			$arrows = '';
			if ( $arrows_params_get['position'] === 'inrow' ) {
				unset( $arrows_params_get['x'] );
				unset( $arrows_params_get['y'] );
			} else {
				if ( $arrows_params_get['y'] === 'side' ) {
					unset( $arrows_params_get['x'] );
				}
			}

			$arrows_params_get_y        = ! empty( $arrows_params_get['y'] ) ? $arrows_params_get['y'] : '';
			$arrows_params_get_position = ! empty( $arrows_params_get['position'] ) ? $arrows_params_get['position'] : '';

			$arrows_positions = $arrows_params_get_position . ' ' . $arrows_params_get_y;


			if ( array_key_exists( 'position', $compare_array ) && array_key_exists( 'y', $compare_array ) && array_key_exists( 'x', $compare_array ) ) {

				$merge = true;


				$prefix_dots = apply_filters( 'basement_carousel_prefix_merge_paginate', 'basement-carousel-paginate-' );


				if ( $dots_params_get['type'] === 'dots' ) {
					$paginate_id                   = uniqid( '#' . apply_filters( 'basement_carousel_id_merge_dots', $prefix_dots . 'dots-' ) );
					$params_carousel['pagination']['container'] = $paginate_id;
				} else {
					$paginate_id  = uniqid( '#' . apply_filters( 'basement_carousel_id_merge_numbers', $prefix_dots . 'number-' ) );
					$dots_numbers = 'data-basement-total="' . $paginate_id . '"';
				}


				$prefix_arrows = apply_filters( 'basement_carousel_prefix_merge_arrows', 'basement-carousel-arrow-' );

				$prev_arrow = uniqid( '#' . apply_filters( 'basement_carousel_id_merge_prev', $prefix_arrows . 'prev-' ) );
				$next_arrow = uniqid( '#' . apply_filters( 'basement_carousel_id_merge_next', $prefix_arrows . 'next-' ) );


				$params_carousel['prev'] = $prev_arrow;
				$params_carousel['next'] = $next_arrow;

				$merge_array_dots_arrows = array(
					'dots'   => array(
						'params' => $dots_params_get,
						'id'     => $paginate_id,
						'prefix' => $prefix_dots
					),
					'arrows' => array(
						'params' => $arrows_params_get,
						'id'     => array(
							'prev' => $prev_arrow,
							'next' => $next_arrow
						),
						'prefix' => $prefix_arrows
					)
				);

				if ( $arrows_params_get['type'] !== 'nope' || $dots_params_get['type'] !== 'nope' ) {
					if ( $arrows_params_get['type'] === 'nope' ) {
						unset( $merge_array_dots_arrows['arrows'] );
					}
					if ( $dots_params_get['type'] === 'nope' ) {
						unset( $merge_array_dots_arrows['dots'] );
					}


					$merge_controls = $this->generate_html_merge_controls( apply_filters( 'basement_carousel_generate_merge_controls', $merge_array_dots_arrows ) );

				}

			} else {

				if ( $dots_params_get['type'] !== 'nope' ) {

					$prefix_dots = apply_filters( 'basement_carousel_prefix_paginate', 'basement-carousel-paginate-' );


					if ( $dots_params_get['type'] === 'dots' ) {
						$paginate_id                   = uniqid( '#' . apply_filters( 'basement_carousel_id_dots', $prefix_dots . 'dots-' ) );
						$params_carousel['pagination']['container'] = $paginate_id;
					} else {
						$paginate_id  = uniqid( '#' . apply_filters( 'basement_carousel_id_numbers', $prefix_dots . 'number-' ) );
						$dots_numbers = 'data-basement-total="' . $paginate_id . '"';
					}

					$dots = $this->generate_html_dots( apply_filters( 'basement_carousel_generate_dots', array(
						'params' => $dots_params_get,
						'id'     => $paginate_id,
						'prefix' => $prefix_dots
					) ) );
				}

				if ( $arrows_params_get['type'] !== 'nope' ) {

					$prefix_arrows = apply_filters( 'basement_carousel_prefix_arrows', 'basement-carousel-arrow-' );

					$prev_arrow = uniqid( '#' . apply_filters( 'basement_carousel_id_prev', $prefix_arrows . 'prev-' ) );
					$next_arrow = uniqid( '#' . apply_filters( 'basement_carousel_id_next', $prefix_arrows . 'next-' ) );


					$params_carousel['prev'] = $prev_arrow;
					$params_carousel['next'] = $next_arrow;

					$arrows = $this->generate_html_arrows( apply_filters( 'basement_carousel_generate_arrows', array(
						'params' => $arrows_params_get,
						'id'     => array(
							'prev' => $prev_arrow,
							'next' => $next_arrow
						),
						'prefix' => $prefix_arrows
					) ) );
				}
			}

			$style_item = array();

			if(isset($item_padding_top) && is_numeric($item_padding_top)) {
				$style_item[] = 'padding-top:' . absint($item_padding_top) . 'px !important';
			}

			if(isset($item_padding_bottom) && is_numeric($item_padding_bottom)) {
				$style_item[] = 'padding-bottom:' . absint($item_padding_bottom) . 'px !important';
			}


			// Slides
			$slides = $this->generate_html_slides( apply_filters( 'basement_carousel_generate_html_slides', array(
				'ids'     => $slides_carousel,
				'classes' => array( 'clearfix' ),
				'style' => $style_item
			) ) );


			// Carousel
			$main_carousel = sprintf( '<div class="%1$s" data-carousel-id="%2$s" %3$s %4$s>%5$s</div>',
				esc_attr( 'basement-carousel clearfix' ),
				$id,
				! empty( $params_carousel ) ? 'data-basement-params="' . htmlspecialchars( json_encode( $params_carousel ) ) . '"' : '',
				$dots_numbers,
				$slides
			);


			$help_row_position = ! empty( $arrows_params_get['position'] ) ? 'basement-help-' . $arrows_params_get['position'] : '';
			$help_row_size     = ! empty( $arrows_params_get['size'] ) ? 'basement-help-' . $arrows_params_get['size'] : '';
			$help_row_y        = ! empty( $arrows_params_get['y'] ) ? 'basement-help-' . $arrows_params_get['y'] : '';
			$help_row_type     = ! empty( $arrows_params_get['type'] ) ? 'basement-help-' . $arrows_params_get['type'] : '';

			// Help Row carolusel
			$builder_row = sprintf( '<div class="%1$s %4$s">%2$s%3$s%5$s%6$s</div><div class="full-width-basement-help-row"></div>',
				esc_attr( 'basement-carousel-help-row' ),
				$main_carousel,
				$this->generate_positions( array(
					'position' => 'inrow ',
					'arrows'   => array(
						'position' => $arrows_positions,
						'elements' => $arrows
					),
					'merge'    => $merge
				) ),
				$help_row_size . ' ' . $help_row_position . ' ' . $help_row_y . ' ' . $help_row_type,
				$this->generate_positions( array(
					'position' => 'inside side',
					'arrows'   => array(
						'position' => $arrows_positions,
						'elements' => $arrows
					),
					'merge'    => $merge
				) ),
				$this->generate_positions( array(
					'position' => 'outside side',
					'arrows'   => array(
						'position' => $arrows_positions,
						'elements' => $arrows
					),
					'merge'    => $merge
				) )
			);


			$arrows_params_get_x = ! empty( $arrows_params_get['x'] ) ? $arrows_params_get['x'] : '';
			$dots_params_get_x   = ! empty( $dots_params_get['x'] ) ? $dots_params_get['x'] : '';



			// Carousel Row
			$carousel_row = sprintf( '%6$s<div class="%1$s %2$s" %3$s %4$s>%7$s%5$s%8$s</div><div class="full-width-basement"></div>%9$s',
				esc_attr( 'basement-carousel-row clearfix' ),
				implode( ' ', $classes ),
				! empty( $base_stretch ) ? 'data-stretch="' . $base_stretch . '"' : '',
				! empty( $style ) ? 'style="' . esc_attr( $style ) . '"' : '',
				$builder_row,
				$this->generate_positions( array(
					'position' => 'outside top',
					'dots'     => array(
						'position' => $dots_positions,
						'x'        => $dots_params_get_x,
						'elements' => $dots
					),
					'arrows'   => array(
						'position' => $arrows_positions,
						'x'        => $arrows_params_get_x,
						'elements' => $arrows
					),
					'merged'   => array(
						'status'   => $merge,
						'elements' => $merge_controls
					)
				) ),
				$this->generate_positions( array(
					'position' => 'inside top',
					'dots'     => array(
						'position' => $dots_positions,
						'x'        => $dots_params_get_x,
						'elements' => $dots
					),
					'arrows'   => array(
						'position' => $arrows_positions,
						'x'        => $arrows_params_get_x,
						'elements' => $arrows
					),
					'merged'   => array(
						'status'   => $merge,
						'elements' => $merge_controls
					)
				) ),
				$this->generate_positions( array(
					'position' => 'inside bottom',
					'dots'     => array(
						'position' => $dots_positions,
						'x'        => $dots_params_get_x,
						'elements' => $dots
					),
					'arrows'   => array(
						'position' => $arrows_positions,
						'x'        => $arrows_params_get_x,
						'elements' => $arrows
					),
					'merged'   => array(
						'status'   => $merge,
						'elements' => $merge_controls
					)
				) ),
				$this->generate_positions( array(
					'position' => 'outside bottom',
					'dots'     => array(
						'position' => $dots_positions,
						'x'        => $dots_params_get_x,
						'elements' => $dots
					),
					'arrows'   => array(
						'position' => $arrows_positions,
						'x'        => $arrows_params_get_x,
						'elements' => $arrows
					),
					'merged'   => array(
						'status'   => $merge,
						'elements' => $merge_controls
					)
				) )
			);


			// Container Carousel
			$container = sprintf( '<div class="%1$s">%2$s</div>',
				esc_attr( 'basement-carousel-container clearfix' ),
				$carousel_row
			);


			$wrap_classses = array();
			if(! empty( $base_stretch )) {
				$wrap_classses[] = 'basement-carousel-stretched';
			}


			if(!empty($dots_lg)) {
				$wrap_classses[] = 'basement-carousel-'.$dots_lg;
			}

			if(!empty($dots_md)) {
				$wrap_classses[] = 'basement-carousel-'.$dots_md;
			}

			if(!empty($dots_sm)) {
				$wrap_classses[] = 'basement-carousel-'.$dots_sm;
			}

			if(!empty($dots_xs)) {
				$wrap_classses[] = 'basement-carousel-'.$dots_xs;
			}




			if(!empty($arrows_lg)) {
				$wrap_classses[] = 'basement-carousel-'.$arrows_lg;
			}

			if(!empty($arrows_md)) {
				$wrap_classses[] = 'basement-carousel-'.$arrows_md;
			}

			if(!empty($arrows_sm)) {
				$wrap_classses[] = 'basement-carousel-'.$arrows_sm;
			}

			if(!empty($arrows_xs)) {
				$wrap_classses[] = 'basement-carousel-'.$arrows_xs;
			}



			// Common block
			$carousel_area = sprintf( '<div class="%1$s %4$s" %2$s>%3$s</div>',
				esc_attr( 'basement-carousel-wrap-block' ),
				! empty( $margin ) ? 'style="' . esc_attr( $margin ) . '"' : '',
				$container,
				!empty($wrap_classses) ? implode(' ', $wrap_classses) : ''
			);


			if(!empty($shortcodes_custom_css)) {
				$carousel_area = '<style>'.$shortcodes_custom_css.'</style>' . $carousel_area;
			}

		}

		return $carousel_area;
	}


	/**
	 * Check if slide CPT exist
	 *
	 * @param $slides
	 *
	 * @return array|bool
	 */
	private function slide_exists( $slides ) {
		$list_slides = array();

		foreach ( $slides as $slide ) {
			if ( get_post_type( absint( $slide ) ) === 'carousel_slide' && get_post_status ( absint($slide) ) === 'publish' ) {
				$list_slides[] = $slide;
			}
		}

		return $list_slides ? $list_slides : false;
	}


	/**
	 * Check if carousel CPT exist
	 *
	 * @param $carousel
	 *
	 * @return bool
	 */
	private function carousel_exists( $carousel ) {

		if ( get_post_type( absint( $carousel ) ) === 'carousel' && get_post_meta( absint( $carousel ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generate Params For Carousel
	 *
	 * @param $meta
	 * @param $id
	 *
	 * @return mixed|void
	 */
	private function generate_meta_data( $meta, $id ) {
		$params = array();

		foreach ( $meta as $key => $value ) {
			if ( strpos( $key, 'basement_meta_carousel_' ) != false ) {
				if ( $key !== '_basement_meta_carousel_background' ) {
					$params[ substr( $key, 24 ) ] = array_shift( $value );
				} else {
					$background = get_post_meta( absint( $id ), '_basement_meta_carousel_background', true );

					foreach ( $background as $bg_key => $bg_value ) {
						$params[ substr( $key, 24 ) . '_' . $bg_key ] = $bg_value;
					}
				}
			}
		}

		return apply_filters( 'basement_carousel_generate_params', $params, $id );
	}


	/**
	 * Generate params for row
	 *
	 * @param $meta
	 *
	 * @return string
	 */
	private function generate_row_style( $meta ) {
		$style = '';

		extract( $meta );

		/*
		 * Padding for row
		 */
		if ( is_numeric( $padding_left ) ) {
			$style .= 'padding-left:' . $padding_left . 'px;';
		}
		if ( is_numeric( $padding_top ) ) {
			$style .= 'padding-top:' . $padding_top . 'px;';
		}
		if ( is_numeric( $padding_right ) ) {
			$style .= 'padding-right:' . $padding_right . 'px;';
		}
		if ( is_numeric( $padding_bottom ) ) {
			$style .= 'padding-bottom:' . (int) $padding_bottom . 'px;';
		}


		/*
		 * Border for row
		 */
		if ( is_numeric( $border_left ) && $border_unit && $border_style && $border_color ) {
			$style .= 'border-left: ' . $border_left . $border_unit . ' ' . $border_style . ' ' . $border_color . ';';
		}
		if ( is_numeric( $border_top ) && $border_unit && $border_style && $border_color ) {
			$style .= 'border-top: ' . $border_top . $border_unit . ' ' . $border_style . ' ' . $border_color . ';';
		}
		if ( is_numeric( $border_right ) && $border_unit && $border_style && $border_color ) {
			$style .= 'border-right: ' . $border_right . $border_unit . ' ' . $border_style . ' ' . $border_color . ';';
		}
		if ( is_numeric( $border_bottom ) && $border_unit && $border_style && $border_color ) {
			$style .= 'border-bottom: ' . $border_bottom . $border_unit . ' ' . $border_style . ' ' . $border_color . ';';
		}

		/*
		 * Border radius for row
		 */
		if ( ( is_numeric( $border_radius_left ) || is_numeric( $border_radius_top ) || is_numeric( $border_radius_right ) || is_numeric( $border_radius_bottom ) ) && $border_radius_unit ) {

			$bl = $border_radius_left;
			$bt = $border_radius_top;
			$br = $border_radius_right;
			$bb = $border_radius_bottom;
			$u  = $border_radius_unit;

			if ( $u === 'per' ) {
				$u = "%%";
			}

			$style .= '-webkit-border-radius: ' . ( empty( $bl ) ? '0' : $bl ) . $u . ' ' . ( empty( $bt ) ? '0' : $bt ) . $u . ' ' . ( empty( $br ) ? '0' : $br ) . $u . ' ' . ( empty( $bb ) ? '0' : $bb ) . $u . '; -moz-border-radius: ' . ( empty( $bl ) ? '0' : $bl ) . $u . ' ' . ( empty( $bt ) ? '0' : $bt ) . $u . ' ' . ( empty( $br ) ? '0' : $br ) . $u . ' ' . ( empty( $bb ) ? '0' : $bb ) . $u . '; border-radius: ' . ( empty( $bl ) ? '0' : $bl ) . $u . ' ' . ( empty( $bt ) ? '0' : $bt ) . $u . ' ' . ( empty( $br ) ? '0' : $br ) . $u . ' ' . ( empty( $bb ) ? '0' : $bb ) . $u . ';';
		}


		/*
		 * Background for row
		 */
		if ( $background_image ) {
			$style .= 'background-image:url(' . wp_get_attachment_url( $background_image ) . ');';

			if ( $background_attachment && $background_attachment !== 'nope' ) {
				$style .= 'background-attachment: ' . $background_attachment . ';';
			}

			if ( $background_repeat && $background_repeat !== 'nope' ) {
				$style .= 'background-repeat: ' . $background_repeat . ';';
			}

			if ( $background_position && $background_position !== 'nope' ) {
				$style .= 'background-position: ' . $background_position . ';';
			}

			if ( $background_size && $background_size !== 'nope' ) {
				$style .= 'background-size: ' . $background_size . ';';
			}
		}

		if ( $background_color ) {
			$color_l1 = $this->hexToRgb( $background_color );

			$alpha_l1 = 1;
			if ( (float) $background_opacity ) {
				$alpha_l1 = $background_opacity;
			}

			$rgba_l1 = $color_l1['red'] . ', ' . $color_l1['green'] . ', ' . $color_l1['blue'] . ', ' . $alpha_l1 . '';
			$style .= 'background-color: rgba(' . $rgba_l1 . ');';
		}

		return $style;
	}


	/**
	 * Generate params for carousel
	 *
	 * @param $meta
	 *
	 * @return mixed|void
	 */
	private function generate_carousel_params( $meta ) {

		$caroufredsel_params = array();

		extract( $meta );

		if ( !empty( $height ) ) {
			if ( $height === 'js_basement_fixed_height' ) {

				$caroufredsel_params['height'] = (int) $fixed_height ? (int) $fixed_height : 'variable';

			} else {
				$caroufredsel_params['height'] = $height;
			}
		}

		if ( $auto ) {
			$caroufredsel_params['auto']['play'] = $auto === 'true' ? true : false;
		}

		if ( ! empty( $align ) ) {
			$caroufredsel_params['align'] = $align;
		}

		if ( $cookie ) {
			$caroufredsel_params['cookie'] = $cookie === 'true' ? true : false;
		}

		if ( $width ) {
			$caroufredsel_params['width'] = $width;
		}

		if ( $responsive ) {
			if ( $responsive === 'true' ) {
				$caroufredsel_params['responsive'] = true;
				if ( ! (int) $item_width ) {
					$caroufredsel_params['items']['width'] = 100;
				}
			} else {
				$caroufredsel_params['responsive'] = false;
			}
		}

		if ( $direction ) {
			$caroufredsel_params['direction'] = $direction;
		}

		if ( $circular ) {
			$caroufredsel_params['circular'] = $circular === 'true' ? true : false;
		}

		if ( $effects ) {
			$caroufredsel_params['scroll']['fx'] = $effects;
		}

		if(!empty($swipe) && $swipe === 'enable') {
			$caroufredsel_params['swipe']['onTouch'] = true;
			$caroufredsel_params['swipe']['onMouse'] = true;
		}

		if ( $easing ) {
			$caroufredsel_params['scroll']['easing'] = $easing;
		}

		if ( is_numeric( $duration ) ) {
			$caroufredsel_params['scroll']['duration'] = (int)$duration;
		}


		if ( is_numeric( $item_width ) ) {
			$caroufredsel_params['items']['width'] = (int)$item_width;
		}


		if ( $item_height ) {
			if ( $item_height === 'js_basement_fixed_item_height' ) {
				$caroufredsel_params['items']['height'] = (int) $item_fixed_height ? (int) $item_fixed_height : 'variable';

			} else {
				$caroufredsel_params['items']['height'] = $item_height;
			}
		}

		if ( (int) $item_visible && (int) $item_visible >= 0 ) {
			$caroufredsel_params['items']['visible'] = (int) $item_visible;
		}

		if ( (int) $item_visible_min && (int) $item_visible_min >= 0 ) {
			$caroufredsel_params['items']['visible']['min'] = (int) $item_visible_min;
		}

		if ( (int) $item_visible_max && (int) $item_visible_max >= 0 ) {
			$caroufredsel_params['items']['visible']['max'] = (int) $item_visible_max;
		}


		if ( (int) $item_start ) {
			$caroufredsel_params['items']['start'] = (int) $item_start;
		}

		if ( (int) $item_scroll && (int) $item_scroll >= 0 ) {
			$caroufredsel_params['scroll']['items'] = (int) $item_scroll;
		}

		if ( $pause ) {
			$caroufredsel_params['scroll']['pauseOnHover'] = $pause === 'true' ? true : false;
		}

		return apply_filters( 'basement_carousel_set_params', $caroufredsel_params );
	}


	/**
	 * Return positions for carousel controls
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	private function generate_positions( $params = array() ) {

		extract( $params );

		$controls = '';

		$merged_status   = ! empty( $merged['status'] ) ? $merged['status'] : false;
		$dots_position   = ! empty( $dots['position'] ) ? $dots['position'] : '';
		$arrows_position = ! empty( $arrows['position'] ) ? $arrows['position'] : '';
		$arrows_x        = ! empty( $arrows['x'] ) ? $arrows['x'] : '';
		$dots_x          = ! empty( $dots['x'] ) ? $dots['x'] : '';

		if ( ! $merged_status ) {
			if ( $dots_position === $arrows_position && $position === $dots_position && $position === $arrows_position ) {

				$controls = sprintf( '<div class="%1$s">%2$s%3$s%4$s%5$s</div>',
					esc_attr( 'basement-carousel-inline-controls clearfix' ),
					( ( $arrows_x === 'left' || $arrows_x === 'right' ) && $dots_x === 'center' ) ? $arrows['elements'] . $dots['elements'] : '',
					( ( $dots_x === 'left' || $dots_x === 'right' ) && $arrows_x === 'center' ) ? $dots['elements'] . $arrows['elements'] : '',
					( ( $arrows_x === 'left' ) && ( $dots_x === 'right' ) ) ? $arrows['elements'] . $dots['elements'] : '',
					( ( $dots_x === 'left' ) && ( $arrows_x === 'right' ) ) ? $dots['elements'] . $arrows['elements'] : ''
				);

			} else {


				if ( $position === $dots_position ) {
					if ( $dots_x === 'left' || $dots_x === 'right' ) {
						$controls = sprintf( '<div class="%2$s">%1$s</div>',
							$dots['elements'],
							esc_attr( 'basement-carousel-dots-controls clearfix' )
						);
					} else {
						$controls .= $dots['elements'];
					}
				}

				if ( $position === $arrows_position ) {
					if ( $arrows_x === 'left' || $arrows_x === 'right' ) {
						$controls = sprintf( '<div class="%2$s">%1$s</div>',
							$arrows['elements'],
							esc_attr( 'basement-carousel-arrows-controls clearfix' )
						);
					} else {
						$controls .= $arrows['elements'];
					}

				}
			}
		} else {
			if ( $dots_position === $arrows_position && $position === $dots_position && $position === $arrows_position ) {
				$controls = sprintf( '<div class="%1$s">%2$s</div>',
					esc_attr( 'basement-carousel-merge-controls clearfix' ),
					$merged['elements']
				);
			}
		}

		return $controls;
	}


	/**
	 * Generate merge carousel controls
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	private function generate_html_merge_controls( $params = array() ) {

		$classes_outer  = array();
		$merge_controls = '';

		extract( $params );


		if ( $arrows ) {
			$classes_outer[] = substr( $arrows['prefix'], 0, - 1 ) . 's';


			if(isset($dots['params']['type'])) {
				$classes_outer[] = 'basement-carousel-dots-' . $dots['params']['type'];
			}

			switch ( $arrows['params']['type'] ) {
				case 'wobg' :
					$classes_outer[] = $arrows['prefix'] . 'nobg';
					break;
				case 'bg' :
					$classes_outer[] = $arrows['prefix'] . 'bg';
					break;
			}

			switch ( $arrows['params']['color'] ) {
				case 'light' :
					$classes_outer[] = $arrows['prefix'] . 'light';
					break;
				case 'standart' :
					$classes_outer[] = $arrows['prefix'] . 'standart';
					break;
				case 'dark' :
					$classes_outer[] = $arrows['prefix'] . 'dark';
					break;
			}

			switch ( $arrows['params']['size'] ) {
				case 'small' :
					$classes_outer[] = $arrows['prefix'] . 'small';
					break;
				case 'medium' :
					$classes_outer[] = $arrows['prefix'] . 'medium';
					break;
				case 'large' :
					$classes_outer[] = $arrows['prefix'] . 'large';
					break;
			}

			switch ( $arrows['params']['position'] ) {
				case 'inside' :
					$classes_outer[] = $arrows['prefix'] . 'inside';
					break;
				case 'outside' :
					$classes_outer[] = $arrows['prefix'] . 'outside';
					break;
				case 'inrow' :
					$classes_outer[] = $arrows['prefix'] . 'inrow';
					break;
			}


			switch ( $arrows['params']['y'] ) {
				case 'top' :
					$classes_outer[] = $arrows['prefix'] . 'top';
					break;
				case 'bottom' :
					$classes_outer[] = $arrows['prefix'] . 'bottom';
					break;
				case 'side' :
					$classes_outer[] = $arrows['prefix'] . 'side';
					break;
			}

			switch ( $arrows['params']['x'] ) {
				case 'left' :
					$classes_outer[] = $arrows['prefix'] . 'left';
					break;
				case 'center' :
					$classes_outer[] = $arrows['prefix'] . 'center';
					break;
				case 'right' :
					$classes_outer[] = $arrows['prefix'] . 'right';
					break;
			}

			$prev = '<a href="#" id="' . esc_attr( substr( $arrows['id']['prev'], 1 ) ) . '" class="' . esc_attr( $arrows['prefix'] ) . 'prev" title=""></a>';
			$next = '<a href="#" id="' . esc_attr( substr( $arrows['id']['next'], 1 ) ) . '" class="' . esc_attr( $arrows['prefix'] ) . 'prev" title=""></a>';

			//basement-carousel-merged-controls clearfix
			$merge_controls = sprintf( '<div class="%1$s">%2$s%3$s%4$s</div>',
				esc_attr( implode( ' ', $classes_outer ) ),
				$arrows ? $prev : '',
				isset($dots) && !empty($dots) ? $this->generate_html_dots( $dots ) : '',
				isset($arrows) && !empty($arrows) ? $next : ''
			);

		} elseif ( $dots ) {
			$merge_controls = $this->generate_html_dots( $dots );
		}

		return $merge_controls;
	}


	/**
	 * Generate html arrows
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	private function generate_html_arrows( $params = array() ) {

		$classes = array();
		$prefix  = $params['prefix'];

		extract( $params['params'] );

		$classes[] = substr( $prefix, 0, - 1 ) . 's';

		if ( ! empty( $type ) ) {
			switch ( $type ) {
				case 'wobg' :
					$classes[] = $prefix . 'nobg';
					break;
				case 'bg' :
					$classes[] = $prefix . 'bg';
					break;
			}
		}

		if ( ! empty( $color ) ) {
			switch ( $color ) {
				case 'light' :
					$classes[] = $prefix . 'light';
					break;
				case 'standart' :
					$classes[] = $prefix . 'standart';
					break;
				case 'dark' :
					$classes[] = $prefix . 'dark';
					break;
			}
		}

		if ( ! empty( $size ) ) {
			switch ( $size ) {
				case 'small' :
					$classes[] = $prefix . 'small';
					break;
				case 'medium' :
					$classes[] = $prefix . 'medium';
					break;
				case 'large' :
					$classes[] = $prefix . 'large';
					break;
			}
		}

		if ( ! empty( $position ) ) {
			switch ( $position ) {
				case 'inside' :
					$classes[] = $prefix . 'inside';
					break;
				case 'outside' :
					$classes[] = $prefix . 'outside';
					break;
				case 'inrow' :
					$classes[] = $prefix . 'inrow';
					break;
			}
		}


		if ( ! empty( $y ) ) {
			switch ( $y ) {
				case 'top' :
					$classes[] = $prefix . 'top';
					break;
				case 'bottom' :
					$classes[] = $prefix . 'bottom';
					break;
				case 'side' :
					$classes[] = $prefix . 'side';
					break;
			}
		}

		if ( ! empty( $x ) ) {
			switch ( $x ) {
				case 'left' :
					$classes[] = $prefix . 'left';
					break;
				case 'center' :
					$classes[] = $prefix . 'center';
					break;
				case 'right' :
					$classes[] = $prefix . 'right';
					break;
			}
		}


		$arrows = sprintf( '<div class="%1$s"><a href="#" id="%2$s" class="' . $prefix . 'prev" title=""></a><a href="#" id="%3$s" class="' . $prefix . 'next" title=""></a></div>',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( substr( $params['id']['prev'], 1 ) ),
			esc_attr( substr( $params['id']['next'], 1 ) )
		);

		return $arrows;
	}


	/**
	 * Generate html dots
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	private function generate_html_dots( $params = array() ) {

		$classes = array();
		$prefix  = $params['prefix'];

		$number_content = '';

		extract( $params['params'] );

		switch ( $type ) {
			case 'dots' :
				$classes[] = $prefix . 'dots';
				break;
			case 'number' :
				$classes[]      = $prefix . 'number';
				$number_content = '<span class="' . $prefix . 'current">1</span><ins>&mdash;</ins><span class="' . $prefix . 'all">1</span>';
				break;
		}

		switch ( $color ) {
			case 'light' :
				$classes[] = $prefix . 'light';
				break;
			case 'standart' :
				$classes[] = $prefix . 'standart';
				break;
			case 'dark' :
				$classes[] = $prefix . 'dark';
				break;
		}

		switch ( $size ) {
			case 'small' :
				$classes[] = $prefix . 'small';
				break;
			case 'medium' :
				$classes[] = $prefix . 'medium';
				break;
			case 'large' :
				$classes[] = $prefix . 'large';
				break;
		}


		switch ( $position ) {
			case 'inside' :
				$classes[] = $prefix . 'inside';
				break;
			case 'outside' :
				$classes[] = $prefix . 'outside';
				break;
		}

		switch ( $y ) {
			case 'top' :
				$classes[] = $prefix . 'top';
				break;
			case 'bottom' :
				$classes[] = $prefix . 'bottom';
				break;
		}

		switch ( $x ) {
			case 'left' :
				$classes[] = $prefix . 'left';
				break;
			case 'center' :
				$classes[] = $prefix . 'center';
				break;
			case 'right' :
				$classes[] = $prefix . 'right';
				break;
		}


		$dots = sprintf( '<div id="%1$s" class="%2$s">%3$s</div>',
			esc_attr( substr( $params['id'], 1 ) ),
			esc_attr( implode( ' ', $classes ) ),
			$number_content
		);

		return $dots;
	}


	/**
	 * Generate html slides for carousel
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	private function generate_html_slides( $params = array() ) {
		extract( $params = wp_parse_args( $params, array(
			'ids'        => array(),
			'classes'    => array(),
			'id'         => '',
			'style'      => array(),
			'attributes' => array(),
			'slug'       => false
		) ) );



		$slides = '';

		if ( $ids ) {
			foreach ( $ids as $id_slide ) {
				$post_slide = get_post( $id_slide );
				$content    = apply_filters( 'the_content', $post_slide->post_content );

				$slides .= sprintf( '<div %1$s %2$s %3$s data-slide-id="%4$s" %5$s %7$s>%6$s</div>',
					! empty( $classes ) ? 'class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '',
					! empty( $id ) ? 'id="' . esc_attr( $id ) . '"' : '',
					! empty( $attributes ) ? $this->generate_html_attributes( $attributes ) : '',
					esc_attr( $id_slide ),
					$slug ? 'data-slide-slug="' . esc_attr( $post_slide->post_name ) . '"' : '',
					$content,
					!empty($style) ? 'style="'.implode(';',$style).'"' : ''
				);
			}
		}

		return $slides;
	}


	/**
	 * Generate custom attributes
	 *
	 * @param $attributes
	 *
	 * @return string
	 */
	private function generate_html_attributes( $attributes ) {
		$html_attributes = '';
		if ( ! ( empty( $attributes ) && is_array( $attributes ) ) ) {
			foreach ( $attributes as $name => $value ) {
				$current_values   = explode( ' ', $name );
				$current_values[] = $value;

				$html_attributes .= trim( implode( '="', $current_values ), ' ' ) . '" ';
			}
		}

		return $html_attributes;
	}


	/**
	 * Convert hex to RGB
	 *
	 * @param $color
	 *
	 * @return array|bool
	 */
	protected function hexToRgb( $color ) {

		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}


		if ( strlen( $color ) == 6 ) {
			list( $red, $green, $blue ) = array(
				$color[0] . $color[1],
				$color[2] . $color[3],
				$color[4] . $color[5]
			);
		} elseif ( strlen( $cvet ) == 3 ) {
			list( $red, $green, $blue ) = array(
				$color[0] . $color[0],
				$color[1] . $color[1],
				$color[2] . $color[2]
			);
		} else {
			return false;
		}

		$red   = hexdec( $red );
		$green = hexdec( $green );
		$blue  = hexdec( $blue );

		return array(
			'red'   => $red,
			'green' => $green,
			'blue'  => $blue
		);
	}
}


vc_map( array(
	'base'        => 'basement_vc_carousel',
	'name'        => __( 'Carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
	'class'       => '',
	'icon'        => PATCH_CAROUSEL . 'assets/images/icon-vc-carousel.png',
	'category'    => __( 'Basement', BASEMENT_CAROUSEL_TEXTDOMAIN ),
	'description' => __( 'Creates a simple carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
	'params'      => array(
		array(
			'type'        => 'basement_choose_carousel',
			'heading'     => __( 'Carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'param_name'  => 'id',
			'admin_label' => true,
			'description' => __( 'Select the carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		),
		array(
			'type'        => 'basement_choose_slide',
			'heading'     => __( 'Slides', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'param_name'  => 'slides',
			'admin_label' => true,
			'description' => __( 'Select the slides.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
		)
	),
	'js_view'     => 'VcIconElementView_Backend'
) );


if ( ! function_exists( 'basement_vc_carousel_settings_field' ) ) {
	/**
	 * Register new VC field for carousel
	 *
	 * @param $settings
	 * @param $value
	 *
	 * @return string
	 */
	function basement_vc_carousel_settings_field( $settings, $value ) {
		$dom       = new DOMDocument( '1.0', 'UTF-8' );
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_choose_carousel' );
		$param_name    = ! empty( $settings['param_name'] ) ? esc_attr( $settings['param_name'] ) : '';
		$param_value   = isset( $value ) ? esc_attr( $value ) : '';
		$carousel_list = array(
			'' => __( 'Choose the carousel', BASEMENT_CAROUSEL_TEXTDOMAIN )
		);

		$args           = array(
			'post_type'   => 'carousel',
			'numberposts' => - 1
		);
		$carousel_posts = get_posts( $args );

		foreach ( $carousel_posts as $post ) {
			setup_postdata( $post );
			$carousel_list[ $post->ID ] = $post->post_title . ' #' . $post->ID;
		}
		wp_reset_postdata();


		if ( $carousel_posts ) {
			$select = $container->appendChild( $dom->createElement( 'select' ) );
			$select->setAttribute( 'class', 'wpb_vc_param_value wpb-input wpb-select dropdown ' . esc_attr( $settings['type'] ) . '_field' );
			$select->setAttribute( 'name', $param_name );
			foreach ( $carousel_list as $id => $title ) {
				$option = $select->appendChild( $dom->createElement( 'option', esc_attr( $title ) ) );
				$option->setAttribute( 'value', esc_attr( $id ) );
				if ( $param_value == $id ) {
					$option->setAttribute( 'selected', 'selected' );
				}
			}
		} else {
			$link = $container->appendChild( $dom->createElement( 'a', __( 'Add at least one carousel.', BASEMENT_CAROUSEL_TEXTDOMAIN ) ) );
			$link->setAttribute( 'href', 'post-new.php?post_type=carousel' );
			$link->setAttribute( 'target', '_blank' );
			$container->setAttribute( 'style', 'margin-top:14px;' );
		}

		return $dom->saveHTML( $container );
	}

	vc_add_shortcode_param( 'basement_choose_carousel', 'basement_vc_carousel_settings_field' );
}


if ( ! function_exists( 'basement_choose_slide_settings_field' ) ) {
	/**
	 * Register new VC field for carousel
	 *
	 * @param $settings
	 * @param $value
	 *
	 * @return string
	 */
	function basement_choose_slide_settings_field( $settings, $value ) {
		$dom       = new DOMDocument( '1.0', 'UTF-8' );
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_choose_slide' );
		$param_name  = ! empty( $settings['param_name'] ) ? esc_attr( $settings['param_name'] ) : '';
		$param_value = isset( $value ) ? esc_attr( $value ) : '';
		$tag         = 'carousel';
		$slides_list = array();
		$args        = array(
			'post_type'   => 'carousel_slide',
			'numberposts' => - 1
		);
		$slide_posts = get_posts( $args );

		foreach ( $slide_posts as $post ) {
			setup_postdata( $post );
			$slides_list[ $post->ID ] = array(
				'title' => $post->post_title . ' #' . $post->ID,

			);
		}
		wp_reset_postdata();


		if ( $slide_posts ) {
			$select = $container->appendChild( $dom->createElement( 'select' ) );
			$select->setAttribute( 'class', $tag . '_slide_add' );

			$first_option = $select->appendChild( $dom->createElement( 'option', __( 'Choose the slide', BASEMENT_CAROUSEL_TEXTDOMAIN ) ) );
			$first_option->setAttribute( 'value', '' );

			foreach ( $slides_list as $value => $option_param ) {
				$option_param_title = isset($option_param['title']) ? $option_param['title'] : __('Slide', BASEMENT_CAROUSEL_TEXTDOMAIN);
				if ( ! empty( $option_param_title ) && get_post_status ( $value ) === 'publish' ) {
					$option = $dom->createElement( 'option', esc_html( $option_param['title'] ) );
					$option->setAttribute( 'value', esc_attr( $value ) );
					$option->setAttribute( 'data-edit', get_edit_post_link( $value ) );
					$option->setAttribute( 'data-edit-title', __( 'Edit', BASEMENT_CAROUSEL_TEXTDOMAIN ) );
					$select->appendChild( $option );
				}
			}

			$drag_block = $container->appendChild( $dom->createElement( 'div' ) );
			$drag_block->setAttribute( 'class', $tag . '_slide_sortable' );

			$params = array(
				'type'  => 'hidden',
				'class' => $tag . '_slide_insert wpb_vc_param_value wpb-input wpb-select dropdown ' . esc_attr( $settings['type'] ) . '_field',
				'name'  => $param_name,
				'value' => $param_value
			);

			$input = new Basement_Form_Input( $params );
			$container->appendChild( $dom->importNode( $input->create(), true ) );


		} else {
			$link = $container->appendChild( $dom->createElement( 'a', __( 'Add at least one slide.', BASEMENT_CAROUSEL_TEXTDOMAIN ) ) );
			$link->setAttribute( 'href', 'post-new.php?post_type=carousel_slide' );
			$link->setAttribute( 'target', '_blank' );
			$container->setAttribute( 'style', 'margin-top:14px;' );
		}

		return $dom->saveHTML( $container );
	}

	vc_add_shortcode_param( 'basement_choose_slide', 'basement_choose_slide_settings_field', PATCH_CAROUSEL . 'assets/js/back-shortcodes.min.js' );
}