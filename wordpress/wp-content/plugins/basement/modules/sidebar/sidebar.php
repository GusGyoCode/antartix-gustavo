<?php
defined( 'ABSPATH' ) or die();

$basement_sidebar = array();
$basement_content = array();

class Basement_Sidebar {

	private static $instance = null;

	// Name for Sidebar options
	private $options = array();

	// Block for Sidebar options (options & meta)
	private $options_blocks = array();

	// Start tag for meta name
	private $meta_tag = '_basement_meta';

	/**
	 * Basement_Sidebar constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'register_theme_settings' ) );
		}
	}

	/**
	 * If sidebar exist
	 *
	 * @return array
	 */
	public function check_sidebar() {
		global $wp_registered_sidebars;

		return $wp_registered_sidebars;
	}

	/**
	 * Basement_Sidebar init
	 *
	 * @return Basement_Sidebar|null
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Sidebar();
		}

		return self::$instance;
	}


	/**
	 * Register Meta Box
	 *
	 * @param $post_type
	 */
	public function generate_sidebar_meta_box( $post_type, $post ) {
		$post_ID = $post->ID;

		$woo_pages = Basement_Ecommerce_Woocommerce::woo_pages();

		if ( $post_ID != get_option( 'page_for_posts' ) && ! in_array( $post_ID, $woo_pages ) ) {
			if ( in_array( $post_type, array( 'page', 'post' ) ) ) {
				add_meta_box(
					'sidebar_meta_box',
					__( 'Sidebar Parameters', BASEMENT_TEXTDOMAIN ),
					array( &$this, 'render_sidebar_meta_box' ),
					$post_type,
					'side',
					'high'
				);

				add_filter( 'postbox_classes_' . $post_type . '_' . 'sidebar_meta_box', array(
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
	public function render_sidebar_meta_box( $post ) {
		$view = new Basement_Plugin();
		$view->basement_views( $this->theme_settings_meta_box(), array( 'sidebar-param-meta-box' ) );
	}



	/**
	 * Settings for sidebar meta box
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
			'page_sidebar'      => BASEMENT_TEXTDOMAIN . '_page_sidebar',
			'page_sidebar_dir'  => BASEMENT_TEXTDOMAIN . '_page_sidebar_dir',
			'page_sidebar_line' => BASEMENT_TEXTDOMAIN . '_page_sidebar_line',
			'page_sidebar_area' => BASEMENT_TEXTDOMAIN . '_page_sidebar_area',
			'page_sidebar_visibility' => BASEMENT_TEXTDOMAIN . '_page_sidebar_visibility',

			'blog_sidebar'      => BASEMENT_TEXTDOMAIN . '_blog_sidebar',
			'blog_sidebar_dir'  => BASEMENT_TEXTDOMAIN . '_blog_sidebar_dir',
			'blog_sidebar_line' => BASEMENT_TEXTDOMAIN . '_blog_sidebar_line',
			'blog_sidebar_area' => BASEMENT_TEXTDOMAIN . '_blog_sidebar_area',
			'blog_sidebar_visibility' => BASEMENT_TEXTDOMAIN . '_blog_sidebar_visibility',

			'woo_sidebar'      => BASEMENT_TEXTDOMAIN . '_woo_sidebar',
			'woo_sidebar_dir'  => BASEMENT_TEXTDOMAIN . '_woo_sidebar_dir',
			'woo_sidebar_line' => BASEMENT_TEXTDOMAIN . '_woo_sidebar_line',
			'woo_sidebar_area' => BASEMENT_TEXTDOMAIN . '_woo_sidebar_area',
			'woo_sidebar_visibility' => BASEMENT_TEXTDOMAIN . '_woo_sidebar_visibility',
			'woo_sidebar_bg' => BASEMENT_TEXTDOMAIN . '_woo_sidebar_bg',

			'single_sidebar'     => BASEMENT_TEXTDOMAIN . '_single_sidebar',
			'single_sidebar_dir' => BASEMENT_TEXTDOMAIN . '_single_sidebar_dir',
			'single_sidebar_line' => BASEMENT_TEXTDOMAIN . '_single_sidebar_line',
			'single_sidebar_area' => BASEMENT_TEXTDOMAIN . '_single_sidebar_area',
			'single_sidebar_visibility' => BASEMENT_TEXTDOMAIN . '_single_sidebar_visibility'
		);
	}


	/**
	 * Set name for block in Sidebar settings
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
				'title'       => __( 'Sidebar for pages', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets settings for the sidebar on pages.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'page'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Sidebar for blog', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets settings for the sidebar on blog.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'blog'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Sidebar for WooCommerce pages', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets settings for the sidebar on <a href="https://wordpress.org/plugins/woocommerce/" target="_blank" style="text-decoration: none;">WooCommerce</a> pages.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'woo'
			),
			array(
				'type'        => 'dom',
				'title'       => '',
				'description' => '',
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
	 * Register options for Sidebar
	 */
	public function register_theme_settings() {
		$sidebar_status = $this->check_sidebar();
		if ( empty( $sidebar_status ) )
			return;

		add_filter( BASEMENT_TEXTDOMAIN . '_theme_settings_config_filter', array(
			&$this,
			'theme_settings_config_filter'
		) );

		add_action( 'add_meta_boxes', array( &$this, 'generate_sidebar_meta_box' ), 10, 2 );


		$this->set_options_blocks();

		$this->set_options_names();

		foreach ( $this->options as $key => $value ) {
			if (
				$key === 'single_sidebar' ||
				$key === 'single_sidebar_dir' ||
				$key === 'single_sidebar_line' ||
			    $key === 'single_sidebar_area' ||
				$key === 'single_sidebar_visibility'
			)
				continue;
			register_setting( 'basement_theme_options', $value );
		}
	}


	/**
	 * Init sidebar params
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	public function theme_settings_config_filter( $config = array() ) {
		$settings_config = array(
			'sidebar' => array(
				'title'  => __( 'Sidebar', BASEMENT_TEXTDOMAIN ),
				'blocks' => array()
			)
		);

		foreach ( $this->options_blocks as $key => $value ) {
			if( $value['input'] === 'single' || ( !Basement_Ecommerce_Woocommerce::enabled() && $value['input'] === 'woo' ) )
				continue;
			$value['input'] = call_user_func( array( &$this, $value['input'] ) );
			$settings_config['sidebar']['blocks'][] = $value;
		}

		return array_slice( $config, 0, 2, true ) + $settings_config + array_slice( $config, 2, count( $config ) - 1, true );
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
		$sidebar_params        = array(
			'values' => array(
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN ),
				'no'  => __( 'No', BASEMENT_TEXTDOMAIN )
			)
		);

		// Default value (before save)
		$sidebar_current_param = 'yes';

		// Get setting name
		$sidebar_setting = 'single_sidebar';


		if ( $type === 'metabox' ) {
			$sidebar_option                  = $this->meta_tag . substr( $this->options[$sidebar_setting], 18 );
			$sidebar_post_value              = get_post_meta( $post->ID, $sidebar_option, true );
			$sidebar_params['current_value'] = empty( $sidebar_post_value ) ? $sidebar_current_param : $sidebar_post_value;
		} else {
			$sidebar_option                  = $this->options[$sidebar_setting];
			$sidebar_option_value            = get_option( $sidebar_option, $sidebar_current_param );
			$sidebar_params['current_value'] = $sidebar_option_value;
		}

		$sidebar_params['id']   = $sidebar_option;
		$sidebar_params['name'] = $sidebar_option;
		$sidebar_params['label_text'] = __('Show sidebar?', BASEMENT_TEXTDOMAIN);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $sidebar_params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		/*-- Second param --*/
		$div  = $container->appendChild($dom->createElement('div'));
		$div->setAttribute('style','height:20px;');


		// Init params
		$sidebar_dir_params        = array(
			'values' => array(
				'right' => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'left'  => __( 'Left', BASEMENT_TEXTDOMAIN )
			)
		);

		// Default value (before save)
		$sidebar_dir_current_param = 'right';

		// Get setting name
		$sidebar_dir_setting = 'single_sidebar_dir';


		if ( $type === 'metabox' ) {
			$sidebar_dir_option                  = $this->meta_tag . substr( $this->options[$sidebar_dir_setting], 18 );
			$sidebar_dir_post_value              = get_post_meta( $post->ID, $sidebar_dir_option, true );
			$sidebar_dir_params['current_value'] = empty( $sidebar_dir_post_value ) ? $sidebar_dir_current_param : $sidebar_dir_post_value;
		} else {
			$sidebar_dir_option                  = $this->options[$sidebar_dir_setting];
			$sidebar_dir_option_value            = get_option( $sidebar_dir_option, $sidebar_dir_current_param );
			$sidebar_dir_params['current_value'] = $sidebar_dir_option_value;
		}

		$sidebar_dir_params['id']   = $sidebar_dir_option;
		$sidebar_dir_params['name'] = $sidebar_dir_option;
		$sidebar_dir_params['label_text'] = __('Sidebar position', BASEMENT_TEXTDOMAIN);

		$input_2 = new Basement_Form_Input_Radio_Group( $sidebar_dir_params );

		$container->appendChild( $dom->importNode( $input_2->create(), true ) );


		/*-- Third param --*/

		$div_2  = $container->appendChild($dom->createElement('div'));
		$div_2->setAttribute('style','height:20px;');

		// Init params
		$params_3 = array(
			'values' => array(
				'no' => __( 'No', BASEMENT_TEXTDOMAIN ),
				'left' => __( 'Left', BASEMENT_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'left_right'  => __( 'Right & Left', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_3 = 'no';

		// Get setting name
		$setting_3 = 'single_sidebar_line';


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
		$params_3['label_text'] = __('Vertical line', BASEMENT_TEXTDOMAIN);


		$input_3 = new Basement_Form_Input_Radio_Group( $params_3 );

		$container->appendChild( $dom->importNode( $input_3->create(), true ) );


		/*-- Fourth param --*/
		if ( ! empty( $wp_registered_sidebars ) ) {

			$div_3  = $container->appendChild($dom->createElement('div'));
			$div_3->setAttribute('style','height:20px;');

			$select_div = $container->appendChild( $dom->createElement( 'div' ) );
			$select_div->setAttribute('class','basement_block-selecter');

			$sidebars = array();

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$sidebars[$value['id']] = $value['name'];
			}

			$sidebars_params = array( 'values' => $sidebars );


			$current_param_4 = $this->get_first_sidebar();

			// Get setting name
			$setting_4 = 'single_sidebar_area';

			if ( $type === 'metabox' ) {
				$option_4 = $this->meta_tag . substr( $this->options[$setting_4], 18 );
				$post_value_4 = get_post_meta( $post->ID, $option_4, true );
				$sidebars_params['current_value'] = empty( $post_value_4 ) ? $current_param_4 : $post_value_4;
			} else {
				$option_4 = $this->options[$setting_4];
				$option_value_3 = get_option( $option_4, $current_param_4 );
				$sidebars_params['current_value'] = $option_value_3;
			}

			$sidebars_params['id']   = $option_4;
			$sidebars_params['name'] = $option_4;
			$sidebars_params['label_text'] = __('Select the widget area', BASEMENT_TEXTDOMAIN);

			$select = new Basement_Form_Input_Select( $sidebars_params );

			$select_div->appendChild( $dom->importNode( $select->create(), true ) );
		}


		/*-- Fifth param --*/
		$div_4  = $container->appendChild($dom->createElement('div'));
		$div_4->setAttribute('style','height:20px;');

		$select_vis = $container->appendChild( $dom->createElement( 'div' ) );
		$select_vis->setAttribute('class','basement_block-selecter');

		$vis_params =  array(
			'values' => array(
				'all' => __('All devices',BASEMENT_TEXTDOMAIN),
				'hidden-xs hidden-sm hidden-md' => __('Desktops',BASEMENT_TEXTDOMAIN),
				'hidden-xs hidden-lg' => __('Tablets',BASEMENT_TEXTDOMAIN),
				'hidden-sm hidden-md hidden-lg' => __('Phones',BASEMENT_TEXTDOMAIN),
				'hidden-xs' => __('Desktops/Tablets',BASEMENT_TEXTDOMAIN),
				'hidden-lg' => __('Tablets/Phones',BASEMENT_TEXTDOMAIN),
				'hidden-sm hidden-md' => __('Desktops/Phones',BASEMENT_TEXTDOMAIN),
			)
		);
		$current_param_vis = 'all';

		// Get setting name
		$setting_vis = 'single_sidebar_visibility';



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
		$vis_params['label_text'] = __('Visible on:', BASEMENT_TEXTDOMAIN);

		$select_visibility = new Basement_Form_Input_Select( $vis_params );

		$select_vis->appendChild( $dom->importNode( $select_visibility->create(), true ) );



		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Show/Hide sidebar on pages
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
				'no'  => __( 'No', BASEMENT_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN )
			)
		);

		// Default value (before save)
		$current_param = 'no';

		// Get setting name
		$setting = 'page_sidebar';

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
		$params['label_text'] = __('Show sidebar?', BASEMENT_TEXTDOMAIN);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		/*-- Second param --*/

		$div  = $container->appendChild($dom->createElement('div'));
		$div->setAttribute('style','height:20px;');

		$params_2 = array(
			'values' => array(
				'right' => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'left'  => __( 'Left', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_2 = 'right';

		// Get setting name
		$setting_2 = 'page_sidebar_dir';


		if ( $type === 'metabox' ) {
			$option_2                  = $this->meta_tag . substr( $this->options[$setting_2], 18 );
			$post_value_2              = get_post_meta( $post->ID, $option_2, true );
			$params_2['current_value'] = empty( $post_value_2 ) ? $current_param_2 : $post_value_2;
		} else {
			$option_2                  = $this->options[$setting_2];
			$option_value_2            = get_option( $option_2, $current_param_2 );
			$params_2['current_value'] = $option_value_2;
		}

		$params_2['id']   = $option_2;
		$params_2['name'] = $option_2;
		$params_2['label_text'] = __('Sidebar position', BASEMENT_TEXTDOMAIN);


		$input_2 = new Basement_Form_Input_Radio_Group( $params_2 );

		$container->appendChild( $dom->importNode( $input_2->create(), true ) );


		/*-- Third param --*/

		$div_2  = $container->appendChild($dom->createElement('div'));
		$div_2->setAttribute('style','height:20px;');

		$params_3 = array(
			'values' => array(
				'no' => __( 'No', BASEMENT_TEXTDOMAIN ),
				'left' => __( 'Left', BASEMENT_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'left_right'  => __( 'Right & Left', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_3 = 'no';

		// Get setting name
		$setting_3 = 'page_sidebar_line';


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
		$params_3['label_text'] = __('Vertical line', BASEMENT_TEXTDOMAIN);


		$input_3 = new Basement_Form_Input_Radio_Group( $params_3 );

		$container->appendChild( $dom->importNode( $input_3->create(), true ) );

		/*-- Fourth param --*/
		if ( ! empty( $wp_registered_sidebars ) ) {

			$div_3  = $container->appendChild($dom->createElement('div'));
			$div_3->setAttribute('style','height:20px;');

			$select_div = $container->appendChild( $dom->createElement( 'div' ) );
			$select_div->setAttribute('class','basement_block-selecter');

			$sidebars = array();

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$sidebars[$value['id']] = $value['name'];
			}

			$sidebars_params = array( 'values' => $sidebars );


			$current_param_4 = $this->get_first_sidebar();

			// Get setting name
			$setting_4 = 'page_sidebar_area';

			if ( $type === 'metabox' ) {
				$option_4 = $this->meta_tag . substr( $this->options[$setting_4], 18 );
				$post_value_4 = get_post_meta( $post->ID, $option_4, true );
				$sidebars_params['current_value'] = empty( $post_value_4 ) ? $current_param_4 : $post_value_4;
			} else {
				$option_4 = $this->options[$setting_4];
				$option_value_3 = get_option( $option_4, $current_param_4 );
				$sidebars_params['current_value'] = $option_value_3;
			}

			$sidebars_params['id']   = $option_4;
			$sidebars_params['name'] = $option_4;
			$sidebars_params['label_text'] = __('Select the widget area', BASEMENT_TEXTDOMAIN);

			$select = new Basement_Form_Input_Select( $sidebars_params );

			$select_div->appendChild( $dom->importNode( $select->create(), true ) );
		}


		/*-- Fifth param --*/
		$div_4  = $container->appendChild($dom->createElement('div'));
		$div_4->setAttribute('style','height:20px;');

		$select_vis = $container->appendChild( $dom->createElement( 'div' ) );
		$select_vis->setAttribute('class','basement_block-selecter');

		$vis_params =  array(
			'values' => array(
				'all' => __('All devices',BASEMENT_TEXTDOMAIN),
				'hidden-xs hidden-sm hidden-md' => __('Desktops',BASEMENT_TEXTDOMAIN),
				'hidden-xs hidden-lg' => __('Tablets',BASEMENT_TEXTDOMAIN),
				'hidden-sm hidden-md hidden-lg' => __('Phones',BASEMENT_TEXTDOMAIN),
				'hidden-xs' => __('Desktops/Tablets',BASEMENT_TEXTDOMAIN),
				'hidden-lg' => __('Tablets/Phones',BASEMENT_TEXTDOMAIN),
				'hidden-sm hidden-md' => __('Desktops/Phones',BASEMENT_TEXTDOMAIN),
			)
		);
		$current_param_vis = 'all';

		// Get setting name
		$setting_vis = 'page_sidebar_visibility';



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
		$vis_params['label_text'] = __('Visible on:', BASEMENT_TEXTDOMAIN);

		$select_visibility = new Basement_Form_Input_Select( $vis_params );

		$select_vis->appendChild( $dom->importNode( $select_visibility->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Show/Hide sidebar on blog
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
		$current_param = 'no';

		// Get setting name
		$setting = 'blog_sidebar';

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
		$params['label_text'] = __('Show sidebar?', BASEMENT_TEXTDOMAIN);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		/*-- Second param --*/

		$div  = $container->appendChild($dom->createElement('div'));
		$div->setAttribute('style','height:20px;');

		$params_2 = array(
			'values' => array(
				'right' => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'left'  => __( 'Left', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_2 = 'right';

		// Get setting name
		$setting_2 = 'blog_sidebar_dir';


		if ( $type === 'metabox' ) {
			$option_2                  = $this->meta_tag . substr( $this->options[$setting_2], 18 );
			$post_value_2              = get_post_meta( $post->ID, $option_2, true );
			$params_2['current_value'] = empty( $post_value_2 ) ? $current_param_2 : $post_value_2;
		} else {
			$option_2                  = $this->options[$setting_2];
			$option_value_2            = get_option( $option_2, $current_param_2 );
			$params_2['current_value'] = $option_value_2;
		}

		$params_2['id']   = $option_2;
		$params_2['name'] = $option_2;
		$params_2['label_text'] = __('Sidebar position', BASEMENT_TEXTDOMAIN);


		$input_2 = new Basement_Form_Input_Radio_Group( $params_2 );

		$container->appendChild( $dom->importNode( $input_2->create(), true ) );


		/*-- Third param --*/

		$div_2  = $container->appendChild($dom->createElement('div'));
		$div_2->setAttribute('style','height:20px;');

		$params_3 = array(
			'values' => array(
				'no' => __( 'No', BASEMENT_TEXTDOMAIN ),
				'left' => __( 'Left', BASEMENT_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'left_right'  => __( 'Right & Left', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_3 = 'no';

		// Get setting name
		$setting_3 = 'blog_sidebar_line';


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
		$params_3['label_text'] = __('Vertical line', BASEMENT_TEXTDOMAIN);


		$input_3 = new Basement_Form_Input_Radio_Group( $params_3 );

		$container->appendChild( $dom->importNode( $input_3->create(), true ) );


		/*-- Fourth param --*/
		if ( ! empty( $wp_registered_sidebars ) ) {

			$div_3  = $container->appendChild($dom->createElement('div'));
			$div_3->setAttribute('style','height:20px;');

			$select_div = $container->appendChild( $dom->createElement( 'div' ) );
			$select_div->setAttribute('class','basement_block-selecter');

			$sidebars = array();

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$sidebars[$value['id']] = $value['name'];
			}

			$sidebars_params = array( 'values' => $sidebars );


			$current_param_4 = $this->get_first_sidebar();

			// Get setting name
			$setting_4 = 'blog_sidebar_area';

			if ( $type === 'metabox' ) {
				$option_4 = $this->meta_tag . substr( $this->options[$setting_4], 18 );
				$post_value_4 = get_post_meta( $post->ID, $option_4, true );
				$sidebars_params['current_value'] = empty( $post_value_4 ) ? $current_param_4 : $post_value_4;
			} else {
				$option_4 = $this->options[$setting_4];
				$option_value_3 = get_option( $option_4, $current_param_4 );
				$sidebars_params['current_value'] = $option_value_3;
			}

			$sidebars_params['id']   = $option_4;
			$sidebars_params['name'] = $option_4;
			$sidebars_params['label_text'] = __('Select the widget area', BASEMENT_TEXTDOMAIN);

			$select = new Basement_Form_Input_Select( $sidebars_params );

			$select_div->appendChild( $dom->importNode( $select->create(), true ) );
		}


		/*-- Fifth param --*/
		$div_4  = $container->appendChild($dom->createElement('div'));
		$div_4->setAttribute('style','height:20px;');

		$select_vis = $container->appendChild( $dom->createElement( 'div' ) );
		$select_vis->setAttribute('class','basement_block-selecter');

		$vis_params =  array(
			'values' => array(
				'all' => __('All devices',BASEMENT_TEXTDOMAIN),
				'hidden-xs hidden-sm hidden-md' => __('Desktops',BASEMENT_TEXTDOMAIN),
				'hidden-xs hidden-lg' => __('Tablets',BASEMENT_TEXTDOMAIN),
				'hidden-sm hidden-md hidden-lg' => __('Phones',BASEMENT_TEXTDOMAIN),
				'hidden-xs' => __('Desktops/Tablets',BASEMENT_TEXTDOMAIN),
				'hidden-lg' => __('Tablets/Phones',BASEMENT_TEXTDOMAIN),
				'hidden-sm hidden-md' => __('Desktops/Phones',BASEMENT_TEXTDOMAIN),
			)
		);
		$current_param_vis = 'all';

		// Get setting name
		$setting_vis = 'blog_sidebar_visibility';



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
		$vis_params['label_text'] = __('Visible on:', BASEMENT_TEXTDOMAIN);

		$select_visibility = new Basement_Form_Input_Select( $vis_params );

		$select_vis->appendChild( $dom->importNode( $select_visibility->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Show/Hide sidebar on WooCommerce
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
		$current_param = 'no';

		// Get setting name
		$setting = 'woo_sidebar';

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
		$params['label_text'] = __('Show sidebar?', BASEMENT_TEXTDOMAIN);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$input = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $input->create(), true ) );

		/*-- Second param --*/

		$div  = $container->appendChild($dom->createElement('div'));
		$div->setAttribute('style','height:20px;');

		$params_2 = array(
			'values' => array(
				'right' => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'left'  => __( 'Left', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_2 = 'right';

		// Get setting name
		$setting_2 = 'woo_sidebar_dir';


		if ( $type === 'metabox' ) {
			$option_2                  = $this->meta_tag . substr( $this->options[$setting_2], 18 );
			$post_value_2              = get_post_meta( $post->ID, $option_2, true );
			$params_2['current_value'] = empty( $post_value_2 ) ? $current_param_2 : $post_value_2;
		} else {
			$option_2                  = $this->options[$setting_2];
			$option_value_2            = get_option( $option_2, $current_param_2 );
			$params_2['current_value'] = $option_value_2;
		}

		$params_2['id']   = $option_2;
		$params_2['name'] = $option_2;
		$params_2['label_text'] = __('Sidebar position', BASEMENT_TEXTDOMAIN);


		$input_2 = new Basement_Form_Input_Radio_Group( $params_2 );

		$container->appendChild( $dom->importNode( $input_2->create(), true ) );


		/*-- Third param --*/

		$div_2  = $container->appendChild($dom->createElement('div'));
		$div_2->setAttribute('style','height:20px;');

		$params_3 = array(
			'values' => array(
				'no' => __( 'No', BASEMENT_TEXTDOMAIN ),
				'left' => __( 'Left', BASEMENT_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'left_right'  => __( 'Right & Left', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param_3 = 'no';

		// Get setting name
		$setting_3 = 'woo_sidebar_line';


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
		$params_3['label_text'] = __('Vertical line', BASEMENT_TEXTDOMAIN);


		$input_3 = new Basement_Form_Input_Radio_Group( $params_3 );

		$container->appendChild( $dom->importNode( $input_3->create(), true ) );


		/*-- Fourth param --*/
		if ( ! empty( $wp_registered_sidebars ) ) {

			$div_3  = $container->appendChild($dom->createElement('div'));
			$div_3->setAttribute('style','height:20px;');

			$select_div = $container->appendChild( $dom->createElement( 'div' ) );
			$select_div->setAttribute('class','basement_block-selecter');

			$sidebars = array();

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$sidebars[$value['id']] = $value['name'];
			}

			$sidebars_params = array( 'values' => $sidebars );


			$current_param_4 = $this->get_first_sidebar();

			// Get setting name
			$setting_4 = 'woo_sidebar_area';

			if ( $type === 'metabox' ) {
				$option_4 = $this->meta_tag . substr( $this->options[$setting_4], 18 );
				$post_value_4 = get_post_meta( $post->ID, $option_4, true );
				$sidebars_params['current_value'] = empty( $post_value_4 ) ? $current_param_4 : $post_value_4;
			} else {
				$option_4 = $this->options[$setting_4];
				$option_value_3 = get_option( $option_4, $current_param_4 );
				$sidebars_params['current_value'] = $option_value_3;
			}

			$sidebars_params['id']   = $option_4;
			$sidebars_params['name'] = $option_4;
			$sidebars_params['label_text'] = __('Select the widget area', BASEMENT_TEXTDOMAIN);

			$select = new Basement_Form_Input_Select( $sidebars_params );

			$select_div->appendChild( $dom->importNode( $select->create(), true ) );
		}



		/*-- Fifth param --*/
		$div_4  = $container->appendChild($dom->createElement('div'));
		$div_4->setAttribute('style','height:20px;');

		$select_vis = $container->appendChild( $dom->createElement( 'div' ) );
		$select_vis->setAttribute('class','basement_block-selecter');

		$vis_params =  array(
			'values' => array(
				'all' => __('All devices',BASEMENT_TEXTDOMAIN),
				'hidden-xs hidden-sm hidden-md' => __('Desktops',BASEMENT_TEXTDOMAIN),
				'hidden-xs hidden-lg' => __('Tablets',BASEMENT_TEXTDOMAIN),
				'hidden-sm hidden-md hidden-lg' => __('Phones',BASEMENT_TEXTDOMAIN),
				'hidden-xs' => __('Desktops/Tablets',BASEMENT_TEXTDOMAIN),
				'hidden-lg' => __('Tablets/Phones',BASEMENT_TEXTDOMAIN),
				'hidden-sm hidden-md' => __('Desktops/Phones',BASEMENT_TEXTDOMAIN),
			)
		);
		$current_param_vis = 'all';

		// Get setting name
		$setting_vis = 'woo_sidebar_visibility';



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
		$vis_params['label_text'] = __('Visible on:', BASEMENT_TEXTDOMAIN);

		$select_visibility = new Basement_Form_Input_Select( $vis_params );

		$select_vis->appendChild( $dom->importNode( $select_visibility->create(), true ) );



		/*-- Six params --*/
		$div_5  = $container->appendChild($dom->createElement('div'));
		$div_5->setAttribute('style','height:20px;');

		$sidebar_color = $container->appendChild( $dom->createElement( 'div' ) );
		$bg_params = array(
		);
		$current_color = '#fafafa';
		$settings_color = 'woo_sidebar_bg';


		if ( $type === 'metabox' ) {
			$option_bg = $this->meta_tag . substr( $this->options[$settings_color], 18 );
			$post_value_bg = get_post_meta( $post->ID, $option_bg, true );
			$bg_params['value'] = empty( $post_value_bg ) ? $current_color : $post_value_bg;
		} else {
			$option_bg = $this->options[$settings_color];
			$post_value_bg = get_option( $option_bg, $current_color );
			$bg_params['value'] = $post_value_bg;
		}

		$bg_params['id']   = $option_bg;
		$bg_params['name'] = $option_bg;
		$bg_params['label_text'] = __('Background color', BASEMENT_TEXTDOMAIN);

		$colorpicker = new Basement_Form_Input_Colorpicker( $bg_params );

		$sidebar_color->appendChild( $dom->importNode( $colorpicker->create(), true ) );


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
	public function get_sidebar_settings() {
		global $post;

		$names = $this->options_names();

		if ( ! is_home()  && ! is_search() && ! is_archive() ) {
			$custom_sidebar = get_post_meta( $post->ID, '_basement_meta_custom_sidebar', true );
		} else {
			$custom_sidebar = '';
		}

		$settings = array();

		foreach ( $names as $key => $value ) {
			if ( ! empty( $custom_sidebar ) ) {
				$option     = $this->meta_tag . substr( $value, 18 );
				$post_value = get_post_meta( $post->ID, $option, true );

				if ( !empty( $post_value ) ) {
					$settings[ $key ] = $post_value;
				}

			} else {
				if (
					$key === 'single_sidebar' ||
					$key === 'single_sidebar_dir' ||
					$key === 'single_sidebar_line' ||
				    $key === 'single_sidebar_area' ||
					$key === 'single_sidebar_visibility'
				)
					continue;
				$settings[ $key ] = get_option( $value );
			}

		}

		if ( ! empty( $custom_sidebar ) ) {
			$settings['scope'] = 'private';
		} else {
			$settings['scope'] = 'public';
		}

		return apply_filters( 'basement_sidebar_settings', $settings );
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

}


if ( ! function_exists( 'basement_sidebar' ) ) {
	/**
	 * Displays sidebar on page
	 *
	 * @param string $type
	 * @param string $file
	 */
	function basement_sidebar( $type = 'page', $file = '' ) {
		global $post;
		global $wp_registered_sidebars;
		global $basement_sidebar;

		if ( ! empty( $wp_registered_sidebars ) ) {
			$sidebar  = new Basement_Sidebar();
			$settings = $sidebar->get_sidebar_settings();

			if ( ! empty( $settings ) ) {
				extract( $settings );

				$wp_type = get_post_type();
				$single_sidebar_bg = 'transparent';
				if ( $scope !== 'private' ) {
					if ( $wp_type === $type || $type === 'page' ) {
						$single_sidebar            = ! empty( $page_sidebar ) ? $page_sidebar : 'no';
						$single_sidebar_dir        = ! empty( $page_sidebar_dir ) ? $page_sidebar_dir : 'right';
						$single_sidebar_line       = ! empty( $page_sidebar_line ) ? $page_sidebar_line : 'no';
						$single_sidebar_area       = ! empty( $page_sidebar_area ) ? $page_sidebar_area : $sidebar->get_first_sidebar();
						$single_sidebar_visibility = ! empty( $page_sidebar_visibility ) ? $page_sidebar_visibility : 'all';
					} elseif ( $wp_type === $type || $type === 'blog' ) {
						$single_sidebar            = ! empty( $blog_sidebar ) ? $blog_sidebar : 'no';
						$single_sidebar_dir        = ! empty( $blog_sidebar_dir ) ? $blog_sidebar_dir : 'right';
						$single_sidebar_line       = ! empty( $blog_sidebar_line ) ? $blog_sidebar_line : 'no';
						$single_sidebar_area       = ! empty( $blog_sidebar_area ) ? $blog_sidebar_area : $sidebar->get_first_sidebar();
						$single_sidebar_visibility = ! empty( $blog_sidebar_visibility ) ? $blog_sidebar_visibility : 'all';
					} elseif ( $wp_type === $type || $type === 'woo' ) {
						$single_sidebar            = ! empty( $woo_sidebar ) ? $woo_sidebar : 'no';
						$single_sidebar_dir        = ! empty( $woo_sidebar_dir ) ? $woo_sidebar_dir : 'right';
						$single_sidebar_line       = ! empty( $woo_sidebar_line ) ? $woo_sidebar_line : 'no';
						$single_sidebar_area       = ! empty( $woo_sidebar_area ) ? $woo_sidebar_area : $sidebar->get_first_sidebar();
						$single_sidebar_visibility = ! empty( $woo_sidebar_visibility ) ? $woo_sidebar_visibility : 'all';
						$single_sidebar_bg = ! empty( $woo_sidebar_bg ) ? $woo_sidebar_bg : 'transparent';
					}
				}

				$it_woo_page = false;
				if(Basement_Ecommerce_Woocommerce::enabled()) {
					if(is_cart() || is_account_page() || is_checkout() || is_edit_account_page()) {
						$it_woo_page = true;
					}
				}

				if ( $single_sidebar === 'yes' && is_active_sidebar($single_sidebar_area)  && !$it_woo_page  ) {
					$basement_sidebar = array(
						'variant' => $type,
						'display' => $single_sidebar,
						'dir' => $single_sidebar_dir,
						'line' => $single_sidebar_line,
						'sidebar' => $single_sidebar_area,
						'visibility' => $single_sidebar_visibility,
						'bg' => $single_sidebar_bg,
						'place' => 'aside'
					);

					get_sidebar($file);
				}

			}
		}

	}
}


if ( ! function_exists( 'basement_content_classes' ) ) {
	/**
	 * Generate classes for main content
	 *
	 * @param bool   $echo
	 * @param string $type
	 *
	 * @return string
	 */
	function basement_content_classes( $type = 'page', $echo = false ) {
		global $wp_registered_sidebars;
		global $basement_content;
		$classes = array();

		if ( ! empty( $wp_registered_sidebars ) && $type !== 'default' ) {
			$sidebar  = new Basement_Sidebar();
			$settings = $sidebar->get_sidebar_settings();

			if ( ! empty( $settings ) ) {
				extract( $settings );

				$wp_type = get_post_type();

				if ( $scope !== 'private' ) {
					if ( $wp_type === $type || $type === 'page' ) {
						$single_sidebar            = ! empty( $page_sidebar ) ? $page_sidebar : 'no';
						$single_sidebar_dir        = ! empty( $page_sidebar_dir ) ? $page_sidebar_dir : 'right';
						$single_sidebar_line       = ! empty( $page_sidebar_line ) ? $page_sidebar_line : 'no';
						$single_sidebar_area       = ! empty( $page_sidebar_area ) ? $page_sidebar_area : $sidebar->get_first_sidebar();
						$single_sidebar_visibility = ! empty( $page_sidebar_visibility ) ? $page_sidebar_visibility : 'all';
					} elseif ( $wp_type === $type || $type === 'blog' ) {
						$single_sidebar            = ! empty( $blog_sidebar ) ? $blog_sidebar : 'no';
						$single_sidebar_dir        = ! empty( $blog_sidebar_dir ) ? $blog_sidebar_dir : 'right';
						$single_sidebar_line       = ! empty( $blog_sidebar_line ) ? $blog_sidebar_line : 'no';
						$single_sidebar_area       = ! empty( $blog_sidebar_area ) ? $blog_sidebar_area : $sidebar->get_first_sidebar();
						$single_sidebar_visibility = ! empty( $blog_sidebar_visibility ) ? $blog_sidebar_visibility : 'all';
					} elseif ( $wp_type === $type || $type === 'woo' ) {
						$single_sidebar            = ! empty( $woo_sidebar ) ? $woo_sidebar : 'no';
						$single_sidebar_dir        = ! empty( $woo_sidebar_dir ) ? $woo_sidebar_dir : 'right';
						$single_sidebar_line       = ! empty( $woo_sidebar_line ) ? $woo_sidebar_line : 'no';
						$single_sidebar_area       = ! empty( $woo_sidebar_area ) ? $woo_sidebar_area : $sidebar->get_first_sidebar();
						$single_sidebar_visibility = ! empty( $woo_sidebar_visibility ) ? $woo_sidebar_visibility : 'all';
					}
				}

				$it_woo_page = false;
				if(Basement_Ecommerce_Woocommerce::enabled()) {
					if(is_cart() || is_account_page() || is_checkout() || is_edit_account_page()) {
						$it_woo_page = true;
					}
				}

				if ( $single_sidebar === 'yes' && is_active_sidebar( $single_sidebar_area ) && !$it_woo_page ) {
					if($single_sidebar_dir === 'left') {
						if($single_sidebar_visibility === 'all') {
							$classes[] = 'col-md-push-3 col-sm-push-4';
						} elseif($single_sidebar_visibility === 'hidden-xs hidden-sm hidden-md') {
							$classes[] = 'col-lg-push-3';
						} elseif($single_sidebar_visibility === 'hidden-xs hidden-lg') {
							$classes[] = 'col-lg-push-0 col-md-push-3 col-sm-push-4';
						} elseif($single_sidebar_visibility === 'hidden-sm hidden-md hidden-lg') {
							$classes[] = '';
						} elseif($single_sidebar_visibility === 'hidden-xs') {
							$classes[] = 'col-md-push-3 col-sm-push-4';
						} elseif($single_sidebar_visibility === 'hidden-lg') {
							$classes[] = 'col-lg-push-0 col-md-push-3 col-sm-push-4';
						} elseif($single_sidebar_visibility === 'hidden-sm hidden-md') {
							$classes[] = 'col-lg-push-3';
						}
						$classes[] = 'page-content-cell maincontent';
					} elseif($single_sidebar_dir === 'right') {
						$classes[] = 'page-content-cell maincontent';
					}

					if($single_sidebar_visibility === 'all') {
						$classes[] = 'col-md-9 col-sm-8';
					} elseif($single_sidebar_visibility === 'hidden-xs hidden-sm hidden-md') {
						$classes[] = 'col-lg-9';
					} elseif($single_sidebar_visibility === 'hidden-xs hidden-lg') {
						$classes[] = 'col-lg-12 col-md-9 col-sm-8';
					} elseif($single_sidebar_visibility === 'hidden-sm hidden-md hidden-lg') {
						$classes[] = 'col-lg-12';
					} elseif($single_sidebar_visibility === 'hidden-xs') {
						$classes[] = 'col-md-9 col-sm-8';
					} elseif($single_sidebar_visibility === 'hidden-lg') {
						$classes[] = 'col-lg-12 col-md-9 col-sm-8';
					} elseif($single_sidebar_visibility === 'hidden-sm hidden-md') {
						$classes[] = 'col-lg-9';
					}

				} else {
					$classes[] = 'col-lg-12 page-content-cell maincontent';
				}
			}
		} else {
			$classes[] = 'col-lg-12 page-content-cell maincontent';
		}

		if(isset($single_sidebar)) {
			$basement_content = array(
				'display' => $single_sidebar
			);
		}

		if ( $echo ) {
			echo implode( ' ', apply_filters( 'basement_content_classes', $classes ) );
		} else {
			return implode( ' ', apply_filters( 'basement_content_classes', $classes ) );
		}
	}
}


if ( ! function_exists( 'basement_sidebar_classes' ) ) {
	/**
	 * Generate classes for sidebar
	 *
	 * @param string $inline_classes
	 * @param string $inline_style
	 * @param bool   $echo
	 *
	 * @return string
	 */
	function basement_sidebar_classes( $inline_classes = '', $inline_style = '', $echo = true ) {
		global $basement_sidebar;

		$classes = array();
		$styles = array();

		foreach ( $basement_sidebar as $key => $value ) {
			switch ($key) {
				case 'dir' :
					if($value === 'left') {
						$classes[] = 'col-md-pull-9 col-sm-pull-8';
					} elseif($value === 'right') {
						#$classes[] = '';
					}
					break;
				case 'visibility' :
					if($value === 'all') {
						$classes[] = 'sidebar-' . $key . '-' . sanitize_html_class( $value );
					} else {
						$classes[] = $value;
						switch ($value) {
							case  'hidden-xs hidden-sm hidden-md':
								$classes[] = 'sidebar-' . $key . '-desktops';
								break;
							case  'hidden-xs hidden-lg':
								$classes[] = 'sidebar-' . $key . '-tablets';
								break;
							case  'hidden-sm hidden-md hidden-lg':
								$classes[] = 'sidebar-' . $key . '-phones';
								break;
							case  'hidden-xs':
								$classes[] = 'sidebar-' . $key . '-desktops_tablets';
								break;
							case  'hidden-lg':
								$classes[] = 'sidebar-' . $key . '-tablets_phones';
								break;
							case  'hidden-sm hidden-md':
								$classes[] = 'sidebar-' . $key . '-desktops_phones';
								break;
						}
					}
					break;
				case 'bg' :
					$styles[] = 'background-color:'.$value.';';
					if('transparent' !== $value) {
						$classes[] = 'sidebar-custom-color';
					}
				break;
				default :
					$classes[] = 'sidebar-' . $key . '-' . sanitize_html_class( $value );
			}
		}

		$classes[] = 'col-md-3 col-sm-4';

		$classes[] = 'sidebar-content-cell';

		$classes[] = $inline_classes;

		$styles[] = $inline_style;



		if ( $echo ) {
			echo 'class="'.implode( ' ', apply_filters( 'basement_sidebar_classes', $classes ) ).'" style="'.implode( ' ', apply_filters( 'basement_sidebar_styles', $styles ) ).'"';
		} else {
			return array(
				'class' => implode( ' ', apply_filters( 'basement_sidebar_classes', $classes ) ),
				'style' => implode( ' ', apply_filters( 'basement_sidebar_styles', $styles ) )
			);
		}
	}
}