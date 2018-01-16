<?php

class WPBakeryShortCode_vc_vertical_title extends WPBakeryShortCode {

	protected function content( $atts, $content = null ) {

		$title = $height = $align = $align_vertical = $style = $line_color = $text_color  = $border_width  = $el_class = $css_animation = $css = '';


		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );

		$align = ! empty( $align ) ? 'vc_vertical_' . $align : '';
		$align_vertical = ! empty( $align_vertical ) ? 'vc_vertical_' . $align_vertical : '';
		$style = ! empty( $style ) ? 'vc_vertical_' . $style : '';

		$class_to_filter = '';
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_vertical_title' . " {$align} {$align_vertical} {$style} " .$class_to_filter, $this->settings['base'], $atts );

		$vertical_icon = '';

		$css_class   = strlen( $css_class ) > 0 ? ' ' . trim( esc_attr( $css_class ) ) : '';
		$line_color = isset($line_color) ? $line_color : '';
		$text_color = isset($text_color) ? $text_color : '';
		$title = isset($title) ? esc_html($title) : '';
		$inner_styles = array();
		$text_styles = array();
		$border_styles = array();

		if(!empty($title)) {

			/*Styles For Span tag For title*/
			if(is_numeric($height)) {
				$height = str_replace( array(
					'px',
					' ',
				), array(
					'',
					'',
				), $height );
				$text_styles[] = 'width:'.$height.'px;';
			}
			if(!empty($text_color)) {
				$text_styles[] = 'color:'.$text_color.';';
			}
			if(!empty($text_styles)) {
				$text_styles = 'style="' . implode('',$text_styles) .'"';
			} else {
				$text_styles = '';
			}
			/*End styles for Span Tag*/



			/*Border Styles*/
			if(!empty($line_color)) {
				$border_styles[] = 'background-color:'.$line_color.';';
			}

			if(!empty($border_width)) {
				$border_styles[] = 'height:'.$border_width.'px;';
				if($border_width == 5 || $border_width == 6) {
					$border_styles[] = 'top:7px;';
				} elseif($border_width == 8) {
					$border_styles[] = 'top:6px;';
				} elseif($border_width == 10) {
					$border_styles[] = 'top:4px;';
				}
			}
			if(!empty($border_styles)) {
				$border_styles = 'style="' . implode('',$border_styles) .'"';
			} else {
				$border_styles = '';
			}
			/*End border styles*/





			/*Style for Vertical Block*/
			if(is_numeric($height)) {
				$height = str_replace( array(
					'px',
					' ',
				), array(
					'',
					'',
				), $height );

				$fullheigt = $height + 68;
				$inner_styles[] = 'min-height:'.$fullheigt.'px;';
			} else {
				$fullheigt = 140 + 68;
				$inner_styles[] = 'min-height:'.$fullheigt.'px;';
			}
			if(!empty($inner_styles)) {
				$inner_styles = 'style="' . implode('',$inner_styles) .'"';
			} else {
				$inner_styles = '';
			}
			/*End Style for Vertical Block*/



			$vertical_icon = sprintf('<div %5$s class="%4$s"><div class="rotated-text-body"><div class="rotated-text clearfix"><div class="rotated-text__inner"><ins class="rotated-text-bottom" %3$s></ins><span %1$s>%2$s</span><ins class="rotated-text-top" %3$s></ins></div></div></div></div>',
				$text_styles,
				$title,
				$border_styles,
				$css_class,
				$inner_styles
			);
		}

		return $vertical_icon;
	}
}


vc_map( array(
	'base'        => 'vc_vertical_title',
	'name'        => __( 'Vertical Title', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'class'       => '',
	'icon'        => BASEMENT_SHORTCODES_IMG . 'icon-vc-vertical-title.png',
	'category'    => __( 'Basement', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'description' => __( 'Adds a vertical title', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'params'      => array(
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Title', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'title'
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Height', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'height',
			'description' => __( 'Sets block height in px (default 140px).', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'        => 'dropdown',
			'heading'     => __( 'Horizontal alignment', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'align',
			'value'       => array(
				__( 'Left', BASEMENT_SHORTCODES_TEXTDOMAIN )   => 'left',
				__( 'Right', BASEMENT_SHORTCODES_TEXTDOMAIN )  => 'right',
				__( 'Center', BASEMENT_SHORTCODES_TEXTDOMAIN ) => 'center',
			),
			'description' => __( 'Sets the horizontal alignment.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'std'         => 'left'
		),
		array(
			'type'        => 'dropdown',
			'heading'     => __( 'Line position', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'align_vertical',
			'value'       => array(
				__( 'Top', BASEMENT_SHORTCODES_TEXTDOMAIN )   => 'top',
				__( 'Bottom', BASEMENT_SHORTCODES_TEXTDOMAIN )  => 'bottom'
			),
			'description' => __( 'Sets line alignment.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'std'         => 'top'
		),
		array(
			'type'        => 'dropdown',
			'heading'     => __( 'Style', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'style',
			'value'       => array(
				__( 'Dark', BASEMENT_SHORTCODES_TEXTDOMAIN )   => 'dark',
				__( 'Light', BASEMENT_SHORTCODES_TEXTDOMAIN )  => 'light'
			),
			'description' => __( 'Sets the title style.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'std'         => 'dark'
		),
		array(
			'type'        => 'colorpicker',
			'heading'     => __( 'Line color', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'line_color',
			'description' => __( 'Select custom line color.', BASEMENT_SHORTCODES_TEXTDOMAIN )
		),
		array(
			'type'        => 'colorpicker',
			'heading'     => __( 'Text color', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'text_color',
			'description' => __( 'Select custom text color.', BASEMENT_SHORTCODES_TEXTDOMAIN )
		),

		array(
			'type' => 'dropdown',
			'heading' => __( 'Border width', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name' => 'border_width',
			'value' => getVcShared( 'separator border widths' ),
			'description' => __( 'Select line width (in px).', BASEMENT_SHORTCODES_TEXTDOMAIN ),
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