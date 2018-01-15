<?php
defined('ABSPATH') or die();

class Basement_Modals_Cpt extends Basement_Cpt {

	private static $instance = null;

	protected $post_type = 'modals';

	protected $preview_meta_box_name = 'modals-preview-meta-box';

	private $meta = '_basement_meta_modals_';

	protected $duplicate = true;

	public function __construct() {
		parent::__construct();
		
		add_action( 'admin_head' , array(&$this, 'remove_cpt_meta_boxes' ), 99);

		add_filter( 'screen_options_show_screen', array( &$this, 'remove_screen_options' ), 99, 2 );

		add_filter('wp_editor_expand',  array( &$this, 'deregister_editor_expand' ), 10, 2);

		add_filter( 'post_updated_messages', array( &$this, 'modals_updated_messages' ) );

		add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_modals_updated_messages' ), 10, 2 );

		add_filter( 'post_row_actions', array( &$this, 'modals_actions' ), 10, 2 );

		add_action( 'add_meta_boxes', array( &$this, 'generate_modals_param_meta_box' ) );


		add_filter(
			'manage_' . $this->post_type . '_posts_columns',
			array( &$this, 'modal_add_id_column_header' ),
			10
		);

		add_action(
			'manage_' . $this->post_type . '_posts_custom_column',
			array( &$this, 'modal_add_id_column' ),
			10,
			2
		);

		if( defined('DOING_AJAX') && DOING_AJAX ) {
			add_action( 'wp_ajax_basement-modal-call', array( &$this, 'get_modal_content' ) );
			add_action( 'wp_ajax_nopriv_basement-modal-call', array( &$this, 'get_modal_content' ) );
		}
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Modals_Cpt();
		}
		return self::$instance;
	}

	/**
	 * Disable Distraction Free Writing Mode and the "Auto Expanding"
	 * height of the editor based on a list of post types.
	 *
	 * @return bool
	 */
	public function deregister_editor_expand($val, $post_type) {
		$disabled_post_types = array($this->post_type);
		return !in_array($post_type, $disabled_post_types);
	}



	public function modal_add_id_column_header( $columns ) {
		if( !isset( $columns['modal_id'] ) ) {
			$columns = array_slice( $columns, 0, 2, true ) + array( 'modal_id' => '<strong>'.__('ID', BASEMENT_MODALS_TEXTDOMAIN).'</strong>' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
		}
		return $columns;
	}

	public function modal_add_id_column($column_name, $post_id) {
		if ($column_name == 'modal_id') {
			echo '<input type="text" class="basement-modal-id-input" onfocus="this.select();" readonly="readonly" value="#basement-modal-'. $post_id.'">';
		}
	}


	/**
	 * Get Modal Content Via AJAX
	 */
	public function get_modal_content() {

		$post_id = isset( $_POST['post_id'] ) ? absint($_POST['post_id']) : '';

		$bg_name = $this->meta . 'bg_color';
		$close_name = $this->meta . 'close_color';
		$close_hover_name = $this->meta . 'close_hover_color';
		$close_icon_name = $this->meta . 'close_icon_color';
		$close_hover_icon_name = $this->meta . 'close_hover_icon_color';

		$post = get_post($post_id);

		if(!empty($post_id) && $post) {
			$content = isset( $post->post_content ) ? $post->post_content : '';
			if ( ! empty( $content ) ) {
				if ( Basement_Visualcomposer::enabled() ) {
					WPBMap::addAllMappedShortcodes();
				}

				if ( class_exists( 'Basement_Shortcodes' ) ) {
					Basement_Shortcodes::addAllMappedBasementShortcodes();
				}


				if(in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
					$content .= "<script> var getscript = true; if ( jQuery.isFunction(jQuery.fn.wpcf7InitForm) ) { getscript = false; jQuery('#basement-modal-window .wpcf7 > form').wpcf7InitForm(); var act =  jQuery('#basement-modal-window .wpcf7 > form').attr('action'); if(act) { urL = act.split('#'); jQuery('#basement-modal-window .wpcf7 > form').attr('action', \"#\" + urL[1]); } } </script>";
				}

				$content = apply_filters( 'the_content', $content );

				$css = get_post_meta( $post_id, '_wpb_shortcodes_custom_css', true );

				echo wp_json_encode( array(
					'css'               => ! empty( $css ) ? $css : '',
					'bg_color'          => get_post_meta( $post_id, $bg_name, true ),
					'close_color'       => get_post_meta( $post_id, $close_name, true ),
					'close_hover_color' => get_post_meta( $post_id, $close_hover_name, true ),
					'close_icon_color'  => get_post_meta( $post_id, $close_icon_name, true ),
					'close_hover_icon_color'  => get_post_meta( $post_id, $close_hover_icon_name, true ),
					'content'           => do_shortcode( $content )
				) );
			}
		}

		wp_die();
	}




	/**
	 * Register Meta Box
	 */
	public function generate_modals_param_meta_box(){
		add_meta_box(
			'modals_parameters_meta_box',
			__( 'Parameters', BASEMENT_MODALS_TEXTDOMAIN ),
			array( &$this, 'render_modals_param_meta_box' ),
			'modals',
			'normal',
			'core'
		);
	}


	/**
	 * Render Meta Box Parameters
	 */
	public function render_modals_param_meta_box(){
		$view  = new Basement_Modals_Plugin();
		$view->load_views( $this->modals_settings_generate(), array('modals-param-meta-box') );
	}

	/**
	 * Generate Panel With Modal Window Settings
	 *
	 * @param array $config
	 * @return array
	 */
	public function modals_settings_generate( $config = array() ) {
		$config[ 'modals_settings' ] = array(
			'blocks' => array(
				array(
					'type' => 'dom',
					'title' => __( 'Window ID', BASEMENT_MODALS_TEXTDOMAIN ),
					'description' => __( 'The identifier for the modal window. Copy and paste this ID into <b>href/value</b> attribute for any link or button.', BASEMENT_MODALS_TEXTDOMAIN ),
					'input' => $this->id_modal()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Window background color', BASEMENT_MODALS_TEXTDOMAIN ),
					'description' => __( 'Sets the background color for modal window.', BASEMENT_MODALS_TEXTDOMAIN ),
					'input' => $this->bg_modal_color()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Button color', BASEMENT_MODALS_TEXTDOMAIN ),
					'description' => __( 'Sets the colors for close button.', BASEMENT_MODALS_TEXTDOMAIN ),
					'input' => $this->bg_close_color()
				)
			)
		);


		return $config;
	}

	/**
	 * Id for modal window
	 */
	protected function id_modal() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$post_id = isset($post->ID) ? $post->ID : '';
		if(!empty($post_id)) {
			$id_input  = new Basement_Form_Input( array(
				'value'      => '#basement-modal-' . $post_id,
				'class' => 'basement-modal-id-field',
				'attributes' => array(
					'readonly' => 'true',
					'onfocus' => 'this.select();',
				)
			) );
			$container = $dom->appendChild( $dom->importNode( $id_input->create(), true ) );

			return $dom->saveHTML( $container );
		}
	}



	/**
	 * Modal Window background color
	 */
	protected function bg_modal_color() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$option_name = $this->meta . 'bg_color';

		$bg_color = new Basement_Form_Input_Colorpicker( array(
			'name' => $option_name,
			'value' => get_post_meta( $post->ID, $option_name, true )
		) );
		$container = $dom->appendChild($dom->importNode( $bg_color->create(), true  ) );

		return $dom->saveHTML($container);
	}


	/**
	 * Close button background color
	 */
	protected function bg_close_color() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$close_name = $this->meta . 'close_color';
		$close_hover_name = $this->meta . 'close_hover_color';
		$close_icon_name = $this->meta . 'close_icon_color';
		$close_hover_icon_name = $this->meta . 'close_hover_icon_color';

		$container = $dom->appendChild($dom->createElement('div'));

		$bg_button = new Basement_Form_Input_Colorpicker( array(
			'label_text' => __('Background color:',BASEMENT_MODALS_TEXTDOMAIN),
			'name' => $close_name,
			'value' => get_post_meta( $post->ID, $close_name, true )
		) );
		$container->appendChild($dom->importNode( $bg_button->create(), true  ) );


		$bg_button_hover = new Basement_Form_Input_Colorpicker( array(
			'label_text' => __('Background on hover color:',BASEMENT_MODALS_TEXTDOMAIN),
			'name' => $close_hover_name,
			'value' => get_post_meta( $post->ID, $close_hover_name, true )
		) );
		$container->appendChild($dom->importNode( $bg_button_hover->create(), true  ) );


		$bg_icon = new Basement_Form_Input_Colorpicker( array(
			'label_text' => __('Icon color:',BASEMENT_MODALS_TEXTDOMAIN),
			'name' => $close_icon_name,
			'value' => get_post_meta( $post->ID, $close_icon_name, true )
		) );
		$container->appendChild($dom->importNode( $bg_icon->create(), true  ) );



		$bg_icon_hover = new Basement_Form_Input_Colorpicker( array(
			'label_text' => __('Icon on hover color:',BASEMENT_MODALS_TEXTDOMAIN),
			'name' => $close_hover_icon_name,
			'value' => get_post_meta( $post->ID, $close_hover_icon_name, true )
		) );
		$container->appendChild($dom->importNode( $bg_icon_hover->create(), true  ) );

		return $dom->saveHTML($container);
	}



	/**
	 * Remove Preview&Edit links
	 *
	 * @param $actions
	 * @param $post
	 * @return mixed
	 */
	public function modals_actions( $actions, $post ) {

		if ( $post->post_type === $this->post_type ) {
			unset($actions['view']);
			unset($actions['inline hide-if-no-js']);
		}

		return $actions;
	}


	/**
	 * Remove screen options
	 *
	 * @param $display_boolean
	 * @param $wp_screen_object
	 * @return bool
	 */
	public function remove_screen_options ($display_boolean, $wp_screen_object) {
		$post_type = isset($wp_screen_object->id) ? $wp_screen_object->id : '';
			if($this->post_type == $post_type)
				return false;

		return true;
	}


	/**
	 * Custom notify for Modal Window (single page)
	 *
	 * @param $messages
	 * @return mixed
	 */
	public function modals_updated_messages( $messages ) {
		global $post;

		$post_ID = isset($post->ID) ? $post->ID : '';

		if(!empty($post_ID)) {
			$messages['modals'] = array(
				0  => '', // Unused. Messages start at index 1.
				1  => sprintf( __( 'Modal updated.', BASEMENT_MODALS_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
				2  => __( 'Custom field updated.', BASEMENT_MODALS_TEXTDOMAIN ),
				3  => __( 'Custom field deleted.', BASEMENT_MODALS_TEXTDOMAIN ),
				4  => __( 'Modal updated.', BASEMENT_MODALS_TEXTDOMAIN ),
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Modal window restored to revision from %s', BASEMENT_MODALS_TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6  => sprintf( __( 'Modal window published.', BASEMENT_MODALS_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
				7  => __( 'Modal window saved.', BASEMENT_MODALS_TEXTDOMAIN ),
				8  => sprintf( __( 'Modal window submitted.', BASEMENT_MODALS_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				9  => sprintf( __( 'Modal window scheduled for: <strong>%1$s</strong>.', BASEMENT_MODALS_TEXTDOMAIN ),
					date_i18n( __( 'M j, Y @ G:i', BASEMENT_MODALS_TEXTDOMAIN ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
				10 => sprintf( __( 'Modal window draft updated.', BASEMENT_MODALS_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);
		}

		return $messages;
	}


	/**
	 * Custom notify for Modal Window (all modals)
	 *
	 * @param $bulk_messages
	 * @param $bulk_counts
	 * @return mixed
	 */
	public function bulk_modals_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['modals'] = array(
			'updated'   => _n( '%s modal window updated.', '%s modal windows updated.', $bulk_counts['updated'], BASEMENT_MODALS_TEXTDOMAIN ),
			'locked'    => _n( '%s  modal window not updated, somebody is editing it.', '%s modal windows not updated, somebody is editing them.', $bulk_counts['locked'], BASEMENT_MODALS_TEXTDOMAIN ),
			'deleted'   => _n( '%s  modal window permanently deleted.', '%s modal windows permanently deleted.', $bulk_counts['deleted'], BASEMENT_MODALS_TEXTDOMAIN ),
			'trashed'   => _n( '%s  modal window moved to the Trash.', '%s modal windows moved to the Trash.', $bulk_counts['trashed'], BASEMENT_MODALS_TEXTDOMAIN ),
			'untrashed' => _n( '%s  modal window restored from the Trash.', '%s modal windows restored from the Trash.', $bulk_counts['untrashed'], BASEMENT_MODALS_TEXTDOMAIN ),
		);

		return $bulk_messages;
	}


	/**
	 * Register CPT Modal Window
	 */
	protected function register_type() {
		if ( !post_type_exists( $this->post_type ) ) {
			register_post_type(
					apply_filters( 'basement_cpt_modals_register_filter_name', $this->post_type ),
					$this->post_type_args
			);
		}
	}

	
	/**
	 * Init CPT Modal Window
	 */
	protected function fill_post_type_args() {

		$this->post_type_args = apply_filters(
				'basement_cpt_modals_args',
				array (
					'post_type' => $this->post_type,
					'description' => __( 'All windows', BASEMENT_MODALS_TEXTDOMAIN ),
					'public'              => true,
					'show_ui'             => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_nav_menus'   => false,
					'rewrite'             => array( 'slug' => $this->post_type ),
					'has_archive'         => false,
					'menu_icon'           => 'dashicons-desktop',
					'menu_position'       =>  15,
					'hierarchical'        => false,
					'supports'            => array('title', 'editor'),
					'labels' => array (
							'name'               => __( 'All windows', BASEMENT_MODALS_TEXTDOMAIN ),
							'singular_name'      => __( 'All windows', BASEMENT_MODALS_TEXTDOMAIN ),
							'menu_name'          => __( 'Modal Windows', BASEMENT_MODALS_TEXTDOMAIN ),
							'name_admin_bar'     => __( 'All windows', BASEMENT_MODALS_TEXTDOMAIN ),
							'all_items'          => __( 'All windows', BASEMENT_MODALS_TEXTDOMAIN ),
							'add_new'            => __( 'Add new', BASEMENT_MODALS_TEXTDOMAIN ),
							'add_new_item'       => __( 'Add window', BASEMENT_MODALS_TEXTDOMAIN ),
							'edit_item'          => __( 'Edit modal window', BASEMENT_MODALS_TEXTDOMAIN ),
							'new_item'           => __( 'New modal window', BASEMENT_MODALS_TEXTDOMAIN ),
							'view_item'          => __( 'View modal window', BASEMENT_MODALS_TEXTDOMAIN ),
							'search_items'       => __( 'Search modal windows', BASEMENT_MODALS_TEXTDOMAIN ),
							'not_found'          => __( 'Modal windows not found', BASEMENT_MODALS_TEXTDOMAIN ),
							'parent_item_colon'  => __( 'Parent modal window:',  BASEMENT_MODALS_TEXTDOMAIN ),
							'not_found_in_trash' => __( 'No modal windows found in Trash.', BASEMENT_MODALS_TEXTDOMAIN )
					)
				)
		);

	}

	/**
	 * Remove unnecessary Meta boxes
	 */
	public function remove_cpt_meta_boxes() {
		remove_meta_box( 'mymetabox_revslider_0', $this->post_type , 'normal' );
		remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		remove_meta_box( 'icl_div_config', $this->post_type, 'normal' );
	}
}

Basement_Modals_Cpt::init();