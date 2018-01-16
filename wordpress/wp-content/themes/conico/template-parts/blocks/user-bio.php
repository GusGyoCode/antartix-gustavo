<?php
/**
 * The template for displaying Author bios
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>


<?php

// Author bio.
if ( is_single() && get_the_author_meta( 'description' ) ) {

	$id = get_the_ID();
	$comments_number = (int)get_comments_number($id);

    ?>
	<div class="single-entry-bio clearfix <?php echo !empty($comments_number) ? 'has-comments' : 'no-comments'; ?>">

		<div class="user-thumbnail"><?php echo get_avatar( get_the_author_meta( 'user_email' ), 100 ) ?></div>

		<div class="about">
			<div class="row">
				<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
					<h5><?php echo get_the_author(); ?></h5>
					<p><?php the_author_meta( 'description' ); ?></p>
				</div>
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
					<div class="more-posts-block">
						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="btn-author" title="<?php echo esc_attr( __('All Posts', 'conico') ); ?>"><?php echo esc_html( __('All Posts', 'conico') ); ?></a>
					</div>
				</div>
			</div>
		</div>

	</div>
	<?php
}
?>