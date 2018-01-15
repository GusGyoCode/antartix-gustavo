<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

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


	<?php do_action( 'conico_before_content' ); ?>
		<div class="content">
			<!-- CONTAINER -->
			<div class="container">
				<div class="row">

					<div class="<?php echo esc_attr( basement_content_classes( 'page' ) ); ?>" role="main">
						<?php
							// Start the Loop.
							while ( have_posts() ) : the_post();
								the_content();


								// If comments are open or we have at least one comment, load up the comment template.
								if ( comments_open() || get_comments_number() ) :
									comments_template();
								endif;

							endwhile;
						?>
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
							basement_sidebar();
						}
					?>

				</div>
			</div> <!-- /.container -->
		</div>
	<?php do_action( 'conico_after_content' ); ?>

<?php

get_footer();