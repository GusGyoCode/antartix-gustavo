<?php

class WPBakeryShortCode_vc_yashare extends WPBakeryShortCode {

	protected function content( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'title' => '',
			'type'  => 'dropdown',
			'css'   => ''
		), $atts ) );

		$title = isset( $title ) ? $title : '';
		$type  = isset( $type ) ? $type : '';
		$css   = isset( $css ) ? $css : '';

		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );


		$socials       = get_option( 'conico_social_sharing' );
		$socials_clean = array();

		if ( $socials ) {
			foreach ( $socials as $social ) {
				if ( ! empty( $social ) ) {
					$socials_clean[] = $social;
				}
			}
		}

		$share = '';

		$socials = $socials_clean ? implode( ',', $socials_clean ) : 'gplus,facebook,twitter';

		$id = uniqid('vc-share-');

		if ( $type === 'dropdown' ) {
			$btn            = '<i class="icon-share"></i>';
			$share_block    = sprintf( '<div class="theme-share ya-share2" data-services="%1$s"  ></div>', $socials );
			$share_dropdown = sprintf( '<a href="#" class="theme-share-dropdown theme-share" id="'.$id.'">' . $btn . '<div class="share-tooltip">' . $share_block . '</div></a>' );
			if ( ! empty( $title ) ) {
				$share = '<div class="vc_share_'.esc_attr($type).' clearfix theme-share-title ' . esc_attr( $css_class ) . '"><span>' . esc_html( $title ) . '</span>' . $share_dropdown . '</div>';
			} else {
				$share = '<div class="vc_share_'.esc_attr($type).' clearfix theme-share-title ' . esc_attr( $css_class ) . '">' . $share_dropdown . '</div>';
			}
		} elseif ( $type === 'horizontal' ) {
			$share_block      = sprintf( '<div class="theme-share ya-share2" data-services="%1$s"  ></div>', $socials );
			$share_horizontal = sprintf( '<div class="theme-share-horizontal theme-share" id="'.$id.'">' . $share_block . '</div>' );
			if ( ! empty( $title ) ) {
				$share = '<div class="vc_share_'.esc_attr($type).' clearfix theme-share-title ' . esc_attr( $css_class ) . '"><span>' . esc_html( $title ) . '</span>' . $share_horizontal . '</div>';
			} else {
				$share = '<div class="vc_share_'.esc_attr($type).' clearfix theme-share-title ' . esc_attr( $css_class ) . '">' . $share_horizontal . '</div>';
			}
		}


		$share .= $this->debugComment( 'vc_yashare' );

		return $share;
	}
}

vc_map( array(
	'base'        => 'vc_yashare',
	'name'        => __( 'Social Sharing', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'class'       => '',
	'icon'        => BASEMENT_SHORTCODES_IMG . 'icon-vc-sharing.png',
	'category' => __( 'Basement', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'description' => __( 'Adds simple social sharing block', BASEMENT_SHORTCODES_TEXTDOMAIN ),
	'params'      => array(
		array(
			'type'        => 'textfield',
			'class'       => '',
			'heading'     => __( 'Title', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'title',
			'description' => __( 'Sets the help text.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'        => 'dropdown',
			'heading'     => __( 'Type', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name'  => 'type',
			'value'       => array(
				__( 'Dropdown', BASEMENT_SHORTCODES_TEXTDOMAIN )   => 'dropdown',
				__( 'Horizontal', BASEMENT_SHORTCODES_TEXTDOMAIN ) => 'horizontal',
			),
			'description' => __( 'Set the view of social sharing block.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		),
		array(
			'type'       => 'css_editor',
			'heading'    => __( 'Css', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'param_name' => 'css',
			'group'      => __( 'Design options', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		)
	),
) );