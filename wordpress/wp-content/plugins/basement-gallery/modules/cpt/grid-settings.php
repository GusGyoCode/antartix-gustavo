<?php
defined( 'ABSPATH' ) or die();


class Basement_Grid_Settings {

	private static $instance = null;

	private $meta = '_basement_meta_grid_';

	public function __construct() {

		add_action( 'add_meta_boxes', array( &$this, 'generate_grid_param_meta_box' ) );
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Grid_Settings();
		}

		return self::$instance;
	}


	/**
	 * Register Meta Box
	 */
	public function generate_grid_param_meta_box() {
		add_meta_box(
			'grid_parameters_meta_box',
			__( 'Parameters', BASEMENT_GALLERY_TEXTDOMAIN ),
			array( &$this, 'render_grid_param_meta_box' ),
			'grid',
			'normal',
			'core'
		);
	}


	/**
	 * Filtrate Params For Grid
	 *
	 * @param      $meta
	 * @param null $id
	 *
	 * @return mixed|void
	 */
	private function filtrate_grid_meta_data( $meta, $id = null ) {
		if ( ! empty( $meta ) ) {
			$unsets = array(
				'multirow'  => array(
					'dots_type',
					'dots_color',
					'dots_size',
					'dots_position',
					'dots_position_vertical',
					'dots_position_horizontal',
					'arrow_type',
					'arrow_color',
					'arrow_size',
					'arrow_position',
					'arrow_position_vertical',
					'arrow_position_horizontal',
					'tiles_width',
					'tiles_scroll',
					'tiles_min',
					'tiles_max',
					'auto',
					'duration'
				),
				'singlerow' => array( 'margins', 'load_more', 'load_more_size', 'grid_type', 'pills', 'pills_position' )
			);


			$meta_pills = !empty($meta['pills']) ? $meta['pills'] : '';
			$meta_grid_type = !empty($meta['grid_type']) ? $meta['grid_type'] : '';
			$meta_tiles_type = !empty($meta['tiles_type']) ? $meta['tiles_type'] : '';
			$meta_margins = !empty($meta['margins']) ? $meta['margins'] : '';
			$meta_load_more = !empty($meta['load_more']) ? $meta['load_more'] : '';


			foreach ( $unsets[ $meta['type'] ] as $unset ) {
				unset( $meta[ $unset ] );
			}

			if ( $meta_tiles_type == 'classic' && $meta_margins == 'no' ) {
				$meta['margins'] = 'yes';
			}

			if ( empty( $meta['load_more_size'] ) || $meta_load_more === 'no' ) {
				unset( $meta['load_more_size'] );
				unset( $meta['load_more'] );
			} else {
				unset( $meta['load_more'] );
			}


			if ( $meta_pills == 'hide' ) {
				unset( $meta['pills'] );
				unset( $meta['pills_position'] );
			}


			if ( $meta_grid_type === 'mixed' || $meta_grid_type === 'masonry' || empty( $meta['tiles_height'] ) ) {
				unset( $meta['tiles_height'] );
			}


			if ( empty( $meta['tiles_width'] ) ) {
				unset( $meta['tiles_width'] );
			}


			if ( empty( $meta['tiles_min'] ) ) {
				unset( $meta['tiles_min'] );
			}


			if ( empty( $meta['tiles_max'] ) ) {
				unset( $meta['tiles_max'] );
			}


			if ( empty( $meta['tiles_scroll'] ) ) {
				unset( $meta['tiles_scroll'] );
			}


			$dots_unsets = array(
				'dots_color',
				'dots_size',
				'dots_position',
				'dots_position_vertical',
				'dots_position_horizontal'
			);


			$meta_dots_type = !empty($meta['dots_type']) ? $meta['dots_type'] : '';

			if ( $meta_dots_type == 'nope' ) {
				foreach ( $dots_unsets as $unset ) {
					unset( $meta[ $unset ] );
				}
			}


			$arrows_unsets = array(
				'arrow_color',
				'arrow_size',
				'arrow_position',
				'arrow_position_vertical',
				'arrow_position_horizontal'
			);

			$meta_arrow_type = !empty($meta['arrow_type']) ? $meta['arrow_type'] : '';
			if ( $meta_arrow_type == 'nope' ) {
				foreach ( $arrows_unsets as $unset ) {
					unset( $meta[ $unset ] );
				}
			}


			$meta_arrow_position  = !empty($meta['arrow_position']) ? $meta['arrow_position'] : '';
			$meta_arrow_position_vertical = !empty($meta['arrow_position_vertical']) ? $meta['arrow_position_vertical'] : '';

			if ( $meta_arrow_position == 'inrow' ) {
				unset( $meta['arrow_position_vertical'] );
				unset( $meta['arrow_position_horizontal'] );
			} else {
				if ( $meta_arrow_position_vertical == 'side' ) {
					unset( $meta['arrow_position_horizontal'] );
				}
			}


			if ( empty( $meta['title'] ) || $meta['title_show'] === 'no' ) {
				unset( $meta['title'] );
				unset( $meta['title_show'] );
				unset( $meta['title_position'] );
			} else {
				unset( $meta['title_show'] );
			}


		}

		return apply_filters( 'basement_grid_filtrate_params', $meta, $id );
	}


	/**
	 * Generate Params For Grid
	 *
	 * @param $meta
	 * @param $id
	 *
	 * @return mixed|void
	 */
	private function generate_grid_meta_data( $meta, $id = null ) {
		$params = array();
		if ( ! empty( $meta ) ) {
			foreach ( $meta as $key => $value ) {
				if ( strpos( $key, substr( $this->meta, 1 ) ) != false ) {
					$params[ substr( $key, 20 ) ] = wp_strip_all_tags( array_shift( $value ) );
				}
			}
		}

		return apply_filters( 'basement_grid_generate_params', $params, $id );
	}


	public function get_grid( $id = null ) {
		if ( empty( $id ) ) {
			return false;
		}


		// Get params
		$params = $this->generate_grid_meta_data( get_post_meta( absint( $id ) ) );

		// Filtrate params
		$grid = $this->filtrate_grid_meta_data( $params );

		return $grid;
	}


	/**
	 * Render Meta Box Parameters
	 */
	public function render_grid_param_meta_box( $post ) {


		$view = new Basement_Gallery_Plugin();
		$view->load_views( $this->grid_settings_generate(), array( 'grid-param-meta-box' ) );
	}


	/**
	 * Generate Panel With Grid Settings
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	public function grid_settings_generate( $config = array() ) {
		$config['grid_settings'] = array(
			'blocks' => array(
				array(
					'type'        => 'dom',
					'title'       => __( 'Number of columns', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Select how many columns should be displayed.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->number_cols()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Row type', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Select the appearance of the grid.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->type_grid()
				),
				'singlerow'       => array(
					array(
						'type'        => 'dom',
						'title'       => __( 'Layout', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'An exemplary display of arrows and points in the gallery.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->layout_builder()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Dots settings', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets the style, color, size and position of the dots.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->dots_builder()
					),
					array(
						'type' => 'dom',
						'title' => __( 'Dots visibility', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets the dots visibility for different resolutions.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input' => $this->dots_visibility()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Arrows settings', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets the style, color, size and position of the arrows.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->arrow_builder()
					),
					array(
						'type' => 'dom',
						'title' => __( 'Arrow visibility', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets the arrows visibility for different resolutions.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input' => $this->arrows_visibility()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Auto', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Determines whether the gallery should scroll automatically or not.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->auto_inputs()
					),
					array(
						'type' => 'dom',
						'title' => __( 'Swipe', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets whether the gallery should scroll via swiping gestures (on touch-devices only).', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input' => $this->swipe()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Duration', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Determines the duration of the transition in milliseconds. (Default 500ms).', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->duration_inputs()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Tiles width', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'The width of the items. Use integer value w/o "px" and if number of columns > 1.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->tiles_width()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Tiles min', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'The number of min visible tiles.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->tiles_min()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Tiles max', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'The number of max visible tiles.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->tiles_max()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Tiles scroll', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'The number of tiles to scroll.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->tiles_scroll()
					),
					array(
						'type' => 'dom',
						'title' => __( 'Effects', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Indicates which effect to use for the transition.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input' => $this->effects_inputs()
					),
					array(
						'type' => 'dom',
						'title' => __( 'Easing', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Indicates which easing function to use for the transition. jQuery.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input' => $this->easing_inputs()
					),
				),
				'multirow'        => array(
					array(
						'type'        => 'dom',
						'title'       => __( 'Margins', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'The margins between the tiles. <b>\'No\' option doesn\'t work with tiles type classic</b>.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->margins()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Load more', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Loading tile by pressing a "Load More" button.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->load_more()
					),

					array(
						'type'        => 'dom',
						'title'       => __( 'Grid type', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets the grid type.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->grid_type()
					),

					array(
						'type'        => 'dom',
						'title'       => __( 'Categories pills', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Show category or not.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->pills()
					),
					array(
						'type'        => 'dom',
						'title'       => __( 'Categories pills position', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets the position of the categories.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->pills_position()
					)
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Grid size', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the size(width) of the grid.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->grid_size()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Tiles type', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the tiles type.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->tiles_type()
				),
				'header_position' => array(
					array(
						'type'        => 'dom',
						'title'       => __( 'Tiles header position', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets the alignment of the title and category in tile.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->tiles_title_position()
					),
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Tiles height', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the tiles height. Use integer value w/o "px". <b>Doesn\'t work with Masonry and Mixed grid type</b>.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->tiles_height()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Click type', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the tile behavior when clicking on it.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->click_type()
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
					'type'        => 'dom',
					'title'       => __( 'Top bar style', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the style of the top bar.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->top_bar_style()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Top bar size', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the top bar size (width).', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->top_bar_size()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Margin bottom for top bar', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets bottom margin (in "px") for the top bar.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->top_bar_padding_bottom()
				),
				array(
					'type'        => 'dom',
					'title'       => __( 'Grid title', BASEMENT_GALLERY_TEXTDOMAIN ),
					'description' => __( 'Sets the title name.', BASEMENT_GALLERY_TEXTDOMAIN ),
					'input'       => $this->title()
				),
				'titlegrid'       => array(
					array(
						'type'        => 'dom',
						'title'       => __( 'Grid title position', BASEMENT_GALLERY_TEXTDOMAIN ),
						'description' => __( 'Sets the position of the title.', BASEMENT_GALLERY_TEXTDOMAIN ),
						'input'       => $this->title_position()
					)
				)
			)
		);

		return $config;
	}


	/**
	 * Number columns
	 */
	protected function number_cols() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name' => $this->meta . 'cols',
			'values'    => array(
				1 => __( '1', BASEMENT_GALLERY_TEXTDOMAIN ),
				2 => __( '2', BASEMENT_GALLERY_TEXTDOMAIN ),
				3 => __( '3', BASEMENT_GALLERY_TEXTDOMAIN ),
				4 => __( '4', BASEMENT_GALLERY_TEXTDOMAIN ),
				5 => __( '5', BASEMENT_GALLERY_TEXTDOMAIN ),
				6 => __( '6', BASEMENT_GALLERY_TEXTDOMAIN )
			)
		);

		$select = new Basement_Form_Input_Select( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => (int) get_post_meta( $post->ID, $params['meta_name'], true ) ? (int) get_post_meta( $post->ID, $params['meta_name'], true ) : 4,
			'values'        => $params['values']
		) );

		$container = $dom->appendChild( $dom->importNode( $select->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Type grid
	 */
	protected function type_grid() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'id', 'js-row-choose' );

		$params = array(
			'meta_name'     => $this->meta . 'type',
			'values'        => array(
				'multirow'  => __( 'Multirow', BASEMENT_GALLERY_TEXTDOMAIN ),
				'singlerow' => __( 'Single row', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'multirow'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container->appendChild( $dom->importNode( $radio->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Arrows settings
	 */
	protected function arrow_builder() {
		global $post;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'z_horizontal-list' );
		$container->setAttribute( 'id', 'js-arrowSettings' );

		$dots_settings = array(
			'Type'                => array(
				'settings'      => array(
					'nope' => __( 'Nope', BASEMENT_GALLERY_TEXTDOMAIN ),
					'wobg' => __( 'W/o background', BASEMENT_GALLERY_TEXTDOMAIN ),
					'bg'   => __( 'With background', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'type',
				'disable'       => false,
				'current_value' => 'wobg',
				'meta_name'     => $this->meta . 'arrow_type'
			),
			'Color'               => array(
				'settings'      => array(
					'light'    => __( 'Light', BASEMENT_GALLERY_TEXTDOMAIN ),
					'standart' => __( 'Standart', BASEMENT_GALLERY_TEXTDOMAIN ),
					'dark'     => __( 'Dark', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'color-arrows',
				'disable'       => false,
				'current_value' => 'standart',
				'meta_name'     => $this->meta . 'arrow_color'
			),
			'Size'                => array(
				'settings'      => array(
					'small'  => __( 'Small', BASEMENT_GALLERY_TEXTDOMAIN ),
					'medium' => __( 'Medium', BASEMENT_GALLERY_TEXTDOMAIN ),
					'large'  => __( 'Large', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'size-arrows',
				'disable'       => false,
				'current_value' => 'medium',
				'meta_name'     => $this->meta . 'arrow_size'
			),
			'Position'            => array(
				'settings'      => array(
					'inside'  => __( 'Inside', BASEMENT_GALLERY_TEXTDOMAIN ),
					'outside' => __( 'Outside', BASEMENT_GALLERY_TEXTDOMAIN ),
					'inrow'   => __( 'In row', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'position',
				'disable'       => false,
				'current_value' => 'inside',
				'meta_name'     => $this->meta . 'arrow_position'
			),
			'Vertical Position'   => array(
				'settings'      => array(
					'top'    => __( 'Top', BASEMENT_GALLERY_TEXTDOMAIN ),
					'bottom' => __( 'Bottom', BASEMENT_GALLERY_TEXTDOMAIN ),
					'side'   => __( 'Side', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'y',
				'disable'       => false,
				'current_value' => 'side',
				'meta_name'     => $this->meta . 'arrow_position_vertical'
			),
			'Horizontal Position' => array(
				'settings'      => array(
					'left'   => __( 'Left', BASEMENT_GALLERY_TEXTDOMAIN ),
					'center' => __( 'Center', BASEMENT_GALLERY_TEXTDOMAIN ),
					'right'  => __( 'Right', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'x',
				'disable'       => true,
				'current_value' => 'center',
				'meta_name'     => $this->meta . 'arrow_position_horizontal'
			)
		);

		foreach ( $dots_settings as $title => $settings ) {

			$column = $container->appendChild( $dom->createElement( 'div' ) );

			$value = get_post_meta( $post->ID, $settings['meta_name'], true );

			$atts = array(
				'data-control' => $settings['control']
			);

			if ( $settings['disable'] ) {
				$atts['disabled'] = 'disabled';
			}


			$select_unit = new Basement_Form_Input_Radio_Group( array(
				'label_text'    => $title,
				'name'          => $settings['meta_name'],
				'id'            => $settings['meta_name'],
				'current_value' => empty( $value ) ? $settings['current_value'] : $value,
				'values'        => $settings['settings'],
				'attributes'    => $atts
			) );


			$column->appendChild( $dom->importNode( $select_unit->create(), true ) );
		}

		return $dom->saveHTML( $container );
	}




	/**
	 * Dots visibility
	 */
	protected function dots_visibility() {
		global $post;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'z_horizontal-list' );

		$vosibility = array(
			'lg' => __('Large devices',BASEMENT_GALLERY_TEXTDOMAIN),
			'md' => __('Medium devices',BASEMENT_GALLERY_TEXTDOMAIN),
			'sm' => __('Small devices',BASEMENT_GALLERY_TEXTDOMAIN),
			'xs' => __('Extra small devices',BASEMENT_GALLERY_TEXTDOMAIN)
		);

		foreach ($vosibility as $screen => $label ) {

			$column = $container->appendChild( $dom->createElement( 'div' ) );
			$option = $this->meta.'dots_' . $screen;

			$value = get_post_meta( $post->ID, $option, true );
			$select = new Basement_Form_Input_Select( array(
				'label_text' => $label,
				'values'  => array(
					''  => __( '&mdash; Select &mdash;', BASEMENT_GALLERY_TEXTDOMAIN ),
					'dots-visible-' . $screen => __( 'Show', BASEMENT_GALLERY_TEXTDOMAIN ),
					'dots-hidden-' . $screen  => __( 'Hide', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'name' => $option,
				'id' => $option,
				'current_value' => $value
			) );

			$column->appendChild($dom->importNode( $select->create(), true  ));
		}

		return $dom->saveHTML($container);
	}


	/**
	 * Arrows visibility
	 */
	protected function arrows_visibility() {
		global $post;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'z_horizontal-list' );

		$vosibility = array(
			'lg' => __('Large devices',BASEMENT_GALLERY_TEXTDOMAIN),
			'md' => __('Medium devices',BASEMENT_GALLERY_TEXTDOMAIN),
			'sm' => __('Small devices',BASEMENT_GALLERY_TEXTDOMAIN),
			'xs' => __('Extra small devices',BASEMENT_GALLERY_TEXTDOMAIN)
		);

		foreach ($vosibility as $screen => $label ) {

			$column = $container->appendChild( $dom->createElement( 'div' ) );
			$option = $this->meta.'arrows_' . $screen;

			$value = get_post_meta( $post->ID, $option, true );
			$select = new Basement_Form_Input_Select( array(
				'label_text' => $label,
				'values'  => array(
					''  => __( '&mdash; Select &mdash;', BASEMENT_GALLERY_TEXTDOMAIN ),
					'arrows-visible-' . $screen => __( 'Show', BASEMENT_GALLERY_TEXTDOMAIN ),
					'arrows-hidden-' . $screen  => __( 'Hide', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'name' => $option,
				'id' => $option,
				'current_value' => $value
			) );

			$column->appendChild($dom->importNode( $select->create(), true  ));
		}

		return $dom->saveHTML($container);
	}
	
	
	


	/**
	 * Dots settings
	 */
	protected function dots_builder() {
		global $post;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'z_horizontal-list' );
		$container->setAttribute( 'id', 'js-dotsSettings' );

		$dots_settings = array(
			'Type'                => array(
				'settings'      => array(
					'nope'   => __( 'Nope', BASEMENT_GALLERY_TEXTDOMAIN ),
					'dots'   => __( 'Dots', BASEMENT_GALLERY_TEXTDOMAIN ),
					'number' => __( 'Numbers', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'type',
				'current_value' => 'dots',
				'meta_name'     => $this->meta . 'dots_type'
			),
			'Color'               => array(
				'settings'      => array(
					'light'    => __( 'Light', BASEMENT_GALLERY_TEXTDOMAIN ),
					'standart' => __( 'Standart', BASEMENT_GALLERY_TEXTDOMAIN ),
					'dark'     => __( 'Dark', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'color-dots',
				'current_value' => 'standart',
				'meta_name'     => $this->meta . 'dots_color'
			),
			'Size'                => array(
				'settings'      => array(
					'small'  => __( 'Small', BASEMENT_GALLERY_TEXTDOMAIN ),
					'medium' => __( 'Medium', BASEMENT_GALLERY_TEXTDOMAIN ),
					'large'  => __( 'Large', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'size-dots',
				'current_value' => 'medium',
				'meta_name'     => $this->meta . 'dots_size'
			),
			'Position'            => array(
				'settings'      => array(
					'inside'  => __( 'Inside', BASEMENT_GALLERY_TEXTDOMAIN ),
					'outside' => __( 'Outside', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'position',
				'current_value' => 'inside',
				'meta_name'     => $this->meta . 'dots_position'
			),
			'Vertical Position'   => array(
				'settings'      => array(
					'top'    => __( 'Top', BASEMENT_GALLERY_TEXTDOMAIN ),
					'bottom' => __( 'Bottom', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'y',
				'current_value' => 'bottom',
				'meta_name'     => $this->meta . 'dots_position_vertical'
			),
			'Horizontal Position' => array(
				'settings'      => array(
					'left'   => __( 'Left', BASEMENT_GALLERY_TEXTDOMAIN ),
					'center' => __( 'Center', BASEMENT_GALLERY_TEXTDOMAIN ),
					'right'  => __( 'Right', BASEMENT_GALLERY_TEXTDOMAIN )
				),
				'control'       => 'x',
				'current_value' => 'center',
				'meta_name'     => $this->meta . 'dots_position_horizontal'
			)
		);

		foreach ( $dots_settings as $title => $settings ) {

			$column = $container->appendChild( $dom->createElement( 'div' ) );

			$value = get_post_meta( $post->ID, $settings['meta_name'], true );

			$select_unit = new Basement_Form_Input_Radio_Group( array(
				'label_text'    => $title,
				'name'          => $settings['meta_name'],
				'id'            => $settings['meta_name'],
				'current_value' => empty( $value ) ? $settings['current_value'] : $value,
				'values'        => $settings['settings'],
				'attributes'    => array(
					'data-control' => $settings['control']
				)
			) );
			$column->appendChild( $dom->importNode( $select_unit->create(), true ) );
		}

		return $dom->saveHTML( $container );
	}


	/**
	 * Arrows/Dots Mini Preview
	 */
	protected function layout_builder() {

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'layout-wrapper-builder' );

		$row = $container->appendChild( $dom->createElement( 'div' ) );
		$row->setAttribute( 'class', 'layout-row-builder' );

		$carousel = $row->appendChild( $dom->createElement( 'div' ) );
		$carousel->setAttribute( 'class', 'layout-carousel-builder' );

		$dots = $carousel->appendChild( $dom->createElement( 'div' ) );
		$dots->setAttribute( 'class', 'layout-dots-builder' );

		$nav_dots = $dots->appendChild( $dom->createElement( 'ul' ) );
		$nav_dots->setAttribute( 'class', 'layout-nav-builder' );
		$i1 = $nav_dots->appendChild( $dom->createElement( 'li' ) );
		$i2 = $nav_dots->appendChild( $dom->createElement( 'li' ) );
		$i3 = $nav_dots->appendChild( $dom->createElement( 'li' ) );

		$arrows = $carousel->appendChild( $dom->createElement( 'div' ) );
		$arrows->setAttribute( 'class', 'layout-arrows-builder' );

		$left = $arrows->appendChild( $dom->createElement( 'i' ) );
		$left->setAttribute( 'class', 'layout-arrow-left' );

		$right = $arrows->appendChild( $dom->createElement( 'i' ) );
		$right->setAttribute( 'class', 'layout-arrow-right' );


		$dots_arrow = $carousel->appendChild( $dom->createElement( 'div' ) );
		$dots_arrow->setAttribute( 'class', 'layout-dots-arrows-builder none' );


		$left = $dom->importNode( $left, true );
		$prev = $dots_arrow->appendChild( $left->cloneNode() );

		$nav_dots = $dom->importNode( $nav_dots, true );
		$nav      = $dots_arrow->appendChild( $nav_dots->cloneNode( true ) );

		$right = $dom->importNode( $right, true );
		$next  = $dots_arrow->appendChild( $right->cloneNode() );

		return $dom->saveHTML( $container );
	}


	/**
	 * Margins
	 */
	protected function margins() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container_margin = $container->appendChild($dom->createElement('div'));
		$container_margin->setAttribute('id','js-margin-value-choose');
		$params = array(
			'meta_name'     => $this->meta . 'margins',
			'values'        => array(
				'no'  => __( 'No', BASEMENT_GALLERY_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'no'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container_margin->appendChild( $dom->importNode( $radio->create(), true ) );



		$container_type = $container->appendChild( $dom->createElement( 'div' ) );
		$container_type->setAttribute( 'id', 'margin_value_mode_show_yes' );

		$params_type = array(
			'meta_name'     => $this->meta . 'margin_value',
			'values'        => array(
				's5'   => __( '5', BASEMENT_GALLERY_TEXTDOMAIN ),
				's10'   => __( '10', BASEMENT_GALLERY_TEXTDOMAIN ),
				's15'   => __( '15', BASEMENT_GALLERY_TEXTDOMAIN ),
				's20'   => __( '20', BASEMENT_GALLERY_TEXTDOMAIN ),
				's25'   => __( '25', BASEMENT_GALLERY_TEXTDOMAIN ),
				's30'   => __( '30', BASEMENT_GALLERY_TEXTDOMAIN ),
				's35'   => __( '35', BASEMENT_GALLERY_TEXTDOMAIN ),
				's40'   => __( '40', BASEMENT_GALLERY_TEXTDOMAIN ),
				's45'   => __( '45', BASEMENT_GALLERY_TEXTDOMAIN ),
				's50'   => __( '50', BASEMENT_GALLERY_TEXTDOMAIN ),
				's55'   => __( '55', BASEMENT_GALLERY_TEXTDOMAIN ),
				's60'   => __( '60', BASEMENT_GALLERY_TEXTDOMAIN ),
				's65'   => __( '65', BASEMENT_GALLERY_TEXTDOMAIN ),
				's70'   => __( '70', BASEMENT_GALLERY_TEXTDOMAIN ),
				's75'   => __( '75', BASEMENT_GALLERY_TEXTDOMAIN ),
				's80'   => __( '80', BASEMENT_GALLERY_TEXTDOMAIN ),
				's85'   => __( '85', BASEMENT_GALLERY_TEXTDOMAIN ),
				's90'   => __( '90', BASEMENT_GALLERY_TEXTDOMAIN ),
				's95'   => __( '95', BASEMENT_GALLERY_TEXTDOMAIN ),
				's100'   => __( '100', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 's15'
		);
		$value_type  = get_post_meta( $post->ID, $params_type['meta_name'], true );

		$radio_type = new Basement_Form_Input_Select( array(
			'label_text' => __('Choose the tiles indent',BASEMENT_GALLERY_TEXTDOMAIN),
			'name'          => $params_type['meta_name'],
			'id'            => $params_type['meta_name'],
			'current_value' => empty( $value_type ) ? $params_type['current_value'] : $value_type,
			'values'        => $params_type['values']
		) );

		$container_type->appendChild( $dom->importNode( $radio_type->create(), true ) );



		return $dom->saveHTML( $container );
	}


	/**
	 * Load more enable
	 */
	protected function load_more() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'id', 'js-load-more-choose' );

		$params = array(
			'meta_name'     => $this->meta . 'load_more',
			'values'        => array(
				'no'  => __( 'No', BASEMENT_GALLERY_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'no'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container->appendChild( $dom->importNode( $radio->create(), true ) );


		$input = new Basement_Form_Input( array(
			'type'  => 'number',
			'name'  => $this->meta . 'load_more_size',
			'value' => get_post_meta( $post->ID, $this->meta . 'load_more_size', true )
		) );


		$container_input = $container->appendChild( $dom->createElement( 'div' ) );
		$container_input->setAttribute( 'id', 'load_more_yes' );
		$title = $container_input->appendChild( $dom->createElement( 'strong', __( 'Number of downloads tiles:', BASEMENT_GALLERY_TEXTDOMAIN ) ) );
		$container_input->appendChild( $dom->importNode( $input->create(), true ) );


		return $dom->saveHTML( $container );
	}


	/**
	 * Grid size
	 */
	protected function grid_size() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'grid_size',
			'values'        => array(
				'boxed'     => __( 'Boxed', BASEMENT_GALLERY_TEXTDOMAIN ),
				'fullwidth' => __( 'Full Width', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'boxed'
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
	 * Grid type
	 */
	protected function grid_type() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );


		$container_grid = $container->appendChild( $dom->createElement( 'div' ) );
		$container_grid->setAttribute( 'id', 'js-grid-type-choose' );

		$params = array(
			'meta_name'     => $this->meta . 'grid_type',
			'values'        => array(
				'grid'    => __( 'Grid', BASEMENT_GALLERY_TEXTDOMAIN ),
				'masonry' => __( 'Masonry', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'grid'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container_grid->appendChild( $dom->importNode( $radio->create(), true ) );


		$container_type = $container->appendChild( $dom->createElement( 'div' ) );
		$container_type->setAttribute( 'id', 'layer_mode_show_yes' );

		$params_type = array(

			'meta_name'     => $this->meta . 'layout_mode',
			'values'        => array(
				'default'   => __( 'Default', BASEMENT_GALLERY_TEXTDOMAIN ),
				'mixed'   => __( 'Mixed', BASEMENT_GALLERY_TEXTDOMAIN ),
				//'cellsByRow'   => __( 'CellsByRow', BASEMENT_GALLERY_TEXTDOMAIN ),
				//'cellsByColumn'   => __( 'CellsByColumn', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'default'
		);
		$value_type  = get_post_meta( $post->ID, $params_type['meta_name'], true );

		$radio_type = new Basement_Form_Input_Radio_Group( array(
			'label_text' => __('Choose the layer type',BASEMENT_GALLERY_TEXTDOMAIN),
			'name'          => $params_type['meta_name'],
			'id'            => $params_type['meta_name'],
			'current_value' => empty( $value_type ) ? $params_type['current_value'] : $value_type,
			'values'        => $params_type['values']
		) );

		$container_type->appendChild( $dom->importNode( $radio_type->create(), true ) );


		return $dom->saveHTML( $container );
	}



	/**
	 * Tiles type
	 */
	protected function tiles_type() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'id', 'js-tiles-type-choose' );

		$params = array(
			'meta_name'     => $this->meta . 'tiles_type',
			'values'        => array(
				'hover'   => __( 'Hover', BASEMENT_GALLERY_TEXTDOMAIN ),
				'classic' => __( 'Classic', BASEMENT_GALLERY_TEXTDOMAIN ),
				'simple'  => __( 'Simple', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'hover'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container->appendChild( $dom->importNode( $radio->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Header tile position
	 *
	 * @return string
	 */
	protected function tiles_title_position() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$params = array(
			'meta_name'     => $this->meta . 'tiles_header_position',
			'values'        => array(
				'left'   => __( 'Left', BASEMENT_GALLERY_TEXTDOMAIN ),
				'center' => __( 'Center', BASEMENT_GALLERY_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'left'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container->appendChild( $dom->importNode( $radio->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Tiles height
	 */
	protected function tiles_height() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$input     = new Basement_Form_Input( array(
			'type'  => 'number',
			'name'  => $this->meta . 'tiles_height',
			'value' => get_post_meta( $post->ID, $this->meta . 'tiles_height', true )
		) );
		$container = $dom->appendChild( $dom->importNode( $input->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Tiles width
	 */
	protected function tiles_width() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$input     = new Basement_Form_Input( array(
			'type'  => 'number',
			'name'  => $this->meta . 'tiles_width',
			'value' => get_post_meta( $post->ID, $this->meta . 'tiles_width', true )
		) );
		$container = $dom->appendChild( $dom->importNode( $input->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Tiles min
	 */
	protected function tiles_min() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$input     = new Basement_Form_Input( array(
			'type'  => 'number',
			'name'  => $this->meta . 'tiles_min',
			'value' => get_post_meta( $post->ID, $this->meta . 'tiles_min', true )
		) );
		$container = $dom->appendChild( $dom->importNode( $input->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Auto settings
	 */
	protected function auto_inputs() {
		global $post;

		$select_params = array(
			'meta_name' => $this->meta . 'auto',
			'values'    => array(
				'false' => __( 'No', BASEMENT_GALLERY_TEXTDOMAIN ),
				'true'  => __( 'Yes', BASEMENT_GALLERY_TEXTDOMAIN )
			)
		);

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$select = new Basement_Form_Input_Select( array(
			'name'          => $select_params['meta_name'],
			'id'            => $select_params['meta_name'],
			'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
			'values'        => $select_params['values']
		) );
		$container->appendChild( $dom->importNode( $select->create(), true ) );

		return $dom->saveHTML( $container );
	}

	/**
	 * Swipe settings
	 */
	protected function swipe() {
		global $post;

		$select_params = array(
			'meta_name' => $this->meta . 'swipe',
			'values' => array(
				'disable' => __('Disable', BASEMENT_GALLERY_TEXTDOMAIN),
				'enable' => __('Enable', BASEMENT_GALLERY_TEXTDOMAIN)
			)
		);

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$select = new Basement_Form_Input_Select(array(
			'name' => $select_params['meta_name'],
			'id' => $select_params['meta_name'],
			'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
			'values' => $select_params['values']
		));
		$container->appendChild($dom->importNode( $select->create(), true  ));

		return $dom->saveHTML($container);
	}



	/**
	 * Duration settings
	 */
	protected function duration_inputs() {
		global $post;

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'z_min-block' );

		$input = new Basement_Form_Input( array(
			'name'  => $this->meta . 'duration',
			'value' => get_post_meta( $post->ID, $this->meta . 'duration', true )
		) );
		$container->appendChild( $dom->importNode( $input->create(), true ) );


		return $dom->saveHTML( $container );
	}


	/**
	 * Tiles max
	 */
	protected function tiles_max() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$input     = new Basement_Form_Input( array(
			'type'  => 'number',
			'name'  => $this->meta . 'tiles_max',
			'value' => get_post_meta( $post->ID, $this->meta . 'tiles_max', true )
		) );
		$container = $dom->appendChild( $dom->importNode( $input->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Tiles scroll
	 */
	protected function tiles_scroll() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$input     = new Basement_Form_Input( array(
			'type'  => 'number',
			'name'  => $this->meta . 'tiles_scroll',
			'value' => get_post_meta( $post->ID, $this->meta . 'tiles_scroll', true )
		) );
		$container = $dom->appendChild( $dom->importNode( $input->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Effects settings
	 */
	protected function effects_inputs() {
		global $post;

		$select_params = array(
			'meta_name' => $this->meta . 'tiles_effects',
			'values' => array(
				'fade' => __('Fade', BASEMENT_GALLERY_TEXTDOMAIN),
				'crossfade' => __('Crossfade', BASEMENT_GALLERY_TEXTDOMAIN),
				'scroll' => __('Scroll', BASEMENT_GALLERY_TEXTDOMAIN),
				'none' => __('None', BASEMENT_GALLERY_TEXTDOMAIN),
				'directscroll' => __('Directscroll', BASEMENT_GALLERY_TEXTDOMAIN),
				'cover' => __('Cover', BASEMENT_GALLERY_TEXTDOMAIN),
				'cover-fade' => __('Cover-fade', BASEMENT_GALLERY_TEXTDOMAIN),
				'uncover' => __('Uncover', BASEMENT_GALLERY_TEXTDOMAIN),
				'uncover-fade' => __('Uncover-fade', BASEMENT_GALLERY_TEXTDOMAIN)
			)
		);

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$select = new Basement_Form_Input_Select(array(
			'name' => $select_params['meta_name'],
			'id' => $select_params['meta_name'],
			'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
			'values' => $select_params['values']
		));
		$container->appendChild($dom->importNode( $select->create(), true  ));

		return $dom->saveHTML($container);
	}


	/**
	 * Easing settings
	 */
	protected function easing_inputs() {
		global $post;

		$select_params = array(
			'meta_name' => $this->meta . 'tiles_easing',
			'values' => array(
				'swing' => __('Swing', BASEMENT_GALLERY_TEXTDOMAIN),
				'linear' => __('Linear', BASEMENT_GALLERY_TEXTDOMAIN),
				'quadratic' => __('Quadratic', BASEMENT_GALLERY_TEXTDOMAIN),
				'cubic' => __('Cubic', BASEMENT_GALLERY_TEXTDOMAIN),
				'elastic' => __('Elastic', BASEMENT_GALLERY_TEXTDOMAIN)
			)
		);

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );

		$select = new Basement_Form_Input_Select(array(
			'name' => $select_params['meta_name'],
			'id' => $select_params['meta_name'],
			'current_value' => get_post_meta( $post->ID, $select_params['meta_name'], true ),
			'values' => $select_params['values']
		));
		$container->appendChild($dom->importNode( $select->create(), true  ));

		return $dom->saveHTML($container);
	}


	/**
	 * Click type
	 */
	protected function click_type() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'click_type',
			'values'        => array(
				'popup' => __( 'Popup', BASEMENT_GALLERY_TEXTDOMAIN ),
				'link'  => __( 'Link', BASEMENT_GALLERY_TEXTDOMAIN ),
				'none'  => __( 'No', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'popup'
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
				'none' => __( 'None', BASEMENT_GALLERY_TEXTDOMAIN ),
				'graysale'  => __( 'Grayscale', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'none'
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
	 * Tile filter behavior
	 */
	protected function tile_filter_behavior() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'filter_behavior',
			'values'        => array(
				'disable' => __( 'Disable', BASEMENT_GALLERY_TEXTDOMAIN ),
				'add'  => __( 'Add filter', BASEMENT_GALLERY_TEXTDOMAIN ),
				'remove'  => __( 'Remove filter', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'disable'
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
	 * Top Bar Style
	 */
	protected function top_bar_style() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name' => $this->meta . 'top_bar_style',
			'values'    => array(
				'light' => __( 'Light', BASEMENT_GALLERY_TEXTDOMAIN ),
				'dark' => __( 'Dark', BASEMENT_GALLERY_TEXTDOMAIN ),
			)
		);

		$select = new Basement_Form_Input_Select( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => get_post_meta( $post->ID, $params['meta_name'], true ) ? get_post_meta( $post->ID, $params['meta_name'], true ) : 'light',
			'values'        => $params['values']
		) );

		$container = $dom->appendChild( $dom->importNode( $select->create(), true ) );

		return $dom->saveHTML( $container );
	}



	/**
	 * Top Bar Size
	 */
	protected function top_bar_size() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'top_bar_size',
			'values'        => array(
				'boxed'     => __( 'Boxed', BASEMENT_GALLERY_TEXTDOMAIN ),
				'fullwidth' => __( 'Full Width', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'boxed'
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
	 * Top bar padding bottom
	 */
	protected function top_bar_padding_bottom() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$input     = new Basement_Form_Input( array(
			'type'  => 'number',
			'name'  => $this->meta . 'top_bar_padding_bottom',
			'value' => get_post_meta( $post->ID, $this->meta . 'top_bar_padding_bottom', true )
		) );
		$container = $dom->appendChild( $dom->importNode( $input->create(), true ) );

		return $dom->saveHTML( $container );
	}



	/**
	 * Title
	 */
	protected function title() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );


		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'id', 'js-title-choose' );

		$params = array(
			'meta_name'     => $this->meta . 'title_show',
			'values'        => array(
				'no'  => __( 'No', BASEMENT_GALLERY_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'no'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values']
		) );

		$container->appendChild( $dom->importNode( $radio->create(), true ) );


		$input = new Basement_Form_Input( array(
			'name'  => $this->meta . 'title',
			'value' => get_post_meta( $post->ID, $this->meta . 'title', true ),
			'class' => 'basement-full-width'
		) );


		$container_input = $container->appendChild( $dom->createElement( 'div' ) );
		$container_input->setAttribute( 'id', 'title_show_yes' );
		$container_input->appendChild( $dom->importNode( $input->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Title position
	 */
	protected function title_position() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'title_position',
			'values'        => array(
				'left'   => __( 'Left', BASEMENT_GALLERY_TEXTDOMAIN ),
				'center' => __( 'Center', BASEMENT_GALLERY_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'left'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values'],
			'class'         => 'basement_position-compare'
		) );

		$container = $dom->appendChild( $dom->importNode( $radio->create(), true ) );

		return $dom->saveHTML( $container );
	}


	/**
	 * Pills
	 */
	protected function pills() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'pills',
			'values'        => array(
				'hide' => __( 'Hide', BASEMENT_GALLERY_TEXTDOMAIN ),
				'show' => __( 'Show', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'hide'
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
	 * Pills position
	 */
	protected function pills_position() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'pills_position',
			'values'        => array(
				'left'   => __( 'Left', BASEMENT_GALLERY_TEXTDOMAIN ),
				'center' => __( 'Center', BASEMENT_GALLERY_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_GALLERY_TEXTDOMAIN )
			),
			'current_value' => 'right'
		);
		$value  = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name'          => $params['meta_name'],
			'id'            => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values'        => $params['values'],
			'class'         => 'basement_position-compare'
		) );

		$container = $dom->appendChild( $dom->importNode( $radio->create(), true ) );

		return $dom->saveHTML( $container );
	}

}

Basement_Grid_Settings::init();