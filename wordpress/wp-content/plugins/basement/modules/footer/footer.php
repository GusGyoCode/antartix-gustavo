<?php
defined( 'ABSPATH' ) or die();

$basement_footer = array();

class Basement_Footer {

	private static $instance = null;

	// Name for Footer options
	private $options = array();

	// Block for Footer options (options & meta)
	private $options_blocks = array();

	// Start tag for meta name
	private $meta_tag = '_basement_meta';

	/**
	 * Basement_Footer constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'register_theme_settings' ) );
		}
	}

	/**
	 * If footer exist
	 *
	 * @return array
	 */
	public function check_sidebars() {
		global $wp_registered_sidebars;

		return $wp_registered_sidebars;
	}

	/**
	 * Basement_Footer init
	 *
	 * @return Basement_Footer|null
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Footer();
		}

		return self::$instance;
	}


	/**
	 * Register options for footer
	 */
	public function register_theme_settings() {

		$sidebar_check = $this->check_sidebars();

		if ( empty( $sidebar_check ) )
			return;

		add_filter( BASEMENT_TEXTDOMAIN . '_theme_settings_config_filter', array(
			&$this,
			'theme_settings_config_filter'
		) );

		add_action( 'add_meta_boxes', array( &$this, 'generate_meta_box' ), 10, 2 );


		$this->set_options_blocks();

		$this->set_options_names();

		foreach ( $this->options as $key => $value ) {
			if (
				$key === 'single_footer' ||
				$key === 'single_footer_line' ||
				$key === 'single_footer_area' ||
				$key === 'single_footer_style' ||
				$key === 'single_footer_sticky'
			)
				continue;
			register_setting( 'basement_theme_options', $value );
		}
	}


	/**
	 * Init footer params
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	public function theme_settings_config_filter( $config = array() ) {
		$settings_config = array(
			'footer' => array(
				'title'  => __( 'Footer', BASEMENT_TEXTDOMAIN ),
				'blocks' => array()
			)
		);

		foreach ( $this->options_blocks as $key => $value ) {
			if( $value['input'] === 'single' || ( !Basement_Ecommerce_Woocommerce::enabled() && $value['input'] === 'woo' ) )
				continue;


			$value['input'] = call_user_func( array( &$this, $value['input'] ) );
			$settings_config['footer']['blocks'][] = $value;
		}

		return array_slice( $config, 0, 3, true ) + $settings_config + array_slice( $config, 3, count( $config ) - 1, true );
	}



	/**
	 * Register Meta Box
	 *
	 * @param $post_type
	 */
	public function generate_meta_box( $post_type, $post ) {
		$post_ID = $post->ID;
		if ( $post_ID != get_option( 'page_for_posts' ) ) {
			if ( in_array( $post_type, array( 'page', 'post', 'product', 'single_project' ) ) ) {
				add_meta_box(
					'footer_meta_box',
					__( 'Footer Parameters', BASEMENT_TEXTDOMAIN ),
					array( &$this, 'render_meta_box' ),
					$post_type,
					'advanced',
					'low'
				);

				add_filter( 'postbox_classes_' . $post_type . '_' . 'footer_meta_box', array(
					&$this,
					'class_meta_box'
				) );
			}
		}
	}


	/**
	 * Change meta box after load
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
	 * Render Meta Box Parameters
	 */
	public function render_meta_box( $post ) {
		$view = new Basement_Plugin();
		$view->basement_views( $this->theme_settings_meta_box(), array( 'footer-param-meta-box' ) );
	}



	/**
	 * Settings for footer meta box
	 *
	 * @return array
	 */
	public function theme_settings_meta_box() {
		$settings_config = array();

		foreach ( $this->options_blocks as $key => $value ) {
			if (
				$value['input'] === 'page' ||
				$value['input'] === 'blog' ||
				$value['input'] === 'woo'
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
			'page_footer'      => BASEMENT_TEXTDOMAIN . '_page_footer',
			'page_footer_line' => BASEMENT_TEXTDOMAIN . '_page_footer_line',
			'page_footer_area' => BASEMENT_TEXTDOMAIN . '_page_footer_area',
			'page_footer_style' => BASEMENT_TEXTDOMAIN . '_page_footer_style',
			'page_footer_sticky' => BASEMENT_TEXTDOMAIN . '_page_footer_sticky',

			'blog_footer'      => BASEMENT_TEXTDOMAIN . '_blog_footer',
			'blog_footer_line' => BASEMENT_TEXTDOMAIN . '_blog_footer_line',
			'blog_footer_area' => BASEMENT_TEXTDOMAIN . '_blog_footer_area',
			'blog_footer_style' => BASEMENT_TEXTDOMAIN . '_blog_footer_style',
			'blog_footer_sticky' => BASEMENT_TEXTDOMAIN . '_blog_footer_sticky',

			'woo_footer'      => BASEMENT_TEXTDOMAIN . '_woo_footer',
			'woo_footer_line' => BASEMENT_TEXTDOMAIN . '_woo_footer_line',
			'woo_footer_area' => BASEMENT_TEXTDOMAIN . '_woo_footer_area',
			'woo_footer_style' => BASEMENT_TEXTDOMAIN . '_woo_footer_style',
			'woo_footer_sticky' => BASEMENT_TEXTDOMAIN . '_woo_footer_sticky',

			'single_footer'     => BASEMENT_TEXTDOMAIN . '_single_footer',
			'single_footer_line' => BASEMENT_TEXTDOMAIN . '_single_footer_line',
			'single_footer_area' => BASEMENT_TEXTDOMAIN . '_single_footer_area',
			'single_footer_style' => BASEMENT_TEXTDOMAIN . '_single_footer_style',
			'single_footer_sticky' => BASEMENT_TEXTDOMAIN . '_single_footer_sticky'
		);
	}


	/**
	 * Set name for block in Footer settings
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
				'title'       => __( 'Footer for pages', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets settings for the footer on pages.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'page'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Footer for blog', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets settings for the footer on blog.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'blog'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Footer for WooCommerce pages', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets settings for the footer on <a href="https://wordpress.org/plugins/woocommerce/" target="_blank" style="text-decoration: none;">WooCommerce</a> pages.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'woo'
			),
			array(
				'type'        => 'dom',
				'title'       => __('Footer', BASEMENT_TEXTDOMAIN),
				'description' => __('Sets custom settings for footer.', BASEMENT_TEXTDOMAIN),
				'input'       => 'single'
			)
		);
	}


	/**
	 * Set block for Sidebar settings
	 */
	private function set_options_blocks() {
		$this->options_blocks = $this->options_blocks();
	}



	/**
	 * Settings fo single page
	 *
	 * @param string $type
	 *
	 * @return DOMNode|string
	 */
	public function single( $type = '' ) {
		global $post;
		global $wp_registered_sidebars;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		/*-- First param --*/

		// Init params
		$footer_params        = array(
			'values' => array(
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN ),
				'no'  => __( 'No', BASEMENT_TEXTDOMAIN )
			)
		);

		// Default value (before save)
		$footer_current_param = 'yes';

		// Get setting name
		$footer_setting = 'single_footer';


		if ( $type === 'metabox' ) {
			$footer_option                  = $this->meta_tag . substr( $this->options[$footer_setting], 18 );
			$footer_post_value              = get_post_meta( $post->ID, $footer_option, true );
			$footer_params['current_value'] = empty( $footer_post_value ) ? $footer_current_param : $footer_post_value;
		} else {
			$footer_option                  = $this->options[$footer_setting];
			$footer_option_value            = get_option( $footer_option, $footer_current_param );
			$footer_params['current_value'] = $footer_option_value;
		}

		$footer_params['id']   = $footer_option;
		$footer_params['name'] = $footer_option;
		$footer_params['label_text'] = __('Show footer?', BASEMENT_TEXTDOMAIN);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $footer_params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		/*-- Third param --*/

		$div_2  = $container->appendChild($dom->createElement('div'));
		$div_2->setAttribute('style','height:20px;');

		// Init params
		$params_3 = array(
			'values' => array(
				'no' => __( 'No', BASEMENT_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_3 = 'no';

		// Get setting name
		$setting_3 = 'single_footer_line';


		if ( $type === 'metabox' ) {
			$option_3                  = $this->meta_tag . substr( $this->options[$setting_3], 18 );
			$post_value_3              = get_post_meta( $post->ID, $option_3, true );
			$params_3['current_value'] = empty( $post_value_3 ) ? $current_param_3 : $post_value_3;
		} else {
			$option_3                  = $this->options[$setting_3];
			$option_value_3            = get_option( $option_3, $current_param_3 );
			$params_3['current_value'] = $option_value_3;
		}

		$params_3['id']   = $option_3;
		$params_3['name'] = $option_3;
		$params_3['label_text'] = __('Horizontal lines', BASEMENT_TEXTDOMAIN);


		$input_3 = new Basement_Form_Input_Radio_Group( $params_3 );

		$container->appendChild( $dom->importNode( $input_3->create(), true ) );

		$container->appendChild($dom->createElement('small','Shows or hides the \'Horizontal separator\' widget'));

		/*-- Fourth param --*/
		if ( ! empty( $wp_registered_sidebars ) ) {

			$div_3  = $container->appendChild($dom->createElement('div'));
			$div_3->setAttribute('style','height:20px;');

			$select_div = $container->appendChild( $dom->createElement( 'div' ) );
			$select_div->setAttribute('class','basement_block-selecter');

			$footers = array();

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$footers[$value['id']] = $value['name'];
			}

			$footers_params = array( 'values' => $footers );


			$current_param_4 = $this->get_first_sidebar();

			// Get setting name
			$setting_4 = 'single_footer_area';

			if ( $type === 'metabox' ) {
				$option_4 = $this->meta_tag . substr( $this->options[$setting_4], 18 );
				$post_value_4 = get_post_meta( $post->ID, $option_4, true );
				$footers_params['current_value'] = empty( $post_value_4 ) ? $current_param_4 : $post_value_4;
			} else {
				$option_4 = $this->options[$setting_4];
				$option_value_3 = get_option( $option_4, $current_param_4 );
				$footers_params['current_value'] = $option_value_3;
			}

			$footers_params['id']   = $option_4;
			$footers_params['name'] = $option_4;
			$footers_params['label_text'] = __('Select the widget area:', BASEMENT_TEXTDOMAIN);

			$select = new Basement_Form_Input_Select( $footers_params );

			$select_div->appendChild( $dom->importNode( $select->create(), true ) );
		}


		/*-- Fifth param --*/
		$div_4  = $container->appendChild($dom->createElement('div'));
		$div_4->setAttribute('style','height:20px;');

		$select_vis = $container->appendChild( $dom->createElement( 'div' ) );
		$select_vis->setAttribute('class','basement_block-toggler');

		$vis_params =  array(
			'values' => array(
				'dark' => __('Dark',BASEMENT_TEXTDOMAIN),
				'light' => __('Light',BASEMENT_TEXTDOMAIN)
			)
		);
		$current_param_vis = 'dark';

		// Get setting name
		$setting_vis = 'single_footer_style';



		if ( $type === 'metabox' ) {
			$option_vis = $this->meta_tag . substr( $this->options[$setting_vis], 18 );
			$post_value_vis = get_post_meta( $post->ID, $option_vis, true );
			$vis_params['current_value'] = empty( $post_value_vis ) ? $current_param_vis : $post_value_vis;
		} else {
			$option_vis = $this->options[$setting_vis];
			$post_value_vis = get_option( $option_vis, $current_param_vis );
			$vis_params['current_value'] = $post_value_vis;
		}

		$vis_params['id']   = $option_vis;
		$vis_params['name'] = $option_vis;
		$vis_params['label_text'] = __('Style:', BASEMENT_TEXTDOMAIN);

		$select_visibility = new Basement_Form_Input_Radio_Group( $vis_params );

		$select_vis->appendChild( $dom->importNode( $select_visibility->create(), true ) );




		/*-- Six param STICKY FOOTER --*/
		$div_separator  = $container->appendChild($dom->createElement('div'));
		$div_separator->setAttribute('style','height:20px;');

		$div_sticky = $container->appendChild( $dom->createElement( 'div' ) );
		$div_sticky->setAttribute('class','basement_block-toggler');

		$sticky_params = array(
			'values'        => array(
				'disable' => __( 'Disable', BASEMENT_TEXTDOMAIN ),
				'enable'  => __( 'Enable', BASEMENT_TEXTDOMAIN )
			)
		);

		$current_sticky_value = 'enable';

		// Get setting name
		$name_sticky_setting = 'single_footer_sticky';


		if ( $type === 'metabox' ) {
			$option_sticky = $this->meta_tag . substr( $this->options[$name_sticky_setting], 18 );
			$post_value_sticky = get_post_meta( $post->ID, $option_sticky, true );
			$sticky_params['current_value'] = empty( $post_value_sticky ) ? $current_sticky_value : $post_value_sticky;
		} else {
			$option_sticky = $this->options[$name_sticky_setting];
			$global_value_sticky = get_option( $option_sticky, $current_sticky_value );
			$sticky_params['current_value'] = $global_value_sticky;
		}


		$sticky_params['id']   = $option_sticky;
		$sticky_params['name'] = $option_sticky;
		$sticky_params['label_text'] = __('Sticky footer:', BASEMENT_TEXTDOMAIN);

		$select_sticky = new Basement_Form_Input_Radio_Group( $sticky_params );

		$div_sticky->appendChild( $dom->importNode( $select_sticky->create(), true ) );




		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Show/Hide footer on pages
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function page( $type = '' ) {
		global $post;
		global $wp_registered_sidebars;

		$dom = new DOMDocument( '1.0', 'UTF-8' );


		/*-- First param --*/

		// Init params
		$params        = array(
			'values' => array(
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN ),
				'no'  => __( 'No', BASEMENT_TEXTDOMAIN )
			)
		);

		// Default value (before save)
		$current_param = 'yes';

		// Get setting name
		$setting = 'page_footer';

		if ( $type === 'metabox' ) {
			$option                  = $this->meta_tag . substr( $this->options[$setting], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options[$setting];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;
		$params['label_text'] = __('Show footer?', BASEMENT_TEXTDOMAIN);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );


		/*-- Third param --*/

		$div_2  = $container->appendChild($dom->createElement('div'));
		$div_2->setAttribute('style','height:20px;');

		$params_3 = array(
			'values' => array(
				'no' => __( 'No', BASEMENT_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_3 = 'yes';

		// Get setting name
		$setting_3 = 'page_footer_line';


		if ( $type === 'metabox' ) {
			$option_3                  = $this->meta_tag . substr( $this->options[$setting_3], 18 );
			$post_value_3              = get_post_meta( $post->ID, $option_3, true );
			$params_3['current_value'] = empty( $post_value_3 ) ? $current_param_3 : $post_value_3;
		} else {
			$option_3                  = $this->options[$setting_3];
			$option_value_3            = get_option( $option_3, $current_param_3 );
			$params_3['current_value'] = $option_value_3;
		}

		$params_3['id']   = $option_3;
		$params_3['name'] = $option_3;
		$params_3['label_text'] = __('Horizontal lines:', BASEMENT_TEXTDOMAIN);



		$input_3 = new Basement_Form_Input_Radio_Group( $params_3 );

		$container->appendChild( $dom->importNode( $input_3->create(), true ) );

		$container->appendChild($dom->createElement('small','Shows or hides the \'Horizontal separator\' widget'));

		/*-- Fourth param --*/
		if ( ! empty( $wp_registered_sidebars ) ) {

			$div_3  = $container->appendChild($dom->createElement('div'));
			$div_3->setAttribute('style','height:20px;');

			$select_div = $container->appendChild( $dom->createElement( 'div' ) );
			$select_div->setAttribute('class','basement_block-selecter');

			$footers = array();

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$footers[$value['id']] = $value['name'];
			}

			$footers_params = array( 'values' => $footers );


			$current_param_4 = $this->get_first_sidebar();

			// Get setting name
			$setting_4 = 'page_footer_area';

			if ( $type === 'metabox' ) {
				$option_4 = $this->meta_tag . substr( $this->options[$setting_4], 18 );
				$post_value_4 = get_post_meta( $post->ID, $option_4, true );
				$footers_params['current_value'] = empty( $post_value_4 ) ? $current_param_4 : $post_value_4;
			} else {
				$option_4 = $this->options[$setting_4];

				$option_value_3 = get_option( $option_4, $current_param_4 );

				$footers_params['current_value'] = $option_value_3;
			}

			$footers_params['id']   = $option_4;
			$footers_params['name'] = $option_4;
			$footers_params['label_text'] = __('Select the widget area:', BASEMENT_TEXTDOMAIN);

			$select = new Basement_Form_Input_Select( $footers_params );

			$select_div->appendChild( $dom->importNode( $select->create(), true ) );
		}


		/*-- Fifth param --*/
		$div_4  = $container->appendChild($dom->createElement('div'));
		$div_4->setAttribute('style','height:20px;');

		$select_vis = $container->appendChild( $dom->createElement( 'div' ) );
		$select_vis->setAttribute('class','basement_block-toggler');

		$vis_params =  array(
			'values' => array(
				'dark' => __('Dark',BASEMENT_TEXTDOMAIN),
				'light' => __('Light',BASEMENT_TEXTDOMAIN)
			)
		);
		$current_param_vis = 'dark';

		// Get setting name
		$setting_vis = 'page_footer_style';



		if ( $type === 'metabox' ) {
			$option_vis = $this->meta_tag . substr( $this->options[$setting_vis], 18 );
			$post_value_vis = get_post_meta( $post->ID, $option_vis, true );
			$vis_params['current_value'] = empty( $post_value_vis ) ? $current_param_vis : $post_value_vis;
		} else {
			$option_vis = $this->options[$setting_vis];
			$post_value_vis = get_option( $option_vis, $current_param_vis );
			$vis_params['current_value'] = $post_value_vis;
		}

		$vis_params['id']   = $option_vis;
		$vis_params['name'] = $option_vis;
		$vis_params['label_text'] = __('Style:', BASEMENT_TEXTDOMAIN);

		$select_visibility = new Basement_Form_Input_Radio_Group( $vis_params );

		$select_vis->appendChild( $dom->importNode( $select_visibility->create(), true ) );


		/*-- Six param STICKY FOOTER --*/
		$div_separator  = $container->appendChild($dom->createElement('div'));
		$div_separator->setAttribute('style','height:20px;');

		$div_sticky = $container->appendChild( $dom->createElement( 'div' ) );
		$div_sticky->setAttribute('class','basement_block-toggler');

		$sticky_params = array(
			'values'        => array(
				'disable' => __( 'Disable', BASEMENT_TEXTDOMAIN ),
				'enable'  => __( 'Enable', BASEMENT_TEXTDOMAIN )
			)
		);

		$current_sticky_value = 'enable';

		// Get setting name
		$name_sticky_setting = 'page_footer_sticky';


		if ( $type === 'metabox' ) {
			$option_sticky = $this->meta_tag . substr( $this->options[$name_sticky_setting], 18 );
			$post_value_sticky = get_post_meta( $post->ID, $option_sticky, true );
			$sticky_params['current_value'] = empty( $post_value_sticky ) ? $current_sticky_value : $post_value_sticky;
		} else {
			$option_sticky = $this->options[$name_sticky_setting];
			$global_value_sticky = get_option( $option_sticky, $current_sticky_value );
			$sticky_params['current_value'] = $global_value_sticky;
		}


		$sticky_params['id']   = $option_sticky;
		$sticky_params['name'] = $option_sticky;
		$sticky_params['label_text'] = __('Sticky footer:', BASEMENT_TEXTDOMAIN);

		$select_sticky = new Basement_Form_Input_Radio_Group( $sticky_params );

		$div_sticky->appendChild( $dom->importNode( $select_sticky->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Show/Hide footer on blog
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function blog( $type = '' ) {
		global $post;
		global $wp_registered_sidebars;

		$dom = new DOMDocument( '1.0', 'UTF-8' );


		/*-- First param --*/

		// Init params
		$params        = array(
			'values' => array(
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN ),
				'no'  => __( 'No', BASEMENT_TEXTDOMAIN )
			)
		);

		// Default value (before save)
		$current_param = 'yes';

		// Get setting name
		$setting = 'blog_footer';

		if ( $type === 'metabox' ) {
			$option                  = $this->meta_tag . substr( $this->options[$setting], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options[$setting];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;
		$params['label_text'] = __('Show footer?', BASEMENT_TEXTDOMAIN);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );


		/*-- Third param --*/

		$div_2  = $container->appendChild($dom->createElement('div'));
		$div_2->setAttribute('style','height:20px;');

		$params_3 = array(
			'values' => array(
				'no' => __( 'No', BASEMENT_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_3 = 'no';

		// Get setting name
		$setting_3 = 'blog_footer_line';


		if ( $type === 'metabox' ) {
			$option_3                  = $this->meta_tag . substr( $this->options[$setting_3], 18 );
			$post_value_3              = get_post_meta( $post->ID, $option_3, true );
			$params_3['current_value'] = empty( $post_value_3 ) ? $current_param_3 : $post_value_3;
		} else {
			$option_3                  = $this->options[$setting_3];
			$option_value_3  = get_option( $option_3, $current_param_3 );

			$params_3['current_value'] = $option_value_3;
		}

		$params_3['id']   = $option_3;
		$params_3['name'] = $option_3;
		$params_3['label_text'] = __('Horizontal lines:', BASEMENT_TEXTDOMAIN);


		$input_3 = new Basement_Form_Input_Radio_Group( $params_3 );

		$container->appendChild( $dom->importNode( $input_3->create(), true ) );

		$container->appendChild($dom->createElement('small','Shows or hides the \'Horizontal separator\' widget'));

		/*-- Fourth param --*/
		if ( ! empty( $wp_registered_sidebars ) ) {

			$div_3  = $container->appendChild($dom->createElement('div'));
			$div_3->setAttribute('style','height:20px;');

			$select_div = $container->appendChild( $dom->createElement( 'div' ) );
			$select_div->setAttribute('class','basement_block-selecter');

			$footers = array();

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$footers[$value['id']] = $value['name'];
			}

			$footers_params = array( 'values' => $footers );


			$current_param_4 = $this->get_first_sidebar();

			// Get setting name
			$setting_4 = 'blog_footer_area';

			if ( $type === 'metabox' ) {
				$option_4 = $this->meta_tag . substr( $this->options[$setting_4], 18 );
				$post_value_4 = get_post_meta( $post->ID, $option_4, true );
				$footers_params['current_value'] = empty( $post_value_4 ) ? $current_param_4 : $post_value_4;
			} else {
				$option_4 = $this->options[$setting_4];
				$option_value_3 = get_option( $option_4, apply_filters('basement_default_blog_widgets_footer','sidebar-11') );

				$footers_params['current_value'] = $option_value_3;
			}

			$footers_params['id']   = $option_4;
			$footers_params['name'] = $option_4;
			$footers_params['label_text'] = __('Select the widget area:', BASEMENT_TEXTDOMAIN);

			$select = new Basement_Form_Input_Select( $footers_params );

			$select_div->appendChild( $dom->importNode( $select->create(), true ) );
		}


		/*-- Fifth param --*/
		$div_4  = $container->appendChild($dom->createElement('div'));
		$div_4->setAttribute('style','height:20px;');

		$select_vis = $container->appendChild( $dom->createElement( 'div' ) );
		$select_vis->setAttribute('class','basement_block-toggler');

		$vis_params =  array(
			'values' => array(
				'dark' => __('Dark',BASEMENT_TEXTDOMAIN),
				'light' => __('Light',BASEMENT_TEXTDOMAIN)
			)
		);
		$current_param_vis = 'dark';

		// Get setting name
		$setting_vis = 'blog_footer_style';



		if ( $type === 'metabox' ) {
			$option_vis = $this->meta_tag . substr( $this->options[$setting_vis], 18 );
			$post_value_vis = get_post_meta( $post->ID, $option_vis, true );
			$vis_params['current_value'] = empty( $post_value_vis ) ? $current_param_vis : $post_value_vis;
		} else {
			$option_vis = $this->options[$setting_vis];
			$post_value_vis = get_option( $option_vis, $current_param_vis );
			$vis_params['current_value'] = $post_value_vis;
		}

		$vis_params['id']   = $option_vis;
		$vis_params['name'] = $option_vis;
		$vis_params['label_text'] = __('Style:', BASEMENT_TEXTDOMAIN);

		$select_visibility = new Basement_Form_Input_Radio_Group( $vis_params );

		$select_vis->appendChild( $dom->importNode( $select_visibility->create(), true ) );


		/*-- Six param STICKY FOOTER --*/
		$div_separators  = $container->appendChild($dom->createElement('div'));
		$div_separators->setAttribute('style','height:20px;');

		$div_stickys = $container->appendChild( $dom->createElement( 'div' ) );
		$div_stickys->setAttribute('class','basement_block-toggler');

		$sticky_paramss = array(
			'values'        => array(
				'disable' => __( 'Disable', BASEMENT_TEXTDOMAIN ),
				'enable'  => __( 'Enable', BASEMENT_TEXTDOMAIN )
			)
		);

		$current_sticky_values = 'enable';

		// Get setting name
		$name_sticky_settings = 'blog_footer_sticky';


		if ( $type === 'metabox' ) {
			$option_stickys = $this->meta_tag . substr( $this->options[$name_sticky_settings], 18 );
			$post_value_stickys = get_post_meta( $post->ID, $option_stickys, true );
			$sticky_paramss['current_value'] = empty( $post_value_stickys ) ? $current_sticky_values : $post_value_stickys;
		} else {
			$option_stickys = $this->options[$name_sticky_settings];
			$global_value_stickys = get_option( $option_stickys, $current_sticky_values );
			$sticky_paramss['current_value'] = empty($global_value_stickys) ? $current_sticky_values : $global_value_stickys;
		}


		$sticky_paramss['id']   = $option_stickys;
		$sticky_paramss['name'] = $option_stickys;
		$sticky_paramss['label_text'] = __('Sticky footer:', BASEMENT_TEXTDOMAIN);

		$select_stickys = new Basement_Form_Input_Radio_Group( $sticky_paramss );

		$div_stickys->appendChild( $dom->importNode( $select_stickys->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Show/Hide footer on WooCommerce
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function woo( $type = '' ) {
		global $post;
		global $wp_registered_sidebars;

		$dom = new DOMDocument( '1.0', 'UTF-8' );


		/*-- First param --*/

		// Init params
		$params        = array(
			'values' => array(
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN ),
				'no'  => __( 'No', BASEMENT_TEXTDOMAIN )
			)
		);

		// Default value (before save)
		$current_param = 'yes';

		// Get setting name
		$setting = 'woo_footer';

		if ( $type === 'metabox' ) {
			$option                  = $this->meta_tag . substr( $this->options[$setting], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options[$setting];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;
		$params['label_text'] = __('Show footer?', BASEMENT_TEXTDOMAIN);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		/*-- Third param --*/

		$div_2  = $container->appendChild($dom->createElement('div'));
		$div_2->setAttribute('style','height:20px;');

		$params_3 = array(
			'values' => array(
				'no' => __( 'No', BASEMENT_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_3 = 'no';

		// Get setting name
		$setting_3 = 'woo_footer_line';


		if ( $type === 'metabox' ) {
			$option_3                  = $this->meta_tag . substr( $this->options[$setting_3], 18 );
			$post_value_3              = get_post_meta( $post->ID, $option_3, true );
			$params_3['current_value'] = empty( $post_value_3 ) ? $current_param_3 : $post_value_3;
		} else {
			$option_3                  = $this->options[$setting_3];
			$option_value_3            = get_option( $option_3, $current_param_3 );
			$params_3['current_value'] = $option_value_3;
		}

		$params_3['id']   = $option_3;
		$params_3['name'] = $option_3;
		$params_3['label_text'] = __('Horizontal lines', BASEMENT_TEXTDOMAIN);


		$input_3 = new Basement_Form_Input_Radio_Group( $params_3 );

		$container->appendChild( $dom->importNode( $input_3->create(), true ) );

		$container->appendChild($dom->createElement('small','Shows or hides the \'Horizontal separator\' widget'));

		/*-- Fourth param --*/
		if ( ! empty( $wp_registered_sidebars ) ) {

			$div_3  = $container->appendChild($dom->createElement('div'));
			$div_3->setAttribute('style','height:20px;');

			$select_div = $container->appendChild( $dom->createElement( 'div' ) );
			$select_div->setAttribute('class','basement_block-selecter');

			$footers = array();

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$footers[$value['id']] = $value['name'];
			}

			$footers_params = array( 'values' => $footers );


			$current_param_4 = $this->get_first_sidebar();

			// Get setting name
			$setting_4 = 'woo_footer_area';

			if ( $type === 'metabox' ) {
				$option_4 = $this->meta_tag . substr( $this->options[$setting_4], 18 );
				$post_value_4 = get_post_meta( $post->ID, $option_4, true );
				$footers_params['current_value'] = empty( $post_value_4 ) ? $current_param_4 : $post_value_4;
			} else {
				$option_4 = $this->options[$setting_4];
				$option_value_3 = get_option( $option_4, $current_param_4 );
				$footers_params['current_value'] = $option_value_3;
			}

			$footers_params['id']   = $option_4;
			$footers_params['name'] = $option_4;
			$footers_params['label_text'] = __('Select the widget area', BASEMENT_TEXTDOMAIN);

			$select = new Basement_Form_Input_Select( $footers_params );

			$select_div->appendChild( $dom->importNode( $select->create(), true ) );
		}



		/*-- Fifth param --*/
		$div_4  = $container->appendChild($dom->createElement('div'));
		$div_4->setAttribute('style','height:20px;');

		$select_vis = $container->appendChild( $dom->createElement( 'div' ) );
		$select_vis->setAttribute('class','basement_block-toggler');

		$vis_params =  array(
			'values' => array(
				'dark' => __('Dark',BASEMENT_TEXTDOMAIN),
				'light' => __('Light',BASEMENT_TEXTDOMAIN),
			)
		);
		$current_param_vis = 'dark';

		// Get setting name
		$setting_vis = 'woo_footer_style';



		if ( $type === 'metabox' ) {
			$option_vis = $this->meta_tag . substr( $this->options[$setting_vis], 18 );
			$post_value_vis = get_post_meta( $post->ID, $option_vis, true );
			$vis_params['current_value'] = empty( $post_value_vis ) ? $current_param_vis : $post_value_vis;
		} else {
			$option_vis = $this->options[$setting_vis];
			$post_value_vis = get_option( $option_vis, $current_param_vis );
			$vis_params['current_value'] = $post_value_vis;
		}

		$vis_params['id']   = $option_vis;
		$vis_params['name'] = $option_vis;
		$vis_params['label_text'] = __('Style:', BASEMENT_TEXTDOMAIN);

		$select_visibility = new Basement_Form_Input_Radio_Group( $vis_params );

		$select_vis->appendChild( $dom->importNode( $select_visibility->create(), true ) );



		/*-- Six param STICKY FOOTER --*/
		$div_separators  = $container->appendChild($dom->createElement('div'));
		$div_separators->setAttribute('style','height:20px;');

		$div_stickys = $container->appendChild( $dom->createElement( 'div' ) );
		$div_stickys->setAttribute('class','basement_block-toggler');

		$sticky_paramss = array(
			'values'        => array(
				'disable' => __( 'Disable', BASEMENT_TEXTDOMAIN ),
				'enable'  => __( 'Enable', BASEMENT_TEXTDOMAIN )
			)
		);

		$current_sticky_values = 'enable';

		// Get setting name
		$name_sticky_settings = 'woo_footer_sticky';


		if ( $type === 'metabox' ) {
			$option_stickys = $this->meta_tag . substr( $this->options[$name_sticky_settings], 18 );
			$post_value_stickys = get_post_meta( $post->ID, $option_stickys, true );
			$sticky_paramss['current_value'] = empty( $post_value_stickys ) ? $current_sticky_values : $post_value_stickys;
		} else {
			$option_stickys = $this->options[$name_sticky_settings];
			$global_value_stickys = get_option( $option_stickys, $current_sticky_values );
			$sticky_paramss['current_value'] = empty($global_value_stickys) ? $current_sticky_values : $global_value_stickys;
		}


		$sticky_paramss['id']   = $option_stickys;
		$sticky_paramss['name'] = $option_stickys;
		$sticky_paramss['label_text'] = __('Sticky footer:', BASEMENT_TEXTDOMAIN);

		$select_stickys = new Basement_Form_Input_Radio_Group( $sticky_paramss );

		$div_stickys->appendChild( $dom->importNode( $select_stickys->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Get all options
	 *
	 * @return array
	 */
	public function get_footer_settings() {
		$id = get_the_ID();
		$names = $this->options_names();


		if ( is_404() ) {
			$id = $this->get_id_404_template_page();
		}

		if ( ! is_home() && ! is_search() && ! is_archive() && ! is_attachment() ) {
			$custom_footer = get_post_meta( $id, '_basement_meta_custom_footer', true );
		} else {
			$custom_footer = '';
		}



		$settings = array();

		foreach ( $names as $key => $value ) {
			if ( ! empty( $custom_footer ) ) {
				$option     = $this->meta_tag . substr( $value, 18 );
				$post_value = get_post_meta( $id, $option, true );

				if ( !empty( $post_value ) ) {
					$settings[ $key ] = $post_value;
				}

			} else {
				if (
					$key === 'single_footer' ||
					$key === 'single_footer_line' ||
					$key === 'single_footer_area' ||
					$key === 'single_footer_style' ||
					$key === 'single_footer_sticky'
				)
					continue;
				$settings[ $key ] = get_option( $value );
			}

		}

		if ( ! empty( $custom_footer ) ) {
			$settings['scope'] = 'private';
		} else {
			$settings['scope'] = 'public';
		}


		return apply_filters( 'basement_footer_settings', $settings );
	}


	/**
	 * Return first registered sidebar
	 *
	 * @return mixed
	 */
	public function get_first_sidebar() {
		global $wp_registered_sidebars;

		$sidebars = array();

		if ( ! empty( $wp_registered_sidebars ) ) {
			foreach ( $wp_registered_sidebars as $key => $value ) {
				$sidebars[] = $value['id'];
			}
		}

		return array_shift($sidebars);
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
	 * Generate Setting For Footer
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	public function front_get_settings_options($type = 'page') {
		global $wp_registered_sidebars;

		$id = get_the_ID();

		$settings = array();
		if ( ! empty( $wp_registered_sidebars ) ) {

			$footer  = new Basement_Footer();
			$settings = $footer->get_footer_settings();

			if ( ! empty( $settings ) ) {
				extract( $settings );


				$template_name = get_page_template_slug( get_queried_object_id() );

				if ( is_home() || is_singular('post') || is_archive() || strpos( $template_name, 'page-blog' ) !== false ) {
					$type = 'blog';
				}

				$wp_type = get_post_type();

				if ( $scope !== 'private' ) {
					if ( $wp_type === $type || $type === 'page' ) {
						$single_footer       = ! empty( $page_footer ) ? $page_footer : 'yes';
						$single_footer_line  = ! empty( $page_footer_line ) ? $page_footer_line : 'no';
						$single_footer_area  = ! empty( $page_footer_area ) ? $page_footer_area : $footer->get_first_sidebar();
						$single_footer_style = ! empty( $page_footer_style ) ? $page_footer_style : 'dark';
						$single_footer_sticky = ! empty( $page_footer_sticky ) ? $page_footer_sticky : 'enable';
					} elseif ( $wp_type === $type || $type === 'blog' ) {
						$single_footer       = ! empty( $blog_footer ) ? $blog_footer : 'yes';
						$single_footer_line  = ! empty( $blog_footer_line ) ? $blog_footer_line : 'no';
						$single_footer_area  = ! empty( $blog_footer_area ) ? $blog_footer_area : $footer->get_first_sidebar();
						$single_footer_style = ! empty( $blog_footer_style ) ? $blog_footer_style : 'dark';
						$single_footer_sticky = ! empty( $blog_footer_sticky ) ? $blog_footer_sticky : 'enable';
					} elseif ( $wp_type === $type || $type === 'woo' ) {
						$single_footer       = ! empty( $woo_footer ) ? $woo_footer : 'yes';
						$single_footer_line  = ! empty( $woo_footer_line ) ? $woo_footer_line : 'no';
						$single_footer_area  = ! empty( $woo_footer_area ) ? $woo_footer_area : $footer->get_first_sidebar();
						$single_footer_style = ! empty( $woo_footer_style ) ? $woo_footer_style : 'dark';
						$single_footer_sticky = ! empty( $woo_footer_sticky ) ? $woo_footer_sticky : 'enable';
					}
				}


				$settings = array(
					'footer_type' => $type,
					'footer' => $single_footer,
					'footer_line' => $single_footer_line,
					'footer_area' => $single_footer_area,
					'footer_style' => $single_footer_style,
					'footer_sticky' => $single_footer_sticky,
					'scope' => $scope
				);

			}
		}

		return $settings;
	}

	public function front_classes_footer() {
		$names   = apply_filters( 'basement_footer_classes', $this->front_get_settings_options() );
		$classes = array();
		foreach ( $names as $key => $value ) {
			$classes[] = $key . '_' . sanitize_html_class(   $value );
		}

		return implode( ' ', apply_filters( 'basement_footer_classes_format', $classes ) );
	}

}


if ( ! function_exists( 'basement_footer_class' ) ) {
	/**
	 * Display the Classes For the Footer Element.
	 *
	 * @param $class
	 */
	function basement_footer_class( $class = '' ) {
		// Separates classes with a single space, collates classes for Footer element
		echo 'class="' . implode( ' ', basement_get_footer_class( $class ) ) . '"';
	}
}


if ( ! function_exists( 'basement_get_footer_class' ) ) {
	/**
	 * Retrieve the Classes For the Footer Element as an Array.
	 *
	 * @param string $class
	 *
	 * @return array
	 */
	function basement_get_footer_class( $class = '' ) {

		$classes = array();


		$basement_footer = new Basement_Footer();
		$classes[]       = $basement_footer->front_classes_footer();

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

		$classes = apply_filters( 'footer_class', $classes, $class );

		return array_unique( $classes );
	}
}


if ( ! function_exists( 'basement_footer_sort_elements' ) ) {
	/**
	 * Output Sidebar In Footer
	 *
	 * @param string $file
	 */
	function basement_footer_sort_elements( $file ) {
		global $wp_registered_sidebars, $basement_footer;

		if ( empty( $file ) ) {
			$file = 'footer';
		}


		if ( ! empty( $wp_registered_sidebars ) ) {
			$footer_obj = new Basement_Footer();
			$settings = $footer_obj->front_get_settings_options();

			if ( ! empty( $settings ) ) {
				extract( $settings );


				if ( $footer === 'yes' && is_active_sidebar($footer_area) ) {
					$basement_footer = array(
						'display' => $footer,
						'line' => $footer_line,
						'sidebar' => $footer_area,
						'visibility' => $footer_style,
						'place' => 'footer'
					);

					get_sidebar($file);
				}

			}
		}
	}

	add_action( 'conico_content_footer', 'basement_footer_sort_elements', 10 );
}



if ( ! function_exists( 'basement_action_theme_before_footer_row' ) ) {
	/**
	 * Displays params before Footer Row
	 */
	function basement_action_theme_before_footer_row() {
		ob_start();
	}
	add_action('conico_before_footer_row', 'basement_action_theme_before_footer_row');
}


if ( ! function_exists( 'basement_action_theme_after_footer_row' ) ) {
	/**
	 * Displays params after Footer Row
	 */
	function basement_action_theme_after_footer_row() {
		$footer   = new Basement_Footer();
		$settings = $footer->front_get_settings_options();

		$footer = ob_get_contents();
		ob_end_clean();

		$footer_area = isset($settings['footer_area']) ? $settings['footer_area'] : '';
		$footer_enable = isset($settings['footer']) ? $settings['footer'] : '';




		if ( isset( $settings['footer'] ) && $footer_enable === 'yes' && is_active_sidebar($footer_area) ) {
			echo $footer;
		}
	}

	add_action( 'conico_after_footer_row', 'basement_action_theme_after_footer_row' );
}


if ( ! function_exists( 'basement_action_footer_body_class' ) ) {
	/**
	 * Added classes to <body> for Header
	 */
	function basement_action_footer_body_class($classes, $class) {
		$footer = new Basement_Footer();
		$settings = $footer->front_get_settings_options();

		$sticky = isset($settings['footer_sticky']) ? $settings['footer_sticky'] : '';

		if($sticky === 'enable') {
			$classes[] = 'is-fix-footer';
		}



		if(isset($settings['footer']) && $settings['footer'] === 'yes' ) {
			$classes[] = 'basement-footer-enable';
		} else {
			$classes[] = 'basement-footer-disable';
		}

		return $classes;
	}

	add_filter( 'body_class', 'basement_action_footer_body_class', 10, 2 );
}


if ( ! function_exists( 'Basement_Footer' ) ) {
	/**
	 * Generate Basement Footer Settings
	 *
	 * @return array
	 */
	function Basement_Footer() {
		$basement_footer = new Basement_Footer();
		return $basement_footer->get_footer_settings();
	}
}