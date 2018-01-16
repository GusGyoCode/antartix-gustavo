<?php
defined('ABSPATH') or die();

class Basement_Admin_Colorscheme {

	private static $instance = null;

	/**
	 * Init class instance
	 */
	public function __construct() {
		add_action( 'admin_footer', array( &$this, 'print_color_scheme_css' ) );
	}

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Admin_Colorscheme();
		}
		return self::$instance;
	}

	public function print_color_scheme_css() {
		global $_wp_admin_css_colors;
		$color = get_user_meta(get_current_user_id(), 'admin_color', true);
		
		if ( $color && isset( $_wp_admin_css_colors[ $color ] ) ) {
			$colors = $_wp_admin_css_colors[ $color ]->colors;
		} else {
			$colors = reset( $_wp_admin_css_colors )->colors;
		}

		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$style = $dom->appendChild( $dom->createElement( 'style' ) );
		$style->appendChild( $dom->createTextNode( '
			.basement_admin_background_color_1 { background-color: ' . $colors[ 0 ] . ';}
			.basement_admin_background_color_2 { background-color: ' . $colors[ 1 ] . ';}
			.basement_admin_background_color_3 { background-color: ' . $colors[ 2 ] . ';}
			.basement_admin_background_color_4 { background-color: ' . $colors[ 3 ] . ';}

			.basement_admin_active_background_color_1.active { background-color: ' . $colors[ 0 ] . ';}
			.basement_admin_active_background_color_2.active { background-color: ' . $colors[ 1 ] . ';}
			.basement_admin_active_background_color_3.active { background-color: ' . $colors[ 2 ] . ';}
			.basement_admin_active_background_color_4.active { background-color: ' . $colors[ 3 ] . ';}

			.basement_admin_active_hover_background_color_1.active:hover { background-color: ' . $colors[ 0 ] . ';}
			.basement_admin_active_hover_background_color_2.active:hover { background-color: ' . $colors[ 1 ] . ';}
			.basement_admin_active_hover_background_color_3.active:hover { background-color: ' . $colors[ 2 ] . ';}
			.basement_admin_active_hover_background_color_4.active:hover { background-color: ' . $colors[ 3 ] . ';}

			.basement_admin_hover_background_color_1:hover { background-color: ' . $colors[ 0 ] . ';}
			.basement_admin_hover_background_color_2:hover { background-color: ' . $colors[ 1 ] . ';}
			.basement_admin_hover_background_color_3:hover { background-color: ' . $colors[ 2 ] . ';}
			.basement_admin_hover_background_color_4:hover { background-color: ' . $colors[ 3 ] . ';}

			.basement_admin_color_1 { color: ' . $colors[ 0 ] . ';}
			.basement_admin_color_2 { color: ' . $colors[ 1 ] . ';}
			.basement_admin_color_3 { color: ' . $colors[ 2 ] . ';}
			.basement_admin_color_4 { color: ' . $colors[ 3 ] . ';}

			.basement_admin_hover_color_1:hover { color: ' . $colors[ 0 ] . ';}
			.basement_admin_hover_color_2:hover { color: ' . $colors[ 1 ] . ';}
			.basement_admin_hover_color_3:hover { color: ' . $colors[ 2 ] . ';}
			.basement_admin_hover_color_4:hover { color: ' . $colors[ 3 ] . ';}

			.basement_admin_active_color_1.active { color: ' . $colors[ 0 ] . ';}
			.basement_admin_active_color_2.active { color: ' . $colors[ 1 ] . ';}
			.basement_admin_active_color_3.active { color: ' . $colors[ 2 ] . ';}
			.basement_admin_active_color_4.active { color: ' . $colors[ 3 ] . ';}

			.active > .basement_admin_right_after_active_color_1 { color: ' . $colors[ 0 ] . ';}
			.active > .basement_admin_right_after_active_color_2 { color: ' . $colors[ 1 ] . ';}
			.active > .basement_admin_right_after_active_color_3 { color: ' . $colors[ 2 ] . ';}
			.active > .basement_admin_right_after_active_color_4 { color: ' . $colors[ 3 ] . ';}

			input:checked + .basement_admin_active_color_1 { color: ' . $colors[ 0 ] . ';}
			input:checked + .basement_admin_active_color_2 { color: ' . $colors[ 1 ] . ';}
			input:checked + .basement_admin_active_color_3 { color: ' . $colors[ 2 ] . ';}
			input:checked + .basement_admin_active_color_4 { color: ' . $colors[ 3 ] . ';}

			input:checked + .basement_admin_active_border_color_1 { border-color: ' . $colors[ 0 ] . ';}
			input:checked + .basement_admin_active_border_color_2 { border-color: ' . $colors[ 1 ] . ';}
			input:checked + .basement_admin_active_border_color_3 { border-color: ' . $colors[ 2 ] . ';}
			input:checked + .basement_admin_active_border_color_4 { border-color: ' . $colors[ 3 ] . ';}

			.basement_admin_border_color_1 { border-color: ' . $colors[ 0 ] . ';}
			.basement_admin_border_color_2 { border-color: ' . $colors[ 1 ] . ';}
			.basement_admin_border_color_3 { border-color: ' . $colors[ 2 ] . ';}
			.basement_admin_border_color_4 { border-color: ' . $colors[ 3 ] . ';}

			.basement_admin_border_hover_color_1:hover { border-color: ' . $colors[ 0 ] . ';}
			.basement_admin_border_hover_color_2:hover { border-color: ' . $colors[ 1 ] . ';}
			.basement_admin_border_hover_color_3:hover { border-color: ' . $colors[ 2 ] . ';}
			.basement_admin_border_hover_color_4:hover { border-color: ' . $colors[ 3 ] . ';}
		' ) );

		echo $dom->saveHTML();
	}

}



















