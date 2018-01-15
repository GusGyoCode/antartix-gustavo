<?php
defined('ABSPATH') or die();

class Basement_Tile_Cpt extends Basement_Cpt {

	private static $instance = null;

	protected $post_type = 'tile';

	protected $tax_name = 'tile_category';

	protected $show_thumbnail_admin_column = true;

	protected $show_id_admin_column = true;

	protected $duplicate = true;



	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( &$this, 'tile_menu') );

		add_action( 'do_meta_boxes' , array(&$this, 'remove_cpt_meta_boxes' ), 10, 2 );

		add_filter( 'screen_options_show_screen', array( &$this, 'remove_screen_options' ), 10, 2 );

		add_filter( 'post_updated_messages', array( &$this, 'tile_updated_messages' ) );

		add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_tile_updated_messages' ), 10, 2 );

		add_filter( 'post_row_actions', array( &$this, 'tile_actions' ), 10, 2 );

		add_action( 'init', array( &$this, 'register_taxonomy' ), 5 );

		add_action( 'admin_head', array( &$this, 'menu_highlight' ) );

		add_filter( 'manage_edit-' . $this->tax_name . '_columns', array( &$this,  'tax_column_edit' ) );

		add_filter( $this->tax_name . '_row_actions', array( &$this, 'tax_actions' ), 10, 2 );

		add_action( 'admin_head', array( &$this, 'tax_clear' ), 999 );

		if( defined('DOING_AJAX') && DOING_AJAX ) {
			add_action('wp_ajax_load-more-tiles', array( &$this, 'load_more_tiles' ) );
			add_action('wp_ajax_nopriv_load-more-tiles', array( &$this, 'load_more_tiles' ) );
		}
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Tile_Cpt();
		}
		return self::$instance;
	}


	/**
	 * Clear Taxonomy Fields
	 */
	public function tax_clear() {
		$screen = get_current_screen();

		if($screen && $screen->taxonomy === $this->tax_name) {
			?>
			<script type='text/javascript'>
				window.onload = function() {
					if(typenow === 'tile') {
						var classes = ['term-parent-wrap', 'term-description-wrap'],
							taxChoose = function (tax_class) {
								return document.getElementsByClassName(tax_class)[0];
							};
						for (var i = 0; i < classes.length; i++) {
							taxChoose(classes[i]).parentNode.removeChild(taxChoose(classes[i]));
						}
					}
				}
			</script>
			<style type="text/css">
				.term-parent-wrap,
				.term-description-wrap {
					display: none;
				}
			</style>
		<?php
		}
	}


	/**
	 * Ajax load tiles
	 */
	public function load_more_tiles() {
		#ob_start();

		if(!isset($_POST['data']))
			wp_die();

		extract($_POST['data']);


		$item_tiles = '';
		$help_load = $load + $need;
		$inner_key = 0;

		$tiles = explode(',',$tiles);

		$grid_settings = new Basement_Grid_Settings();
		$grid_params = $grid_settings->get_grid( absint( $grid ) );

		foreach ($tiles as $key => $id) {
			if ($key < $load) {
				continue;
			} else {
				$inner_key++;
				if($inner_key <= $need) {
					$sh_gallery = new WPBakeryShortCode_basement_vc_gallery();
					$item_tiles .= $sh_gallery->generate_item_tile($grid_params, $id);
				}
			}
		}

		$frarments = array(
			'html' => $item_tiles,
			'load' => $help_load
		);


		wp_send_json( $frarments );

		wp_die();
	}


	/**
	 * Remove Preview&Edit links
	 *
	 * @param $actions
	 * @param $tag
	 * @return mixed
	 */
	public function tax_actions( $actions, $tag ) {
		unset($actions['view']);
		unset($actions['inline hide-if-no-js']);
		return $actions;
	}


	/**
	 * Change Tax Columns
	 *
	 * @param $columns
	 * @return mixed
	 */
	public function tax_column_edit($columns) {
		unset($columns['description']);
		unset($columns['slug']);

		return $columns;
	}


	/**
	 * Register core taxonomies.
	 */
	public function register_taxonomy() {
		if ( ! taxonomy_exists( $this->tax_name ) ) {
			do_action( 'tile_register_taxonomy' );
			register_taxonomy( $this->tax_name, $this->post_type, array(
				'public'             => true,
				'hierarchical'       => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_tagcloud'      => false,
				'show_in_nav_menus' => false,
				'show_admin_column'  => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'category-tile', 'with_front' => false ),
				'labels'             => array(
					'name'              => __( 'Categories', BASEMENT_GALLERY_TEXTDOMAIN ),
					'singular_name'     => __( 'Categories', BASEMENT_GALLERY_TEXTDOMAIN ),
					'search_items'      => __( 'Search categories', BASEMENT_GALLERY_TEXTDOMAIN ),
					'all_items'         => __( 'All categories', BASEMENT_GALLERY_TEXTDOMAIN ),
					'parent_item'       => __( 'Parent categories', BASEMENT_GALLERY_TEXTDOMAIN ),
					'parent_item_colon' => __( 'Parent categories:', BASEMENT_GALLERY_TEXTDOMAIN ),
					'edit_item'         => __( 'Edit category', BASEMENT_GALLERY_TEXTDOMAIN ),
					'update_item'       => __( 'Update categories', BASEMENT_GALLERY_TEXTDOMAIN ),
					'add_new_item'      => __( 'Add new category', BASEMENT_GALLERY_TEXTDOMAIN ),
					'new_item_name'     => __( 'Category name', BASEMENT_GALLERY_TEXTDOMAIN ),
					'menu_name'         => __( 'Categories', BASEMENT_GALLERY_TEXTDOMAIN )
				)
			) );
			do_action( 'tile_after_register_taxonomy' );
		}
	}


	/**
	 * Remove Preview&Edit links
	 *
	 * @param $actions
	 * @param $post
	 * @return mixed
	 */
	public function tile_actions( $actions, $post ) {
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
		global $post;

		if ( isset($post->post_type) && $post->post_type === $this->post_type ) {
			return false;
		} else {
			return true;
		}
	}


	/**
	 * Custom notify for Tiles (single page)
	 *
	 * @param $messages
	 * @return mixed
	 */
	public function tile_updated_messages( $messages ) {
		global $post;

		$post_ID = $post->ID;
		$messages[$this->post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Tile updated.', BASEMENT_GALLERY_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', BASEMENT_GALLERY_TEXTDOMAIN ),
			3 => __( 'Custom field deleted.', BASEMENT_GALLERY_TEXTDOMAIN ),
			4 => __( 'Tile updated.', BASEMENT_GALLERY_TEXTDOMAIN ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Tile restored to revision from %s', BASEMENT_GALLERY_TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Tile published.', BASEMENT_GALLERY_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Tile saved.', BASEMENT_GALLERY_TEXTDOMAIN ),
			8 => sprintf( __( 'Tile submitted.', BASEMENT_GALLERY_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Tile scheduled for: <strong>%1$s</strong>.', BASEMENT_GALLERY_TEXTDOMAIN ),
				date_i18n( __( 'M j, Y @ G:i', BASEMENT_GALLERY_TEXTDOMAIN ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Tile draft updated.', BASEMENT_GALLERY_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}


	/**
	 * Custom notify for Tile (all tiles)
	 *
	 * @param $bulk_messages
	 * @param $bulk_counts
	 * @return mixed
	 */
	public function bulk_tile_updated_messages( $bulk_messages, $bulk_counts ) {
		global $post;

		$bulk_messages[$this->post_type] = array(
			'updated'   => _n( '%s tile updated.', '%s tiles updated.', $bulk_counts['updated'], BASEMENT_GALLERY_TEXTDOMAIN ),
			'locked'    => _n( '%s tile not updated, somebody is editing it.', '%s tiles not updated, somebody is editing them.', $bulk_counts['locked'], BASEMENT_GALLERY_TEXTDOMAIN ),
			'deleted'   => _n( '%s tile permanently deleted.', '%s tiles permanently deleted.', $bulk_counts['deleted'], BASEMENT_GALLERY_TEXTDOMAIN ),
			'trashed'   => _n( '%s tile moved to the Trash.', '%s tiles moved to the Trash.', $bulk_counts['trashed'], BASEMENT_GALLERY_TEXTDOMAIN ),
			'untrashed' => _n( '%s tile restored from the Trash.', '%s tiles restored from the Trash.', $bulk_counts['untrashed'], BASEMENT_GALLERY_TEXTDOMAIN ),
		);

		return $bulk_messages;
	}


	/**
	 * Register CPT Grid Tile
	 */
	protected function register_type() {
		if ( !post_type_exists( $this->post_type ) ) {
			register_post_type(
				apply_filters( 'basement_cpt_grid_tile_register_filter_name', $this->post_type ),
				$this->post_type_args
			);
		}
	}


	/**
	 * Get CPT name
	 *
	 * @return string
	 */
	public function tile_cpt_name() {
		return $this->post_type;
	}


	/**
	 * Get Tax name
	 *
	 * @return string
	 */
	public function tile_tax_name() {
		return $this->tax_name;
	}


	/**
	 * Add CPT menu
	 */
	public function tile_menu() {
		add_submenu_page( 'edit.php?post_type=grid', __('Add new tile', BASEMENT_GALLERY_TEXTDOMAIN), __('Add tile', BASEMENT_GALLERY_TEXTDOMAIN), 'edit_posts', 'post-new.php?post_type='. $this->post_type , null );
		add_submenu_page( 'edit.php?post_type=grid', __('Categories', BASEMENT_GALLERY_TEXTDOMAIN), __('Categories', BASEMENT_GALLERY_TEXTDOMAIN), 'edit_posts', 'edit-tags.php?taxonomy=' . $this->tax_name . '&post_type=' . $this->post_type , null );
	}



	/**
	 * Highlights the correct top level admin menu item for post type add screens.
	 */
	public function menu_highlight() {
		global $parent_file, $submenu_file, $post_type;
		switch ( $post_type ) {
			case 'tile' :
				$screen = get_current_screen();
				if ( $screen && $screen->taxonomy === $this->tax_name ) {
					$submenu_file = 'edit-tags.php?taxonomy=' . $this->tax_name . '&post_type=' . $this->post_type;
					$parent_file  = 'edit.php?post_type=grid';
				}
				break;
		}
	}



	/**
	 * Init CPT Grid Tile
	 */
	protected function fill_post_type_args() {
		$this->post_type_args = apply_filters(
				'basement_cpt_grid_tile_args',
				array (
					'post_type'           => $this->post_type,
					'description'         => __( 'Tiles', BASEMENT_GALLERY_TEXTDOMAIN ),
					'public'              => false,
					'show_ui'             => true,
					'show_in_menu'        => 'edit.php?post_type=grid',
					'query_var'           => true,
					'rewrite'             => array( 'slug' => $this->post_type ),
					'capability_type'     => 'post',
					'has_archive'         => false,
					'show_in_nav_menus'   => false,
					'exclude_from_search' => true,
					'hierarchical'        => false,
					'supports'            => array('title','thumbnail'),
					'labels' => array (
							'name'               => __( 'Tiles', BASEMENT_GALLERY_TEXTDOMAIN ),
							'singular_name'      => __( 'Tile', BASEMENT_GALLERY_TEXTDOMAIN ),
							'menu_name'          => __( 'Tiles', BASEMENT_GALLERY_TEXTDOMAIN ),
							'name_admin_bar'     => __( 'Tiles', BASEMENT_GALLERY_TEXTDOMAIN ),
							'all_items'          => __( 'All tiles', BASEMENT_GALLERY_TEXTDOMAIN ),
							'add_new'            => __( 'Add tile', BASEMENT_GALLERY_TEXTDOMAIN ),
							'add_new_item'       => __( 'Add tile', BASEMENT_GALLERY_TEXTDOMAIN ),
							'edit_item'          => __( 'Edit tile', BASEMENT_GALLERY_TEXTDOMAIN ),
							'new_item'           => __( 'New tile', BASEMENT_GALLERY_TEXTDOMAIN ),
							'view_item'          => __( 'View tile', BASEMENT_GALLERY_TEXTDOMAIN ),
							'search_items'       => __( 'Search tiles', BASEMENT_GALLERY_TEXTDOMAIN ),
							'not_found'          => __( 'Tiles not found', BASEMENT_GALLERY_TEXTDOMAIN ),
							'parent_item_colon'  => __( 'Parent tile:',  BASEMENT_GALLERY_TEXTDOMAIN ),
							'not_found_in_trash' => __( 'No tiles found in Trash.', BASEMENT_GALLERY_TEXTDOMAIN )
					)
				)
		);
	}


	/**
	 * Remove unnecessary Meta boxes
	 */
	public function remove_cpt_meta_boxes() {
		remove_meta_box( 'icl_div_config', $this->post_type, 'normal' );
		remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		remove_meta_box( 'mymetabox_revslider_0', $this->post_type , 'normal' );
	}

}

Basement_Tile_Cpt::init();
