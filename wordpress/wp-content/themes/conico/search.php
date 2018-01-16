<?php
/**
 * The template for displaying search results pages.
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
				<div class="blog-posts-wrapper">
					<?php do_action( 'conico_before_search_item' ); ?>
					<!--  POSTS GRID -->
					<div class="row blog-posts-grid classic">
						<?php

						global $query_string;

						$query_args = explode("&", $query_string);


						$search_params = array(
                            'post_status' => 'publish',
                            'posts_per_page' => 12,
                            'paged' => get_query_var( 'paged' ) ? (int)get_query_var( 'paged' ) : 1
                        );

						if( strlen($query_string) > 0 ) {
							foreach($query_args as $key => $string) {
								$query_split = explode("=", $string);
								$search_params[$query_split[0]] = urldecode($query_split[1]);
							}
						}

						$empty_posts = conico_empty_search_posts();

						if(!empty($empty_posts)) {
							$search_params['post__not_in'] = $empty_posts;
                        }

						$search_query = new WP_Query($search_params);

						if ( $search_query->have_posts() ) :
							// Start the Loop.
							while ( $search_query->have_posts() ) : $search_query->the_post();

								/*
								 * Run the loop for the search to output the results.
								 * If you want to overload this in a child theme then include a file
								 * called content-search.php and that will be used instead.
								 */
								get_template_part( 'template-parts/main/content', 'search' );

							endwhile;

						else :
							// If no content, include the "No posts found" template.
							get_template_part( 'template-parts/main/content', 'none' );

						endif;

						wp_reset_query();
						?>
					</div>
					<!-- ./POSTS GRID -->
					<?php do_action( 'conico_after_search_item' ); ?>
				</div>
				<?php
					// Previous/next page navigation.
					conico_paging_nav($search_query);
				?>
			</div>
			<!-- ./main-content -->
		</div>
	</div> <!-- /.container -->
</div>

<?php get_footer();
