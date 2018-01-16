<?php
defined('ABSPATH') or die();

class Basement_Autoload {

	private static $instance = null;

	/**
	 * Init class instance
	 */
	public function __construct() {
		spl_autoload_register( array( &$this, 'autoload' ) );
	}

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Autoload();
		}
		return self::$instance;
	}

	public function autoload( $class ) {
		if ( class_exists( $class ) || strpos( $class, 'Basement_' ) === -1 ) {
			return;
		}
		$module_name = str_replace( 'Basement_', '', $class );
		$class_name_parts = explode( '_', $module_name );
		$file_name = strtolower( array_pop( $class_name_parts ) );
		$base_path = Basement::directory() . "/modules/" . strtolower( str_replace( '_', '/', $module_name ) );

		if ( is_readable( ( $class_path = $base_path .'.php' ) ) || 
				is_readable( ( $class_path = $base_path . '/' . $file_name .'.php' ) ) ) {
			require_once $class_path;
		}
	}

}



















