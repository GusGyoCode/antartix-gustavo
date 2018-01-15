<?php

if ( ! function_exists( 'basement_page_title_float_sort_elements' ) ) {
	/**
	 * Display template parts elements in float page title
	 */
	function basement_page_title_float_sort_elements() {
		get_template_part( 'template-parts/page-title/title-float' );
	}
	add_action( 'conico_content_page_title_float', 'basement_page_title_float_sort_elements', 10 );
}


if ( ! function_exists( 'basement_page_title_float_class' ) ) {
	/**
	 * Display the classes for the page title float element.
	 *
	 * @param string $class
	 * @param bool   $echo
	 *
	 * @return array
	 */
	function basement_page_title_float_class( $class = '', $echo = true ) {
		if($echo) {
			// Separates classes with a single space, collates classes for page title element
			echo 'class="' . join( ' ', basement_get_page_title_float_class( $class ) ) . '" ';
		} else {
			return array('class'=>join( ' ', basement_get_page_title_float_class( $class ) ));
		}
	}
}


if ( ! function_exists( 'basement_get_page_title_float_class' ) ) {
	/**
	 * Retrieve the classes for the page title float element as an array.
	 *
	 * @param string $class
	 *
	 * @return array
	 */
	function basement_get_page_title_float_class( $class = '' ) {
		$classes = array();

		$settings  = Basement_Page_Title();


		if(!empty($settings)) {
			foreach( $settings as $key => $value) {
				if(strpos($key, 'pt_float') !== false && strpos($key, 'size') === false && strpos($key, 'color') === false) {
					$classes[] = $key .'_' . $value;
				}
			}
		}

		$classes[] = 'pt_float_position_left';

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );

		$classes = apply_filters( 'pagetitle_float_class', $classes, $class );

		return array_unique( $classes );
	}
}


if ( ! function_exists( 'basement_action_theme_before_page_title_float' ) ) {
	/**
	 * Displays params before Page Title
	 */
	function basement_action_theme_before_page_title_float() {
		ob_start();
	}
	add_action('conico_before_page_title_float', 'basement_action_theme_before_page_title_float');
}


if ( ! function_exists( 'basement_action_after_page_title_float' ) ) {
	/**
	 * Displays params after Page Title
	 */
	function basement_action_after_page_title_float() {

		$settings  = Basement_Page_Title();


		$pt_float_enable = isset($settings['pt_float_enable']) ? $settings['pt_float_enable'] : '';

		$page_title_float = ob_get_contents();
		ob_end_clean();


		if ( $pt_float_enable === 'yes' && ( is_singular( array( 'post', 'single_project' ) ) || is_page() ) ) {
			printf('%s', $page_title_float);
		}
	}
	add_action('conico_after_page_title_float', 'basement_action_after_page_title_float');
}



if ( ! function_exists( 'basement_page_title_class' ) ) {
	/**
	 * Display the classes for the page title element.
	 *
	 * @param $class
	 */
	function basement_page_title_class( $class = '', $echo = true ) {

		$settings  = get_pagetitle_settings();

		$style      = array();
		$style_attr = '';

		$pt_bg = isset($settings['pt_bg']) ? $settings['pt_bg'] : '';
		$padding_top    = is_numeric( $settings['pt_padding_top'] ) ? $settings['pt_padding_top'] : '';
		$padding_bottom = is_numeric( $settings['pt_padding_bottom'] ) ? $settings['pt_padding_bottom'] : '';

		if(is_numeric($pt_bg)) {
			$pt_bg = wp_get_attachment_image_url($pt_bg,'full');
			if(!empty($pt_bg)) {
				$style[]   = "background-image:url({$pt_bg});";
			}
		} else {
			$pt_bg = esc_url($pt_bg);
			if(!empty($pt_bg)) {
				$style[]   = "background-image:url({$pt_bg});";
			}
		}



		if($padding_top !== '') {
			$padding_top = absint($padding_top);
			$style[]   = "padding-top:{$padding_top}px;";
		}

		if($padding_bottom !== '') {
			$padding_bottom = absint($padding_bottom);
			$style[]   = "padding-bottom:{$padding_bottom}px;";
		}

		if ( ! empty( $style ) ) {
			$style      = implode( ' ', $style );
			$style_attr = " style=\"{$style}\" ";
		}

		if($echo) {
			// Separates classes with a single space, collates classes for page title element
			echo 'class="' . join( ' ', basement_get_page_title_class( $class ) ) . '" ' . $style_attr;
		} else {
			return array('class'=>join( ' ', basement_get_page_title_class( $class ) ), 'style' => $style_attr);
		}
	}
}


if ( ! function_exists( 'get_pagetitle_settings' ) ) {
	function get_pagetitle_settings() {

		$settings = Basement_Page_Title();

		return apply_filters( 'basement_pagetitle_settings', $settings );
	}
}


if ( ! function_exists( 'front_classes_page_title' ) ) {
	function front_classes_page_title( $in_classes = '' ) {
		$names   = apply_filters( 'basement_pagetitle_classes', get_pagetitle_settings() );
		$classes = array();
		foreach ( $names as $key => $value ) {

			if ( $value ) {

				if ( $key === 'pt_elements' ) {
					foreach ( $value as $inner_key => $inner_value ) {
						if ( empty( $inner_value ) ) {
							$classes[] = 'page-title_' . $inner_key . '_no';
						} else {
							$classes[] = 'page-title_' . $inner_key . '_yes';
						}
					}
				}

				if (
					$key === 'pt_alternate' ||
					$key === 'pt_elements' ||
					$key === 'pt_float_text_size' ||
					$key === 'pt_float_text_color' ||
					$key === 'pt_custom_title' ||
					$key === 'pt_icon' ||
					$key === 'pt_icon_size' ||
					$key === 'pt_icon_color' ||
					$key === 'pt_title_size' ||
					$key === 'pt_title_color' ||
					$key === 'pt_bg' ||
					$key === 'pt_bg_color' ||
					$key === 'pt_bg_opacity' ||
					$key === 'pt_padding_top' ||
					$key === 'pt_padding_bottom'
				) {
					continue;
				}

				$classes[] = 'page-title' . substr( $key, 2 ) . '_' . sanitize_html_class( $value );

			}
		}

		$classes[] = $in_classes;

		return implode( ' ', apply_filters( 'basement_pagetitle_classes_format', $classes ) );
	}
}


if ( ! function_exists( 'basement_get_page_title_class' ) ) {
	/**
	 * Retrieve the classes for the page title element as an array.
	 *
	 * @param string $class
	 *
	 * @return array
	 */
	function basement_get_page_title_class( $class = '' ) {
		$classes = array();


		$classes[] = front_classes_page_title();

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );

		$classes = apply_filters( 'pagetitle_class', $classes, $class );

		return array_unique( $classes );
	}
}


if ( ! function_exists( 'basement_page_title_sort_elements' ) ) {
	/**
	 * Display template parts elements in page title
	 */
	function basement_page_title_sort_elements() {
		get_template_part( 'template-parts/page-title/page-title' );
	}
	add_action( 'conico_content_page_title', 'basement_page_title_sort_elements', 10 );
}


if ( ! function_exists( 'basement_the_title_alternative' ) ) {
	/**
	 * Display alternative title
	 *
	 * @param bool $return
	 *
	 * @return string
	 */
	function basement_the_title_alternative($return = false) {

		$settings  = Basement_Page_Title();
		$alternate = isset($settings['pt_alternate']) ? $settings['pt_alternate'] : '';

		if(!empty($alternate)) {
			if($return) {
				return do_shortcode(nl2br($alternate));
			} else {
				echo do_shortcode(nl2br($alternate));
			}
		}

	}
}


if ( ! function_exists( 'basement_the_specific_title' ) ) {
	/**
	 * Displays title for specific page
	 *
	 * @param string $type
	 * @param string $user_title
	 * @param bool   $echo
	 *
	 * @return bool|string
	 */
	function basement_the_specific_title( $type = '', $user_title = '', $echo = true ) {
		if ( ! $type ) {
			return false;
		}

		$title = esc_html( get_option( "basement_framework_pt_custom_{$type}" ) );

		if ( empty( $title ) ) {
			$title = $user_title;
		}

		if ( $echo ) {
			printf('%s', $title);
		} else {
			return $title;
		}
	}
}


if ( ! function_exists( 'basement_action_theme_before_page_title' ) ) {
	/**
	 * Displays params before Page Title
	 */
	function basement_action_theme_before_page_title() {
		ob_start();
	}
	add_action('conico_before_page_title', 'basement_action_theme_before_page_title');
}


if ( ! function_exists( 'basement_action_theme_after_page_title' ) ) {
	/**
	 * Displays params after Page Title
	 */
	function basement_action_theme_after_page_title() {

		$settings  = Basement_Page_Title();


		$id = get_the_ID();
		$is_woo = false;


		$revslider_position = '';
		$shortcode = '';
		if ( is_page() || is_single()  ) {

			$revslider_position = get_post_meta( $id, 'basement_rev_position', true );
			$revslider_position = ! empty( $revslider_position ) ? $revslider_position : '';

			$shortcode = get_post_meta( $id, 'revlider_content_meta', true );
			$shortcode = ! empty( $shortcode ) ? $shortcode : '';

			if(empty($shortcode)) {
				$revslider_position = '';
			}
		}

		$pt_off = isset($settings['pt_off']) ? $settings['pt_off'] : '';
		$pt_element_title = isset($settings['pt_elements']['title']) ? $settings['pt_elements']['title'] : '';
		$pt_element_breadcrumb = isset($settings['pt_elements']['breadcrumbs']) ? $settings['pt_elements']['breadcrumbs'] : '';
		$pt_element_breadcrumb_woo = isset($settings['pt_elements']['woo_breadcrumbs']) ? $settings['pt_elements']['woo_breadcrumbs'] : '';
		$pt_elements_icon = isset($settings['pt_elements']['icon']) ? $settings['pt_elements']['icon'] : '';

		$page_title = ob_get_contents();
		ob_end_clean();

		if ( isset( $settings ) && ( $revslider_position !== 'header_content' && $pt_off == 'no' && ( ! empty( $pt_element_title ) || ( ! $is_woo && ! empty( $pt_element_breadcrumb ) ) || ( $is_woo && ! empty( $pt_element_breadcrumb_woo ) ) ) ) ) {

			printf('%s', $page_title);
		}
	}
	add_action('conico_after_page_title', 'basement_action_theme_after_page_title');
}


if ( ! function_exists( 'basement_page_title_customize' ) ) {
	/**
	 * Customize Page Title
	 */
	function basement_page_title_customize() {
		$settings  = Basement_Page_Title();
		$styles = array(
			'.main-page-title' => '',
			'.pagetitle:after' => ''
		);

		#$flow = isset($settings['pt_flow']) ? $settings['pt_flow'] : '';
		$title_size = isset($settings['pt_title_size']) ? $settings['pt_title_size'] : '';
		$title_color = isset($settings['pt_title_color']) ? $settings['pt_title_color'] : '';
		$pt_bg_color = isset($settings['pt_bg_color']) ? $settings['pt_bg_color'] : '';
		$pt_bg_opacity = is_numeric($settings['pt_bg_opacity']) ? $settings['pt_bg_opacity'] : '';

		if(is_numeric($title_size)) {
			$styles['.main-page-title'] .= "font-size:{$title_size}px !important;";
		}

		if(!empty($title_color)) {
			$title_color = sanitize_hex_color($title_color);
			$styles['.main-page-title'] .= "color:{$title_color} !important;";
		}

		if(!empty($pt_bg_color)) {
			$pt_bg_color = sanitize_hex_color($pt_bg_color);
			$pt_bg_color = basement_hexToRgb($pt_bg_color);
			$pt_bg_color = !empty($pt_bg_color) ? $pt_bg_color['red'] .','. $pt_bg_color['green'] .','.$pt_bg_color['blue'] : '';
			$end_opacity = ',1';
			if($pt_bg_opacity !== '') {
				$end_opacity = ",{$pt_bg_opacity}";
			}
			$styles['.pagetitle:after'] .= "background-color:rgba({$pt_bg_color}{$end_opacity}) !important;";
		}



		if ( ! empty( $styles ) ) {
			?>
			<style type="text/css">
				<?php
				foreach ($styles as $selector => $value ) {
					if(!empty($value)) {
						printf('%s', $selector ."{{$value}}");
					}
				}
				?>
			</style>
			<?php
		}
	}

	add_action( 'wp_head', 'basement_page_title_customize' );
}


if ( ! function_exists( 'basement_page_title_icon' ) ) {
	/**
	 * Set icon for Page Title
	 */
	function basement_page_title_icon() {
		$settings  = Basement_Page_Title();
		$icon      = isset( $settings['pt_elements']['icon'] ) ? $settings['pt_elements']['icon'] : '';
		$pt_icon   = isset( $settings['pt_icon'] ) ? $settings['pt_icon'] : '';
		$svg_file = '';
		$pt_icon_size  = isset( $settings['pt_icon_size'] ) ? $settings['pt_icon_size'] : '';
		$pt_icon_color = isset( $settings['pt_icon_color'] ) ? $settings['pt_icon_color'] : '';

		if ( ! empty( $icon ) && ! empty( $pt_icon ) ) {

			echo '';

		}
	}
}