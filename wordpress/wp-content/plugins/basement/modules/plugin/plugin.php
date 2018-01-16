<?php
defined('ABSPATH') or die();

class Basement_Plugin {

	protected $post_types = array();
	protected $vendor_assets = array();
	protected $reflector;
	protected $url = null;

	public function __construct() {
		$this->reflector = new ReflectionClass( get_class( $this ) );
		$this->load_textdomain();
		#$this->enqueue_vendor_assets();
		$this->load_modules();

		add_filter( 'basement_shortcodes_groups_config', array( &$this, 'register_shortcodes_groups' ) );
		add_filter( 'basement_shortcodes_config', array( &$this, 'register_shortcodes' ) );
		add_action( 'wp_enqueue_scripts', array( &$this,'basement_plugins' ) );

		if ( Basement_Visualcomposer::enabled() ) {
			add_action( 'vc_before_init', array( $this, 'integrate_with_visual_composer' ) );
		}
	}


	public function basement_plugins() {
		$url = Basement::url() . '/assets/vendor/';
		$url_front = Basement::url() . '/assets/javascript/';
		wp_register_script( 'basement_plugins', $url . 'basement-plugins.js', array( 'jquery' ), '1.0', true );

		wp_register_script( 'basement-javascript', $url_front . 'basement-front.min.js', array( 'jquery', 'basement_plugins' ), '1.0', true );
		wp_enqueue_script('basement-javascript');
	}


	protected function dir() {
		return realpath( dirname( $this->reflector->getFileName() ) . '/../../');
	}


	public function url() {
		if ( null === $this->url ) {
			$this->url = Basement_Url::of_file( realpath( $this->dir() ) );
		}
		return $this->url;
	}


	public function zurl() {
		if ( null === $this->url ) {
			$this->url = '/wp-content/plugins/'.plugin_basename( $this->dir() );
		}
		return $this->url;
	}

	public function load_textdomain() {
		load_plugin_textdomain( $this->textdomain(), false, '/' . plugin_basename( $this->dir() ) . '/translations' );
	}

	protected function textdomain() {
		return BASEMENT_TEXTDOMAIN;
	}

	protected function load_modules() {
		$modules = array();
		if ( !empty( $this->post_types ) ) {
			foreach ( $this->post_types as $post_type) {
				$modules[] = realpath( $this->dir() . '/modules/cpt/' . $post_type . '.php' );
			}
		}

		if ( !is_array( $modules ) || empty( $modules ) ) {
			return;
		}

		foreach ( $modules as $module ) {
			if ( file_exists( $module ) ) {
				require_once $module;
			}
		}
	}


	/**
	 * Load necessary views
	 */
	public function basement_views( $params , $files ) {

		$views = array();
		if ( !empty( $files ) ) {
			foreach ( $files as $view) {
				$views[] = realpath( $this->dir() . '/modules/views/' . $view . '.php' );
			}
		}

		if ( !is_array( $views ) || empty( $views ) ) {
			return;
		}

		foreach ( $views as $view ) {
			if ( file_exists( $view ) ) {
				require_once $view;
			}
		}
	}

	public function register_shortcodes( $config ) { return $config; }

	public function register_shortcodes_groups( $config ) { return $config; }

	public function integrate_with_visual_composer() {}
}