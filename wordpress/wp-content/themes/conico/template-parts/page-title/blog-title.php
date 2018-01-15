<?php
/**
 * The template part for displaying a main title in page title
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_blog_title' ); ?>

		<?php
		if ( function_exists( 'basement_the_specific_title' ) ) {
			if ( is_search() ) {
				basement_the_specific_title( 'search', __( 'Search', 'conico' ) );
			} elseif ( is_home() && ! is_front_page() ) {
				wp_title( '' );
			} elseif ( ( is_home() && is_front_page() ) ) {
				basement_the_specific_title( 'blog', __( 'Our Blog', 'conico' ) );
			} elseif ( is_singular( 'post' ) ) {
				$blog_type = get_option( 'show_on_front' );
				if ( $blog_type === 'posts' ) {
					basement_the_specific_title( 'blog', __( 'Our Blog', 'conico' ) );
				} else {
					echo get_the_title( get_option( 'page_for_posts' ) );
				}
			} elseif ( is_archive() && ! is_tag() && ! Basement_Ecommerce_Woocommerce::is_shop() && ! is_tax( array(
					'product_cat',
					'product_tag'
				) )
			) {

				if ( empty( basement_the_specific_title( 'archives', '', false ) ) ) {
					if ( is_day() ) {
						_e( 'Daily Archives', 'conico' );
					} elseif ( is_month() ) {
						_e( 'Monthly Archives', 'conico' );
					} elseif ( is_year() ){
						_e( 'Yearly Archives', 'conico' );
					} else {
						if(is_author()) {
							_e( 'All posts by', 'conico' );
						} elseif(is_category()){
							_e( 'Category', 'conico' );
						} else {
							basement_the_specific_title( 'archives', __( 'Archives', 'conico' ) );
						}
					}
				} else {
					if ( is_day() || is_month() || is_year()) {
						basement_the_specific_title( 'archives', __( 'Archives', 'conico' ) );
					} else {
						if(is_author()) {
							_e( 'All posts by', 'conico' );
						} elseif(is_category()){
							_e( 'Category', 'conico' );
						} else {
							basement_the_specific_title( 'archives', __( 'Archives', 'conico' ) );
						}
					}
				}

			} elseif ( is_tag() ) {
				_e( 'Tag Archive', 'conico' );
			} elseif ( Basement_Ecommerce_Woocommerce::is_shop() || is_tax( array( 'product_cat', 'product_tag' ) ) ) {
				if ( apply_filters( 'woocommerce_show_page_title', true ) ) :
					woocommerce_page_title();
				endif;
			} elseif ( is_singular( 'single_project' ) ) {
				if ( function_exists( 'basement_single_project_page_title' ) ) {
					basement_single_project_page_title();
				}
			} elseif ( is_404() ) {
				basement_the_specific_title( '404', __( 'Page not found', 'conico' ) );
			} else {
				the_title();
			}
		} else {

			if ( is_search() ) {
				_e( 'Search', 'conico' );
			} elseif ( is_home() && ! is_front_page() ) {
				wp_title( '' );
			} elseif ( ( is_home() && is_front_page() ) ) {
				_e( 'Our Blog', 'conico' );
			} elseif ( is_singular( 'post' ) ) {
				$blog_type = get_option( 'show_on_front' );
				if ( $blog_type === 'posts' ) {
					_e( 'Our Blog', 'conico' );
				} else {
					echo get_the_title( get_option( 'page_for_posts' ) );
				}
			} elseif ( is_archive() && ! is_tag() && ! basement_is_shop() && ! is_tax( array(
					'product_cat',
					'product_tag'
				) )
			) {
				if ( is_day() ) :
					_e( 'Daily Archives', 'conico' );
				elseif ( is_month() ) :
					_e( 'Monthly Archives', 'conico' );
				elseif ( is_year() ) :
					_e( 'Yearly Archives', 'conico' );
				else :
					if(is_author()) :
						_e( 'All posts by', 'conico' );
					elseif(is_category()):
						_e( 'Category', 'conico' );
					else:
						_e( 'Archives', 'conico' );
					endif;
				endif;
			} elseif ( is_tag() ) {
				_e( 'Tag Archive', 'conico' );
			} elseif ( basement_is_shop() || is_tax( array( 'product_cat', 'product_tag' ) ) ) {
				if ( conico_woo_enabled() ) {
					if ( apply_filters( 'woocommerce_show_page_title', true ) ) :
						woocommerce_page_title();
					endif;
				}
			} elseif (is_author()) {
				_e( 'All posts by', 'conico' );
			} elseif ( is_404() ) {
				_e( 'Page not found', 'conico' );
			} else {
				the_title();
			}
		}
		?>

<?php do_action( 'conico_after_blog_title' ); ?>