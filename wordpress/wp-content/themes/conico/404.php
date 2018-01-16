<?php
/**
 * The main template for displaying 404 pages (not found)
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

get_header(); ?>

	<div class="content">
		<!-- CONTAINER -->
		<div class="container">
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-lg-12 page-content-cell maincontent" role="main">
					<?php

						$args = array(
							'post_type'  => 'page',
							'fields'     => 'ids',
							'nopaging'   => true,
							'orderby'    => 'post_date',
							'order'      => 'DESC',
							'meta_key'   => '_wp_page_template',
							'meta_value' => 'page-templates/page-404.php'
						);

						$page_id = '';

						$pages = get_posts( apply_filters('basement_404_template_args', $args ) );
						if(!empty($pages)) {
							foreach ( $pages as $index => $id ) {
								if ($index == 0) {
									$page_id = $id;
								}
							}
						}

						$conico_404 = new WP_Query( array(
							'page_id' => $page_id
						) );

						if ( $conico_404->have_posts() && !empty($page_id) ) :

							$css = get_post_meta( $page_id, '_wpb_shortcodes_custom_css', true );

						if ( $css ) {
								echo "<style>{$css}</style>";
							}

							// Start the Loop.
							while ( $conico_404->have_posts() ) : $conico_404->the_post();
								the_content();
							endwhile;

							wp_reset_postdata();
						else :
							// If no content, include the "No posts found" template.
							get_template_part( 'template-parts/main/content', '404' );
						endif;
					?>
				</div>
				<!-- ./main-content -->
			</div>
		</div> <!-- /.container -->
	</div>

<?php get_footer();
