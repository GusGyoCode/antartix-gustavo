<?php
defined('ABSPATH') or die();

class Basement_Settings_Theme {

	private static $instance = null;
	private $version = '1.0.0';

	public function __construct() {
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Settings_Theme();
		}
		return self::$instance;
	}

	public function register_settings() {
		$settings = Basement_Config::section( 'settings' );
		if ( !is_array( $settings ) ) {
			return;
		}

		foreach ($settings as $setting ) {
			if ( empty( $setting[ 'blocks' ] ) || !is_array( $setting[ 'blocks' ] ) ) {
				continue;
			}
			foreach ( $setting[ 'blocks' ] as $block ) {
				if ( empty( $block ) || !is_array( $block[ 'inputs' ] ) ) {
					continue;
				}
				foreach ( $block[ 'inputs' ] as $input ) {
					if ( !empty( $input[ 'name' ] ) ) {
						register_setting( 'basement_theme_options', $input[ 'name' ] );
					}
				}
			}
		}
	}



	/**
	 * Adds menu item for plugin page
	 */
	public function admin_menu() {
		add_theme_page(
			__( 'Theme Settings', BASEMENT_TEXTDOMAIN),
			__( 'Theme Settings', BASEMENT_TEXTDOMAIN),
			'manage_options',
			'basement_theme_options',
			array( &$this, 'theme_settings_page' )
		);
	}

	/**
	 * Renders theme settings page
	 */
	public function theme_settings_page() {
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$dom->appendChild( $dom->createElement( 'h2', __( 'Theme settings', BASEMENT_TEXTDOMAIN ) ) );

		$dom->appendChild(
			$dom->importNode(
				Basement_Settings_Panel::instance()->create_panel(
					/**
					 * Filter: basement_framework_theme_settings_config_filter
					 * Filter for theme setting page config
					 */
					apply_filters( BASEMENT_TEXTDOMAIN . '_theme_settings_config_filter', Basement_Config::section( 'settings' ) ),
					array(
						'form_type' => 'simple_options'
					)
				),
				true
			)
		);
		echo $dom->saveHTML();
	}

}