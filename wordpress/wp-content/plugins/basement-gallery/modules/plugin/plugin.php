<?php
defined('ABSPATH') or die();

define( 'BASEMENT_GALLERY_TEXTDOMAIN', 'basement_gallery' );


function basement_vc_gallery_notice() { ?>
	<div class="error">
		<p><?php echo __('For correct work the Basement Gallery plugin, please activate/install the WPBakery Visual Composer plugin.', BASEMENT_GALLERY_TEXTDOMAIN) ?></p>
	</div>
<?php }

if ( ! defined( 'WPB_VC_VERSION' ) ) {
	add_action( 'admin_notices', 'basement_vc_gallery_notice' );
}

if ( !class_exists( 'Basement_Gallery_Plugin' ) ) {
	class Basement_Gallery_Plugin extends Basement_Plugin {

		private static $instance = null;

		/**
		 * Contains an array of script handles registered by BasementGallery.
		 * @var array
		 */
		private static $scripts = array();


		/**
		 * Contains an array of script handles registered by BasementGallery.
		 * @var array
		 */
		private static $styles = array();

		protected $post_types = array(
			'grid',
			'grid-settings',
			'tile',
			'tile-settings'
		);


		public function __construct() {
			parent::__construct();

			if ( ! defined( 'THEME_VERSION' ) ) {
				define( 'THEME_VERSION', '1.0' );
			}

			add_action( 'admin_enqueue_scripts', array( &$this, 'load_back_scripts' ) );

			add_action( 'wp_enqueue_scripts', array( &$this, 'load_front_scripts' ) );

		}

		public static function init() {
			self::instance();
		}

		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new Basement_Gallery_Plugin();
			}

			return self::$instance;
		}

		/**
		 * Register a script for use.
		 *
		 * @uses   wp_register_script()
		 * @access private
		 *
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
		 *
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
		 *
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
		 *
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
		public function load_back_scripts() {
			global $post;

			if ( ! empty( $post->post_type ) ) {
				if ( ( $post->post_type === 'grid' || $post->post_type === 'tile' ) && $this->is_edit_page() ) {

					// Global backend BasementGallery scripts
					self::enqueue_script( BASEMENT_GALLERY_TEXTDOMAIN . '-script', PATCH_GALLERY . 'assets/js/back-production.min.js', array( 'jquery' ) );

					// Global frontend BasementGallery styles
					self::enqueue_style( BASEMENT_GALLERY_TEXTDOMAIN . '-style', PATCH_GALLERY . 'assets/css/back-production.min.css' );

				} else if ( ( $post->post_type !== 'grid' && $post->post_type !== 'tile' && $post->post_type !== 'carousel' && $post->post_type !== 'carousel_slide' && $post->post_type !== 'portfolio' ) && $this->is_edit_page() ) {

					// Global backend BasementGallery shortcodes styles
					self::enqueue_style( BASEMENT_GALLERY_TEXTDOMAIN . '-shortcode-style', PATCH_GALLERY . 'assets/css/back-shortcodes.min.css' );

				}
			}

		}


		/**
		 * Detect current page
		 *
		 * @param null $new_edit
		 *
		 * @return bool
		 */
		private function is_edit_page( $new_edit = null ) {
			global $pagenow;
			//make sure we are on the backend
			if ( ! is_admin() ) {
				return false;
			}

			if ( $new_edit == "edit" ) {
				return in_array( $pagenow, array( 'post.php', ) );
			} elseif ( $new_edit == "new" ) //check for new post page
			{
				return in_array( $pagenow, array( 'post-new.php' ) );
			} else //check for either new or edit
			{
				return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
			}
		}


		/**
		 * Register js/css in front
		 */
		public function load_front_scripts() {

			// Global frontend BasementGallery scripts
			self::enqueue_script( BASEMENT_GALLERY_TEXTDOMAIN . '-script', PATCH_GALLERY . 'assets/js/front-production.min.js', array(
				'jquery',
				'basement_plugins'
			) );

			// Global frontend BasementGallery styles
			self::enqueue_style( BASEMENT_GALLERY_TEXTDOMAIN . '-style', PATCH_GALLERY . 'assets/css/front-production.min.css' );


			wp_localize_script( BASEMENT_GALLERY_TEXTDOMAIN . '-script', 'basement_gallery_ajax', array(
				'url' => admin_url( 'admin-ajax.php' )
			) );

		}



		/**
		 * Load necessary views
		 */
		public function load_views( $params, $files ) {

			$views = array();
			if ( ! empty( $files ) ) {
				foreach ( $files as $view ) {
					$views[] = realpath( $this->dir() . '/modules/views/' . $view . '.php' );
				}
			}

			if ( ! is_array( $views ) || empty( $views ) ) {
				return;
			}

			foreach ( $views as $view ) {
				if ( file_exists( $view ) ) {
					require_once $view;
				}
			}
		}


		/**
		 * Return current post type
		 *
		 * @return string
		 */
		public function get_post_type() {
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

			$post = get_post( $post_id );

			return empty($post) ? $post_list : $post->post_type;
		}


		/**
		 * TextDomain for Basement Gallery
		 *
		 * @return string
		 */
		protected function textdomain() {
			return BASEMENT_GALLERY_TEXTDOMAIN;
		}


		/**
		 * Load textdomain for Basement Gallery
		 */
		public function load_textdomain() {
			load_plugin_textdomain( $this->textdomain(), false, '/' . plugin_basename( $this->dir() ) . '/translations' );
		}


		/**
		 * Integrate with VC
		 */
		public function integrate_with_visual_composer() {
			$disabled_posts = array( 'carousel_slide', 'carousel', 'grid', 'tile', 'portfolio' );
			$post_type = $this->get_post_type();
			if ( ! in_array( $post_type, $disabled_posts ) ) {
				$path =  realpath( $this->dir() . '/modules/shortcodes/vc-gallery.php' );
				if(file_exists($path)) {
					require_once( $path );
				}
			}
		}

	}

	Basement_Gallery_Plugin::init();
}