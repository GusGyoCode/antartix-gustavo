<?php
defined( 'ABSPATH' ) or die();

define( 'HEADPATH', WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );

class Basement_Header {

	private static $instance = null;

	// Name for Header options
	private $options = array();

	// Block for Header options
	private $options_blocks = array();

	// Holds our custom fields
	protected static $fields = array();

	protected static $fields_count = 0;

	/**
	 * Basement_Header constructor.
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

			add_action( 'add_meta_boxes', array( &$this, 'generate_header_param_meta_box' ), 10, 2 );

			add_action( 'edit_form_after_title', array( &$this, 'header_meta_box_move' ) );


			if ( 'nav-menus.php' == basename( $_SERVER['PHP_SELF'] ) ) {
				add_filter( 'wp_edit_nav_menu_walker', array( &$this, '_filter_walker' ), 99 ); # Require walker for Menu (ignore)

				add_action( 'wp_nav_menu_item_custom_fields', array( &$this, '_fields' ), 10, 4 );

				add_action( 'wp_update_nav_menu_item', array( &$this, '_save' ), 10, 3 ); # Save menu-item-%s fields

				add_filter( 'manage_nav-menus_columns', array( &$this, '_columns' ), 99 );  # Merge column files

				self::$fields = array(
					'field-megamenu' => __( 'Enable Mega Menu', BASEMENT_TEXTDOMAIN ),
					'field-title'   => __( 'Column Title', BASEMENT_TEXTDOMAIN ),
					'field-column'   => __( 'Columns', BASEMENT_TEXTDOMAIN )
				);
			}

		}

	}


	/**
	 * Basement_Header init
	 *
	 * @return Basement_Header|null
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Header();
		}

		return self::$instance;
	}

	/**
	 * Check if WPML plugin is enable
	 *
	 * @return bool
	 */
	public function wpml_enable() {
		$wpml_options = get_option( 'icl_sitepress_settings' );
		$default_lang = isset($wpml_options['default_language']) ? $wpml_options['default_language'] : '';
		if($default_lang) {
			$default_lang = true;
		} else {
			$default_lang = false;
		}
		
		return ( in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && $default_lang ) || defined('FAKE_WPML');
	}


	/**
	 * Replace default menu editor walker with ours
	 *
	 * We don't actually replace the default walker. We're still using it and
	 * only injecting some HTMLs.
	 *
	 * @param $walker
	 *
	 * @return string
	 */
	public static function _filter_walker( $walker ) {
		$walker = 'Menu_Item_Custom_Fields_Walker';
		if ( ! class_exists( $walker ) ) {
			require_once( 'walker-nav-menu-edit.php' );
		}

		return $walker;
	}


	/**
	 * Save custom field value
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		foreach ( self::$fields as $_key => $label ) {
			$key = sprintf( 'menu-item-%s', $_key );

			// Sanitize
			if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
				// Do some checks here...
				$value = $_POST[ $key ][ $menu_item_db_id ];
			} else {
				$value = null;
			}

			// Update
			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $key, $value );
			} else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}


	/**
	 * Print field
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $id, $item, $depth, $args ) {

		if ( $depth === 0 ) {
			foreach ( self::$fields as $_key => $label ) {
				if ( $_key === 'field-megamenu' ) {
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );
					?>
					<p class="basement-mega-menu description description-wide <?php echo esc_attr( $class ) ?>">
						<?php printf(
							'<label for="%1$s"><input type="hidden" value="" name="%3$s" autocomplete="off"><input type="checkbox" id="%1$s" class="basement-megamenu-check widefat %1$s" name="%3$s" value="yes" %4$s /> %2$s</label>',
							esc_attr( $id ),
							esc_html( $label ),
							esc_attr( $name ),
							checked( $value, 'yes', false )
						) ?>
					</p>
				<?php }
			}
		} elseif ( $depth === 1 && $item->menu_item_parent ) {
			$value = get_post_meta( absint( $item->menu_item_parent ), 'menu-item-field-megamenu', true );

			self::$fields_count ++;
			if ( self::$fields_count > 4 ) {
				self::$fields_count = 1;
			}

			if ( $value === 'yes' ) {

				foreach ( self::$fields as $_key => $label ) {
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );

					if($_key === 'field-title') { ?>
						<p class="basement-mega-title description description-wide <?php echo esc_attr( $class ) ?>">
							<?php printf(
								'<label for="%1$s"><input type="hidden" value="" name="%3$s" autocomplete="off"><input type="checkbox" id="%1$s" class="widefat %1$s" name="%3$s" value="yes" %4$s /> %2$s</label>',
								esc_attr( $id ),
								__('As title', BASEMENT_TEXTDOMAIN),
								esc_attr( $name ),
								checked( $value, 'yes', false )
							) ?>
						</p>
					<?php } elseif ( $_key === 'field-column' ) { ?>

						<p class="basement-mega-columns description description-wide <?php echo esc_attr( $class ) ?>">
							<?php

							printf(
								'<label for="%1$s"><input type="radio" id="%1$s" class="widefat %1$s" name="%3$s" value="col-1" %4$s />' . __( 'First column', BASEMENT_TEXTDOMAIN ) . '</label>',
								esc_attr( $id ) . '-col-1',
								esc_html( $label ) . '-col-1',
								esc_attr( $name ),
								( empty( $value ) && self::$fields_count === 1 ) ? 'checked' : checked( $value, 'col-1', false )
							);

							printf(
								'<label for="%1$s"><input type="radio" id="%1$s" class="widefat %1$s" name="%3$s" value="col-2" %4$s />' . __( 'Second column', BASEMENT_TEXTDOMAIN ) . '</label>',
								esc_attr( $id ) . '-col-2',
								esc_html( $label ) . '-col-2',
								esc_attr( $name ),
								( empty( $value ) && self::$fields_count === 2 ) ? 'checked' : checked( $value, 'col-2', false )
							);

							printf(
								'<label for="%1$s"><input type="radio" id="%1$s" class="widefat %1$s" name="%3$s" value="col-3" %4$s />' . __( 'Third column', BASEMENT_TEXTDOMAIN ) . '</label>',
								esc_attr( $id ) . '-col-3',
								esc_html( $label ) . '-col-3',
								esc_attr( $name ),
								( empty( $value ) && self::$fields_count === 3 ) ? 'checked' : checked( $value, 'col-3', false )
							);

							printf(
								'<label for="%1$s"><input type="radio" id="%1$s" class="widefat %1$s" name="%3$s" value="col-4" %4$s />' . __( 'Fourth column', BASEMENT_TEXTDOMAIN ) . '</label>',
								esc_attr( $id ) . '-col-4',
								esc_html( $label ) . '-col-4',
								esc_attr( $name ),
								( empty( $value ) && self::$fields_count === 4 ) ? 'checked' : checked( $value, 'col-4', false )
							);


							?>
						</p>
						<?php
					}
				}
			}

		}
	}


	/**
	 * Add our fields to the screen options toggle
	 *
	 * @param array $columns Menu item columns
	 *
	 * @return array
	 */
	public static function _columns( $columns ) {

		$columns = array_merge( $columns, self::$fields );

		return $columns;
	}


	/**
	 * Register options for Header
	 */
	public function register_theme_settings() {
		foreach ( $this->options as $key => $value ) {
			register_setting( 'basement_theme_options', $value );
		}
	}


	/**
	 * Register Meta Box
	 *
	 * @param $post_type
	 */
	public function generate_header_param_meta_box( $post_type, $post ) {
		$post_ID = $post->ID;
		if ( $post_ID != get_option( 'page_for_posts' ) ) {
			if ( in_array( $post_type, apply_filters('basement_header_meta_box', array( 'page', 'post', 'product', 'single_project' ) ) ) ) {
				add_meta_box(
					'header_parameters_meta_box',
					__( 'Header Parameters', BASEMENT_TEXTDOMAIN ),
					array( &$this, 'render_header_param_meta_box' ),
					$post_type,
					'header',
					'high'
				);

				add_filter( 'postbox_classes_' . $post_type . '_' . 'header_parameters_meta_box', array(
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
	public function header_meta_box_move( $params ) {
		global $post, $wp_meta_boxes;
		$post_type = isset( $params->post_type ) ? $params->post_type : '';
		if ( ! empty( $post_type ) &&  in_array( $post_type, array( 'page', 'post', 'product', 'single_project' ) )  ) {
			do_meta_boxes( get_current_screen(), 'header', $post );
			unset( $wp_meta_boxes[ get_post_type( $post ) ]['header'] );
		}
	}


	/**
	 * Render Meta Box Parameters
	 */
	public function render_header_param_meta_box( $post ) {
		$view = new Basement_Plugin();
		$view->basement_views( $this->theme_settings_meta_box(), array( 'header-param-meta-box' ) );
	}

	/**
	 * List of option name settings
	 *
	 * @return array
	 */
	private function options_names() {
		return apply_filters('basement_header_options', array(
			'menu'                       => BASEMENT_TEXTDOMAIN . '_menu',
			'menu_type'                  => BASEMENT_TEXTDOMAIN . '_menu_type',
			'header_sticky'              => BASEMENT_TEXTDOMAIN . '_header_sticky',
			'logo_text'                  => BASEMENT_TEXTDOMAIN . '_logo_text',
			'logo_text_size'             => BASEMENT_TEXTDOMAIN . '_logo_text_size',
			'logo_text_color'            => BASEMENT_TEXTDOMAIN . '_logo_text_color',
			'logo_image'                 => BASEMENT_TEXTDOMAIN . '_logo_image',
			'logo_link'                  => BASEMENT_TEXTDOMAIN . '_logo_link',
			'logo_link_toggle'           => BASEMENT_TEXTDOMAIN . '_logo_link_toggle',
			'logo_position'              => BASEMENT_TEXTDOMAIN . '_logo_position',
			'header_size'                => BASEMENT_TEXTDOMAIN . '_header_size',
			'header_off'                 => BASEMENT_TEXTDOMAIN . '_header_off',
			'header_helper'              => BASEMENT_TEXTDOMAIN . '_header_helper',
			'header_elements'            => BASEMENT_TEXTDOMAIN . '_header_elements',
			'header_style'               => BASEMENT_TEXTDOMAIN . '_header_style',
			'header_bg'                  => BASEMENT_TEXTDOMAIN . '_header_bg',
			'header_opacity'             => BASEMENT_TEXTDOMAIN . '_header_opacity',
			'header_border_bg'           => BASEMENT_TEXTDOMAIN . '_header_border_bg',
			'header_border_opacity'      => BASEMENT_TEXTDOMAIN . '_header_border_opacity',
			'header_border_size'         => BASEMENT_TEXTDOMAIN . '_header_border_size',
			'header_padding_top'         => BASEMENT_TEXTDOMAIN . '_header_padding_top',
			'header_padding_bottom'      => BASEMENT_TEXTDOMAIN . '_header_padding_bottom',
			'header_btn_text'            => BASEMENT_TEXTDOMAIN . '_header_btn_text',
			'header_btn_icon'            => BASEMENT_TEXTDOMAIN . '_header_btn_icon',
			'header_btn_link'            => BASEMENT_TEXTDOMAIN . '_header_btn_link',
			'header_global_border'       => BASEMENT_TEXTDOMAIN . '_header_global_border',
			'header_global_border_color' => BASEMENT_TEXTDOMAIN . '_header_global_border_color',
			'header_global_border_size'  => BASEMENT_TEXTDOMAIN . '_header_global_border_size',
		) );
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
	public function front_get_settings_options() {
		global $post;

		$id = isset($post->ID) ? $post->ID : false;

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
			$custom_header = get_post_meta( $id, '_basement_meta_custom_header', true );
		} elseif ( Basement_Ecommerce_Woocommerce::is_shop() ) {
			$custom_header = get_post_meta( $id, '_basement_meta_custom_header', true );
		}  else {
			$custom_header = '';
		}

		$settings = array();

		foreach ( $names as $key => $value ) {

			if ( ! empty( $custom_header ) ) {
				$option     = '_basement_meta_header' . substr( $value, 18 );
				$post_value = get_post_meta( $id, $option, true );

				switch ($key) {
					case 'header_padding_top':
					case 'header_padding_bottom':
					case 'header_border_bg':
					case 'header_border_opacity':
					case 'header_bg':
					case 'header_opacity':
					case 'header_btn_text':
					case 'header_btn_link':
					case 'header_btn_icon':
					case 'header_global_border':
					case 'header_global_border_color':
					case 'header_global_border_size':
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
				$settings[ $key ] = get_option( $value );
			}

			switch ( $key ) {
				case 'menu' :
					$settings[ $key ] = ( $settings[ $key ] !== 'default' ) ? absint( $settings[ $key ] ) : $settings[ $key ];
					break;
				case 'logo_link' :
					if ( empty( $settings[ $key ] ) || $settings[ $key ] === 'http://' || $settings[ $key ] === 'https://' ) {
						$settings[ $key ] = get_site_url();
					} else {
						$settings[ $key ] = esc_url( $settings[ $key ] );
					}
					break;
				case 'header_elements' :
					break;
				default :
					$settings[ $key ] = esc_attr( $settings[ $key ] );
			}

		}

		
		$settings = $this->fill_empty_settings($settings);

		return apply_filters( 'basement_header_settings', $settings );
	}
	
	public function fill_empty_settings($settings) {

		foreach($settings as $key => $value) {
			switch ($key) {
				case 'menu_type' :
					$settings[$key] = !empty($value) ? $value : 'default';
				break;
				case 'header_sticky' :
					$settings[$key] = !empty($value) ? $value : 'disable';
				break;
				case 'logo_text' :
					$settings[$key] = !empty($value) ? $value : __('Co&ntilde;ico', BASEMENT_TEXTDOMAIN);
				break;
				case 'header_global_border' :
					$settings[$key] = !empty($value) ? $value : 'no';
					break;

				case 'logo_link_toggle' :
					$settings[$key] = !empty($value) ? $value : 'yes';
				break;
				case 'logo_position' :
					$settings[$key] = !empty($value) ? $value : 'left';
				break;
				case 'header_off' :
					$settings[$key] = !empty($value) ? $value : 'no';
				break;
				case 'header_helper' :
					$settings[$key] = !empty($value) ? $value : 'no';
					break;
				case 'header_style' :
					$settings[$key] = !empty($value) ? $value : 'dark';
				break;
				case 'header_size' :
					$settings[$key] = !empty($value) ? $value : 'fullwidth';
					break;
				case 'header_elements' :
					$settings[$key] = !empty($value) ? $value : array('logo_text'=>'logo_text','user_section'=>'','shop_section'=>'','lang_section'=>'lang_section', 'search_section' => 'search_section');
				break;
				/*case 'logo_text_size' :
						$settings[ $key ] = ! empty( $value ) ? $value : '20';
					break;
				case 'logo_text_color' :
					if($settings['header_style'] == 'gray') {
						$settings[ $key ] = ! empty( $value ) ? $value : '#fff';
					}
					break;*/
			}
		}

		return $settings;
	}


	/**
	 * Return formatted classes
	 *
	 * @return array
	 */
	public function front_classes_header() {
		$names   = apply_filters( 'basement_header_classes', $this->front_get_settings_options() );
		$classes = array();
		foreach ( $names as $key => $value ) {
			if (
				$key === 'logo_text' ||
				$key === 'logo_image' ||
				$key === 'logo_text_size' ||
				$key === 'logo_text_color' ||
				$key === 'logo_link' ||
				$key === 'header_elements' ||
				$key === 'header_bg' ||
				$key === 'header_opacity' ||
				$key === 'header_border_bg' ||
				$key === 'header_border_opacity' ||
				$key === 'header_border_size' ||
				$key === 'header_padding_top' ||
				$key === 'header_padding_bottom' ||
				$key === 'header_btn_text' ||
				$key === 'header_btn_icon' ||
				$key === 'header_btn_link' ||
				$key === 'header_global_border' ||
				$key === 'header_global_border_size' ||
				$key === 'header_global_border_color'
			) {
				continue;
			}

			$classes[] = $key . '_' . sanitize_html_class( $value );
		}

		return implode( ' ', apply_filters( 'basement_header_classes_format', $classes ) );
	}


	/**
	 * Set name for block in header settings
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
				'title'       => __( 'Menu', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Select the main menu in header.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'menu'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Menu type', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the type of menu and show icon or standard menu.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'menu_type'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Logo Text', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets logo text in the header.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'text_logo'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Logo Image', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets logo image in the header.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'image_logo'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Logo Link', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the link on the logo. By default, the Home page.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'link_logo'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Logo position', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the position of the logo.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'position_logo'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Disable header', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Completely turn off the header.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_off'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Auto height', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Automatically calculates the header height.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_helper'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Sticky header', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the sticky header at the top of the page.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_sticky'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Header items', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'It enables or disables the elements in header.<br/><b><ins>Language Section</ins> works only if <a href="https://wpml.org/" target="_blank" style="text-decoration: none;" title="WPML">WPML</a> plugin activated.</b>', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_elements'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Header style', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the preset heading style.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_style'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Header background', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the background color for the header.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_bg'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Header border', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the color/size bottom border for header.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_border'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Header padding', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the inner padding for the header.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_padding'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Header size', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the header width.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_size'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Header button', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the button for the header.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_button'
			),
			array(
				'type'        => 'dom',
				'title'       => __( 'Header global border', BASEMENT_TEXTDOMAIN ),
				'description' => __( 'Sets the border for the whole page.', BASEMENT_TEXTDOMAIN ),
				'input'       => 'header_global_border'
			)
		);
	}


	/**
	 * Set block for header settings
	 */
	private function set_options_blocks() {
		$this->options_blocks = $this->options_blocks();
	}


	/**
	 * Init header params
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	public function theme_settings_config_filter( $config = array() ) {
		$settings_config = array(
			'header' => array(
				'title'  => __( 'Header', BASEMENT_TEXTDOMAIN ),
				'blocks' => array()
			)
		);

		foreach ( $this->options_blocks as $key => $value ) {
			$value['input'] = call_user_func( array( &$this, $value['input'] ) );
			$settings_config['header']['blocks'][] = $value;
		}

		return array_merge( $settings_config, $config );
	}


	/**
	 * Settings for header meta box
	 *
	 * @return array
	 */
	public function theme_settings_meta_box() {
		$settings_config = array();

		foreach ( $this->options_blocks as $key => $value ) {
			$value['key']      = $value['input'];
			$value['input']    = call_user_func( array( &$this, $value['input'] ), 'metabox' );
			$settings_config[] = $value;
		}

		return $settings_config;
	}


	/**
	 * Default menu
	 *
	 * @param string $type
	 *
	 * @return DOMNode|string
	 */
	public function menu( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$menus = wp_get_nav_menus();

		if ( empty( $menus ) ) {
			if ( isset( $GLOBALS['wp_customize'] ) && $GLOBALS['wp_customize'] instanceof WP_Customize_Manager ) {
				$url = 'javascript: wp.customize.panel( "nav_menus" ).focus();';
			} else {
				$url = admin_url( 'nav-menus.php' );
			}
			$container = $dom->appendChild( $dom->createElement( 'p', __( 'No menus have been created yet. ', BASEMENT_TEXTDOMAIN ) ) );
			$a         = $container->appendChild( $dom->createElement( 'a', __( 'Create some', BASEMENT_TEXTDOMAIN ) ) );
			$a->setAttribute( 'href', esc_attr( $url ) );
		} else {

			$theme_locations = get_nav_menu_locations();
			$theme_locations_header = isset($theme_locations['header']) ? $theme_locations['header'] : '';

			$menu_location = get_term( $theme_locations_header, 'nav_menu' );

			$menu_items = array();

			foreach ( $menus as $menu ) {
				$menu_items[ absint( $menu->term_id ) ] = esc_html( $menu->name );
			}


			if ( ! is_wp_error( $menu_location ) ) {

				$menu_name = apply_filters('basement_default_menu_no_error','Main Menu');
				$key = array_search ($menu_name, $menu_items);

				if($key) {
					$current_param = $key;
				} else {
					$current_param = 'default';
				}

				$menu_items    = array( 'default' => __( 'WordPress menu', BASEMENT_TEXTDOMAIN ) ) + $menu_items;
			} else {
				reset( $menu_items );

				$menu_name = apply_filters('basement_default_menu_on_error','Main Menu');
				$key = array_search ($menu_name, $menu_items);

				if($key) {
					$current_param = $key;
                } else {
					$current_param = absint( key( $menu_items ) );
                }

			}

			$params = array( 'values' => $menu_items );


			if ( $type === 'metabox' ) {
				$option                  = '_basement_meta_header' . substr( $this->options['menu'], 18 );
				$post_value              = get_post_meta( $post->ID, $option, true );
				$params['current_value'] = empty( $post_value ) || $post_value === 'default' ? $current_param : (int) $post_value;
			} else {
				$option                  = $this->options['menu'];
				$option_value            = get_option( $option );
				$params['current_value'] = empty( $option_value ) || $option_value === 'default' ? $current_param : (int) $option_value;
			}

			$params['id']   = $option;
			$params['name'] = $option;

			$select = new Basement_Form_Input_Select( $params );

			$container = $dom->appendChild( $dom->importNode( $select->create(), true ) );

		}
		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Menu type
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function menu_type( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$params        = array(
			'values' => array(
				'default' => __( 'Default', BASEMENT_TEXTDOMAIN ),
				'simple'  => __( 'Simple', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'default';


		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_header' . substr( $this->options['menu_type'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['menu_type'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;

		$menu_type = new Basement_Form_Input_Radio_Group( $params );

		$container = $dom->appendChild( $dom->importNode( $menu_type->create(), true ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Sticky menu
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_sticky( $type = '' ) {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$option = $this->options['header_sticky'];

		$header_sticky = new Basement_Form_Input_Radio_Group( array(
				'name'          => $option,
				'id'            => $option,
				'current_value' => esc_attr( get_option( $option, 'enable' ) ),
				'values'        => array(
					'enable'  => __( 'Enable', BASEMENT_TEXTDOMAIN ),
					'disable' => __( 'Disable', BASEMENT_TEXTDOMAIN )
				)
			)
		);

		$container = $dom->appendChild( $dom->importNode( $header_sticky->create(), true ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		return $container;
	}


	/**
	 * Text logo
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function text_logo( $type = '' ) {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$logo_text = new Basement_Form_Input( array(
			'label_text' => __( 'Text', BASEMENT_TEXTDOMAIN ),
			'name'       => $this->options['logo_text'],
			'value'      => esc_attr( get_option( $this->options['logo_text'], 'Coñico' ) )
		) );

		$container_logo_text = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text->setAttribute( 'style', 'width:150px;margin-bottom:17px;display:inline-block;vertical-align:top;margin-right:16px;' );
		$container_logo_text->appendChild( $dom->importNode( $logo_text->create(), true ) );


		$logo_text_size = new Basement_Form_Input( array(
			'label_text' => __( 'Text size in px', BASEMENT_TEXTDOMAIN ),
			'type'       => 'number',
			'style' => 'width:100%;',
			'min' => '1',
			'name'       => $this->options['logo_text_size'],
			'value'      => esc_attr( get_option( $this->options['logo_text_size'], '' ) )
		) );

		$container_logo_text_size = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text_size->setAttribute( 'style', 'width:100px;margin-bottom:17px;display:inline-block;vertical-align:top;margin-right:16px;' );
		$container_logo_text_size->appendChild( $dom->importNode( $logo_text_size->create(), true ) );


		$logo_text_color = new Basement_Form_Input_Colorpicker( array(
			'label_text' => __( 'Text color', BASEMENT_TEXTDOMAIN ),
			'name'       => $this->options['logo_text_color'],
			'value'      => esc_attr( get_option( $this->options['logo_text_color'], '' ) )
		) );


		$container_logo_text_color = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text_color->setAttribute( 'style', 'width:100px;display:inline-block;vertical-align:top;' );
		$container_logo_text_color->appendChild( $dom->importNode( $logo_text_color->create(), true ) );

		return $container;
	}


	/**
	 * Image logo
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function image_logo( $type = '' ) {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$image     = new Basement_Form_Input_Image( array(
				'name'              => $this->options['logo_image'],
				'value'             => esc_attr( get_option( $this->options['logo_image'] ) ),
				'text_buttons'      => true,
				'upload_text'       => __( 'Set logotype image', BASEMENT_TEXTDOMAIN ),
				'delete_text'       => __( 'Remove logotype image', BASEMENT_TEXTDOMAIN ),
				'frame_title'       => __( 'Set logotype image', BASEMENT_TEXTDOMAIN ),
				'frame_button_text' => __( 'Set logotype image', BASEMENT_TEXTDOMAIN ),
			)
		);
		$container = $dom->appendChild( $dom->importNode( $image->create(), true ) );

		return $container;
	}


	/**
	 * Link logo
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function link_logo( $type = '' ) {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$logo_link = new Basement_Form_Input( array(
			'attributes' => array(
				'placeholder' => 'http://',
			),
			'name'       => $this->options['logo_link'],
			'value'      => esc_url( get_option( $this->options['logo_link'] ) )
		) );


		$container_logo_link = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_link->setAttribute( 'style', 'margin-bottom:17px;' );
		$container_logo_link->appendChild( $dom->importNode( $logo_link->create(), true ) );


		$logo_link_toggle           = new Basement_Form_Input_Radio_Group( array(
				'label_text'    => __( 'Enable link?', BASEMENT_TEXTDOMAIN ),
				'name'          => $this->options['logo_link_toggle'],
				'id'            => $this->options['logo_link_toggle'],
				'current_value' => esc_attr( get_option( $this->options['logo_link_toggle'], 'yes' ) ),
				'values'        => array(
					'yes' => __( 'Yes', BASEMENT_TEXTDOMAIN ),
					'no'  => __( 'No', BASEMENT_TEXTDOMAIN )
				)
			)
		);
		$container_logo_link_toggle = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_link_toggle->setAttribute( 'class', 'basement_block-toggler' );
		$container_logo_link_toggle->appendChild( $dom->importNode( $logo_link_toggle->create(), true ) );


		$style = $container->appendChild( $dom->createElement( 'style', '.basement_block-toggler .basement_form_radios > label {margin-bottom: 5px;display:block;} .basement_block-toggler .basement_form_radios_wrapper > label, .basement_block-toggler .basement_form_checkboxes_wrapper > label {display: block;font-weight: 700;margin-bottom: 10px;}' ) );
		$style->setAttribute( 'type', 'text/css' );

		return $container;
	}


	/**
	 * Logo position
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function position_logo( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$params        = array(
			'values' => array(
				'left'         => __( 'Left', BASEMENT_TEXTDOMAIN ),
				'right'        => __( 'Right', BASEMENT_TEXTDOMAIN ),
				'center_left'  => __( 'Center (menu on the left)', BASEMENT_TEXTDOMAIN ),
				'center_right' => __( 'Center (menu on the right)', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'left';


		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_header' . substr( $this->options['logo_position'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['logo_position'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;


		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$header_off = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $header_off->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Header Size
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_size( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$params        = array(
			'values' => array(
				'fullwidth'        => __( 'Full Width', BASEMENT_TEXTDOMAIN ),
				'boxed'         => __( 'Boxed', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'fullwidth';


		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_header' . substr( $this->options['header_size'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['header_size'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;


		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$header_off = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $header_off->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Disable header
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_off( $type = '' ) {
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
			$option                  = '_basement_meta_header' . substr( $this->options['header_off'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['header_off'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$header_off = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $header_off->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}



	/**
	 * Automatically calculates the height of the header.
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_helper( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$params        = array(
			'values' => array(
				'no'  => __( 'Disable', BASEMENT_TEXTDOMAIN ),
				'yes' => __( 'Enable', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'no';


		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_header' . substr( $this->options['header_helper'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['header_helper'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$header_off = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $header_off->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Header Element
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_elements( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$params = array(
			'values' => array(
				'logo_image'     => __( 'Logo Image', BASEMENT_TEXTDOMAIN ),
				'logo_text'      => __( 'Logo Text', BASEMENT_TEXTDOMAIN ),
				'menu'           => __( 'Menu', BASEMENT_TEXTDOMAIN ),
				'search_section' => __( 'Search Section', BASEMENT_TEXTDOMAIN ),
				'button_section' => __( 'Button', BASEMENT_TEXTDOMAIN ),
				'user_section' => __( 'Account Section', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = array(
			'logo_text',
			'menu',
			'search_section',
			'button_section'
		);


		/*if ( Basement_Ecommerce_Woocommerce::enabled() ) {
			$params['values'] = array_slice( $params['values'], 0, 4, true ) + array(
					'shop_section' => __( 'Shop Section', BASEMENT_TEXTDOMAIN )
				) + array_slice( $params['values'], 4, count( $params['values'] ) - 1, true );

			array_splice( $current_param, 1, 0, array( 'shop_section' ) );
		}*/


		if ( $this->wpml_enable() ) {
			$params['values']['lang_section'] = __( 'Language Section', BASEMENT_TEXTDOMAIN );
			array_push($current_param, 'lang_section');
		}



		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_header' . substr( $this->options['header_elements'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['header_elements'];
			$option_value            = get_option( $option );
			$params['current_value'] = empty( $option_value ) ? $current_param : $option_value;
		}

		$params['id']         = $option;
		$params['name']       = $option;
		$params['label_text'] = __( 'Select the items to display:', BASEMENT_TEXTDOMAIN );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$header_elements = new Basement_Form_Input_Checkbox_Group( $params );

		$container->appendChild( $dom->importNode( $header_elements->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Header style
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_style( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$params = array(
			'values' => array(
				'dark'             => __( 'Dark', BASEMENT_TEXTDOMAIN ),
				'white'            => __( 'Light', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'dark';

		if ( $type === 'metabox' ) {
			$option                  = '_basement_meta_header' . substr( $this->options['header_style'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$option                  = $this->options['header_style'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;


		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_block-toggler' );

		$header_off = new Basement_Form_Input_Radio_Group( $params );

		$container->appendChild( $dom->importNode( $header_off->create(), true ) );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Header background
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_bg( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$label_bg = __( 'Background Color', BASEMENT_TEXTDOMAIN );
		$label_opacity = __( 'Opacity', BASEMENT_TEXTDOMAIN );

		$name_bg = $this->options['header_bg'];
		$name_opacity = $this->options['header_opacity'];

		if ( $type === 'metabox' ) {
			$name_bg = '_basement_meta_header' . substr( $name_bg, 18 );
			$name_opacity = '_basement_meta_header' . substr( $name_opacity, 18 );
			$value_bg  = get_post_meta( $post->ID, $name_bg, true );
			$value_opacity = get_post_meta( $post->ID, $name_opacity, true );
		} else {
			$value_bg = esc_attr( get_option( $name_bg ) );
			$value_opacity = esc_attr( get_option( $name_opacity ) );

		}

		$logo_text_color = new Basement_Form_Input_Colorpicker( array(
			'label_text' => $label_bg,
			'name'  => $name_bg,
			'value' => $value_bg
		) );


		$logo_text = new Basement_Form_Input( array(
			'label_text' => $label_opacity,
			'type' => 'number',
			'name'  => $name_opacity,
			'value' => $value_opacity,
			'min' => '0',
			'max' => '1',
			'step' => '0.1'
		) );


		$container_logo_text_color = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text_color->setAttribute( 'style', 'width:150px;margin-bottom:17px;display:inline-block;vertical-align:top;margin-right:16px;' );
		$container_logo_text_color->appendChild( $dom->importNode( $logo_text_color->create(), true ) );

		$container_logo_text = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text->setAttribute( 'style', 'width:150px;display:inline-block;vertical-align:top;' );
		$container_logo_text->appendChild( $dom->importNode( $logo_text->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Border For Header
	 *
	 * @param string $type
	 *
	 * @return DOMNode|string
	 */
	public function header_border( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$label_bg = __( 'Color', BASEMENT_TEXTDOMAIN );
		$label_opacity = __( 'Opacity', BASEMENT_TEXTDOMAIN );

		$name_bg = $this->options['header_border_bg'];
		$name_opacity = $this->options['header_border_opacity'];

		$params = array(
			'label_text' => __('Border width', BASEMENT_TEXTDOMAIN),
			'values' => array(
				'fullwidth'             => __( 'Fullwidth', BASEMENT_TEXTDOMAIN ),
				'boxed'            => __( 'Boxed', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'fullwidth';

		if ( $type === 'metabox' ) {
			$name_bg = '_basement_meta_header' . substr( $name_bg, 18 );
			$name_opacity = '_basement_meta_header' . substr( $name_opacity, 18 );
			$value_bg  = get_post_meta( $post->ID, $name_bg, true );
			$value_opacity = get_post_meta( $post->ID, $name_opacity, true );

			$option                  = '_basement_meta_header' . substr( $this->options['header_border_size'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$value_bg = esc_attr( get_option( $name_bg ) );
			$value_opacity = esc_attr( get_option( $name_opacity ) );

			$option                  = $this->options['header_border_size'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;

		$logo_text_color = new Basement_Form_Input_Colorpicker( array(
			'label_text' => $label_bg,
			'name'  => $name_bg,
			'value' => $value_bg
		) );


		$logo_text = new Basement_Form_Input( array(
			'label_text' => $label_opacity,
			'type' => 'number',
			'name'  => $name_opacity,
			'value' => $value_opacity,
			'min' => '0',
			'max' => '1',
			'step' => '0.1'
		) );


		$container_logo_text_color = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text_color->setAttribute( 'style', 'width:150px;margin-bottom:17px;display:inline-block;vertical-align:top;margin-right:16px;' );
		$container_logo_text_color->appendChild( $dom->importNode( $logo_text_color->create(), true ) );


		$container_logo_text = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text->setAttribute( 'style', 'width:150px;display:inline-block;vertical-align:top;' );
		$container_logo_text->appendChild( $dom->importNode( $logo_text->create(), true ) );


		$container_size = $container->appendChild( $dom->createElement( 'div' ) );
		$container_size->setAttribute( 'class', 'basement_block-toggler' );

		$header_off = new Basement_Form_Input_Radio_Group( $params );

		$container_size->appendChild( $dom->importNode( $header_off->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Header padding
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_padding( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$label_pt = __( 'Padding top', BASEMENT_TEXTDOMAIN );
		$label_pb = __( 'Padding Bottom', BASEMENT_TEXTDOMAIN );

		$name_pt = $this->options['header_padding_top'];
		$name_pb = $this->options['header_padding_bottom'];

		if ( $type === 'metabox' ) {
			$name_pt = '_basement_meta_header' . substr( $name_pt, 18 );
			$name_pb = '_basement_meta_header' . substr( $name_pb, 18 );
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



	/**
	 * Setting for Header Button
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_button( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$label_bt = __( 'Text', BASEMENT_TEXTDOMAIN );
		$label_bl = __( 'Link', BASEMENT_TEXTDOMAIN );
		$label_bi = __( 'Icon', BASEMENT_TEXTDOMAIN );

		$name_bt = $this->options['header_btn_text'];
		$name_bl = $this->options['header_btn_link'];
		$name_bi = $this->options['header_btn_icon'];

		if ( $type === 'metabox' ) {
			$name_bt = '_basement_meta_header' . substr( $name_bt, 18 );
			$name_bl = '_basement_meta_header' . substr( $name_bl, 18 );
			$name_bi = '_basement_meta_header' . substr( $name_bi, 18 );
			$value_bt  = get_post_meta( $post->ID, $name_bt, true );
			$value_bl = get_post_meta( $post->ID, $name_bl, true );
			$value_bi = get_post_meta( $post->ID, $name_bi, true );
		} else {
			$value_bt = get_option( $name_bt, apply_filters('basement_default_btn_header_text',__('Contact',BASEMENT_TEXTDOMAIN) ) );
			$value_bl = get_option( $name_bl , apply_filters('basement_default_btn_header_link', '#basement-modal-840' ) );
			$value_bi = get_option( $name_bi , apply_filters('basement_default_btn_header_icon', 'icon-mail' ) );
		}

		$input_bt = new Basement_Form_Input( array(
			'label_text' => $label_bt,
			'name'  => $name_bt,
			'value' => $value_bt,
			'style'=> 'width:100%;'
		) );


		$input_bl = new Basement_Form_Input( array(
			'label_text' => $label_bl,
			'name'  => $name_bl,
			'attributes' => array(
					'placeholder' => 'http://'
			),
			'value' => $value_bl,
			'style'=> 'width:100%;'
		) );

		$input_bi = new Basement_Form_Input( array(
			'label_text' => $label_bi,
			'name'  => $name_bi,
			'help_text' => __('Use only class names from icons library for this field.', BASEMENT_TEXTDOMAIN),
			'help_icon' => 'icons',
			'value' => $value_bi,
			'attributes' => array(
					'placeholder' => __('e.g.: fa fa-user', BASEMENT_TEXTDOMAIN)
			),
			'style'=> 'width:100%;'
		) );



		$button_text = $container->appendChild( $dom->createElement( 'div' ) );
		$button_text->setAttribute( 'style', 'width:320px;margin-bottom:17px;' );
		$button_text->appendChild( $dom->importNode( $input_bt->create(), true ) );

		$button_link = $container->appendChild( $dom->createElement( 'div' ) );
		$button_link->setAttribute( 'style', 'width:320px;margin-bottom:17px;' );
		$button_link->appendChild( $dom->importNode( $input_bl->create(), true ) );


		$button_icon = $container->appendChild( $dom->createElement( 'div' ) );
		$button_icon->setAttribute( 'style', 'width:320px;' );
		$button_icon->appendChild( $dom->importNode( $input_bi->create(), true ) );


		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}


	/**
	 * Setting for Header Global Border
	 *
	 * @param string $type
	 *
	 * @return DOMNode
	 */
	public function header_global_border( $type = '' ) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$label_bg = __( 'Color', BASEMENT_TEXTDOMAIN );
		$label_opacity = __( 'Weight in px', BASEMENT_TEXTDOMAIN );

		$name_bg = $this->options['header_global_border_color'];
		$name_opacity = $this->options['header_global_border_size'];

		$params = array(
			'label_text' => __('Enable border:', BASEMENT_TEXTDOMAIN),
			'values' => array(
				'no'            => __( 'No', BASEMENT_TEXTDOMAIN ),
				'yes'             => __( 'Yes', BASEMENT_TEXTDOMAIN )
			)
		);
		$current_param = 'no';

		if ( $type === 'metabox' ) {
			$name_bg = '_basement_meta_header' . substr( $name_bg, 18 );
			$name_opacity = '_basement_meta_header' . substr( $name_opacity, 18 );
			$value_bg  = get_post_meta( $post->ID, $name_bg, true );
			$value_opacity = get_post_meta( $post->ID, $name_opacity, true );

			$option                  = '_basement_meta_header' . substr( $this->options['header_global_border'], 18 );
			$post_value              = get_post_meta( $post->ID, $option, true );
			$params['current_value'] = empty( $post_value ) ? $current_param : $post_value;
		} else {
			$value_bg = esc_attr( get_option( $name_bg ) );
			$value_opacity = esc_attr( get_option( $name_opacity ) );

			$option                  = $this->options['header_global_border'];
			$option_value            = get_option( $option, $current_param );
			$params['current_value'] = $option_value;
		}

		$params['id']   = $option;
		$params['name'] = $option;

		$logo_text_color = new Basement_Form_Input_Colorpicker( array(
			'label_text' => $label_bg,
			'name'  => $name_bg,
			'value' => $value_bg
		) );


		$logo_text = new Basement_Form_Input( array(
			'label_text' => $label_opacity,
			'type' => 'number',
			'name'  => $name_opacity,
			'value' => $value_opacity,
			'min' => '0',
			'step' => '1'
		) );


		$container_size = $container->appendChild( $dom->createElement( 'div' ) );
		$container_size->setAttribute( 'class', 'basement_block-toggler' );
		$container_size->setAttribute( 'style', 'margin-bottom:17px;' );
		$header_off = new Basement_Form_Input_Radio_Group( $params );

		$container_size->appendChild( $dom->importNode( $header_off->create(), true ) );


		$container_logo_text_color = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text_color->setAttribute( 'style', 'width:150px;margin-bottom:17px;display:inline-block;vertical-align:top;margin-right:16px;' );
		$container_logo_text_color->appendChild( $dom->importNode( $logo_text_color->create(), true ) );


		$container_logo_text = $container->appendChild( $dom->createElement( 'div' ) );
		$container_logo_text->appendChild( $dom->importNode( $logo_text->create(), true ) );
		$container_logo_text->setAttribute( 'style', 'width:150px;margin-bottom:17px;display:inline-block;vertical-align:top;margin-right:16px;' );

		if ( $type === 'metabox' ) {
			return $dom->saveHTML( $container );
		} else {
			return $container;
		}
	}

}


if ( ! function_exists( 'basement_header_class' ) ) {
	/**
	 * Display the classes for the header element.
	 *
	 * @param string $class
	 * @param bool   $echo
	 *
	 * @return array
	 */
	function basement_header_class( $class = '', $echo = true ) {
		$sticky_option   = '';
		$basement_header = new Basement_Header();
		$settings        = $basement_header->front_get_settings_options();

		$pagetitle = new Basement_Pagetitle();
		$settings_pt  = $pagetitle->get_pagetitle_settings();

		$bg             = ! empty( $settings['header_bg'] ) ? $settings['header_bg'] : '';
		$opacity        = is_numeric( $settings['header_opacity'] ) ? $settings['header_opacity'] : '';
		$padding_top    = is_numeric( $settings['header_padding_top'] ) ? $settings['header_padding_top'] : '';
		$padding_bottom = is_numeric( $settings['header_padding_bottom'] ) ? $settings['header_padding_bottom'] : '';

		$pt_placement = isset($settings_pt['pt_placement']) ? $settings_pt['pt_placement'] : '';
		$pt_off = isset($settings_pt['pt_off']) ? $settings_pt['pt_off'] : '';


		$id = get_the_ID();
		$custom_pagetitle = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );
		$custom_header = get_post_meta( $id, '_basement_meta_custom_header', true );

		if( ( is_singular('post') ) && empty($custom_pagetitle)) {
			$pt_placement = 'under';
		}


		if ( ! empty( $bg ) ) {
			$bg = basement_hexToRgb( $bg );
			$bg = ! empty( $bg ) ? $bg['red'] . ',' . $bg['green'] . ',' . $bg['blue'] : '';
		}

		#if ( ( $settings['header_sticky'] !== 'disable' ) || ( ! empty( $pt_placement ) && $pt_placement == 'under' && $pt_off == 'no' ) ) {
		if ( $settings['header_sticky'] !== 'disable' ) {
			$sticky_option = ' data-spy="affix" data-offset-top="10" ';
		}

		
		$style      = array();
		$style_attr = '';
		$data_attr  = '';
		if ( ! empty( $bg ) ) {
			$end_opacity = ',1';
			if($opacity !== '') {
				$end_opacity = ",{$opacity}";
			}
			$style[]   = "background:rgba({$bg}{$end_opacity});";
			$data_attr = " data-bgparams=\"{$bg}{$end_opacity}\" "; // this required param for JS scroll
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

		$blog_classes = implode( ' ', basement_get_header_class( $class ) );

		if($echo) {
			// Separates classes with a single space, collates classes for header element
			echo 'class="' . $blog_classes . '"' . $sticky_option . $style_attr . $data_attr;
		} else {
			return array(
				'class' => 	 basement_get_header_class( $class ),
				'sticky' => $sticky_option,
				'style' => $style_attr,
				'data' => $data_attr
			);
		}

	}
}


if ( ! function_exists( 'basement_action_theme_after_nav' ) ) {
	/**
	 * Displays elements after Navigation
	 */
	function basement_action_theme_after_nav() {
		$basement_header = new Basement_Header();
		$settings  = $basement_header->front_get_settings_options();

		$bg = !empty($settings['header_border_bg']) ? $settings['header_border_bg'] : '';
		$opacity = is_numeric($settings['header_border_opacity']) ? $settings['header_border_opacity'] : '';
		$size = !empty($settings['header_border_size']) ? $settings['header_border_size'] : '';

		if(!empty($bg)) {
			$bg = basement_hexToRgb($bg);
			$bg = !empty($bg) ? $bg['red'] .','. $bg['green'] .','.$bg['blue'] : '';
		}

		$style = array();
		$style_attr = '';

		if ( ! empty( $bg ) ) {
			$end_opacity = ',1';
			if($opacity !== '') {
				$end_opacity = ",{$opacity}";
			}
			$style[] = "background:rgba({$bg}{$end_opacity});";
		}

		if(!empty($style)) {
			$style = implode(' ', $style);
			$style_attr = " style=\"{$style}\" ";
		}

		$class = '';
		if(!empty($size) && $size === 'boxed') {
			$class = 'class="container"';
		}

		echo "<div class=\"header-border-wrapper\"><div {$class}><div {$style_attr} class=\"header-border\"></div></div></div>";
	}

	add_action( 'conico_after_nav', 'basement_action_theme_after_nav' );
}


if ( ! function_exists( 'basement_hexToRgb' ) ) {
	function basement_hexToRgb( $color ) {

		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}


		if ( strlen( $color ) == 6 ) {
			list( $red, $green, $blue ) = array (
				$color[0] . $color[1],
				$color[2] . $color[3],
				$color[4] . $color[5]
			);
		} elseif ( strlen( $cvet ) == 3 ) {
			list( $red, $green, $blue ) = array (
				$color[0] . $color[0],
				$color[1] . $color[1],
				$color[2] . $color[2]
			);
		} else {
			return false;
		}

		$red = hexdec( $red );
		$green = hexdec( $green );
		$blue = hexdec( $blue );

		return array (
			'red'   => $red,
			'green' => $green,
			'blue'  => $blue
		);
	}
}


if ( ! function_exists( 'basement_get_header_class' ) ) {
	/**
	 * Retrieve the classes for the header element as an array.
	 *
	 * @param string $class
	 *
	 * @return array
	 */
	function basement_get_header_class( $class = '' ) {

		$classes = array();


		$basement_header = new Basement_Header();
		$classes_string = $basement_header->front_classes_header();

		$id = get_the_ID();
		$pagetitle = new Basement_Pagetitle();
		$settings_pt  = $pagetitle->get_pagetitle_settings();


		$pt_placement = isset($settings_pt['pt_placement']) ? $settings_pt['pt_placement'] : '';
		$pt_off = isset($settings_pt['pt_off']) ? $settings_pt['pt_off'] : '';



		$custom_pagetitle = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );

		if( ( is_singular('post') ) && empty($custom_pagetitle)) {
			$pt_placement = 'under';
		}

		/*if ( ! empty( $pt_placement ) && $pt_placement === 'under' && $pt_off == 'no' ) {
			$classes_string = preg_replace( '/header_sticky_disable/', 'header_sticky_enable', $classes_string );
		}*/

		$classes[] = $classes_string;

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

		$classes = apply_filters( 'header_class', $classes, $class );

		return array_unique( $classes );
	}
}


if ( ! function_exists( 'basement_header_sort_elements' ) ) {
	/**
	 * Output template parts elements in header
	 */
	function basement_header_sort_elements() {
		$basement_header = new Basement_Header();
		$settings = $basement_header->front_get_settings_options();

		/*
		 * logo_image
		 * logo_text
		 * menu
		 * user_section
		 * shop_section
		 * lang_section
		 * search_section
		 */

		$logo      = !empty($settings['header_elements']['logo_image']) ? $settings['header_elements']['logo_image'] : '';
		$logo_text = !empty($settings['header_elements']['logo_text']) ? $settings['header_elements']['logo_text'] : '';
		$menu      = !empty($settings['header_elements']['menu']) ? $settings['header_elements']['menu'] : 'default';
		$user      =  isset($settings['header_elements']['user_section']) ? $settings['header_elements']['user_section'] : '';
		$shop      = isset($settings['header_elements']['shop_section']) ? $settings['header_elements']['shop_section'] : '';
		$lang      = isset($settings['header_elements']['lang_section']) || defined('FAKE_WPML') ? $settings['header_elements']['lang_section'] : '';
		$search    = !empty($settings['header_elements']['search_section']) ? $settings['header_elements']['search_section'] : '';
		$button    = !empty($settings['header_elements']['button_section']) ? $settings['header_elements']['button_section'] : '';


		// menu on the left
		if ( $settings['logo_position'] === 'center_left' ) {
			echo '<div class="row">';
			echo '<div class="col-md-2 col-sm-12 no-padding text-center logo-block-center-left hidden visible-sm visible-xs">';
			if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
				get_template_part( 'template-parts/header/logo' );
			}
			echo '</div>';
			echo '<div class="col-md-5 col-sm-1 col-xs-2 clearfix head-col" style="position: static;">';
			if ( ! empty( $menu ) ) {
				get_template_part( 'template-parts/header/menu' );
			}
			echo '</div>';

				echo '<div class="col-md-2 col-sm-12 no-padding text-center logo-block-center-left hidden-sm hidden-xs">';
					if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
						get_template_part( 'template-parts/header/logo' );
					}
				echo '</div>';


			echo '<div class="col-md-5 col-sm-11 col-xs-10 head-col">';
			if ( ! empty( $lang ) ) {
				get_template_part( 'template-parts/header/lang' );
			}

			if(!empty($button)) {
				get_template_part( 'template-parts/header/button' );
			}
			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search' );
			}
			if ( ! empty( $user ) ) {
				get_template_part( 'template-parts/header/account' );
			}

			if ( ! empty( $shop ) ) {
				get_template_part( 'template-parts/header/shop' );
			}


			echo '</div>';

			echo '</div>';
			/*if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search-form' );
			}*/
			//menu on the right
		} elseif ( $settings['logo_position'] === 'center_right' ) {
			echo '<div class="row">';
				echo '<div class="col-md-2 col-md-push-5 col-sm-12 no-padding text-center">';
					if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
						get_template_part( 'template-parts/header/logo' );
					}
				echo '</div>';

				echo '<div class="col-md-5 col-md-pull-2 col-sm-11 col-xs-10 head-col">';
					if ( ! empty( $lang ) ) {
						get_template_part( 'template-parts/header/lang' );
					}


					if(!empty($button)) {
						get_template_part( 'template-parts/header/button' );
					}

			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search' );
			}
			if ( ! empty( $user ) ) {
				get_template_part( 'template-parts/header/account' );
			}

					if ( ! empty( $shop ) ) {
						get_template_part( 'template-parts/header/shop' );
					}


				echo '</div>';

				echo '<div class="col-md-5 col-sm-1 col-xs-2 head-col" style="position: static;">';
					if ( ! empty( $menu ) ) {
						get_template_part( 'template-parts/header/menu' );
					}
				echo '</div>';

			echo '</div>';
			/*if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search-form' );
			}*/
		} elseif ( $settings['logo_position'] === 'left' ) {
			/*echo '<div class="row header-left-row-elements">';
				echo '<div class="col-xs-6 col-sm-6 col-md-3 clearfix header-left-cell">';*/

				/*if ( ! empty( $search ) ) {
					get_template_part( 'template-parts/header/search-form' );
				}*/

				if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
					get_template_part( 'template-parts/header/logo' );
				}

			if ( ! empty( $menu ) ) {
				echo '<div class="navbar-divider pull-left"></div>';
				get_template_part( 'template-parts/header/menu' );
			}

				if ( ! empty( $lang ) ) {
					get_template_part( 'template-parts/header/lang' );
				}



				if(!empty($button)) {
					get_template_part( 'template-parts/header/button' );
				}

			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search' );
			}

			if ( ! empty( $user ) ) {
				get_template_part( 'template-parts/header/account' );
			}

				if ( ! empty( $shop ) ) {
					get_template_part( 'template-parts/header/shop' );
				}



		} elseif ( $settings['logo_position'] === 'right' ) {
			#echo '<div class="row">';

			#echo '<div class="col-md-3 col-md-push-9">';
				if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
					get_template_part( 'template-parts/header/logo' );
				}
			#echo '</div>';

			if ( ! empty( $menu ) ) {
				echo '<div class="navbar-divider pull-right"></div>';
				get_template_part( 'template-parts/header/menu' );
			}

			#echo '<div class="col-md-9 col-md-pull-3">';
				if ( ! empty( $lang ) ) {
					get_template_part( 'template-parts/header/lang' );
				}


				if(!empty($button)) {
					get_template_part( 'template-parts/header/button' );
				}

			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search' );
			}

			if ( ! empty( $user ) ) {
				get_template_part( 'template-parts/header/account' );
			}

				if ( ! empty( $shop ) ) {
					get_template_part( 'template-parts/header/shop' );
				}



			#echo '</div>';

			#echo '</div>';

			/*if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search-form' );
			}*/
		}
	}
	add_action( 'conico_content_header', 'basement_header_sort_elements', 10 );
}


if ( ! function_exists( 'basement_button' ) ) {
	/**
	 * Displays Button In Header
	 */
	function basement_button() {
		$basement_header = new Basement_Header();
		$settings = $basement_header->front_get_settings_options();

		$header_btn_text = ! empty( $settings['header_btn_text'] ) ? ' '.$settings['header_btn_text'] : '';
		$header_btn_icon = ! empty( $settings['header_btn_icon'] ) ? '<i class="'.esc_attr($settings['header_btn_icon']).'"></i>' : '';
		$header_btn_link = ! empty( $settings['header_btn_link'] ) ? $settings['header_btn_link'] : '#';

		if(!empty($header_btn_text) || !empty($header_btn_icon)) {
			?>
			<a href="<?php echo esc_url($header_btn_link); ?>" title="<?php echo esc_attr($header_btn_text); ?>"><?php echo $header_btn_icon; echo esc_html($header_btn_text); ?></a>
			<?php
		}
	}
}


if ( ! function_exists( 'basement_logo' ) ) {
	/**
	 * Displays Logo (Text/Image)
	 */
	function basement_logo() {
		$basement_header = new Basement_Header();
		$settings        = $basement_header->front_get_settings_options();

		$brand_classes = array( 'navbar-brand' );

		if ( isset( $settings['logo_position'] ) ) {
			switch ( $settings['logo_position'] ) {
				case 'left' :
					$brand_classes[] = 'pull-left';
					break;
				case 'right' :
					$brand_classes[] = 'pull-right';
					break;
			}
		}

		$style_logo = '';

		$title_logo = apply_filters( 'header_title_logo', esc_attr( get_bloginfo( 'name', 'display' ) ) );
		$size       = empty( $settings['logo_text_size'] ) ? '' : 'font-size:' . abs( $settings['logo_text_size'] ) . 'px;';
		$color      = empty( $settings['logo_text_color'] ) ? '' : 'color:' . $settings['logo_text_color'] . ';';

		if ( ! empty( $size ) || ! empty( $color ) ) {
			$style_logo = 'style="' . $size . $color . '"';
		}

		$text   = empty( $settings['logo_text'] ) || empty( $settings['header_elements']['logo_text'] ) ? '' : esc_attr( $settings['logo_text'] );
		$image_src = wp_get_attachment_image_url( $settings['logo_image'], 'full' );
		$image  = empty( $settings['logo_image'] ) || empty( $settings['header_elements']['logo_image'] ) ? '' : '<img src="' . esc_url($image_src) . '" alt="' . esc_attr($title_logo) . '">';
		$link   = empty( $settings['logo_link'] ) ? esc_url( home_url( '/' ) ) : esc_url( $settings['logo_link'] );
		$toggle = !empty($settings['logo_link_toggle']) ? $settings['logo_link_toggle'] : '';


		$logo_slug = $image . ' ' . esc_html($text);

		if ( ! empty( $settings['logo_text'] ) || ! empty( $settings['logo_image'] ) ) {
			if ( $toggle === 'yes' ) {
				$logo = '<div class="' . esc_attr( implode( ' ', $brand_classes ) ) . '"><a ' . $style_logo . '  href="' . esc_url( $link ) . '" title="' . esc_attr( $title_logo ) . '">' . $logo_slug . '</a></div>';
			} else {
				$brand_classes[] = 'basement-disabled-brand';
				$logo = '<div class="' . esc_attr( implode( ' ', $brand_classes ) ) . '"><span' . $style_logo . ' >' .  $logo_slug . '</span></div>';
			}
		} else {
			$logo = '';
		}

		echo apply_filters( 'basement_header_logo', $logo );
	}
}


if ( ! function_exists( 'basement_action_theme_before_wrapper' ) ) {
	/**
	 * Displays params before Main Wrapper
	 */
	function basement_action_theme_before_wrapper() {
		$basement_header_settings = new Basement_Header();
		$basement_header = $basement_header_settings->front_get_settings_options();


		if(isset($basement_header['header_off']) && $basement_header['header_off'] === 'no') {


			$menu_type = isset( $basement_header['menu_type'] ) ? $basement_header['menu_type'] : '';

			$global_border       = isset( $basement_header['header_global_border'] ) ? $basement_header['header_global_border'] : '';
			$global_border_size  = isset( $basement_header['header_global_border_size'] ) ? $basement_header['header_global_border_size'] : '';
			$global_border_color = isset( $basement_header['header_global_border_color'] ) ? $basement_header['header_global_border_color'] : '';


			$search = ! empty( $basement_header['header_elements']['search_section'] ) ? $basement_header['header_elements']['search_section'] : '';


			get_template_part( 'template-parts/header/menu-simple' );

			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search-form' );
			}

			if ( 'yes' === $global_border ) {
				$border_styles = array();
				if ( is_numeric( $global_border_size ) ) {
					$border_styles[] = "border-width:{$global_border_size}px;";
				}
				if ( ! empty( $global_border_color ) ) {
					$border_styles[] = "border-color:{$global_border_color};";
				}

				if ( ! empty( $border_styles ) ) {
					$border_styles = 'style="' . implode( '', $border_styles ) . '"';
				} else {
					$border_styles = '';
				}
				echo '<div class="header-global-borders" ' . $border_styles . '></div>';
			}

		}
	}
	add_action('conico_before_wrapper', 'basement_action_theme_before_wrapper');
}


if ( ! function_exists( 'basement_action_theme_before_header' ) ) {
	/**
	 * Displays params before Header
	 */
	function basement_action_theme_before_header() {
		ob_start();
	}
	add_action('conico_before_header', 'basement_action_theme_before_header');
}


if ( ! function_exists( 'basement_action_header_body_class' ) ) {
	/**
	 * Added classes for Header
	 */
	function basement_action_header_body_class( $classes, $class ) {
		$basement_header_settings = new Basement_Header();
		$basement_header          = $basement_header_settings->front_get_settings_options();
		$menu_type                = isset( $basement_header['menu_type'] ) ? $basement_header['menu_type'] : '';
		$header_sticky            = isset( $basement_header['header_sticky'] ) ? $basement_header['header_sticky'] : '';

		if ( ! empty( $header_sticky ) ) {
			$classes[] = "basement-{$header_sticky}-sticky";
		}

		if ( ! empty( $menu_type ) ) {
			$classes[] = "basement-{$menu_type}-menu";
		}

		return $classes;
	}

	add_filter( 'body_class', 'basement_action_header_body_class', 10, 2 );
}


if ( ! function_exists( 'basement_action_theme_after_header' ) ) {
	/**
	 * Displays params after Header
	 */
	function basement_action_theme_after_header() {
		$basement_header_settings = new Basement_Header();
		$basement_header = $basement_header_settings->front_get_settings_options();
		$helper = '';
		$sticky = !empty($basement_header['header_sticky']) ? $basement_header['header_sticky'] : '';
		$header_off = isset($basement_header['header_off']) ? $basement_header['header_off'] : '';
		$header_helper = isset($basement_header['header_helper']) ? $basement_header['header_helper'] : '';
		$header_elements = isset($basement_header['header_elements']) ? $basement_header['header_elements'] : array();
		$header = ob_get_contents();
		ob_end_clean();


		$id = get_the_ID();
		$pagetitle = new Basement_Pagetitle();
		$settings_pt  = $pagetitle->get_pagetitle_settings();


		$pt_placement = isset($settings_pt['pt_placement']) ? $settings_pt['pt_placement'] : '';
		$pt_off = isset($settings_pt['pt_off']) ? $settings_pt['pt_off'] : '';

		$custom_pagetitle = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );

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


		if( ( $pt_placement === 'after' && $pt_off === 'no' && $revslider_position !== 'header_content' ) || ( $pt_off === 'yes' && $revslider_position === 'before_content' ) || $header_helper === 'yes' ) {
			$helper = apply_filters('basement_header_helper','<div class="header-helper"></div>');
		}

		if ( isset( $basement_header ) && ( $header_off == 'no' && array_filter( $header_elements ) ) ) {
			echo $header . $helper;
		}

	}
	add_action('conico_after_header', 'basement_action_theme_after_header');
}



if ( ! function_exists( 'basement_navbar_class' ) ) {
	/**
	 * Display the classes for the navbar element.
	 *
	 * @param string $class
	 * @param bool   $echo
	 *
	 * @return array
	 */
	function basement_navbar_class( $class = '', $echo = true ) {

		$basement_header = new Basement_Header();
		$settings = $basement_header->front_get_settings_options();


		$classes = array();

		$header_size = !empty($settings['header_size']) ? $settings['header_size'] : '';

		if($header_size === 'fullwidth') {
		    $classes[] = 'container-fluid';
        } else {
			$classes[] = 'container';
        }

		$classes[] = $class;
        $class = implode(' ', $classes);

		if($echo) {
			// Separates classes with a single space, collates classes for header element
			echo esc_attr($class);
		} else {
			return $classes;
		}

	}
}



if ( ! function_exists( 'Basement_Header' ) ) {
	/**
	 * Generate Basement Header Settings
	 *
	 * @return array
	 */
	function Basement_Header() {
		$basement_header = new Basement_Header();
		return $basement_header->front_get_settings_options();
	}
}