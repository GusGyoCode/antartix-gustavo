<?php
defined('ABSPATH') or die();

class Basement_Settings {

	private static $instance = null;

	public function __construct() {
		Basement_Settings_Theme::init();
		Basement_Settings_Post::init();
	}

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Settings();
		}
		return self::$instance;
	}

}