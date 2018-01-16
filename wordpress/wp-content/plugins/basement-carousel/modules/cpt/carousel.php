<?php
defined('ABSPATH') or die();

class Basement_Carousel_Cpt extends Basement_Cpt {

	private static $instance = null;

	protected $post_type = 'carousel';

	protected $preview_meta_box_name = 'carousel-preview-meta-box';

	protected $show_id_admin_column = true;

	protected $duplicate = true;

	public function __construct() {
		parent::__construct();
		
		add_action( 'admin_head' , array(&$this, 'remove_cpt_meta_boxes' ), 99 );

		add_action( 'post_submitbox_misc_actions', array( &$this, 'carousel_preview_button' ) );

		add_filter( 'screen_options_show_screen', array( &$this, 'remove_screen_options' ), 99, 2 );

		add_filter( 'post_updated_messages', array( &$this, 'carousel_updated_messages' ) );

		add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_carousel_updated_messages' ), 10, 2 );

		add_filter( 'post_row_actions', array( &$this, 'carousel_actions' ), 10, 2 );
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Carousel_Cpt();
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
	public function carousel_actions( $actions, $post ) {

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
	 * Custom notify for Carousel (single page)
	 *
	 * @param $messages
	 * @return mixed
	 */
	public function carousel_updated_messages( $messages ) {
		global $post;

		$post_ID = $post->ID;
		
		$messages['carousel'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Carousel updated.', BASEMENT_CAROUSEL_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			3 => __( 'Custom field deleted.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			4 => __( 'Carousel updated.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Carousel restored to revision from %s', BASEMENT_CAROUSEL_TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Carousel published.', BASEMENT_CAROUSEL_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Carousel saved.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
			8 => sprintf( __( 'Carousel submitted.', BASEMENT_CAROUSEL_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Carousel scheduled for: <strong>%1$s</strong>.', BASEMENT_CAROUSEL_TEXTDOMAIN ),
				date_i18n( __( 'M j, Y @ G:i', BASEMENT_CAROUSEL_TEXTDOMAIN ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Carousel draft updated.', BASEMENT_CAROUSEL_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}


	/**
	 * Custom notify for Carousel (all carousels)
	 *
	 * @param $bulk_messages
	 * @param $bulk_counts
	 * @return mixed
	 */
	public function bulk_carousel_updated_messages( $bulk_messages, $bulk_counts ) {
		global $post;

		$bulk_messages['carousel'] = array(
			'updated'   => _n( '%s carousel updated.', '%s carousels updated.', $bulk_counts['updated'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'locked'    => _n( '%s carousel not updated, somebody is editing it.', '%s carousels not updated, somebody is editing them.', $bulk_counts['locked'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'deleted'   => _n( '%s carousel permanently deleted.', '%s carousels permanently deleted.', $bulk_counts['deleted'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'trashed'   => _n( '%s carousel moved to the Trash.', '%s carousels moved to the Trash.', $bulk_counts['trashed'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
			'untrashed' => _n( '%s carousel restored from the Trash.', '%s carousels restored from the Trash.', $bulk_counts['untrashed'], BASEMENT_CAROUSEL_TEXTDOMAIN ),
		);


		return $bulk_messages;
	}


	/**
	 * Register CPT Carousel
	 */
	protected function register_type() {
		if ( !post_type_exists( $this->post_type ) ) {
			register_post_type(
					apply_filters( 'basement_cpt_carousel_register_filter_name', $this->post_type ),
					$this->post_type_args
			);
		}
	}

	/**
	 * Generate Preview (Show/Hide) Button
	 */
	public function carousel_preview_button() {
		global $post;

		$status = get_post_status( $post->ID );
		$type = $post->post_type;

		if( $status === 'publish' && $type === $this->post_type ) {
			$html  = '<div id="major-publishing-actions" style="overflow:hidden;background-color: #eee;text-align:center;">';
			$html .= '<a href="#' . $this->preview_meta_box_name . '" data-id="' . $post->ID . '" class="button button-secondary button-large" id="basement-generate-preview" title="">' . __( 'Show Preview Carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ) . '</a>';
			$html .= '<a href="#' . $this->preview_meta_box_name . '" data-id="' . $post->ID . '" class="button button-secondary button-large button-error hidden" id="basement-close-preview" title="">' . __( 'Hide Preview Carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ) . '</a>';
			$html .= '</div>';
			echo $html;
		}
	}

	
	/**
	 * Init CPT Carousel
	 */
	protected function fill_post_type_args() {

		$this->post_type_args = apply_filters(
				'basement_cpt_carousel_args',
				array (
					'post_type' => $this->post_type,
					'description' => __( 'All carousels', BASEMENT_CAROUSEL_TEXTDOMAIN ),
					'public'              => false,
					'show_ui'             => true,
					'exclude_from_search' => true,
					'show_in_nav_menus'   => false,
					'rewrite'             => array( 'slug' => $this->post_type ),
					'has_archive'         => false,
					'capability_type'     => 'post',
					'menu_icon'           => 'dashicons-slides',
					'menu_position'       =>  15,
					'hierarchical'        => false,
					'supports'            => array('title'),
					'labels' => array (
							'name'               => __( 'All Carousels', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'singular_name'      => __( 'All carousels', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'menu_name'          => __( 'Carousels', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'name_admin_bar'     => __( 'All carousels', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'all_items'          => __( 'All carousels', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'add_new'            => __( 'Add carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'add_new_item'       => __( 'Add carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'edit_item'          => __( 'Edit carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'new_item'           => __( 'New carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'view_item'          => __( 'View carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'search_items'       => __( 'Search carousel', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'not_found'          => __( 'Slides not found', BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'parent_item_colon'  => __( 'Parent carousels:',  BASEMENT_CAROUSEL_TEXTDOMAIN ),
							'not_found_in_trash' => __( 'No carousels found in Trash.', BASEMENT_CAROUSEL_TEXTDOMAIN )
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

Basement_Carousel_Cpt::init();