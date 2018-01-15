<?php

class WPBakeryShortCode_vc_counter extends WPBakeryShortCode {

	protected function getParamData( $key ) {
		return WPBMap::getParam( $this->shortcode, $this->getField( $key ) );
	}

	protected $fields = array(
		'google_fonts' => 'google_fonts',
		#'font_container' => 'font_container',
		'el_class' => 'el_class',
		'css' => 'css',
		'text' => 'text',
	);

	protected function getField( $key ) {
		return isset( $this->fields[ $key ] ) ? $this->fields[ $key ] : false;
	}

	public function getStyles( $el_class, $css, $google_fonts_data, $font_container_data, $atts ) {
		$styles = array();
		if ( ! empty( $font_container_data ) && isset( $font_container_data['values'] ) ) {
			foreach ( $font_container_data['values'] as $key => $value ) {
				if ( 'tag' !== $key && strlen( $value ) ) {
					if ( preg_match( '/description/', $key ) ) {
						continue;
					}
					if ( 'font_size' === $key || 'line_height' === $key ) {
						$value = preg_replace( '/\s+/', '', $value );
					}
					if ( 'font_size' === $key ) {
						$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
						// allowed metrics: http://www.w3schools.com/cssref/css_units.asp
						$regexr = preg_match( $pattern, $value, $matches );
						$value = isset( $matches[1] ) ? (float) $matches[1] : (float) $value;
						$unit = isset( $matches[2] ) ? $matches[2] : 'px';
						$value = $value . $unit;
					}
					if ( strlen( $value ) > 0 ) {
						$styles[] = str_replace( '_', '-', $key ) . ': ' . $value;
					}
				}
			}
		}
		if ( ( ! isset( $atts['use_theme_fonts'] ) || 'yes' !== $atts['use_theme_fonts'] ) && ! empty( $google_fonts_data ) && isset( $google_fonts_data['values'], $google_fonts_data['values']['font_family'], $google_fonts_data['values']['font_style'] ) ) {
			$google_fonts_family = explode( ':', $google_fonts_data['values']['font_family'] );
			$styles[] = 'font-family:' . $google_fonts_family[0];
			$google_fonts_styles = explode( ':', $google_fonts_data['values']['font_style'] );
			$styles[] = 'font-weight:' . $google_fonts_styles[1];
			$styles[] = 'font-style:' . $google_fonts_styles[2];
		}

		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_countup ' . $el_class . vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );

		return array(
			'css_class' => trim( preg_replace( '/\s+/', ' ', $css_class ) ),
			'styles' => $styles,
		);
	}

	public function getAttributes( $atts ) {
		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );


		$google_fonts_field = $this->getParamData( 'google_fonts' );
		#$font_container_field = $this->getParamData( 'font_container' );

		$el_class = $this->getExtraClass( $el_class );
		#$font_container_obj = new Vc_Font_Container();
		$google_fonts_obj = new Vc_Google_Fonts();
		#$font_container_field_settings = isset( $font_container_field['settings'], $font_container_field['settings']['fields'] ) ? $font_container_field['settings']['fields'] : array();
		$google_fonts_field_settings = isset( $google_fonts_field['settings'], $google_fonts_field['settings']['fields'] ) ? $google_fonts_field['settings']['fields'] : array();
		#$font_container_data = $font_container_obj->_vc_font_container_parse_attributes( $font_container_field_settings, $font_container );
		$google_fonts_data = strlen( $google_fonts ) > 0 ? $google_fonts_obj->_vc_google_fonts_parse_attributes( $google_fonts_field_settings, $google_fonts ) : '';

		return array(
			#'text' => isset( $text ) ? $text : '',
			'google_fonts' => $google_fonts,
			#'font_container' => $font_container,
			'el_class' => $el_class,
			'css' => $css,
			#'link' => ( 0 === strpos( $link, '|' ) ) ? false : $link,
			#'font_container_data' => $font_container_data,
			'google_fonts_data' => $google_fonts_data,
		);
	}

	protected function content( $atts, $content = null ) {

		$start = $end = $speed = $sign_before = $size_sbefore = $color_sbefore = $google_fonts_data = $sign_after = $size_safter = $font_container_data = $color_safter = $font_size = $color = $align = $el_class = $css_animation = $css = '';
		extract( $this->getAttributes( $atts ) );


		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );


		/**
		 * @var $css_class
		 */
		extract( $this->getStyles( $el_class . $this->getCSSAnimation( $css_animation ), $css, $google_fonts_data, $font_container_data, $atts ) );


		#$class_to_filter = '';
		#$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
		#$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
		#$css_class   = strlen( $css_class ) > 0 ? ' ' . trim( esc_attr( $css_class ) ) : '';

		$settings = get_option( 'wpb_js_google_fonts_subsets' );
		if ( is_array( $settings ) && ! empty( $settings ) ) {
			$subsets = '&subset=' . implode( ',', $settings );
		} else {
			$subsets = '';
		}


		wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets );


		if ( ! empty( $styles ) ) {
			$vc_style = esc_attr( implode( ';', $styles ) );
		} else {
			$vc_style = '';
		}

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$end = ( float )$end;

		$split = explode('.', $end);

		$num_after = !empty($split['1']) ? strlen($split['1']) : -1;

		if ( !$end ) {
			$end = 1;
		}

		$start = ( float )$start;
		if ( !$start ) {
			$start = 0;
		}

		$speed = ( int )$speed * 100;
		if ( !$speed ) {
			$speed = 100;
		}

		$counterVC = $dom->appendChild( $dom->createElement('div') );
		$counterVC->setAttribute('class', esc_attr($css_class));

		$counterWrapper = $counterVC->appendChild( $dom->createElement('div') );
		$counterWrapper->setAttribute('class','countup');

		$style_wrap = '';
		if ( $align ) {
			switch($align) {
				case "center" :
					$style_wrap .= 'text-align: ' . $align . ';';
					break;
				case "right" :
					$style_wrap .= 'text-align: ' . $align . ';';
					break;
				default :
					$style_wrap .= 'text-align:left;';
					break;
			}

		}

		if ( $font_size ) {
			$style_wrap .= 'max-height:' . $font_size . 'px;';
		}


		if($style_wrap) {
			$counterWrapper->setAttribute( 'style', $style_wrap . $vc_style );
		}

		$counter = $counterWrapper->appendChild($dom->createElement('span'));


		$counter->setAttribute( 'class', esc_attr( 'basement_counter countup-amount' ) );
		$counter->setAttribute( 'id', uniqid('basement_counter-' ) );
		$counter->setAttribute( 'data-to', esc_attr( $end ) );
		$counter->setAttribute( 'data-speed', esc_attr( $speed ) );
		$counter->setAttribute( 'data-from', esc_attr( $start) );
		if($num_after > 0) {
			$counter->setAttribute( 'data-decimals', esc_attr( $num_after ) );
		}
		$counter->setAttribute( 'data-refresh-interval', esc_attr( '1' ) );

		if($sign_before) {
			$style_before = '';

			$span_before = $dom->createElement('span', esc_html( $sign_before ));
			$span_before->setAttribute('class', esc_attr('countup-sign' ));
			if($size_sbefore) {
				$style_before .= 'font-size:'.$size_sbefore.'px;';
			}

			if($color_sbefore) {
				$style_before .= 'color:'.$color_sbefore.';';
			}
			if($style_before) {
				$span_before->setAttribute('style', $style_before);
			}
			$counterWrapper->insertBefore($span_before,$counterWrapper->firstChild);
		}


		if($sign_after) {
			$style_after = '';

			$span_after = $counterWrapper->appendChild($dom->createElement('span', esc_html( $sign_after )));
			$span_after->setAttribute('class',esc_attr( 'countup-sign' ));


			if($size_safter) {
				$style_after .= 'font-size:'.$size_safter.'px;';
			}

			if($color_safter) {
				$style_after .= 'color:'.$color_safter.';';
			}
			if($style_after) {
				$span_after->setAttribute('style', $style_after);
			}
		}

		$style = '';

		if ( $color ) {
			$style .= 'color:' . $color . ';';
		}

		if ( $font_size ) {
			$style .= 'font-size:' . $font_size . 'px;';
		}

		if ( $style ) {
			$counter->setAttribute( 'style', $style );
		}

		return $dom->saveHTML();
	}
}



vc_map( array(
	'base'        => 'vc_counter',
	'name'        => __( 'Counter', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'class'       => '',
	'icon'        => BASEMENT_SHORTCODES_IMG . 'icon-vc-counter.png',
	'category'    => __( 'Basement', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'description' => __( 'Creates a simple counter', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'params'      => array(
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Start', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'start',
			'description' => __( 'Sets initial value to count from.(Important! The fractional part separated by a dot).', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'End', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'end',
			'description' => __( 'Sets final value to stop on.(Important! The fractional part separated by a dot).', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Speed', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'speed',
			'description' => __( 'The number of seconds it should take to finish counting. (Default 1 seconds).', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Sign before', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'sign_before',
			'description' => __( 'Sets a sign to show before a digit.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Sign before (font size)', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'size_sbefore'
		),
		array(
			'type'        => 'colorpicker',
			'heading'     => __( 'Sign before (color)', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'color_sbefore'
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Sign after', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'sign_after',
			'description' => __( 'Sets a sign to show after a digit.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Sign after (font size)', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'size_safter'
		),
		array(
			'type'        => 'colorpicker',
			'heading'     => __( 'Sign after (color)', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'color_safter'
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Font size', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'font_size',
			'description' => __( 'Sets font size of a counter. Use integer value w/o "px".', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),


		array(
			'type'        => 'colorpicker',
			'heading'     => __( 'Color', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'color',
			'description' => __( 'Set counters color.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),


		array(
			'type'        => 'dropdown',
			'heading'     => __( 'Counter alignment', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'align',
			'value'       => array(
				__( 'Left', BASEMENT_SHORTCODES_TEXTDOMAIN )   => 'left',
				__( 'Right', BASEMENT_SHORTCODES_TEXTDOMAIN )  => 'right',
				__( 'Center', BASEMENT_SHORTCODES_TEXTDOMAIN ) => 'center',
			),
			'description' => __( 'Choose counter alignment.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'std' => 'center'
		),
		array(
			'type' => 'google_fonts',
			'param_name' => 'google_fonts',
			'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
			'settings' => array(
				'fields' => array(
					'font_family_description' => __( 'Select font family.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
					'font_style_description' => __( 'Select font styling.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
				),
			)
		),
		vc_map_add_css_animation(),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Extra class name', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'       => 'css_editor',
			'heading'    => __( 'CSS box', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name' => 'css',
			'group'      => __( 'Design Options', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
	),
	'js_view'     => 'VcIconElementView_Backend'
) );
