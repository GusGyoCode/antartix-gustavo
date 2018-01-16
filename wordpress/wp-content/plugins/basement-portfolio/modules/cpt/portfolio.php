<?php
defined('ABSPATH') or die();

class Basement_Portfolio_Cpt extends Basement_Cpt {

	private static $instance = null;

	protected $post_type = 'portfolio';

	protected $show_id_admin_column = true;

	protected $duplicate = true;

	public function __construct() {
		parent::__construct();
		
		add_action( 'do_meta_boxes' , array(&$this, 'remove_portfolio_meta_boxes' ), 10, 2 );

		add_filter( 'post_updated_messages', array( &$this, 'portfolio_updated_messages' ) );

		add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_portfolio_updated_messages' ), 10, 2 );

		add_filter( 'post_row_actions', array( &$this, 'portfolio_actions' ), 10, 2 );
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Portfolio_Cpt();
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
	public function portfolio_actions( $actions, $post ) {

		if ( $post->post_type === $this->post_type ) {
			unset($actions['view']);
			unset($actions['inline hide-if-no-js']);
		}

		return $actions;
	}
	

	/**
	 * Custom notify for PortfolioGrid (single)
	 *
	 * @param $messages
	 * @return mixed
	 */
	public function portfolio_updated_messages( $messages ) {
		global $post;

		$post_ID = $post->ID;

		$messages[$this->post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Grid updated.', BASEMENT_PORTFOLIO_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			3 => __( 'Custom field deleted.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			4 => __( 'Grid updated.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Portfolio restored to revision from %s', BASEMENT_PORTFOLIO_TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Grid published.', BASEMENT_PORTFOLIO_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Grid saved.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			8 => sprintf( __( 'Grid submitted.', BASEMENT_PORTFOLIO_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Grid scheduled for: <strong>%1$s</strong>.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				date_i18n( __( 'M j, Y @ G:i', BASEMENT_PORTFOLIO_TEXTDOMAIN ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Grid draft updated.', BASEMENT_PORTFOLIO_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}


	/**
	 * Custom notify for Portfolio (all portfolio)
	 *
	 * @param $bulk_messages
	 * @param $bulk_counts
	 * @return mixed
	 */
	public function bulk_portfolio_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages[$this->post_type] = array(
			'updated'   => _n( '%s grid updated.', '%s grids items updated.', $bulk_counts['updated'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'locked'    => _n( '%s grid not updated, somebody is editing it.', '%s grids items not updated, somebody is editing them.', $bulk_counts['locked'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'deleted'   => _n( '%s grid permanently deleted.', '%s grids items permanently deleted.', $bulk_counts['deleted'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'trashed'   => _n( '%s grid moved to the Trash.', '%s grids items moved to the Trash.', $bulk_counts['trashed'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'untrashed' => _n( '%s grid restored from the Trash.', '%s grids items restored from the Trash.', $bulk_counts['untrashed'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
		);
		
		return $bulk_messages;
	}


	/**
	 * Register CPT Grid
	 */
	protected function register_type() {
		if ( !post_type_exists( $this->post_type ) ) {
			register_post_type(
					apply_filters( 'basement_cpt_portfolio_register_filter_name', $this->post_type ),
					$this->post_type_args
			);
		}
	}


	/**
	 * Get CPT name
	 *
	 * @return string
	 */
	public function portfolio_cpt_name() {
		return $this->post_type;
	}



	/**
	 * Init CPT Grid
	 */
	protected function fill_post_type_args() {
		$this->post_type_args = apply_filters(
				'basement_cpt_portfolio_args',
				array (
					'post_type'           => $this->post_type,
					'description'         => __( 'All grids', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'public'              => false,
					'show_ui'             => true,
					'exclude_from_search' => true,
					'show_in_nav_menus'   => false,
					'rewrite'             => array( 'slug' => $this->post_type ),
					'has_archive'         => false,
					'capability_type'     => 'post',
					'menu_icon'           => 'dashicons-portfolio',
					'menu_position'       =>  17,
					'hierarchical'        => false,
					'supports'            => array('title'),
					'labels' => array (
							'name'               => __( 'All grids', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'singular_name'      => __( 'All grids', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'menu_name'          => __( 'Portfolio', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'name_admin_bar'     => __( 'All grids', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'all_items'          => __( 'All grids', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'add_new'            => __( 'Add grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'add_new_item'       => __( 'Add grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'edit_item'          => __( 'Edit grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'new_item'           => __( 'New grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'view_item'          => __( 'View grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'search_items'       => __( 'Search grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'not_found'          => __( 'Grids not found', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'parent_item_colon'  => __( 'Parent grids:',  BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'not_found_in_trash' => __( 'No grids found in trash.', BASEMENT_PORTFOLIO_TEXTDOMAIN )
					)
				)
		);
	}

	/**
	 * Remove unnecessary Meta boxes
	 */
	public function remove_portfolio_meta_boxes() {
		remove_meta_box( 'mymetabox_revslider_0', $this->post_type , 'normal' );
		remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		remove_meta_box( 'icl_div_config', $this->post_type, 'normal' );
	}
}

Basement_Portfolio_Cpt::init();