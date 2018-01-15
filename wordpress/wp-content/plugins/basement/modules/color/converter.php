<?php
defined('ABSPATH') or die();

class Basement_Color_Converter {
	private static $instance = null;
	private $version = '1.0.0';

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Color_Converter();
		}
		return self::$instance;
	}

	public function hex_to_rgb( $hex ) {
		$hex = str_replace("#", "", $hex);

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ).substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ).substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ).substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}

		$rgb = new Basement_Color_Rgb( array($r, $g, $b) );

		return $rgb;
	}
}