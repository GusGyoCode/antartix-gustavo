<?php
defined( 'ABSPATH' ) or die();


class Basement_Typography {

	private static $instance = null;

	// Name for Google Fonts options
	private $options = array();

	public $fonts_list = '[{"font_family":"Open+Sans","font_styles":"300,300i,400i,400,600i,600,700,700i,800i,800","font_types":"300 light regular:300:normal,300 light italic:300:italic,400 italic:400:italic,400 regular:400:normal,600 bold italic:600:italic,600 bold regular:600:normal,700 bold regular:700:normal,700 bold italic:700:italic,800 bold italic:800:italic,800 bold regular:800:normal"},{"font_family":"Playfair+Display","font_styles":"400,400i,700i,700,900i,900","font_types":"400 regular:400:normal,400 italic:400:italic,700 bold italic:700:italic,700 bold regular:700:normal,900 bold italic:900:italic,900 bold regular:900:normal"},{"font_family":"Chivo","font_styles":"300,300i,400,400i,700,700i,900,900i","font_types":"300 light regular:300:normal,300 light italic:300:italic,400 regular:400:normal,400 italic:400:italic,500 bold regular:500:normal,500 bold italic:500:italic,700 bold regular:700:normal,700 bold italic:700:italic,900 bold regular:900:normal,900 bold italic:900:italic"},{"font_family":"Roboto","font_styles":"100,100i,300i,300,400i,400,500i,500,700i,700,900i,900","font_types":"100 light regular:100:normal,100 light italic:100:italic,300 light italic:300:italic,300 light regular:300:normal,400 italic:400:italic,400 regular:400:normal,500 bold italic:500:italic,500 bold regular:500:normal,700 bold italic:700:italic,700 bold regular:700:normal,900 bold italic:900:italic,900 bold regular:900:normal"},{"font_family":"Lato","font_styles":"100,100i,300,300i,400,400i,700,700i,900,900i","font_types":"100 light regular:100:normal,100 light italic:100:italic,300 light regular:300:normal,300 light italic:300:italic,400 regular:400:normal,400 italic:400:italic,700 bold regular:700:normal,700 bold italic:700:italic,900 bold regular:900:normal,900 bold italic:900:italic"},{"font_family":"Lora","font_styles":"400,400i,700i,700","font_types":"400 regular:400:normal,400 italic:400:italic,700 bold italic:700:italic,700 bold regular:700:normal"},{"font_family":"Roboto+Slab","font_styles":"100,300,400,700","font_types":"100 light regular:100:normal,300 light regular:300:normal,400 regular:400:normal,700 bold regular:700:normal"},{"font_family":"Merriweather","font_styles":"300i,300,400i,400,700i,700,900i,900","font_types":"300 light italic:300:italic,300 light regular:300:normal,400 italic:400:italic,400 regular:400:normal,700 bold italic:700:italic,700 bold regular:700:normal,900 bold italic:900:italic,900 bold regular:900:normal"}]';


	// Block for Google Fonts options
	private $options_blocks = array();

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( &$this, 'add_googlefonts_to_theme' ) );
		add_filter( 'mce_css', array( &$this, 'add_googlefonts_to_editor' ) );
		add_action( 'admin_init', array( &$this, 'add_google_fonts_js' ) );
		add_action( 'admin_head', array( &$this, 'my_add_mce_button' ) );

		$this->set_options_blocks();

		$this->set_options_names();

		add_action( 'admin_init', array( &$this, 'register_theme_settings' ) );

		add_filter(
			BASEMENT_TEXTDOMAIN . '_theme_settings_config_filter',
			array( &$this, 'theme_settings_config_filter' )
		);

		add_action('wp_ajax_add_new_google_font', array( &$this, 'ajax_add_new_google_font' ));
		add_action('wp_ajax_remove_google_font', array( &$this, 'ajax_remove_google_font' ));

		if($this->vc_enabled()) {
			add_filter('vc_google_fonts_get_fonts_filter',array( &$this,'google_fonts_vc_custom'));
		}

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
			self::$instance = new Basement_Typography();
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
				'title'       => __( 'Google Fonts list', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Select fonts that are available in the TinyMCE editor of your theme.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'google_fonts_list'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'New Google Font', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Adds a new <a href="https://fonts.google.com/" target="_blank">Google Font</a> in the editor.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'google_fonts_add'
			)
		);
	}

	/**
	 * Adds new Google Fonts
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function google_fonts_add($type = '') {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$input = new Basement_Form_Input( array(
			'label_text' => __( 'URL', BASEMENT_TEXTDOMAIN ),
			'id' => 'basement_new_google_font',
			'attributes' => array(
				'placeholder' => __('e.g.: https://fonts.googleapis.com/css?family=Roboto:400,400i,500', BASEMENT_TEXTDOMAIN)
			)
		) );

		$container->appendChild( $dom->importNode( $input->create(), true ) );


		$btn = $container->appendChild( $dom->createElement( 'button', __('Add', BASEMENT_TEXTDOMAIN) ) );
		$btn->setAttribute('class','button button-primary');
		$btn->setAttribute('type','button');
		$btn->setAttribute('id','basement_add_new_google_font');

		$spinner = $container->appendChild( $dom->createElement( 'span', '') );
		$spinner->setAttribute('class','spinner');
		$spinner->setAttribute('style','float:none;');


		$response = $container->appendChild( $dom->createElement( 'div' ) );
		$response->setAttribute('id','basement_new_google_font_response');
		$response->setAttribute('style','color:red;margin-top:5px;');

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}





	/**
	 * List of Google Fonts
	 *
	 * @param string $type
	 *
	 * @return DOMNode|string
	 */
	public function google_fonts_list( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$check_fonts_name = $this->options['google_include'];
		$all_fonts_name = $this->options['google_fonts'];

		$fonts_list = trim(preg_replace('/\s+/', ' ', $this->fonts_list));

		$all_google_fonts = get_option($all_fonts_name, $fonts_list);

		$textarea_params = array(
			'value' => !empty($all_google_fonts) ? $all_google_fonts : $fonts_list,
			'name' => $all_fonts_name,
			'attributes' => array(
				'style' => 'display:none;'
			)
		);

		$all_google_fonts = json_decode($all_google_fonts);
		$values = array();

		if(!empty($all_google_fonts)) {
			foreach ( $all_google_fonts as $key => $value ) {
				$value_font_family = isset($value->font_family) ? $value->font_family : '';

				if(!empty($value_font_family)) {
					$values[ $value_font_family ] = str_replace('+',' ', $value_font_family);
				}
			}
		}

		$params = array(
			'values' => $values
		);
		$current_param = array( '' );

		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_google_fonts' . substr( $check_fonts_name, 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $check_fonts_name;
			$option_value            = get_option( $option );
			$params['current_value'] = empty( $option_value ) ? $current_param : $option_value;
		}

		$params['id']         = $option;
		$params['name']       = $option;

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );
		$container->setAttribute( 'id', 'basement_list_google_fonts' );

		$fonts_checkboxes = new Basement_Form_Input_Checkbox_Group( $params );

		$container->appendChild( $dom->importNode( $fonts_checkboxes->create(), true ) );

		$fonts_textarea = new Basement_Form_Input_Textarea( $textarea_params );

		$container->appendChild( $dom->importNode( $fonts_textarea->create(), true ) );


		if ( $type === 'metabox' || $type === 'options' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * List of option name settings
	 *
	 * @return array
	 */
	private function options_names() {
		return array(
			'google_include' => BASEMENT_TEXTDOMAIN . '_google_include',
			'google_fonts' => BASEMENT_TEXTDOMAIN . '_google_fonts'
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
			'google_fonts' => array(
				'title'  => __( 'Google Fonts', BASEMENT_TEXTDOMAIN ),
				'blocks' => array()
			)
		);

		foreach ( $this->options_blocks as $key => $value ) {
			$value['input'] = call_user_func( array( &$this, $value['input'] ) );
			$settings_config['google_fonts']['blocks'][] = $value;
		}

		return array_merge( $settings_config, $config );
	}


	/**
	 * Get All checked Google Fonts
	 *
	 * @return array
	 */
	public function get_google_fonts() {
		$check_fonts_name = $this->options['google_include'];
		$all_fonts_name = $this->options['google_fonts'];
		$fonts_list = trim(preg_replace('/\s+/', ' ', $this->fonts_list));

		$checked_fonts = get_option( $check_fonts_name );
		$all_google_fonts = get_option($all_fonts_name, $fonts_list);
		$all_google_fonts = json_decode($all_google_fonts);

		$defaults = array();
		if ( $checked_fonts ) {
			foreach ( $checked_fonts as $fonts ) {

				if ( $fonts && !empty($all_google_fonts) ) {

					foreach ($all_google_fonts as $key => $value ) {
						$value_font_family = isset($value->font_family) ? $value->font_family : '';
						$value_font_styles = isset($value->font_styles) ? $value->font_styles : '';
						if(!empty($value_font_family) && $value_font_family == $fonts ) {
							$defaults[ $fonts ] = $value_font_family .':'. $value_font_styles;
						}
					}

				}
			}
		}

		#$defaults[ 'poppins' ] = 'Poppins:300,400,500,600,700';

		return $defaults;
	}


	/**
	 * Adds google fonts to VC Custom Heading
	 *
	 * @param $vc_fonts
	 *
	 * @return array
	 */
	public function google_fonts_vc_custom($vc_fonts) {
		$google_fonts_name = $this->options['google_fonts']; // Just string for et option value
		$fonts_list = trim(preg_replace('/\s+/', ' ', $this->fonts_list)); // Get static Google Fonts List

		$google_fonts = get_option($google_fonts_name, $fonts_list); // Get option with all fonts
		$google_fonts_fixed = $this->google_font_fix_params($google_fonts);
		$basement_fonts = json_decode($google_fonts_fixed); // Convert option string to array


		$basement_check_fonts = $this->get_google_fonts();

		if(!empty($vc_fonts) && !empty($basement_check_fonts)) {
			foreach ( $vc_fonts as $key => $value ) {
				$vc_font_family = isset( $value->font_family ) ? $value->font_family : '';

				foreach ( $basement_check_fonts as $inner_key => $inner_value ) {
					$basement_font_family = isset( $inner_key ) ? $inner_key : '';

					if(!empty($basement_font_family)) {
						$basement_font_family = str_replace('+',' ', $basement_font_family);

						if($basement_font_family == $vc_font_family) {
							unset($basement_check_fonts[$inner_key]);
						}
					}
				}
			}

			if ( ! empty( $basement_check_fonts ) ) {
				foreach ( $basement_check_fonts as $key => $value ) {
					foreach ( $basement_fonts as $inner_key => $inner_value ) {
						$basement_font_family = isset( $inner_value->font_family ) ? $inner_value->font_family : '';
						$basement_font_styles = isset( $inner_value->font_styles ) ? $inner_value->font_styles : '';
						$basement_font_types = isset( $inner_value->font_types ) ? $inner_value->font_types : '';
						if ( $basement_font_family == $key && ! empty( $basement_font_family ) && ! empty( $basement_font_styles ) && !empty($basement_font_types) ) {
							$basement_font_family = str_replace( '+', ' ', $basement_font_family );
							$basement_font_styles = str_replace( array( '400', '400i' ), array( 'regular', 'italic' ), $basement_font_styles );
							$vc_fonts[] = (object) array(
								"font_family" => $basement_font_family,
								"font_styles" => $basement_font_styles,
								"font_types"  => $basement_font_types
							);
						}
					}

				}
			}
		}

		return $vc_fonts;
	}


	public function ajax_remove_google_font() {
		$font = isset($_POST['font']) ? $_POST['font'] : ''; // Get AJAX variable


		$google_fonts_name = $this->options['google_fonts']; // Just string for et option value
		$fonts_list = trim(preg_replace('/\s+/', ' ', $this->fonts_list)); // Get static Google Fonts List

		$google_fonts = get_option($google_fonts_name, $fonts_list); // Get option with all fonts
		$google_fonts_fixed = $this->google_font_fix_params($google_fonts);
		$google_fonts_array = json_decode($google_fonts_fixed); // Convert option string to array

		if ( ! empty( $font ) ) {
			foreach ( $google_fonts_array as $key => $value ) {
				$value_font_family = isset( $value->font_family ) ? $value->font_family : '';

				if ( ! empty( $value_font_family ) ) {
					if ( $value_font_family == $font ) {
						unset($google_fonts_array[$key]);
					}
				}
			}
			$google_fonts_array = array_values($google_fonts_array);

			$updated = update_option( $google_fonts_name, json_encode($google_fonts_array) );

			$result = array(
				'list' => $this->google_fonts_list('options'),
				'textarea' => json_encode($google_fonts_array)
			);

			if($updated) {
				echo json_encode($result);
			}

		}

		wp_die();
	}


	/**
	 * Function for AJAX adds Google Fonts
	 */
	public function ajax_add_new_google_font() {
		$url = isset($_POST['url']) ? $_POST['url'] : ''; // Get AJAX variable

		$google_fonts_name = $this->options['google_fonts']; // Just string for et option value
		$fonts_list = trim(preg_replace('/\s+/', ' ', $this->fonts_list)); // Get static Google Fonts List

		$google_fonts = get_option($google_fonts_name, $fonts_list); // Get option with all fonts
		$google_fonts_fixed = $this->google_font_fix_params($google_fonts);
		$google_fonts_array = json_decode($google_fonts_fixed); // Convert option string to array


		if ( ! empty( $url ) ) {
			$filter_result     = $this->google_font_filtrate( $url );
			$font_family       = isset( $filter_result['param']['font_family'] ) ? $filter_result['param']['font_family'] : '';
			$font_styles       = isset( $filter_result['param']['font_styles'] ) ? $filter_result['param']['font_styles'] : '';
			$font_styles_array = isset( $filter_result['param']['font_styles_array'] ) ? $filter_result['param']['font_styles_array'] : array();
			$new_font = isset( $filter_result['param']['new_font'] ) ? $filter_result['param']['new_font'] : false;
			$filter_result['list'] = '';
			$filter_result['textarea'] = '';
			if ( $filter_result['success'] && ! empty( $google_fonts_array ) ) {

				if($new_font) {
					$google_fonts_array[] = (object)array(
						'font_family' => $font_family,
						'font_styles' => $font_styles,
						'font_types' => ''
					);
					$filter_result['message'] = __('<span style="color:green;">The new font is successfully added.</span>', BASEMENT_TEXTDOMAIN);
				} else {
					$font_updated = '';
					$style_updated = '';
					foreach ( $google_fonts_array as $key => $value ) {
						$value_font_family = isset( $value->font_family ) ? $value->font_family : '';
						$value_font_styles = isset( $value->font_styles ) ? $value->font_styles : '';

						if ( ! empty( $value_font_family ) && ! empty( $value_font_styles ) ) {
							if ( $value_font_family === $font_family ) {
								$google_fonts_array[$key]->font_styles .= ','.$font_styles;
								$font_updated = str_replace('+','',$value_font_family);
								$style_updated = $font_styles;
								break;
							}
						}
					}
					$filter_result['message'] = sprintf(__('<span style="color:green;">The %s font successfully updated (%s).</span>', BASEMENT_TEXTDOMAIN), "<strong>{$font_updated}</strong>", $style_updated);
				}

				$google_fonts_array_sorted = $this->google_font_sort_styles($google_fonts_array); // Sort styles

				$updated = update_option( $google_fonts_name, json_encode($google_fonts_array_sorted) );

				if(!$updated) {
					$filter_result['message'] = __('Something went wrong.',BASEMENT_TEXTDOMAIN);
				} else {
					$filter_result['list'] = $this->google_fonts_list('options');
					$filter_result['textarea'] = json_encode($google_fonts_array_sorted);
				}
				echo json_encode($filter_result);
			} else {
				echo json_encode($filter_result);
			}
		}


		wp_die();
	}


	/**
	 * Sort Google Font styles
	 *
	 * @param array $google_fonts_array
	 *
	 * @return array
	 */
	public function google_font_sort_styles( $google_fonts_array = array() ) {
		if ( ! empty( $google_fonts_array ) ) {
			foreach ( $google_fonts_array as $key => $value ) {
				$value_font_family = isset( $value->font_family ) ? $value->font_family : '';
				$value_font_styles = isset( $value->font_styles ) ? $value->font_styles : '';
				$value_font_types  = isset( $value->font_types ) ? $value->font_types : '';

				if(!empty($value_font_styles)) {
					$styles = explode(',', $value_font_styles);
					sort($styles, SORT_NUMERIC);

					$google_fonts_array[$key]->font_styles = implode(',', $styles);
					$google_fonts_array[$key]->font_types = $this->google_font_create_font_types($styles);
				}
			}
		}

		return $google_fonts_array;
	}


	/**
	 * Fixed deprecated params e.g: italic => i
	 *
	 * @param string $data
	 *
	 * @return mixed
	 */
	public function google_font_fix_params($data = '') {
		return str_replace('0italic','0i',$data);
	}


	/**
	 * Filtrate new Google Font
	 *
	 * @param string $url
	 *
	 * @return array
	 */
	public function google_font_filtrate($url = '') {
		$result = array( 'success' => false, 'message' => __( 'Something went wrong', BASEMENT_TEXTDOMAIN ), 'param' => '' );

		if(!empty($url)) {
			$google_fonts = $this->options['google_fonts']; // Just string for et option value
			$fonts_list   = trim( preg_replace( '/\s+/', ' ', $this->fonts_list ) ); // Get static Google Fonts List

			$google_fonts       = get_option( $google_fonts, $fonts_list ); // Get option with all fonts
			$google_fonts_fixed = $this->google_font_fix_params($google_fonts);
			$google_fonts_array = json_decode( $google_fonts_fixed ); // Convert option string to array

			$result = $this->check_google_fonts_link( $url );

			if ( $result['success'] && ! empty( $google_fonts_array ) ) {
				$new_font_family       = isset( $result['param']['font_family'] ) ? $result['param']['font_family'] : '';
				$new_font_styles       = isset( $result['param']['font_styles'] ) ? $result['param']['font_styles'] : '';
				$new_font_styles_array = isset( $result['param']['font_styles_array'] ) ? $result['param']['font_styles_array'] : array();


				if ( ! empty( $new_font_family ) && ! empty( $new_font_styles ) && ! empty( $new_font_styles_array ) ) {

					$font_styles_array_filtrate = array();
					$new_font = true;

					foreach ( $google_fonts_array as $key => $value ) {
						$value_font_family = isset( $value->font_family ) ? $value->font_family : '';
						$value_font_styles = isset( $value->font_styles ) ? $value->font_styles : '';
						$value_font_types  = isset( $value->font_types ) ? $value->font_types : '';

						if ( ! empty( $value_font_family ) && ! empty( $value_font_styles ) ) {
							if ( $value_font_family === $new_font_family ) {
								$new_font = false;
								$value_font_styles_exploded = explode( ',', $value_font_styles );


								foreach ( $new_font_styles_array as $font_style ) {
									if ( ! in_array( $font_style, $value_font_styles_exploded ) ) {
										$font_styles_array_filtrate[] = $font_style;
									}
								}

								break;
							} else {
								continue;
							}
						}
					}


					if( !$new_font) {
						if(!empty($font_styles_array_filtrate)) {
							$result['param']['font_styles_array'] = $font_styles_array_filtrate;
							$result['param']['font_styles'] = implode( ',', $font_styles_array_filtrate );
							$result['param']['new_font'] = $new_font;
						} else {
							$result['success'] = false;
							$result['message'] = __( 'This font already exists in the list.', BASEMENT_TEXTDOMAIN );
							$result['param']   = '';
						}
					} else {
						$result['param']['new_font'] = $new_font;
					}

				}
			}
		}

		return $result;
	}


	/**
	 * Check Google Font URL
	 *
	 * @param $url
	 *
	 * @return bool
	 */
	public function google_font_url_exists( $url ) {
		$headers = get_headers( $url );

		return stripos( $headers[0], "200 OK" ) ? true : false;
	}


	/**
	 * Check new Google Font URL
	 *
	 * @param string $link
	 *
	 * @return array
	 */
	public function check_google_fonts_link($link = '') {
		$response = array( 'success' => false, 'message' => __( 'Something went wrong', BASEMENT_TEXTDOMAIN ), 'param' => '' );
		if(!empty($link)) {
			$url = esc_url( $link );
			$link_exist = $this->google_font_url_exists($url);
			$url = wp_parse_url( $url, PHP_URL_QUERY );

			if (strpos($url, 'family=') !== false && $link_exist) {
				if(!empty($url)) {
					$url = str_replace( array('family=','&','&amp;'), '', $url );
					$font = explode( ':', $url );
					$font_family = isset($font['0']) ? $font['0'] : '';
					$font_styles = isset($font['1']) ? $font['1'] : '';

					if(!empty($font_family) && strpos($font_family, '|') === false) {
						if(!empty($font_styles)) {
							$font_styles_array = explode(',',$font_styles);
						} else {
							$font_styles = '400';
							$font_styles_array = array('400');
						}

						$results = array(
							'font_family' => $font_family,
							'font_styles' => $font_styles,
							'font_styles_array' => $font_styles_array
						);

						$response = array('success'=>true,'message'=>'','param' => $results );
					} else {
						$response['message'] = __( 'It\'s wrong link', BASEMENT_TEXTDOMAIN );
					}


				}
			} else {
				$response['message'] = __( 'It\'s wrong link', BASEMENT_TEXTDOMAIN );
			}
		}
		return $response;
	}


	/**
	 * Generate Google Font types for VC
	 *
	 * @param array $styles
	 *
	 * @return string
	 */
	public function google_font_create_font_types ($styles = array()) {
		$types = array();

		if(!empty($styles)) {
			foreach ($styles as $style) {
				switch ($style) {
					case '100':
						$types[] = '100 light regular:100:normal';
						break;
					case '100italic':
					case '100i':
						$types[] = '100 light italic:100:italic';
						break;
					case '200':
						$types[] = '200 light regular:200:normal';
						break;
					case '200italic':
					case '200i':
						$types[] = '200 light italic:200:italic';
						break;
					case '300':
						$types[] = '300 light regular:300:normal';
						break;
					case '300italic':
					case '300i':
						$types[] = '300 light italic:300:italic';
						break;
					case '400':
						$types[] = '400 regular:400:normal';
						break;
					case '400italic':
					case '400i':
						$types[] = '400 italic:400:italic';
						break;
					case '500':
						$types[] = '500 bold regular:500:normal';
						break;
					case '500italic':
					case '500i':
						$types[] = '500 bold italic:500:italic';
						break;
					case '600':
						$types[] = '600 bold regular:600:normal';
						break;
					case '600italic':
					case '600i':
						$types[] = '600 bold italic:600:italic';
						break;
					case '700':
						$types[] = '700 bold regular:700:normal';
						break;
					case '700italic':
					case '700i':
						$types[] = '700 bold italic:700:italic';
						break;
					case '800':
						$types[] = '800 bold regular:800:normal';
						break;
					case '800italic':
					case '800i':
						$types[] = '800 bold italic:800:italic';
						break;
					case '900':
						$types[] = '900 bold regular:900:normal';
						break;
					case '900italic':
					case '900i':
						$types[] = '900 bold italic:900:italic';
						break;
				}
			}

			$types = implode(',',$types);
		}

		return $types;
	}


	/**
	 * Add Google Fonts to MCE Editor
	 *
	 * @param $mce_css
	 *
	 * @return string
	 */
	public function add_googlefonts_to_editor( $mce_css ) {
		$fonts = $this->get_google_fonts();

		if ( ! empty( $fonts ) ) {
			if ( ! empty( $mce_css ) ) {
				$mce_css .= ',';
			}

			$font_url = '//fonts.googleapis.com/css?family=' . implode( "%7C", $fonts );
			$mce_css .= str_replace( ',', '%2C', $font_url );

			return $mce_css;
		}

		return $mce_css;
	}


	/**
	 * Add Google Fonts to Template
	 */
	public function add_googlefonts_to_theme() {
		$fonts = $this->get_google_fonts();

		if ( ! empty( $fonts ) ) {
			$google_fonts_url = '//fonts.googleapis.com/css?family=' . implode( "%7C", $fonts );
			wp_enqueue_style( THEME_TEXTDOMAIN . '-basement-fonts', $google_fonts_url, array(), null );
		}
	}


	public function my_add_mce_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array(&$this, 'my_add_tinymce_plugin' ) );
			add_filter( 'mce_buttons_2', array(&$this, 'my_register_mce_button' ) );
		}
	}

	public function my_add_tinymce_plugin( $plugin_array ) {
		$fonts = $this->get_google_fonts();
		if ( ! empty( $fonts ) ) {
			$plugin_array['my_mce_button'] = plugin_dir_url( __FILE__ ) . 'js/tinymce/plug.googlefonts.select.js';
		}
		$plugin_array['my_lh_button'] = plugin_dir_url( __FILE__ ) . 'js/tinymce/line-height.select.js';
		$plugin_array['my_ls_button'] = plugin_dir_url( __FILE__ ) . 'js/tinymce/letter-spacing.select.js';


		return $plugin_array;
	}


	public function my_register_mce_button( $buttons ) {
		$fonts = $this->get_google_fonts();
		if ( ! empty( $fonts ) ) {
			array_push( $buttons, 'my_mce_button' );
		}
		array_push( $buttons, 'my_lh_button' );
		array_push( $buttons, 'my_ls_button' );
		return $buttons;
	}


	/**
	 * Added Google Font to Editor
	 */
	public function add_google_fonts_js() {
		$fonts = $this->get_google_fonts();
		if ( ! empty( $fonts ) ) {

			$array_fonts = array();
			foreach ( $fonts as $font => $value ) {
				$font_params = explode( ":", $value );

				$font_title  = isset( $font_params['0'] ) ? $font_params['0'] : '';
				$font_styles = isset( $font_params['1'] ) ? $font_params['1'] : '';


				if ( ! empty( $font_styles ) ) {
					$array_fonts[ $font ] = explode( ",", $font_styles );

					#$array_fonts[ $font ][0] = '400';

					$array_fonts[ $font ] = array_flip( $array_fonts[ $font ] );

					$array_fonts[ $font ]['title_font'] = str_replace( '+', ' ', $font_title );


					foreach ( $array_fonts[ $font ] as $id => $key ) {

						switch ( $id ) {
							case '100':
								$array_fonts[ $font ][ $id ] = 'Thin ' . $id;
								break;
							case '100italic':
							case '100i':
								$array_fonts[ $font ][ $id ] = 'Thin ' . substr( $id, 0, 3 ) . ' Italic';
								break;
							case '200':
								$array_fonts[ $font ][ $id ] = 'Extra-Light ' . $id;
								break;
							case '200italic':
							case '200i':
								$array_fonts[ $font ][ $id ] = 'Extra-Light ' . substr( $id, 0, 3 ) . ' Italic';
								break;
							case '300':
								$array_fonts[ $font ][ $id ] = 'Light ' . $id;
								break;
							case '300italic':
							case '300i':
								$array_fonts[ $font ][ $id ] = 'Light ' . substr( $id, 0, 3 ) . ' Italic';
								break;
							case '400':
								$array_fonts[ $font ][ $id ] = 'Normal ' . $id;
								break;
							case '400italic':
							case '400i':
								$array_fonts[ $font ][ $id ] = 'Normal ' . substr( $id, 0, 3 ) . ' Italic';
								break;
							case '500':
								$array_fonts[ $font ][ $id ] = 'Medium ' . $id;
								break;
							case '500italic':
							case '500i':
								$array_fonts[ $font ][ $id ] = 'Medium ' . substr( $id, 0, 3 ) . ' Italic';
								break;
							case '600':
								$array_fonts[ $font ][ $id ] = 'Semi-Bold ' . $id;
								break;
							case '600italic':
							case '600i':
								$array_fonts[ $font ][ $id ] = 'Semi-Bold ' . substr( $id, 0, 3 ) . ' Italic';
								break;
							case '700':
								$array_fonts[ $font ][ $id ] = 'Bold ' . $id;
								break;
							case '700italic':
							case '700i':
								$array_fonts[ $font ][ $id ] = 'Bold ' . substr( $id, 0, 3 ) . ' Italic';
								break;
							case '800':
								$array_fonts[ $font ][ $id ] = 'Extra-Bold ' . $id;
								break;
							case '800italic':
							case '800i':
								$array_fonts[ $font ][ $id ] = 'Extra-Bold ' . substr( $id, 0, 3 ) . ' Italic';
								break;
							case '900':
								$array_fonts[ $font ][ $id ] = 'Ultra-Bold ' . $id;
								break;
							case '900italic':
							case '900i':
								$array_fonts[ $font ][ $id ] = 'Ultra-Bold ' . substr( $id, 0, 3 ) . ' Italic';
								break;
						}
					}

				}

			}



			wp_localize_script( 'jquery', 'objGF', $array_fonts );
		}


	}

}