<?php
class Conico_List_Comments extends Walker_Comment {
	/**
	 * Outputs a comment in the HTML5 format.
	 *
	 * @since 3.6.0
	 * @access protected
	 *
	 * @see wp_list_comments()
	 *
	 * @param WP_Comment $comment Comment to display.
	 * @param int        $depth   Depth of the current comment.
	 * @param array      $args    An array of arguments.
	 */
	protected function html5_comment( $comment, $depth, $args ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

		$custom_class = $this->has_children ? 'parent' : '';
		$custom_class .= 'comment clearfix';

		$avatar = get_avatar( $comment, 90 );

		if(!$avatar) {
			$custom_class .= ' comment-not-avatar';
		}


		?>
		<<?php echo esc_attr($tag); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $custom_class, $comment ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body clearfix">
			<?php if ( $avatar ) { ?>
				<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>" class="comment-ava"><?php if ( 0 != $args['avatar_size'] ) {  printf('%s',$avatar); } ?></a>
			<?php } ?>
			<div class="comment-text">
				<div class="comment-line clearfix">
                    <div class="row">
                        <div class="col-sm-3 col-sm-push-9">
                            <div class="comment-meta-block">
			                    <?php
			                    /* translators: 1: comment date, 2: comment time */
			                    printf( __( '<span class="comment-date">%s</span>', 'conico' ), get_comment_date( '', $comment ) );
			                    ?>
                                <div class="comment-meta-block-footer">
				                    <?php
				                    printf( __( '<span class="comment-time">%s</span>', 'conico' ), get_comment_time() );
				                    ?>
				                    <?php

				                    comment_reply_link( array_merge( $args, array(
					                    'add_below' => 'comment',
					                    'depth'     => $depth,
					                    'max_depth' => $args['max_depth'],
					                    'before'     => ' - '
				                    ) ) );
				                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 col-sm-pull-3">
                            <div class="comment-line-info">
		                        <?php printf( __( '<div class="comment-author">%s</div>', 'conico' ), sprintf( '%s', get_comment_author_link( $comment ) ) );  edit_comment_link( __( 'Edit', 'conico' ), ' &mdash; ', '' ); ?>
                            </div>
                            <div class="comment-text-body">
		                        <?php comment_text(); ?>
                            </div>
                        </div>

                    </div>
				</div>

			</div>
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<div class="comment-text comment-text-footer">
					<p class="comment-awaiting-moderation text-warning"><?php _e( 'Your comment is awaiting moderation.', 'conico' ); ?></p>
				</div>
			<?php endif; ?>
		</article><!-- .comment-body -->
		<?php
	}
}