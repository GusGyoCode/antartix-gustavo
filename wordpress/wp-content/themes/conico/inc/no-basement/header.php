<?php

if ( ! function_exists( 'basement_header_class' ) ) {
	/**
	 * Display the classes for the header element.
	 *
	 * @param string $class
	 * @param bool   $echo
	 *
	 * @return array
	 */
	function basement_header_class( $class = '', $echo = true ) {
		$sticky_option   = '';

		$settings        = Basement_Header();

		$settings_pt  = Basement_Page_Title();

		$bg             = ! empty( $settings['header_bg'] ) ? $settings['header_bg'] : '';
		$opacity        = is_numeric( $settings['header_opacity'] ) ? $settings['header_opacity'] : '';
		$padding_top    = is_numeric( $settings['header_padding_top'] ) ? $settings['header_padding_top'] : '';
		$padding_bottom = is_numeric( $settings['header_padding_bottom'] ) ? $settings['header_padding_bottom'] : '';

		$pt_placement = isset($settings_pt['pt_placement']) ? $settings_pt['pt_placement'] : '';
		$pt_off = isset($settings_pt['pt_off']) ? $settings_pt['pt_off'] : '';


		$id = get_the_ID();
		$custom_pagetitle = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );
		$custom_header = get_post_meta( $id, '_basement_meta_custom_header', true );

		if( ( is_singular('post') ) && empty($custom_pagetitle)) {
			$pt_placement = 'under';
		}


		if ( ! empty( $bg ) ) {
			$bg = basement_hexToRgb( $bg );
			$bg = ! empty( $bg ) ? $bg['red'] . ',' . $bg['green'] . ',' . $bg['blue'] : '';
		}

		#if ( ( $settings['header_sticky'] !== 'disable' ) || ( ! empty( $pt_placement ) && $pt_placement == 'under' && $pt_off == 'no' ) ) {
		if ( $settings['header_sticky'] !== 'disable' ) {
			$sticky_option = ' data-spy="affix" data-offset-top="10" ';
		}


		$style      = array();
		$style_attr = '';
		$data_attr  = '';
		if ( ! empty( $bg ) ) {
			$end_opacity = ',1';
			if($opacity !== '') {
				$end_opacity = ",{$opacity}";
			}
			$style[]   = "background:rgba({$bg}{$end_opacity});";
			$data_attr = " data-bgparams=\"{$bg}{$end_opacity}\" "; // this required param for JS scroll
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

		$blog_classes = implode( ' ', basement_get_header_class( $class ) );

		if($echo) {
			// Separates classes with a single space, collates classes for header element
			echo 'class="' . $blog_classes . '"' . $sticky_option . $style_attr . $data_attr;
		} else {
			return array(
				'class' => 	 basement_get_header_class( $class ),
				'sticky' => $sticky_option,
				'style' => $style_attr,
				'data' => $data_attr
			);
		}
	}
}

if ( ! function_exists( 'basement_navbar_class' ) ) {
	/**
	 * Display the classes for the navbar element.
	 *
	 * @param string $class
	 * @param bool   $echo
	 *
	 * @return array
	 */
	function basement_navbar_class( $class = '', $echo = true ) {

		$settings = Basement_Header();

		$classes = array();

		$header_size = !empty($settings['header_size']) ? $settings['header_size'] : '';

		if($header_size === 'fullwidth') {
			$classes[] = 'container-fluid';
		} else {
			$classes[] = 'container';
		}

		$classes[] = $class;
		$class = implode(' ', $classes);

		if($echo) {
			// Separates classes with a single space, collates classes for header element
			echo esc_attr($class);
		} else {
			return $classes;
		}

	}
}




if ( ! function_exists( 'front_get_settings_options' ) ) {
	function front_get_settings_options() {

		$settings = Basement_Header();

		return apply_filters( 'basement_header_settings', $settings );
	}
}

if ( ! function_exists( 'front_classes_header' ) ) {
	function front_classes_header() {
		$names   = apply_filters( 'basement_header_classes', front_get_settings_options() );
		$classes = array();
		foreach ( $names as $key => $value ) {
			if (
				$key === 'logo_text' ||
				$key === 'logo_image' ||
				$key === 'logo_text_size' ||
				$key === 'logo_text_color' ||
				$key === 'logo_link' ||
				$key === 'header_elements' ||
				$key === 'header_bg' ||
				$key === 'header_opacity' ||
				$key === 'header_border_bg' ||
				$key === 'header_border_opacity' ||
				$key === 'header_border_size' ||
				$key === 'header_padding_top' ||
				$key === 'header_padding_bottom' ||
				$key === 'header_btn_text' ||
				$key === 'header_btn_icon' ||
				$key === 'header_btn_link' ||
				$key === 'header_global_border' ||
				$key === 'header_global_border_size' ||
				$key === 'header_global_border_color'
			) {
				continue;
			}

			$classes[] = $key . '_' . sanitize_html_class( $value );
		}

		return implode( ' ', apply_filters( 'basement_header_classes_format', $classes ) );
	}
}

if ( ! function_exists( 'basement_get_header_class' ) ) {
	/**
	 * Retrieve the classes for the header element as an array.
	 *
	 * @param string $class
	 *
	 * @return array
	 */
	function basement_get_header_class( $class = '' ) {

		$classes = array();


		$classes_string = front_classes_header();

		$id = get_the_ID();
		$settings_pt  = Basement_Page_Title();


		$pt_placement = isset($settings_pt['pt_placement']) ? $settings_pt['pt_placement'] : '';
		$pt_off = isset($settings_pt['pt_off']) ? $settings_pt['pt_off'] : '';



		$custom_pagetitle = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );

		if( ( is_singular('post') ) && empty($custom_pagetitle)) {
			$pt_placement = 'under';
		}

		/*if ( ! empty( $pt_placement ) && $pt_placement === 'under' && $pt_off == 'no' ) {
			$classes_string = preg_replace( '/header_sticky_disable/', 'header_sticky_enable', $classes_string );
		}*/

		$classes[] = $classes_string;

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

		$classes = apply_filters( 'header_class', $classes, $class );

		return array_unique( $classes );
	}
}


if ( ! function_exists( 'basement_action_theme_after_nav' ) ) {
	/**
	 * Displays elements after Navigation
	 */
	function basement_action_theme_after_nav() {
		$settings  = Basement_Header();

		$bg = !empty($settings['header_border_bg']) ? $settings['header_border_bg'] : '';
		$opacity = is_numeric($settings['header_border_opacity']) ? $settings['header_border_opacity'] : '';
		$size = !empty($settings['header_border_size']) ? $settings['header_border_size'] : '';

		if(!empty($bg)) {
			$bg = basement_hexToRgb($bg);
			$bg = !empty($bg) ? $bg['red'] .','. $bg['green'] .','.$bg['blue'] : '';
		}

		$style = array();
		$style_attr = '';

		if ( ! empty( $bg ) ) {
			$end_opacity = ',1';
			if($opacity !== '') {
				$end_opacity = ",{$opacity}";
			}
			$style[] = "background:rgba({$bg}{$end_opacity});";
		}

		if(!empty($style)) {
			$style = implode(' ', $style);
			$style_attr = " style=\"{$style}\" ";
		}

		$class = '';
		if(!empty($size) && $size === 'boxed') {
			$class = 'class="container"';
		}

		echo "<div class=\"header-border-wrapper\"><div {$class}><div {$style_attr} class=\"header-border\"></div></div></div>";
	}

	add_action( 'conico_after_nav', 'basement_action_theme_after_nav' );
}


if ( ! function_exists( 'basement_hexToRgb' ) ) {
	function basement_hexToRgb( $color ) {

		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}


		if ( strlen( $color ) == 6 ) {
			list( $red, $green, $blue ) = array (
				$color[0] . $color[1],
				$color[2] . $color[3],
				$color[4] . $color[5]
			);
		} elseif ( strlen( $cvet ) == 3 ) {
			list( $red, $green, $blue ) = array (
				$color[0] . $color[0],
				$color[1] . $color[1],
				$color[2] . $color[2]
			);
		} else {
			return false;
		}

		$red = hexdec( $red );
		$green = hexdec( $green );
		$blue = hexdec( $blue );

		return array (
			'red'   => $red,
			'green' => $green,
			'blue'  => $blue
		);
	}
}


if ( ! function_exists( 'basement_header_sort_elements' ) ) {
	/**
	 * Output template parts elements in header
	 */
	function basement_header_sort_elements() {
		$settings = Basement_Header();

		$logo      = !empty($settings['header_elements']['logo_image']) ? $settings['header_elements']['logo_image'] : '';
		$logo_text = !empty($settings['header_elements']['logo_text']) ? $settings['header_elements']['logo_text'] : '';
		$menu      = !empty($settings['header_elements']['menu']) ? $settings['header_elements']['menu'] : 'default';
		$user      =  isset($settings['header_elements']['user_section']) ? $settings['header_elements']['user_section'] : '';
		$shop      = isset($settings['header_elements']['shop_section']) ? $settings['header_elements']['shop_section'] : '';
		$lang      = isset($settings['header_elements']['lang_section']) ? $settings['header_elements']['lang_section'] : '';
		$search    = !empty($settings['header_elements']['search_section']) ? $settings['header_elements']['search_section'] : '';
		$button    = isset($settings['header_elements']['button_section']) ? $settings['header_elements']['button_section'] : '';

		// menu on the left
		if ( $settings['logo_position'] === 'center_left' ) {
			echo '<div class="row">';
			echo '<div class="col-md-2 col-sm-12 no-padding text-center logo-block-center-left hidden visible-sm visible-xs">';
			if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
				get_template_part( 'template-parts/header/logo' );
			}
			echo '</div>';
			echo '<div class="col-md-5 col-sm-1 col-xs-2 clearfix head-col" style="position: static;">';
			if ( ! empty( $menu ) ) {
				get_template_part( 'template-parts/header/menu' );
			}
			echo '</div>';

			echo '<div class="col-md-2 col-sm-12 no-padding text-center logo-block-center-left hidden-sm hidden-xs">';
			if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
				get_template_part( 'template-parts/header/logo' );
			}
			echo '</div>';


			echo '<div class="col-md-5 col-sm-11 col-xs-10 head-col">';
			if ( ! empty( $lang ) ) {
				get_template_part( 'template-parts/header/lang' );
			}
			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search' );
			}
			if ( ! empty( $user ) ) {
				get_template_part( 'template-parts/header/account' );
			}
			if(!empty($button)) {
				get_template_part( 'template-parts/header/button' );
			}


			if ( ! empty( $shop ) ) {
				get_template_part( 'template-parts/header/shop' );
			}


			echo '</div>';

			echo '</div>';

			//menu on the right
		} elseif ( $settings['logo_position'] === 'center_right' ) {
			echo '<div class="row">';
			echo '<div class="col-md-2 col-md-push-5 col-sm-12 no-padding text-center">';
			if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
				get_template_part( 'template-parts/header/logo' );
			}
			echo '</div>';

			echo '<div class="col-md-5 col-md-pull-2 col-sm-11 col-xs-10 head-col">';
			if ( ! empty( $lang ) ) {
				get_template_part( 'template-parts/header/lang' );
			}
			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search' );
			}
			if ( ! empty( $user ) ) {
				get_template_part( 'template-parts/header/account' );
			}

			if(!empty($button)) {
				get_template_part( 'template-parts/header/button' );
			}

			if ( ! empty( $shop ) ) {
				get_template_part( 'template-parts/header/shop' );
			}


			echo '</div>';

			echo '<div class="col-md-5 col-sm-1 col-xs-2 head-col" style="position: static;">';
			if ( ! empty( $menu ) ) {
				get_template_part( 'template-parts/header/menu' );
			}
			echo '</div>';

			echo '</div>';

		} elseif ( $settings['logo_position'] === 'left' ) {

			if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
				get_template_part( 'template-parts/header/logo' );
			}

			if ( ! empty( $lang ) ) {
				get_template_part( 'template-parts/header/lang' );
			}

			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search' );
			}

			if ( ! empty( $user ) ) {
				get_template_part( 'template-parts/header/account' );
			}


			if(!empty($button)) {
				get_template_part( 'template-parts/header/button' );
			}



			if ( ! empty( $shop ) ) {
				get_template_part( 'template-parts/header/shop' );
			}

			if ( ! empty( $menu ) ) {
				echo '<div class="navbar-divider pull-right"></div>';
				get_template_part( 'template-parts/header/menu' );
			}

		} elseif ( $settings['logo_position'] === 'right' ) {



			if ( ! empty( $logo ) || ! empty( $logo_text ) ) {
				get_template_part( 'template-parts/header/logo' );
			}



			if ( ! empty( $lang ) ) {
				get_template_part( 'template-parts/header/lang' );
			}

			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search' );
			}

			if ( ! empty( $user ) ) {
				get_template_part( 'template-parts/header/account' );
			}
			if(!empty($button)) {
				get_template_part( 'template-parts/header/button' );
			}

			if ( ! empty( $shop ) ) {
				get_template_part( 'template-parts/header/shop' );
			}


			if ( ! empty( $menu ) ) {
				echo '<div class="navbar-divider pull-left"></div>';
				get_template_part( 'template-parts/header/menu' );
			}

		}
	}
	add_action( 'conico_content_header', 'basement_header_sort_elements', 10 );
}


if ( ! function_exists( 'basement_button' ) ) {
	/**
	 * Displays Button In Header
	 */
	function basement_button() {
		$settings = Basement_Header();

		$header_btn_text = ! empty( $settings['header_btn_text'] ) ? ' '.$settings['header_btn_text'] : '';
		$header_btn_icon = ! empty( $settings['header_btn_icon'] ) ? '<i class="'.esc_attr($settings['header_btn_icon']).'"></i>' : '';
		$header_btn_link = ! empty( $settings['header_btn_link'] ) ? $settings['header_btn_link'] : '#';

		if(!empty($header_btn_text) || !empty($header_btn_icon)) {
			?>
			<a href="<?php echo esc_url($header_btn_link); ?>" title="<?php echo esc_attr($header_btn_text); ?>"><?php  printf('%s',$header_btn_icon); echo esc_html($header_btn_text); ?></a>
			<?php
		}
	}
}


if ( ! function_exists( 'basement_logo' ) ) {
	/**
	 * Displays Logo (Text/Image)
	 */
	function basement_logo() {
		$settings        = Basement_Header();

		$brand_classes = array( 'navbar-brand' );

		if ( isset( $settings['logo_position'] ) ) {
			switch ( $settings['logo_position'] ) {
				case 'left' :
					$brand_classes[] = 'pull-left';
					break;
				case 'right' :
					$brand_classes[] = 'pull-right';
					break;
			}
		}

		$style_logo = '';

		$title_logo = apply_filters( 'header_title_logo', esc_attr( get_bloginfo( 'name', 'display' ) ) );
		$size       = empty( $settings['logo_text_size'] ) ? '' : 'font-size:' . abs( $settings['logo_text_size'] ) . 'px;';
		$color      = empty( $settings['logo_text_color'] ) ? '' : 'color:' . $settings['logo_text_color'] . ';';

		if ( ! empty( $size ) || ! empty( $color ) ) {
			$style_logo = 'style="' . $size . $color . '"';
		}

		$text   = empty( $settings['logo_text'] ) || empty( $settings['header_elements']['logo_text'] ) ? '' : esc_attr( $settings['logo_text'] );
		$image_src = wp_get_attachment_image_url( $settings['logo_image'], 'full' );
		$image  = empty( $settings['logo_image'] ) || empty( $settings['header_elements']['logo_image'] ) ? '' : '<img src="' . esc_url($image_src) . '" alt="' . esc_attr($title_logo) . '">';
		$link   = empty( $settings['logo_link'] ) ? esc_url( home_url( '/' ) ) : esc_url( $settings['logo_link'] );
		$toggle = !empty($settings['logo_link_toggle']) ? $settings['logo_link_toggle'] : '';


		$logo_slug = $image . ' ' . esc_html($text);

		if ( ! empty( $settings['logo_text'] ) || ! empty( $settings['logo_image'] ) ) {
			if ( $toggle === 'yes' ) {
				$logo = '<div class="' . esc_attr( implode( ' ', $brand_classes ) ) . '"><a ' . $style_logo . '  href="' . esc_url( $link ) . '" title="' . esc_attr( $title_logo ) . '">' . $logo_slug . '</a></div>';
			} else {
				$brand_classes[] = 'basement-disabled-brand';
				$logo = '<div class="' . esc_attr( implode( ' ', $brand_classes ) ) . '"><span' . $style_logo . ' >' .  $logo_slug . '</span></div>';
			}
		} else {
			$logo = '';
		}

		echo apply_filters( 'basement_header_logo', $logo );
	}
}


if ( ! function_exists( 'basement_action_theme_before_wrapper' ) ) {
	/**
	 * Displays params before Main Wrapper
	 */
	function basement_action_theme_before_wrapper() {

		$basement_header = Basement_Header();

		$header_off = isset($basement_header['header_off']) ? $basement_header['header_off'] : '';
		$search = ! empty( $basement_header['header_elements']['search_section'] ) ? $basement_header['header_elements']['search_section'] : '';

		if($header_off === 'no') {

			get_template_part( 'template-parts/header/menu-simple' );

			if ( ! empty( $search ) ) {
				get_template_part( 'template-parts/header/search-form' );
			}

		}
	}
	add_action('conico_before_wrapper', 'basement_action_theme_before_wrapper');
}



if ( ! function_exists( 'basement_action_header_body_class' ) ) {
	/**
	 * Added classes for Header
	 */
	function basement_action_header_body_class( $classes, $class ) {

		$basement_header          = Basement_Header();
		$menu_type                = isset( $basement_header['menu_type'] ) ? $basement_header['menu_type'] : '';
		$header_sticky            = isset( $basement_header['header_sticky'] ) ? $basement_header['header_sticky'] : '';

		if ( ! empty( $header_sticky ) ) {
			$classes[] = "basement-{$header_sticky}-sticky";
		}

		if ( ! empty( $menu_type ) ) {
			$classes[] = "basement-{$menu_type}-menu";
		}

		return $classes;
	}

	add_filter( 'body_class', 'basement_action_header_body_class', 10, 2 );
}











if ( ! function_exists( 'basement_action_theme_after_header' ) ) {
	/**
	 * Displays params after Header
	 */
	function basement_action_theme_after_header() {

		$basement_header = Basement_Header();
		$helper = '';
		$sticky = !empty($basement_header['header_sticky']) ? $basement_header['header_sticky'] : '';
		$header_off = isset($basement_header['header_off']) ? $basement_header['header_off'] : '';
		$header_helper = isset($basement_header['header_helper']) ? $basement_header['header_helper'] : '';
		$header_elements = isset($basement_header['header_elements']) ? $basement_header['header_elements'] : array();
		$header = ob_get_contents();
		if (ob_get_length() > 0 ) {
			ob_end_clean();
		}


		$id = get_the_ID();

		$settings_pt  = Basement_Page_Title();


		$pt_placement = isset($settings_pt['pt_placement']) ? $settings_pt['pt_placement'] : '';
		$pt_off = isset($settings_pt['pt_off']) ? $settings_pt['pt_off'] : '';

		$custom_pagetitle = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );

		$revslider_position = '';
		$shortcode = '';
		if ( is_page() || is_single() ) {

			$revslider_position = get_post_meta( $id, 'basement_rev_position', true );
			$revslider_position = ! empty( $revslider_position ) ? $revslider_position : '';

			$shortcode = get_post_meta( $id, 'revlider_content_meta', true );
			$shortcode = ! empty( $shortcode ) ? $shortcode : '';

			if(empty($shortcode)) {
				$revslider_position = '';
			}
		}


		if( ( $pt_placement === 'after' && $pt_off === 'no' && $revslider_position !== 'header_content' ) || ( $pt_off === 'yes' && $revslider_position === 'before_content' ) || $header_helper === 'yes' ) {
			$helper = apply_filters('basement_header_helper','<div class="header-helper"></div>');
		}

		if ( isset( $basement_header ) && ( $header_off == 'no' && array_filter( $header_elements ) ) ) {

			printf('%s', $header . $helper);
		}

	}
	add_action('conico_after_header', 'basement_action_theme_after_header');
}





