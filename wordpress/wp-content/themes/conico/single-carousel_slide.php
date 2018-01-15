<?php
/**
 * The template for displaying preview carousel slide.
 *
 * @package Aisconverse
 * @subpackage Conico
 * @since Conico 1.0
 */

get_header(); ?>

    <!-- CONTAINER -->
    <div class="content">
	    <div class="container">
	        <?php
		        // Start the Loop.
		        while ( have_posts() ) : the_post();

		            do_action('conico_before_carousel_slide');

		            the_content();

		            do_action('conico_after_carousel_slide');

		        endwhile;
	        ?>
	    </div> <!-- /.container -->
    </div>

<?php get_footer(); ?>