<?php
if ( ! class_exists( 'WPBakeryShortCode_vc_countdown' ) ) {
	class WPBakeryShortCode_vc_countdown extends WPBakeryShortCode {

		protected function content( $atts, $content = null ) {

			$date = $time = $style = $align = $el_class = $css_animation = $css = '';

			$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
			extract( $atts );

			$countdown    = '';
			$countdown_id = uniqid( 'countdown-' );

			$class_to_filter = '';
			$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
			$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
			$css_class = strlen( $css_class ) > 0 ? ' ' . trim( esc_attr( $css_class ) ) : '';

			if (
				$date && preg_match( '/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/', $date ) ||
				$time && preg_match( '/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/', $time )
			) {
				$countdown = sprintf( '<div class="vc_countdown_element %4$s"><div class="vc_countdown %5$s %3$s" id="' . $countdown_id . '" %1$s %2$s></div></div>',
					! empty( $date ) ? 'data-date="' . esc_attr( $date ) . '"' : '',
					! empty( $time ) ? 'data-time="' . esc_attr( $time ) . '"' : '',
					! empty( $align ) ? 'text-' . esc_attr( $align ) : '',
					esc_attr( $css_class ),
					! empty( $style ) ? 'vc_countdown_style_' . $style : ''
				);
			}


			return $countdown;
		}
	}

	vc_map( array(
		'base'        => 'vc_countdown',
		'name'        => __( 'Countdown', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'class'       => '',
		'icon'        => BASEMENT_SHORTCODES_IMG . 'icon-vc-countdown.png',
		'category'    => __( 'Basement', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'description' => __( 'Sets Countdown to any date', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'params'      => array(
			array(
				'type'        => 'basement_vc_date',
				'heading'     => __( 'Date', BASEMENT_SHORTCODES_TEXTDOMAIN ),
				'param_name'  => 'date',
				"holder"      => "div",
				"class"       => "",
				'description' => __( 'Sets the end date.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			),
			array(
				'type'        => 'basement_vc_time',
				'heading'     => __( 'Time', BASEMENT_SHORTCODES_TEXTDOMAIN ),
				'param_name'  => 'time',
				"holder"      => "div",
				"class"       => "",
				'description' => __( 'Sets the end time.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Style', BASEMENT_SHORTCODES_TEXTDOMAIN ),
				'param_name'  => 'style',
				'admin_label' => true,
				'value'       => array(
					__( 'Dark', BASEMENT_SHORTCODES_TEXTDOMAIN )  => 'dark',
					__( 'Light', BASEMENT_SHORTCODES_TEXTDOMAIN ) => 'light'
				),
				'description' => __( 'Sets the countdown style.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Countdown alignment', BASEMENT_SHORTCODES_TEXTDOMAIN ),
				'param_name'  => 'align',
				'value'       => array(
					__( 'Left', BASEMENT_SHORTCODES_TEXTDOMAIN )   => 'left',
					__( 'Right', BASEMENT_SHORTCODES_TEXTDOMAIN )  => 'right',
					__( 'Center', BASEMENT_SHORTCODES_TEXTDOMAIN ) => 'center',
				),
				'description' => __( 'Select countdown alignment.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
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


	if ( ! function_exists( 'basement_vc_date_settings_field' ) ) {
		/**
		 * Date field for Datepicker
		 */
		function basement_vc_date_settings_field( $settings, $value ) {
			return '<div class="basement_vc_date_block">'
			       . '<input data-mask="yy/mm/dd" name="' . esc_attr( $settings['param_name'] ) . '" class="input-datepicker wpb_vc_param_value wpb-textinput ' .
			       esc_attr( $settings['param_name'] ) . ' ' .
			       esc_attr( $settings['type'] ) . '_field" type="text" value="' . esc_attr( $value ) . '" />'
			       . '</div>';
		}

		vc_add_shortcode_param( 'basement_vc_date', 'basement_vc_date_settings_field', BASEMENT_SHORTCODES_URL . 'vc-shortcodes/vc_countdown/countdown.js' );
	}

	if ( ! function_exists( 'basement_vc_time_settings_field' ) ) {
		/**
		 * Time field for Timerpicker
		 *
		 */
		function basement_vc_time_settings_field( $settings, $value ) {
			return '<div class="basement_vc_time_block">'
			       . '<input data-mask="hh:mm:ss" name="' . esc_attr( $settings['param_name'] ) . '" class="input-timepicker wpb_vc_param_value wpb-textinput ' .
			       esc_attr( $settings['param_name'] ) . ' ' .
			       esc_attr( $settings['type'] ) . '_field" type="text" value="' . esc_attr( $value ) . '" />'
			       . '</div>';
		}

		vc_add_shortcode_param( 'basement_vc_time', 'basement_vc_time_settings_field' );
	}
}