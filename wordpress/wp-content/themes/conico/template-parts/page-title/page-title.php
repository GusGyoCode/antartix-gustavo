<?php
/**
 * The template part for displaying the elements in page title
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */


if ( function_exists( 'Basement_Page_Title' ) ) {

	$basement_page_title = Basement_Page_Title();

	$title       = isset( $basement_page_title['pt_elements']['title'] ) ? $basement_page_title['pt_elements']['title'] : '';
	$breadcrumbs = isset( $basement_page_title['pt_elements']['breadcrumbs'] ) ? $basement_page_title['pt_elements']['breadcrumbs'] : '';
	$icon        = isset( $basement_page_title['pt_elements']['icon'] ) ? $basement_page_title['pt_elements']['icon'] : '';
	$alternate   = isset( $basement_page_title['pt_alternate'] ) ? $basement_page_title['pt_alternate'] : '';
	$position    = isset( $basement_page_title['pt_position'] ) ? $basement_page_title['pt_position'] : '';
	$template_name = get_page_template_slug( get_queried_object_id() );

	$title_meta_page = '';
	if ( is_category() ) {
		$title_meta_page = __('Category posts','conico');
	}  elseif ( is_tag() ) {
		$title_meta_page = __('Tag posts','conico');
	} elseif (is_day() || is_month() || is_year()) {
		$title_meta_page = basement_the_specific_title( 'archives', __( 'Archives', 'conico' ), false );
	} elseif (is_author()) {
		$title_meta_page = __('Author posts','conico');
	}

	?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-sm-offset-0 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                <div class="page-title-content">
                    <?php

                    if ( ! is_search() ) {

                        if(is_archive() ) {
	                        if ( is_author() ) {
		                        $author_id = get_the_author_meta( 'ID' );
		                        $size      = 95;
		                        if ( ! empty( $author_id ) ) {
			                        $author_avatar = get_avatar( $author_id, $size );
			                        echo '<div class="main-page-title-icon is-img">' . $author_avatar . '</div>';
		                        }
	                        }

	                        if ( ! empty( $title ) ) {
		                        echo sprintf('<h1 class="main-page-title"><span>%s</span></h1>', get_the_archive_title() ) ;
	                        }

	                        if ( ! empty( $breadcrumbs ) ) {
		                        ?>
                                    <div class="breadcrumb-block">
                                        <ol class="breadcrumb">
                                            <li>
                                    <span>
                                        <span><?php echo esc_html( $title_meta_page ); ?></span>
                                    </span>
                                            </li>
                                        </ol>
                                </div>
		                        <?php
	                        }
                        } elseif(is_singular('post')) {
	                        if ( ! empty( $breadcrumbs ) ) {
		                        ?>
                                <ol class="breadcrumb text-uppercase">
                                    <li>
								<span>
									<?php the_category(', '); ?>
								</span>
                                    </li>
                                </ol>
		                        <?php
	                        }
	                        if ( ! empty( $title ) ) {
		                        if ( empty( $alternate ) ) {
			                        get_template_part( 'template-parts/page-title/title' );
		                        } else {
			                        get_template_part( 'template-parts/page-title/title-alternative' );
		                        }
	                        }

	                        $author_id   = get_post_field( 'post_author' );
	                        $author      = get_the_author_meta( 'display_name', $author_id );
	                        $author_link = get_author_posts_url( $author_id );
	                        ?>
                            <div class="page-title-meta text-uppercase">
		                        <?php conico_entry_date(); ?><span class="separator-meta"><?php _e( '/', 'conico' ); ?></span><?php echo '<a href="' . $author_link . '" class="auth-link" title="' . $author . '">' . $author . '</a>'; ?>
                            </div>
	                        <?php
                        } else {
	                        if ( ! empty( $icon ) ) {
		                        get_template_part( 'template-parts/page-title/icon' );
	                        }
	                        if ( ! empty( $title ) ) {
		                        if ( empty( $alternate ) ) {
			                        get_template_part( 'template-parts/page-title/title' );
		                        } else {
			                        get_template_part( 'template-parts/page-title/title-alternative' );
		                        }
	                        }
                        }

	                    if ( is_home() || strpos( $template_name, 'blog' ) ) {
		                    if ( ! empty( $breadcrumbs ) ) {
			                    get_template_part( 'template-parts/page-title/breadcrumbs' );
		                    }
	                    }
                    } else {
	                    if ( ! empty( $title ) ) {
		                    echo sprintf('<h1 class="main-page-title"><span>%s</span></h1>', __('Search Results','conico') ) ;
	                    }
	                    if ( ! empty( $breadcrumbs ) ) {


		                    $search_query = apply_filters( 'the_search_query', get_search_query( false ) );

		                    if(empty($search_query) && !is_numeric($search_query)) {
			                    $search_query = '" "';
		                    }

		                    ?>
                            <div class="breadcrumb-block">
                                <ol class="breadcrumb text-uppercase">
                                    <li>
                                    <span>
                                        <span><?php echo esc_attr($search_query); ?></span>
                                    </span>
                                    </li>
                                </ol>
                            </div>
		                    <?php
	                    }
                    }
                     ?>
                </div>

            </div>
        </div>
    </div>
<?php
    if( !is_archive() && !is_home() && !is_search() && !strpos( $template_name, 'blog' ) ) {
	    if ( ! empty( $breadcrumbs ) ) {
		    get_template_part( 'template-parts/page-title/breadcrumbs' );
	    }
    }

}