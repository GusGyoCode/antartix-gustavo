<?php
/**
 * The template for displaying all single posts
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

get_header(); ?>

	<!-- CONTAINER -->
	<?php do_action( 'conico_before_content' ); ?>
		<div class="content">
			<div class="container">
				<div class="row">

					<div class="<?php echo esc_attr( basement_content_classes( 'blog' ) ); ?>" role="main">
						<?php /* The loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>

							<?php get_template_part( 'template-parts/main/content', get_post_format() ); ?>

							<?php
							wp_link_pages( array(
								'before'      => '<div class="single-entry-page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'conico' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
								'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'conico' ) . ' </span>%',
								'separator'   => '<span class="screen-reader-text">, </span>',
							) );
							?>

							<?php edit_post_link( esc_html__( 'Edit', 'conico' ), '<div class="single-entry-footer"><span class="edit-link">', '</span></div>' ); ?>

							<?php conico_single_post_meta(); ?>

							<?php get_template_part( 'template-parts/blocks/user-bio' ); ?>

							<?php comments_template(); ?>

							<?php conico_post_nav(); ?>

						<?php endwhile; ?>
					</div>

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
			</div> <!-- /.container -->
		</div>
	<?php do_action( 'conico_after_content' ); ?>

<?php

get_footer();