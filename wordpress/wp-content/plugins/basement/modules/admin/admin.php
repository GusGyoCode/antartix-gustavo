<?php
defined('ABSPATH') or die();

class Basement_Admin {

	private static $instance = null;

	/**
	 * Init class instance
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		Basement_Admin_Colorscheme::init();
	}

	public static function init() {
		if ( !is_admin() ) {
			return;
		}
		if ( null === self::$instance ) {
			self::$instance = new Basement_Admin();
		}
		return self::$instance;
	}

	public function enqueue_scripts() {

		Basement_Asset::use_admin_media();

		$page = '';
		if(isset($_GET['page'])) {
			$page = $_GET['page'];
		} elseif (isset($_POST['page'])) {
			$page = $_POST['page'];
		}

		if($page !== 'revslider' && $page !== 'revslider_navigation') {

			# load jQuery-ui slider
			wp_enqueue_script( 'jquery-ui-slider' );

			#load jQuery-ui datepicker
			wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_enqueue_script( 'jquery-ui-sortable' );

			if ( defined( 'BASEMENT_SHORTCODES_URL' ) ) {
				# load jQuery UI timepicker addon
				wp_enqueue_script( 'jquery-ui-timepicker', BASEMENT_SHORTCODES_URL . 'assets/javascript/vendor/jquery-ui-timepicker.js', array(
					'jquery',
					'jquery-ui-slider',
					'jquery-ui-datepicker'
				), '1.4.3' );
			}

			wp_register_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
			wp_enqueue_style( 'jquery-ui' );

			Basement_Asset::add_admin_style(
				BASEMENT_TEXTDOMAIN . '_css',
				Basement::url() . '/assets/css/production.min.css'
			);

			Basement_Asset::add_admin_footer_script(
				BASEMENT_TEXTDOMAIN . '_js',
				Basement::url() . '/assets/javascript/production.min.js',
				array( 'jquery' )
			);


			Basement_Asset::add_admin_footer_script(
				BASEMENT_TEXTDOMAIN . '_js-admin',
				Basement::url() . '/assets/javascript/admin.min.js',
				array( 'jquery' )
			);
		}

	}

	
	public function page() {
		return '';
	}

	/**
	 * Renders admin page
	 */
	public function admin_page() {
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', BASEMENT_TEXTDOMAIN ) );
		}
		if ( $_GET['page'] != 'basement-framework') {
			$view_name = str_replace( 'basement-framework-', '', $_GET['page'] );
		} else {
			$view_name = 'basement';
		}
		$view_path = 'views/' . $view_name . '.php';
		if ( !file_exists( dirname( __FILE__ ) . '/' .$view_path ) ) {
			wp_die( __( 'Sorry, the page is not found.', BASEMENT_TEXTDOMAIN ) );
		}
		require $view_path;
	}

}



















