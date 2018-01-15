<?php
defined('ABSPATH') or die();

class Basement_Visualcomposer {

	private static $instance = null;
	private $version = '1.0.0';

	public static function enabled() {
		return in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

}