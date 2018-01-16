<?php
/**
 * This file will be included if Basement Framework plugin is not installed
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

/**
 * Global variables for sidebars
 */
$basement_footer = array();
$basement_sidebar = array();


if ( ! function_exists( 'Basement_Header' ) ) {
	/**
	 * Main Basement Header Settings
	 *
	 * @return array
	 */
	function Basement_Header() {
		return array(
			'menu' => 'default',
			'menu_type'        => 'default', # default/simple
			'logo_text'        => __( 'Co&ntilde;ico', 'conico' ), # just string
			'logo_text_size'   => '', # int number w/o px
			'logo_text_color'  => '', # color in hex format
			'logo_image'       => '', # image ID
			'logo_link'        => esc_url( home_url( '/' ) ), # blog url
			'logo_link_toggle' => 'yes', # yes/no
			'logo_position'    => 'left', # left/right/center_left/center_right
			'header_off'       => 'no', # yes/no
			'header_helper'    => 'no',
			'header_size'     => 'fullwidth',
			'header_sticky'    => 'enable', # enable/disable
			'header_elements'  => array( # disable/enable elements
				'logo_image'     => false,
				'logo_text'      => true,
				'menu'           => true,
				'search_section' => true,
				'button_section' => false,
				'user_section'   => false,
				'lang_section'   => true
			),
			'header_style'   => 'dark', # header style dark/white
			'header_bg'                 => '',
			'header_opacity'             => '',
			'header_border_bg'           => '',
			'header_border_opacity'      => '',
			'header_border_size'         => 'fullwidth',
			'header_padding_top'         => '',
			'header_padding_bottom'      => '',
			'header_btn_text'            => __('Contact','conico'),
			'header_btn_icon'            => 'icon-mail',
			'header_btn_link'            => esc_url( home_url( '/' ) ),
			'header_global_border'       => 'no',
			'header_global_border_color' => '',
			'header_global_border_size'  => '',

		);
	}
}


if ( ! function_exists( 'Basement_Page_Title' ) ) {
	function Basement_Page_Title() {
		return array(
			'pt_placement'  => 'under',
			'pt_style'      => 'dark',
			'pt_elements'   => array(
				'icon' => false,
				'title' => true,
				'line' => true,
				'breadcrumbs' => true,
				'breadcrumbs_last' => false
			),
			'pt_icon'       => '',
			'pt_icon_size'  => '',
			'pt_icon_color' => '',
			'pt_position'   => 'center_right',
			'pt_title_size'   => '',
			'pt_title_color'   => '',

			'pt_float_enable' =>  'no',
			'pt_float_text_size' => '',
			'pt_float_text_color' => '',

			'pt_bg' => '',
			'pt_bg_color' => '',
			'pt_bg_opacity' => '',
			'pt_padding_top'    =>  '',
			'pt_padding_bottom' =>  '',
			'pt_off'        => 'no',
			'pt_alternate'  =>  ''
		);
	}
}


/**
 * No basement Header
 */
require( trailingslashit( get_template_directory() ) . 'inc/no-basement/header.php' );


/**
 * No basement Page Title
 */
require( trailingslashit( get_template_directory() ) . 'inc/no-basement/page-title.php' );




if ( ! function_exists( 'basement_sidebar' ) ) {
	/**
	 * Displays sidebar on page
	 *
	 * @param string $type
	 * @param string $file
	 */
	function basement_sidebar( $type = 'page', $file = '' ) {
		// get_sidebar( $file );
	}
}


if ( ! function_exists( 'basement_content_classes' ) ) {
	/**
	 * Generate classes for main content
	 *
	 * @param bool   $echo
	 * @param string $type
	 *
	 * @return string
	 */
	function basement_content_classes( $type = 'page', $echo = false ) {

		$classes = 'col-lg-12 page-content-cell maincontent';

		if ( $echo ) {
			echo esc_attr($classes);
		} else {
			return $classes;
		}

	}
}


if ( ! function_exists( 'basement_sidebar_classes' ) ) {
	/**
	 * Generate classes for sidebar
	 */
	function basement_sidebar_classes($inline_classes = '', $inline_style = '', $echo = true) {
		$classes = array('col-md-3 col-sm-4 sidebar-content-cell');
		$classes[] = $inline_classes;


		if ( $echo ) {
			echo 'class="' . implode( ' ',  $classes  ) .'"';
		} else {
			return implode( ' ',  $classes );
		}
	}
}


if ( ! function_exists( 'basement_footer_class' ) ) {
	/**
	 * Generate classes for footer
	 */
	function basement_footer_class( $class = '' ) {
		echo 'class="footer_type_page footer_yes footer_line_yes footer_area_sidebar-2 footer_style_dark footer_sticky_disable scope_public footer ' .  esc_html($class) .'"';
	}
}


if ( ! function_exists( 'basement_footer_sort_elements' ) ) {
	/**
	 * Displays footer page
	 */
	function basement_footer_sort_elements( $file ) {
		global $wp_registered_sidebars, $basement_footer;
		if ( empty( $file ) ) {
			$file = 'footer';
		}
		if ( ! empty( $wp_registered_sidebars ) ) {
			$basement_footer = array(
				'sidebar' => 'sidebar-1',
				'place' => 'footer'
			);
			get_sidebar( $file );
		}
	}

	add_action( 'conico_content_footer', 'basement_footer_sort_elements');
}




