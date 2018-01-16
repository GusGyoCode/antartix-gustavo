<?php
global $basement_content;
$post_id = get_the_ID();
$format  = get_post_format();
$sidebar = isset( $basement_content['display'] ) ? $basement_content['display'] : '';
$thumbnail   = get_the_post_thumbnail_url( null, 'full' );
$title   = get_the_title();

if ( false === $format ) {
	$format = 'standard';
}


$post_classes  = array();
$template_name = get_page_template_slug( get_queried_object_id() );

if(strpos( $template_name, 'page-header-light' ) !== false || strpos( $template_name, 'page-404' ) !== false) {
	$template_name = '';
}

if( ( is_archive() || is_home() || is_tag() ) && empty($template_name)) {
	$template_name = get_option('basement_framework_blog_archive','classic');
}

if(strpos( $template_name, 'classic' ) !== false || empty($template_name)) {
	if ( strpos( $template_name, 'fullwidth' ) !== false ) {

		if ( $sidebar === 'yes' ) {
			$post_classes[] = 'col-xs-12 col-sm-12 col-md-6 col-lg-6';
		} else {
			$post_classes[] = 'col-xs-12 col-sm-6 col-md-6 col-lg-4';
		}
	} else {

		if ( $sidebar === 'yes' ) {
			$post_classes[] = 'col-xs-12 col-sm-12 col-md-12 col-lg-6';
		} else {
			$post_classes[] = 'col-xs-12 col-sm-12 col-md-6 col-lg-6';
		}
	}
} elseif(strpos( $template_name, 'creative' ) !== false) {
    if ( strpos( $template_name, 'fullwidth' ) !== false) {


	    if ( $sidebar === 'yes' ) {
		    $post_classes[] = 'col-xs-12 col-sm-12 col-md-6 col-lg-4';
	    } else {
		    $post_classes[] = 'col-xs-12 col-sm-6 col-md-4 col-lg-3';
	    }
    } else {


	    if ( $sidebar === 'yes' ) {
		    $post_classes[] = 'col-xs-12 col-sm-12 col-md-6 col-lg-6';
	    } else {
		    $post_classes[] = 'col-xs-12 col-sm-6 col-md-6 col-lg-4';
	    }

    }
}

if ( is_single() ) {
	$post_classes = array('post-single');
}



// Time tag as link if title missed
if ( empty( $title ) ) {
	$post_classes[] = 'time-linked';
}


// Check if post sticky
$is_sticky = false;
if ( is_sticky( $post_id ) && ! is_paged() ) {
	$is_sticky = true;
}

// Check if thumbnail exist
$is_thumbnail = false;
if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment() && ! empty( $thumbnail ) ) {
	$is_thumbnail = true;
}


// Added sticky class if post sticky
if ( $is_sticky ) {
	$post_classes[] = 'sticky';
}


?>

<article id="post-<?php the_ID(); ?>" <?php post_class( implode( ' ', $post_classes ) ); ?>>
	<?php if ( ! is_single() ) : ?>

		<div class="entry">


            <div class="entry-bg-thumbnail-wrapper <?php echo ($format === 'standard' && !$is_sticky && !$is_thumbnail) ? 'hide' : ''; ?>">
                <?php if ( $is_sticky ) { ?>
                    <span class="sticky-post"><i class="icon-loader"></i></span>
                <?php }

                if($format !== 'standard') {
	                $icon = '';
	                switch ( $format ) {
		                case 'link' :
			                $icon = 'icon-link';
			                break;
		                case 'video' :
			                $icon = 'icon-play';
			                break;
		                case 'quote' :
			                $icon = 'icon-speech-bubble';
			                break;
		                case 'status' :
			                $icon = 'icon-flag';
			                break;
		                case 'aside' :
			                $icon = 'icon-paper';
			                break;
		                case 'audio' :
			                $icon = 'icon-volume';
			                break;
		                case 'chat' :
			                $icon = 'icon-ellipsis';
			                break;
		                case 'image' :
			                $icon = 'icon-image';
			                break;
		                case 'gallery' :
			                $icon = 'icon-camera';
			                break;
	                }
	                printf( '<span class="format-post"><i class="%s"></i></span>', $icon );
                }

                if ( $is_thumbnail ) { ?>
                    <a href="<?php the_permalink(); ?>" class="entry-bg-thumbnail" title="<?php the_title(); ?>"  style="background-image: url(<?php echo esc_url( $thumbnail ); ?>);"></a>
                <?php } ?>
            </div>

            <div class="entry-meta-category">
                <?php the_category( __( ', ', 'conico' ) ); ?>
            </div>

			<?php

            $disabled_header = array('status', 'quote', 'aside', 'link');
            if ( ! in_array($format, $disabled_header) && ( ! empty( $title ) || is_numeric( $title ) ) ) : ?>
				<div class="entry-header">
					<h3>
						<?php the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); ?>
						<?php conico_comments(); ?>
					</h3>

				</div>
			<?php endif; ?>

            <div class="entry-meta-info">
	            <?php conico_entry_date(); ?><span class="indent"></span><?php echo get_the_author_posts_link(); ?>
            </div>

			<div class="entry-content is-card">
				<?php
					if ( has_excerpt() ) {
						the_excerpt();
					} else {
						$content = get_the_content();
						$content = preg_replace( '/\[\\/?vc_(.*?)\]/', '', $content );
						$content = apply_filters( 'the_content', $content );
						$content = str_replace( ']]>', ']]&gt;', $content );
						
						printf('%s', $content);
					}
				?>
			</div>
			<?php edit_post_link( __( 'Edit', 'conico' ), '<div class="entry-footer"><span class="edit-link">', '</span></div>' ); ?>
		</div>

	<?php else : ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>

	<?php endif; ?>
</article><!-- #post-## -->