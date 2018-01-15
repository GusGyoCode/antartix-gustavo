<?php
/**
 * The template part for displaying a message that posts cannot be found
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_content_none' ); ?>

	<article class="no-results not-found search-item text-center col-xs-12">
		<div class="entry-summary">

			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

				<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'conico' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

			<?php elseif ( is_search() ) : ?>

				<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'conico' ); ?></p>

			<?php else : ?>

				<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'conico' ); ?></p>

			<?php endif; ?>

		</div><!-- /.entry-summary -->
	</article><!-- /.no-results -->

<?php do_action( 'conico_after_content_none' ); ?>