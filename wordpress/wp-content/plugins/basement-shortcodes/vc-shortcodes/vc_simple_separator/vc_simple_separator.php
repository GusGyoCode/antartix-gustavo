<?php

class WPBakeryShortCode_vc_simple_separator extends WPBakeryShortCode {

	protected function content( $atts, $content = null ) {

		$line_color = $width = $border_width = $text_color = $align = $el_class = $css_animation = $css = '';


		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );

		$align       = ! empty( $align ) ? 'text-' . $align : '';
		$class_to_filter = '';
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_simple_separator' . ' ' . $align . ' ' .$class_to_filter, $this->settings['base'], $atts );

		$icon       = '';

		$css_class   = strlen( $css_class ) > 0 ? ' ' . trim( esc_attr( $css_class ) ) : '';
		$line_color = isset($line_color) ? $line_color : '';
		$text_color = isset($text_color) ? $text_color : '';
		$title = isset($title) ? esc_html($title) : '';
		$inner_styles = array();
		$border_styles = array();

		if(!empty($title)) {
			if(!empty($text_color)) {
				$inner_styles[] = 'color:'.$text_color.';';
			}

			if(is_numeric($width)) {

				$width = str_replace( array(
					'px',
					' ',
				), array(
					'',
					'',
				), $width );


				$inner_styles[] = 'min-width:'.$width.'px;';
			} else {
				$inner_styles[] = 'display:block;';
			}


			if(!empty($line_color)) {
				$border_styles[] = 'background-color:'.$line_color.';';
			}

			if(!empty($border_width)) {
				$border_styles[] = 'height:'.$border_width.'px;';
			}

			if(!empty($inner_styles)) {
				$inner_styles = 'style="' . implode('',$inner_styles) .'"';
			} else {
				$inner_styles = '';
			}


			if(!empty($border_styles)) {
				$border_styles = 'style="' . implode('',$border_styles) .'"';
			} else {
				$border_styles = '';
			}



			$icon = sprintf('<div class="%1$s"><div %2$s>%4$s<span %3$s></span></div></div>', $css_class, $inner_styles,$border_styles, $title);
		}

		return $icon;
	}
}


vc_map( array(
	'base'        => 'vc_simple_separator',
	'name'        => __( 'Horizontal Title', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'class'       => '',
	'icon'        => BASEMENT_SHORTCODES_IMG . 'icon-vc-simple-separator.png',
	'category'    => __( 'Basement', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'description' => __( 'Adds a simple horizontal separator', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'params'      => array(
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Title', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'title'
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Width', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'width',
			'description' => __('Sets the block size in px (full width default).', BASEMENT_SHORTCODES_TEXTDOMAIN)
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
			'type'        => 'dropdown',
			'heading'     => __( 'Text alignment', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'align',
			'value'       => array(
				__( 'Left', BASEMENT_SHORTCODES_TEXTDOMAIN )   => 'left',
				__( 'Right', BASEMENT_SHORTCODES_TEXTDOMAIN )  => 'right',
				__( 'Center', BASEMENT_SHORTCODES_TEXTDOMAIN ) => 'center',
			),
			'description' => __( 'Select text alignment.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'std'         => 'left'
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