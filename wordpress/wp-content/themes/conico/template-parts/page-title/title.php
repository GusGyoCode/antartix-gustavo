<?php
/**
 * The template part for displaying a main title in page title
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_title' ); ?>

	<?php

		$title = '';

		if ( function_exists( 'basement_the_specific_title' ) ) {
			if ( is_search() ) {
				$title = basement_the_specific_title( 'search', __( 'Search', 'conico' ), false );
			} elseif ( is_home() && ! is_front_page() ) {
				$title = wp_title( '', false );
			} elseif ( ( is_home() && is_front_page() ) ) {
				$title = basement_the_specific_title( 'blog', __( 'Our Blog', 'conico' ), false );
			}  elseif ( is_archive() && ! is_tag() ) {
				if ( is_day() ) :
					$title = __( 'Daily Archives', 'conico' );
				elseif ( is_month() ) :
					$title = __( 'Monthly Archives', 'conico' );
				elseif ( is_year() ) :
					$title = __( 'Yearly Archives', 'conico' );
				else :
					$title = basement_the_specific_title( 'archives', __( 'Archives', 'conico' ), false );
				endif;
			} elseif ( is_tag() ) {
				$title = single_tag_title( '', false );
			}  elseif ( is_singular( 'single_project' ) ) {
				if ( function_exists( 'basement_single_project_page_title' ) ) {
					$title = basement_single_project_page_title(false);
				}
			} elseif ( is_404() ) {
				$title = basement_the_specific_title( '404', __( 'Page not found', 'conico' ), false );
			} else {
				$title = the_title('','',false);
			}
		} else {
			if ( is_search() ) {
				$title = __( 'Search', 'conico' );
			} elseif ( is_home() && ! is_front_page() ) {
				$title = wp_title( '', false );
			} elseif ( ( is_home() && is_front_page() ) ) {
				$title = __( 'Our Blog', 'conico' );
			} elseif ( is_archive() && ! is_tag() ) {
				if ( is_day() ) :
					$title = __( 'Daily Archives', 'conico' );
				elseif ( is_month() ) :
					$title = __( 'Monthly Archives', 'conico' );
				elseif ( is_year() ) :
					$title = __( 'Yearly Archives', 'conico' );
				else :
					$title = __( 'Archives', 'conico' );
				endif;
			} elseif ( is_tag() ) {
				$title = sprintf( __( 'Tag Archives: %s', 'conico' ), single_tag_title( '', false ) );
			}   elseif ( is_404() ) {
				$title = __( 'Page not found', 'conico' );
			} else {
				$title = the_title('','',false);
			}
		}
	?>

	<?php printf('<h1 class="main-page-title"><span>%s</span></h1>', trim( $title ) ); ?>

<?php do_action( 'conico_after_title' ); ?>