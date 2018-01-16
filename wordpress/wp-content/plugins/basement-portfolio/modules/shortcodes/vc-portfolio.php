<?php
defined( 'ABSPATH' ) or die();


class WPBakeryShortCode_basement_vc_portfolio extends WPBakeryShortCode {

	protected function content( $atts, $content = null ) {

		$id = $projects = '';

		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );

		$gallery = '';

		if(!empty($projects) && !empty($id)) {
			$tiles = $projects;

			$gallery_grid = $this->grid_exists(absint($id));
			$gallery_tiles = $this->tiles_exists(explode(',', $tiles));

			if ($gallery_grid && $gallery_tiles) {
				$gallery = $this->grid_builder($gallery_grid, $gallery_tiles);
			}
		} elseif (!empty($id) && empty($projects)) {

			$gallery_grid = $this->grid_exists(absint($id));


			if($gallery_grid) {

				$tiles = $this->find_snap_tiles($gallery_grid);

				$gallery = $this->grid_builder($gallery_grid, $tiles);
			}

		}

		return $gallery;
	}



	/**
	 * Get Grid CPT name
	 *
	 * @return string
	 */
	private function get_grid_cpt() {
		$cpt = new Basement_Portfolio_Cpt();
		return $cpt->portfolio_cpt_name();
	}


	/**
	 * Get Tile CPT name
	 *
	 * @return string
	 */
	private function get_tile_cpt() {
		$cpt = new Basement_Project_Cpt();
		return $cpt->project_cpt_name();
	}



	/**
	 * Check if Grid CPT exist
	 *
	 * @param $id
	 * @return bool
	 */
	private function grid_exists( $id ) {
		$grid_cpt = $this->get_grid_cpt();
		if(get_post_type(absint($id)) === $grid_cpt && get_post_meta(absint($id))) {
			return $id;
		} else {
			return false;
		}
	}


	/**
	 * Check if tile CPT exist
	 *
	 * @param $tiles
	 * @return array|bool
	 */
	private function tiles_exists( $tiles ) {
		$list_tiles = array();
		$tile_cpt = $this->get_tile_cpt();

		foreach ( $tiles as $tile ) {
			if ( get_post_type(absint($tile)) === $tile_cpt && get_post_status ( absint($tile) ) === 'publish') {
				$list_tiles[] = $tile;
			}
		}

		return $list_tiles ? $list_tiles : false;
	}


	/**
	 * Grid builder
	 *
	 * @param $id
	 * @param $tiles
	 * @return string
	 */
	private function grid_builder( $id, $tiles ) {
		$grid_settings = new Basement_Portfolio_Grid_Settings();
		$grid_params = $grid_settings->get_grid(absint($id));

		// Build gallery carousel by like usually tile gallery
		$grid = $this->grid_multirow_builder($id, $grid_params, $tiles);

		return $grid;
	}


	/**
	 * Show tiles only with thumbnails
	 *
	 * @param $tiles
	 * @return array|bool
	 */
	private function get_normal_tiles( $tiles ) {
		if(empty($tiles))
			return false;

		$normal_tiles = array();

		foreach ($tiles as $tile) {
			if ( has_post_thumbnail($tile) && ! post_password_required($tile) && ! is_attachment() ) {
				$normal_tiles[] = $tile;
			}
		}
		return $normal_tiles;
	}


	/**
	 * Build Multirow Gallery
	 *
	 * @param $id
	 * @param $params
	 * @param $tiles
	 * @return string
	 */
	private function grid_multirow_builder($id, $params, $tiles) {
		$normal_tiles = $tiles;

		if(empty($normal_tiles))
			return '';

		$grid_classes = $this->generate_grid_classes('basement-gallery-', $params);

		extract($params);

		$mix_ajax_class = uniqid('ajax-mix-');

		$class_grid = '';

		$params_grid_type = !empty($params['grid_type']) ? $params['grid_type'] : '';

		if($params['grid_type'] === 'grid') {
			$class_grid = 'basement-portfolio-mix-list';
		} elseif ($params['grid_type'] === 'masonry') {
			$class_grid = 'basement-portfolio-isotope-list clearfix '; #masonry-wrap

		}


		if($params_grid_type === 'grid') {
			$class_grid = 'basement-portfolio-mix-list';
		} elseif ($params_grid_type === 'masonry') {
			$class_grid = 'basement-portfolio-isotope-list clearfix';
			if(isset($params['layout_mode']) && !empty($params['layout_mode'])) {
				$layout_mode = $params['layout_mode'];
				if($layout_mode === 'default') {
					$layout_mode = 'masonry';
				}
				$class_grid = 'basement-portfolio-isotope-list '.$layout_mode.'-wrap';
			}
		}


		$grid_styles = array();
		if ( isset( $params['margin_value'] ) && ! empty( $params['margin_value'] ) && $params['margins'] === 'yes' ) {
			$margin_value = str_replace('s','',$params['margin_value']);

			if($margin_value < 45) {
				$grid_styles[] = "margin-top:-{$margin_value}px !important;";
				$grid_styles[] = "margin-bottom:-{$margin_value}px !important;";
			}
		}


		if(!empty($grid_styles)) {
			$grid_styles = 'style="'.implode($grid_styles).'"';
		} else {
			$grid_styles = '';
		}


		$top_bar_style = isset($top_bar_style) ? 'basement-gallery-' . $top_bar_style . '-style' : '';
		$top_bar_padding_bottom = isset($top_bar_padding_bottom) ? $top_bar_padding_bottom : '';
		$style_top_bar = array();
		if(is_numeric($top_bar_padding_bottom)) {
			if($top_bar_padding_bottom <= 17) {
				$top_bar_padding_bottom = 17;
			}
			$style_top_bar[] = "padding-bottom:{$top_bar_padding_bottom}px;";
		}


		$grid_load_type = $params['grid_type'];
		if($grid_load_type === 'mixed') {
			$grid_load_type = 'masonry';
		}



		#$fwidth_start = '';
		#$fwidth_end = '';

		#if($grid_size === 'fullwidth') {
		$fwidth_start = '';
		$fwidth_end = '';
		#}

		$title_position = !empty($title_position) ? $title_position : '';
		$title = !empty($title) ? $title : '';
		$pills_position = !empty($pills_position) ? $pills_position : '';
		$pills = !empty($pills) ? $pills : '';
		$sorting_position  = !empty($sorting_position) ? $sorting_position : '';
		$sorting = !empty($sorting) ? $sorting : '';
		$grid_type = !empty($grid_type) ? $grid_type : '';



		if(empty($sorting) && empty($pills) && empty($title)) {
			$style_top_bar[] = 'padding-top:0 !important;';
			$style_top_bar[] = 'padding-bottom:0 !important;';
		}

		$style_top_bar = 'style="'.implode('',$style_top_bar).'"';

		if(!empty($title) && $pills === 'show' && $sorting === 'show') {

			$portfolio_header = sprintf($fwidth_start.'<div class="row">%1$s%2$s%3$s</div>'.$fwidth_end,
				$this->generate_header_positions( array(
					'ltr' => 'left',
					'position' => array(
						$title_position => $this->generate_title($title_position, $title),
						$pills_position => $this->generate_pills($pills_position, $tiles, $pills),
						$sorting_position => $this->generate_ddl_sorting($sorting_position, $tiles, $grid_type)
					)
				) ),
				$this->generate_header_positions( array(
					'ltr' => 'center',
					'position' => array(
						$title_position => $this->generate_title($title_position, $title),
						$pills_position => $this->generate_pills($pills_position, $tiles, $pills),
						$sorting_position => $this->generate_ddl_sorting($sorting_position, $tiles, $grid_type)
					)
				) ),
				$this->generate_header_positions( array(
					'ltr' => 'right',
					'position' => array(
						$title_position => $this->generate_title($title_position, $title),
						$pills_position => $this->generate_pills($pills_position, $tiles, $pills),
						$sorting_position => $this->generate_ddl_sorting($sorting_position, $tiles, $grid_type)
					)
				) )
			);

		} else {
			$portfolio_header = '';
			$part_sortable = array(
				'left' => '',
				'center' => '',
				'right' => ''
			);
			$part_positions = array(
				$title_position => $this->generate_title($title_position, $title),
				$pills_position => $this->generate_pills($pills_position, $tiles, $pills),
				$sorting_position => $this->generate_ddl_sorting($sorting_position, $tiles, $grid_type)
			);

			foreach ($part_positions as $key => $value) {
				switch ($key) {
					case 'left' :
						$part_sortable[$key] = $value;
						break;
					case 'center' :
						$part_sortable[$key] = $value;
						break;
					case 'right' :
						$part_sortable[$key] = $value;
						break;
				}
			}

			$pills_html = '';

			if($pills !== 'show') {
				$pills_html = $this->generate_pills($pills_position, $tiles, $pills);
			}


			if( empty($part_sortable['left']) && empty($part_sortable['center']) && empty($part_sortable['right']) ) {
				$portfolio_header = $pills_html;
			} elseif ( (empty($part_sortable['left']) && empty($part_sortable['center'])) || (empty($part_sortable['center']) && empty($part_sortable['right'])) || (empty($part_sortable['left']) && empty($part_sortable['right'])) ) {
				$cell = '';
				foreach ($part_sortable as $keys => $item) {
					if($item) {
						$cell = $item;
					}
				}
				$portfolio_header = sprintf($fwidth_start.'<div class="row">%1$s%2$s</div>'.$fwidth_end,
					'<div class="col-xs-12" style="float: none !important;">'.$cell.'</div>',
					$pills_html
				);
			} else {
				if(empty($part_sortable['center'])) {
					$portfolio_header = sprintf($fwidth_start.'<div class="row">%1$s%2$s%3$s%4$s</div>'.$fwidth_end,
						'<div class="col-md-6">'.$part_sortable['left'].'</div>',
						'',
						'<div class="col-md-6">'.$part_sortable['right'].'</div>',
						$pills_html
					);
				} else {
					$portfolio_header = sprintf($fwidth_start.'<div class="row">%1$s%2$s%3$s%4$s</div>'.$fwidth_end,
						'<div class="col-md-4">'.$part_sortable['left'].'</div>',
						'<div class="col-md-4">'.$part_sortable['center'].'</div>',
						'<div class="col-md-4">'.$part_sortable['right'].'</div>',
						$pills_html
					);
				}
			}

		}

		if ( ! empty( $params['tiles_height'] ) ) {
			$grid_classes  .= ' basement-portfolio-tile-height ';
		}


		$top_bar_size = isset($top_bar_size) ? $top_bar_size : '';
		$grid_size = isset($grid_size) ? $grid_size : '';


		$boxed = '';
		$fullwidth = '';
		if($grid_size !== $top_bar_size) {
			switch ($top_bar_size) {
				case 'fullwidth' :
					$fullwidth = 'data-grid-size="fullwidth"';
					break;
				case 'boxed' :
					$boxed = 'container';
					break;
			}
		}





		$grid = sprintf('<div  class="basement-gallery-wrap-block basement-portfolio-wrapper %1$s" %4$s><div class="basement-gallery-top-bar %9$s '.$top_bar_style.'" '.$style_top_bar.' %8$s >%2$s%3$s</div><div class="full-width-basement"></div><div '.$grid_styles.' class="' . $class_grid . ' magnific-wrap %7$s">%5$s</div>%10$s%6$s</div><div class="full-width-basement"></div>',
			!empty($grid_classes) ? esc_attr( $grid_classes ) : '',
			$portfolio_header,
			'',
			$grid_size === 'fullwidth' ? 'data-grid-size="fullwidth"' : '',
			(!empty($load_more_size) && absint($load_more_size) < count($tiles)) ? $this->generate_loadmore_tiles($id, $params, $tiles, $load_more_size) : $this->generate_normal_tiles($id, $params, $tiles),
			(!empty($load_more_size) && absint($load_more_size) < count($tiles)) ? '<div class="basement-gallery-load-more basement-portfolio-load"><a href=".'.$mix_ajax_class.'" data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i>' . __('Loading...',BASEMENT_PORTFOLIO_TEXTDOMAIN) . '" class="btn btn-primary btn-basement-portfolio-load-more" data-grid="' . esc_attr( $id ) . '" data-need="' . esc_attr( $load_more_size ) . '" data-all="' . esc_attr( count($tiles) ) . '" data-load="' . esc_attr( $load_more_size ) . '" data-tiles="' . htmlspecialchars(json_encode($tiles)) . '" data-type="' . esc_attr( $params['grid_type'] ) . '" title=""><span class="icon-layers"></span>' . __('Load More',BASEMENT_PORTFOLIO_TEXTDOMAIN) . '</a></div>' : '',
			(!empty($load_more_size) && absint($load_more_size) < count($tiles)) ? $mix_ajax_class : '',
			$fullwidth,
			$boxed,
			$info === 'yes' ? $this->total_projects($tiles) : ''
		);

		return $grid;
	}


	/**
	 * Generate positions for portfolio header
	 *
	 * @param $params
	 * @return string
	 */
	private function generate_header_positions( $params ) {
		if ( empty( $params ) ) {
			return '';
		}

		extract( $params );

		$ltr = isset($ltr) ? $ltr : '';
		$final_position = isset($position[ $ltr ]) ? $position[ $ltr ] : '';

		if ( !empty($final_position) ) {
			/*if (strpos($final_position, 'basement-portfolio-categories') !== false) {
				$cell = '<div class="col-md-6">' . $final_position . '</div>';
			} elseif (strpos($final_position, 'basement-portfolio-title') !== false) {
				$cell = '<div class="col-md-3">' . $final_position . '</div>';
			} else {*/
			$cell = '<div class="col-md-4">' . $final_position . '</div>';
			/*}*/

		} else {
			$cell = '<div class="col-md-4"></div>';
		}

		return $cell;
	}


	/**
	 * Generate title for portfolio header
	 *
	 * @param $title_position
	 * @param $title
	 * @return string
	 */
	private function generate_title( $title_position, $title ) {
		return sprintf('<div class="basement-portfolio-title %1$s"><h2>%2$s</h2></div>',
			!empty($title_position) ? esc_attr( 'basement-portfolio-title-position-' . $title_position ) : '',
			esc_html( $title )
		);
	}


	/**
	 * Generate ddl sorting
	 *
	 * @param $sorting_position
	 * @param $tiles
	 * @param $grid
	 * @return string
	 */
	private function generate_ddl_sorting($sorting_position, $tiles, $grid) {

		$option = '';

		$select_args = array(
			'grid' => array(
				'default'  => __('Default sorting', BASEMENT_PORTFOLIO_TEXTDOMAIN),
				'name'     => __('Sort by name', BASEMENT_PORTFOLIO_TEXTDOMAIN),
				'published-date:desc name:desc'     => __('Sort by date', BASEMENT_PORTFOLIO_TEXTDOMAIN),
				'category' => __('Sort by category', BASEMENT_PORTFOLIO_TEXTDOMAIN),
				'random'   => __('Sort by random', BASEMENT_PORTFOLIO_TEXTDOMAIN)
			),
			'masonry' => array(
				'original-order' => __('Default sorting', BASEMENT_PORTFOLIO_TEXTDOMAIN),
				'name'           => __('Sort by name', BASEMENT_PORTFOLIO_TEXTDOMAIN),
				'date'           => __('Sort by date', BASEMENT_PORTFOLIO_TEXTDOMAIN),
				'category'       => __('Sort by category', BASEMENT_PORTFOLIO_TEXTDOMAIN),
				'random'         => __('Sort by random', BASEMENT_PORTFOLIO_TEXTDOMAIN)
			)
		);


		foreach ($select_args[$grid] as $key => $value) {
			$option .= '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
		}
		$select = sprintf('<select class="basement-portfolio-cat-select">%1$s</select>',
			$option
		);

		return sprintf('<div class="clearfix basement-portfolio-sorting-wrap"><div class="basement-portfolio-sorting form-solid sorting-%1$s %3$s">%2$s</div></div>',
			sanitize_html_class($grid),
			$select,
			!empty($sorting_position) ? 'basement-portfolio-sorting-' . $sorting_position : ''
		);
	}


	/**
	 * Total projects
	 *
	 * @param $tiles
	 * @return string
	 */
	private function total_projects($tiles) {
		$filtrate_categories = array();
		$categories = array();

		foreach ($tiles as $tile) {
			$tile_settings = new Basement_Project_Settings();
			$tile_params = $tile_settings->get_project(absint($tile));

			if(!empty($tile_params['terms'])) {

				foreach ($tile_params['terms'] as $tile_term) {
					if(!in_array($tile_term->term_id, $filtrate_categories)) {
						$filtrate_categories[] = $tile_term->term_id;
						$categories[] = $tile_term->name;
					}
				}
			}
		}


		$count_tiles = count( $tiles );
		$count_items = $this->count_items( number_format_i18n( count( $tiles ) ), array(
			__( 'project', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			__( 'project', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			__( 'projects', BASEMENT_PORTFOLIO_TEXTDOMAIN )
		) );

		$count_items_two = $this->count_items( number_format_i18n( count( $categories ) ), array(
			__( 'category', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			__( 'category', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			__( 'categories', BASEMENT_PORTFOLIO_TEXTDOMAIN )
		) );

		$count_cats = count($categories);

		$info = sprintf('<div class="basement-gallery-info text-center">%1$s%2$s</div>',
			$count_tiles . ' ' . $count_items,
			empty($count_cats) ? '' : __('<ins>&mdash;</ins>',BASEMENT_PORTFOLIO_TEXTDOMAIN) . $count_cats.' '.$count_items_two
		);

		return $info;
	}


	/**
	 * Count items
	 *
	 * @param $n
	 * @param $words
	 * @return mixed
	 */
	private function count_items($n, $words) {
		$plural = ($n % 10 == 1 && $n % 100 != 11) ? 0 : (($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20)) ? 1 : 2);
		return $words[$plural];
	}


	/**
	 * Generate dynamic Tile For Multirow Grid
	 *
	 * @param $grid_id
	 * @param $grid_params
	 * @param $tiles_id
	 * @param $load_more_size
	 * @return string
	 */
	private function generate_loadmore_tiles($grid_id, $grid_params, $tiles_id, $load_more_size) {
		$tiles = '';
		$flag_show_tiles = 0;
		foreach ($tiles_id as $tile) {
			if($flag_show_tiles !== absint($load_more_size)) {
				$flag_show_tiles++;

				$tiles .= $this->generate_item_tile($grid_params, $tile);
			}
		}

		return $tiles;
	}



	/**
	 * Generate static Tile For Multirow Grid
	 *
	 * @param $grid_id
	 * @param $grid_params
	 * @param $tiles_id
	 * @return string
	 */
	private function generate_normal_tiles($grid_id, $grid_params, $tiles_id) {
		$tiles = '';

		foreach ($tiles_id as $tile) {
			$tiles .= $this->generate_item_tile($grid_params, absint($tile));
		}

		return $tiles;
	}


	/**
	 * Generate tiles item
	 *
	 * @param $grid_params
	 * @param $tile
	 * @return string
	 */
	public function generate_item_tile($grid_params, $tile) {
		$col_lg = '';
		$col_md = '';
		$col_sm = '';
		$col_xs = '';
		$items = '';
		$generate_items = '';
		switch ($grid_params['cols']) {
			case '1' :
				$col_xs = 'col-xs-12';
				break;
			case '2' :
				$col_xs = 'col-xs-6';
				break;
			case '3' :
				$col_xs = 'col-xs-6';
				$col_sm = 'col-sm-4';
				break;
			case '4' :
				$col_xs = 'col-xs-6';
				$col_sm = 'col-sm-4';
				$col_md = 'col-md-3';
				break;
			case '5' :
				$col_xs = 'col-xs-6';
				$col_sm = 'col-sm-4';
				$col_md = 'col-md-5';
				break;
			case '6' :
				$col_xs = 'col-xs-6';
				$col_sm = 'col-sm-4';
				$col_md = 'col-md-2';
				break;
		}

		$tile_settings = new Basement_Project_Settings();
		$tile_params = $tile_settings->get_project(absint($tile));
		$tile_taxes = array();
		$tile_classes = array();
		$tile_styles = array();


		if(isset($grid_params['grid_type']) && isset($tile_params['params']) && $grid_params['grid_type'] === 'masonry' && $grid_params['layout_mode'] === 'mixed') {
			$sizes = array('lg_width','md_width','sm_width','xs_width');
			foreach ($sizes as $size) {
				if ( isset( $tile_params['params'][ $size ] ) && !empty($tile_params['params'][ $size ]) ) {
					$size_new = $tile_params['params'][ $size ];
					switch ($size) {
						case 'lg_width':
							$col_lg = $size_new;
							break;
						case 'md_width':
							$col_md = $size_new;
							break;
						case 'sm_width':
							$col_sm = $size_new;
							break;
						case 'xs_width':
							$col_xs = $size_new;
							break;
					}
				}
			}
		}


		$tile_filter = isset($tile_params['params']['filter']) ? $tile_params['params']['filter'] : '';
		$grid_filter = isset($grid_params['filter']) ? $grid_params['filter'] : '';


		$tile_filter_behavior = isset($tile_params['params']['filter_behavior']) ? $tile_params['params']['filter_behavior'] : '';
		$grid_filter_behavior = isset($grid_params['filter_behavior']) ? $grid_params['filter_behavior'] : '';


		if(!empty($tile_filter) && $tile_filter !== 'default') {
			$tile_classes[] = 'bs-filter-' . $tile_filter;
		} else {
			$tile_classes[] = 'bs-filter-' . $grid_filter;
		}


		if ( ! empty( $tile_filter_behavior ) && $tile_filter_behavior !== 'default' ) {
			$tile_classes[] = ' bs-is-' . $tile_filter_behavior;
		} else {
			$tile_classes[] = ' bs-is-' . $grid_filter_behavior;
		}



		if (!empty($tile_params['terms'])) {
			foreach ( $tile_params['terms'] as $tile_term ) {
				$tile_taxes[] = sanitize_html_class( $tile_term->slug );
			}

			$tile_post = get_post( $tile );

			if ( $grid_params['margins'] === 'yes' ) {
				$tile_classes[] = 'basement-thumb-margins';
			}

			if ( isset( $grid_params['margin_value'] ) && ! empty( $grid_params['margin_value'] ) && $grid_params['margins'] === 'yes' ) {
				$margin_value = str_replace('s','',$grid_params['margin_value']);

				$tile_styles[] = "padding-left:{$margin_value}px !important;";
				$tile_styles[] = "padding-right:{$margin_value}px !important;";
				$tile_styles[] = "padding-top:{$margin_value}px !important;";
				$tile_styles[] = "padding-bottom:{$margin_value}px !important;";
			}


			if(!empty($tile_styles)) {
				$tile_styles = 'style="'.implode($tile_styles).'"';
			} else {
				$tile_styles = '';
			}

			$click_type = ! empty( $tile_params['params']['click_type'] ) ? 'basement-gallery-click-' . $tile_params['params']['click_type'] : '';

			if ( $grid_params['tiles_type'] === 'hover' ) {
				$tile_content   = $this->generate_hover_multirow_tiles( $grid_params, $tile_params, $tile_post );
				$generate_items = sprintf( '<div class="%1$s mix %2$s %3$s" %4$s %5$s %6$s %7$s ><figure class="figure ' . esc_attr( $click_type ) . '">' . $tile_content . '</figure></div>',
					esc_attr( $col_xs . ' ' . $col_sm . ' ' . $col_md . ' ' . $col_lg ),
					esc_attr( implode( ' ', $tile_taxes ) ),
					! empty( $tile_classes ) ? esc_attr( implode( ' ', $tile_classes ) ) : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-name="' . esc_attr( get_the_title( $tile_post ) ) . '"' : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-published-date="' . get_the_time( 'Y-m-d', $tile_post ) . '"' : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-category="' . esc_attr( implode( ', ', $tile_taxes ) ) . '"' : '',
					$tile_styles
				);
			} elseif ( $grid_params['tiles_type'] === 'classic' ) {
				$tile_content = $this->generate_classic_multirow_tiles( $grid_params, $tile_params, $tile_post );


				$tile_thumbnail = ! empty( $tile_content['thumbnail'] ) ? $tile_content['thumbnail'] : '';
				$tile_header    = ! empty( $tile_content['header'] ) ? $tile_content['header'] : '';

				$generate_items = sprintf( '<div class="%1$s mix %2$s %3$s" %4$s %5$s %6$s %9$s ><figure class="figure ' . esc_attr( $click_type ) . '">%7$s</figure>%8$s</div>',
					esc_attr( $col_xs . ' ' . $col_sm . ' ' . $col_md . ' ' . $col_lg ),
					esc_attr( implode( ' ', $tile_taxes ) ),
					! empty( $tile_classes ) ? esc_attr( implode( ' ', $tile_classes ) ) : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-name="' . esc_attr( get_the_title( $tile_post ) ) . '"' : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-published-date="' . get_the_time( 'Y-m-d', $tile_post ) . '"' : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-category="' . esc_attr( implode( ', ', $tile_taxes ) ) . '"' : '',
					$tile_thumbnail,
					$tile_header,
					$tile_styles
				);
			} elseif ( $grid_params['tiles_type'] === 'simple' ) {
				$tile_content   = $this->generate_simple_multirow_tiles( $grid_params, $tile_params, $tile_post );
				$generate_items = sprintf( '<div class="%1$s mix %2$s %3$s" %4$s %5$s %6$s %7$s ><figure class="figure ' . esc_attr( $click_type ) . '">' . $tile_content . '</figure></div>',
					esc_attr( $col_xs . ' ' . $col_sm . ' ' . $col_md . ' ' . $col_lg ),
					esc_attr( implode( ' ', $tile_taxes ) ),
					! empty( $tile_classes ) ? esc_attr( implode( ' ', $tile_classes ) ) : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-name="' . esc_attr( get_the_title( $tile_post ) ) . '"' : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-published-date="' . get_the_time( 'Y-m-d', $tile_post ) . '"' : '',
					! empty( $grid_params['sorting'] ) && $grid_params['sorting'] === 'show' ? 'data-category="' . esc_attr( implode( ', ', $tile_taxes ) ) . '"' : '',
					$tile_styles
				);
			}

			$items .= $generate_items;
		}
		return $items;
	}

	/**
	 * Generate simple multirow tiles
	 *
	 * @param $grid_params
	 * @param $tile_params
	 * @param $tile_post
	 * @return string
	 */
	private function generate_simple_multirow_tiles($grid_params, $tile_params, $tile_post) {
		$thumbnail_classes = array();
		$thumbnail_styles = array();
		$thumbnail_taxes = array();
		$post_name = get_the_title($tile_post);#$tile_params['params']['title'];

		$thumbnail_full_image = '#';


		$thumbnail_classes[] = 'basement-gallery-thumb';


		if(!empty($tile_params['thumbnail']['url'])) {
			$thumbnail_styles[] = 'background-image: url(' . $tile_params['thumbnail']['url'] . ');';
		}

		if(!empty($tile_params['terms'])) {
			foreach ($tile_params['terms'] as $tile_term) {
				$thumbnail_taxes[] =  $tile_term->name;
			}
		}

		if(!empty($tile_params['params']['image'])) {
			$thumbnail_full_image = wp_get_attachment_url($tile_params['params']['image']);
		}

		$click_type_start = '<a href="#" class="mask-simple" title="' . esc_attr($post_name) . '">';
		$click_type_end = '</a>';

		$video_icon = '';

		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask-simple" data-title="%1$s %2$s " title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr($post_name) : ''
				);
			} elseif ($grid_params['click_type'] === 'standard') {
				$click_type_start = '<a href="' . get_permalink($tile_post) . '" class="mask-simple" title="' . esc_attr($post_name) . '">';
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url($tile_params['params']['normal_link']) . '" class="mask-simple"  title="' . esc_attr($post_name) . '">';
			} elseif ($grid_params['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
				$click_type_start = sprintf('<a href="' . $tile_params['params']['video_link'] . '" class="magnific mfp-iframe mask-simple" data-title="%1$s %2$s" title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr($post_name) : ''
				);
			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '<div class="mask-simple">';
				$click_type_end = '</div>';
			}
		} elseif ($tile_params['params']['click_type'] === 'standard') {
			$click_type_start = '<a href="' . get_permalink($tile_post) . '" class="mask-simple" title="' . esc_attr($post_name) . '">';
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask-simple" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr($post_name) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="'.esc_url( $tile_params['params']['normal_link'] ).'" class="mask-simple"  title="' . esc_attr($post_name) . '">';
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . $tile_params['params']['video_link'] . '" class="magnific mfp-iframe mask-simple" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr($post_name) : ''
			);

			$video_icon = '<div class="icon"><i class="icon-video"></i></div>';
		}

		if($grid_params['grid_type'] !== 'masonry' && $grid_params['grid_type'] !== 'mixed') {
			if(!empty($grid_params['tiles_height'])) {
				$thumbnail_classes[] = 'basement-gallery-thumb-fixed';
				$thumbnail_styles[] = 'height: ' . $grid_params['tiles_height'] . 'px;';

				$thumbnail = sprintf('<div %1$s %2$s ></div>',
					$thumbnail_classes ? 'class="'. esc_attr( implode(' ',$thumbnail_classes) ) .'"' : '',
					$thumbnail_styles ? 'style="' . implode(' ',$thumbnail_styles) . '"' : ''
				);
			} else {
				$thumbnail_classes[] = 'basement-gallery-thumb-auto';
				$thumbnail = sprintf('<div %1$s %2$s ></div>',
					$thumbnail_classes ? 'class="'. esc_attr( implode(' ',$thumbnail_classes) ) .'"' : '',
					$thumbnail_styles ? 'style="' . implode(' ',$thumbnail_styles) . '"' : ''
				);
			}
		} else {
			$thumbnail_classes[] = 'basement-gallery-thumb-auto';
			$thumbnail = sprintf('<img src="%1$s" %2$s alt="">',
				!empty($tile_params['thumbnail']['url']) ? $tile_params['thumbnail']['url'] : '',
				'style="max-width: 100%;"'
			);
		}

		$thumbnail_header = $click_type_start . $click_type_end;

		return $thumbnail . $video_icon . $thumbnail_header;
	}


	/**
	 * Generate hover multirow tiles
	 *
	 * @param $grid_params
	 * @param $tile_params
	 * @param $tile_post
	 * @return string
	 */
	private function generate_hover_multirow_tiles($grid_params, $tile_params, $tile_post) {
		$thumbnail_classes = array();
		$thumbnail_styles = array();
		$thumbnail_taxes = array();
		$post_name = get_the_title($tile_post);#$tile_params['params']['title'];

		$thumbnail_full_image = '#';


		$thumbnail_classes[] = 'basement-gallery-thumb';


		if(!empty($tile_params['thumbnail']['url'])) {
			$thumbnail_styles[] = 'background-image: url(' . $tile_params['thumbnail']['url'] . ');';
		}

		if(!empty($tile_params['terms'])) {
			foreach ($tile_params['terms'] as $tile_term) {
				$thumbnail_taxes[] =  $tile_term->name;
			}
		}

		if(!empty($tile_params['params']['image'])) {
			$thumbnail_full_image = wp_get_attachment_url($tile_params['params']['image']);
		}

		$click_type_start = '<a href="#" class="mask mask-info mask-dark" title="' . esc_attr($post_name) . '">';
		$click_type_end = '</a>';

		$video_icon = '';
		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask mask-info mask-dark" data-title="%1$s %2$s" title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr($post_name) : ''
				);
			} elseif ($grid_params['click_type'] === 'standard') {
				$click_type_start = '<a href="'.get_permalink($tile_post).'" class="mask mask-info mask-dark" title="' . esc_attr($post_name) . '">';
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url($tile_params['params']['normal_link']) . '" target="_blank" class="mask mask-info mask-dark"  title="' . esc_attr($post_name) . '">';
			} elseif ($grid_params['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
				$click_type_start = sprintf('<a href="' . esc_url($tile_params['params']['video_link']) . '" class="magnific mfp-iframe mask mask-info mask-dark" data-title="%1$s %2$s" title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr($post_name) : ''
				);

			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '<div class="mask mask-info mask-dark">';
				$click_type_end = '</div>';
			}
		} elseif ($tile_params['params']['click_type'] === 'standard') {
			$click_type_start = '<a href="'.get_permalink($tile_post).'" class="mask mask-info mask-dark" title="' . esc_attr($post_name) . '">';
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask mask-info mask-dark" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr($post_name) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="' . esc_url($tile_params['params']['normal_link']) . '" target="_blank" class="mask mask-info mask-dark"  title="' . esc_attr($post_name) . '">';
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . esc_url($tile_params['params']['video_link']) . '" class="magnific mfp-iframe mask mask-info mask-dark" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr($post_name) : ''
			);
			$video_icon = '<div class="icon"><i class="icon-video"></i></div>';
		}

		if($grid_params['grid_type'] !== 'masonry' && $grid_params['grid_type'] !== 'mixed') {
			if ( ! empty( $grid_params['tiles_height'] ) ) {
				$thumbnail_classes[] = 'basement-gallery-thumb-fixed';
				$thumbnail_styles[]  = 'height:' . $grid_params['tiles_height'] . 'px;';

				$thumbnail = sprintf( '<div %1$s %2$s ></div>',
					$thumbnail_classes ? 'class="' . esc_attr( implode( ' ', $thumbnail_classes ) ) . '"' : '',
					$thumbnail_styles ? 'style="' . implode( ' ', $thumbnail_styles ) . '"' : ''
				);
			} else {
				$thumbnail_classes[] = 'basement-gallery-thumb-auto';
				$thumbnail           = sprintf( '<div %1$s %2$s ></div>',
					$thumbnail_classes ? 'class="' . esc_attr( implode( ' ', $thumbnail_classes ) ) . '"' : '',
					$thumbnail_styles ? 'style="' . implode( ' ', $thumbnail_styles ) . '"' : ''
				);
			}
		} else {
			$thumbnail_classes[] = 'basement-gallery-thumb-auto';
			$thumbnail = sprintf('<img src="%1$s" %2$s alt="">',
				!empty($tile_params['thumbnail']['url']) ? $tile_params['thumbnail']['url'] : '',
				'style="max-width: 100%;"'
			);

		}

		$thumbnail_header = sprintf('%3$s<div class="mask-info"><h5>%1$s</h5>%2$s</div>%4$s',
			!empty($post_name) ? esc_html($post_name) : '',
			!empty($thumbnail_taxes) ? '<div class="category">' . esc_html( implode(', ', $thumbnail_taxes) ) . '</div>' : '',
			$click_type_start,
			$click_type_end
		);

		return $thumbnail . $thumbnail_header . $video_icon;
	}



	/**
	 * Generate classic tiles
	 *
	 * @param $grid_params
	 * @param $tile_params
	 * @param $tile_post
	 * @return string
	 */
	private function generate_classic_multirow_tiles($grid_params, $tile_params, $tile_post) {
		$thumbnail_classes = array();
		$thumbnail_styles = array();
		$thumbnail_taxes = array();
		$post_name = get_the_title($tile_post);#$tile_params['params']['title'];

		$thumbnail_full_image = '';


		$thumbnail_classes[] = 'basement-gallery-thumb';

		if(!empty($grid_params['tiles_height'])) {
			$thumbnail_classes[] = 'basement-gallery-thumb-fixed';
			$thumbnail_styles[] = 'height: ' . $grid_params['tiles_height'] . 'px;';

		} else {
			$thumbnail_classes[] = 'basement-gallery-thumb-auto';
		}


		if(!empty($tile_params['thumbnail']['url'])) {
			$thumbnail_styles[] = 'background-image: url(' . $tile_params['thumbnail']['url'] . ');';
		}

		if(!empty($tile_params['terms'])) {
			foreach ($tile_params['terms'] as $tile_term) {
				$thumbnail_taxes[] =  $tile_term->name;
			}
		}

		if(!empty($tile_params['params']['image'])) {
			$thumbnail_full_image = wp_get_attachment_url($tile_params['params']['image']);
		}

		$click_type_start = '<a href="#" title="' . esc_attr($post_name) . '">';
		$click_type_end = '</a>';
		$cursor = '';
		$wrap_link = false;
		$video_icon = '';
		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific" data-title="%1$s %2$s" title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr($post_name) : ''
				);
			} elseif ($grid_params['click_type'] === 'standard') {
				$click_type_start = '<a href="'.get_permalink($tile_post).'" class="basement-link-classic" title="' . esc_attr($post_name) . '">';
				$wrap_link = true;
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url($tile_params['params']['normal_link']) . '" target="_blank" class="basement-link-classic"  title="' . esc_attr($post_name) . '">';
				$wrap_link = true;
			} elseif ($grid_params['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
				$click_type_start = sprintf('<a href="' . esc_url($tile_params['params']['video_link']) . '" class="magnific mfp-iframe" data-title="%1$s %2$s" title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr($post_name) : ''
				);
			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '';
				$click_type_end = '';
				$thumbnail_styles[] = 'cursor: default !important;';
				$cursor = 'style="cursor: default !important;"';
			}
		} elseif ($tile_params['params']['click_type'] === 'standard') {
			$click_type_start = '<a href="'.get_permalink($tile_post).'" class="basement-link-classic"  title="' . esc_attr($post_name) . '">';
			$wrap_link = true;
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific" data-title="%1$s %2$s" title="%3$s">',
				!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr($post_name) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="' . esc_url($tile_params['params']['normal_link']) . '"  target="_blank" class="basement-link-classic" title="' . esc_attr($post_name) . '">';
			$wrap_link = true;
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . esc_url($tile_params['params']['video_link']) . '" class="magnific mfp-iframe" data-title="%1$s %2$s" title="%3$s">',
				!empty($post_name) ? '<span>' . esc_attr($post_name) . '</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr($post_name) : ''
			);
			$video_icon = '<div class="icon"><i class="icon-video"></i></div>';
		}

		if($grid_params['grid_type'] !== 'masonry' && $grid_params['grid_type'] !== 'mixed') {
			$thumbnail = sprintf('%5$s%3$s<div %1$s %2$s ></div>%4$s%6$s',
				$thumbnail_classes ? 'class="'. esc_attr( implode(' ',$thumbnail_classes) ) .'"' : '',
				$thumbnail_styles ? 'style="' . implode(' ',$thumbnail_styles) . '"' : '',
				empty($grid_params['tiles_height'])  ? '' : '',
				empty($grid_params['tiles_height'])  ? '' : '',
				$wrap_link ? $click_type_start : '',
				$wrap_link ? $click_type_end : ''
			);

		} else {
			$thumbnail_classes[] = 'basement-gallery-thumb-auto';
			$thumbnail = sprintf('%5$s%3$s<img src="%1$s" %2$s alt="">%4$s%6$s',
				!empty($tile_params['thumbnail']['url']) ? esc_url($tile_params['thumbnail']['url']) : '',
				'style="max-width: 100%;"',
				empty($grid_params['tiles_height'])  ? '' : '',
				empty($grid_params['tiles_height'])  ? '' : '',
				$wrap_link ? $click_type_start : '',
				$wrap_link ? $click_type_end : ''
			);

		}

		$arrow_icon = '';
		if(empty($click_type_start) && empty($click_type_end)) {
			$arrow_icon = '<div class="icon-arr"></div>';
		} else {
			$click_type_start_fixed = '<a href="#" class="icon-arr" title="">';
			$click_type_end_fixed = '</a>';
			$arrow_icon = $click_type_start_fixed . $click_type_end_fixed;
		}


		$thumbnail_header = sprintf('<div class="work-info %5$s"><h5>%3$s%1$s%4$s</h5>%2$s</div>',
			!empty($post_name) ? esc_html($post_name) : '',
			!empty($thumbnail_taxes) ? '<div class="category">' . esc_html( implode(', ', $thumbnail_taxes) ) . '</div>' : '',
			'<div class="classic-helpers-icons">'.$video_icon. $arrow_icon . '</div>' . $click_type_start,
			$click_type_end,
			!empty($grid_params['tiles_header_position']) ? esc_attr('text-'.$grid_params['tiles_header_position']) : ''
		);

		return array( 'thumbnail' => $thumbnail, 'header' => $thumbnail_header );
	}



	/**
	 * Generate pills
	 *
	 * @param $position
	 * @param $tiles
	 * @return string
	 */
	private function generate_pills( $position, $tiles, $pills ) {

		$categories          = array( '<li class="selected"><span><a href="#all" class="filter" data-filter="*">' . __( 'All', BASEMENT_PORTFOLIO_TEXTDOMAIN ) . '</a></span></li>' );
		$filtrate_categories = array();

		if ( empty( $position ) ) {
			$position = 'center';
		}

		foreach ( $tiles as $tile ) {
			$tile_settings = new Basement_Project_Settings();
			$tile_params   = $tile_settings->get_project( absint( $tile ) );

			if ( ! empty( $tile_params['terms'] ) ) {

				foreach ( $tile_params['terms'] as $tile_term ) {
					if ( ! in_array( $tile_term->term_id, $filtrate_categories ) ) {
						$filtrate_categories[] = $tile_term->term_id;
						$categories[]          = sprintf( '<li><span><a href="#%1$s" class="filter" data-filter=".%1$s">%2$s</a></span></li>',
							sanitize_html_class( $tile_term->slug ),
							esc_attr( $tile_term->name )
						);
					}
				}
			}
		}

		$pillsz = sprintf( '<div class="basement-portfolio-categories %3$s %4$s" %1$s><ul class="basement-portfolio-nav-category">%2$s</ul></div>',
			'style="text-align:' . $position . ';"',
			implode( ' ', $categories ),
			$pills === 'show' ? '' : 'hide',
			'pull-' . $position
		);

		return apply_filters( 'basement_portfolio_pills', $pillsz, $position, $tiles );
	}


	/**
	 * Class for grid
	 *
	 * @param $params
	 * @return string
	 */
	private function generate_grid_classes( $prefix, $params ) {
		$classes = array();
		$class_for = array(
			'cols','type','load_more_size','margins','grid_size','grid_type','tiles_type','click_type','title_position', 'pills_position', 'grid_sorting_position', 'grid_info'
		);

		foreach ($params as $key => $value) {
			if($value === 'mixed') {
				$value = 'masonry';
			}
			if(in_array($key,$class_for)) {
				$classes[] = $prefix . $key .'-'. $value;
			}
		}

		return apply_filters('basement_portfolio_main_classes',implode(' ', $classes));
	}


	/**
	 * Find snap projects
	 *
	 * @param $grid
	 * @return array
	 */
	private function find_snap_tiles($grid) {
		$projects = array();

		$args = array(
			'post_type' => 'single_project',
			'numberposts' => -1
		);
		$project_posts = get_posts( $args );
		$tile_settings = new Basement_Project_Settings();

		foreach( $project_posts as $post ){ setup_postdata($post);
			if ( has_post_thumbnail($post) && ! post_password_required($post) && ! is_attachment() ) {

				$tile_params = $tile_settings->get_project(absint($post->ID));

				if(!empty($tile_params['params']['snap_grid'])) {
					if(in_array($grid, $tile_params['params']['snap_grid'])) {
						$projects[] = $post->ID;
					}
				}
			}
		}
		wp_reset_postdata();

		return $projects;
	}

}


vc_map( array(
	'base'        => 'basement_vc_portfolio',
	'name'        => __( 'Portfolio', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
	'class'       => '',
	'icon'        => BASEMENT_PORTFOLIO_URL . 'assets/images/icon-vc-portfolio.png',
	'category'    => __( 'Basement', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
	'description' => __( 'Creates a simple portfolio', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
	'params'      => array(
		array(
			'type'        => 'basement_choose_portfolio_grid',
			'heading'     => __( 'Grid', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'param_name'  => 'id',
			'admin_label' => true,
			'description' => __( 'Select the grid (if grid has already attached projects, following parameter can be skipped).', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
		),
		array(
			'type'        => 'basement_choose_portfolio_tile',
			'heading'     => __( 'Projects', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
			'param_name'  => 'projects',
			'admin_label' => true,
			'description' => __( 'Select the projects.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
		)
	),
	'js_view'     => 'VcIconElementView_Backend'
) );


if ( ! function_exists( 'basement_vc_portfolio_settings_field' ) ) {
	/**
	 * Register new VC field for Portfolio
	 *
	 * @param $settings
	 * @param $value
	 *
	 * @return string
	 */
	function basement_vc_portfolio_settings_field( $settings, $value ) {
		$dom       = new DOMDocument( '1.0', 'UTF-8' );
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_choose_portfolio_grid' );
		$param_name    = ! empty( $settings['param_name'] ) ? esc_attr( $settings['param_name'] ) : '';
		$param_value   = isset( $value ) ? esc_attr( $value ) : '';
		$grid_list = array(
			'' => __( 'Choose the grid', BASEMENT_PORTFOLIO_TEXTDOMAIN )
		);

		$args           = array(
			'post_type'   => 'portfolio',
			'numberposts' => - 1
		);
		$grid_posts = get_posts( $args );

		foreach ( $grid_posts as $post ) {
			setup_postdata( $post );
			$grid_list[ $post->ID ] = esc_html($post->post_title . ' #' . $post->ID);
		}
		wp_reset_postdata();


		if ( $grid_posts ) {
			$select = $container->appendChild( $dom->createElement( 'select' ) );
			$select->setAttribute( 'class', 'wpb_vc_param_value wpb-input wpb-select dropdown ' . esc_attr( $settings['type'] ) . '_field' );
			$select->setAttribute( 'name', $param_name );
			foreach ( $grid_list as $id => $title ) {
				$option = $select->appendChild( $dom->createElement( 'option', esc_attr( $title ) ) );
				$option->setAttribute( 'value', esc_attr( $id ) );
				if ( $param_value == $id ) {
					$option->setAttribute( 'selected', 'selected' );
				}
			}
		} else {
			$link = $container->appendChild( $dom->createElement( 'a', __( 'Add at least one grid.', BASEMENT_PORTFOLIO_TEXTDOMAIN ) ) );
			$link->setAttribute( 'href', 'post-new.php?post_type=portfolio' );
			$link->setAttribute( 'target', '_blank' );
			$container->setAttribute( 'style', 'margin-top:14px;' );
		}

		return $dom->saveHTML( $container );
	}

	vc_add_shortcode_param( 'basement_choose_portfolio_grid', 'basement_vc_portfolio_settings_field' );
}


if ( ! function_exists( 'basement_choose_portfolio_tile_settings_field' ) ) {
	/**
	 * Register new VC field for Portfolio
	 *
	 * @param $settings
	 * @param $value
	 *
	 * @return string
	 */
	function basement_choose_portfolio_tile_settings_field( $settings, $value ) {
		$dom       = new DOMDocument( '1.0', 'UTF-8' );
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_choose_portfolio_tile' );
		$param_name  = ! empty( $settings['param_name'] ) ? esc_attr( $settings['param_name'] ) : '';
		$param_value = isset( $value ) ? esc_attr( $value ) : '';
		$tag         = 'portfolio';


		$tiles_list = array();
		$args = array(
			'post_type' => 'single_project',
			'numberposts' => -1
		);
		$tile_posts = get_posts( $args );

		foreach( $tile_posts as $post ){ setup_postdata($post);
			$tiles_list[$post->ID] = array(
				'title' => esc_html($post->post_title.' #'.$post->ID),

			);
		}
		wp_reset_postdata();


		if ( $tile_posts ) {
			$select = $container->appendChild( $dom->createElement( 'select' ) );
			$select->setAttribute( 'class', $tag . '_project_add' );

			$first_option = $select->appendChild( $dom->createElement( 'option', __( 'Choose the project', BASEMENT_PORTFOLIO_TEXTDOMAIN ) ) );
			$first_option->setAttribute( 'value', '' );

			foreach ( $tiles_list as $value => $option_param) {
				if(!empty($value)) {
					if ( has_post_thumbnail($value) && ! post_password_required($value) && ! is_attachment() && get_post_status ( $value ) === 'publish' ) {
						$option = $select->appendChild($dom->createElement('option', !empty($option_param['title']) ? $option_param['title'] : $option_param));
						$option->setAttribute('value', esc_attr($value));
						$option->setAttribute('data-edit', get_edit_post_link($value));
						$option->setAttribute('data-img', get_the_post_thumbnail_url($value,'thumbnail'));
						$option->setAttribute('data-edit-title', __('Edit', BASEMENT_PORTFOLIO_TEXTDOMAIN));
						$select->appendChild($option);
					}
				}
			}

			$drag_block = $container->appendChild( $dom->createElement( 'div' ) );
			$drag_block->setAttribute( 'class', $tag . '_project_sortable' );

			$params = array(
				'type'  => 'hidden',
				'class' => $tag . '_project_insert wpb_vc_param_value wpb-input wpb-select dropdown ' . esc_attr( $settings['type'] ) . '_field',
				'name'  => $param_name,
				'value' => $param_value
			);

			$input = new Basement_Form_Input( $params );
			$container->appendChild( $dom->importNode( $input->create(), true ) );

		} else {
			$link = $container->appendChild( $dom->createElement( 'a', __( 'Add at least one project.', BASEMENT_PORTFOLIO_TEXTDOMAIN ) ) );
			$link->setAttribute( 'href', 'post-new.php?post_type=single_project' );
			$link->setAttribute( 'target', '_blank' );
			$container->setAttribute( 'style', 'margin-top:14px;' );
		}

		return $dom->saveHTML( $container );
	}

	vc_add_shortcode_param( 'basement_choose_portfolio_tile', 'basement_choose_portfolio_tile_settings_field', BASEMENT_PORTFOLIO_URL . 'assets/js/back-shortcodes.min.js' );
}