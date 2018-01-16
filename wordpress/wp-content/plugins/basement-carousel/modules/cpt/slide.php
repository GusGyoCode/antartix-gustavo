<?php
defined('ABSPATH') or die();

class Basement_Slide_Cpt extends Basement_Cpt {

	private static $instance = null;

	protected $post_type = 'carousel_slide';

	protected $show_id_admin_column = true;

	protected $duplicate = true;



	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( &$this, 'carousel_menu') );

		add_action( 'admin_head' , array(&$this, 'remove_cpt_meta_boxes' ), 10, 2 );

		add_filter( 'screen_options_show_screen', array( &$this, 'remove_screen_options' ), 999, 2 );
		
		add_filter( 'post_updated_messages', array( &$this, 'slide_updated_messages' ) );

		add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_slide_updated_messages' ), 10, 2 );

		add_filter( 'post_row_actions', array( &$this, 'slide_actions' ), 10, 2 );
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Slide_Cpt();
		}
		return self::$instance;
	}


	/**
	 * Remove Preview&Edit links
	 *
	 * @param $actions
	 * @param $post
	 * @return mixed
	 */
	public function slide_actions( $actions, $post ) {

		if ( $post->post_type === $this->post_type ) {
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
		global $post;

		if ( isset($post->post_type) && $post->post_type === $this->post_type ) {
			return false;
		} else {
			return true;
		}
	}


	/**
	 * Custom notify for Slides (single page)
	 *
	 * @param $messages
	 * @return mixed
	 */
	public function slide_updated_messages( $messages ) {
		global $post;

		$post_ID = $post->ID;
		$messages['carousel_slide'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Slide updated.', BASEMENT_CAROUSEL_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			3 => __( 'Custom field deleted.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			4 => __( 'Slide updated.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Slide restored to revision from %s', BASEMENT_CAROUSEL_TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Slide published.', BASEMENT_CAROUSEL_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Slide saved.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			8 => sprintf( __( 'Slide submitted.', BASEMENT_CAROUSEL_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Slide scheduled for: <strong>%1$s</strong>.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
				date_i18n( __( 'M j, Y @ G:i', BASEMENT_CAROUSEL_TEXTDOMAIN ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Slide draft updated.', BASEMENT_CAROUSEL_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}


	/**
	 * Custom notify for Slides (all slides)
	 *
	 * @param $bulk_messages
	 * @param $bulk_counts
	 * @return mixed
	 */
	public function bulk_slide_updated_messages( $bulk_messages, $bulk_counts ) {
		global $post;

		$bulk_messages['carousel_slide'] = array(
			'updated'   => _n( '%s slide updated.', '%s slides updated.', $bulk_counts['updated'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'locked'    => _n( '%s slide not updated, somebody is editing it.', '%s slides not updated, somebody is editing them.', $bulk_counts['locked'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'deleted'   => _n( '%s slide permanently deleted.', '%s slides permanently deleted.', $bulk_counts['deleted'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'trashed'   => _n( '%s slide moved to the Trash.', '%s slides moved to the Trash.', $bulk_counts['trashed'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'untrashed' => _n( '%s slide restored from the Trash.', '%s slides restored from the Trash.', $bulk_counts['untrashed'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
		);

		return $bulk_messages;
	}
	

	/**
	 * Register CPT Carousel Slide
	 */
	protected function register_type() {
		if ( !post_type_exists( $this->post_type ) ) {
			register_post_type(
					apply_filters( 'basement_cpt_carousel_slide_register_filter_name', $this->post_type ),
					$this->post_type_args
			);
		}
	}

	/**
	 * Add CPT menu
	 */
	public function carousel_menu() {
		add_submenu_page( 'edit.php?post_type=carousel', __('Add new slide', BASEMENT_CAROUSEL_TEXTDOMAIN), __('Add slide', BASEMENT_CAROUSEL_TEXTDOMAIN), 'edit_posts', 'post-new.php?post_type='. $this->post_type , null );
	}

	/**
	 * Init CPT Carousel Slide
	 */
	protected function fill_post_type_args() {

		$this->post_type_args = apply_filters(
				'basement_cpt_carousel_slide_args',
				array (
					'post_type'           => $this->post_type,
					'description'         => __( 'Slides', BASEMENT_CAROUSEL_TEXTDOMAIN ),
					'public'              => true,
					'publicly_queryable'  => true,
					'show_ui'             => true,
					'show_in_menu'        => 'edit.php?post_type=carousel',
					'query_var'           => true,
					'rewrite'             => array( 'slug' => $this->post_type ),
					'capability_type'     => 'post',
					'has_archive'         => false,
					'show_in_nav_menus'   => false,
					'exclude_from_search' => true,
					'hierarchical'        => false,
					'supports'            => array(),
					'labels' => array (
							'name'               => __( 'Slides', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'singular_name'      => __( 'Slide', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'menu_name'          => __( 'Slides', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'name_admin_bar'     => __( 'Slides', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'all_items'          => __( 'All slides', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'add_new'            => __( 'Add slide', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'add_new_item'       => __( 'Add slide', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'edit_item'          => __( 'Edit slide', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'new_item'           => __( 'New slide', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'view_item'          => __( 'View slide', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'search_items'       => __( 'Search slides', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'not_found'          => __( 'Slides not found', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'parent_item_colon'  => __( 'Parent slide:',  BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'not_found_in_trash' => __( 'No slides found in Trash.', BASEMENT_CAROUSEL_TEXTDOMAIN )
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
		#remove_meta_box( 'icl_div_config', $this->post_type, 'normal' );
	}

}

Basement_Slide_Cpt::init();
