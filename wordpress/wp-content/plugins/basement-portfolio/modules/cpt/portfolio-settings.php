<?php
defined( 'ABSPATH' ) or die();


class Basement_Portfolio_Grid_Settings {

	private static $instance = null;

	private $meta = '_basement_meta_portfolio_grid_';

	protected $portfolio = 'portfolio';

	public function __construct() {

		add_action( 'add_meta_boxes', array( &$this, 'generate_grid_param_meta_box' ) );
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Portfolio_Grid_Settings();
		}

		return self::$instance;
	}


	/**
	 * Register Meta Box
	 */
	public function generate_grid_param_meta_box() {
		add_meta_box(
			'portfolio_grid_parameters_meta_box',
			__( 'Parameters', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			array( &$this, 'render_grid_param_meta_box' ),
			$this->portfolio,
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


			if ( empty( $meta['title'] ) || $meta['title_show'] === 'no' ) {
				unset( $meta['title'] );
				unset( $meta['title_show'] );
				unset( $meta['title_position'] );
			} else {
				unset( $meta['title_show'] );
			}


			if ( $meta['pills'] === 'hide' ) {
				unset( $meta['pills'] );
				unset( $meta['pills_position'] );
			}


			if ( $meta['sorting'] === 'hide' ) {
				unset( $meta['sorting'] );
				unset( $meta['sorting_position'] );
			}

			if ( $meta['tiles_type'] === 'classic' && $meta['margins'] === 'no' ) {
				$meta['margins'] = 'yes';
			}


			if ( empty( $meta['load_more_size'] ) || $meta['load_more'] === 'no' ) {
				unset( $meta['load_more_size'] );
				unset( $meta['load_more'] );
			} else {
				unset( $meta['load_more'] );
			}


			if ( $meta['grid_type'] === 'masonry' || empty( $meta['tiles_height'] ) ) {
				unset( $meta['tiles_height'] );
			}


		}

		return apply_filters( 'basement_portfolio_grid_filtrate_params', $meta, $id );
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
					$params[ substr( $key, 30 ) ] = wp_strip_all_tags( array_shift( $value ) );
				}
			}
		}

		return apply_filters( 'basement_portfolio_grid_generate_params', $params, $id );
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


		$view = new Basement_Portfolio_Plugin();
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
				'topbar'    => array(
					'title'  => __( 'Portfolio top bar', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'params' => array(
						array(
							'type'        => 'dom',
							'title'       => __( 'Filter', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the filter style for the tile.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->tile_filter()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Filter behavior', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the filter behavior when hovering.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->tile_filter_behavior()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Top bar style', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the style of the top bar.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->top_bar_style()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Top bar size', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the top bar size (width).', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->top_bar_size()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Margin bottom for top bar', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets bottom margin (in "px") for the top bar.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->top_bar_padding_bottom()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Grid title', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the title name.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->title()
						),

						array(
							'type'        => 'dom',
							'title'       => __( 'Grid title position', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the position of the title.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->title_position()
						),

						array(
							'type'        => 'dom',
							'title'       => __( 'Categories pills', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Show category or not.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->pills()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Categories pills position', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the position of the categories.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->pills_position()
						),


						array(
							'type'        => 'dom',
							'title'       => __( 'Sorting', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Show sorting ddl or not.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->sorting()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Sorting ddl position', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the position of the ddl sorting.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->sorting_position()
						)

					)
				),
				'content'   => array(
					'title'  => __( 'Grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'params' => array(
						array(
							'type'        => 'dom',
							'title'       => __( 'Number of columns', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Select how many columns should be displayed.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->number_cols()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Margins', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'The margins between the tiles. <b>\'No\' option doesn\'t work with tiles type classic</b>.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->margins()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Grid size', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the size(width) of the grid.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->grid_size()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Grid type', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the grid type.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->grid_type()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Tiles type', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the tiles type.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->tiles_type()
						),
						'header_position' => array(
							array(
								'type'        => 'dom',
								'title'       => __( 'Tiles header position', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
								'description' => __( 'Sets the alignment of the title and category in tile.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
								'input'       => $this->tiles_title_position()
							),
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Tiles height', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the tiles height. Use integer value w/o "px". <b>Doesn\'t work with Masonry</b>.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->tiles_height()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Click type', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Sets the tile behavior when clicking on it.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->click_type()
						)
					)
				),
				'bottombar' => array(
					'title'  => __( 'Portfolio bottom bar', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
					'params' => array(
						array(
							'type'        => 'dom',
							'title'       => __( 'Load more', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Loading projects by pressing a "Load More" button.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->load_more()
						),
						array(
							'type'        => 'dom',
							'title'       => __( 'Portfolio info', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'description' => __( 'Shows the number of projects in the portfolio.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
							'input'       => $this->portfolio_info()
						)
					)
				)
			)
		);

		return $config;
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
				'none' => __( 'None', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'graysale'  => __( 'Grayscale', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'disable' => __( 'Disable', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'add'  => __( 'Add filter', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'remove'  => __( 'Remove filter', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'light' => __( 'Light', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'dark' => __( 'Dark', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
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
				'boxed'     => __( 'Boxed', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'fullwidth' => __( 'Full Width', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'no'  => __( 'No', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'left'   => __( 'Left', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'center' => __( 'Center', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'hide' => __( 'Hide', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'show' => __( 'Show', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'left'   => __( 'Left', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'center' => __( 'Center', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'center'
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
	 * Sorting
	 */
	protected function sorting() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'sorting',
			'values'        => array(
				'hide' => __( 'Hide', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'show' => __( 'Show', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
	 * Sorting position
	 */
	protected function sorting_position() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'sorting_position',
			'values'        => array(
				'left'   => __( 'Left', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'center' => __( 'Center', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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


	/**
	 * Number columns
	 */
	protected function number_cols() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name' => $this->meta . 'cols',
			'values'    => array(
				1 => __( '1', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				2 => __( '2', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				3 => __( '3', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				4 => __( '4', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				5 => __( '5', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				6 => __( '6', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'no'  => __( 'No', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				's5'   => __( '5', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's10'   => __( '10', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's15'   => __( '15', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's20'   => __( '20', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's25'   => __( '25', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's30'   => __( '30', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's35'   => __( '35', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's40'   => __( '40', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's45'   => __( '45', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's50'   => __( '50', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's55'   => __( '55', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's60'   => __( '60', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's65'   => __( '65', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's70'   => __( '70', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's75'   => __( '75', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's80'   => __( '80', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's85'   => __( '85', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's90'   => __( '90', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's95'   => __( '95', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				's100'   => __( '100', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 's15'
		);
		$value_type  = get_post_meta( $post->ID, $params_type['meta_name'], true );

		$radio_type = new Basement_Form_Input_Select( array(
			'label_text' => __('Choose the tiles indent',BASEMENT_PORTFOLIO_TEXTDOMAIN),
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
				'no'  => __( 'No', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'yes' => __( 'Yes', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
		$title = $container_input->appendChild( $dom->createElement( 'strong', __( 'Number of downloads tiles:', BASEMENT_PORTFOLIO_TEXTDOMAIN ) ) );
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
				'boxed'     => __( 'Boxed', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'fullwidth' => __( 'Full Width', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'grid'    => __( 'Grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'masonry' => __( 'Masonry', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'default'   => __( 'Default', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'mixed'   => __( 'Mixed', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				//'cellsByRow'   => __( 'CellsByRow', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				//'cellsByColumn'   => __( 'CellsByColumn', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'default'
		);
		$value_type  = get_post_meta( $post->ID, $params_type['meta_name'], true );

		$radio_type = new Basement_Form_Input_Radio_Group( array(
			'label_text' => __('Choose the layer type',BASEMENT_PORTFOLIO_TEXTDOMAIN),
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
				'hover'   => __( 'Hover', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'classic' => __( 'Classic', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'simple'  => __( 'Simple', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
				'left'   => __( 'Left', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'center' => __( 'Center', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'right'  => __( 'Right', BASEMENT_PORTFOLIO_TEXTDOMAIN )
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
	 * Click type
	 */
	protected function click_type() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'click_type',
			'values'        => array(
				'standard' => __( 'Standard', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'popup'    => __( 'Popup', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'link'     => __( 'Link', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'video'    => __( 'Video', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'none'     => __( 'No', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'standard'
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
	 * Portfolio info
	 */
	protected function portfolio_info() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name'     => $this->meta . 'info',
			'values'        => array(
				'yes' => __( 'Yes', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'no'  => __( 'No', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'yes'
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


}

Basement_Portfolio_Grid_Settings::init();