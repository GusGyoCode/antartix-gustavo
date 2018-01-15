<?php
defined( 'ABSPATH' ) or die();


class Basement_Preloader {

	private static $instance = null;

	// Name for Google Fonts options
	private $options = array();

	// Block for Google Fonts options
	private $options_blocks = array();

	public function __construct() {

		$this->set_options_blocks();

		$this->set_options_names();

		add_action( 'admin_init', array( &$this, 'register_theme_settings' ) );

		add_filter(
			BASEMENT_TEXTDOMAIN . '_theme_settings_config_filter',
			array( &$this, 'theme_settings_config_filter' )
		);


	}

	public function vc_enabled() {
		return in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

	/**
	 * Set block for Google Fonts settings
	 */
	private function set_options_blocks() {
		$this->options_blocks = $this->options_blocks();
	}

	/**
	 * Set name for block in Google Fonts settings
	 */
	private function set_options_names() {
		$this->options = $this->options_names();
	}


	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Preloader();
		}

		return self::$instance;
	}





	/**
	 * List of option blocks settings
	 *
	 * @return array
	 */
	private function options_blocks() {
		return array(
			array(
				'type'        => 'dom',
				'title'       => __( 'Preloader', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Check off to not to show preloader and overlay on page loading.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'preloader_enable'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Background', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the background color for preloader.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'preloader_bg'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Color', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the color for preloader.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'preloader_color'
			)
		);
	}


	/**
	 * Enable / Disable preloader
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function preloader_enable( $type = '') {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$option = $this->options['preloader_enable'];

		$header_sticky = new Basement_Form_Input_Radio_Group( array(
				'name'          => $option,
				'id'            => $option,
				'current_value' => get_option( $option, 'disable' ) ,
				'values'        => array(
					'enable'  => __( 'Enable', BASEMENT_TEXTDOMAIN ),
					'disable' => __( 'Disable', BASEMENT_TEXTDOMAIN )
				)
			)
		);

		$container = $dom->appendChild( $dom->importNode( $header_sticky->create(), true ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		return $container;
	}


	/**
	 * Background for preloader
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function preloader_bg( $type = '') {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$option = $this->options['preloader_bg'];

		$header_sticky = new Basement_Form_Input_Colorpicker( array(
				'name'          => $option,
				'id'            => $option,
				'value' => esc_attr( get_option( $option, '' ) )
			)
		);

		$container = $dom->appendChild( $dom->importNode( $header_sticky->create(), true ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		return $container;
	}



	/**
	 * Color for preloader
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function preloader_color( $type = '') {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$option = $this->options['preloader_color'];

		$header_sticky = new Basement_Form_Input_Colorpicker( array(
				'name'          => $option,
				'id'            => $option,
				'value' => esc_attr( get_option( $option, '' ) )
			)
		);

		$container = $dom->appendChild( $dom->importNode( $header_sticky->create(), true ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		return $container;
	}


	/**
	 * List of option name settings
	 *
	 * @return array
	 */
	private function options_names() {
		return array(
			'preloader_enable' => BASEMENT_TEXTDOMAIN . '_preloader_enable',
			'preloader_bg' => BASEMENT_TEXTDOMAIN . '_preloader_bg',
			'preloader_color' => BASEMENT_TEXTDOMAIN . '_preloader_color'
		);
	}

	/**
	 * Register options for Google Fonts
	 */
	public function register_theme_settings() {
		foreach ( $this->options as $key => $value ) {
			register_setting( 'basement_theme_options', $value );
		}
	}


	/**
	 * Init Google Fonts params
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	public function theme_settings_config_filter( $config = array() ) {
		$settings_config = array(
			'preloader' => array(
				'title'  => __( 'Preloader', BASEMENT_TEXTDOMAIN ),
				'blocks' => array()
			)
		);

		foreach ( $this->options_blocks as $key => $value ) {
			$value['input'] = call_user_func( array( &$this, $value['input'] ) );
			$settings_config['preloader']['blocks'][] = $value;
		}

		return array_merge( $settings_config, $config );
	}

}



















