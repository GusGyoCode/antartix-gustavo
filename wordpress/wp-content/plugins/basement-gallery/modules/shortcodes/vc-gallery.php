<?php
defined( 'ABSPATH' ) or die();


class WPBakeryShortCode_basement_vc_gallery extends WPBakeryShortCode {

	protected function content( $atts, $content = null ) {

		$id = $tiles = '';

		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );


		$gallery_grid = $this->grid_exists(absint($id));
		$gallery_tiles = $this->tiles_exists(explode(',',$tiles));
		$gallery = '';

		if( $gallery_grid && $gallery_tiles ) {
			$gallery = $this->grid_builder($gallery_grid, $gallery_tiles);
		}

		return $gallery;
	}


	/**
	 * Get Grid CPT name
	 *
	 * @return string
	 */
	private function get_grid_cpt() {
		$cpt = new Basement_Grid_Cpt();
		return $cpt->grid_cpt_name();
	}


	/**
	 * Get Tile CPT name
	 *
	 * @return string
	 */
	private function get_tile_cpt() {
		$cpt = new Basement_Tile_Cpt();
		return $cpt->tile_cpt_name();
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
			if ( get_post_type(absint($tile)) === $tile_cpt && get_post_status ( absint($tile) ) === 'publish' ) {
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
		$grid_settings = new Basement_Grid_Settings();
		$grid_params   = $grid_settings->get_grid( absint( $id ) );
		$grid          = '';
		if ( $grid_params['type'] === 'singlerow' ) {

			// Build gallery carousel by like usually carousel
			$grid = $this->grid_carousel_builder( $id, $grid_params, $tiles );

		} elseif ( $grid_params['type'] === 'multirow' ) {

			// Build gallery carousel by like usually tile gallery
			$grid = $this->grid_multirow_builder( $id, $grid_params, $tiles );

		}

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
		$normal_tiles = $this->get_normal_tiles($tiles);

		if(empty($normal_tiles))
			return '';

		$grid_classes = $this->generate_grid_classes('basement-gallery-', $params);

		extract($params);


		$mix_ajax_class = uniqid('ajax-mix-');

		$class_grid = '';


		$params_grid_type = !empty($params['grid_type']) ? $params['grid_type'] : '';


		if($params_grid_type === 'grid') {
			$class_grid = 'basement-gallery-mix-list';
		} elseif ($params_grid_type === 'masonry') {
			$class_grid = 'basement-gallery-isotope-list masonry-wrap';
			if(isset($params['layout_mode']) && !empty($params['layout_mode'])) {
				$layout_mode = $params['layout_mode'];
				if($layout_mode === 'default') {
					$layout_mode = 'masonry';
				}
				$class_grid = 'basement-gallery-isotope-list '.$layout_mode.'-wrap';
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



		$pills_position = !empty($pills_position) ? $pills_position : '';
		$pills = !empty($pills) ? $pills : '';


		if ( ! empty( $params['tiles_height'] ) ) {
			$grid_classes  .= ' basement-gallery-carousel-tile-height ';
		}

		$top_bar_style = isset($top_bar_style) ? 'basement-gallery-' . $top_bar_style . '-style' : '';
		$top_bar_padding_bottom = isset($top_bar_padding_bottom) ? $top_bar_padding_bottom : '';
		$style_top_bar = array();
		if(is_numeric($top_bar_padding_bottom)) {
			$style_top_bar[] = "padding-bottom:{$top_bar_padding_bottom}px;";
		}


		$grid_load_type = $params['grid_type'];
		if($grid_load_type === 'mixed') {
			$grid_load_type = 'masonry';
		}

		$fwidth_start = '';
		$fwidth_end = '';


		$gallery_header = '';
		$title_position = !empty($title_position) ? $title_position : '';
		$title = !empty($title) ? $title : '';
		$pills_position = !empty($pills_position) ? $pills_position : '';
		$pills = !empty($pills) ? $pills : '';


		if((empty($pills) || $pills === 'hide') && empty($title)) {
			$style_top_bar[] = "padding-top:0px !important;padding-bottom:0px !important;";
		}
		$style_top_bar = 'style="'.implode('',$style_top_bar).'"';
		if(!empty($title) && $pills === 'show') {
			$center_elements = '';
			$center_status = false;
			if($title_position === 'center' || $pills_position === 'center') {
				$center_elements = $this->generate_header_positions( array(
					'ltr' => 'center',
					'position' => array(
						$title_position => $this->generate_title($title_position, $title),
						$pills_position => $this->generate_pills($pills_position, $tiles, $pills)
					),
					'center' => true
				) );
			}
			if(!empty($center_elements)) {
				$center_status = true;
			}
			$gallery_header = sprintf($fwidth_start.'<div class="row">%1$s%2$s%3$s</div>'.$fwidth_end,
				$this->generate_header_positions( array(
					'ltr' => 'left',
					'position' => array(
						$title_position => $this->generate_title($title_position, $title),
						$pills_position => $this->generate_pills($pills_position, $tiles, $pills)
					),
					'center' => $center_status
				) ),
				$center_elements,
				$this->generate_header_positions( array(
					'ltr' => 'right',
					'position' => array(
						$title_position => $this->generate_title($title_position, $title),
						$pills_position => $this->generate_pills($pills_position, $tiles, $pills)
					),
					'center' => $center_status
				) )
			);
		} else {
			$part_sortable = array(
				'left' => '',
				'center' => '',
				'right' => ''
			);
			$part_positions = array(
				$title_position => $this->generate_title($title_position, $title),
				$pills_position => $this->generate_pills($pills_position, $tiles, $pills)
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
				$gallery_header = $pills_html;
			} elseif ( (empty($part_sortable['left']) && empty($part_sortable['center'])) || (empty($part_sortable['center']) && empty($part_sortable['right'])) || (empty($part_sortable['left']) && empty($part_sortable['right'])) ) {
				$cell = '';
				foreach ($part_sortable as $keys => $item) {
					if($item) {
						$cell = $item;
					}
				}
				$gallery_header = sprintf($fwidth_start.'<div class="row">%1$s%2$s</div>'.$fwidth_end,
					'<div class="col-xs-12">'.$cell.'</div>',
					$pills_html
				);
			} else {
				if(empty($part_sortable['center'])) {
					$gallery_header = sprintf($fwidth_start.'<div class="row">%1$s%2$s%3$s%4$s</div>'.$fwidth_end,
						'<div class="col-lg-6 col-md-4">'.$part_sortable['left'].'</div>',
						'',
						'<div class="col-lg-6 col-md-8">'.$part_sortable['right'].'</div>',
						$pills_html
					);
				} else {
					$gallery_header = sprintf($fwidth_start.'<div class="row">%1$s%2$s%3$s%4$s</div>'.$fwidth_end,
						'<div class="col-md-4">'.$part_sortable['left'].'</div>',
						'<div class="col-md-4">'.$part_sortable['center'].'</div>',
						'<div class="col-md-4">'.$part_sortable['right'].'</div>',
						$pills_html
					);
				}
			}
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

		$grid = sprintf('<div class="basement-gallery-wrap-block %1$s" %4$s><div class="basement-gallery-top-bar %9$s '.$top_bar_style.'" '.$style_top_bar.' %8$s >%2$s%3$s</div><div class="full-width-basement"></div><div '.$grid_styles.' class="row ' . $class_grid . ' basement-gallery-magnific-wrap %7$s">%5$s</div>%6$s</div><div class="full-width-basement"></div>',
			!empty($grid_classes) ? esc_attr( $grid_classes ) : '',
			$gallery_header,//$carousel_title,
			'',
			$grid_size === 'fullwidth' ? 'data-grid-size="fullwidth"' : '',
			(!empty($load_more_size) && absint($load_more_size) < count($tiles)) ? $this->generate_loadmore_tiles($id, $params, $tiles, $load_more_size) : $this->generate_normal_tiles($id, $params, $tiles),
			(!empty($load_more_size) && absint($load_more_size) < count($tiles)) ? '<div class="basement-gallery-load-more"><a href=".'.$mix_ajax_class.'" data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i>' . __('Loading...',BASEMENT_GALLERY_TEXTDOMAIN) . '" class="btn btn-primary basement-load-more" data-grid="' . esc_attr( $id ) . '" data-need="' . esc_attr( $load_more_size ) . '" data-all="' . esc_attr( count($tiles) ) . '" data-load="' . esc_attr( $load_more_size ) . '" data-tiles="' . htmlspecialchars(json_encode($tiles)) . '" data-type="' . esc_attr( $grid_load_type ) . '" title=""><span class="icon-layers"></span>' . __('Load More',BASEMENT_GALLERY_TEXTDOMAIN) . '</a></div>' : '',
			(!empty($load_more_size) && absint($load_more_size) < count($tiles)) ? $mix_ajax_class : '',
			$fullwidth,
			$boxed
		);

		return $grid;
	}






	/**
	 * Generate title for Gallery header
	 *
	 * @param $title_position
	 * @param $title
	 * @return string
	 */
	private function generate_title( $title_position, $title ) {
		return  sprintf('<div class="basement-gallery-title %1$s"><h2>%2$s</h2></div>',
			!empty($title_position) ? 'basement-title-position-' . $title_position : '',
			esc_html( $title )
		);
	}




	/**
	 * Generate positions for Gallery header
	 *
	 * @param $params
	 * @return string
	 */
	private function generate_header_positions( $params ) {
		if(empty($params))
			return '';

		extract($params);

		if($center) {
			if ( ! empty( $position[ $ltr ] ) ) {
				$cell = '<div class="col-md-4">' . $position[ $ltr ] . '</div>';
			} else {
				$cell = '<div class="col-md-4"></div>';
			}
		} else {

			if ( ! empty( $position[ $ltr ] ) ) {
				if ( strpos( $position[ $ltr ], 'categories' ) !== false ) {
					$cell = '<div class="col-lg-6 col-md-8">' . $position[ $ltr ] . '</div>';
				} else {
					$cell = '<div class="col-lg-6 col-md-4">' . $position[ $ltr ] . '</div>';
				}

			} else {
				$cell = '';
			}
		}

		return $cell;
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
			$tiles .= $this->generate_item_tile($grid_params, $tile);
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


		$tile_settings = new Basement_Tile_Settings();
		$tile_params = $tile_settings->get_tile(absint($tile));
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
			foreach ($tile_params['terms'] as $tile_term) {
				$tile_taxes[] = sanitize_html_class($tile_term->slug);
			}

			$tile_post = get_post($tile);

			if ($grid_params['margins'] === 'yes') {
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


			$click_type = !empty($tile_params['params']['click_type']) ? 'basement-gallery-click-'.$tile_params['params']['click_type'] : '';

			if ($grid_params['tiles_type'] === 'hover') {
				$tile_content = $this->generate_hover_multirow_tiles($grid_params, $tile_params, $tile_post);
				$generate_items = sprintf('<div class="%1$s mix %2$s %3$s" %4$s><div class="basement-wrap-inner"><figure class="figure '.$click_type.'">' . $tile_content . '</figure></div></div>',
					esc_attr( $col_xs . ' ' . $col_sm . ' ' . $col_md . ' ' . $col_lg ),
					esc_attr( implode(' ', $tile_taxes) ),
					esc_attr( implode(' ', $tile_classes) ),
					$tile_styles
				);
			} elseif ($grid_params['tiles_type'] === 'classic') {
				$tile_content = $this->generate_classic_multirow_tiles($grid_params, $tile_params, $tile_post);

				$tile_thumbnail = !empty($tile_content['thumbnail']) ? $tile_content['thumbnail'] : '';
				$tile_header = !empty($tile_content['header']) ? $tile_content['header'] : '';

				$generate_items = sprintf('<div class="%1$s mix %2$s %3$s" %6$s><figure class="figure '.$click_type.'">%4$s</figure>%5$s</div>',
					esc_attr( $col_xs . ' ' . $col_sm . ' ' . $col_md . ' ' . $col_lg ),
					esc_attr( implode(' ', $tile_taxes) ),
					esc_attr( implode(' ', $tile_classes) ),
					$tile_thumbnail,
					$tile_header,
					$tile_styles
				);
			} elseif ($grid_params['tiles_type'] === 'simple') {
				$tile_content = $this->generate_simple_multirow_tiles($grid_params, $tile_params, $tile_post);
				$generate_items = sprintf('<div class="%1$s mix %2$s %3$s" %4$s><div class="basement-wrap-inner"><figure class="figure '.$click_type.'">' . $tile_content . '</figure></div></div>',
					esc_attr( $col_xs . ' ' . $col_sm . ' ' . $col_md . ' ' . $col_lg ),
					esc_attr( implode(' ', $tile_taxes) ),
					esc_attr( implode(' ', $tile_classes) ),
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
		$post_name = $tile_post->post_title;

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

		$click_type_start = '<a href="#" class="mask-simple" title="">';
		$click_type_end = '</a>';

		$video_icon = '';
		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask-simple" data-title="%1$s %2$s " title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr( $post_name ) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr( $post_name ) : ''
				);
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url( $tile_params['params']['normal_link'] ) . '" class="mask-simple" target="_blank" title="' . esc_attr( $post_name ) . '">';
			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '';
				$click_type_end = '';
			}
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask-simple" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="'. esc_url( $tile_params['params']['normal_link'] ).'" class="mask-simple" target="_blank" title="' . esc_attr( $post_name ) . '">';
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . $tile_params['params']['video_link'] . '" class="magnific mfp-iframe mask-simple" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
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
				'style="max-width: 100%%;"'
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
		$post_name = $tile_post->post_title;

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

		$click_type_start = '<a href="#" class="mask mask-info mask-dark" title="">';
		$click_type_end = '</a>';

		$video_icon = '';
		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask mask-info mask-dark" data-title="%1$s %2$s" title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr( $post_name ) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr( $post_name ) : ''
				);
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url( $tile_params['params']['normal_link'] ) . '" class="mask mask-info mask-dark" target="_blank" title="' . esc_attr( $post_name ) . '">';
			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '<div class="mask mask-info mask-dark">';
				$click_type_end = '</div>';
			}
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask mask-info mask-dark" data-title="%1$s %2$s" title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="'.esc_url( $tile_params['params']['normal_link'] ).'" class="mask mask-info mask-dark" target="_blank" title="' . esc_attr( $post_name ) . '">';
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . $tile_params['params']['video_link'] . '" class="magnific mfp-iframe mask mask-info mask-dark" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr($post_name).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
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
				'style="max-width: 100%%;"'
			);

		}

		$thumbnail_header = sprintf('%3$s<div class="mask-info"><h5>%1$s</h5>%2$s</div>%4$s',
			!empty($post_name) ? esc_html( $post_name ) : '',
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
		$post_name = $tile_post->post_title;

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

		$click_type_start = '<a href="#" title="">';
		$click_type_end = '</a>';
		$cursor = '';
		$wrap_link = false;
		$video_icon = '';

		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific" data-title="%1$s %2$s " title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr( $post_name ) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr( $post_name ) : ''
				);
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url( $tile_params['params']['normal_link'] ) . '" class="basement-link-classic" target="_blank" title="' . esc_attr( $post_name ) . '">';
				$wrap_link = true;
			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '';
				$click_type_end = '';
				$thumbnail_styles[] = 'cursor: default !important;';
				$cursor = 'style="cursor: default !important;"';
			}
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'. esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="'.esc_url( $tile_params['params']['normal_link']).'" class="basement-link-classic" target="_blank" title="' . esc_attr( $post_name ) . '">';
			$wrap_link = true;
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . $tile_params['params']['video_link'] . '" class="magnific mfp-iframe" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'. esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
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
				'style="max-width: 100%%;"',
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
			!empty($post_name) ? esc_html( $post_name ) : '',
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
	private function generate_pills($position, $tiles, $pills) {
		$categories = array('<li class="selected"><span><a href="#all" class="filter" data-filter="*">' . __('All',BASEMENT_GALLERY_TEXTDOMAIN) . '</a></span></li>');
		$filtrate_categories = array();

		if(empty($position))
			$position = 'center';

		foreach ($tiles as $tile) {
			$tile_settings = new Basement_Tile_Settings();
			$tile_params = $tile_settings->get_tile(absint($tile));

			if(!empty($tile_params['terms'])) {
				foreach ($tile_params['terms'] as $tile_term) {
					if(!in_array($tile_term->term_id, $filtrate_categories)) {
						$filtrate_categories[] = $tile_term->term_id;
						$categories[] = sprintf('<li><span><a href="#%1$s" class="filter" data-filter=".%1$s">%2$s</a></span></li>',
							sanitize_html_class($tile_term->slug),
							esc_html( $tile_term->name )
						);
					}
				}
			}
		}


		$pills = sprintf('<div class="row basement-gallery-categories %3$s" %1$s><ul class="basement-gallery-nav-category">%2$s</ul></div>',
			'style="text-align:' . $position .  ';"',
			implode(' ', $categories),
			$pills === 'show' ? '' : 'hide'
		);

		return apply_filters('basement_gallery_pills', $pills, $position, $tiles);
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
			'cols','type','load_more_size','margins','grid_size','grid_type','tiles_type','click_type','title_position', 'pills_position'
		);

		foreach ($params as $key => $value) {
			if($value === 'mixed') {
				$value = 'masonry';
			}
			if(in_array($key,$class_for)) {
				$classes[] = $prefix . $key .'-'. $value;
			}
		}

		return apply_filters('basement_gallery_main_classes',implode(' ', $classes));
	}


	/**
	 * Gallery Carousel Builder
	 *
	 * @param $id
	 * @param $params
	 * @param $tiles_carousel
	 * @return string
	 */
	private function grid_carousel_builder($id, $params, $tiles_carousel) {
		/*
		 * Extract params to variables
		 */
		extract( $params );




		$arrow_stat = isset($arrow_type) ? 'basement-arrows-'.$arrow_type : '';
		$dots_stat = isset($dots_type) ? 'basement-dots-'.$dots_type : '';


		$tiles_effects = isset($tiles_effects) ? $tiles_effects : '';
		$tiles_easing = isset($tiles_easing) ? $tiles_easing : '';

		/*
		 * Generate CarouFredsel Params
		 */
		$params_gallery = array(
			'responsive' => true,
			#'width' => 'auto',
			'height' => empty($tiles_height) && $tiles_type !== 'classic' ? '100%' : 'variable',
			'items' => array(
				'width' => 100,
				'height' => 'variable',
				'visible' => absint($cols) > count($tiles_carousel) ? count($tiles_carousel) : absint($cols)
			),
			'scroll' => array(
				'fx' => 'crossfade',
				'items' => absint($cols) > count($tiles_carousel) ? count($tiles_carousel) : absint($cols)
			)
		);

		if(!empty($tiles_min) || !empty($tiles_max)) {
			unset($params_gallery['items']['visible']);
			unset($params_gallery['scroll']['items']);
		}

		$tiles_min = isset($tiles_min) ? $tiles_min : false;
		$tiles_max = isset($tiles_max) ? $tiles_max : false;
		$tiles_scroll = isset($tiles_scroll) ? $tiles_scroll : false;
		$tiles_width = isset($tiles_width) ? $tiles_width : false;

		if( is_numeric($tiles_min) && (int)$tiles_min >= 0 ) {
			$params_gallery['items']['visible']['min'] = (int)$tiles_min;
		}

		if( is_numeric($tiles_max) && (int)$tiles_max >= 0 ) {
			$params_gallery['items']['visible']['max'] = (int)$tiles_max;
		}


		if( is_numeric($tiles_scroll) && (int)$tiles_scroll >= 0 ) {
			$params_gallery['scroll']['items'] = (int)$tiles_scroll;
		}

		if( is_numeric($tiles_width) && (int)$tiles_width >= 0 ) {
			$params_gallery['items']['width'] = (int)$tiles_width;
		}

		if( !empty($tiles_scroll) ) {
			$params_gallery['scroll']['items'] = (int)$tiles_scroll;
		}

		if( $auto ) {
			$params_gallery['auto']['play'] = $auto === 'true' ? true : false;
		} else {
			$params_gallery['auto']['play'] = false;
		}


		if( is_numeric( $duration ) && $duration >= 0 ) {
			$params_gallery['scroll']['duration'] = absint($duration);
		}


		if ( !empty($tiles_effects) ) {
			$params_gallery['scroll']['fx'] = $tiles_effects;
		}

		if ( !empty($tiles_easing) ) {
			$params_gallery['scroll']['easing'] = $tiles_easing;
		}


		if(!empty($swipe) && $swipe === 'enable') {
			$params_gallery['swipe']['onTouch'] = true;
			$params_gallery['swipe']['onMouse'] = true;
		}


		/*
		 * Classes for row
		 */
		$classes = array();
		$classes[] = 'basement-gallery-magnific-wrap';
		$classes[] = $this->generate_grid_classes('basement-gallery-carousel-', $params);


		if ( ! empty( $params['tiles_height'] ) ) {
			$classes[] = 'basement-gallery-carousel-tile-height';
		}

		/*
		 * Style for row
		 */
		$style = '';


		$dots_params_get = array(
			'type' => !empty($dots_type) ? $dots_type : 'dots',
			'color' => !empty($dots_color) ? $dots_color : 'standart',
			'size' => !empty($dots_size) ? $dots_size : 'medium',
			'position' => !empty($dots_position) ? $dots_position : 'inside',
			'y' => !empty($dots_position_vertical) ? $dots_position_vertical : 'bottom',
			'x' => !empty($dots_position_horizontal) ? $dots_position_horizontal : 'center'
		);


		$arrows_params_get = array(
			'type' => !empty($arrow_type) ? $arrow_type : 'wobg',
			'color' => !empty($arrow_color) ? $arrow_color : 'standart',
			'size' => !empty($arrow_size) ? $arrow_size : 'medium',
			'position' => !empty($arrow_position) ? $arrow_position : 'inside',
			'y' => !empty($arrow_position_vertical) ? $arrow_position_vertical : '',
			'x'=> !empty($arrow_position_horizontal) ? $arrow_position_horizontal : ''
		);


		$compare_array = array_intersect($dots_params_get,$arrows_params_get);

		$merge = false;
		$merge_controls = '';

		$dots = '';
		$dots_positions = $dots_params_get['position'] . ' ' . $dots_params_get['y'];
		$dots_numbers = '';

		$arrows = '';

		$arrows_positions = $arrows_params_get['position'] . ' ' . $arrows_params_get['y'];


		if ( array_key_exists('position', $compare_array) && array_key_exists('y', $compare_array) && array_key_exists('x', $compare_array) ) {

			$merge = true;


			$prefix_dots = apply_filters( 'basement_gallery_carousel_prefix_merge_paginate','basement-gallery-carousel-paginate-');


			if($dots_params_get['type'] === 'dots') {
				$paginate_id = uniqid('#' . apply_filters( 'basement_gallery_carousel_id_merge_dots', $prefix_dots . 'dots-') );
				$params_gallery['pagination']['container'] = $paginate_id;
			} else {
				$paginate_id =  uniqid('#' . apply_filters( 'basement_gallery_carousel_id_merge_numbers', $prefix_dots. 'number-') );
				$dots_numbers = 'data-basement-total="' . $paginate_id . '"';
			}


			$prefix_arrows = apply_filters( 'basement_gallery_carousel_prefix_merge_arrows','basement-gallery-carousel-arrow-');

			$prev_arrow = uniqid('#' . apply_filters( 'basement_gallery_carousel_id_merge_prev', $prefix_arrows . 'prev-') );
			$next_arrow = uniqid('#' . apply_filters( 'basement_gallery_carousel_id_merge_next', $prefix_arrows . 'next-') );


			$params_gallery['prev'] = $prev_arrow;
			$params_gallery['next'] = $next_arrow;

			$merge_array_dots_arrows = array(
				'dots' => array(
					'params' => $dots_params_get,
					'id' => $paginate_id,
					'prefix' => $prefix_dots
				),
				'arrows' => array(
					'params' => $arrows_params_get,
					'id' => array(
						'prev' => $prev_arrow,
						'next' => $next_arrow
					),
					'prefix' => $prefix_arrows
				)
			);

			if( $arrows_params_get['type'] !== 'nope' || $dots_params_get['type'] !== 'nope' ) {
				if ( $arrows_params_get['type'] === 'nope' ) {
					unset($merge_array_dots_arrows['arrows']);
				}
				if ( $dots_params_get['type'] === 'nope' ) {
					unset($merge_array_dots_arrows['dots']);
				}
				$merge_controls = $this->generate_html_merge_controls( apply_filters('basement_gallery_carousel_generate_merge_controls', $merge_array_dots_arrows ) );
			}

		} else {

			if( $dots_params_get['type'] !== 'nope' ) {

				$prefix_dots = apply_filters( 'basement_gallery_carousel_prefix_paginate','basement-gallery-carousel-paginate-');


				if($dots_params_get['type'] === 'dots') {
					$paginate_id = uniqid('#' . apply_filters( 'basement_gallery_carousel_id_dots', $prefix_dots . 'dots-') );
					$params_gallery['pagination']['container'] = $paginate_id;
				} else {
					$paginate_id =  uniqid('#' . apply_filters( 'basement_gallery_carousel_id_numbers', $prefix_dots. 'number-') );
					$dots_numbers = 'data-basement-total="' . $paginate_id . '"';
				}

				$dots = $this->generate_html_dots( apply_filters('basement_gallery_carousel_generate_dots', array(
					'params' => $dots_params_get,
					'id' => $paginate_id,
					'prefix' => $prefix_dots
				) ) );
			}

			if( $arrows_params_get['type'] !== 'nope' ) {

				$prefix_arrows = apply_filters( 'basement_gallery_carousel_prefix_arrows','basement-gallery-carousel-arrow-');

				$prev_arrow = uniqid('#' . apply_filters( 'basement_gallery_carousel_id_prev', $prefix_arrows . 'prev-') );
				$next_arrow = uniqid('#' . apply_filters( 'basement_gallery_carousel_id_next', $prefix_arrows . 'next-') );


				$params_gallery['prev'] = $prev_arrow;
				$params_gallery['next'] = $next_arrow;

				$arrows = $this->generate_html_arrows( apply_filters('basement_gallery_carousel_generate_arrows', array(
					'params' => $arrows_params_get,
					'id' => array(
						'prev' => $prev_arrow,
						'next' => $next_arrow
					),
					'prefix' => $prefix_arrows
				) ) );
			}
		}



		// Slides
		$slides = $this->generate_html_tiles( apply_filters( 'basement_gallery_carousel_generate_html_tiles', array(
			'ids' => $tiles_carousel,
			'grid_params' => $params,
			'classes' => array('clearfix')
		) ) );


		// Carousel
		$main_carousel = sprintf( '<div class="%1$s" data-carousel-id="%2$s" %3$s %4$s>%5$s</div>',
			esc_attr('basement-gallery-carousel clearfix'),
			esc_attr( $id ),
			!empty($params_gallery) ? 'data-basement-params="' . htmlspecialchars(json_encode($params_gallery)) . '"' : '',
			$dots_numbers,
			$slides
		);


		$arrows_params_get_size = !empty($arrows_params_get['size']) ? 'basement-help-'.$arrows_params_get['size'] : '';
		$arrows_params_get_position = !empty($arrows_params_get['position']) ? 'basement-help-'.$arrows_params_get['position'] : '';
		$arrows_params_get_y = !empty($arrows_params_get['y']) ? 'basement-help-'.$arrows_params_get['y'] : '';
		$arrows_params_get_type = !empty($arrows_params_get['type']) ? 'basement-help-'.$arrows_params_get['type'] : '';


		// Help Row carolusel
		$builder_row = sprintf( '<div class="%1$s %4$s">%2$s%3$s%5$s%6$s</div><div class="full-width-basement-help-row"></div>' ,
			esc_attr('basement-gallery-carousel-help-row'),
			$main_carousel,
			$this->generate_positions( array(
				'position' => 'inrow ',
				'arrows' => array(
					'position' => $arrows_positions,
					'elements' => $arrows
				),
				'merge' => $merge
			) ),
			esc_attr($arrow_stat .' ' . $dots_stat .' '. $arrows_params_get_size . ' ' .$arrows_params_get_position . ' ' .$arrows_params_get_y . ' ' .$arrows_params_get_type ),
			$this->generate_positions( array(
				'position' => 'inside side',
				'arrows' => array(
					'position' => $arrows_positions,
					'elements' => $arrows
				),
				'merge' => $merge
			) ),
			$this->generate_positions( array(
				'position' => 'outside side',
				'arrows' => array(
					'position' => $arrows_positions,
					'elements' => $arrows
				),
				'merge' => $merge
			) )
		);



		$top_bar_padding_bottom = isset($top_bar_padding_bottom) ? $top_bar_padding_bottom : '';
		$style_top_bar = array();
		if(is_numeric($top_bar_padding_bottom)) {
			$style_top_bar[] = "padding-bottom:{$top_bar_padding_bottom}px;";
		}

		$carousel_title = '';



		$boxed = '';
		$fullwidth = '';

		switch ($top_bar_size) {
			case 'fullwidth' :
				$fullwidth = 'data-grid-size="fullwidth"';
				break;
		}


		if(!empty($title)) {
			$carousel_title = sprintf('<div class="basement-gallery-carousel-title %1$s" %3$s %4$s><h2>%2$s</h2></div><div class="full-width-basement"></div>',
				!empty($title_position) ? esc_attr( 'basement-title-position-' . $title_position ) : '',
				$title,
				!empty($style_top_bar) ? 'style="'.implode('',$style_top_bar).'"' : '',
				$fullwidth
			);
		}


		// Carousel Row
		$carousel_row = sprintf( '%10$s%6$s<div class="%1$s %2$s" %3$s %4$s>%7$s%5$s%8$s</div><div class="full-width-basement"></div>%9$s' ,
			esc_attr('basement-gallery-carousel-row clearfix'),
			esc_attr( implode(' ', $classes) ),
			$grid_size === 'fullwidth' ? 'data-grid-size="fullwidth"' : '',
			!empty($style) ? 'style="'. $style .'"' : '',
			$builder_row,
			$this->generate_positions( array(
				'position' => 'outside top',
				'dots' => array(
					'position' => $dots_positions,
					'x' => $dots_params_get['x'],
					'elements' => $dots
				),
				'arrows' => array(
					'position' => $arrows_positions,
					'x' => $arrows_params_get['x'],
					'elements' => $arrows
				),
				'merged' => array(
					'status' => $merge,
					'elements' => $merge_controls
				)
			) ),
			$this->generate_positions( array(
				'position' => 'inside top',
				'dots' => array(
					'position' => $dots_positions,
					'x' => $dots_params_get['x'],
					'elements' => $dots
				),
				'arrows' => array(
					'position' => $arrows_positions,
					'x' => $arrows_params_get['x'],
					'elements' => $arrows
				),
				'merged' => array(
					'status' => $merge,
					'elements' => $merge_controls
				)
			) ),
			$this->generate_positions( array(
				'position' => 'inside bottom',
				'dots' => array(
					'position' => $dots_positions,
					'x' => $dots_params_get['x'],
					'elements' => $dots
				),
				'arrows' => array(
					'position' => $arrows_positions,
					'x' => $arrows_params_get['x'],
					'elements' => $arrows
				),
				'merged' => array(
					'status' => $merge,
					'elements' => $merge_controls
				)
			) ),
			$this->generate_positions( array(
				'position' => 'outside bottom',
				'dots' => array(
					'position' => $dots_positions,
					'x' => $dots_params_get['x'],
					'elements' => $dots
				),
				'arrows' => array(
					'position' => $arrows_positions,
					'x' => $arrows_params_get['x'],
					'elements' => $arrows
				),
				'merged' => array(
					'status' => $merge,
					'elements' => $merge_controls
				)
			) ),
			!empty($carousel_title) ? $carousel_title : ''
		);


		// Container Gallery
		$container = sprintf( '<div class="%1$s">%2$s</div>' ,
			esc_attr('basement-gallery-carousel-container clearfix'),
			$carousel_row
		);


		$container_size = isset($grid_size) ? 'basement-gallery-'.$grid_size : '';
		$top_bar_style = isset($top_bar_style) ? 'basement-gallery-' . $top_bar_style . '-style' : '';


		$is_title_flag = '';
		if(!empty($title)) {
			$is_title_flag = 'basement-gallery-has-title';
		}


		
		$wrap_classses = array();

		if(!empty($dots_lg)) {
			$wrap_classses[] = 'basement-gallery-'.$dots_lg;
		}

		if(!empty($dots_md)) {
			$wrap_classses[] = 'basement-gallery-'.$dots_md;
		}

		if(!empty($dots_sm)) {
			$wrap_classses[] = 'basement-gallery-'.$dots_sm;
		}

		if(!empty($dots_xs)) {
			$wrap_classses[] = 'basement-gallery-'.$dots_xs;
		}




		if(!empty($arrows_lg)) {
			$wrap_classses[] = 'basement-gallery-'.$arrows_lg;
		}

		if(!empty($arrows_md)) {
			$wrap_classses[] = 'basement-gallery-'.$arrows_md;
		}

		if(!empty($arrows_sm)) {
			$wrap_classses[] = 'basement-gallery-'.$arrows_sm;
		}

		if(!empty($arrows_xs)) {
			$wrap_classses[] = 'basement-gallery-'.$arrows_xs;
		}



		// Common block
		$gallery_area = sprintf( '<div class="%1$s %4$s" %2$s>%3$s</div>' ,
			esc_attr('basement-gallery-carousel-wrap-block ' . $container_size . ' ' . $top_bar_style . ' ' .$is_title_flag),
			!empty($margin) ? 'style="'. $margin .'"' : '',
			$container,
			!empty($wrap_classses) ? implode(' ', $wrap_classses) : ''
		);


		return $gallery_area;
	}



	/**
	 * Return positions for gallery controls
	 *
	 * @param array $params
	 * @return string
	 */
	private function generate_positions( $params = array() ) {

		extract( $params );

		$controls = '';


		$merged_status = !empty($merged['status']) ? $merged['status'] : '';
		$dots_position = !empty($dots['position']) ? $dots['position'] : '';
		$arrows_position = !empty($arrows['position']) ? $arrows['position'] : '';
		$dots_x = !empty($dots['x']) ? $dots['x'] : '';
		$arrows_x = !empty($arrows['x']) ? $arrows['x'] : '';
		$arrows_elements = !empty($arrows['elements']) ? $arrows['elements'] : '';
		$dots_elements = !empty($dots['elements']) ? $dots['elements'] : '';
		$position = !empty($position) ? $position : '';

		if(!$merged_status) {
			if ($dots_position === $arrows_position && $position === $dots_position && $position === $arrows_position) {

				$controls = sprintf('<div class="%1$s">%2$s%3$s%4$s%5$s</div>',
					esc_attr('basement-gallery-carousel-inline-controls clearfix'),
					(($arrows_x === 'left' || $arrows_x === 'right') && $dots_x === 'center') ? $arrows_elements . $dots_elements : '',
					(($dots_x === 'left' || $dots_x === 'right') && $arrows_x === 'center') ? $dots_elements . $arrows_elements : '',
					(($arrows_x === 'left') && ($dots_x === 'right')) ? $arrows_elements . $dots_elements : '',
					(($dots_x === 'left') && ($arrows_x === 'right')) ? $dots_elements . $arrows_elements : ''
				);

			} else {


				if ($position === $dots_position) {
					if ($dots_x === 'left' || $dots_x === 'right') {
						$controls = sprintf('<div class="%2$s">%1$s</div>',
							$dots_elements,
							esc_attr('basement-gallery-carousel-dots-controls clearfix')
						);
					} else {
						$controls .= $dots_elements;
					}
				}

				if ($position === $arrows_position) {
					if ($arrows_x === 'left' || $arrows_x === 'right') {
						$controls = sprintf('<div class="%2$s">%1$s</div>',
							$arrows_elements,
							esc_attr('basement-gallery-carousel-arrows-controls clearfix')
						);
					} else {
						$controls .= $arrows_elements;
					}

				}
			}
		} else {
			if ($dots_position === $arrows_position && $position === $dots_position && $position === $arrows_position) {
				$controls = sprintf('<div class="%1$s">%2$s</div>',
					esc_attr('basement-gallery-carousel-merge-controls clearfix'),
					$merged['elements']
				);
			}
		}
		return $controls;
	}



	/**
	 * Generate html tiles for gallery
	 *
	 * @param array $params
	 * @return string
	 */
	private function generate_html_tiles( $params = array() ) {
		extract( $params = wp_parse_args( $params, array(
			'ids' => array(),
			'grid_params' => array(),
			'classes' => array(),
			'id' => '',
			'attributes' => array(),
			'slug' => false
		) ) );

		$tile = '';
		$tile_build = '';


		if( $ids ) {

			foreach( $ids as $id_tile ) {
				$tile_settings = new Basement_Tile_Settings();
				$tile_params = $tile_settings->get_tile(absint($id_tile));

				if($tile_params) {
					$tile_post = get_post($id_tile);

					$inner_classes = '';
					$tile_filter = isset($tile_params['params']['filter']) ? $tile_params['params']['filter'] : '';
					$grid_filter = isset($grid_params['filter']) ? $grid_params['filter'] : '';


					$tile_filter_behavior = isset($tile_params['params']['filter_behavior']) ? $tile_params['params']['filter_behavior'] : '';
					$grid_filter_behavior = isset($grid_params['filter_behavior']) ? $grid_params['filter_behavior'] : '';


					if ( ! empty( $tile_filter ) && $tile_filter !== 'default' ) {
						$inner_classes = ' bs-filter-' . $tile_filter;
					} else {
						$inner_classes = ' bs-filter-' . $grid_filter;
					}


					if ( ! empty( $tile_filter_behavior ) && $tile_filter_behavior !== 'default' ) {
						$inner_classes .= ' bs-is-' . $tile_filter_behavior;
					} else {
						$inner_classes .= ' bs-is-' . $grid_filter_behavior;
					}


					$click_type = !empty($tile_params['params']['click_type']) ? 'basement-gallery-click-'.$tile_params['params']['click_type'] : '';

					if($grid_params['tiles_type'] === 'hover') {
						$tile_content = $this->generate_hover_tiles($grid_params, $tile_params, $tile_post);
						$tile_build = sprintf('<div %1$s %2$s %3$s data-tile-id="%4$s" %5$s><figure class="figure '.$click_type.'">%6$s</figure></div>',
							!empty($classes) ? 'class="' . esc_attr(implode(' ', $classes)) . $inner_classes .'"' : '',
							!empty($id) ? 'id="' . esc_attr($id) . '"' : '',
							!empty($attributes) ? $this->generate_html_attributes($attributes) : '',
							esc_attr($id_tile),
							$slug ? 'data-tile-slug="' . esc_attr( $tile_post->post_name ) . '"' : '',
							$tile_content
						);
					} elseif ($grid_params['tiles_type'] === 'classic') {
						$tile_content = $this->generate_classic_tiles($grid_params, $tile_params, $tile_post);
						$tile_thumbnail = !empty($tile_content['thumbnail']) ? $tile_content['thumbnail'] : '';
						$tile_header = !empty($tile_content['header']) ? $tile_content['header'] : '';
						$tile_build = sprintf('<div %1$s %2$s %3$s data-tile-id="%4$s" %5$s><figure class="figure '.$click_type.'">%6$s</figure>%7$s</div>',
							!empty($classes) ? 'class="' . esc_attr(implode(' ', $classes)) . $inner_classes .'"' : '',
							!empty($id) ? 'id="' . esc_attr($id) . '"' : '',
							!empty($attributes) ? $this->generate_html_attributes($attributes) : '',
							esc_attr($id_tile),
							$slug ? 'data-tile-slug="' . esc_attr( $tile_post->post_name ) . '"' : '',
							$tile_thumbnail,
							$tile_header
						);
					} elseif ($grid_params['tiles_type'] === 'simple') {
						$tile_content = $this->generate_simple_tiles($grid_params, $tile_params, $tile_post);
						$tile_build = sprintf('<div %1$s %2$s %3$s data-tile-id="%4$s" %5$s><figure class="figure '.$click_type.'">%6$s</figure></div>',
							!empty($classes) ? 'class="' . esc_attr(implode(' ', $classes)) . $inner_classes .'"' : '',
							!empty($id) ? 'id="' . esc_attr($id) . '"' : '',
							!empty($attributes) ? $this->generate_html_attributes($attributes) : '',
							esc_attr($id_tile),
							$slug ? 'data-tile-slug="' . esc_attr( $tile_post->post_name ) . '"' : '',
							$tile_content
						);
					}

					$tile .= $tile_build;
				}
			}
		}

		return $tile;
	}


	/**
	 * Generate simple tiles
	 *
	 * @param $grid_params
	 * @param $tile_params
	 * @param $tile_post
	 * @return string
	 */
	private function generate_simple_tiles($grid_params, $tile_params, $tile_post) {
		$thumbnail_classes = array();
		$thumbnail_styles = array();
		$thumbnail_taxes = array();
		$post_name = $tile_post->post_title;

		$thumbnail_full_image = '#';


		$thumbnail_classes[] = 'basement-gallery-carousel-thumb';

		if(!empty($grid_params['tiles_height'])) {
			$thumbnail_classes[] = 'basement-gallery-carousel-thumb-fixed';
			$thumbnail_styles[] = 'height:' . $grid_params['tiles_height'] . 'px;';
		} else {
			$thumbnail_classes[] = 'basement-gallery-carousel-thumb-auto';
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

		$click_type_start = '<a href="#" class="mask-simple" title="">';
		$click_type_end = '</a>';
		$video_icon = '';
		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask-simple" data-title="%1$s %2$s" title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr( $post_name ) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr( $post_name ) : ''
				);
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url( $tile_params['params']['normal_link'] ) . '" class="mask-simple" target="_blank" title="' . esc_attr( $post_name ) . '">';
			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '';
				$click_type_end = '';
			}
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask-simple" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr($post_name).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr(implode(', ', $thumbnail_taxes)) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="'.esc_url( $tile_params['params']['normal_link'] ).'" class="mask-simple" target="_blank" title="' . esc_attr( $post_name ) . '">';
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . $tile_params['params']['video_link'] . '" class="magnific mfp-iframe mask-simple" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
			$video_icon = '<div class="icon"><i class="icon-video"></i></div>';
		}


		$thumbnail = sprintf('<div %1$s %2$s ></div>',
			$thumbnail_classes ? 'class="'. esc_attr( implode(' ',$thumbnail_classes) ) .'"' : '',
			$thumbnail_styles ? 'style="' . implode(' ',$thumbnail_styles) . '"' : ''
		);


		$thumbnail_header = $click_type_start . $click_type_end;

		return $thumbnail . $video_icon . $thumbnail_header;
	}


	/**
	 * Generate hover tiles
	 *
	 * @param $grid_params
	 * @param $tile_params
	 * @param $tile_post
	 * @return string
	 */
	private function generate_hover_tiles($grid_params, $tile_params, $tile_post) {
		$thumbnail_classes = array();
		$thumbnail_styles = array();
		$thumbnail_taxes = array();
		$post_name = $tile_post->post_title;

		$thumbnail_full_image = '#';


		$thumbnail_classes[] = 'basement-gallery-carousel-thumb';

		if(!empty($grid_params['tiles_height'])) {
			$thumbnail_classes[] = 'basement-gallery-carousel-thumb-fixed';
			$thumbnail_styles[] = 'height: ' . $grid_params['tiles_height'] . 'px;';
		} else {
			$thumbnail_classes[] = 'basement-gallery-carousel-thumb-auto';
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

		$click_type_start = '<a href="#" class="mask mask-info mask-dark" title="">';
		$click_type_end = '</a>';
		$video_icon = '';
		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask mask-info mask-dark" data-title="%1$s %2$s" title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr( $post_name ) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr( $post_name ) : ''
				);
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url( $tile_params['params']['normal_link'] ) . '" class="mask mask-info mask-dark" target="_blank" title="' . esc_attr($post_name) . '">';
			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '<div class="mask mask-info mask-dark">';
				$click_type_end = '</div>';
			}
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific mask mask-info mask-dark" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="'.esc_url( $tile_params['params']['normal_link'] ).'" class="mask mask-info mask-dark" target="_blank" title="' . esc_attr( $post_name ) . '">';
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . $tile_params['params']['video_link'] . '" class="magnific mfp-iframe mask mask-info mask-dark" data-title="%1$s %2$s" title="%3$s"><i class="i-video"></i>',
				!empty($post_name) ? '<span>'. esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
			$video_icon = '<div class="icon"><i class="icon-video"></i></div>';
		}


		$thumbnail = sprintf('<div %1$s %2$s ></div>',
			$thumbnail_classes ? 'class="'. esc_attr( implode(' ',$thumbnail_classes) ) .'"' : '',
			$thumbnail_styles ? 'style="' . implode(' ',$thumbnail_styles) . '"' : ''
		);


		$thumbnail_header = sprintf('%3$s<div class="mask-info"><h5>%1$s</h5>%2$s</div>%4$s',
			!empty($post_name) ? esc_html( $post_name ) : '',
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
	private function generate_classic_tiles($grid_params, $tile_params, $tile_post) {
		$thumbnail_classes = array();
		$thumbnail_styles = array();
		$thumbnail_taxes = array();
		$post_name = $tile_post->post_title;

		$thumbnail_full_image = '';


		$thumbnail_classes[] = 'basement-gallery-carousel-thumb';

		if(!empty($grid_params['tiles_height'])) {
			$thumbnail_classes[] = 'basement-gallery-carousel-thumb-fixed';
			$thumbnail_styles[] = 'height: ' . $grid_params['tiles_height'] . 'px;';

		} else {
			$thumbnail_classes[] = 'basement-gallery-carousel-thumb-auto';
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

		$click_type_start = '<a href="#" title="">';
		$click_type_end = '</a>';
		$cursor = '';
		$wrap_link = false;
		$video_icon = '';
		if($tile_params['params']['click_type'] === 'default') {
			if($grid_params['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
				$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific" data-title="%1$s %2$s " title="%3$s">',
					!empty($post_name) ? '<span>' . esc_attr( $post_name ) . '</span>' : '',
					!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
					!empty($post_name) ? esc_attr( $post_name ) : ''
				);
			} elseif ($grid_params['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
				$click_type_start = '<a href="' . esc_url( $tile_params['params']['normal_link'] ) . '" target="_blank" class="basement-link-classic" title="' . esc_attr( $post_name ) . '">';
				$wrap_link = true;
			} elseif ($grid_params['click_type'] === 'none') {
				$click_type_start = '';
				$click_type_end = '';
				$thumbnail_styles[] = 'cursor: default !important;';
				$cursor = 'style="cursor: default !important;"';
			}
		} elseif ($tile_params['params']['click_type'] === 'popup' && !empty($thumbnail_full_image)) {
			$click_type_start = sprintf('<a href="' . $thumbnail_full_image . '" class="magnific" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
		} elseif ($tile_params['params']['click_type'] === 'link' && !empty($tile_params['params']['normal_link'])) {
			$click_type_start = '<a href="'.esc_url( $tile_params['params']['normal_link']).'" target="_blank" class="basement-link-classic" title="' . esc_attr( $post_name ) . '">';
			$wrap_link = true;
		} elseif ($tile_params['params']['click_type'] === 'video' && !empty($tile_params['params']['video_link'])) {
			$click_type_start = sprintf('<a href="' . $tile_params['params']['video_link'] . '" class="magnific mfp-iframe" data-title="%1$s %2$s " title="%3$s">',
				!empty($post_name) ? '<span>'.esc_attr( $post_name ).'</span>' : '',
				!empty($thumbnail_taxes) ? '<small>' . esc_attr( implode(', ', $thumbnail_taxes) ) . '</small>' : '',
				!empty($post_name) ? esc_attr( $post_name ) : ''
			);
			$video_icon = '<div class="icon"><i class="icon-video"></i></div>';
		}



		$thumbnail = sprintf('%5$s%3$s<div %1$s %2$s ></div>%4$s%6$s',
			$thumbnail_classes ? 'class="'. esc_attr( implode(' ',$thumbnail_classes) ) .'"' : '',
			$thumbnail_styles ? 'style="' . implode(' ',$thumbnail_styles) . '"' : '',
			empty($grid_params['tiles_height'])  ? '' : '',
			empty($grid_params['tiles_height'])  ? '' : '',
			$wrap_link ? $click_type_start : '',
			$wrap_link ? $click_type_end : ''
		);

		$arrow_icon = '';
		if(empty($click_type_start) && empty($click_type_end)) {
			$arrow_icon = '<div class="icon-arr"></div>';
		} else {
			$click_type_start_fixed = '<a href="#" class="icon-arr" title="">';
			$click_type_end_fixed = '</a>';
			$arrow_icon = $click_type_start_fixed . $click_type_end_fixed;
		}

		$thumbnail_header = sprintf('<div class="work-info %5$s"><h5>%3$s%1$s%4$s</h5>%2$s</div>',
			!empty($post_name) ? $post_name : '',
			!empty($thumbnail_taxes) ? '<div class="category">' . esc_html( implode(', ', $thumbnail_taxes) ) . '</div>' : '',
			'<div class="classic-helpers-icons">'.$video_icon. $arrow_icon . '</div>' . $click_type_start,
			$click_type_end,
			!empty($grid_params['tiles_header_position']) ?  esc_attr('text-'.$grid_params['tiles_header_position']) : ''
		);

		return array( 'thumbnail' => $thumbnail , 'header' => $thumbnail_header );
	}



	/**
	 * Generate custom attributes
	 *
	 * @param $attributes
	 * @return string
	 */
	private function generate_html_attributes( $attributes ) {
		$html_attributes = '';
		if ( !(empty( $attributes ) && is_array( $attributes ) ) ) {
			foreach ( $attributes as $name => $value ) {
				$current_values = explode(' ', $name  );
				$current_values[] = $value;

				$html_attributes .= trim( implode( '="', $current_values ), ' ' ).'" ';
			}
		}
		return $html_attributes;
	}


	/**
	 * Generate merge carousel controls
	 *
	 * @param array $params
	 * @return string
	 */
	private function generate_html_merge_controls( $params = array() ) {

		$classes_outer = array();
		$merge_controls = '';

		extract( $params );


		if( $arrows ) {
			$classes_outer[] = substr($arrows['prefix'],0,-1) . 's';

			if(isset($dots['params']['type'])) {
				$classes_outer[] = 'basement-gallery-dots-' . $dots['params']['type'];
			}

			switch ($arrows['params']['type']) {
				case 'wobg' :
					$classes_outer[] = $arrows['prefix'] . 'nobg';
					break;
				case 'bg' :
					$classes_outer[] = $arrows['prefix'] . 'bg';
					break;
			}

			switch ($arrows['params']['color']) {
				case 'light' :
					$classes_outer[] = $arrows['prefix'] . 'light';
					break;
				case 'standart' :
					$classes_outer[] = $arrows['prefix'] . 'standart';
					break;
				case 'dark' :
					$classes_outer[] = $arrows['prefix'] . 'dark';
					break;
			}

			switch ($arrows['params']['size']) {
				case 'small' :
					$classes_outer[] = $arrows['prefix'] . 'small';
					break;
				case 'medium' :
					$classes_outer[] = $arrows['prefix'] . 'medium';
					break;
				case 'large' :
					$classes_outer[] = $arrows['prefix'] . 'large';
					break;
			}

			switch ($arrows['params']['position']) {
				case 'inside' :
					$classes_outer[] = $arrows['prefix'] . 'inside';
					break;
				case 'outside' :
					$classes_outer[] = $arrows['prefix'] . 'outside';
					break;
				case 'inrow' :
					$classes_outer[] = $arrows['prefix'] . 'inrow';
					break;
			}


			switch ($arrows['params']['y']) {
				case 'top' :
					$classes_outer[] = $arrows['prefix'] . 'top';
					break;
				case 'bottom' :
					$classes_outer[] = $arrows['prefix'] . 'bottom';
					break;
				case 'side' :
					$classes_outer[] = $arrows['prefix'] . 'side';
					break;
			}

			switch ($arrows['params']['x']) {
				case 'left' :
					$classes_outer[] = $arrows['prefix'] . 'left';
					break;
				case 'center' :
					$classes_outer[] = $arrows['prefix'] . 'center';
					break;
				case 'right' :
					$classes_outer[] = $arrows['prefix'] . 'right';
					break;
			}

			$prev = '<a href="#" id="' . esc_attr( substr($arrows['id']['prev'],1) ) . '" class="' . esc_attr( $arrows['prefix'] ) . 'prev" title=""></a>';
			$next = '<a href="#" id="' . esc_attr( substr($arrows['id']['next'],1) ) . '" class="' . esc_attr( $arrows['prefix'] ) . 'prev" title=""></a>';


			$merge_controls = sprintf('<div class="%1$s">%2$s%3$s%4$s</div>',
				esc_attr( implode( ' ', $classes_outer ) ),
				$arrows ? $prev : '',
				$dots ? $this->generate_html_dots($dots) : '',
				$arrows ? $next : ''
			);

		} elseif ( $dots ) {
			$merge_controls = $this->generate_html_dots($dots);
		}

		return $merge_controls;
	}



	/**
	 * Generate html dots
	 *
	 * @param array $params
	 * @return string
	 */
	private function generate_html_dots( $params = array() ) {

		$classes = array();
		$prefix = $params['prefix'];

		$number_content = '';

		extract( $params['params'] );

		if(!empty($type)) {
			switch ( $type ) {
				case 'dots' :
					$classes[] = $prefix . 'dots';
					break;
				case 'number' :
					$classes[]      = $prefix . 'number';
					$number_content = '<span class="' . esc_attr( $prefix ) . 'current">1</span><ins>&#8213;</ins><span class="' . esc_attr( $prefix ) . 'all">1</span>';
					break;
			}
		}

		if(!empty($color)) {
			switch ( $color ) {
				case 'light' :
					$classes[] = $prefix . 'light';
					break;
				case 'standart' :
					$classes[] = $prefix . 'standart';
					break;
				case 'dark' :
					$classes[] = $prefix . 'dark';
					break;
			}
		}

		if(!empty($size)) {
			switch ( $size ) {
				case 'small' :
					$classes[] = $prefix . 'small';
					break;
				case 'medium' :
					$classes[] = $prefix . 'medium';
					break;
				case 'large' :
					$classes[] = $prefix . 'large';
					break;
			}
		}


		if(!empty($position)) {
			switch ( $position ) {
				case 'inside' :
					$classes[] = $prefix . 'inside';
					break;
				case 'outside' :
					$classes[] = $prefix . 'outside';
					break;
			}
		}

		if(!empty($y)) {
			switch ( $y ) {
				case 'top' :
					$classes[] = $prefix . 'top';
					break;
				case 'bottom' :
					$classes[] = $prefix . 'bottom';
					break;
			}
		}

		if(!empty($x)) {
			switch ( $x ) {
				case 'left' :
					$classes[] = $prefix . 'left';
					break;
				case 'center' :
					$classes[] = $prefix . 'center';
					break;
				case 'right' :
					$classes[] = $prefix . 'right';
					break;
			}
		}


		$dots = sprintf( '<div id="%1$s" class="%2$s">%3$s</div>',
			esc_attr( substr($params['id'],1) ),
			esc_attr( implode( ' ', $classes ) ),
			$number_content
		);

		return $dots;
	}


	/**
	 * Generate html arrows
	 *
	 * @param array $params
	 * @return string
	 */
	private function generate_html_arrows( $params = array() ) {

		$classes = array();
		$prefix = $params['prefix'];

		extract( $params['params'] );

		$classes[] = substr($prefix,0,-1) . 's';

		if(!empty($type)) {
			switch ( $type ) {
				case 'wobg' :
					$classes[] = $prefix . 'nobg';
					break;
				case 'bg' :
					$classes[] = $prefix . 'bg';
					break;
			}
		}

		if(!empty($color)) {
			switch ( $color ) {
				case 'light' :
					$classes[] = $prefix . 'light';
					break;
				case 'standart' :
					$classes[] = $prefix . 'standart';
					break;
				case 'dark' :
					$classes[] = $prefix . 'dark';
					break;
			}
		}

		if(!empty($size)) {
			switch ( $size ) {
				case 'small' :
					$classes[] = $prefix . 'small';
					break;
				case 'medium' :
					$classes[] = $prefix . 'medium';
					break;
				case 'large' :
					$classes[] = $prefix . 'large';
					break;
			}
		}

		if(!empty($position)) {
			switch ( $position ) {
				case 'inside' :
					$classes[] = $prefix . 'inside';
					break;
				case 'outside' :
					$classes[] = $prefix . 'outside';
					break;
				case 'inrow' :
					$classes[] = $prefix . 'inrow';
					break;
			}
		}


		if(!empty($y)) {
			switch ( $y ) {
				case 'top' :
					$classes[] = $prefix . 'top';
					break;
				case 'bottom' :
					$classes[] = $prefix . 'bottom';
					break;
				case 'side' :
					$classes[] = $prefix . 'side';
					break;
			}
		}

		if(!empty($x)) {
			switch ( $x ) {
				case 'left' :
					$classes[] = $prefix . 'left';
					break;
				case 'center' :
					$classes[] = $prefix . 'center';
					break;
				case 'right' :
					$classes[] = $prefix . 'right';
					break;
			}
		}


		$arrows = sprintf( '<div class="%1$s"><a href="#" id="%2$s" class="' . esc_attr( $prefix ) . 'prev" title=""></a><a href="#" id="%3$s" class="' . esc_attr( $prefix ) . 'next" title=""></a></div>',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( substr($params['id']['prev'],1) ),
			esc_attr( substr($params['id']['next'],1) )
		);

		return $arrows;
	}

}


vc_map( array(
	'base'        => 'basement_vc_gallery',
	'name'        => __( 'Gallery', BASEMENT_GALLERY_TEXTDOMAIN ),
	'class'       => '',
	'icon'        => PATCH_GALLERY . 'assets/images/icon-vc-gallery.png',
	'category'    => __( 'Basement', BASEMENT_GALLERY_TEXTDOMAIN ),
	'description' => __( 'Creates a simple gallery', BASEMENT_GALLERY_TEXTDOMAIN ),
	'params'      => array(
		array(
			'type'        => 'basement_choose_grid',
			'heading'     => __( 'Grid', BASEMENT_GALLERY_TEXTDOMAIN ),
			'param_name'  => 'id',
			'admin_label' => true,
			'description' => __( 'Select the grid.', BASEMENT_GALLERY_TEXTDOMAIN ),
		),
		array(
			'type'        => 'basement_choose_tile',
			'heading'     => __( 'Tiles', BASEMENT_GALLERY_TEXTDOMAIN ),
			'param_name'  => 'tiles',
			'admin_label' => true,
			'description' => __( 'Select the tiles.', BASEMENT_GALLERY_TEXTDOMAIN ),
		)
	),
	'js_view'     => 'VcIconElementView_Backend'
) );


if ( ! function_exists( 'basement_vc_gallery_settings_field' ) ) {
	/**
	 * Register new VC field for gallery
	 *
	 * @param $settings
	 * @param $value
	 *
	 * @return string
	 */
	function basement_vc_gallery_settings_field( $settings, $value ) {
		$dom       = new DOMDocument( '1.0', 'UTF-8' );
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_choose_grid' );
		$param_name    = ! empty( $settings['param_name'] ) ? esc_attr( $settings['param_name'] ) : '';
		$param_value   = isset( $value ) ? esc_attr( $value ) : '';
		$grid_list = array(
			'' => __( 'Choose the grid', BASEMENT_GALLERY_TEXTDOMAIN )
		);

		$args           = array(
			'post_type'   => 'grid',
			'numberposts' => - 1
		);
		$grid_posts = get_posts( $args );

		foreach ( $grid_posts as $post ) {
			setup_postdata( $post );
			$grid_list[ $post->ID ] = $post->post_title . ' #' . $post->ID;
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
			$link = $container->appendChild( $dom->createElement( 'a', __( 'Add at least one grid.', BASEMENT_GALLERY_TEXTDOMAIN ) ) );
			$link->setAttribute( 'href', 'post-new.php?post_type=grid' );
			$link->setAttribute( 'target', '_blank' );
			$container->setAttribute( 'style', 'margin-top:14px;' );
		}

		return $dom->saveHTML( $container );
	}

	vc_add_shortcode_param( 'basement_choose_grid', 'basement_vc_gallery_settings_field' );
}


if ( ! function_exists( 'basement_choose_tile_settings_field' ) ) {
	/**
	 * Register new VC field for Gallery
	 *
	 * @param $settings
	 * @param $value
	 *
	 * @return string
	 */
	function basement_choose_tile_settings_field( $settings, $value ) {
		$dom       = new DOMDocument( '1.0', 'UTF-8' );
		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', 'basement_choose_tile' );
		$param_name  = ! empty( $settings['param_name'] ) ? esc_attr( $settings['param_name'] ) : '';
		$param_value = isset( $value ) ? esc_attr( $value ) : '';
		$tag         = 'galleries';


		$tiles_list = array();
		$args = array(
			'post_type' => 'tile',
			'numberposts' => -1
		);
		$tile_posts = get_posts( $args );

		foreach( $tile_posts as $post ){ setup_postdata($post);
			$tiles_list[$post->ID] = array(
				'title' => $post->post_title.' #'.$post->ID,

			);
		}
		wp_reset_postdata();


		if ( $tile_posts ) {
			$select = $container->appendChild( $dom->createElement( 'select' ) );
			$select->setAttribute( 'class', $tag . '_tile_add' );

			$first_option = $select->appendChild( $dom->createElement( 'option', __( 'Choose the tile', BASEMENT_GALLERY_TEXTDOMAIN ) ) );
			$first_option->setAttribute( 'value', '' );

			foreach ( $tiles_list as $value => $option_param) {
				if(!empty($value)) {
					if ( has_post_thumbnail($value) && ! post_password_required($value) && ! is_attachment() && get_post_status ( $value ) === 'publish' ) {
						$option = $select->appendChild($dom->createElement('option', !empty($option_param['title']) ? $option_param['title'] : $option_param));
						$option->setAttribute('value', $value);
						$option->setAttribute('data-edit', get_edit_post_link($value));
						$option->setAttribute('data-img', get_the_post_thumbnail_url($value,'thumbnail'));
						$option->setAttribute('data-edit-title', __('Edit', BASEMENT_GALLERY_TEXTDOMAIN));
						$select->appendChild($option);
					}
				}
			}

			$drag_block = $container->appendChild( $dom->createElement( 'div' ) );
			$drag_block->setAttribute( 'class', $tag . '_tile_sortable' );

			$params = array(
				'type'  => 'hidden',
				'class' => $tag . '_tile_insert wpb_vc_param_value wpb-input wpb-select dropdown ' . esc_attr( $settings['type'] ) . '_field',
				'name'  => $param_name,
				'value' => $param_value
			);

			$input = new Basement_Form_Input( $params );
			$container->appendChild( $dom->importNode( $input->create(), true ) );

		} else {
			$link = $container->appendChild( $dom->createElement( 'a', __( 'Add at least one tile.', BASEMENT_GALLERY_TEXTDOMAIN ) ) );
			$link->setAttribute( 'href', 'post-new.php?post_type=tile' );
			$link->setAttribute( 'target', '_blank' );
			$container->setAttribute( 'style', 'margin-top:14px;' );
		}

		return $dom->saveHTML( $container );
	}

	vc_add_shortcode_param( 'basement_choose_tile', 'basement_choose_tile_settings_field', PATCH_GALLERY . 'assets/js/back-shortcodes.min.js' );
}