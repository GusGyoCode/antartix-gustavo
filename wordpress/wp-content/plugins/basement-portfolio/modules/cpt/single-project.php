<?php
defined('ABSPATH') or die();

class Basement_Project_Cpt extends Basement_Cpt {

	private static $instance = null;

	protected $post_type = 'single_project';

	protected $tax_name = 'project_category';

	protected $custom_fields = 'project_custom_fields';

	protected $show_thumbnail_admin_column = true;

	protected $show_id_admin_column = true;

	protected $duplicate = true;

	protected $portfolio = 'portfolio';


	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( &$this, 'single_project_menu') );

		add_action( 'do_meta_boxes' , array(&$this, 'remove_cpt_meta_boxes' ), 10, 2 );

		add_filter( 'screen_options_show_screen', array( &$this, 'remove_screen_options' ), 10, 2 );

		add_filter( 'post_updated_messages', array( &$this, 'project_updated_messages' ) );

		add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_project_updated_messages' ), 10, 2 );

		add_filter( 'post_row_actions', array( &$this, 'project_actions' ), 10, 2 );

		add_action( 'init', array( &$this, 'register_taxonomy' ), 5 );

		add_action( 'admin_head', array( &$this, 'menu_highlight' ) );

		add_filter( 'manage_edit-' . $this->tax_name . '_columns', array( &$this,  'tax_column_edit' ) );

		add_filter( 'manage_edit-' . $this->custom_fields . '_columns', array( &$this,  'tax_custom_fields_column_edit' ) );

		add_filter( $this->tax_name . '_row_actions', array( &$this, 'tax_actions' ), 10, 2 );

		add_action( 'admin_head', array( &$this, 'tax_clear' ), 999 );

		add_filter( 'manage_' . $this->post_type . '_posts_columns', array( &$this, 'single_project_remove_columns' ), 10 );


		// Add form
		add_action( $this->custom_fields . '_add_form_fields', array( &$this, 'add_custom_fields' ) );
		add_action( $this->custom_fields . '_edit_form_fields', array( &$this, 'edit_custom_fields' ), 10 );
		add_action( 'created_term', array( &$this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( &$this, 'save_category_fields' ), 10, 3 );


		add_filter( 'manage_' .  $this->custom_fields . '_custom_column', array( $this, 'column_custom_fields' ), 10, 3 );
		add_filter( $this->custom_fields . '_row_actions', array( &$this, 'tax_custom_fields_actions' ), 10, 2 );



		if( defined('DOING_AJAX') && DOING_AJAX ) {
			add_action('wp_ajax_load-more-projects', array( &$this, 'load_more_projects' ) );
			add_action('wp_ajax_nopriv_load-more-projects', array( &$this, 'load_more_projects' ) );
		}


	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Project_Cpt();
		}
		return self::$instance;
	}

	
	/**
	 * Clear Taxonomy Fields
	 */
	public function tax_clear() {
		$screen = get_current_screen();

		if($screen && $screen->taxonomy === $this->tax_name || $screen && $screen->taxonomy === $this->custom_fields) {
			?>
			<script type='text/javascript'>
				window.onload = function() {
					if(typenow === '<?php echo $this->post_type; ?>') {
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
				<?php if($screen->taxonomy === $this->custom_fields) { ?>
				.form-field > p,
				.form-field p.description {
					display: none;
				}
				.column-display_type {
					width: 20%;
					text-transform: capitalize;
				}
				<?php } ?>
			</style>
			<?php
		}
	}



	/**
	 * Ajax load tiles
	 */
	public function load_more_projects() {
		ob_start();

		if(!isset($_POST['data']))
			die();

		extract($_POST['data']);


		$item_tiles = '';
		$help_load = $load + $need;
		$inner_key = 0;

		$tiles = explode(',',$tiles);

		$grid_settings = new Basement_Portfolio_Grid_Settings();
		$grid_params = $grid_settings->get_grid(absint($grid));


		foreach ($tiles as $key => $id) {
			if ($key < $load) {
				continue;
			} else {
				$inner_key++;
				if($inner_key <= $need) {
					$sh_gallery = new WPBakeryShortCode_basement_vc_portfolio();
					$item_tiles .= $sh_gallery->generate_item_tile($grid_params, $id);
				}
			}
		}

		$frarments = array(
			'html' => $item_tiles,
			'load' => $help_load
		);


		wp_send_json( $frarments );

		die();
	}




	/**
	 * Remove Preview&Edit links (Categories)
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
	 * Remove Preview&Edit links (CustomFields)
	 *
	 * @param $actions
	 * @param $tag
	 * @return mixed
	 */
	public function tax_custom_fields_actions( $actions, $tag ) {
		unset($actions['view']);
		unset($actions['inline hide-if-no-js']);

		return $actions;
	}



	/**
	 * Change Tax Columns (Categories)
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
	 * Change Tax Columns (CustomFields)
	 *
	 * @param $columns
	 * @return mixed
	 */
	public function tax_custom_fields_column_edit($columns) {
		unset($columns['description']);
		unset($columns['slug']);
		unset($columns['posts']);
		$columns['display_type'] = __( 'Type', BASEMENT_PORTFOLIO_TEXTDOMAIN );
		return $columns;
	}


	/**
	 * Remove column at project list
	 *
	 * @param $columns
	 * @return mixed
	 */
	public function single_project_remove_columns($columns) {
		unset($columns['taxonomy-project_custom_fields']);
		return$columns;
	}


	/**
	 * Display type column value added to category admin.
	 *
	 * @param string $columns
	 * @param string $column
	 * @param int $id
	 * @return array
	 */
	public function column_custom_fields($columns, $column, $id) {
		if ( 'display_type' == $column ) {
			$display_type = get_term_meta( $id, 'display_type', true );
			$columns .= esc_attr__( $display_type );
		}
		return $columns;
	}



	/**
	 * Custom types field
	 */
	public function add_custom_fields() {
		?>
		<div class="form-field">
			<label for="display_type"><?php _e( 'Display type', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></label>
			<select id="display_type" name="display_type" class="postform">
				<option value="text"><?php _e( 'Text', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
				<option value="textblock"><?php _e( 'Textblock', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
				<option value="link"><?php _e( 'Link', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
				<option value="button"><?php _e( 'Button', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
				<option value="categories"><?php _e( 'Categories', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
			</select>
		</div>
		<?php
	}


	/**
	 * Edit custom fields
	 *
	 * @param $term
	 */
	public function edit_custom_fields( $term ) {
		$display_type = get_term_meta( $term->term_id, 'display_type', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Display type', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></label></th>
			<td>
				<select id="display_type" name="display_type" class="postform">
					<option value="text" <?php selected( 'text', $display_type ); ?>><?php _e( 'Text', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
					<option value="textblock" <?php selected( 'textblock', $display_type ); ?>><?php _e( 'Textblock', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
					<option value="link" <?php selected( 'link', $display_type ); ?>><?php _e( 'Link', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
					<option value="button" <?php selected( 'button', $display_type ); ?>><?php _e( 'Button', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
					<option value="categories" <?php selected( 'categories', $display_type ); ?>><?php _e( 'Categories', BASEMENT_PORTFOLIO_TEXTDOMAIN ); ?></option>
				</select>
			</td>
		</tr>
		<?php
	}


	/**
	 * save_category_fields function.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param string $taxonomy
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['display_type'] ) && $this->custom_fields === $taxonomy ) {
			update_term_meta( $term_id, 'display_type', esc_attr( $_POST['display_type'] ) );
		}
	}


	/**
	 * Register core taxonomies.
	 */
	public function register_taxonomy() {
		if ( !taxonomy_exists($this->tax_name) ) {
			do_action( 'project_category_before_register_taxonomy' );
			register_taxonomy( $this->tax_name, $this->post_type, array(
				'public'             => true,
				'hierarchical'       => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_nav_menus' => false,
				'show_admin_column'  => true,
				'show_tagcloud'      => false,
				'query_var'          => true,
				'rewrite'            => array('slug' => 'project-category', 'with_front' => false),
				'labels'             => array(
					'name'              => __( 'Categories', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'singular_name'     => __( 'Categories', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'search_items'      => __( 'Search categories', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'all_items'         => __( 'All categories', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'parent_item'       => __( 'Parent categories', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'parent_item_colon' => __( 'Parent categories:', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'edit_item'         => __( 'Edit category', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'update_item'       => __( 'Update categories', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'add_new_item'      => __( 'Add new category', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'new_item_name'     => __( 'Category name', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'menu_name'         => __( 'Categories', BASEMENT_PORTFOLIO_TEXTDOMAIN )
				)
			));


			register_taxonomy( $this->custom_fields, $this->post_type, array(
				'public'             => true,
				'hierarchical'       => true,
				'publicly_queryable' => false,
				'show_in_nav_menus' => false,
				'show_ui'            => true,
				'show_tagcloud'      => false,
				'show_admin_column'  => true,
				'query_var'          => true,
				'rewrite'            => array('slug' => 'custom-fields', 'with_front' => false),
				'labels'             => array(
					'name'              => __( 'Custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'singular_name'     => __( 'Custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'search_items'      => __( 'Search custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'all_items'         => __( 'All custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'parent_item'       => __( 'Parent custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'parent_item_colon' => __( 'Parent custom fields:', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'edit_item'         => __( 'Edit custom field', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'update_item'       => __( 'Update custom field', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'add_new_item'      => __( 'Add new custom field', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'new_item_name'     => __( 'Custom field name', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'menu_name'         => __( 'Custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN )
				)
			));


			do_action( 'project_category_after_register_taxonomy' );
		}
	}


	/**
	 * Remove Preview&Edit links
	 *
	 * @param $actions
	 * @param $post
	 * @return mixed
	 */
	public function project_actions( $actions, $post ) {
		if ( $post->post_type === $this->post_type ) {
			#unset($actions['view']);
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
	 * Custom notify for Projects (single project)
	 *
	 * @param $messages
	 * @return mixed
	 */
	public function project_updated_messages( $messages ) {
		global $post;

		$post_ID = $post->ID;
		$messages[$this->post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Project updated.', BASEMENT_PORTFOLIO_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			3 => __( 'Custom field deleted.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			4 => __( 'Project updated.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Project restored to revision from %s', BASEMENT_PORTFOLIO_TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Project published.', BASEMENT_PORTFOLIO_TEXTDOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Project saved.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			8 => sprintf( __( 'Project submitted.', BASEMENT_PORTFOLIO_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Project scheduled for: <strong>%1$s</strong>.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				date_i18n( __( 'M j, Y @ G:i', BASEMENT_PORTFOLIO_TEXTDOMAIN ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Project draft updated.', BASEMENT_PORTFOLIO_TEXTDOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}


	/**
	 * Custom notify for Project (all projects)
	 *
	 * @param $bulk_messages
	 * @param $bulk_counts
	 * @return mixed
	 */
	public function bulk_project_updated_messages( $bulk_messages, $bulk_counts ) {
		global $post;

		$bulk_messages[$this->post_type] = array(
			'updated'   => _n( '%s project updated.', '%s projects updated.', $bulk_counts['updated'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'locked'    => _n( '%s project not updated, somebody is editing it.', '%s projects not updated, somebody is editing them.', $bulk_counts['locked'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'deleted'   => _n( '%s project permanently deleted.', '%s projects permanently deleted.', $bulk_counts['deleted'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'trashed'   => _n( '%s project moved to the Trash.', '%s projects moved to the Trash.', $bulk_counts['trashed'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'untrashed' => _n( '%s project restored from the Trash.', '%s projects restored from the Trash.', $bulk_counts['untrashed'], BASEMENT_PORTFOLIO_TEXTDOMAIN ),
		);

		return $bulk_messages;
	}


	/**
	 * Register CPT Grid Project
	 */
	protected function register_type() {
		if ( !post_type_exists( $this->post_type ) ) {
			register_post_type(
				apply_filters( 'basement_cpt_grid_single_project_register_filter_name', $this->post_type ),
				$this->post_type_args
			);
		}
	}


	/**
	 * Get CPT name
	 *
	 * @return string
	 */
	public function project_cpt_name() {
		return $this->post_type;
	}


	/**
	 * Get Tax name
	 *
	 * @return string
	 */
	public function project_tax_name() {
		return $this->tax_name;
	}


	/**
	 * Add CPT menu
	 */
	public function single_project_menu() {
		add_submenu_page( 'edit.php?post_type=' . $this->portfolio, __('Add new project', BASEMENT_PORTFOLIO_TEXTDOMAIN), __('Add project', BASEMENT_PORTFOLIO_TEXTDOMAIN), 'edit_posts', 'post-new.php?post_type='. $this->post_type , null );
		add_submenu_page( 'edit.php?post_type=' . $this->portfolio, __('Categories', BASEMENT_PORTFOLIO_TEXTDOMAIN), __('Categories', BASEMENT_PORTFOLIO_TEXTDOMAIN), 'edit_posts', 'edit-tags.php?taxonomy=' . $this->tax_name . '&post_type=' . $this->post_type , null );
		add_submenu_page( 'edit.php?post_type=' . $this->portfolio, __('Custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN), __('Custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN), 'edit_posts', 'edit-tags.php?taxonomy=' . $this->custom_fields . '&post_type=' . $this->post_type , null );
	}



	/**
	 * Highlights the correct top level admin menu item for post type add screens.
	 */
	public function menu_highlight() {
		global $parent_file, $submenu_file, $post_type;

		switch ( $post_type ) {
			case $this->post_type :
				$screen = get_current_screen();
				if ( $screen && $screen->taxonomy === $this->tax_name ) {
					$submenu_file = 'edit-tags.php?taxonomy=' . $this->tax_name . '&post_type=' . $this->post_type;
					$parent_file  = 'edit.php?post_type=' . $this->portfolio;
				}

				if ( $screen && $screen->taxonomy === $this->custom_fields ) {
					$submenu_file = 'edit-tags.php?taxonomy=' . $this->custom_fields . '&post_type=' . $this->post_type;
					$parent_file  = 'edit.php?post_type=' . $this->portfolio;
				}
				break;
		}
	}



	/**
	 * Init CPT Grid Project
	 */
	protected function fill_post_type_args() {
		$this->post_type_args = apply_filters(
			'basement_cpt_grid_single_project_args',
			array (
				'post_type'           => $this->post_type,
				'description'         => __( 'Projects', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=portfolio',
				'query_var'           => true,
				'show_in_nav_menus'   => true,
				'rewrite'             => array( 'slug' => 'single-work' ),
				'capability_type'     => 'post',
				'has_archive'         => false,
				'exclude_from_search' => true,
				'hierarchical'        => false,
				'supports'            => array('title','thumbnail','editor'),
				'labels' => array (
					'name'               => __( 'All projects', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'singular_name'      => __( 'All projects', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'menu_name'          => __( 'All projects', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'name_admin_bar'     => __( 'Projects', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'all_items'          => __( 'All projects', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'add_new'            => __( 'Add new project', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'add_new_item'       => __( 'Add new project', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'edit_item'          => __( 'Edit project', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'new_item'           => __( 'New project', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'view_item'          => __( 'View project', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'search_items'       => __( 'Search projects', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'not_found'          => __( 'Projects not found', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'parent_item_colon'  => __( 'Parent project:',  BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'not_found_in_trash' => __( 'No projects found in Trash.', BASEMENT_PORTFOLIO_TEXTDOMAIN )
				)
			)
		);
	}


	/**
	 * Remove unnecessary Meta boxes
	 */
	public function remove_cpt_meta_boxes() {
		remove_meta_box( 'icl_div_config', $this->post_type, 'normal' );
		remove_meta_box( 'mymetabox_revslider_0', $this->post_type , 'normal' );
		remove_meta_box( 'project_custom_fieldsdiv', $this->post_type , 'side' );
	}

}

Basement_Project_Cpt::init();
