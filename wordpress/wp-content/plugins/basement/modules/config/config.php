<?php
defined('ABSPATH') or die();

class Basement_Config {

	private static $config = array();

	public static function section( $section ) {
		if ( !isset( self::$config[ $section ] ) ) {
			$config_path = get_template_directory() . '/basement/configs/' . $section . '.php';
			if ( file_exists( $config_path ) &&
				( is_array( $config = require $config_path ) ) ) {
				self::$config[ $section ] = apply_filters( 'basement_filter_config_' . $section, $config );
			} else {
				self::$config[ $section ] = null;
			}
		}

		return !empty( self::$config[ $section ] ) ? self::$config[ $section ] : array() ;
	}

}



















