<?php
defined('ABSPATH') or die();

define( 'BASEMENT_MODALS_TEXTDOMAIN', 'basement_modals' );



class Basement_Modals_Plugin extends Basement_Plugin {

	private static $instance = null;

	/**
	 * Contains an array of script handles registered by BasementModals.
	 * @var array
	 */
	private static $scripts = array();


	/**
	 * Contains an array of script handles registered by BasementModals.
	 * @var array
	 */
	private static $styles = array();

	protected $post_types = array(
		'modals'
	);
	


	public function __construct() {
		parent::__construct();

		if ( !defined( 'THEME_VERSION' ) ) {
			define( 'THEME_VERSION', '1.0' );
		}
		add_action( 'admin_enqueue_scripts', array ( &$this, 'load_back_scripts' ));

		add_action( 'wp_enqueue_scripts', array( &$this, 'load_front_scripts' ) );

		add_action('wp_footer',array(&$this,'modal_window_init'));

		add_action('wp_head',array(&$this,'style_modal_window_init'));
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Modals_Plugin();
		}
		return self::$instance;
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = THEME_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}


	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = THEME_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}


	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = THEME_VERSION, $media = 'all' ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = THEME_VERSION, $media = 'all' ) {
		if ( ! in_array( $handle, self::$styles ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Register js/css in backend
	 */
	public function load_back_scripts($type_page) {
		if('post.php' !== $type_page && 'post-new.php' !== $type_page)
			return;

		global $post;

		$post_type = isset($post->post_type) ? $post->post_type : '';

		if( 'modals' === $post_type && $this->is_edit_page()  ) {
			// Global backend BasementModals styles
			self::enqueue_style( BASEMENT_MODALS_TEXTDOMAIN . '_style', PATH_MODALS . 'assets/css/back-shortcodes.min.css' );
		}

	}


	/**
	 * Detect current page
	 *
	 * @param null $new_edit
	 * @return bool
	 */
	private function is_edit_page($new_edit = null){
		global $pagenow;
		//make sure we are on the backend
		if (!is_admin()) return false;

		if($new_edit == "edit")
			return in_array( $pagenow, array( 'post.php',  ) );
		elseif($new_edit == "new") //check for new post page
			return in_array( $pagenow, array( 'post-new.php' ) );
		else //check for either new or edit
			return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	}


	/**
	 * Register js/css in front
	 */
	public function load_front_scripts() {

		// Global frontend BasementModals scripts
		self::enqueue_script( BASEMENT_MODALS_TEXTDOMAIN . '_script', PATH_MODALS . 'assets/js/front-production.min.js', array( 'jquery', 'basement_plugins' ) );

		// Global frontend BasementModals styles
		self::enqueue_style( BASEMENT_MODALS_TEXTDOMAIN . '_style', PATH_MODALS . 'assets/css/front-production.min.css' );

		wp_localize_script( BASEMENT_MODALS_TEXTDOMAIN . '_script', 'basement_modals_ajax', array(
			'url' => admin_url( 'admin-ajax.php' )
		) );

	}


	/**
	 * Load necessary views
	 */
	public function load_views( $params , $files ) {

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

	public function style_modal_window_init() {
		echo '<style id="basement-modal-style"></style>';
	}


	/**
	 * Init div block for Modal Windows
	 */
	public function modal_window_init() {
		?>

		<div id="basement-modal-preloader">
            <div class="loader"><div class="triangle-skew-spin"><div></div></div></div>
		</div>

		<div id="basement-modal-window">

			<div class="basement-modal-header">
				<a href="#" class="basement-modal-close" title="<?php echo __('Close', BASEMENT_MODALS_TEXTDOMAIN); ?>"><i class="ais-close"></i></a>
			</div>

			<div class="basement-modal-body">
				<div class="basement-modal-content" >
					<div class="maincontent modal-maincontent"></div>
				</div>
			</div>

		</div>

		<?php
	}


	/**
	 * Init textdomain for translations
	 *
	 * @return string
	 */
	protected function textdomain() {
		return BASEMENT_MODALS_TEXTDOMAIN;
	}


	/**
	 * Load textdomain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( $this->textdomain(), false, '/' . plugin_basename( $this->dir() ) . '/translations' );
	}


}

Basement_Modals_Plugin::init();
