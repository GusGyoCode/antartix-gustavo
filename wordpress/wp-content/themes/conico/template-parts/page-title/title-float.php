<?php
/**
 * The template part for displaying the elements in float page title
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */


if ( function_exists( 'Basement_Page_Title' ) ) {

	$basement_page_title = Basement_Page_Title();

	$title = '';
	$styles = array();
	$dash_styles = array();

	$alternate   = isset( $basement_page_title['pt_alternate'] ) ? $basement_page_title['pt_alternate'] : '';
	$pt_float_text_color = isset( $basement_page_title['pt_float_text_color'] ) ? $basement_page_title['pt_float_text_color'] : '';
	$pt_float_text_size = isset( $basement_page_title['pt_float_text_size'] ) ? $basement_page_title['pt_float_text_size'] : '';


	if(!empty($pt_float_text_color)) {
		$styles[] = "color:{$pt_float_text_color};";
		$dash_styles[] = "background-color:{$pt_float_text_color};";
	}

	if(!empty($pt_float_text_size)) {
		$styles[] = "font-size:{$pt_float_text_size}px;";
	}


	if(!empty($styles)) {
		$styles = 'style="'.implode('', $styles).'"';
	} else {
		$styles = '';
	}

	if(!empty($dash_styles)) {
		$dash_styles = 'style="'.implode('', $dash_styles).'"';
	} else {
		$dash_styles = '';
	}

	?>

	<div class="page-title-float">
		<?php
		if(is_singular('post') || is_page()) {
			if ( empty( $alternate ) ) {
				$title = get_the_title();
			} else {
				if ( function_exists( 'basement_the_title_alternative' ) ) {
					$title = basement_the_title_alternative( true );
				}
			}
		} elseif (is_singular( 'single_project' )) {
			if ( empty( $alternate ) ) {
				if ( function_exists( 'basement_single_project_page_title' ) ) {
					$title = basement_single_project_page_title( false );
				}
			} else {
				if ( function_exists( 'basement_the_title_alternative' ) ) {
					$title = basement_the_title_alternative( true );
				}
			}
		}
		?>

		<?php printf('<div class="page-title-rotate" %s><ins %s></ins><span>%s</span></div>', $styles, $dash_styles, trim( $title ) ); ?>
	</div>
	<?php

}