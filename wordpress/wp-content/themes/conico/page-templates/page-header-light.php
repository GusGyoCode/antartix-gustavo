<?php
/**
 * Template Name: Page - Header Light
 *
 * The custom template for displaying simple page.
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */


/**
 * Ready to use settings for the template
 */
/*###
[{
	"header" : {
	    "menu_type" : "default",
		"logo_position" : "left",
		"header_off" : "no",
		"header_elements" : ["logo_text","menu","search_section","lang_section"],
		"header_style" : "white",
		"header_bg" : "",
        "header_helper" : "no",
        "header_size" : "fullwidth",
		"header_opacity" : "",
		"header_border_bg" : "",
		"header_border_opacity" : "",
		"header_border_size" : "",
		"header_padding_top" : "",
		"header_padding_bottom" : "",
		"header_btn_text" : "Contact",
		"header_btn_link" : "/",
		"header_btn_icon" : "icon-mail",
		"header_global_border" : "no",
		"header_global_border_color" : "",
		"header_global_border_size" : ""
	},
	"page_title" : {
		"pt_alternate" : "",
	    "pt_placement" : "under",
		"pt_style" : "white",
		"pt_elements" : ["title","line","breadcrumbs","breadcrumbs_last"],
		"pt_icon" : "",
		"pt_icon_size" : "",
		"pt_icon_color" : "",
		"pt_position" : "center_right",
		"pt_title_size" : "",
		"pt_title_color" : "",
		"pt_float_enable" : "yes",
		"pt_float_text_size" : "",
		"pt_float_text_color" : "",
		"pt_bg" : "",
		"pt_bg_color" : "#f7f7f7",
		"pt_bg_opacity" : "",
		"pt_padding_top" : "",
		"pt_padding_bottom" : "",
		"pt_off" : "no"
	},
	"rev_slider" : {
		"shortcode" : "",
		"alias" : "",
		"rev_position" : "before_content",
		"hide_content" : []
	},
    "sidebar" : {
		"sidebar" : "no",
		"sidebar_dir" : "right",
		"sidebar_line" : "no",
		"sidebar_area" : "sidebar-1",
		"sidebar_visibility" : "all"
	},
	"footer" : {
		"footer" : "yes",
		"footer_line" : "yes",
		"footer_area" : "sidebar-1",
		"footer_style" : "dark",
		"footer_sticky" : "disable"
	}
}]
###*/


get_header(); ?>

<?php
if ( function_exists( 'basement_revslider' ) ) {
	/**
	 * Displays Slider Revolution.
	 *
	 * @package    Aisconverse
	 * @subpackage Conico
	 * @since      Conico 1.0
	 */
	basement_revslider();
}
?>


<?php do_action( 'conico_before_content' ); ?>
	<div class="content">
		<!-- CONTAINER -->
		<div class="container">
			<div class="row">

				<div class="<?php echo esc_attr( basement_content_classes( 'page' ) ); ?>" role="main">
					<?php
					// Start the Loop.
					while ( have_posts() ) : the_post();
						the_content();
					endwhile;
					?>
				</div>

				<?php
				if ( function_exists( 'basement_sidebar' ) ) {
					/**
					 * Displays Main sidebar. Important!
					 * For correct display of the sidebar, code 'basement_sidebar' should be on the right (after content).
					 *
					 * @package    Aisconverse
					 * @subpackage Conico
					 * @since      Conico 1.0
					 */
					basement_sidebar();
				}
				?>

			</div>
		</div> <!-- /.container -->
	</div>
<?php do_action( 'conico_after_content' ); ?>

<?php

get_footer();