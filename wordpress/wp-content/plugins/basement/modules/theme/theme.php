<?php
defined('ABSPATH') or die();

class Basement_Theme {

	private static $instance = null;

	public function __construct() {
		$this->define_constants();
		add_filter( 'basement_framework_theme_settings_page_params', array( &$this, '_theme_settings_page_params_filter' ) );
		add_filter( 'basement_framework_theme_settings_page_params', array( &$this, 'theme_settings_page_params_filter' ) );

		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'), 666);
	}

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Theme();
		}
		return self::$instance;
	}

	public function enqueue_scripts() {
		#wp_enqueue_style(BASEMENT_TEXTDOMAIN.'-theme-main', Basement::url() . '/assets/css/theme-main-css.css', false, null );
		#wp_enqueue_script(BASEMENT_TEXTDOMAIN.'-theme-main', Basement::url().'/assets/javascript/theme-main-js.js', false, '1.3', true);
	}

	public function define_constants() {
		/**
		 * Set the theme textdomain which is equal the theme name
		 */
		if ( !defined( 'THEME_TEXTDOMAIN' ) ) {
			define( 'THEME_TEXTDOMAIN', wp_get_theme()->get_template() );
		}


		if ( !defined( 'IMAGES' ) ) {
			define( 'IMAGES', get_template_directory_uri() . '/assets/images');
		}
	}

	public function _theme_settings_page_params_filter( $params = array() ) {
		$params[ 'form_type' ] = 'simple_options';
		return $params;
	}

	public function theme_settings_page_params_filter( $params = array() ) { 
		return $params; 
	}

}