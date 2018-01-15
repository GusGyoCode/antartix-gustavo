<?php
defined('ABSPATH') or die();


final class Basement_Project_Front {

	private static $instance = null;

	public function __construct($front = true) {
		if($front) {
			// Get project
			$this->get_single_project();
		}
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if (null === self::$instance) {
			self::$instance = new Basement_Project_Front();
		}
		return self::$instance;
	}

	/**
	 * Get Project CPT name
	 *
	 * @return string
	 */
	private function get_project_cpt() {
		$cpt = new Basement_Project_Cpt();
		return $cpt->project_cpt_name();
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
	 * Check if Project CPT exist
	 *
	 * @param $id
	 * @return bool
	 */
	public function project_exists( $id ) {
		$grid_cpt = $this->get_project_cpt();
		if(get_post_type(absint($id)) === $grid_cpt && get_post_meta(absint($id))) {
			return $id;
		} else {
			return false;
		}
	}


	/**
	 * Echo/Return single project
	 */
	public function get_single_project() {
		$id = get_the_ID();
		$exit_project = $this->project_exists($id);

		if($exit_project) {

			// Get project params
			$settings_class = new Basement_Project_Settings();
			$settings = $settings_class->get_project($id);


			if(!empty($settings['params']['position_custom_fields']) && $settings['params']['position_custom_fields'] === 'bottom') {
				// Generate project content
				echo $this->project_content( $settings );
			}


			// Generate project featured
			echo $this->project_featured($settings);


			// Generate project navigation
			echo $this->project_navigation($settings);
		}
	}


	/**
	 * Generate featured projects
	 *
	 * @param $params
	 * @return string
	 */
	public function project_featured($params) {
		if(!empty($params))
			extract($params['params']);

		$html = '';
		if(!empty($featured)) {
			/*if( count($featured) === 1 ) {
				$navs = sprintf('<div class="row basement-slider-project-nav"><div class="col-lg-12 text-center"><h2>'.__('Featured Projects', BASEMENT_PORTFOLIO_TEXTDOMAIN).'</h2></div></div>');
			} elseif (count($featured) === 3 || count($featured) === 2) {
				$navs = sprintf('<div class="row basement-slider-project-nav"><div class="col-lg-12 text-center"><h2>'.__('Featured Projects', BASEMENT_PORTFOLIO_TEXTDOMAIN).'</h2></div><div class="col-lg-12 hidden-lg" style="text-align:center !important;"><div class="slider-nav" style="text-align:center !important; margin-top: 20px;"><a href="" class="prev"></a><nav class="nav-pages"></nav><a href="" class="next"></a></div></div></div>');
			} else {
				$navs = sprintf('<div class="row basement-slider-project-nav"><div class="col-sm-5"><h2>'.__('Featured Projects', BASEMENT_PORTFOLIO_TEXTDOMAIN).'</h2></div><div class="col-sm-7"><div class="slider-nav"><a href="" class="prev"></a><nav class="nav-pages"></nav><a href="" class="next"></a></div></div></div>');
			}*/

			$navs = '';

			$slides = '';
			foreach ($featured as $feature) {

				if (has_post_thumbnail($feature) && !post_password_required($feature)) {

					$ftitle = get_the_title($feature->ID);
					$tems = get_the_terms( $feature->ID, 'project_category' );
					$tems_html = array();
					if(!empty($tems)) {
						foreach ($tems as $tem) {
							$tems_html[] = $tem->name;
						}
					}

					$t_htmle = '';

					if(!empty($tems_html)) {
						$t_htmle = implode(', ', $tems_html );
					}

					$image_slide = get_the_post_thumbnail_url($feature, 'large');
					$link_slide = get_permalink($feature);

					$slides .= $this->generate_featured_slide($image_slide, $link_slide, $ftitle, $t_htmle );
				}
			}


			if(count($featured) === 1) {
				#$slides .= '<div class="col-sm-4 col-xs-12"></div><div class="col-sm-4 col-xs-12"></div>';
			}

			if(count($featured) === 2) {
				#$slides .= '<div class="col-sm-4 col-xs-12"></div>';
			}

			$pagi = '';
			$arri = '';


			$html = $this->generate_featured_slider($slides);

		}

		return $html;
	}


	/**
	 * Generate featured slider for works
	 *
	 * @param $slides
	 *
	 * @return string
	 */
	public function generate_featured_slider($slides) {
		$slider = '';

		$slider .= '<div class="basement-project-featured" data-grid-size="fullwidth">';
			$slider .= '<a href="#" class="basement-project-featured-left basement-project-featured-arrow" title=""></a>';
			$slider .= '<a href="#" class="basement-project-featured-right basement-project-featured-arrow" title=""></a>';

			$slider .= '<div class="basement-project-featured-slider row">';
			$slider .= $slides;
			$slider .= '</div>';
		$slider .= '</div>';
		$slider .= '<div class="full-width-basement"></div>';

		return $slider;
	}


	/**
	 * Generate slider for featured works
	 *
	 * @param $image
	 * @param $link
	 * @param $title
	 * @param $categories
	 *
	 * @return string
	 */
	public function generate_featured_slide($image, $link, $title, $categories) {
		$slide = '';

		$slide .= '<div class="col-sm-3">';
			$slide .= '<figure class="figure">';
				$slide .= '<div class="basement-featured-thumbnail" style="background-image: url('.$image.');"></div>';

				$slide .= '<a href="'.esc_url($link).'" class="mask mask-info mask-dark" title="'.esc_attr($title).'">';

					$slide .= '<div class="mask-info">';

					$slide .= '<h5>'.esc_html($title).'</h5><div class="category">'.esc_html($categories).'</div>';

					$slide .= '</div>';

				$slide .= '</a>';

			$slide .= '</figure>';
		$slide .= '</div>';

		return $slide;
	}



	/**
	 * Generate navigation for project
	 *
	 * @param $params
	 * @return string
	 */
	public function project_navigation($params) {
		if(!empty($params))
			extract($params['params']);


		$html = '';
		if(!empty($pagination)) {
			if ( $pagination == 'yes' ) {


				$prev = '<div class="col-sm-5"></div>';
				if ( ! empty( $prev_link ) || $prev_link !== '0' ) {

					$post_prev = get_post( absint( $prev_link ) );

					if ( ! empty( $post_prev ) ) {

						$post_prev_title = get_the_title( absint( $prev_link ) );

						$prev = '<div class="col-sm-5 clearfix"><div class="prev pull-left" ><div class="inner-project-links"><span>'.__('Previous Project', BASEMENT_PORTFOLIO_TEXTDOMAIN).'</span><a href="' . get_permalink( $post_prev ) . '" title="' . esc_attr( $post_prev_title ) . '">' . esc_html( $post_prev_title ) . '</a></div></div></div>';
					}
				}

				$next = '<div class="col-sm-5"></div>';
				if ( ! empty( $next_link ) || $next_link !== '0' ) {

					$post_next = get_post( absint( $next_link ) );

					if ( ! empty( $post_next ) ) {

						$post_next_title = get_the_title( absint( $next_link ) );

						$next = '<div class="col-sm-5 clearfix"><div class="next pull-right" ><div class="inner-project-links"><span>'.__('Next Project', BASEMENT_PORTFOLIO_TEXTDOMAIN).'</span><a href="' . get_permalink( $post_next ) . '" title="' . esc_attr( $post_next_title ) . '">' . esc_html( $post_next_title ) . '</a></div></div></div>';
					}

				}

				$all = '<div class="col-sm-2"></div>';
				if ( ! empty( $grid_url ) ) {
					$all = '<div class="col-sm-2"><a href="' . esc_url( $grid_url ) . '" class="all ais-all-projects"></a></div>';
				}

				$html = sprintf( '<nav class="row basement-project-pagination">%1$s%2$s%3$s</nav>',
					$prev,
					$all,
					$next
					#( ( ! empty( $prev_link ) || $prev_link !== '0' ) || ( ! empty( $next_link ) || $next_link !== '0' ) || ( ! empty( $grid_url ) ) ) ? '<hr class="basement-project-nav-hr">' : ''
				);
			}
		}

		return $html;
	}


	/**
	 * Generate main project content
	 *
	 * @param $params
	 * @return string
	 */
	public function project_content( $params ) {
		if ( ! empty( $params ) )
			extract( $params['params'] );


		$terms = isset($params['terms']) ? $params['terms'] : array();

		$html = '';
		if ( ! empty( $custom_fields ) ) {
			$html = $this->project_custom_fields( $params['params'], $terms );
		}

		return $html;
	}


	/**
	 * Custom fields generator
	 *
	 * @param       $params
	 * @param array $custom_terms
	 *
	 * @return string
	 */
	public function project_custom_fields($params, $custom_terms = array() ) {
		if(!empty($params))
			extract($params);

		$inner_field = '';

		if(!empty($custom_fields) && isset($custom_field)) {

			$terms = explode( ',', $custom_fields );


			$inner_cell  = '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 basement-type-cf-%3$s"><div class="header-custom-field">%1$s</div><div class="body-custom-field">%2$s</div></div>';
			$i           = 0;
			foreach ( $custom_field as $field ) {

				$get_term = get_term_by( 'slug', $terms[ $i ++ ], 'project_custom_fields' );
				$terms_id = $get_term->term_id;
				$term_obj = get_term( $terms_id );

				$term_id   = $term_obj->term_id;
				$term_name = $term_obj->name;
				$term_slug = $term_obj->slug;
				$term_type = get_term_meta( $term_id, 'display_type', true );

				if ( ! empty( $field ) || ! empty( $term_name ) ) {
					switch ( $term_type ) {
						case 'categories' :
							$case_field = '';
							$taxes = array();

							if(!empty($custom_terms)) {
								foreach ($custom_terms as $term) {
									$taxes[] = $term->name;
								}
							}

							if(!empty($field)) {
								foreach ( $field as $case ) {
									$case_field .= $case;
								}
							}
							$inner_field .= sprintf( $inner_cell,
								esc_html( $case_field ),
								!empty($taxes) ? esc_html( implode(', ', $taxes) ) : '',
								$term_type
							);
							break;
						case 'text' :
						case 'textblock' :
							$case_field = '';
							if(!empty($field)) {
								foreach ( $field as $case ) {
									$case_field .= $case;
								}
							}
							$inner_field .= sprintf( $inner_cell, esc_html( $term_name ), nl2br( $case_field ), $term_type );

							break;
						case 'link' :
							$params_link = '';
							$case_field = '';
							$case_name   = isset( $field['0'] ) ? $field['0'] : '';
							$case_link   = isset( $field['1'] ) ? $field['1'] : '';
							$case_target = isset($field['2']) && $field['2'] === 'yes' ? 'target="_blank"' : '';
							if(!empty($case_name)) {
								$case_field = sprintf( '<a class="basement-custom-link-project vc_general vc_btn3 vc_btn3-size-lg vc_btn3-shape-rounded vc_btn3-style-underlined vc_btn3-color-inverse" href="%1$s" %4$s %2$s>%3$s</a>',
									!empty($case_link) ? $case_link : '',
									$case_target,
									esc_html( $case_name ),
									apply_filters( 'basement_portfolio_custom_link_params', $params_link )
								);
							}
							$inner_field .= sprintf( $inner_cell, esc_html( $term_name ), $case_field, $term_type );
							break;
						case 'button' :
							$case_name = isset( $field['0'] ) ? $field['0'] : '';
							$case_link = isset( $field['1'] ) ? $field['1'] : '';
							$btn = '';
							$params_btn = '';
							$case_style = '';
							$span_l     = '';
							$span_r     = '';
							switch ( $field['2'] ) {
								case 'default' :
									$case_style = 'vc_btn3-style-flat vc_btn3-color-white';
									break;
								case 'btn-primary' :
									$case_style = 'vc_btn3-style-flat vc_btn3-color-inverse';
									break;
								case 'btn-success' :
									$case_style = 'vc_btn3-style-flat vc_btn3-color-success';
									break;
								case 'btn-info' :
									$case_style = 'vc_btn3-style-flat vc_btn3-color-info';
									break;
								case 'btn-warning' :
									$case_style = 'vc_btn3-style-flat vc_btn3-color-warning';
									break;
								case 'btn-danger' :
									$case_style = 'vc_btn3-style-flat vc_btn3-color-danger';
									break;
								case 'btn-link' :
									$case_style = 'vc_btn3-style-underlined vc_btn3-color-inverse';
									break;
							}
							$case_size = '';
							switch ( $field['3'] ) {
								case 'default' :
									$case_size = 'vc_btn3-size-md';
									break;
								case 'btn-lg' :
									$case_size = 'vc_btn3-size-lg';
									break;
								case 'btn-sm' :
									$case_size = 'vc_btn3-size-sm';
									break;
								case 'btn-xs' :
									$case_size = 'vc_btn3-size-xs';
									break;
							}

							if(!empty($case_name)) {
								$btn = sprintf( '<a class="vc_general vc_btn3 vc_btn3-shape-round %1$s" %4$s href="%2$s">%3$s</a>',
									esc_attr( $case_style ) . ' ' . esc_attr( $case_size ),
									!empty($case_link) ? $case_link : '',
									$span_l . esc_html( $case_name ) . $span_r,
									apply_filters( 'basement_portfolio_custom_btn_params', $params_btn )
								);
							}

							$inner_field .= sprintf( $inner_cell, '', $btn, $term_type );

							break;
					}
				}

			}
		}

		return sprintf('<div class="basement-project-info-main">%1$s</div>', $inner_field );
	}
}


if ( ! function_exists( 'basement_the_single_project' ) ) {
	/**
	 * Main front function for project
	 */
	function basement_the_single_project() {
		Basement_Project_Front::init();
	}
}

if ( ! function_exists( 'basement_single_project_page_title' ) ) {
	/**
	 * Displays page title for Page Title
	 */
	function basement_single_project_page_title($echo = true) {
		$id = get_the_ID();

		$page_title = get_post_meta($id, '_basement_meta_project_title',true);

		if($echo) {
			echo ! empty( $page_title ) ? $page_title : get_the_title( $id );
		} else {
			return ! empty( $page_title ) ? $page_title : get_the_title( $id );
		}
	}
}


if ( ! function_exists( 'basement_body_class_single_project' ) ) {
	/**
	 * Classes body for Single Project
	 */
	function basement_body_class_single_project( $classes ) {

		if ( is_singular( 'single_project' ) ) {
			$id             = get_the_ID();
			$settings_class = new Basement_Project_Settings();
			$settings       = $settings_class->get_project( $id );

			if ( isset( $settings['params']['pagination'] ) && $settings['params']['pagination'] === 'yes' ) {
				$classes[] = 'basement_single_project_pagination_yes';
			} else {
				$classes[] = 'basement_single_project_pagination_no';
			}
		}

		return $classes;
	}

	add_action( 'body_class', 'basement_body_class_single_project' );
}


if ( ! function_exists( 'basement_custom_field_page_title' ) ) {
	function basement_custom_field_page_title() {
		$id = get_the_ID();

		$front_obj = new Basement_Project_Front(false);

		if ( is_singular( 'single_project' ) ) {

			// Get project params
			$settings_class = new Basement_Project_Settings();
			$settings  = $settings_class->get_project( $id );

			if(!empty($settings['params']['position_custom_fields']) && $settings['params']['position_custom_fields'] === 'top') {
				echo '<div class="container">';
					// Generate project content
					echo $front_obj->project_content( $settings );
				echo '</div>';
			}
		}
	}

	add_action( 'conico_after_page_title_float', 'basement_custom_field_page_title' );
}