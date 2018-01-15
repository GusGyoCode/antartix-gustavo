<?php
/**
 * The template for displaying Tag pages
 *
 * Used to display archive-type pages for posts in a tag.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

get_header(); ?>

	<!-- CONTAINER -->
	<div class="content">
		<?php do_action( 'conico_start_blog_container' ); ?>
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="<?php echo esc_attr( basement_content_classes( 'blog' ) ); ?>" role="main">
					<div class="blog-posts-wrapper">
						<!--  POSTS GRID -->
						<?php do_action( 'conico_start_blog_grid' ); ?>
							<?php
								if ( have_posts() ) :

									// Start the Loop.
									while ( have_posts() ) : the_post();

										/*
										 * Include the post format-specific template for the content. If you want to
										 * use this in a child theme, then include a file called called content-___.php
										 * (where ___ is the post format) and that will be used instead.
										 */
										get_template_part( 'template-parts/main/content', get_post_format() );

									endwhile;

								else :
									// If no content, include the "No posts found" template.
									get_template_part( 'template-parts/main/content', 'none' );

								endif;
							?>
						<?php do_action( 'conico_end_blog_grid' ); ?>
						<!-- ./POSTS GRID -->
					</div>
					<?php
						// Previous/next page navigation.
						conico_paging_nav();

						// Show an optional term description.
						$term_description = term_description();
						if ( ! empty( $term_description ) ) :
							printf( '<div class="blog-content">%s</div>', $term_description );
						endif;
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
					basement_sidebar( 'blog' );
				}
				?>

			</div>
		<?php do_action( 'conico_end_blog_container' ); ?>
        <!-- /.container -->
	</div>


<?php

get_footer();