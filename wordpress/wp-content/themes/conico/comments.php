<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<?php if ( have_comments() ) : ?>
	<div id="comments" class="comments single-entry-comments">
		<div class="single-comments-body">
			<?php
				$comments_number = get_comments_number();

				if ( 1 === absint( $comments_number ) ) {
					$comment_word = esc_html__( 'Comment', 'conico' );
				} else {
					$comment_word = esc_html__( 'Comments', 'conico' );
				}
				?>
				<h3 class="comments-subtitle"><?php echo esc_html( $comment_word ); ?> <sup><?php echo esc_html( $comments_number ); ?></sup></h3>

				<?php the_comments_navigation(); ?>

				<div class="comments-inner">
					<?php
						wp_list_comments( array(
							'walker' => new Conico_List_Comments(),
							'style'  => 'div'
						) );
					?>

				</div>

		</div>
	</div><!-- .comments-area -->


	<?php if ( get_comment_pages_count() > 1 ) { ?>
		<div class="comments-pagination">
			<?php paginate_comments_links(); ?>
		</div>
	<?php } ?>

<?php endif; // Check for have_comments(). ?>

<?php
// If comments are closed and there are comments, let's leave a little note, shall we?
if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
	<p class="no-comments text-danger text-center"><?php esc_html_e( 'Comments are closed.', 'conico' ); ?></p>
<?php endif; ?>


<?php
$commenter     = wp_get_current_commenter();
$user          = wp_get_current_user();
$user_identity = $user->exists() ? $user->display_name : '';

$args = array();
if ( ! isset( $args['format'] ) ) {
	$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
}

$req      = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );
$html_req = ( $req ? " required='required'" : '' );
$html5    = 'html5' === $args['format'];

$reqs = ( $req ? "*" : '' );

$fields = array(
	'author' => '<div class="wpcf7-form-control-wrap"><input id="author" name="author"  placeholder="' . esc_html__( 'Your name', 'conico' ) . $reqs. '" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245"' . $aria_req . $html_req . ' /></div>',
	'email'  => '<div class="row wpcf7-form-control-wrap"><div class="col-sm-6 "><input id="email" name="email" placeholder="' . esc_html__( 'Email address', 'conico' ) . $reqs. '" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></div>',
	'url'    => '<div class="col-sm-6"><input id="url" name="url" placeholder="' . esc_html__( 'Website', 'conico' ) . '" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" maxlength="200" /></div></div>',
);

$fieldset = '';
$notice = esc_html__( 'Your email address will not be published', 'conico' );
if ( is_user_logged_in() ) {
	$fieldset = '<div class="form-wrap">';
	$notice = '';
}

$args = array(
	'comment_field'        => $fieldset . '<div class="wpcf7-form-control-wrap"><textarea id="comment" placeholder="' . esc_html__( 'Comment*', 'conico' ) . '" name="comment" cols="45" rows="8" maxlength="65525" aria-required="true" required="required"></textarea></div></div>',
	'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
	'title_reply'          => sprintf( '%s<small>%s</small>', esc_html__( 'Add Comment', 'conico' ), $notice ),
	'label_submit'         => esc_html__( 'Add Comment', 'conico' ),
	'submit_field'         => '%1$s %2$s',
	'comment_notes_before' => '',
	'cancel_reply_before'  => '',
	'cancel_reply_after'   => '',
	'cancel_reply_link'    => esc_html__( 'Cancel Reply', 'conico' ),
	'class_form' => 'comment-form wpcf7-form '
);
 ?>
<div role="form" class="wpcf7 comment-respond" lang="en-US" dir="ltr"><?php comment_form( $args ); ?></div>
