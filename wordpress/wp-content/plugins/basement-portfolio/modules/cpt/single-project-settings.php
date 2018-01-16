<?php
defined('ABSPATH') or die();


class Basement_Project_Settings {

	private static $instance = null;

	private $meta = '_basement_meta_project_';

	public function __construct() {
		add_action( 'add_meta_boxes', array( &$this, 'generate_project_param_meta_box' ) );

		add_filter( 'admin_post_thumbnail_html', array( &$this, 'thumbnail_settings' ), 10, 2 );

		if( defined('DOING_AJAX') && DOING_AJAX ) {
			// Remove custom field
			add_action('wp_ajax_remove-custom-field', array( &$this, 'remove_custom_field' ) );
			add_action('wp_ajax_nopriv_remove-custom-field', array( &$this, 'remove_custom_field' ) );
		}
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if (null === self::$instance) {
			self::$instance = new Basement_Project_Settings();
		}
		return self::$instance;
	}


	/**
	 * Register Meta Box
	 */
	public function generate_project_param_meta_box(){
		add_meta_box(
			'project_parameters_meta_box',
			__( 'Parameters', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			array( &$this, 'render_project_param_meta_box' ),
			'single_project',
			'normal',
			'core'
		);
	}


	/**
	 * Filtrate Params For project
	 *
	 * @param $meta
	 * @param null $id
	 * @return mixed|void
	 */
	private function filtrate_project_meta_data ( $meta, $id = null ) {
		if( !empty($meta) ) {


			$unsets = array(
				'video_gallery_type' => array('dots_type', 'dots_color', 'dots_size', 'dots_position', 'dots_position_vertical', 'dots_position_horizontal', 'arrow_type', 'arrow_color', 'arrow_size', 'arrow_position', 'arrow_position_vertical', 'arrow_position_horizontal', 'gallery_size', 'popup', 'gallery', 'image_height'),
				'image_gallery_type' => array('video_url', 'video_type')
			);
			$meta_gellery_type = isset($meta['gallery_type']) ? $meta['gallery_type'] : '';
			if(!empty($unsets[$meta_gellery_type])) {
				foreach ( $unsets[ $meta_gellery_type ] as $unset ) {
					unset( $meta[ $unset ] );
				}
			}

			if(empty($meta['video_url'])) {
				unset($meta['video_url']);
				unset($meta['video_type']);
			}

			$meta_pagination = !empty($meta['pagination']) ? $meta['pagination'] : '';

			if ( $meta_pagination === 'no' ) {
				unset( $meta['pagination'] );
				unset( $meta['prev_link'] );
				unset( $meta['next_link'] );
				unset( $meta['grid_url'] );
			}


			if ( empty( $meta['image_height'] ) ) {
				unset( $meta['image_height'] );
			}


			if ( empty( $meta['custom_fields'] ) ) {
				unset( $meta['custom_fields'] );
			}

			if($meta['click_type'] === 'popup' && empty($meta['image'])) {
				if ( has_post_thumbnail($id) && ! post_password_required($id) && ! is_attachment() ) {
					$meta['image'] = get_post_thumbnail_id($id);
				} else {
					$meta['click_type'] = 'link';
				}
			} elseif ($meta['click_type'] === 'popup' && !empty($meta['image'])) {
				unset($meta['normal_link']);
				unset($meta['video_link']);
			}

			if($meta['click_type'] === 'link' && empty($meta['normal_link'])) {
				unset($meta['image']);
				unset($meta['click_type']);
				unset($meta['normal_link']);
				unset($meta['video_link']);
			} elseif($meta['click_type'] === 'link' && !empty($meta['normal_link'])) {
				unset($meta['image']);
				unset($meta['video_link']);
			}

			if($meta['click_type'] === 'video' && empty($meta['video_link'])) {
				unset($meta['image']);
				unset($meta['click_type']);
				unset($meta['normal_link']);
				unset($meta['video_link']);
			} elseif($meta['click_type'] === 'video' && !empty($meta['video_link'])) {
				unset($meta['image']);
				unset($meta['normal_link']);
			}

			if(empty($meta['title'])) {
				unset($meta['title']);
			}


			if(empty($meta['gallery'])) {
				unset($meta['gallery']);
			}



			$dots_unsets = array('dots_color', 'dots_size', 'dots_position', 'dots_position_vertical', 'dots_position_horizontal');

			$meta_dots_type = !empty($meta['dots_type']) ? $meta['dots_type'] : '';

			if($meta_dots_type == 'nope') {
				foreach ($dots_unsets as $unset) {
					unset($meta[$unset]);
				}
			}


			$arrows_unsets = array('arrow_color', 'arrow_size', 'arrow_position', 'arrow_position_vertical', 'arrow_position_horizontal');

			$meta_arrow_type = !empty($meta['arrow_type']) ? $meta['arrow_type'] : '';

			if($meta_arrow_type === 'nope') {
				foreach ($arrows_unsets as $unset) {
					unset($meta[$unset]);
				}
			}

			if( !empty($meta['arrow_position']) && $meta['arrow_position'] == 'inrow' ) {
				unset($meta['arrow_position_vertical']);
				unset($meta['arrow_position_horizontal']);
			} else {
				if( !empty($meta['arrow_position_vertical']) && $meta['arrow_position_vertical'] == 'side') {
					unset($meta['arrow_position_horizontal']);
				}
			}



			if($meta['click_type'] === 'default') {
				if(empty($meta['video_link'])) {
					unset($meta['video_link']);
				}


				if(empty($meta['image'])) {
					if ( has_post_thumbnail($id) && ! post_password_required($id) && ! is_attachment() ) {
						$meta['image'] = get_post_thumbnail_id($id);
					} else {
						unset($meta['image']);
					}
				}
				if(empty($meta['normal_link'])) {
					unset($meta['normal_link']);
				}
			}
		}

		return apply_filters('basement_project_filtrate_params', $meta, $id);
	}


	/**
	 * Get project Thumbnail
	 *
	 * @param null $id
	 * @param string $size
	 * @param array $attr
	 * @return bool|mixed|void
	 */
	private function get_project_thumbnail( $id = null, $size = 'full', $attr = array() ){
		if( empty($id) )
			return false;

		$thumbnail = array();

		if ( has_post_thumbnail($id) && ! post_password_required($id) && ! is_attachment() ) {
			$thumbnail['img'] = get_the_post_thumbnail( $id, $size, $attr );
			$thumbnail['url'] = get_the_post_thumbnail_url( $id, $size );
		}

		return apply_filters('basement_project_generate_thumbnail', $thumbnail, $id, $size, $attr);
	}


	/**
	 * Get term for project
	 *
	 * @param null $id
	 * @return array|bool|false|WP_Error
	 */
	private function get_project_terms( $id = null ) {
		if( empty($id) )
			return false;

		return get_the_terms( $id, 'project_category' );
	}


	/**
	 * Generate Params For project
	 *
	 * @param $meta
	 * @param $id
	 * @return mixed|void
	 */
	private function generate_project_meta_data( $meta, $id = null ) {
		$params = array();

		if( !empty($meta) ) {
			foreach ($meta as $key => $value) {
				if (strpos($key, substr($this->meta, 1)) != false) {
					if( $key !== '_basement_meta_project_snap_grid' && $key !== '_basement_meta_project_custom_field' && $key !== '_basement_meta_project_featured' ) {
						$params[substr($key, 23)] = wp_strip_all_tags(array_shift($value));
					}  elseif( $key === $this->meta . 'snap_grid' ) {
						$snap_grid = get_post_meta($id, $this->meta . 'snap_grid', true);

						foreach ($snap_grid as $snap_value) {
							if(!empty($snap_value)) {
								$params[substr($key, 23)][] = $snap_value;
							}
						}

					} elseif( $key === $this->meta . 'featured' ) {
						$featured = get_post_meta($id, $this->meta . 'featured', true);
						if(!empty($featured)) {
							foreach (explode(',',$featured) as $feature) {
								$feature = get_post($feature);
								if(!empty($feature)) {
									$params[substr($key, 23)][] = $feature;
								}
							}
						}
					} elseif ($key === '_basement_meta_project_custom_field') {
						$custom_field = get_post_meta($id, '_basement_meta_project_custom_field', true);

						if(!empty($custom_field)) {
							foreach ($custom_field as $custom_value) {
								if (!empty($custom_value)) {
									$params[substr($key, 23)][$custom_value] = get_post_meta($id, '_basement_meta_project_' . $custom_value, true);
								}
							}
						}

					}
				}
			}
		}

		return apply_filters('basement_project_generate_params', $params, $id);
	}



	/**
	 * Get beautiful&smart array of params
	 *
	 * @param null $id
	 * @param string $thumbnail_size
	 * @param array $thumbnail_attr
	 * @return array|bool
	 */
	public function get_project( $id = null, $thumbnail_size = '', $thumbnail_attr = array() ) {
		if( empty($id) )
			return false;

		$project = array();

		// Get params
		$params = $this->generate_project_meta_data( get_post_meta(absint($id)), $id );


		// Filtrate params
		$filtrate_params = $this->filtrate_project_meta_data($params, absint($id));

		// Get thumbnail
		$thumbnail = $this->get_project_thumbnail(absint($id), $thumbnail_size ? $thumbnail_size : $filtrate_params['thumbnail_size'], $thumbnail_attr);

		// Get terms
		$terms = $this->get_project_terms(absint($id));


		if(!empty($terms)) {
			$project['terms'] = $terms;
		}


		if(!empty($filtrate_params)) {
			$project['params'] = $filtrate_params;
		}

		$project['thumbnail'] = $thumbnail;

		if(empty($project['thumbnail']))
			return false;

		return $project;
	}


	/**
	 * Add settings to thumbnail
	 *
	 * @param $content
	 * @param $post_id
	 * @return string
	 */
	public function thumbnail_settings( $content, $post_id ) {
		$thumb_settings = '';

		if(get_post_type($post_id) === 'single_project') {
			$thumb_settings = $this->thumbnail_type();
		}
		return $content. $thumb_settings;
	}


	/**
	 * Thumbnail type
	 */
	protected function thumbnail_type() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$type_wrapper = $dom->appendChild( $dom->createElement( 'div' ) );
		$type_wrapper->setAttribute('class','basement-thumbnail-choose');

		$params = array(
			'meta_name' => $this->meta . 'thumbnail_size',
			'values' => array(
				'full'       => __( 'Full', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'large '     => __( 'Large ', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'medium '    => __( 'Medium', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'thumbnail ' => __( 'Thumbnail', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'full'
		);
		$value = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name' => $params['meta_name'],
			'id' => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values' => $params['values']
		) );

		$type_wrapper->appendChild($dom->importNode( $radio->create(), true  ) );

		return $dom->saveHTML($type_wrapper);
	}



	/**
	 * Render Meta Box Parameters
	 */
	public function render_project_param_meta_box( $post ){

		$view  = new Basement_Portfolio_Plugin();
		$view->load_views( $this->project_settings_generate(), array('project-param-meta-box') );
	}


	/**
	 * Generate Panel With Grid Settings
	 *
	 * @param array $config
	 * @return array
	 */
	public function project_settings_generate( $config = array() ) {
		$config[ 'project_settings' ] = array(
			'blocks' => array(
				array(
					'type' => 'dom',
					'title' => __( 'Project grid.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Snap to grid (automatic binding this project to the grid).', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->snap_to_grid()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Width', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Sets width for tile. <b>Works only with Mixed layout mode</b>.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->width_tile()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Title', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Sets the title project.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->title()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Full image', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'The image in the pop-up. If the full image is not exist, uses a preview.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->full_image()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Click type', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Sets the tile behavior when clicking on it.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->click_type()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Filter', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Sets the filter style for the tile.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input'       => $this->tile_filter()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Filter behavior', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Sets the filter behavior for the tile when hovering.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input'       => $this->tile_filter_behavior()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Source', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Sets a link to the source.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->link()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Custom fields', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Displays additional information.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->custom_fields()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Pagination', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Sets the next/prev project pagination.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->pagination()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Featured works', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'description' => __( 'Sets similar projects.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'input' => $this->featured()
				)
			)
		);

		return $config;
	}


	/**
	 * Snap to grids
	 */
	protected function snap_to_grid() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$grids = array();

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$container_checkboxes = $container->appendChild( $dom->createElement( 'div' ) );
		$container_checkboxes->setAttribute( 'class', 'basement_group-checkboxes' );

		$posts = get_posts( array(
			'numberposts'     => -1,
			'post_type'       => 'portfolio',
			'post_status'     => 'publish'
		) );
		foreach($posts as $item){ setup_postdata($item);
			$grids[$item->ID] = $item->post_title;
		}
		wp_reset_postdata();

		if(!empty($grids)) {
			$params = array(
				'meta_name'     => $this->meta . 'snap_grid',
				'values'        => $grids,
				'current_value' => array()
			);
			$value  = get_post_meta( $post->ID, $params['meta_name'], true );

			$radios = new Basement_Form_Input_Checkbox_Group( array(
					'name'          => $params['meta_name'],
					'id'            => $params['meta_name'],
					'current_value' => empty( $value ) ? $params['current_value'] : $value,
					'values'        => $params['values']
				)
			);
			$container_checkboxes->appendChild( $dom->importNode( $radios->create(), true ) );
		} else {
			$link = $container_checkboxes->appendChild( $dom->createElement( 'a', __( 'Add at least one grid.',BASEMENT_PORTFOLIO_TEXTDOMAIN ) ) );
			$link->setAttribute('href','post-new.php?post_type=portfolio');
		}

		return $dom->saveHTML($container);
	}


	/**
	 * Tile width
	 */
	protected function width_tile() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$footer_params = array(
			array(
				'key' => 'grids',
				'label' => __('Appearance Settings',BASEMENT_PORTFOLIO_TEXTDOMAIN)
			)
		);
		foreach ($footer_params as $footer_param) {
			$key = !empty($footer_param['key']) ? $footer_param['key'] : false;


			$panel = $container->appendChild( $dom->createElement( 'div' ) );

			if($key == 'grids') {
				$table = $panel->appendChild( $dom->createElement( 'table' ) );
				$table->setAttribute('style','width:100%;');
				$all_params = array(
					array(
						array(
							'label' => __( 'Large devices', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'params'    => array(
								array(
									'name'    => 'lg_width',
									'type'    => 'width',
									'size'    => 'lg',
									'current' => ''
								)
							)
						),
						array(
							'label' => __( 'Medium devices', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'params'    => array(
								array(
									'name'    => 'md_width',
									'type'    => 'width',
									'size'    => 'md',
									'current' => ''
								)
							)
						),
					),
					array(
						array(
							'label' => __( 'Small devices', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'params'    => array(
								array(
									'name'    => 'sm_width',
									'type'    => 'width',
									'size'    => 'sm',
									'current' => ''
								)
							)
						),
						array(
							'label' => __( 'Extra small devices', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'params'    => array(
								array(
									'name'    => 'xs_width',
									'type'    => 'width',
									'size'    => 'xs',
									'current' => ''
								)
							)
						)
					)
				);

				foreach ($all_params as $sizes) {
					$tr = $table->appendChild( $dom->createElement( 'tr' ) );

					foreach ($sizes as $size) {

						$td = $tr->appendChild( $dom->createElement( 'td' ) );
						$td->setAttribute('style','padding:10px;width:50%;');

						$label = $td->appendChild( $dom->createElement( 'label', $size['label'] ) );
						$label->setAttribute('style','font-weight:600;display: block;margin-bottom: 5px;');
						foreach ( $size['params'] as $key => $value ) {
							$name = $this->meta . $value['name'];

							$select = $td->appendChild( $dom->createElement( 'select' ) );
							$select->setAttribute( 'id', $name );
							$select->setAttribute( 'name', $name );
							$select->setAttribute( 'style', 'display:block;width:100%;margin-bottom:5px;' );
							foreach ( $this->generate_grid( $value['type'], $value['size'] ) as $inner_key => $inner_value ) {
								$option = $select->appendChild( $dom->createElement( 'option', $inner_value ) );
								$option->setAttribute( 'value', $inner_key );
								$current_value = get_post_meta( $post->ID, $name, true );
								if ( selected( $inner_key, ! empty( $current_value ) ? $current_value : $value['current'], false ) ) {
									$option->setAttribute( 'selected', 'selected' );
								}
							}
						}
					}
				}

			}

		}


		return $dom->saveHTML($container);
	}


	/**
	 * Tile settings
	 */
	private function generate_grid( $type, $size ) {
		$array = array();
		switch ( $type ) {
			case 'width' :
				$array[''] = __( 'Don\'t use width', BASEMENT_PORTFOLIO_TEXTDOMAIN );
				for ( $x = 1; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-' . $x ] = __( 'Width ' . $x, BASEMENT_PORTFOLIO_TEXTDOMAIN );
				}
				break;
			case 'offset' :
				$array[''] = __( 'Don\'t use offset', BASEMENT_PORTFOLIO_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-offset-' . $x ] = __( 'Offset ' . $x, BASEMENT_PORTFOLIO_TEXTDOMAIN );
				}
				break;
			case 'push' :
				$array[''] = __( 'Don\'t use push', BASEMENT_PORTFOLIO_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-push-' . $x ] = __( 'Push ' . $x, BASEMENT_PORTFOLIO_TEXTDOMAIN );
				}
				break;
			case 'pull' :
				$array[''] = __( 'Don\'t use pull', BASEMENT_PORTFOLIO_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-pull-' . $x ] = __( 'Pull ' . $x, BASEMENT_PORTFOLIO_TEXTDOMAIN );
				}
				break;
		}

		return $array;
	}


	/**
	 * Project title
	 */
	protected function title() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$link = new Basement_Form_Input( array(
			'name' => $this->meta . 'title',
			'value' => get_post_meta( $post->ID, $this->meta . 'title', true ),
			'class' => 'basement-full-width'
		) );
		$container = $dom->appendChild($dom->importNode( $link->create(), true  ) );

		return $dom->saveHTML($container);
	}


	/**
	 * Full image
	 */
	protected function full_image() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$image = new Basement_Form_Input_Image( array(
				'name' => $this->meta . 'image',
				'value' => get_post_meta( $post->ID, $this->meta . 'image', true ),
				'text_buttons' => true,
				'upload_text' => __( 'Set full image', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'delete_text' => __( 'Remove full image', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'frame_title' => __( 'Set full image', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'frame_button_text' => __( 'Set full image', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			)
		);
		$container = $dom->appendChild($dom->importNode( $image->create(), true  ) );
		return $dom->saveHTML($container);
	}


	/**
	 * Click type
	 */
	protected function click_type() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name' => $this->meta . 'click_type',
			'values' => array(
				'default' => __( 'Default (value from the Grid)', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'standard' => __( 'Standard', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'popup' => __( 'Popup', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'link' => __( 'Link', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'video' => __( 'Video', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'default'
		);
		$value = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name' => $params['meta_name'],
			'id' => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values' => $params['values']
		) );

		$container = $dom->appendChild($dom->importNode( $radio->create(), true  ) );

		return $dom->saveHTML($container);
	}


	/**
	 * Tile filter behavior
	 */
	protected function tile_filter_behavior() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'filter_behavior',
			'values'        => array(
				'default' => __( 'Default (value from the Grid)', BASEMENT_GALLERY_TEXTDOMAIN ),
				'disable' => __( 'Disable', BASEMENT_GALLERY_TEXTDOMAIN ),
				'add'  => __( 'Add filter', BASEMENT_GALLERY_TEXTDOMAIN ),
				'remove'  => __( 'Remove filter', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'default'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container = $dom->appendChild( $dom->importNode( $radio->create(), true ) );

		return $dom->saveHTML( $container );
	}



	/**
	 * Tile filter
	 */
	protected function tile_filter() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'filter',
			'values'        => array(
				'default' => __( 'Default (value from the Grid)', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'none' => __( 'None', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'graysale'  => __( 'Grayscale', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'default'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container = $dom->appendChild( $dom->importNode( $radio->create(), true ) );

		return $dom->saveHTML( $container );
	}

	/**
	 * Normal link
	 */
	protected function link() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$container_links = $container->appendChild( $dom->createElement( 'div' ) );
		$container_links->setAttribute( 'class', 'basement-links-container' );
		$title_links = $container_links->appendChild( $dom->createElement( 'strong', __('Link:',BASEMENT_PORTFOLIO_TEXTDOMAIN) ) );
		$link = new Basement_Form_Input( array(
			'name' => $this->meta . 'normal_link',
			'value' => get_post_meta( $post->ID, $this->meta . 'normal_link', true ),
			'class' => 'basement-full-width'
		) );
		$container_links->appendChild($dom->importNode( $link->create(), true  ) );



		$container_videos = $container->appendChild( $dom->createElement( 'div' ) );
		$container_videos->setAttribute( 'class', 'basement-links-container' );
		$title_videos = $container_videos->appendChild( $dom->createElement( 'strong', __('Video link:',BASEMENT_PORTFOLIO_TEXTDOMAIN) ) );
		$video = new Basement_Form_Input( array(
			'name' => $this->meta . 'video_link',
			'value' => get_post_meta( $post->ID, $this->meta . 'video_link', true ),
			'class' => 'basement-full-width'
		) );

		$container_videos->appendChild($dom->importNode( $video->create(), true  ) );

		return $dom->saveHTML($container);
	}



	/**
	 * Prev/next pagination
	 */
	protected function pagination() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name' => $this->meta . 'pagination',
			'values' => array(
				'no' => __( 'No', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'no'
		);
		$value = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name' => $params['meta_name'],
			'id' => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values' => $params['values']
		) );

		$container = $dom->appendChild($dom->importNode( $radio->create(), true  ) );
		$container->setAttribute('id','basement_pagination_shoose');

		$settings_pagination = $container->appendChild( $dom->createElement( 'div' ) );
		$settings_pagination->setAttribute('class','basement_pagination_settings');



		$settings_pagination->appendChild($dom->importNode( $this->generate_prev_next_links(array(
			'title' => __('Previous project:', BASEMENT_PORTFOLIO_TEXTDOMAIN),
			'key' => $this->meta . 'prev_link'
		)), true  ) );


		$settings_pagination->appendChild($dom->importNode( $this->generate_prev_next_links(array(
			'title' => __('Next project:', BASEMENT_PORTFOLIO_TEXTDOMAIN),
			'key' => $this->meta . 'next_link'
		)), true  ) );


		$settings_pagination->appendChild($dom->importNode( $this->url_to_grid(), true  ) );


		return $dom->saveHTML($container);
	}


	/**
	 * Url to grid
	 *
	 * @return DOMNode
	 */
	protected function url_to_grid() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$link = new Basement_Form_Input( array(
			'label_text' => __('Link to the page with VC Portfolio shortcode',BASEMENT_PORTFOLIO_TEXTDOMAIN),
			'name' => $this->meta . 'grid_url',
			'value' => get_post_meta( $post->ID, $this->meta . 'grid_url', true ),
			'class' => 'basement-full-width'
		) );
		$container = $dom->appendChild($dom->importNode( $link->create(), true  ) );

		return $container;
	}


	/**
	 * Previous/next project link
	 *
	 * @return DOMNode
	 */
	protected function generate_prev_next_links( $param_link = array()) {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$grids = array();
		$grids[0] = __('Select the project',BASEMENT_PORTFOLIO_TEXTDOMAIN);

		$posts = get_posts( array(
			'numberposts'     => -1,
			'post_type'       => 'single_project',
			'post_status'     => 'publish'
		) );
		foreach($posts as $item){ setup_postdata($item);
			if(absint($post->ID) !== absint($item->ID)) {
				$grids[$item->ID] = $item->post_title;
			}
		}
		wp_reset_postdata();

		$params = array(
			'meta_name' => $param_link['key'],
			'values' => $grids
		);
		$value = get_post_meta( $post->ID, $params['meta_name'], true );


		$radios = new Basement_Form_Input_Select( array(
				'label_text' => $param_link['title'],
				'name' => $params['meta_name'],
				'id' => $params['meta_name'],
				'current_value' => (int)get_post_meta( $post->ID, $params['meta_name'], true ) ? (int)get_post_meta( $post->ID, $params['meta_name'], true ) : 0,
				'values' => $params['values']
			)
		);
		$container = $dom->appendChild($dom->importNode( $radios->create(), true  ) );

		return $container;

	}


	/**
	 * Featured Works
	 */
	protected function featured() {
		global $post;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		// Get current Post ID
		$current_project = $post->ID;

		// Get featured works
		$featured_value =  get_post_meta( $post->ID, $this->meta . 'featured', true );

		// Get all projects
		$projects = get_posts( array(
			'post_type' => 'single_project',
			'numberposts' => -1,
			'exclude' => $featured_value
		) );

		// Create Featured container
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute('class','basement-featured-works cf');



		// Create Featured input
		$featured = new Basement_Form_Input( array(
			'type' => 'hidden',
			'name' => $this->meta . 'featured',
			'value' => $featured_value,
			'class' => 'projects-ids'
		) );
		$container->appendChild($dom->importNode( $featured->create(), true  ) );

		// Create left sortable half
		$projects_half = $container->appendChild( $dom->createElement('div') );
		$projects_half->setAttribute('class','basement-featured-half basement-featured-left-half');
		$projects_half->appendChild( $dom->createElement('h4',__('All projects',BASEMENT_PORTFOLIO_TEXTDOMAIN)) );

		// Create JS sortable list
		$projects_half_sortable = $projects_half->appendChild( $dom->createElement('div') );
		$projects_half_sortable->setAttribute('class','basement-featured-source-sortable ui-sortable');

		// Check current post
		if(count($projects) === 1 && absint($projects[0]->ID) === absint($current_project)) {
			$projects_half_sortable->appendChild( $dom->createElement('p', __('No matching works',BASEMENT_PORTFOLIO_TEXTDOMAIN)) );
		} else {

			if($projects) {
				// Project list
				foreach ($projects as $project) {
					setup_postdata($project);

					if (absint($project->ID) !== absint($current_project)) {
						if (has_post_thumbnail($project) && !post_password_required($project) && !is_attachment()) {
							$project_item = $projects_half_sortable->appendChild($dom->createElement('div'));
							$project_item->setAttribute('class', 'ui-state-default basement-featured-item');
							$project_item->setAttribute('data-post', $project->ID);

							$remove = $project_item->appendChild($dom->createElement('i'));
							$remove->setAttribute('class', 'fa fa-arrow-left basement-featured-remove');


							$project_item->setAttribute('class', 'ui-state-default ui-state-default-image basement-featured-item');
							$img = $project_item->appendChild($dom->createElement('div'));
							$img->setAttribute('style', 'background-image: url(' . get_the_post_thumbnail_url($project, 'thumbnail') . ');');
							$img->setAttribute('class', 'basement-featured-thumb');


							$title = $project_item->appendChild($dom->createElement('strong', esc_html($project->post_title)));
							$title->setAttribute('class', 'basement-featured-title');

							$handle = $project_item->appendChild($dom->createElement('i'));
							$handle->setAttribute('class', 'fa fa-arrow-right basement-source-remove');


							$handle_move = $project_item->appendChild($dom->createElement('i'));
							$handle_move->setAttribute('class', 'fa fa-arrows basement-featured-move');
						}
					}

				}
				wp_reset_postdata();
			} else {
				$projects_half_sortable->appendChild( $dom->createElement('p', __('No matching works',BASEMENT_PORTFOLIO_TEXTDOMAIN)) );
			}

		}

		// Create left featured half
		$featured_half = $container->appendChild( $dom->createElement('div') );
		$featured_half->setAttribute('class','basement-featured-half basement-featured-right-half');
		$featured_half->appendChild( $dom->createElement('h4',__('Featured works',BASEMENT_PORTFOLIO_TEXTDOMAIN)) );



		// Create JS sortable featured list
		$featured_half_sortable = $featured_half->appendChild( $dom->createElement('div') );
		$featured_half_sortable->setAttribute('class','basement-featured-feature-sortable');

		if(!empty($featured_value)) {

			// Get saved projects
			$save_projects = explode(',',$featured_value);


			if($save_projects) {
				// Project list
				foreach ($save_projects as $save_project) {
					$save_project = get_post(absint($save_project));

					if (absint($save_project->ID) !== absint($current_project)) {
						$save_project_item = $featured_half_sortable->appendChild($dom->createElement('div'));
						$save_project_item->setAttribute('class', 'ui-state-default basement-featured-item');
						$save_project_item->setAttribute('data-post', $save_project->ID);

						$remove = $save_project_item->appendChild($dom->createElement('i'));
						$remove->setAttribute('class', 'fa fa-arrow-left basement-featured-remove');

						if (has_post_thumbnail($save_project) && !post_password_required($save_project) && !is_attachment()) {
							$save_project_item->setAttribute('class', 'ui-state-default ui-state-default-image basement-featured-item');
							$img = $save_project_item->appendChild($dom->createElement('div'));
							$img->setAttribute('style', 'background-image: url(' . get_the_post_thumbnail_url($save_project) . ');');
							$img->setAttribute('class', 'basement-featured-thumb');
						}

						$title = $save_project_item->appendChild($dom->createElement('strong', esc_html($save_project->post_title)));
						$title->setAttribute('class', 'basement-featured-title');

						$handle = $save_project_item->appendChild($dom->createElement('i'));
						$handle->setAttribute('class', 'fa fa-arrow-right basement-source-remove');


						$handle_move = $save_project_item->appendChild($dom->createElement('i'));
						$handle_move->setAttribute('class', 'fa fa-arrows basement-featured-move');

					}

				}
			}
		}

		return $dom->saveHTML($container);
	}


	/**
	 * Custom fields
	 */
	protected function custom_fields() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		// Get terms
		$args = array(
			'taxonomy' => 'project_custom_fields',
			'hide_empty' => false,
		);
		$terms = get_terms( $args );

		// Create main wrap block
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute('class','basement_custom-fields-block');


		$div_position_cf = $container->appendChild( $dom->createElement( 'div' ) );
		$div_position_cf->setAttribute('style','margin-bottom:30px;');

		$label = $div_position_cf->appendChild( $dom->createElement( 'strong',__('Placement', BASEMENT_PORTFOLIO_TEXTDOMAIN) ) );
		$label->setAttribute('style','margin-bottom:5px;display:block;');

		$name_position_cf = $this->meta . 'position_custom_fields';
		$value_position_cf = get_post_meta( $post->ID, $name_position_cf, true );
		$position_cf = new Basement_Form_Input_Radio_Group( array(
			'values' => array(
				'bottom' => __( 'Bottom (above footer)', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'top'  => __( 'Top (after page title)', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => !empty($value_position_cf) ? $value_position_cf : 'bottom',
			'id' => $name_position_cf,
			'name' => $name_position_cf
		) );

		$div_position_cf->appendChild( $dom->importNode( $position_cf->create(), true ) );



		// Template for custom fields
		$field_params = array(
			'text' => array(
				array(
					'field_params' => array(
						'label_text' =>  __( 'Text', BASEMENT_PORTFOLIO_TEXTDOMAIN )
					),
					'field'   => 'Basement_Form_Input'
				)
			),
			'textblock' => array(
				array(
					'field_params' => array(
						'label_text' =>  __( 'Text', BASEMENT_PORTFOLIO_TEXTDOMAIN )
					),
					'field'   => 'Basement_Form_Input_Textarea'
				)
			),
			'categories' => array(
				array(
					'field_params' => array(
						'label_text' =>  __( 'Title', BASEMENT_PORTFOLIO_TEXTDOMAIN )
					),
					'field'   => 'Basement_Form_Input'
				)
			),
			'link' => array(
				array(
					'field_params' => array(
						'label_text' =>  __( 'Text', BASEMENT_PORTFOLIO_TEXTDOMAIN )
					),
					'field'   => 'Basement_Form_Input'
				),
				array(
					'field_params' => array(
						'label_text' =>  __( 'Link', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					),
					'field'   => 'Basement_Form_Input'
				),
				array(
					'field_params' => array(
						'label_text' =>  __( 'Open in new window?', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
						'values' => array(
							'yes' => __( 'Yes', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'no'   => __( 'No', BASEMENT_PORTFOLIO_TEXTDOMAIN )
						),
						'current_value' => 'yes'
					),
					'field'   => 'Basement_Form_Input_Radio_Group'
				)
			),
			'button' => array(
				array(
					'field_params' => array(
						'label_text' =>  __( 'Text', BASEMENT_PORTFOLIO_TEXTDOMAIN )
					),
					'field'   => 'Basement_Form_Input'
				),
				array(
					'field_params' => array(
						'label_text' =>  __( 'Link', BASEMENT_PORTFOLIO_TEXTDOMAIN )
					),
					'field'   => 'Basement_Form_Input'
				),
				array(
					'field_params' => array(
						'label_text' =>  __( 'Style', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
						'values' => array(
							'btn-primary' => __('Primary', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'default' => __('Default', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'btn-success' => __('Success', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'btn-info' => __('Info', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'btn-warning' => __('Warning', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'btn-danger' => __('Danger', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'btn-link' => __('Link', BASEMENT_PORTFOLIO_TEXTDOMAIN)
						),
						'current_value' => 'default'
					),
					'field'   => 'Basement_Form_Input_Select'
				),
				array(
					'field_params' => array(
						'label_text' =>  __( 'Size', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
						'values' => array(
							'default' => __('Medium', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'btn-lg' => __('Large', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'btn-sm' => __('Small', BASEMENT_PORTFOLIO_TEXTDOMAIN),
							'btn-xs' => __('Extra Small', BASEMENT_PORTFOLIO_TEXTDOMAIN)
						),
						'current_value' => 'default'
					),
					'field'   => 'Basement_Form_Input_Select'
				)
			)
		);

		// Unique ID
		$id = uniqid('basement_custom-fields-');


		// Hidden input with all terms (custom fields) id`s
		$value_input = get_post_meta( $post->ID, $this->meta . 'custom_fields', true );
		$fields_input = new Basement_Form_Input( array(
			'type'  => 'hidden',
			'name'  => $this->meta . 'custom_fields',
			'value' => $value_input,
			'class' => 'basement_custom-fields-ids'
		) );
		$container->appendChild($dom->importNode( $fields_input->create(), true  ) );


		// Select for choose custom fields
		$select = $container->appendChild( $dom->createElement( 'select' ) );
		$select->setAttribute('data-fields','#'.$id);
		$select->setAttribute('class','basement_custom-fields-select');
		$start_option = $select->appendChild( $dom->createElement( 'option', __( 'Add new field',BASEMENT_PORTFOLIO_TEXTDOMAIN ) ) );
		$start_option->setAttribute('value','');

		foreach ($terms as $term) {
			
			$term_name = $term->name;
			$term_slug = $term->slug;
			$get_term = get_term_by( 'slug', $term_slug, 'project_custom_fields' );
			$term_id = $get_term->term_id;
			
			
			$term_type = get_term_meta( $term_id, 'display_type', true );
			$option = $select->appendChild( $dom->createElement( 'option', $term_name  ) );
			$option->setAttribute('value',$term_slug);
			$option->setAttribute('data-slug',$term_slug);
			$option->setAttribute('data-type',$term_type);
		}


		// JqueryUI sortable container for custom fields
		$fields_block = $container->appendChild( $dom->createElement( 'div' ) );
		$fields_block->setAttribute('id',$id);
		$fields_block->setAttribute('class','basement_custom-fields-sortable');


		$values = get_post_meta( $post->ID, $this->meta . 'custom_field', true );


		if(!empty($value_input)) {
			$index = 1;
			foreach (explode(',',$value_input) as $value_item) {
				
				
				$get_term = get_term_by( 'slug', $value_item, 'project_custom_fields' );
				$term_id = $get_term->term_id;
				$custom_type = get_term_meta( $term_id, 'display_type', true );

				if(!empty($values)) {
					$fields_block->appendChild($dom->importNode($this->custom_fields_builder(
						array(
							'key' => $custom_type,
							'term' => get_term($term_id),
							'snap_field' => $values,
							'index' => $index++,
							'param' => $field_params
						)
					), true));
				}
			}
		}


		$templates_block = $container->appendChild( $dom->createElement( 'div' ) );
		$templates_block->setAttribute('data-templates',$id);
		$templates_block->setAttribute('style','display:none;');
		foreach ($field_params as $key_field => $param_field) {
			$templates_block->appendChild($dom->importNode( $this->custom_template_builder(
				array(
					'id' => uniqid($key_field .'-'),
					'key' => $key_field,
					'param' => $param_field
				)
			), true ) );
		}

		return $dom->saveHTML($container);
	}


	/**
	 * Custom fields builder
	 *
	 * @param array $params
	 * @return DOMNode
	 */
	protected function custom_fields_builder($params = array()) {
		global $post;
		$dom = new DOMDocument('1.0', 'UTF-8');

		extract($params);
		
		
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute('class', 'is-static ui-state-default field-' . $key);
		$container->setAttribute('data-field', esc_attr( $term->slug ));
		$container->setAttribute('data-id', esc_attr( $index ) );
		$container->setAttribute('data-post', esc_attr( $post->ID ));

		$id_input = $container->appendChild( $dom->createElement( 'input' ) );
		$id_input->setAttribute('type', 'hidden');
		$id_input->setAttribute('value', $snap_field[$index]);
		$id_input->setAttribute('class', 'basement-snap-field');
		$id_input->setAttribute('data-name', $this->meta . 'custom_field');
		$id_input->setAttribute('name', $this->meta . 'custom_field['.$index.']');

		$cf = $container->appendChild( $dom->createElement( 'div' ) );
		$cf->setAttribute('class','cf');

		$title = $cf->appendChild( $dom->createElement( 'strong', esc_html($term->name) ) );
		$title->setAttribute('class','custom-field-title');

		$handle = $cf->appendChild( $dom->createElement( 'i' ) );
		$handle->setAttribute('class','fa fa-arrows custom-field-handle');

		$remove = $cf->appendChild( $dom->createElement( 'a' ) );
		$remove->setAttribute('class','custom-field-remove');
		$remove->setAttribute('href','#');
		$fa_remove = $remove->appendChild( $dom->createElement( 'i' ) );
		$fa_remove->setAttribute('class','fa fa-remove');

		$edit = $cf->appendChild( $dom->createElement( 'a', __('Edit', BASEMENT_PORTFOLIO_TEXTDOMAIN) ) );
		$edit->setAttribute('class','button-secondary button custom-field-edit');

		$edit_area = $container->appendChild( $dom->createElement( 'div' ) );
		$edit_area->setAttribute('class', 'fast-edit-custom-fields');

		$meta_index = 0;
		if(!empty($param[$key])) {
			foreach ( $param[ $key ] as $block ) {
				$block['field_params']['name'] = $this->meta . $snap_field[ $index ] . '[]';

				$p_meta     = get_post_meta( $post->ID, $this->meta . $snap_field[ $index ], true );
				$meta_value = $p_meta[ $meta_index ++ ];

				$block['field_params'][ $block['field'] === 'Basement_Form_Input_Select' || $block['field'] === 'Basement_Form_Input_Radio_Group' ? 'current_value' : 'value' ] = $meta_value ? $meta_value : '';

				$input = new $block['field']( $block['field_params'] );
				$edit_area->appendChild( $dom->importNode( $input->create(), true ) );
			}
		}
		return $container;
	}


	/**
	 * Custom template builder
	 *
	 * @param array $params
	 * @return DOMNode
	 */
	protected function custom_template_builder($params = array()) {
		global $post;
		$dom = new DOMDocument('1.0', 'UTF-8');

		extract($params);

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute('class', 'ui-state-default field-' . $key);
		$container->setAttribute('data-post', $post->ID);

		$id_input = $container->appendChild( $dom->createElement( 'input' ) );
		$id_input->setAttribute('type', 'hidden');
		$id_input->setAttribute('value', '');
		$id_input->setAttribute('class', 'basement-snap-field');
		$id_input->setAttribute('data-name', $this->meta . 'custom_field');

		$cf = $container->appendChild( $dom->createElement( 'div' ) );
		$cf->setAttribute('class','cf');

		$title = $cf->appendChild( $dom->createElement( 'strong', '' ) );
		$title->setAttribute('class','custom-field-title');

		$handle = $cf->appendChild( $dom->createElement( 'i' ) );
		$handle->setAttribute('class','fa fa-arrows custom-field-handle');

		$remove = $cf->appendChild( $dom->createElement( 'a' ) );
		$remove->setAttribute('class','custom-field-remove');
		$remove->setAttribute('href','#');
		$fa_remove = $remove->appendChild( $dom->createElement( 'i' ) );
		$fa_remove->setAttribute('class','fa fa-remove');

		$edit = $cf->appendChild( $dom->createElement( 'a', __('Edit', BASEMENT_PORTFOLIO_TEXTDOMAIN) ) );
		$edit->setAttribute('class','button-secondary button custom-field-edit');

		$edit_area = $container->appendChild( $dom->createElement( 'div' ) );
		$edit_area->setAttribute('class', 'fast-edit-custom-fields');

		foreach ($param as $block) {
			$input = new $block['field']($block['field_params']);
			$edit_area->appendChild($dom->importNode( $input->create(), true  ) );
		}

		return $container;
	}


	/**
	 * Remove Custom Fields via AJAX
	 */
	public function remove_custom_field() {
		if(!isset($_POST['data']))
			die();

		$params = (array)$_POST['data'];

		extract($params);

		delete_post_meta($post_id, $this->meta . $value);

		update_post_meta( $post_id, $this->meta . 'custom_fields', $ids );

		$list_of_values = get_post_meta($post_id,  $this->meta . 'custom_field', true );
		if (! empty ( $list_of_values ))  {
			unset($list_of_values[$index]);
		}
		update_post_meta( $post_id, $this->meta . 'custom_field', array_combine(range(1, count($list_of_values)), array_values($list_of_values)) );

		wp_send_json( $this->meta . $value );
		die();
	}


}
Basement_Project_Settings::init();