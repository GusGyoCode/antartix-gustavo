<?php
/**
 * The template part for displaying results in search pages
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

$post_id = get_the_ID();

$format = get_post_format();

if ( false === $format )
	$format = 'standard';

$post_classes = array('post post-search col-xs-12 col-sm-6 col-md-6 col-lg-4');

if ( is_single() ) {
	$post_classes = array();
}

$excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );
$text = preg_replace(array('/#vc-ai-(.+)\}/','/&#8230;\./','/&#8230;/','/\.{3,}/'), array('','',' ',' '),$excerpt);

if ( ! conico_empty_content( $excerpt ) ) { ?>

<?php do_action( 'conico_before_content_search' ); ?>

<article id="post-<?php the_ID(); ?>" class="<?php echo implode(' ', $post_classes); ?>">

	<div class="entry">

		<div class="entry-header">
			<?php the_title( '<h3><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
		</div>

		<div class="entry-content is-card text-left">
			<?php
                $max_length = 160;

                if (strlen($text) > $max_length) {
                    $offset = ($max_length - 3) - strlen($text);
                    $text = substr($text, 0, strrpos($text, ' ', $offset)) . '...';
                } else {
	                $text = str_replace('</p>','...</p>', $text);
                }

				printf('%s',$text);
			?>
		</div>
		<?php edit_post_link( __( 'Edit', 'conico' ), '<div class="entry-footer"><span class="edit-link">', '</span></div>' ); ?>
	</div>

</article><!-- #post-## -->

<?php do_action( 'conico_after_content_search' ); ?>

<?php  } ?>