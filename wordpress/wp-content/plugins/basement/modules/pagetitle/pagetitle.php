<?php
defined( 'ABSPATH' ) or die();

define( 'PAGETITLEPATH', WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );

class Basement_Pagetitle {

	private static $instance = null;

	// Name for Page Title options
	private $options = array();

	// Block for Page Title options (options & meta)
	private $options_blocks = array();

	/**
	 * Basement_Page Title constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {

			$this->set_options_blocks();

			$this->set_options_names();

			add_action( 'admin_init', array( &$this, 'register_theme_settings' ) );

			add_filter(
				BASEMENT_TEXTDOMAIN . '_theme_settings_config_filter',
				array( &$this, 'theme_settings_config_filter' )
			);

			add_action( 'add_meta_boxes', array( &$this, 'generate_pagetitle_param_meta_box' ), 10, 2 );

			add_action( 'edit_form_after_title', array( &$this, 'pagetitle_meta_box_move' ) );


		}
	}

	/**
	 * Basement_Page Title init
	 *
	 * @return Basement_Pagetitle|null
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Pagetitle();
		}

		return self::$instance;
	}


	/**
	 * Check if breadcrumbs plugin is enable
	 *
	 * @return bool
	 */
	public function breadcrumbs_navtx_enable() {
		return in_array( 'breadcrumb-navxt/breadcrumb-navxt.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}


	/**
	 * Register Meta Box
	 *
	 * @param $post_type
	 */
	public function generate_pagetitle_param_meta_box( $post_type, $post ) {
		$post_ID = $post->ID;
		if ( $post_ID != get_option( 'page_for_posts' ) ) {
			if ( in_array( $post_type, array( 'page', 'post', 'product', 'single_project' ) ) ) {
				add_meta_box(
					'pagetitle_parameters_meta_box',
					__( 'Page Title Parameters', BASEMENT_TEXTDOMAIN ),
					array( &$this, 'render_pagetitle_param_meta_box' ),
					$post_type,
					'pagetitle',
					'high'
				);

				add_filter( 'postbox_classes_' . $post_type . '_' . 'pagetitle_parameters_meta_box', array(
					&$this,
					'class_meta_box'
				) );
			}
		}
	}

	/**
	 * Change metabox after load
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function class_meta_box( $classes = array() ) {
		if ( ! in_array( 'closed', $classes ) ) {
			$classes[] = 'closed';
		}
		return $classes;
	}


	/**
	 * Move meta box
	 */
	public function pagetitle_meta_box_move($params) {
		global $post, $wp_meta_boxes;
		$post_type = isset( $params->post_type ) ? $params->post_type : '';
		if ( ! empty( $post_type ) &&  in_array( $post_type, array( 'page', 'post', 'product', 'single_project' ) )  ) {
			do_meta_boxes( get_current_screen(), 'pagetitle', $post );

			unset( $wp_meta_boxes[ get_post_type( $post ) ]['pagetitle'] );
		}
	}


	/**
	 * Render Meta Box Parameters
	 */
	public function render_pagetitle_param_meta_box( $post ) {
		$view = new Basement_Plugin();
		$view->basement_views( $this->theme_settings_meta_box(), array( 'pagetitle-param-meta-box' ) );
	}


	/**
	 * Settings for page title meta box
	 *
	 * @return array
	 */
	public function theme_settings_meta_box() {
		$settings_config = array();

		foreach ( $this->options_blocks as $key => $value ) {
			if (
				$value['input'] === 'pt_custom_title'
			)
				continue;
			$value['key']  = $value['input'];
			$value['input'] = call_user_func( array( &$this, $value['input'] ), 'metabox' );
			$settings_config[] = $value;
		}

		return $settings_config;
	}


	/**
	 * List of option name settings
	 *
	 * @return array
	 */
	private function options_names() {
		return array(
			'pt_placement'  => BASEMENT_TEXTDOMAIN . '_pt_placement',
			'pt_style'      => BASEMENT_TEXTDOMAIN . '_pt_style',
			'pt_elements'   => BASEMENT_TEXTDOMAIN . '_pt_elements',
			'pt_icon'       => BASEMENT_TEXTDOMAIN . '_pt_icon',
			'pt_icon_size'  => BASEMENT_TEXTDOMAIN . '_pt_icon_size',
			'pt_icon_color' => BASEMENT_TEXTDOMAIN . '_pt_icon_color',
			'pt_bg' => BASEMENT_TEXTDOMAIN . '_pt_bg',
			'pt_bg_color' => BASEMENT_TEXTDOMAIN . '_pt_bg_color',
			'pt_bg_opacity' => BASEMENT_TEXTDOMAIN . '_pt_bg_opacity',
			'pt_position'   => BASEMENT_TEXTDOMAIN . '_pt_position',
			'pt_title_size'   => BASEMENT_TEXTDOMAIN . '_pt_title_size',
			'pt_title_color'   => BASEMENT_TEXTDOMAIN . '_pt_title_color',
			'pt_alternate'  => BASEMENT_TEXTDOMAIN . '_pt_alternate',
			'pt_padding_top'    => BASEMENT_TEXTDOMAIN . '_pt_padding_top',
			'pt_padding_bottom' => BASEMENT_TEXTDOMAIN . '_pt_padding_bottom',

			'pt_float_enable' => BASEMENT_TEXTDOMAIN . '_pt_float_enable',
			'pt_float_text_color' => BASEMENT_TEXTDOMAIN . '_pt_float_text_color',
			'pt_float_text_size' => BASEMENT_TEXTDOMAIN . '_pt_float_text_size',

			'pt_off'        => BASEMENT_TEXTDOMAIN . '_pt_off',
			'pt_custom_title' => array(
				#'pt_custom_search'   => BASEMENT_TEXTDOMAIN . '_pt_custom_search',
				'pt_custom_blog'     => BASEMENT_TEXTDOMAIN . '_pt_custom_blog',
				'pt_custom_archives' => BASEMENT_TEXTDOMAIN . '_pt_custom_archives',
				'pt_custom_404'      => BASEMENT_TEXTDOMAIN . '_pt_custom_404'
			)
		);
	}


	/**
	 * Set name for block in Page Title settings
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
				'title'       => __( 'Alternative title', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the alternative title for page.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_alternate'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Placement', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the page title position.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_placement'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Style', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the preset style for page title.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_style'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Page title elements', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'It enables or disables the elements in page title.<br/><b><ins>Breadcrumbs</ins> section works only if <a href="https://wordpress.org/plugins/breadcrumb-navxt/" target="_blank" style="text-decoration: none;" title="Breadcrumb NavXT">Breadcrumb NavXT</a> plugin activated.</b>', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_elements'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Icon', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the icon for page title.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_icon'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Title', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the main title for page title.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_position'
			),


			array(
				'type'        => 'dom',
				'title'       => __( 'Float Title', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the floating title for the page.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_float'
			),

			array(
				'type'        => 'dom',
				'title'       => __( 'Background', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the opacity and background color/image for page title.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_bg'
			),
			/*array(
				'type'        => 'dom',
				'title'       => __( 'Below the page title', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Set the height of additional block after page title.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_flow'
			),*/
			array(
				'type'        => 'dom',
				'title'       => __( 'Padding', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the inner padding for the page title.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_padding'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Disable page title', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Completely turn off the page title', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_off'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Custom title', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the custom title for blog and search page.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'pt_custom_title'
			)
		);
	}


	/**
	 * Set block for page title settings
	 */
	private function set_options_blocks() {
		$this->options_blocks = $this->options_blocks();
	}


	/**
	 * Register options for page title
	 */
	public function register_theme_settings() {
		foreach ( $this->options as $key => $value ) {
			if (
				$key === 'pt_alternate'
				#|| $key === 'pt_flow'
			)
				continue;

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
	 * Find Page With 404 Template
	 */
	public function get_id_404_template_page() {
		$args = array(
			'post_type'  => 'page',
			'fields'     => 'ids',
			'nopaging'   => true,
			'orderby'    => 'post_date',
			'order'      => 'DESC',
			'meta_key'   => '_wp_page_template',
			'meta_value' => apply_filters('basement_404_template_name','page-templates/page-404.php')
		);

		$page_id = '';

		$pages = get_posts( apply_filters('basement_404_template_args', $args) );
		if(!empty($pages)) {
			foreach ( $pages as $index => $id ) {
				if ($index == 0) {
					$page_id = $id;
				}
			}
		}

		return apply_filters('basement_404_page_id', $page_id );
	}


	/**
	 * Get all options
	 *
	 * @return array
	 */
	public function get_pagetitle_settings() {

		$id = get_the_ID();
		$names = $this->options_names();

		if ( Basement_Ecommerce_Woocommerce::enabled() ) {
			if ( is_shop() || is_tax( array( 'product_cat', 'product_tag' ) ) ) {
				$id = get_option( 'woocommerce_shop_page_id' );
			}
		}
		if (is_404()) {
			$id = $this->get_id_404_template_page();
		}

		if ( ! is_home() && ! is_search() && ! is_archive() ) {
			$custom_pagetitle = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );
		} elseif ( Basement_Ecommerce_Woocommerce::is_shop() || is_tax( array( 'product_cat', 'product_tag' ) ) ) {
			$custom_pagetitle = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );
		} else {
			$custom_pagetitle = '';
		}

		$settings = array();

		foreach ( $names as $key => $value ) {
			if (
				$key === 'pt_custom_title'
			)
				continue;

			if ( ! empty( $custom_pagetitle ) && !is_search() ) {
				$option     = '_basement_meta_pagetitle' . substr( $value, 18 );
				$post_value = get_post_meta( $id, $option, true );

				switch ($key) {
					case 'pt_icon':
					case 'pt_icon_size':
					case 'pt_icon_color':
					case 'pt_bg':
					case 'pt_bg_color':
					case 'pt_bg_opacity':
					case 'pt_title_size':
					case 'pt_title_color':
					case 'pt_padding_top':
					case 'pt_padding_bottom':

					case 'pt_float_text_size':
					case 'pt_float_text_color':
						$settings[ $key ] = $post_value;
						break;
					default :
						if ( empty( $post_value ) ) {
							$settings[ $key ] = get_option( $value );
						} else {
							$settings[ $key ] = $post_value;
						}
				}


			} else {
				if (
					$key === 'pt_alternate'
					#|| $key === 'pt_flow'
				)
					continue;
				$settings[ $key ] = get_option( $value );
			}

		}


		// Set default values for page title
		if(!empty($settings)) {
			foreach ($settings as $key_setting => $value_setting ) {
				if(!$value_setting) {
					switch ($key_setting) {
						case 'pt_placement' :
							$settings[$key_setting] = 'under';
							break;
						case 'pt_style' :
							$settings[$key_setting] = 'dark';
							break;
						case 'pt_elements' :
							$settings[$key_setting] = array(
								'icon' => 'icon',
								'title' => 'title',
								'breadcrumbs' => 'breadcrumbs'
							);
							break;
						case 'pt_off' :
							$settings[$key_setting] = 'no';
							break;
						case 'pt_float_enable' :
							$settings[$key_setting] = 'no';
							break;
						case 'pt_position' :
							$settings[$key_setting] = 'left';
							break;
					}
				}
			}
		}

		return apply_filters( 'basement_pagetitle_settings', $settings );
	}


	/**
	 * Return formatted classes
	 *
	 * @param string $in_classes
	 *
	 * @return mixed|void
	 */
	public function front_classes_page_title($in_classes = '') {
		$names   = apply_filters( 'basement_pagetitle_classes', $this->get_pagetitle_settings() );
		$classes = array();
		foreach ( $names as $key => $value ) {

			if($value) {

				if ( $key === 'pt_elements' ) {
					foreach ( $value as $inner_key => $inner_value ) {
						if ( empty( $inner_value ) ) {
							$classes[] = 'page-title_' . $inner_key . '_no';
						} else {
							$classes[] = 'page-title_' . $inner_key . '_yes';
						}
					}
				}

				/*if ( $key === 'pt_flow' && ! empty( $value ) ) {
					$classes[] = 'page-title_' . $key;
				}*/

				if (
					$key === 'pt_alternate' ||
					$key === 'pt_elements' ||
					$key === 'pt_float_text_size' ||
					$key === 'pt_float_text_color' ||
					$key === 'pt_custom_title' ||
					$key === 'pt_icon' ||
					$key === 'pt_icon_size' ||
					$key === 'pt_icon_color' ||
					$key === 'pt_title_size' ||
					$key === 'pt_title_color' ||
					$key === 'pt_bg' ||
					$key === 'pt_bg_color' ||
					$key === 'pt_bg_opacity' ||
					$key === 'pt_padding_top' ||
					$key === 'pt_padding_bottom'
				) {
					continue;
				}

				$classes[] = 'page-title' . substr( $key, 2 ) . '_' . sanitize_html_class( $value );

			}
		}

		$classes[] = $in_classes;

		return implode( ' ', apply_filters( 'basement_pagetitle_classes_format', $classes ) );
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
			'pagetitle' => array(
				'title'  => __( 'Page Title', BASEMENT_TEXTDOMAIN ),
				'blocks' => array()
			)
		);

		foreach ( $this->options_blocks as $key => $value ) {
			if(
				$value['input'] === 'pt_alternate'
				#$value['input'] === 'pt_flow'
			)
				continue;

			$value['input'] = call_user_func( array( &$this, $value['input'] ) );
			$settings_config['pagetitle']['blocks'][] = $value;
		}

		return array_slice( $config, 0, 1, true ) + $settings_config + array_slice( $config, 1, count( $config ) - 1, true );
	}


	/**
	 * Ser custom page title
	 *
	 * @param string $type
	 *
	 * @return DOMNode|string
	 */
	public function pt_custom_title($type = '') {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$custom_titles = $this->options['pt_custom_title'];
		$titles = array(
			#__('Search', BASEMENT_TEXTDOMAIN),
			__('Blog', BASEMENT_TEXTDOMAIN),
			__('Archives', BASEMENT_TEXTDOMAIN),
			__('404', BASEMENT_TEXTDOMAIN)
		);
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$i = 0;
		foreach ($custom_titles as $key => $value) {

			$option_value = get_option( $value );
			$params['current_value'] = $option_value;

			$input = new Basement_Form_Input( array(
				'label_text' => $titles[$i++],
				'name'  => $value,
				'value' => $option_value
			) );

			$container->appendChild( $dom->importNode( $input->create(), true ) );
			$container->setAttribute( 'style', 'width:250px;margin-bottom:17px;' );
		}

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}

	}

	/**
	 * Sets placement for page title
	 *
	 * @param string $type
	 *
	 * @return DOMNode|string
	 */
	public function pt_placement( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params        = array(
			'values' => array(
				'under' => __( 'Under Header ', BASEMENT_TEXTDOMAIN ),
				'after'  => __( 'After Header', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'under';


		if ( $type === 'metabox' ) {
			$option = '_basement_meta_pagetitle' . substr( $this->options['pt_placement'], 18 );
			$post_value = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option = $this->options['pt_placement'];
			$option_value = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;


		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Style for page title
	 *
	 * @param string $type
	 *
	 * @return DOMNode|string
	 */
	public function pt_style( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params        = array(
			'values' => array(
				'dark'  => __( 'Dark', BASEMENT_TEXTDOMAIN ),
				'white' => __( 'Light', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'dark';


		if ( $type === 'metabox' ) {
			$option = '_basement_meta_pagetitle' . substr( $this->options['pt_style'], 18 );
			$post_value = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option = $this->options['pt_style'];
			$option_value = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;


		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Page Title Element
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function pt_elements( $type = '' ) {
		global $post;
		$id        = isset( $post->ID ) ? $post->ID : ' ';
		$post_type = get_post_type( $id );

		$woo_pages = Basement_Ecommerce_Woocommerce::woo_pages();

		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$params = array(
			'values' => array(
				'icon'  => __( 'Icon', BASEMENT_TEXTDOMAIN ),
				'title' => __( 'Title', BASEMENT_TEXTDOMAIN ),
				'line'  => __( 'Separator', BASEMENT_TEXTDOMAIN )
			)
		);

		$current_param = array( 'title', 'line' );

		if ( $this->breadcrumbs_navtx_enable() ) {
			if ( ! in_array( $id, $woo_pages ) && $post_type !== 'product' ) {
				$params['values']['breadcrumbs'] = __( 'Breadcrumbs', BASEMENT_TEXTDOMAIN );
				array_push( $current_param, 'breadcrumbs' );
			}
		}


		/*if ( Basement_Ecommerce_Woocommerce::enabled() ) {
			if ( $type === 'metabox' && ( in_array( $id, $woo_pages ) || $post_type === 'product' ) ) {
				//$params['values'] = array_slice($params['values'], 0, 3, true) + array("woo_breadcrumbs" => __( 'WooCommerce Breadcrumbs', BASEMENT_TEXTDOMAIN )) + array_slice($params['values'], 3, count($params['values']) - 1, true) ;
				//array_splice( $current_param, 3, 0, array('woo_breadcrumbs') )

				$params['values']['woo_breadcrumbs'] = __( 'WooCommerce breadcrumbs', BASEMENT_TEXTDOMAIN );
				array_push( $current_param, 'woo_breadcrumbs' );
			} elseif ( empty( $type ) && ( ! in_array( $id, $woo_pages ) || $post_type !== 'product' ) ) {
				//$params['values'] = array_slice($params['values'], 0, 3, true) + array("woo_breadcrumbs" => __( 'WooCommerce Breadcrumbs', BASEMENT_TEXTDOMAIN )) + array_slice($params['values'], 3, count($params['values']) - 1, true) ;
				//array_splice( $current_param, 3, 0, array('woo_breadcrumbs') );

				$params['values']['woo_breadcrumbs'] = __( 'WooCommerce breadcrumbs', BASEMENT_TEXTDOMAIN );
				array_push( $current_param, 'woo_breadcrumbs' );
			}
		}*/


		if ( $this->breadcrumbs_navtx_enable() || Basement_Ecommerce_Woocommerce::enabled() ) {
			$params['values']['breadcrumbs_last'] = __( 'Remove end of breadcrumb?', BASEMENT_TEXTDOMAIN );
			#array_push( $current_param, 'breadcrumbs_last' );
		}


		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_pagetitle' . substr( $this->options['pt_elements'], 18 );
			$post_value              = get_post_meta( $id, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['pt_elements'];
			$option_value            = get_option( $option );
			$params['current_value'] = empty( $option_value ) ? $current_param : $option_value;
		}

		$params['id']         = $option;
		$params['name']       = $option;
		$params['label_text'] = __( 'Select the items to display:', BASEMENT_TEXTDOMAIN );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Checkbox_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Setting for Page Title Icon
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function pt_icon( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$label_icon = __( 'Icon', BASEMENT_TEXTDOMAIN );
		$label_icon_size = __( 'Size (in px)', BASEMENT_TEXTDOMAIN );
		$label_icon_color = __( 'Color', BASEMENT_TEXTDOMAIN );

		$name_icon = $this->options['pt_icon'];
		$name_icon_size = $this->options['pt_icon_size'];
		$name_icon_color = $this->options['pt_icon_color'];

		if ( $type === 'metabox' ) {
			$name_icon = '_basement_meta_pagetitle' . substr( $name_icon, 18 );
			$name_icon_size = '_basement_meta_pagetitle' . substr( $name_icon_size, 18 );
			$name_icon_color = '_basement_meta_pagetitle' . substr( $name_icon_color, 18 );
			$value_icon  = get_post_meta( $post->ID, $name_icon, true );
			$value_icon_size = get_post_meta( $post->ID, $name_icon_size, true );
			$value_icon_color = get_post_meta( $post->ID, $name_icon_color, true );
		} else {
			$value_icon = esc_attr( get_option( $name_icon ) );
			$value_icon_size = esc_attr( get_option( $name_icon_size ) );
			$value_icon_color = esc_attr( get_option( $name_icon_color ) );
		}

		$input_icon = new Basement_Form_Input( array(
			'label_text' => $label_icon,
			'name'  => $name_icon,
			'help_text' => __('Use only class names from icons library for this field.', BASEMENT_TEXTDOMAIN),
			'value' => $value_icon,
			'help_icon' => 'icons',
			'attributes' => array(
				'placeholder' => __('e.g.: fa fa-picture-o', BASEMENT_TEXTDOMAIN)
			),
			'style'=> 'width:100%;'
		) );


		$input_icon_size = new Basement_Form_Input( array(
			'label_text' => $label_icon_size,
			'name'  => $name_icon_size,
			'type' => 'number',
			'min' => '0',
			'step' => '1',
			'value' => $value_icon_size,
			'style'=> 'width:100%;'
		) );

		$input_icon_color = new Basement_Form_Input_Colorpicker( array(
			'label_text' => $label_icon_color,
			'name'  => $name_icon_color,
			'value' => $value_icon_color,
			'style'=> 'width:100%;'
		) );

		$icon = $container->appendChild( $dom->createElement( 'div' ) );
		$icon->setAttribute( 'style', 'width:320px;margin-bottom:17px;' );
		$icon->appendChild( $dom->importNode( $input_icon->create(), true ) );

		$icon_size = $container->appendChild( $dom->createElement( 'div' ) );
		$icon_size->setAttribute( 'style', 'width:120px;margin-bottom:17px;' );
		$icon_size->appendChild( $dom->importNode( $input_icon_size->create(), true ) );

		$icon_color = $container->appendChild( $dom->createElement( 'div' ) );
		$icon_color->setAttribute( 'style', 'width:120px;' );
		$icon_color->appendChild( $dom->importNode( $input_icon_color->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Setting for Page Title Background
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function pt_bg( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		#$label_bg = __( 'Background', BASEMENT_TEXTDOMAIN );
		$label_bg_color = __( 'Color', BASEMENT_TEXTDOMAIN );
		$label_bg_opacity = __( 'Opacity', BASEMENT_TEXTDOMAIN );

		$name_bg = $this->options['pt_bg'];
		$name_bg_opacity = $this->options['pt_bg_opacity'];
		$name_bg_color = $this->options['pt_bg_color'];

		if ( $type === 'metabox' ) {
			$name_bg = '_basement_meta_pagetitle' . substr( $name_bg, 18 );
			$name_bg_opacity = '_basement_meta_pagetitle' . substr( $name_bg_opacity, 18 );
			$name_bg_color = '_basement_meta_pagetitle' . substr( $name_bg_color, 18 );
			$value_bg  = get_post_meta( $post->ID, $name_bg, true );
			$value_bg_opacity = get_post_meta( $post->ID, $name_bg_opacity, true );
			$value_bg_color = get_post_meta( $post->ID, $name_bg_color, true );
		} else {
			$value_bg = get_option( $name_bg, apply_filters('basement_default_page_title_bg_img','') );
			$value_bg_opacity = get_option( $name_bg_opacity, apply_filters('basement_default_page_title_opacity','0.5') );
			$value_bg_color = get_option( $name_bg_color, apply_filters('basement_default_page_title_bg_color','#141414') );
		}

		$input_bg = new Basement_Form_Input_Image( array(
				#'label_text' => $label_bg,
				'name' => $name_bg,
				'value' => $value_bg,
				'text_buttons' => true,
				'upload_text' => __( 'Set background image', BASEMENT_TEXTDOMAIN ),
				'delete_text' => __( 'Remove background image', BASEMENT_TEXTDOMAIN ),
				'frame_title' => __( 'Set background image', BASEMENT_TEXTDOMAIN ),
				'frame_button_text' => __( 'Set background image', BASEMENT_TEXTDOMAIN ),
			)
		);

		$input_bg_color = new Basement_Form_Input_Colorpicker( array(
			'label_text' => $label_bg_color,
			'name'  => $name_bg_color,
			'value' => $value_bg_color,
			'help_text' => __('Sets background color for the layer above the image.', BASEMENT_TEXTDOMAIN),
			'style'=> 'width:100%;'
		) );


		$input_bg_opacity = new Basement_Form_Input( array(
			'label_text' => $label_bg_opacity,
			'name'  => $name_bg_opacity,
			'type' => 'number',
			'min' => '0',
			'step' => '0.1',
			'help_text' => __('Sets opacity for background color.', BASEMENT_TEXTDOMAIN),
			'max' => '1',
			'value' => $value_bg_opacity,
			'style'=> 'width:100%;'
		) );



		$bg = $container->appendChild( $dom->createElement( 'div' ) );
		$bg->setAttribute( 'style', 'width:320px;margin-bottom:25px;' );
		$bg->appendChild( $dom->importNode( $input_bg->create(), true ) );

		$bg_color = $container->appendChild( $dom->createElement( 'div' ) );
		$bg_color->setAttribute( 'style', 'width:220px;margin-bottom:17px;' );
		$bg_color->appendChild( $dom->importNode( $input_bg_color->create(), true ) );

		$bg_opacity = $container->appendChild( $dom->createElement( 'div' ) );
		$bg_opacity->setAttribute( 'style', 'width:220px;' );
		$bg_opacity->appendChild( $dom->importNode( $input_bg_opacity->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Disable page title
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function pt_off( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$params        = array(
			'values' => array(
				'no'  => __( 'No', BASEMENT_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'no';


		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_pagetitle' . substr( $this->options['pt_off'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['pt_off'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Title position
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function pt_position( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$params = array(
			'label_text' => __('Position',BASEMENT_TEXTDOMAIN),
			'values' => array(
				'left'         => __( 'Left', BASEMENT_TEXTDOMAIN ),
				'right'        => __( 'Right', BASEMENT_TEXTDOMAIN ),
				#'center_left'  => __( 'Center (breadcrumbs on the left)', BASEMENT_TEXTDOMAIN ),
				'center_right' => __( 'Center', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'center_right';


		if ( $type === 'metabox' ) {
			$option = '_basement_meta_pagetitle' . substr( $this->options['pt_position'], 18 );
			$post_value = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option = $this->options['pt_position'];
			$option_value = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;

		$input = new Basement_Form_Input_Radio_Group( $params );

		$position = $container->appendChild( $dom->createElement( 'div' ) );
		$position->setAttribute( 'class', 'basement_block-toggler' );
		$position->setAttribute( 'style', 'width:120px;margin-bottom:17px;' );
		$position->appendChild( $dom->importNode( $input->create(), true ) );

		/*--------------------------------------------------------------------------------*/

		$title_size = __( 'Size (in px)', BASEMENT_TEXTDOMAIN );
		$title_color = __( 'Color', BASEMENT_TEXTDOMAIN );

		$name_title_size = $this->options['pt_title_size'];
		$name_title_color = $this->options['pt_title_color'];

		if ( $type === 'metabox' ) {
			$name_title_size = '_basement_meta_pagetitle' . substr( $name_title_size, 18 );
			$name_title_color = '_basement_meta_pagetitle' . substr( $name_title_color, 18 );
			$value_title_size = get_post_meta( $post->ID, $name_title_size, true );
			$value_title_color = get_post_meta( $post->ID, $name_title_color, true );
		} else {
			$value_title_size = esc_attr( get_option( $name_title_size ) );
			$value_title_color = esc_attr( get_option( $name_title_color ) );
		}


		$input_title_size = new Basement_Form_Input( array(
			'label_text' => $title_size,
			'name'  => $name_title_size,
			'type' => 'number',
			'min' => '0',
			'step' => '1',
			'value' => $value_title_size,
			'style'=> 'width:100%;'
		) );

		$input_title_color = new Basement_Form_Input_Colorpicker( array(
			'label_text' => $title_color,
			'name'  => $name_title_color,
			'value' => $value_title_color,
			'style'=> 'width:100%;'
		) );

		$icon_size = $container->appendChild( $dom->createElement( 'div' ) );
		$icon_size->setAttribute( 'style', 'width:120px;margin-bottom:17px;' );
		$icon_size->appendChild( $dom->importNode( $input_title_size->create(), true ) );

		$icon_color = $container->appendChild( $dom->createElement( 'div' ) );
		$icon_color->setAttribute( 'style', 'width:120px;' );
		$icon_color->appendChild( $dom->importNode( $input_title_color->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Float Title
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function pt_float( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$params = array(
			'label_text' => __('Enable',BASEMENT_TEXTDOMAIN),
			'values' => array(
				'no'  => __( 'No', BASEMENT_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'yes';

		if ( $type === 'metabox' ) {
			$option = '_basement_meta_pagetitle' . substr( $this->options['pt_float_enable'], 18 );
			$post_value = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? 'no' : $post_value;
		} else {
			$option = $this->options['pt_float_enable'];
			$option_value = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;

		$input = new Basement_Form_Input_Radio_Group( $params );

		$position = $container->appendChild( $dom->createElement( 'div' ) );
		$position->setAttribute( 'class', 'basement_block-toggler' );
		$position->setAttribute( 'style', 'width:120px;margin-bottom:17px;' );
		$position->appendChild( $dom->importNode( $input->create(), true ) );

		/*--------------------------------------------------------------------------------*/

		$title_size = __( 'Size (in px)', BASEMENT_TEXTDOMAIN );
		$title_color = __( 'Color', BASEMENT_TEXTDOMAIN );

		$name_title_size = $this->options['pt_float_text_size'];
		$name_title_color = $this->options['pt_float_text_color'];

		if ( $type === 'metabox' ) {
			$name_title_size = '_basement_meta_pagetitle' . substr( $name_title_size, 18 );
			$name_title_color = '_basement_meta_pagetitle' . substr( $name_title_color, 18 );
			$value_title_size = get_post_meta( $post->ID, $name_title_size, true );
			$value_title_color = get_post_meta( $post->ID, $name_title_color, true );
		} else {
			$value_title_size = esc_attr( get_option( $name_title_size ) );
			$value_title_color = esc_attr( get_option( $name_title_color ) );
		}


		$input_title_size = new Basement_Form_Input( array(
			'label_text' => $title_size,
			'name'  => $name_title_size,
			'type' => 'number',
			'min' => '0',
			'step' => '1',
			'value' => $value_title_size,
			'style'=> 'width:100%;'
		) );

		$input_title_color = new Basement_Form_Input_Colorpicker( array(
			'label_text' => $title_color,
			'name'  => $name_title_color,
			'value' => $value_title_color,
			'style'=> 'width:100%;'
		) );

		$icon_size = $container->appendChild( $dom->createElement( 'div' ) );
		$icon_size->setAttribute( 'style', 'width:120px;margin-bottom:17px;' );
		$icon_size->appendChild( $dom->importNode( $input_title_size->create(), true ) );

		$icon_color = $container->appendChild( $dom->createElement( 'div' ) );
		$icon_color->setAttribute( 'style', 'width:120px;' );
		$icon_color->appendChild( $dom->importNode( $input_title_color->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Alternate title
	 *
	 * @param string $type
	 *
	 * @return DOMNode|string
	 */
	public function pt_alternate( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$meta = '_basement_meta_pagetitle' . substr( $this->options['pt_alternate'], 18 );


		$fragment = $dom->createDocumentFragment();
		$fragment->appendXML( '<p style="margin-top: 0;">Use shortcodes <b class="multitext">[white]...[/white]</b>, <b class="multitext">[black]...[/black]</b> or <b class="multitext">[gray]...[/gray]</b> to make the text in black, white or gray color.</p>' );

		$p = $container->appendChild($fragment);

		$input = new Basement_Form_Input_Textarea( array(
			'name'       => $meta,
			'value'      => esc_html(get_post_meta( $post->ID, $meta, true ))
		) );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Page title padding
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function pt_padding( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$label_pt = __( 'Padding top', BASEMENT_TEXTDOMAIN );
		$label_pb = __( 'Padding Bottom', BASEMENT_TEXTDOMAIN );

		$name_pt = $this->options['pt_padding_top'];
		$name_pb = $this->options['pt_padding_bottom'];

		if ( $type === 'metabox' ) {
			$name_pt = '_basement_meta_pagetitle' . substr( $name_pt, 18 );
			$name_pb = '_basement_meta_pagetitle' . substr( $name_pb, 18 );
			$value_pt  = get_post_meta( $post->ID, $name_pt, true );
			$value_pb = get_post_meta( $post->ID, $name_pb, true );
		} else {
			$value_pt = esc_attr( get_option( $name_pt ) );
			$value_pb = esc_attr( get_option( $name_pb ) );

		}

		$input_pt = new Basement_Form_Input( array(
			'label_text' => $label_pt,
			'name'  => $name_pt,
			'type' => 'number',
			'min' => '0',
			'step' => '1',
			'style' => 'width:100%;',
			'value' => $value_pt
		) );


		$input_pb = new Basement_Form_Input( array(
			'label_text' => $label_pb,
			'name'  => $name_pb,
			'type' => 'number',
			'min' => '0',
			'step' => '1',
			'style' => 'width:100%;',
			'value' => $value_pb,
		) );


		$container_logo_text_color = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text_color->setAttribute( 'style', 'width:120px;margin-bottom:17px;display:inline-block;vertical-align:top;margin-right:16px;' );
		$container_logo_text_color->appendChild( $dom->importNode( $input_pt->create(), true ) );

		$container_logo_text = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text->setAttribute( 'style', 'width:120px;display:inline-block;vertical-align:top;' );
		$container_logo_text->appendChild( $dom->importNode( $input_pb->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}

}







if ( ! function_exists( 'basement_page_title_float_sort_elements' ) ) {
	/**
	 * Display template parts elements in float page title
	 */
	function basement_page_title_float_sort_elements() {
		get_template_part( 'template-parts/page-title/title-float' );
	}
	add_action( 'conico_content_page_title_float', 'basement_page_title_float_sort_elements', 10 );
}


if ( ! function_exists( 'basement_page_title_float_class' ) ) {
	/**
	 * Display the classes for the page title float element.
	 *
	 * @param string $class
	 * @param bool   $echo
	 *
	 * @return array
	 */
	function basement_page_title_float_class( $class = '', $echo = true ) {
		if($echo) {
			// Separates classes with a single space, collates classes for page title element
			echo 'class="' . join( ' ', basement_get_page_title_float_class( $class ) ) . '" ';
		} else {
			return array('class'=>join( ' ', basement_get_page_title_float_class( $class ) ));
		}
	}
}


if ( ! function_exists( 'basement_get_page_title_float_class' ) ) {
	/**
	 * Retrieve the classes for the page title float element as an array.
	 *
	 * @param string $class
	 *
	 * @return array
	 */
	function basement_get_page_title_float_class( $class = '' ) {
		$classes = array();

		$pagetitle = new Basement_Pagetitle();
		$settings  = $pagetitle->get_pagetitle_settings();


		if(!empty($settings)) {
			foreach( $settings as $key => $value) {
				if(strpos($key, 'pt_float') !== false && strpos($key, 'size') === false && strpos($key, 'color') === false) {
					$classes[] = $key .'_' . $value;
				}
			}
		}

		$classes[] = 'pt_float_position_left';

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );

		$classes = apply_filters( 'pagetitle_float_class', $classes, $class );

		return array_unique( $classes );
	}
}


if ( ! function_exists( 'basement_action_theme_before_page_title_float' ) ) {
	/**
	 * Displays params before Page Title
	 */
	function basement_action_theme_before_page_title_float() {
		ob_start();
	}
	add_action('conico_before_page_title_float', 'basement_action_theme_before_page_title_float');
}


if ( ! function_exists( 'basement_action_after_page_title_float' ) ) {
	/**
	 * Displays params after Page Title
	 */
	function basement_action_after_page_title_float() {

		$pagetitle = new Basement_Pagetitle();
		$settings  = $pagetitle->get_pagetitle_settings();


		$pt_float_enable = isset($settings['pt_float_enable']) ? $settings['pt_float_enable'] : '';

		$page_title_float = ob_get_contents();
		ob_end_clean();


		if ( $pt_float_enable === 'yes' && ( is_singular( array( 'post', 'single_project' ) ) || is_page() ) ) {
			echo $page_title_float;
		}
	}
	add_action('conico_after_page_title_float', 'basement_action_after_page_title_float');
}


if ( ! function_exists( 'basement_page_title_black_render' ) ) {
	/**
	 * Render [black][/black] shortcode for pagetitle
	 *
	 * @param        $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function basement_page_title_black_render( $atts, $content = "" ) {
		$content = do_shortcode($content);
		return "<span class=\"page-title-black-word\">{$content}</span>";
	}
	add_shortcode( 'black', 'basement_page_title_black_render' );
}


if ( ! function_exists( 'basement_page_title_white_render' ) ) {
	/**
	 * Render [white][/white] shortcode for pagetitle
	 *
	 * @param        $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function basement_page_title_white_render( $atts, $content = "" ) {
		$content = do_shortcode($content);
		return "<span class=\"page-title-white-word\">{$content}</span>";
	}
	add_shortcode( 'white', 'basement_page_title_white_render' );
}


if ( ! function_exists( 'basement_page_title_gray_render' ) ) {
	/**
	 * Render [gray][/gray] shortcode for pagetitle
	 *
	 * @param        $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function basement_page_title_gray_render( $atts, $content = "" ) {
		$content = do_shortcode($content);
		return "<span class=\"page-title-gray-word\">{$content}</span>";
	}
	add_shortcode( 'gray', 'basement_page_title_gray_render' );
}


if ( ! function_exists( 'basement_page_title_class' ) ) {
	/**
	 * Display the classes for the page title element.
	 *
	 * @param $class
	 */
	function basement_page_title_class( $class = '', $echo = true ) {

		$pagetitle = new Basement_Pagetitle();
		$settings  = $pagetitle->get_pagetitle_settings();

		$style      = array();
		$style_attr = '';

		$pt_bg = isset($settings['pt_bg']) ? $settings['pt_bg'] : '';
		$padding_top    = is_numeric( $settings['pt_padding_top'] ) ? $settings['pt_padding_top'] : '';
		$padding_bottom = is_numeric( $settings['pt_padding_bottom'] ) ? $settings['pt_padding_bottom'] : '';

		if(is_numeric($pt_bg)) {
			$pt_bg = wp_get_attachment_image_url($pt_bg,'full');
			if(!empty($pt_bg)) {
				$style[]   = "background-image:url({$pt_bg});";
			}
		} else {
			$pt_bg = esc_url($pt_bg);
			if(!empty($pt_bg)) {
				$style[]   = "background-image:url({$pt_bg});";
			}
		}

		if($padding_top !== '') {
			$padding_top = absint($padding_top);
			$style[]   = "padding-top:{$padding_top}px;";
		}

		if($padding_bottom !== '') {
			$padding_bottom = absint($padding_bottom);
			$style[]   = "padding-bottom:{$padding_bottom}px;";
		}

		if ( ! empty( $style ) ) {
			$style      = implode( ' ', $style );
			$style_attr = " style=\"{$style}\" ";
		}

		if($echo) {
			// Separates classes with a single space, collates classes for page title element
			echo 'class="' . join( ' ', basement_get_page_title_class( $class ) ) . '" ' . $style_attr;
		} else {
			return array('class'=>join( ' ', basement_get_page_title_class( $class ) ), 'style' => $style_attr);
		}
	}
}


if ( ! function_exists( 'basement_get_page_title_class' ) ) {
	/**
	 * Retrieve the classes for the page title element as an array.
	 *
	 * @param string $class
	 *
	 * @return array
	 */
	function basement_get_page_title_class( $class = '' ) {
		$classes = array();

		$pagetitle = new Basement_Pagetitle();
		$classes[] = $pagetitle->front_classes_page_title();

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );

		$classes = apply_filters( 'pagetitle_class', $classes, $class );

		return array_unique( $classes );
	}
}


if ( ! function_exists( 'basement_page_title_sort_elements' ) ) {
	/**
	 * Display template parts elements in page title
	 */
	function basement_page_title_sort_elements() {
		get_template_part( 'template-parts/page-title/page-title' );
	}
	add_action( 'conico_content_page_title', 'basement_page_title_sort_elements', 10 );
}


if ( ! function_exists( 'basement_the_title_alternative' ) ) {
	/**
	 * Display alternative title
	 *
	 * @param bool $return
	 *
	 * @return string
	 */
	function basement_the_title_alternative($return = false) {
		$pagetitle = new Basement_Pagetitle();
		$settings  = $pagetitle->get_pagetitle_settings();
		$alternate = isset($settings['pt_alternate']) ? $settings['pt_alternate'] : '';

		if(!empty($alternate)) {
			if($return) {
				return do_shortcode(nl2br($alternate));
			} else {
				echo do_shortcode(nl2br($alternate));
			}
		}

	}
}


if ( ! function_exists( 'basement_the_specific_title' ) ) {
	/**
	 * Displays title for specific page
	 *
	 * @param string $type
	 * @param string $user_title
	 * @param bool   $echo
	 *
	 * @return bool|string
	 */
	function basement_the_specific_title( $type = '', $user_title = '', $echo = true ) {
		if ( ! $type ) {
			return false;
		}

		$title = esc_html( get_option( "basement_framework_pt_custom_{$type}" ) );

		if ( empty( $title ) ) {
			$title = $user_title;
		}

		if ( $echo ) {
			echo $title;
		} else {
			return $title;
		}
	}
}


if ( ! function_exists( 'basement_action_theme_before_page_title' ) ) {
	/**
	 * Displays params before Page Title
	 */
	function basement_action_theme_before_page_title() {
		ob_start();
	}
	add_action('conico_before_page_title', 'basement_action_theme_before_page_title');
}


if ( ! function_exists( 'basement_action_theme_after_page_title' ) ) {
	/**
	 * Displays params after Page Title
	 */
	function basement_action_theme_after_page_title() {

		$pagetitle = new Basement_Pagetitle();
		$settings  = $pagetitle->get_pagetitle_settings();


		$id = get_the_ID();
		$is_woo = false;

		if ( Basement_Ecommerce_Woocommerce::enabled() ) {
			$woo_pages = Basement_Ecommerce_Woocommerce::woo_pages();

			if ( in_array( $id, $woo_pages ) || Basement_Ecommerce_Woocommerce::woo_position() ) {
				$is_woo = true;
			}
		}
		$revslider_position = '';
		$shortcode = '';
		if ( is_page() || is_single() || Basement_Ecommerce_Woocommerce::is_shop() ) {
			if(Basement_Ecommerce_Woocommerce::is_shop()) {
				$id = get_option( 'woocommerce_shop_page_id' );
			}
			$revslider_position = get_post_meta( $id, 'basement_rev_position', true );
			$revslider_position = ! empty( $revslider_position ) ? $revslider_position : '';

			$shortcode = get_post_meta( $id, 'revlider_content_meta', true );
			$shortcode = ! empty( $shortcode ) ? $shortcode : '';

			if(empty($shortcode)) {
				$revslider_position = '';
			}
		}

		$pt_off = isset($settings['pt_off']) ? $settings['pt_off'] : '';
		$pt_element_title = isset($settings['pt_elements']['title']) ? $settings['pt_elements']['title'] : '';
		$pt_element_breadcrumb = isset($settings['pt_elements']['breadcrumbs']) ? $settings['pt_elements']['breadcrumbs'] : '';
		$pt_element_breadcrumb_woo = isset($settings['pt_elements']['woo_breadcrumbs']) ? $settings['pt_elements']['woo_breadcrumbs'] : '';
		$pt_elements_icon = isset($settings['pt_elements']['icon']) ? $settings['pt_elements']['icon'] : '';

		$page_title = ob_get_contents();
		ob_end_clean();

		if ( isset( $settings ) && ( $revslider_position !== 'header_content' && $pt_off == 'no' && ( ! empty( $pt_element_title ) || ( ! $is_woo && ! empty( $pt_element_breadcrumb ) ) || ( $is_woo && ! empty( $pt_element_breadcrumb_woo ) ) ) ) ) {
			echo $page_title;
		}
	}
	add_action('conico_after_page_title', 'basement_action_theme_after_page_title');
}


if ( ! function_exists( 'basement_page_title_customize' ) ) {
	/**
	 * Customize Page Title
	 */
	function basement_page_title_customize() {
		$pagetitle = new Basement_Pagetitle();
		$settings  = $pagetitle->get_pagetitle_settings();
		$styles = array(
			'.main-page-title' => '',
			'.pagetitle:after' => ''
		);

		#$flow = isset($settings['pt_flow']) ? $settings['pt_flow'] : '';
		$title_size = isset($settings['pt_title_size']) ? $settings['pt_title_size'] : '';
		$title_color = isset($settings['pt_title_color']) ? $settings['pt_title_color'] : '';
		$pt_bg_color = isset($settings['pt_bg_color']) ? $settings['pt_bg_color'] : '';
		$pt_bg_opacity = is_numeric($settings['pt_bg_opacity']) ? $settings['pt_bg_opacity'] : '';

		if(is_numeric($title_size)) {
			$styles['.main-page-title'] .= "font-size:{$title_size}px !important;";
		}

		if(!empty($title_color)) {
			$title_color = sanitize_hex_color($title_color);
			$styles['.main-page-title'] .= "color:{$title_color} !important;";
		}

		if(!empty($pt_bg_color)) {
			$pt_bg_color = sanitize_hex_color($pt_bg_color);
			$pt_bg_color = basement_hexToRgb($pt_bg_color);
			$pt_bg_color = !empty($pt_bg_color) ? $pt_bg_color['red'] .','. $pt_bg_color['green'] .','.$pt_bg_color['blue'] : '';
			$end_opacity = ',1';
			if($pt_bg_opacity !== '') {
				$end_opacity = ",{$pt_bg_opacity}";
			}
			$styles['.pagetitle:after'] .= "background-color:rgba({$pt_bg_color}{$end_opacity}) !important;";
		}



		if ( ! empty( $styles ) ) {
			?>
			<style type="text/css">
				<?php
				foreach ($styles as $selector => $value ) {
					if(!empty($value)) {
						echo $selector ."{{$value}}";
					}
				}
				?>
			</style>
			<?php
		}
	}

	add_action( 'wp_head', 'basement_page_title_customize' );
}


if ( ! function_exists( 'basement_page_title_icon' ) ) {
	/**
	 * Set icon for Page Title
	 */
	function basement_page_title_icon() {
		$pagetitle = new Basement_Pagetitle();
		$settings  = $pagetitle->get_pagetitle_settings();
		$icon      = isset( $settings['pt_elements']['icon'] ) ? $settings['pt_elements']['icon'] : '';
		$pt_icon   = isset( $settings['pt_icon'] ) ? $settings['pt_icon'] : '';
		$svg_file = '';
		$pt_icon_size  = isset( $settings['pt_icon_size'] ) ? $settings['pt_icon_size'] : '';
		$pt_icon_color = isset( $settings['pt_icon_color'] ) ? $settings['pt_icon_color'] : '';

		if ( ! empty( $icon ) && ! empty( $pt_icon ) ) {

			if ( preg_match( "#(^si-(.*?)-svg$)#", $pt_icon ) ) {

				$css_styles = array();
				$pt_icon = substr($pt_icon, 0, -4);
				$svg_name = $pt_icon . '.svg';
				$clear_name = str_replace(array('.svg','.','si-'),array('','-',''),$svg_name);

				if(defined('BASEMENT_SORTCODES_DIR')) {
					$svg_file = BASEMENT_SORTCODES_DIR . '/assets/images/svg/' . $svg_name;
				}

				if ( file_exists( $svg_file ) ) {

					$svg_content = file_get_contents( $svg_file );
					if ( $svg_content ) {
						$svg_content = preg_replace( array( "/<!--.*?-->/ms", '/<\?.*?\?>/ms' ), "", $svg_content );
						$id          = uniqid( 'vc-ai-' );

						$array = array();
						preg_match( '/id="([^"]*)"/i', $svg_content, $array );

						if ( empty( $array['1'] ) ) {
							$svg_content = preg_replace( '/<svg /', "<svg id=\"{$clear_name}\"", $svg_content, 1 );
							preg_match( '/id="([^"]*)"/i', $svg_content, $array );
						}


						if ( ! empty( $array['1'] ) ) {
							$id          = $id . '-' . $array['1'];
							$svg_content = preg_replace( '/id="(.*?)"/', "id=\"{$id}\"", $svg_content, 1 );



							if ( ! empty( $pt_icon_color ) ) {
								$css_styles[] = "#{$id} * {stroke:{$pt_icon_color} !important;}";
							}

							$css_styles[] = "#{$id} path {stroke-width:1px !important;}";


							$pt_icon_size_svg = '';
							$pt_icon_size_div = '';
							if ( is_numeric($pt_icon_size) ) {
								$pt_icon_size_svg = "style=\"width:{$pt_icon_size}px;\"";
								$pt_icon_size_div = sprintf('style="height:%1$spx;width:%1$spx;line-height:%1$spx;"',$pt_icon_size);
							}

							$svg_content = preg_replace( '/id="/', "{$pt_icon_size_svg} id=\"", $svg_content, 1 );

							if ( ! empty( $css_styles ) ) {
								$css_styles  = implode( '', $css_styles );
								$svg_content = preg_replace( '/<\/svg>/', "<style>{$css_styles} </style></svg>", $svg_content, 1 );
							}

							printf( '<div %2$s class="vc-animated-icon vc_is_animate_icon main-page-title-icon">%1$s</div>', $svg_content, $pt_icon_size_div );


						}
					}
				}


			} else {
				$icon_classes = array( $pt_icon, 'main-page-title-icon' );
				$icon_styles  = array();
				$icon_style   = '';

				if ( is_numeric( $pt_icon_size ) ) {
					$icon_styles[] = "font-size:{$pt_icon_size}px;";
					$icon_styles[] = "width:{$pt_icon_size}px;";
					$icon_styles[] = "height:{$pt_icon_size}px;";
					$icon_styles[] = "line-height:{$pt_icon_size}px;";
				}

				if ( ! empty( $pt_icon_color ) ) {
					$icon_styles[] = "color:{$pt_icon_color};";
				}


				if ( ! empty( $icon_styles ) ) {
					$icon_style = 'style="' . implode( ' ', $icon_styles ) . '"';
				}

				echo '<i ' . $icon_style . ' class="' . esc_attr( implode( ' ', $icon_classes ) ) . '"></i>';
			}

		}
	}
}


if ( ! function_exists( 'Basement_Page_Title' ) ) {
	/**
	 * Generate Basement Page Title Settings
	 *
	 * @return array
	 */
	function Basement_Page_Title() {
		$basement_page_title = new Basement_Pagetitle();
		return $basement_page_title->get_pagetitle_settings();
	}
}