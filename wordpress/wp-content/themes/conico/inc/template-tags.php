<?php
/**
 * Custom template tags for Conico
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */


if ( ! function_exists( 'conico_head' ) ) {
	/**
	 * The wp_head action hook is triggered within the <head></head> section
	 *
	 * @since Conico 1.0
	 */
	function conico_head() {
		if ( has_site_icon() ) {
			return;
		}

		/*
		 * Set Favicon
		 */
		$favicon = get_option( 'conico_favicon' );
		if ( !$favicon ) {
			$favicon = get_template_directory_uri() .'/favicon.ico';
		} else {
			$favicon = wp_get_attachment_url( $favicon );
		}
		echo '<link rel="shortcut icon" type="image/x-icon" href="' . $favicon . '" />';
	}

	add_action( 'wp_head', 'conico_head');
}


if ( ! function_exists( 'conico_preloader' ) ) {
	/**
	 * Displays PreLoader For Template
	 *
	 * @since Conico 1.0
	 */
	function conico_preloader() {
		$conico_preloader = get_option( 'basement_framework_preloader_enable', 'disable' );
		$conico_preloader_bg = get_option( 'basement_framework_preloader_bg', '#ffffff' );
		$conico_preloader_color = get_option( 'basement_framework_preloader_color', '#1a1a1a' );

		if ( 'enable' === $conico_preloader ) {
			if(!empty($conico_preloader_bg)) {
				$conico_preloader_bg = 'style="background-color:'.$conico_preloader_bg.';"';
			}
			if(!empty($conico_preloader_color)) {
				$conico_preloader_color = 'style="border-bottom-color:'.$conico_preloader_color.';"';
			}
			?>
			<!-- Preloader -->
			<div class="preloader" <?php echo "{$conico_preloader_bg}"; ?>>
                <div class="loader">
                    <div class="triangle-skew-spin"><?php echo "<div {$conico_preloader_color}></div>"; ?></div>
                </div>
			</div>
		<?php }
	}
}


if ( ! function_exists( 'conico_paging_nav' ) ) {
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 *
	 * @since Conico 1.0
	 */
	function conico_paging_nav( $conico_query = null, $style = 'default' ) {
		global $wp_query, $wp_rewrite;

		if($conico_query) {
			$max_pages =  isset($conico_query->max_num_pages) ? $conico_query->max_num_pages : 0;
		} else {
			$max_pages =  $wp_query->max_num_pages;
		}


		$paginate = '';
		// Don't print empty markup if there's only one page.
		if ( $max_pages < 2 ) {
			return;
		}

		$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		// Set up paginated links.
		$links = paginate_links( array(
			'base'      => $pagenum_link,
			'format'    => $format,
			'total'     => $max_pages,
			'current'   => $paged,
			'mid_size'  => 1,
			'type'      => 'array',
			'add_args'  => array_map( 'urlencode', $query_args ),
			'prev_text' => __( '<i class="ais-b1l"></i>', 'conico' ),
			'next_text' => __( '<i class="ais-b1r"></i>', 'conico' ),
		) );

		if ( $links ) {
			if($style === 'default') {

				$cross_class = '';

				if($max_pages == $paged && $paged != 1 && $max_pages != 1) {
					$cross_class = 'is-last-page';
				} elseif($paged == 1 && $paged < $max_pages) {
					$cross_class = 'is-first-page';
				}

				?>
				<div class="text-center">
					<!-- PAGINATION -->
					<?php
						$paginate .= "<ul class=\"pagination simple-pagination {$cross_class}\" aria-label=\"Pagination\">\n\t<li>";
						$paginate .= implode( "</li>\n\t<li>", $links );
						$paginate .= "</li>\n</ul>\n";

						printf('%s', $paginate);
					?>
					<!-- /.pagination -->
				</div>
				<?php
			}
		}
	}
}


if ( ! function_exists( 'conico_language_switcher' ) ) {
	/**
	 * Display Custom WPML Language Switcher.
	 *
	 * @since Conico 1.0
	 */
	function conico_language_switcher( $params = array() ) {

		$basement_header = Basement_header();

		$lang_classes = array('navbar-lang');

		$logo_position = !empty( $basement_header['logo_position'] ) ? $basement_header['logo_position'] : '';
		$padding_bottom = isset($basement_header['header_padding_bottom']) && is_numeric($basement_header['header_padding_bottom']) ? $basement_header['header_padding_bottom'] : '27';

		if ( $logo_position ) {
			switch ($logo_position) {
				case 'left' :
				case 'center_left' :
					$lang_classes[] = 'pull-right';
					break;
				case 'right' :
				case 'center_right' :
					$lang_classes[] = 'pull-left';
					break;
			}
		}

		$wpml_languages = apply_filters( 'wpml_active_languages', null, array(
			'skip_missing'  => 0,
			'link_empty_to' => '/translate-missing',
			'orderby'       => 'id',
			'order'         => 'desc'
		) );

		
		if(defined('FAKE_WPML')) {
			$wpml_languages = array(
				'en' => array(
						'code'            => "en",
						'id'              => "1",
						'native_name'     => "English",
						'major'           => 1,
						'active'          => 1,
						'default_locale'  => "en_US",
						'encode_url'      => "0",
						'tag'             => "en",
						'missing'         => 0,
						'url' => '/translate-missing',
						'translated_name' => "English",
						'language_code'   => "en"
					),
				'de' => array(
						'code'            => "de",
						'id'              => "3",
						'native_name'     => "Deutsch",
						'major'           => 1,
						'active'          => 0,
						'default_locale'  => "de_DE",
						'encode_url'      => "0",
						'tag'             => "de",
						'missing'         => 1,
						'url' => '/translate-missing',
						'translated_name' => "German",
						'language_code'   => "de"
					),
				'fr' => array(
						'code'            => "fr",
						'id'              => "4",
						'native_name'     => "Francais",
						'major'           => 1,
						'active'          => 0,
						'default_locale'  => "fr_FR",
						'encode_url'      => "0",
						'tag'             => "de",
						'missing'         => 1,
						'url' => '/translate-missing',
						'translated_name' => "French",
						'language_code'   => "fr"
					)
			);
		}
		
		
		if ( ! empty( $wpml_languages ) ) { ?>
			<div class="<?php echo implode(' ', $lang_classes); ?>">
				<ul class="nav">
					<li>
						<?php
						if ( ! empty( $wpml_languages ) ) {
							foreach ( $wpml_languages as $key => $value ) {
								if ( $value['active'] == 1 ) {
									if(isset($value['language_code']) && !empty($value['language_code'])) {
										echo '<a href="#" title="' . esc_attr( ucfirst( $value['language_code'] ) ) . '"><span>' . esc_html( $value['language_code'] ) . '</span></a>';
										unset( $wpml_languages[ $key ] );
									}
									break;
								}
							}
						} ?>
						<?php if ( ! empty( $wpml_languages ) ) { ?>
							<div class="wpml-dropdown sf-mega" style="padding-top: <?php echo esc_attr($padding_bottom) . 'px;'; ?>">
								<ul>
									<?php foreach ( $wpml_languages as $language ) {
										$wpml_class = $language['active'] ? ' class="active"' : '';
										$wpml_code = isset($language['language_code']) && !empty($language['language_code']) ? $language['language_code'] : '';
										$wpml_url = isset($language['url']) && !empty($language['url']) ? $language['url'] : '';
										echo '<li' . $wpml_class . '><a href="' . esc_url( $wpml_url ) . '" title="' . esc_attr(ucfirst($wpml_code)) . '">' . esc_html($wpml_code) . '</a></li>';
									} ?>
								</ul>
							</div>
						<?php } ?>
					</li>
				</ul>
			</div>
			<?php
		}
	}
}


if ( ! function_exists( 'conico_entry_date' ) ) {
	/**
	 * Print HTML with date information for current post.
	 *
	 * Create your own conico_entry_date() to override in a child theme.
	 *
	 * @since Conico 1.0
	 *
	 * @param boolean $echo (optional) Whether to echo the date. Default true.
	 * @return string The HTML-formatted post date.
	 */
	function conico_entry_date($echo = true) {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date" datetime="%1$s">%2$s</time><time class="times-updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			get_the_date(),
			esc_attr( get_the_modified_date( 'c' ) ),
			get_the_modified_date()
		);

		$date = sprintf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
			_x( 'Posted on', 'Used before publish date.', 'conico' ),
			esc_url( get_permalink() ),
			$time_string
		);

		if ( $echo )
			printf('%s',$date);

		return $date;
	}
}


if ( ! function_exists( 'conico_comments' ) ) {
	/**
	 * Print HTML with number comments
	 *
	 * @since Conico 1.0
	 */
	function conico_comments() {
		$id = get_the_ID();

		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) { ?>
			<?php if ( is_singular('post') ) : ?>
				<a href="#respond" title="<?php _e( 'Add comment', 'conico' ) ?>"><?php echo get_comments_number( $id ); ?></a>
			<?php else :
				comments_popup_link( __('0','conico'), __('1','conico'), __('%','conico'), 'post-comment-link' );
			endif; ?>

		<?php } // comments_open()
	}
}


if ( ! function_exists( 'conico_post_nav' ) ) {
	/**
	 * Display navigation to next/previous post when applicable.
	 *
	 * @since Conico 1.0
	 */
	function conico_post_nav() {
		global $post;

		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}

		?>
		<div class="posts-navigation">

			<nav class="row posts-list-pagination">
				<div class="col-sm-5 clearfix">
					<?php if($previous) { ?>
					<div class="prev pull-left">
						<div class="inner-project-links"><span><?php _e('Previous Post','conico'); ?></span>
							<?php previous_post_link( '%link', _x( '%title', 'Previous', 'conico' ) ); ?>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="col-sm-2">
					<?php
						if ( 'page' === get_option( 'show_on_front' ) ) {
							if ( get_option( 'page_for_posts' ) ) {
								echo '<a href="'.esc_url( get_permalink( get_option( 'page_for_posts' ) ) ).'" class="all"><i class="ais-all-projects"></i></a>';
							} else {
								echo '<a href="'.esc_url( home_url( '/?post_type=post' ) ).'" class="all"><i class="ais-all-projects"></i></a>';
							}
						} else {
							echo '<a href="'.esc_url( home_url( '/' ) ).'" class="all"><i class="ais-all-projects"></i></a>';
						}
					?>
				</div>
				<div class="col-sm-5 clearfix">
					<?php if($next) { ?>
					<div class="next pull-right">
						<div class="inner-project-links"><span><?php _e('Next Post','conico'); ?></span>
							<?php next_post_link( '%link', _x( '%title', 'Next', 'conico' ) ); ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</nav>
		</div>
		<?php
	}
}





function conico_single_post_meta() {
	$meta_class = array('single-entry-meta row');
	$share_status = false;

	$tags = get_the_tags();

	$id = get_the_ID();
	$type = get_post_meta( $id, '_basement_meta_blog_sharing_type', true );

	if ( function_exists( 'basement_post_share' ) ) {
		$share_status = basement_post_share( false );
	}

	if ( empty( $tags ) && empty( $share_status ) ) {
		$meta_class[] = 'hide';
	}

	if(empty($share_status)) {
		$meta_class[] = 'no-tags';
    } else {
		$meta_class[] = 'is-tags';
    }


    if(!empty($meta_class)) {
	    $meta_class = 'class="' . implode(' ', $meta_class) . '"';
    } else {
	    $meta_class = '';
    }

    $tags_class = '';

	?>
    <div <?php printf('%s', $meta_class); ?>>
	    <?php if($share_status) {
		    $tags_class = ($type === 'dropdown') ? 'col-md-10 col-xs-12' : 'col-md-6 col-sm-12 col-xs-12';
        } else {
		    $tags_class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
        } ?>
        <div class="<?php echo esc_attr($tags_class); ?>">
			<?php the_tags( '<div class="tags clearfix"><span class="word-separator">'.__('Post tags', 'conico').'</span><div class="tags-list">', '', '</div></div>' ); ?>
        </div>
        <?php if($share_status) { ?>
            <div class="<?php echo ($type === 'dropdown') ? 'col-md-2 col-xs-12' : 'col-md-6 col-sm-12 col-xs-12'; ?>">
                <?php
                    /**
                     * Displays Share Block For Single Post
                     *
                     * @package    Aisconverse
                     * @subpackage Conico
                     * @since      Conico 1.0
                     */
                    basement_post_share();
                ?>
            </div>
        <?php } ?>
    </div>

<?php
}