<?php
defined('ABSPATH') or die();

class Basement_Cpt {

	protected $post_type = 'post';
	protected $post_type_args = array();
	protected $use_in_slides = false;
	protected $use_in_tiles = false;

	/**
	 * Show featured image as a column in admin post type list
	 * @var boolean
	 */
	protected $show_thumbnail_admin_column = false;
	protected $show_id_admin_column = false;

	protected $duplicate = false;

	static protected $setting_panel_config = array();

	public function __construct( $post_type = '' ) {
		if ( $post_type ) {
			$this->post_type = $post_type;
		}

		 $this->fill_post_type_args();
		 $this->register_type();
		 $this->register_taxonomies();
		 $this->add_hooks();

		if ( $this->duplicate ) {
			add_action(
				'admin_action_copy_' . $this->post_type,
				array( &$this, 'duplicate' )
			);
			add_filter(
				'post_row_actions',
				array( &$this, 'duplicate_link' ),
				10, 2
			);
		}

		add_action( 
			'add_meta_boxes_' . $this->post_type,
			array( &$this, '_add_parameters_meta_box' ) 
		);

		add_action( 
			'add_meta_boxes_' . $this->post_type,
			array( &$this, 'add_meta_boxes' ) 
		);

		add_action( 
			'do_meta_boxes', 
			array( &$this, 'do_meta_boxes' )
		);

		/* Admin columns */
		add_filter(
			'manage_edit-' . $this->post_type . '_columns', 
			array( &$this, 'filter_admin_list_header' )
		);

		add_action( 
			'manage_' . $this->post_type . '_posts_custom_column', 
			array( &$this, 'filter_admin_list_columns' ),
			10, 
			2 
		);

		if ( $this->show_thumbnail_admin_column ) {
			add_filter( 
				'manage_edit-' . $this->post_type . '_columns', 
				array( &$this, 'add_featured_image_column_header' )
			);

			add_action( 
				'manage_' . $this->post_type . '_posts_custom_column', 
				array( &$this, 'add_featured_image_column' ),
				10, 
				2 
			);
		}

		if ( $this->show_id_admin_column ) {
			add_filter(
					'manage_' . $this->post_type . '_posts_columns',
					array( &$this, 'add_id_column_header' ),
					10
			);

			add_action(
					'manage_' . $this->post_type . '_posts_custom_column',
					array( &$this, 'add_id_column' ),
					10,
					2
			);
			add_filter(
					'manage_edit-' . $this->post_type . '_sortable_columns',
					array( &$this, 'sortable_id_column' )
			);
		}


		/* Parameners panel config */
		add_filter(
			BASEMENT_TEXTDOMAIN . '_' . $this->post_type . '_panel_config',
			array( &$this, 'filter_parameters_panel_config' )
		);

		/* Parameners panel config latest */
		add_filter(
			BASEMENT_TEXTDOMAIN . '_' . $this->post_type . '_panel_config_latest',
			array( &$this, 'filter_parameters_panel_config_latest' )
		);

		/**
		 * TODO: add SEO section for all public CPTs
		 */

		add_action(
			'save_post', 
			array( &$this, 'pre_save_post' )
		);

		if ( $this->use_in_slides ) {
			add_filter( 'basement_slider_slides_post_types', array( &$this, 'add_post_type_to_config' ) );
		}

		if ( $this->use_in_tiles ) {
			add_filter( 'basement_tiles_group_tiles_post_types', array( &$this, 'add_post_type_to_config' ) );
		}

	}

	public function duplicate_link( $actions, $post ) {
		if ( $post->post_type === $this->post_type ) {
			$actions['duplicate'] = '<a href="admin.php?action=copy_' . $this->post_type . '&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
		}
		return $actions;
	}

	public function duplicate () {
		global $wpdb;



		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'copy_' . $this->post_type == $_REQUEST['action'] ) ) ) {
			wp_die('No post to duplicate has been supplied!');
		}


		$post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);

		$post = get_post( $post_id );


		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;


		if (isset( $post ) && $post != null) {


			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title . '_copy',
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);


			$new_post_id = wp_insert_post( $args );


			$taxonomies = get_object_taxonomies($post->post_type);
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
				wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
			}


			$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
			if (count($post_meta_infos)!=0) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach ($post_meta_infos as $meta_info) {
					$meta_key = $meta_info->meta_key;
					$meta_value = addslashes($meta_info->meta_value);
					$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query.= implode(" UNION ALL ", $sql_query_sel);
				$wpdb->query($sql_query);
			}



			wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;
		} else {
			wp_die('Post creation failed, could not find original post: ' . $post_id);
		}

	}


	protected function fill_post_type_args() {}

	protected function register_type() {}

	protected function register_taxonomies() {}

	protected function add_hooks() {}

	public function add_meta_boxes() {}

	public function do_meta_boxes() {}

	public function _add_parameters_meta_box() {
		global $post;
		self::$setting_panel_config = apply_filters( BASEMENT_TEXTDOMAIN . '_' . $this->post_type . '_panel_config', array() );
		self::$setting_panel_config = apply_filters( BASEMENT_TEXTDOMAIN . '_post_type_panel_config', self::$setting_panel_config );

		if ( self::$setting_panel_config ) {
			add_meta_box( 
				BASEMENT_TEXTDOMAIN . '_metabox', 
				__( 'Parameters', BASEMENT_TEXTDOMAIN ),
				array( &$this, '_create_parameters_meta_box' ), 
				$this->post_type, 
				'normal', 
				'core' 
			);
		}

	}

	public function _create_parameters_meta_box() {

		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$panel = $dom->appendChild( $dom->createElement( 'div' ) );
		$panel->setAttribute( 'id', 'basement_post_parameters_panel' );
		/**
		 * Setting_Panel config filter: basement_settings_post_panel_config
		 */
		$panel->appendChild(
			$dom->importNode( 
				Basement_Settings_Panel::instance()->create_panel( 
					apply_filters(
						BASEMENT_TEXTDOMAIN . '_' . $this->post_type . '_panel_config_latest',
						self::$setting_panel_config
					),
					apply_filters(
						BASEMENT_TEXTDOMAIN . '_' . $this->post_type . '_params',
						array(
							'no_form' => true,
							'no_wrap_class' => true
						)
					)
				), true 
			) 
		);
		echo $dom->saveHTML();
	}

	public function filter_admin_list_header( $columns ) {
		return $columns;
	}

	public function filter_admin_list_columns( $column_name, $post_id ) {
		return $column_name;
	}


	public function add_featured_image_column_header( $columns ) {
		if ( !isset($columns['icon'] ) ) {
			$columns = array_slice( $columns, 0, 1, true ) + array( 'icon' => '<div class="dashicons-before dashicons-format-image" style="text-align: center; color: #555;"></div>' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
		}
		return $columns;
	}

	public function add_featured_image_column( $column_name, $post_id ) {
		switch ( $column_name ) {
			case 'icon':
				if ( !( $image_src = apply_filters( 'filter_' . $this->post_type . '_admin_column_image_src', '', $post_id ) ) ) {
					$image_src = wp_get_attachment_thumb_url( get_post_thumbnail_id( $post_id ) );
				}
				echo '<div style="max-width: 100px;"><a href="' . get_edit_post_link( $post_id ) . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', BASEMENT_TEXTDOMAIN ), get_the_title( $post_id ) ) ) . '">';
				if ( $image_src ) {
					echo '<img style="max-width: 100%;" src="' . $image_src .'" />';
				}
				echo '</a></div>';
				break;
			default: break;
		}
		return $column_name;
	}

	public function add_id_column_header( $columns ) {
		if( !isset( $columns['id'] ) ) {
			$columns = array_slice( $columns, 0, 2, true ) + array( 'id' => '<strong>ID</strong>' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
		}
		return $columns;
	}

	public function add_id_column($column_name, $post_id) {
		if ($column_name == 'id') {
			echo '<strong>' . $post_id . '</strong>';
		}
	}

	function sortable_id_column( $columns ) {
		$columns['id'] = 'id';
		return $columns;
	}



	public function filter_parameters_panel_config( $config ) {
		return $config;
	}

	public function filter_parameters_panel_config_latest( $config ) {
		return $config;
	}
	
	public function add_post_type_to_config( $post_types ) {
		$post_types[] = $this->post_type;
		return $post_types;
	}

	public function pre_save_post() {
		global $post;
		if ( !( $post instanceof WP_Post ) ) {
			return;
		}
		if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || $post->post_type != $this->post_type ) {
			return $post->ID;
		}

		$this->save_post();
	}

	public function save_post() {}


}