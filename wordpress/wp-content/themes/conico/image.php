<?php
/**
 * The template for displaying image attachments
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

get_header(); ?>

	<!-- CONTAINER -->
	<div class="content ">
		<div class="container">
			<div class="row">

				<div class="col-xs-12 page-content-cell maincontent" role="main">
					<?php

						// Start the Loop.
						while ( have_posts() ) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('container format-image-attachment'); ?>>

								<nav id="image-navigation" class="navigation image-navigation">
									<div class="nav-links">
										<div class="nav-previous"><?php previous_image_link( false, __( 'Previous Image', 'conico' ) ); ?></div>
										<div class="nav-next"><?php next_image_link( false, __( 'Next Image', 'conico' ) ); ?></div>
									</div><!-- .nav-links -->
								</nav><!-- .image-navigation -->


								<div class="entry-attachment entry-content">
										<?php
										/**
										 * Filter the default Conico image attachment size.
										 *
										 * @since Conico 1.0
										 *
										 * @param string $image_size Image size. Default 'large'.
										 */
										$image_size = apply_filters( 'conico_wp_attachment_size', 'large' );

										echo wp_get_attachment_image( get_the_ID(), $image_size );
										?>

										<?php if ( has_excerpt() ) : ?>
											<div class="entry-caption">
												<?php the_excerpt(); ?>
											</div><!-- .entry-caption -->
										<?php endif; ?>

									</div><!-- .entry-attachment -->

									<?php
										the_content();

										wp_link_pages( array(
											'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'conico' ) . '</span>',
											'after'       => '</div>',
											'link_before' => '<span>',
											'link_after'  => '</span>',
											'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'conico' ) . ' </span>%',
											'separator'   => '<span class="screen-reader-text">, </span>',
										) );
									?>

								<?php edit_post_link( __( 'Edit', 'conico' ), '<div class="entry-footer"><span class="edit-link">', '</span></div>' ); ?>

							</article><!-- #post-## -->

							<hr class="hr-comments">

							<div class="image-post-navigation">
								<?php
									// Previous/next post navigation.
									the_post_navigation( array(
										'prev_text' => _x( '<span class="meta-nav">Published in </span><span class="post-title">%title</span>', 'Parent post link', 'conico' ),
									) );
								?>
							</div>

							<hr class="hr-comments">

							<?php comments_template(); ?>

						<?php endwhile; ?>
				</div>

			</div>
		</div> <!-- /.container -->
	</div>


<?php

get_footer();