<?php
defined('ABSPATH') or die();

class Basement_Hook {

	private static $instance = null;

	public function __construct() {
		self::run( 'after_setup_theme' );
		$this->add_actions();
	}

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Hook();
		}
		return self::$instance;
	}

	public function add_actions() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'action' ) );
		add_action( 'wp_head', array( &$this, 'action' ), 999 );
	}

	public function action() {
		self::run( current_filter() );
	}

	public static function file( $hook ) {
		$hook = get_template_directory() . '/hooks/' . $hook . '.php';
		if ( file_exists( $hook ) ) {
			return $hook;
		}
		return false;
	}

	public static function run( $hook ) {
		if ( ( $hook = self::file( $hook ) ) ) {
			require $hook;
		}
	}

}



















