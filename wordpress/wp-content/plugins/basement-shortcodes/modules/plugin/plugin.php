<?php
defined('ABSPATH') or die();

define( 'BASEMENT_SHORTCODES_TEXTDOMAIN', 'basement_shortcodes' );

class Basement_Shortcodes extends Basement_Plugin {

	private $shortcodes = array();
	private static $instance = null;

	public $panel = null;
	public static  $basement_shortcodes = array();
	private $panel_ignore = array('carousel','grid','tile','portfolio');


	public function init() { //static
		if ( !in_array( 'basement/basement.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) &&
			!current_theme_supports( 'basement' ) ) {
			add_action( 'admin_notices', array( &$this, 'no_basement_notice' ) );
			return;
		}
		add_action( 'basement_plugins_loaded', array( self::instance(), 'init_shortcodes' ) );
		add_action( 'basement_plugins_loaded', array( self::instance(), 'init_panel' ) );
		add_action( 'basement_plugins_loaded', array( self::instance(), 'enqueue_scripts' ) );
		add_action( 'vc_before_init', array( self::instance(), 'basement_init_vc_shortcodes' ) );
		add_action( 'wp_enqueue_scripts', array( $this,'basement_shortcodes_scripts' ), 99 );
	}



	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Shortcodes();
		}
		return self::$instance;
	}

	protected function textdomain() {
		return BASEMENT_SHORTCODES_TEXTDOMAIN;
	}

	public function no_basement_notice() { ?>
		<div class="error">
			<p><?php _e( 'Your theme doesn\'t support Basement Framework. Basement Shortcodes plugin will not be available.', BASEMENT_SHORTCODES_TEXTDOMAIN ); ?></p>
		</div>
	<?php }


	public function basement_shortcodes_scripts() {
		wp_register_script( 'basement-shortcodes-javascript', BASEMENT_SHORTCODES_URL . 'assets/javascript/basement-shortcodes.min.js', array( 'jquery', 'basement_plugins' ), '1.0', true );
		wp_enqueue_script('basement-shortcodes-javascript');


		// YandexShare
		wp_register_script( 'yashare', '//yastatic.net/share2/share.js', array(), false, true );
		wp_enqueue_script( 'yashare' );

		// Google Map API
		$gmap_key = get_option( 'conico_api_key' );
		if ( ! empty( $gmap_key ) ) {
			wp_register_script( 'basement_googlemap', '//maps.googleapis.com/maps/api/js?key=' . $gmap_key . '&callback=basementInitMap', array('jquery','basement-shortcodes-javascript'), '3.27', true );
			wp_enqueue_script( 'basement_googlemap' );
		}



		// Script Control
		add_filter( 'script_loader_tag', function ( $tag, $handle ) {

			switch ($handle) {
				case 'basement_googlemap' :
					return str_replace( ' src', ' defer async src', $tag );
					break;
				default :
					return $tag;
			}

		}, 10, 2 );
	}


	public function init_shortcodes() {
		require_once dirname( __FILE__ ) .'/../shortcode/shortcode.php';

		$shortcodes_configs = apply_filters(
			'basement_shortcodes_config',
			array_merge( require dirname( __FILE__ ) .'/../../configs/shortcodes.php', Basement_Config::section( 'shortcodes' ) )
		);

		foreach ( $shortcodes_configs as $shortcode_tag => $shortcode_config) {
			if ( 'dummy' == $shortcode_tag ) {
				continue;
			}
			if ( !empty( $shortcode_config[ 'config' ] ) && array_key_exists( $shortcode_config[ 'config' ], $shortcodes_configs ) ) {
				$shortcode_config = wp_parse_args( $shortcode_config, $shortcodes_configs[ $shortcode_config[ 'config' ] ] );
			}
			if ( empty( $shortcode_config[ 'class' ] ) ) {
				continue;
			}
			if ( empty( $shortcode_config[ 'name' ] ) ) {
				$shortcode_config[ 'name' ] = $shortcode_tag;
			}
			if ( !empty( $shortcode_config[ 'path' ] ) && file_exists( $shortcode_config[ 'path' ] ) ) {
				require_once $shortcode_config[ 'path' ];
			}
			if ( class_exists( $shortcode_config[ 'class' ] ) ) {
				$this->shortcodes[ $shortcode_tag ] = new $shortcode_config[ 'class' ]( $shortcode_config );
				self::$basement_shortcodes[ $shortcode_tag ] = array('name' => $shortcode_config[ 'class' ], 'class'=> new $shortcode_config[ 'class' ]( $shortcode_config ));
			}
		}

		return $this;
	}


	public function init_panel() {
		if ( is_admin() && $this->count() ) {

			$post_id = '';
			if(isset($_GET['post'])) {
				$post_id = $_GET['post'];
			} elseif (isset($_POST['post'])) {
				$post_id = $_POST['post'];
			}

			$post_list = '';
			if(isset($_GET['post_type'])) {
				$post_list = $_GET['post_type'];
			} elseif (isset($_POST['post_type'])) {
				$post_list = $_POST['post_type'];
			}


			$page = '';
			if(isset($_GET['page'])) {
				$page = $_GET['page'];
			} elseif (isset($_POST['page'])) {
				$page = $_POST['page'];
			}
			$php = basename($_SERVER['PHP_SELF']);
			$post = get_post( $post_id );

			$post_current = empty($post) ? $post_list : $post->post_type;

		
			if(!in_array($post_current, $this->panel_ignore ) && $page !== 'revslider' && $page !== 'revslider_navigation' && $php !== 'widgets.php' && $page !== 'basement-placeholder.php') {

				require_once dirname( __FILE__ ) . '/../panel/panel.php';
				require_once dirname( __FILE__ ) . '/../group/group.php';

				Shortcode_Panel::init();
			}

			if($page !== 'revslider' && $page !== 'revslider_navigation' && $page !== 'basement-placeholder.php') {
				Basement_Asset::add_admin_style(
					BASEMENT_SHORTCODES_TEXTDOMAIN . '_css',
					$this->zurl() . '/assets/css/production.min.css'
				);
				Basement_Asset::add_admin_footer_script(
					BASEMENT_SHORTCODES_TEXTDOMAIN . '_js',
					$this->zurl() . '/assets/javascript/production.min.js',
					array( BASEMENT_TEXTDOMAIN . '_js' )
				);
			}

		}
	}

	public function enqueue_scripts() {

		$page = '';
		if(isset($_GET['page'])) {
			$page = $_GET['page'];
		} elseif (isset($_POST['page'])) {
			$page = $_POST['page'];
		}

		if($page !== 'revslider' && $page !== 'revslider_navigation') {

			Basement_Asset::add_admin_footer_script(
				BASEMENT_SHORTCODES_TEXTDOMAIN . '_js',
				Basement_Shortcodes::url() . '/assets/javascript/production.min.js',
				array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-timepicker' )
			);

			Basement_Asset::add_admin_style(
				BASEMENT_SHORTCODES_TEXTDOMAIN . '_css',
				Basement_Shortcodes::url() . '/assets/css/production.min.css'
			);
		}
	}

	public function get_shortcode( $name ) {
		if ( isset( $this->shortcodes[ $name ] ) ) {
			return $this->shortcodes[ $name ];
		}
		return null;
	}


	public static function addAllMappedBasementShortcodes() {
		foreach (self::$basement_shortcodes as $tag => $settings) {
			add_shortcode( $tag, array( $settings['name'], 'render' ) );
		}
	}


	public function collection() {
		return $this->shortcodes;
	}

	public function count() {
		return count( $this->shortcodes );
	}

	public function basement_init_vc_shortcodes() {
		$vc_shortcodes = array(
			'vc_social_sharing' => 'vc_social_sharing.php',
			'vc_animated_icon' => 'vc_animated_icon.php',
			'vc_countdown' => 'vc_countdown.php',
			'vc_counter' => 'vc_counter.php',
			'vc_simple_separator' => 'vc_simple_separator.php',
			'vc_vertical_title' => 'vc_vertical_title.php'
		);
		foreach ($vc_shortcodes as $folder => $file) {
			$path = $this->dir() . '/vc-shortcodes/' . $folder . '/' . $file;
			$path = realpath($path);
			if(file_exists($path)) {
				require_once( $path );
			}
		}

	}

}

$p = new Basement_Shortcodes();
$p->init();