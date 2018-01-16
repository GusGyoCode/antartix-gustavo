<?php
/**
 * Plugin Name: Basement Framework
 * Plugin URI: http://aisconverse.com
 * Description: A solid basement for Basement themes
 * Version: 1.0.1
 * Author: Aisconverse team
 * Author URI: http://aisconverse.com
 * License: GPL2
 */

defined('ABSPATH') or die();

if ( !defined( 'THEME_TEXTDOMAIN' ) ) {
	define( 'THEME_TEXTDOMAIN', wp_get_theme()->get_template() );
}

function basement_php_version_notice() { ?>
	<div class="error">
		<p><?php echo __('Basement Framework needs at least PHP version 5.6.0, your version: ' . PHP_VERSION . "\n", BASEMENT_TEXTDOMAIN); ?></p>
	</div>
<?php }

if ( version_compare( PHP_VERSION, '5.6.0' ) < 0 ) {
	add_action( 'admin_notices', 'basement_php_version_notice' );
}

if ( !class_exists( 'Basement' ) ) {

	define( 'BASEMENT_TEXTDOMAIN', 'basement_framework' );

	class Basement {
		
		private static $url = null;
		private static $instance = null;

		public static function init() {
			add_theme_support( 'basement' );
			return self::instance();
		}

		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new Basement();
			}
			return self::$instance;
		}

		/**
		 * Init class instance
		 */
		public function __construct() {
			Basement_Autoload::init();
			Basement_Admin::init();
			Basement_Theme::init();
			Basement_Hook::init();
			Basement_Typography::init();
			Basement_Templates::init();
			Basement_Revoslider::init();
			Basement_Preloader::init();
			Basement_Header::init();
			Basement_Pagetitle::init();
			Basement_Sidebar::init();
			Basement_Footer::init();
			Basement_Widgets::init();
			Basement_Blog::init();
			Basement_Settings::init();
			Basement_Ecommerce_Woocommerce::init();
			#Basement_Vc::init();
			$this->load_textdomain();
		}

		public function load_textdomain() {
			load_theme_textdomain( BASEMENT_TEXTDOMAIN, dirname( __FILE__ ) . '/translations' );
			load_plugin_textdomain( BASEMENT_TEXTDOMAIN, false, '/' . plugin_basename( dirname( __FILE__ ) ) . '/translations' );
		}


		public static function directory() {
			return dirname( __FILE__ );
		}

		public static function url() {
			if ( null === self::$url ) {
				self::$url = Basement_Url::of_file( plugin_dir_url( __FILE__ ) );
			}
			return self::$url;
		}

		public static function is_blog () {
			global $post;
			$posttype = get_post_type( $post );
			return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post') ) ? true : false ;
		}

		/**
		 * Check if POST for plugin exists
		 *
		 * @return bool
		 */
		private function is_post() {
			return count( $_POST ) && 
					isset( $_POST[ BASEMENT_TEXTDOMAIN ] ) && 
					is_array( $_POST[ BASEMENT_TEXTDOMAIN ] ) && 
					count( $_POST[ BASEMENT_TEXTDOMAIN ] ) ? $_POST[ BASEMENT_TEXTDOMAIN ] : array() ;
		}

	}

	add_action( 'after_setup_theme', 'basement_init' ); 
}



function basement_init() {
	require 'modules/autoload/autoload.php';
	Basement::init();
	do_action( 'basement_loaded' );
	do_action( 'basement_plugins_loaded' );
}
