<?php

/*
 * Custom Walker For Header Menu
 */
require_once( trailingslashit( get_template_directory() ) . 'inc/menu-walker.php' );

/*
 * Custom Walker For Simple Menu
 */
require_once( trailingslashit( get_template_directory() ) . 'inc/simple-menu-walker.php' );

/*
 * Add custom Walker For List Comments
 */
require_once( trailingslashit( get_template_directory() ) . 'inc/list-comments-walker.php' );


/*
 * Remove CF7 CSS styles
 */
add_filter( 'wpcf7_load_css', '__return_false' );


if ( ! function_exists( 'conico_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * Create your own conico_setup() function to override in a child theme.
	 *
	 * @since Conico 1.0
	 */
	function conico_setup() {

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on 'conico', use a find and replace
		 * to change 'conico' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'conico', get_template_directory() . '/languages' );

		/*
		 * Add default posts and comments RSS feed links to head.
		 */
		add_theme_support( 'automatic-feed-links' );


		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );


		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );


		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption'
		) );


		/*
		* This theme uses its own gallery styles.
		*/
		add_filter( 'use_default_gallery_style', '__return_false' );


		// Indicate widget sidebars can use selective refresh in the Customizer.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		 *  Registers navigation menu locations for a theme.
		 */
		register_nav_menus( array(
			'header' => __( 'Header Menu', 'conico' )
		) );


		/*
		 * Disable admin bar callback.
		 */
		add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );


		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', array(
			'aside',
			'gallery',
			'link',
			'image',
			'quote',
			'status',
			'video',
			'audio',
			'chat'
		) );


		/*
		 * Setup the WordPress core custom background feature.
		 */
		add_theme_support( 'custom-background', apply_filters( 'conico_custom_background_args', array(
			'default-color'      => 'ffffff',
			'default-attachment' => 'fixed',
		) ) );


		/*
		 * This theme styles the visual editor to resemble the theme style,
		 * specifically font, colors, icons, and column width.
		*/
		add_editor_style( array(
			'assets/css/editor-style.css',
			'assets/css/theme-icons.min.css',
			conico_google_fonts( array(
				'Chivo' => '300,300i,400,400i,700,700i,900,900i'
			),
			'latin-ext' )
		) );

	}

	add_action( 'after_setup_theme', 'conico_setup' );
}


if ( ! function_exists( 'conico_mce_stop_cache' ) ) {
	/**
	 * Disable cache MCE editor styles
	 *
	 * @since Conico 1.0
	 */
	function conico_mce_stop_cache( $mce_init ) {

		$mce_init['cache_suffix'] = "v=" . time();

		return $mce_init;
	}

	add_filter( 'tiny_mce_before_init', 'conico_mce_stop_cache' );
}


if ( ! function_exists( 'conico_google_fonts' ) ) {
	/**
	 * Register Google fonts for Conico
	 *
	 * Create your own conico_google_fonts() function to override in a child theme.
	 *
	 * @since Conico 1.0
	 */
	function conico_google_fonts( $fonts, $subsets = array() ) {

		/* URL */
		$base_url  = "https://fonts.googleapis.com/css";
		$font_args = array();
		$family    = array();

		/* Format Each Font Family in Array */
		foreach ( $fonts as $font_name => $font_weight ) {
			$font_name = str_replace( ' ', '+', $font_name );
			if ( ! empty( $font_weight ) ) {
				if ( is_array( $font_weight ) ) {
					$font_weight = implode( ",", $font_weight );
				}
				$family[] = trim( $font_name . ':' . urlencode( trim( $font_weight ) ) );
			} else {
				$family[] = trim( $font_name );
			}
		}

		/* Only return URL if font family defined. */
		if ( ! empty( $family ) ) {

			/* Make Font Family a String */
			$family = implode( "%7C", $family );

			/* Add font family in args */
			$font_args['family'] = $family;

			/* Add font subsets in args */
			if ( ! empty( $subsets ) ) {

				/* format subsets to string */
				if ( is_array( $subsets ) ) {
					$subsets = implode( ',', $subsets );
				}

				$font_args['subset'] = urlencode( trim( $subsets ) );
			}

			return add_query_arg( $font_args, $base_url );
		}

		return '';
	}
}


if ( ! function_exists( 'conico_title' ) ) {
	/**
	 * Change 404 page title
	 *
	 * @since Conico 1.0
	 */
	function conico_title( $title ) {
		if ( is_404() ) {
			$title = get_option( 'conico_404_page_window_title', __( 'Page Not Found', 'conico' ) ) . ' | ' . get_bloginfo( 'name' );
		}

		return $title;
	}

	add_filter( 'pre_get_document_title', 'conico_title', 10, 1 );
}


if ( ! function_exists( 'conico_excerpt_more' ) ) {
	/**
	 * Change excerpt dots.
	 *
	 * @since Conico 1.0
	 */
	function conico_excerpt_more( $more ) {
		return '...';
	}

	add_filter( 'excerpt_more', 'conico_excerpt_more' );
}


if ( ! function_exists( 'conico_excerpt_length' ) ) {
	/**
	 * New Excerpt Length
	 *
	 * @since Conico 1.0
	 */
	function conico_excerpt_length( $length ) {
		return 20;
	}

	add_filter( 'excerpt_length', 'conico_excerpt_length' );
}


if ( ! function_exists( 'conico_empty_content' ) ) {
	/**
	 * Check If string is empty
	 *
	 * @since Conico 1.0
	 */
	function conico_empty_content( $str ) {
		return trim( str_replace( '&nbsp;', '', strip_tags( $str ) ) ) === '';
	}
}


if ( ! function_exists( 'conico_empty_search_posts' ) ) {
	/**
	 * Find empty posts in search query
	 *
	 * @since Conico 1.0
	 */
	function conico_empty_search_posts() {
		global $query_string;

		$empty = array();

		$query_args   = explode( "&", $query_string );
		$search_query = array(
			'posts_per_page' => - 1,
			'post_status' => 'publish'
		);

		if ( strlen( $query_string ) > 0 ) {
			foreach ( $query_args as $key => $string ) {
				$query_split                     = explode( "=", $string );
				$search_query[ $query_split[0] ] = urldecode( $query_split[1] );
			}
		}

		$search = new WP_Query( $search_query );

		if ( $search->have_posts() ) {
			// Start the Loop.
			while ( $search->have_posts() ) {
				$search->the_post();
				$excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );
				$text = preg_replace(array('/#vc-ai-(.+)\}/','/&#8230;\./','/&#8230;/','/\.{3,}/'), array('','',' ',' '),$excerpt);

				$id  = get_the_ID();
				if ( conico_empty_content( $text ) ) {
					$empty[] = $id;
				}
			}

			wp_reset_query();
		}

		return $empty;
	}
}

if ( ! function_exists( 'conico_header_classes' ) ) {
	/**
	 * Filtrate specific classes for Header
	 *
	 * @since Conico 1.0
	 */
	function conico_header_classes( $classes ) {
		foreach ( $classes as $key => $class ) {
			switch ( $class ) {
				case 'header_style_white' :
					$classes[ $key ] = 'header-light';
					break;
			}
		}

		return $classes;
	}

	add_filter( 'basement_header_classes_format', 'conico_header_classes', 10 );
}


if ( ! function_exists( 'conico_page_title_classes' ) ) {
	/**
	 * Filtrate specific classes for Page Title
	 *
	 * @since Conico 1.0
	 */
	function conico_page_title_classes( $classes ) {
		foreach ( $classes as $key => $class ) {
			switch ( $class ) {
				case 'page-title_style_dark' :
					$classes[ $key ] = 'pagetitle-dark pagetitle-inverse';
					break;
			}
		}

		return $classes;
	}

	add_filter( 'basement_pagetitle_classes_format', 'conico_page_title_classes', 10 );
}


if ( ! function_exists( 'conico_widgets' ) ) {
	/**
	 * All logic of widgets/sidebar.
	 *
	 * @since Conico 1.0
	 */
	function conico_widgets() {

		// Registering sidebars
		for ( $i = 1; $i <= 40; $i ++ ) {
			register_sidebar( array(
				'name'          => sprintf( __( 'Widget Area %s', 'conico' ), $i ),
				'id'            => 'sidebar-' . $i,
				'description'   => __( 'The area where widgets are displayed.', 'conico' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-body-inner clearfix">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			) );
		}

		// Removing widgets
		unregister_widget( 'bcn_widget' ); // Breadcrumb NavXT
		unregister_widget( 'icl_lang_sel_widget' ); // WPML Switcher (old versions)
		unregister_widget( 'ICL_Language_Switcher' ); // WPML Switcher (old versions)
		unregister_widget( 'WPML_LS_Widget' ); // WPML Switcher (after v.3.6.2)

	}

	add_action( 'widgets_init', 'conico_widgets' );
}


if ( ! function_exists( 'conico_widget_title' ) ) {
	/**
	 * Generate Specific Title For Widgets
	 *
	 * @since Conico 1.0
	 */
	function conico_widget_title( $title, $widget, $id ) {

		if ( empty( $title ) ) {
			switch ( $id ) {
				case 'media_image' :
					$title = __( 'Media Image', 'conico' );
					break;
				case 'media_video' :
					$title = __( 'Media Video', 'conico' );
					break;
				case 'media_audio' :
					$title = __( 'Media Audio', 'conico' );
					break;
				case 'search' :
					$title = __( 'Search', 'conico' );
					break;
				case 'calendar' :
					$title = __( 'Calendar', 'conico' );
					break;
				case 'nav_menu' :
					$title = __( 'Custom Menu', 'conico' );
					break;
				case 'text' :
					$title = __( 'Text', 'conico' );
					break;
				case 'woocommerce_rating_filter' :
					$title = __( 'Average Rating Filter', 'conico' );
					break;
				case 'woocommerce_widget_cart' :
					$title = __( 'Cart', 'conico' );
					break;
				case 'woocommerce_layered_nav' :
					$title = __( 'Filter by', 'conico' );
					break;
				case 'woocommerce_price_filter' :
					$title = __( 'Filter by price', 'conico' );
					break;
				case 'woocommerce_product_categories' :
					$title = __( 'Product Categories', 'conico' );
					break;
				case 'woocommerce_products' :
					$title = __( 'Products', 'conico' );
					break;
				case 'woocommerce_product_search' :
					$title = __( 'Product Search', 'conico' );
					break;
				case 'woocommerce_recently_viewed_products' :
					$title = __( 'Recently Viewed Products', 'conico' );
					break;
				case 'woocommerce_recent_reviews' :
					$title = __( 'Recent Reviews', 'conico' );
					break;
				case 'woocommerce_top_rated_products' :
					$title = __( 'Top Rated Products', 'conico' );
					break;
			}
		}

		return $title;
	}

	add_filter( 'widget_title', 'conico_widget_title', 10, 3 );
}


if ( ! function_exists( 'conico_edit_link' ) ) {
	/**
	 * Change edit post link for post.
	 *
	 * @since Conico 1.0
	 */
	function conico_edit_link( $link, $id, $text ) {

		if ( conico_vc_enabled() ) {
			$vc = new Vc_Frontend_Editor();
			if ( $vc->showButton( get_the_ID() ) && ( !is_attachment() && !wp_attachment_is_image() ) ) {
				$link = str_replace('id="vc_load-inline-editor"','', $link);
				$link = preg_replace('/action=edit">(.*?)<\/a>/im','action=edit">$1</a> |', $link);
			}
		}

		return '<i class="fa fa-pencil"></i> ' . $link;
	}

	add_filter( 'edit_post_link', 'conico_edit_link', 20, 3 );
}


if ( ! function_exists( 'conico_shortcode_filter' ) ) {
	/**
	 * Cleans shortcodes from unnecessary tags.
	 *
	 * @since Conico 1.0
	 *
	 * @return string content.
	 */
	function conico_shortcode_filter( $content ) {

		/*
		 * Array of custom shortcodes requiring the fix
		 */
		$block = implode( "|", array(
			"resetlist",
			"dl",
			"dt",
			"dd",
			"blockquote",
			"footer",
			"cite",
			"table_responsive"
		) );

		/*
		 * Opening tag
		 */
		$rep = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );

		/*
		 * Closing tag
		 */
		$rep = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep );


		if ( false !== strpos( $content, '<table' ) ) {
			$rep = preg_replace('~<table\K(?:[^>]*?\K(\s?)class="([^"]*)")?~', ' class="$2$1table"', $rep);
		}


		return $rep;
	}

	add_filter( 'the_content', 'conico_shortcode_filter' );
}


if ( ! function_exists( 'conico_add_mce_media_button' ) ) {
	/**
	 * Add buttons to MCE Editor (1 row)
	 *
	 * @since Conico 1.0
	 */
	function conico_add_mce_media_button( $buttons, $id ) {
		$buttons[] = 'media';

		/* only add this for content editor */
		if ( 'content' !== $id ) {
			return $buttons;
		}

		/* add next page after more tag button */
		array_splice( $buttons, 13, 0, 'wp_page' );

		return $buttons;
	}

	add_filter( 'mce_buttons', 'conico_add_mce_media_button', 1, 2 );
}


if ( ! function_exists( 'conico_add_mce_typography_buttons' ) ) {
	/**
	 * Add buttons to MCE Editor (2 row)
	 *
	 * @since Conico 1.0
	 */
	function conico_add_mce_typography_buttons( $buttons ) {
		$buttons[] = 'fontselect';
		$buttons[] = 'fontsizeselect';
		$buttons[] = 'cleanup';
		$buttons[] = 'styleselect';

		return $buttons;
	}

	add_filter( 'mce_buttons_2', 'conico_add_mce_typography_buttons' );
}


if ( ! function_exists( 'conico_sort_mce_buttons' ) ) {
	/**
	 * Sorting buttons at MCE Editor
	 *
	 * @since Conico 1.0
	 */
	function conico_sort_mce_buttons( $buttons ) {
		array_splice( $buttons, 3, 0, array( 'superscript', 'subscript' ) );

		return $buttons;
	}

	add_filter( 'mce_buttons', 'conico_sort_mce_buttons' );
}


if ( ! function_exists( 'conico_customize_mce_buttons' ) ) {
	/**
	 * Customize buttons at MCE Editor
	 *
	 * @since Conico 1.0
	 */
	function conico_customize_mce_buttons( $config ) {
		$config['fontsize_formats'] = '1px 2px 3px 4px 5px 6px 7px 8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 26px 27px 28px 29px 30px 31px 32px 33px 34px 35px 36px 37px 38px 39px 40px 41px 42px 43px 44px 45px 46px 47px 48px 49px 50px 51px 52px 53px 54px 55px 56px 57px 58px 59px 60px 61px 62px 63px 64px 65px 66px 67px 68px 69px 70px 71px 72px 73px 74px 75px 76px 77px 78px 79px 80px 81px 82px 83px 84px 85px 86px 87px 88px 89px 90px 91px 92px 93px 94px 95px 96px 97px 98px 99px 100px 101px 102px 103px 104px 105px 106px 107px 108px 109px 110px 111px 112px 113px 114px 115px 116px 117px 118px 119px 120px';

		$new_styles = array(
			array(
				'title' => __( 'Type paragraph', 'conico' ),
				'block' => 'inline',
				'items' => array(
					array(
						'title'    => __( 'Extra paragraph', 'conico' ),
						'selector' => 'p',
						'classes'  => 'extra',
					),
					array(
						'title'    => __( 'Lead paragraph', 'conico' ),
						'selector' => 'p',
						'classes'  => 'lead'
					)
				)
			),
			array(
				'title'  => __( 'Small text', 'conico' ),
				'inline' => 'small'
			)
		);

		$config['style_formats_merge'] = true;
		$config['style_formats']       = json_encode( $new_styles );

		return $config;
	}

	add_filter( 'tiny_mce_before_init', 'conico_customize_mce_buttons' );
}


if ( ! function_exists( 'conico_modify_audio_shortcode' ) ) {
	/**
	 * Modified audio shortcode
	 *
	 * @since Conico 1.0
	 */
	function conico_modify_audio_shortcode( $html, $atts, $audio, $post_id, $library ) {
		return '<div class="audio-wrap">' . $html . '</div>';

	}

	add_filter( 'wp_audio_shortcode', 'conico_modify_audio_shortcode', 10, 5 );
}


if ( ! function_exists( 'conico_disable_script' ) ) {
	/**
	 * Disable script
	 *
	 * @since Conico 1.0
	 */
	function conico_disable_script( $script ) {
		_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

		wp_scripts()->remove( $script );
	}
}


if ( ! function_exists( 'conico_disable_style' ) ) {
	/**
	 * Disable style
	 *
	 * @since Conico 1.0
	 */
	function conico_disable_style( $handle ) {
		_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

		wp_styles()->remove( $handle );
	}
}


if ( ! function_exists( 'conico_modify_before_field_comment' ) ) {
	/**
	 * Start wrapper for comment form
	 *
	 * @since Conico 1.0
	 */
	function conico_modify_before_field_comment() {
		echo '<div class="form-wrap">';
	}

	add_action( 'comment_form_before_fields', 'conico_modify_before_field_comment' );
}


if ( ! function_exists( 'conico_move_comment_field' ) ) {
	/**
	 * Move Comment Field to Bottom
	 *
	 * @since Conico 1.0
	 */
	function conico_move_comment_field( $fields ) {
		$comment_field = $fields['comment'];
		unset( $fields['comment'] );
		$fields['comment'] = $comment_field;

		return $fields;
	}

	add_filter( 'comment_form_fields', 'conico_move_comment_field' );
}


if ( isset( $content_width ) ) {
	unset( $content_width );
}


if ( ! function_exists( 'conico_content_more_link' ) ) {
	/**
	 * Change HTML for read more post link
	 *
	 * @since Conico 1.0
	 */
	function conico_content_more_link( $link ) {
		return '';
	}

	add_filter( 'the_content_more_link', 'conico_content_more_link' );
}



if ( ! function_exists( 'conico_get_calendar' ) ) {
	/**
	 * Change Calendar id's for good validation
	 *
	 * @since Conico 1.0
	 */
	function conico_get_calendar( $calendar_output ) {

		$id = uniqid();

		$calendar_output = preg_replace( array('/id="prev"/','/id="next"/','/id="today"/','/id="wp-calendar"/'), array("id=\"prev-{$id}\"","id=\"next-{$id}\"","id=\"today-{$id}\"","id=\"wp-calendar-{$id}\""), $calendar_output);

		$calendar_output = preg_replace_callback('/<caption>(.*?)<\/caption>/',function($matches){
			$value = isset($matches['0']) ? $matches['0'] : '';

			if(!empty($value)) {
				$value = implode(", ", preg_split("/[\s]+/", $value));
			}

			return $value;
		}, $calendar_output);

		return $calendar_output;
	}

	add_filter( 'get_calendar', 'conico_get_calendar' );
}


if ( ! function_exists( 'jug_change_archive_title' ) ) {
	/**
	 * Sets Custom Title For Archives
	 *
	 * @since Conico 1.0
	 */
	function jug_change_archive_title( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_year() ) {
			$title = sprintf( __( '%s', 'conico' ), get_the_date( _x( 'Y', 'yearly archives date format', 'conico' ) ) );
		} elseif ( is_month() ) {
			$title = sprintf( __( '%s', 'conico' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'conico' ) ) );
		} elseif ( is_day() ) {
			$title = sprintf( __( '%s', 'conico' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'conico' ) ) );
		}

		return $title;
	}

	add_filter( 'get_the_archive_title', 'jug_change_archive_title' );
}

if ( ! function_exists( 'conico_header_settings' ) ) {
	/**
	 * Sets Custom Settings For Header
	 *
	 * @since Conico 1.0
	 */
	function conico_header_settings( $settings ) {

		$id = get_the_ID();
		$custom_header = get_post_meta( $id, '_basement_meta_custom_header', true );

		if ( is_home() || is_archive() || is_search() || is_attachment() ) {
			$settings['menu_type'] = 'default';
			$settings['header_sticky'] = 'enable';
			$settings['logo_position'] = 'left';
			$settings['header_off'] = 'no';
			$settings['header_elements'] = array(
				'logo_image'     => '',
				'logo_text'      => 'logo_text',
				'menu'           => 'menu',
				'search_section' => 'search_section',
				'button_section' => 'button_section',
				'user_section'   => '',
				'lang_section'   => 'lang_section'
			);
			$settings['header_style'] = 'white';
			$settings['header_bg'] = '';
			$settings['header_size'] = 'fullwidth';
			$settings['header_helper'] = 'no';
			$settings['header_opacity'] = '';
			$settings['header_border_bg'] = '';
			$settings['header_border_opacity'] = '';
			$settings['header_border_size'] = 'fullwidth';
			$settings['header_padding_top'] = '';
			$settings['header_padding_bottom'] = '';
			$settings['header_global_border'] = 'no';
		} elseif(is_singular('post')) {
			if ( empty( $custom_header ) ) {
				$settings['header_sticky'] = 'enable';
				$settings['menu_type'] = 'default';
				$settings['logo_position'] = 'left';
				$settings['header_off'] = 'no';
				$settings['header_elements'] = array(
					'logo_image'     => '',
					'logo_text'      => 'logo_text',
					'menu'           => 'menu',
					'search_section' => 'search_section',
					'button_section' => 'button_section',
					'user_section'   => '',
					'lang_section'   => 'lang_section'
				);
				$settings['header_style'] = 'dark';
				$settings['header_bg'] = '';
				$settings['header_size'] = 'fullwidth';
				$settings['header_helper'] = 'no';
				$settings['header_opacity'] = '';
				$settings['header_border_bg'] = '';
				$settings['header_border_opacity'] = '';
				$settings['header_padding_top'] = '';
				$settings['header_padding_bottom'] = '';
				$settings['header_border_size'] = 'fullwidth';
				$settings['header_global_border'] = 'no';
			}

		}

		return $settings;
	}

	add_filter( 'basement_header_settings', 'conico_header_settings' );
}


if ( ! function_exists( 'conico_pagetitle_settings' ) ) {
	/**
	 * Custom Page Title Settings For Pages
	 *
	 * @since Conico 1.0
	 */
	function conico_pagetitle_settings( $settings ) {

		$id = get_the_ID();
		$custom_page_title = get_post_meta( $id, '_basement_meta_custom_pagetitle', true );

		if ( is_home() || is_archive() || is_search() || is_attachment() ) {
			$settings['pt_placement'] = 'under';
			$settings['pt_style'] = 'white';
			$settings['pt_elements'] = array(
				'icon' => '',
				'title' => 'title',
				'line' => 'line',
				'breadcrumbs'=> 'breadcrumbs',
				'breadcrumbs_last' => ''
			);
			$settings['pt_icon'] = '';
			$settings['pt_icon_size'] = '';
			$settings['pt_icon_color'] = '';
			$settings['pt_bg'] = '';
			$settings['pt_bg_color'] = '';
			$settings['pt_bg_opacity'] = '';
			$settings['pt_position'] = 'center_right';
			$settings['pt_title_size'] = '';
			$settings['pt_title_color'] = '';
			$settings['pt_padding_top'] = '';
			$settings['pt_padding_bottom'] = '';
			$settings['pt_float_enable'] = 'no';
			$settings['pt_float_text_color'] = '';
			$settings['pt_float_text_size'] = '';
			$settings['pt_off'] = 'no';
		} elseif(is_singular('post')) {
			$bg = '';
			if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment() )  {
				$bg = get_the_post_thumbnail_url($id, 'full');
			}

			$settings['pt_elements'] = array(
				'icon' => '',
				'title' => 'title',
				'line' => 'line',
				'breadcrumbs'=> 'breadcrumbs',
				'breadcrumbs_last' => ''
			);
			$settings['pt_icon'] = '';
			$settings['pt_icon_size'] = '';
			$settings['pt_icon_color'] = '';
			$settings['pt_bg'] = $bg;
			$settings['pt_position'] = 'center_right';
			$settings['pt_title_size'] = '';
			$settings['pt_title_color'] = '';
			$settings['pt_padding_top'] = '';
			$settings['pt_padding_bottom'] = '';
			$settings['pt_off'] = 'no';


			if ( empty( $custom_page_title ) ) {
				$settings['pt_placement'] = 'under';
				$settings['pt_style'] = 'dark';
				$settings['pt_bg_color'] = '#141414';
				$settings['pt_bg_opacity'] = '0.7';
			}

		}

		return $settings;
	}
	add_filter( 'basement_pagetitle_settings', 'conico_pagetitle_settings' );
}


if ( ! function_exists( 'conico_templates_blog_archive' ) ) {
	/**
	 * Templates Classes For Meta Pages
	 *
	 * @since Conico 1.0
	 */
	function conico_templates_blog_archive( $templates ) {

		$templates['classic is-standard'] = __('Classic Standard','conico');
		$templates['classic'] = __('Classic Boxed','conico');
		$templates['classic is-fullwidth'] = __('Classic Fullwidth','conico');
		$templates['is-creative'] = __('Creative Boxed','conico');
		$templates['is-creative is-fullwidth'] = __('Creative Fullwidth','conico');

		return $templates;
	}

	add_filter( 'basement_templates_blog_archive', 'conico_templates_blog_archive' );
}



if ( ! function_exists( 'conico_action_start_blog_grid' ) ) {
	/**
	 * Tag Before Meta Posts Grid
	 *
	 * @since Conico 1.0
	 */
	function conico_action_start_blog_grid() {
		$grid = get_option('basement_framework_blog_archive','classic is-standard');

		if($grid === 'is-creative' || $grid === 'is-creative is-fullwidth') {
			$grid .= ' classic ';
		}

		printf( '<div class="row blog-posts-grid %s">', esc_attr( $grid ) );
	}

	add_action( 'conico_start_blog_grid', 'conico_action_start_blog_grid' );
}


if ( ! function_exists( 'conico_action_end_blog_grid' ) ) {
	/**
	 * Tag After Meta Posts Grid
	 *
	 * @since Conico 1.0
	 */
	function conico_action_end_blog_grid() {
		printf('%s','</div>');
	}

	add_action( 'conico_end_blog_grid', 'conico_action_end_blog_grid' );
}


if ( ! function_exists( 'conico_action_start_blog_container' ) ) {
	/**
	 * Start Container Tag For Meta Grid Pages
	 *
	 * @since Conico 1.0
	 */
	function conico_action_start_blog_container() {
		$grid = get_option('basement_framework_blog_archive','classic is-standard');

		$container = 'container';
		if ( strpos( $grid, 'fullwidth' ) !== false ) {
			$container = 'container-fluid';
		}
		printf( '<div class="%s">', esc_attr( $container ) );
	}

	add_action( 'conico_start_blog_container', 'conico_action_start_blog_container' );
}


if ( ! function_exists( 'conico_action_end_blog_container' ) ) {
	/**
	 * Closed Container Tag For Meta Grid Pages* Tag After Meta Posts Grid
	 *
	 * @since Conico 1.0
	 */
	function conico_action_end_blog_container() {
		printf('%s','</div>');
	}

	add_action( 'conico_end_blog_container', 'conico_action_end_blog_container' );
}

if ( ! function_exists( 'conico_avatar_comment_types' ) ) {
	/**
	 * Added avatars to pingback comments
	 *
	 * @since Conico 1.0
	 */
	function conico_avatar_comment_types( $type ) {
		$type[] = 'pingback';

		return $type;
	}

	add_filter( 'get_avatar_comment_types', 'conico_avatar_comment_types' );
}