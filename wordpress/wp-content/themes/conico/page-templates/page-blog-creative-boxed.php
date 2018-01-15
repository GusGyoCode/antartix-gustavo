<?php
/**
 * Template Name: Blog Home - Creative Boxed
 *
 * The template for displaying Simple Blog Grid.
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */


/**
 * Ready to use settings for the template
 */
/*###
[{
	"header" : {
	    "menu_type" : "default",
		"logo_position" : "left",
		"header_off" : "no",
		"header_elements" : ["logo_text","menu","search_section","button_section","lang_section"],
		"header_style" : "white",
		"header_bg" : "",
        "header_size" : "fullwidth",
        "header_helper" : "no",
		"header_opacity" : "",
		"header_border_bg" : "",
		"header_border_opacity" : "",
		"header_border_size" : "fullwidth",
		"header_padding_top" : "",
		"header_padding_bottom" : "",
		"header_btn_text" : "Subscribe",
		"header_btn_link" : "/",
		"header_btn_icon" : "icon-mail",
		"header_global_border" : "no",
		"header_global_border_color" : "",
		"header_global_border_size" : ""
	},
	"page_title" : {
		"pt_alternate" : "",
	    "pt_placement" : "under",
		"pt_style" : "white",
		"pt_elements" : ["title","line","breadcrumbs"],
		"pt_icon" : "",
		"pt_icon_size" : "",
		"pt_icon_color" : "",
		"pt_position" : "center_right",
		"pt_title_size" : "",
		"pt_title_color" : "",
		"pt_bg" : "",
		"pt_float_enable" : "no",
		"pt_float_text_size" : "",
		"pt_float_text_color" : "",
		"pt_bg_color" : "",
		"pt_bg_opacity" : "",
		"pt_padding_top" : "",
		"pt_padding_bottom" : "",
		"pt_off" : "no"
	},
	"rev_slider" : {
		"shortcode" : "",
		"alias" : "",
		"rev_position" : "before_content",
		"hide_content" : []
	},
	"sidebar" : {
		"sidebar" : "no",
		"sidebar_dir" : "right",
		"sidebar_line" : "no",
		"sidebar_area" : "sidebar-1",
		"sidebar_visibility" : "all"
	}
}]
###*/


get_header(); ?>

<?php
	if ( function_exists( 'basement_revslider' ) ) {
		/**
		 * Displays Slider Revolution.
		 *
		 * @package    Aisconverse
		 * @subpackage Conico
		 * @since      Conico 1.0
		 */
		basement_revslider();
}
?>

	<!-- CONTAINER -->
<?php do_action( 'conico_before_content' ); ?>
	<div class="content">
		<div class="container">
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="<?php echo esc_attr( basement_content_classes( 'page' ) ); ?>" role="main">
					<div class="blog-posts-wrapper">
						<div class="row blog-posts-grid classic is-creative">
							<?php
                                $posts_per_page = isset($_GET['posts']) ? $_GET['posts'] : 0;
                                $conico_posts_params = array(
                                    'post_type'           => 'post',
                                    'post_status'         => 'publish',
                                    'paged'               => get_query_var( 'paged' ) ? (int)get_query_var( 'paged' ) : 1,
                                    'ignore_sticky_posts' => false
                                );

                                if(!empty($posts_per_page)) {
                                    $conico_posts_params['posts_per_page'] = (int)$posts_per_page;
                                }

                                $conico_posts = new WP_Query( $conico_posts_params );

								if ( $conico_posts->have_posts() ) :
									// Start the Loop.
									while ( $conico_posts->have_posts() ) : $conico_posts->the_post();

										/*
										 * Include the post format-specific template for the content. If you want to
										 * use this in a child theme, then include a file called called content-___.php
										 * (where ___ is the post format) and that will be used instead.
										 */
										get_template_part( 'template-parts/main/content', get_post_format() );

									endwhile;

									wp_reset_postdata();
								else :
									// If no content, include the "No posts found" template.
									get_template_part( 'template-parts/main/content', 'none' );

								endif;
							?>
						</div>
					</div>
					<?php
					// Previous/next page navigation.
					conico_paging_nav( $conico_posts );

					// Start the Loop.
					while ( have_posts() ) : the_post();
						$content = get_the_content();
						if ( trim( $content ) !== "" ) { ?>
							<div class="blog-content">
								<?php the_content(); ?>
							</div>
							<?php
						}
					endwhile;
					?>
				</div>
				<!-- ./main-content -->
				<?php
				if ( function_exists( 'basement_sidebar' ) ) {
					/**
					 * Displays Main sidebar. Important!
					 * For correct display of the sidebar, code 'basement_sidebar' should be on the right (after content).
					 *
					 * @package    Aisconverse
					 * @subpackage Conico
					 * @since      Conico 1.0
					 */
					basement_sidebar();
				}
				?>

			</div>
		</div> <!-- /.container -->
	</div>
<?php do_action( 'conico_after_content' ); ?>

<?php

get_footer();