<?php
defined('ABSPATH') or die();


class Basement_Tile_Settings {

	private static $instance = null;

	private $meta = '_basement_meta_tile_';

	public function __construct() {
		add_action( 'add_meta_boxes', array( &$this, 'generate_tile_param_meta_box' ) );

		add_filter( 'admin_post_thumbnail_html', array( &$this, 'thumbnail_settings' ), 10, 2 );
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if (null === self::$instance) {
			self::$instance = new Basement_Tile_Settings();
		}
		return self::$instance;
	}


	/**
	 * Register Meta Box
	 */
	public function generate_tile_param_meta_box(){
		add_meta_box(
			'tile_parameters_meta_box',
			__( 'Parameters', BASEMENT_GALLERY_TEXTDOMAIN ),
			array( &$this, 'render_tile_param_meta_box' ),
			'tile',
			'normal',
			'core'
		);
	}


	/**
	 * Filtrate Params For Tile
	 *
	 * @param $meta
	 * @param null $id
	 * @return mixed|void
	 */
	private function filtrate_tile_meta_data ( $meta, $id = null ) {
		if( !empty($meta) ) {

			/*if(empty($meta['image'])) {
				unset($meta['image']);
			}*/
			
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


			if($meta['click_type'] === 'default') {
				unset($meta['video_link']);

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

		return apply_filters('basement_tile_filtrate_params', $meta, $id);
	}


	/**
	 * Get Tile Thumbnail
	 *
	 * @param null $id
	 * @param string $size
	 * @param array $attr
	 * @return bool|mixed|void
	 */
	private function get_tile_thumbnail( $id = null, $size = 'full', $attr = array() ){
		if( empty($id) )
			return false;

		$thumbnail = array();

		if ( has_post_thumbnail($id) && ! post_password_required($id) && ! is_attachment() ) {
			$thumbnail['img'] = get_the_post_thumbnail( $id, $size, $attr );
			$thumbnail['url'] = get_the_post_thumbnail_url( $id, $size );
		}

		return apply_filters('basement_tile_generate_thumbnail', $thumbnail, $id, $size, $attr);
	}


	private function get_tile_terms( $id = null ) {
		if( empty($id) )
			return false;

		return get_the_terms( $id, 'tile_category' );
	}


	/**
	 * Generate Params For Tile
	 *
	 * @param $meta
	 * @param $id
	 * @return mixed|void
	 */
	private function generate_tile_meta_data( $meta, $id = null ) {
		$params = array();
		if( !empty($meta) ) {
			foreach ($meta as $key => $value) {
				if (strpos($key, substr($this->meta, 1)) != false) {
					$params[substr($key, 20)] = wp_strip_all_tags(array_shift($value));
				}
			}
		}
		return apply_filters('basement_tile_generate_params', $params, $id);
	}



	/**
	 * Get beautiful&smart array of params
	 *
	 * @param null $id
	 * @param string $thumbnail_size
	 * @param array $thumbnail_attr
	 * @return array|bool
	 */
	public function get_tile( $id = null, $thumbnail_size = '', $thumbnail_attr = array() ) {
		if( empty($id) )
			return false;

		$tile = array();

		// Get params
		$params = $this->generate_tile_meta_data( get_post_meta(absint($id)) );

		// Filtrate params
		$filtrate_params = $this->filtrate_tile_meta_data($params, absint($id));

		// Get thumbnail
		$thumbnail = $this->get_tile_thumbnail(absint($id), $thumbnail_size ? $thumbnail_size : $filtrate_params['thumbnail_size'], $thumbnail_attr);

		// Get terms
		$terms = $this->get_tile_terms(absint($id));


		if(!empty($terms)) {
			$tile['terms'] = $terms;
		}


		if(!empty($filtrate_params)) {
			$tile['params'] = $filtrate_params;
		}

		$tile['thumbnail'] = $thumbnail;

		if(empty($tile['thumbnail']))
			return false;

		return $tile;
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

		if(get_post_type($post_id) === 'tile') {
			$thumb_settings = $this->thumbnail_type();
		}
		return $content. $thumb_settings;
	}



	/**
	 * Render Meta Box Parameters
	 */
	public function render_tile_param_meta_box( $post ){


		$view  = new Basement_Gallery_Plugin();
		$view->load_views( $this->tile_settings_generate(), array('tile-param-meta-box') );
	}


	/**
	 * Generate Panel With Grid Settings
	 *
	 * @param array $config
	 * @return array
	 */
	public function tile_settings_generate( $config = array() ) {
		$config[ 'tile_settings' ] = array(
			'blocks' => array(
				array(
					'type' => 'dom',
					'title' => __( 'Full image', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'The image in the pop-up. If the full image is not exist, uses a preview.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input' => $this->full_image()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Click type', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the tile behavior when clicking on it.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input' => $this->click_type()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Filter', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the filter style for the tile.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->tile_filter()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Filter behavior', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the filter behavior for the tile when hovering.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->tile_filter_behavior()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Source', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets a link to the source.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input' => $this->link()
				),
				array(
					'type' => 'dom',
					'title' => __( 'Width', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets width for tile. <b>Works only with Mixed layout mode</b>.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input' => $this->width_tile()
				)
			)
		);

		return $config;
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
				'upload_text' => __( 'Set full image', BASEMENT_GALLERY_TEXTDOMAIN ),
				'delete_text' => __( 'Remove full image', BASEMENT_GALLERY_TEXTDOMAIN ),
				'frame_title' => __( 'Set full image', BASEMENT_GALLERY_TEXTDOMAIN ),
				'frame_button_text' => __( 'Set full image', BASEMENT_GALLERY_TEXTDOMAIN ),
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
				'default' => __( 'Default (value from the Grid)', BASEMENT_GALLERY_TEXTDOMAIN ),
				'popup' => __( 'Popup', BASEMENT_GALLERY_TEXTDOMAIN ),
				'link' => __( 'Link', BASEMENT_GALLERY_TEXTDOMAIN ),
				'video' => __( 'Video', BASEMENT_GALLERY_TEXTDOMAIN )
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
				'default' => __( 'Default (value from the Grid)', BASEMENT_GALLERY_TEXTDOMAIN ),
				'none' => __( 'None', BASEMENT_GALLERY_TEXTDOMAIN ),
				'graysale'  => __( 'Grayscale', BASEMENT_GALLERY_TEXTDOMAIN )
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
				'full' => __( 'Full', BASEMENT_GALLERY_TEXTDOMAIN ),
				'large ' => __( 'Large ', BASEMENT_GALLERY_TEXTDOMAIN ),
				'medium ' => __( 'Medium', BASEMENT_GALLERY_TEXTDOMAIN ),
				'thumbnail ' => __( 'Thumbnail', BASEMENT_GALLERY_TEXTDOMAIN )
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

		$container = $type_wrapper->appendChild($dom->importNode( $radio->create(), true  ) );

		return $dom->saveHTML($type_wrapper);
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
		$title_links = $container_links->appendChild( $dom->createElement( 'strong', __('Link:',BASEMENT_GALLERY_TEXTDOMAIN) ) );
		$link = new Basement_Form_Input( array(
			'name' => $this->meta . 'normal_link',
			'value' => get_post_meta( $post->ID, $this->meta . 'normal_link', true ),
			'class' => 'basement-full-width'
		) );
		$container_links->appendChild($dom->importNode( $link->create(), true  ) );



		$container_videos = $container->appendChild( $dom->createElement( 'div' ) );
		$container_videos->setAttribute( 'class', 'basement-links-container' );
		$title_videos = $container_videos->appendChild( $dom->createElement( 'strong', __('Video link:',BASEMENT_GALLERY_TEXTDOMAIN) ) );
		$video = new Basement_Form_Input( array(
			'name' => $this->meta . 'video_link',
			'value' => get_post_meta( $post->ID, $this->meta . 'video_link', true ),
			'class' => 'basement-full-width'
		) );

		$container_videos->appendChild($dom->importNode( $video->create(), true  ) );

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
				'label' => __('Appearance Settings',BASEMENT_GALLERY_TEXTDOMAIN)
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
							'label' => __( 'Large devices', BASEMENT_GALLERY_TEXTDOMAIN ),
							'params'    => array(
								array(
									'name'    => 'lg_width',
									'type'    => 'width',
									'size'    => 'lg',
									'current' => ''
								),
								/*array(
									'name'    => 'widget_lg_offset',
									'type'    => 'offset',
									'size'    => 'lg',
									'current' => ''
								),
								array(
									'name'    => 'widget_lg_push',
									'type'    => 'push',
									'size'    => 'lg',
									'current' => ''
								),
								array(
									'name'    => 'widget_lg_pull',
									'type'    => 'pull',
									'size'    => 'lg',
									'current' => ''
								)*/
							)
						),
						array(
							'label' => __( 'Medium devices', BASEMENT_GALLERY_TEXTDOMAIN ),
							'params'    => array(
								array(
									'name'    => 'md_width',
									'type'    => 'width',
									'size'    => 'md',
									'current' => ''
								),
								/*array(
									'name'    => 'widget_md_offset',
									'type'    => 'offset',
									'size'    => 'md',
									'current' => ''
								),
								array(
									'name'    => 'widget_md_push',
									'type'    => 'push',
									'size'    => 'md',
									'current' => ''
								),
								array(
									'name'    => 'widget_md_pull',
									'type'    => 'pull',
									'size'    => 'md',
									'current' => ''
								)*/
							)
						),
					),
					array(
						array(
							'label' => __( 'Small devices', BASEMENT_GALLERY_TEXTDOMAIN ),
							'params'    => array(
								array(
									'name'    => 'sm_width',
									'type'    => 'width',
									'size'    => 'sm',
									'current' => ''
								),
								/*array(
									'name'    => 'widget_sm_offset',
									'type'    => 'offset',
									'size'    => 'sm',
									'current' => ''
								),
								array(
									'name'    => 'widget_sm_push',
									'type'    => 'push',
									'size'    => 'sm',
									'current' => ''
								),
								array(
									'name'    => 'widget_sm_pull',
									'type'    => 'pull',
									'size'    => 'sm',
									'current' => ''
								)*/
							)
						),
						array(
							'label' => __( 'Extra small devices', BASEMENT_GALLERY_TEXTDOMAIN ),
							'params'    => array(
								array(
									'name'    => 'xs_width',
									'type'    => 'width',
									'size'    => 'xs',
									'current' => ''
								),
								/*array(
									'name'    => 'widget_xs_offset',
									'type'    => 'offset',
									'size'    => 'xs',
									'current' => ''
								),
								array(
									'name'    => 'widget_xs_push',
									'type'    => 'push',
									'size'    => 'xs',
									'current' => ''
								),
								array(
									'name'    => 'widget_xs_pull',
									'type'    => 'pull',
									'size'    => 'xs',
									'current' => ''
								)*/
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
				$array[''] = __( 'Don\'t use width', BASEMENT_GALLERY_TEXTDOMAIN );
				for ( $x = 1; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-' . $x ] = __( 'Width ' . $x, BASEMENT_GALLERY_TEXTDOMAIN );
				}
				break;
			case 'offset' :
				$array[''] = __( 'Don\'t use offset', BASEMENT_GALLERY_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-offset-' . $x ] = __( 'Offset ' . $x, BASEMENT_GALLERY_TEXTDOMAIN );
				}
				break;
			case 'push' :
				$array[''] = __( 'Don\'t use push', BASEMENT_GALLERY_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-push-' . $x ] = __( 'Push ' . $x, BASEMENT_GALLERY_TEXTDOMAIN );
				}
				break;
			case 'pull' :
				$array[''] = __( 'Don\'t use pull', BASEMENT_GALLERY_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-pull-' . $x ] = __( 'Pull ' . $x, BASEMENT_GALLERY_TEXTDOMAIN );
				}
				break;
		}

		return $array;
	}
	
}
Basement_Tile_Settings::init();