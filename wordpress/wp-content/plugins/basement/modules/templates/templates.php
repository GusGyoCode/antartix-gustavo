<?php
defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'Basement_Templates' ) ) {
	class Basement_Templates {

		private static $instance = null;

		// Name for Page Title options
		private $options = array();

		// Block for Page Title options (options & meta)
		private $options_blocks = array();


		public function __construct() {
			add_action( 'admin_head', array( &$this, 'basement_list_theme_templates' ) );

			if ( is_admin() ) {

				$this->set_options_blocks();

				$this->set_options_names();

				add_action( 'admin_init', array( &$this, 'register_theme_settings' ) );

				add_filter(
					BASEMENT_TEXTDOMAIN . '_theme_settings_config_filter',
					array( &$this, 'theme_settings_config_filter' )
				);
			}
		}

		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new Basement_Templates();
			}

			return self::$instance;
		}



		/**
		 * Init page title params
		 *
		 * @param array $config
		 *
		 * @return array
		 */
		public function theme_settings_config_filter( $config = array() ) {
			$settings_config = array(
				'templates' => array(
					'title'  => __( 'Grid Templates', BASEMENT_TEXTDOMAIN ),
					'blocks' => array()
				)
			);

			foreach ( $this->options_blocks as $key => $value ) {
				$value['input'] = call_user_func( array( &$this, $value['input'] ) );
				$settings_config['templates']['blocks'][] = $value;
			}

			return array_slice( $config, 0, 2, true ) + $settings_config + array_slice( $config, 2, count( $config ) - 2, true );
		}

		/**
		 * List of option name settings
		 *
		 * @return array
		 */
		private function options_names() {
			return array(
				'blog_archive'  => BASEMENT_TEXTDOMAIN . '_blog_archive'
			);
		}


		/**
		 * Set name for block in Templates settings
		 */
		private function set_options_names() {
			$this->options = $this->options_names();
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
					'title'       => __( 'Meta Pages', BASEMENT_TEXTDOMAIN ),
					'description' => __( 'Sets the grid template for meta pages (archives, tags etc.).', BASEMENT_TEXTDOMAIN ),
					'input'       => 'blog_archive'
				)
			);
		}


		/**
		 * Set block for template settings
		 */
		private function set_options_blocks() {
			$this->options_blocks = $this->options_blocks();
		}


		/**
		 * Register options for templates
		 */
		public function register_theme_settings() {
			foreach ( $this->options as $key => $value ) {
				if (is_array($value) or ($value instanceof Traversable)) {
					foreach ($value as $inner_key => $inner_value) {
						register_setting( 'basement_theme_options', $inner_value );
					}
				} else {
					register_setting( 'basement_theme_options', $value );
				}

			}
		}




		/**
		 * Alternate title
		 *
		 * @param string $type
		 *
		 * @return DOMNode|string
		 */
		public function blog_archive( $type = '' ) {


			$template_list = array();

			/*
			$templates = get_page_templates( null, 'page' );


			ksort( $templates );
			foreach ( array_keys( $templates ) as $template ) {
				$template_list[  $templates[ $template ] ] = esc_html( $template);
			}*/


			$template_list = apply_filters( 'basement_templates_blog_archive', $template_list );

			$dom = new DOMDocument( '1.0', 'UTF-8' );
			$container = $dom->appendChild( $dom->createElement('div'));

			if(!empty($template_list)) {

				$params = array( 'values' => $template_list );

				$option = $this->options['blog_archive'];


				$value = get_option( $option, apply_filters('basement_default_grid_template','is-creative') );

				if(empty($value)) {
					reset($template_list);
					$value = key($template_list);
				}


				$params['current_value'] = $value;

				$params['id']   = $option;
				$params['name'] = $option;

				$select = new Basement_Form_Input_Select( $params );

				$container->appendChild($dom->importNode( $select->create(), true ));
			}

			return $container;
		}


		/**
		 * Main function for load params for templates
		 */
		public function basement_list_theme_templates() {
			global $pagenow, $typenow;

			if(!$this->is_edit_page())
				return;

			$templates = apply_filters('basement_templates_list', get_page_templates(), $pagenow, $typenow );
			$theme_dir = get_template_directory() .'/';

			$settings = array();

			foreach ($templates as $title => $file) {
				$file_path = $theme_dir . $file;

				if(file_exists($file_path) && is_file($file_path)) {
					$template_data = implode( '',  apply_filters('basement_template_data', file( $file_path, FILE_IGNORE_NEW_LINES ), $title, $file, $file_path ) );

					if($template_data && preg_match( '/\/\*###(.*?)###\*\//mi', $template_data, $data )) {

						$string = isset($data['1']) && !empty($data['1']) ?  $data['1'] : false;

						if($this->is_json($string) && $this->is_json_valid($string)) {
							$string = apply_filters('basement_template_json', $string, $title, $file, $file_path);
							$string = preg_replace(array('/^\[/', '/\]$/'), '', $string);
							$settings[] = "'$file' : $string";
						}
					}
				}
			}

			if(!empty($settings)) {
				$settings = apply_filters('basement_templates_settings', $settings, $templates);

				do_action('basement_before_templates_script'); ?>

                <!-- BASEMENT TEMPLATES SETTINGS -->
                <script>
					<?php do_action('basement_before_templates_script_settings'); ?>

					var basement_template_params = {<?php echo implode(',', apply_filters('basement_templates_js_settings', $settings, $templates )); ?>};

					<?php do_action('basement_after_templates_script_settings'); ?>
                </script>
                <!-- END BASEMENT TEMPLATES SETTINGS -->

				<?php
				do_action('basement_after_templates_script');
			}

		}


		/**
		 * Convert JSON object to array
		 *
		 * @param $data
		 *
		 * @return array
		 */
		private function object_to_array($data) {
			if(is_array($data) || is_object($data)) {
				$result = array();

				foreach($data as $key => $value) {
					$result[$key] = $this->object_to_array($value);
				}

				return $result;
			}

			return $data;
		}


		/**
		 * Check valid JSON
		 *
		 * @param $string
		 *
		 * @return array|mixed|object
		 */
		private function is_json_valid($string) {
			// decode the JSON data
			$result = json_decode($string);

			// switch and check possible JSON errors
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					$error = ''; // JSON is valid // No error has occurred
					break;
				case JSON_ERROR_DEPTH:
					$error = 'The maximum stack depth has been exceeded.';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$error = 'Invalid or malformed JSON.';
					break;
				case JSON_ERROR_CTRL_CHAR:
					$error = 'Control character error, possibly incorrectly encoded.';
					break;
				case JSON_ERROR_SYNTAX:
					$error = 'Syntax error, malformed JSON.';
					break;
				// PHP >= 5.3.3
				case JSON_ERROR_UTF8:
					$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
					break;
				// PHP >= 5.5.0
				case JSON_ERROR_RECURSION:
					$error = 'One or more recursive references in the value to be encoded.';
					break;
				// PHP >= 5.5.0
				case JSON_ERROR_INF_OR_NAN:
					$error = 'One or more NAN or INF values in the value to be encoded.';
					break;
				case JSON_ERROR_UNSUPPORTED_TYPE:
					$error = 'A value of a type that cannot be encoded was given.';
					break;
				default:
					$error = 'Unknown JSON error occured.';
					break;
			}

			if ($error !== '') {
				// throw the Exception or exit // or whatever :)
				return false;
			}

			// everything is OK
			return true;
		}



		/**
		 * Check if string is JSON
		 *
		 * @param $json_string
		 *
		 * @return bool
		 */
		private function is_json($json_string) {
			return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/',
				preg_replace('/"(\\.|[^"\\\\])*"/', '', $json_string));
		}


		/**
		 * Detect current edit page
		 *
		 * @param null $new_edit
		 *
		 * @return bool
		 */
		private function is_edit_page( $new_edit = null ) {
			global $pagenow, $typenow;
			//make sure we are on the backend
			if ( ! is_admin() || $typenow !== 'page' ) {
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

	}
}