<?php
/**
 * The template for displaying single project.
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
	<!-- CONTAINER -->
	<?php do_action( 'conico_before_content' ); ?>
		<div class="content">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 page-content-cell maincontent" role="main">
						<?php
                            // Start the Loop.
                            while ( have_posts() ) : the_post();

                                the_content();

                            endwhile;
						?>
					</div>

				</div>

				<?php

				do_action( 'conico_before_single_project' );

				if ( function_exists( 'basement_the_single_project' ) ) {
					/**
					 * Displays Single Project
					 *
					 * @package    Aisconverse
					 * @subpackage Conico
					 * @since      Conico 1.0
					 */
					basement_the_single_project();
				}

				do_action( 'conico_after_single_project' );

				?>
			</div> <!-- /.container -->
		</div>

	<?php do_action( 'conico_after_content' ); ?>

<?php get_footer(); ?>



