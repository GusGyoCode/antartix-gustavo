<?php

if ( ! function_exists( 'conico_child_enqueue_styles' ) ) {
	/**
	 * Conditional enqueue styles.
	 *
	 * @since Conico Child 1.0
	 */
	function conico_child_enqueue_styles() {

		$parent_style = 'conico_style'; // This is 'conico_style' for the Conico theme.

		wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array(
			'conico_bootstrap',
			'conico_development',
			'conico_googlefonts',
			'conico_icons'
		) );

		wp_register_style( 'conico_child_style',
			get_stylesheet_directory_uri() . '/style.css',
			array( $parent_style ),
			wp_get_theme()->get( 'Version' )
		);
		wp_enqueue_style('conico_child_style');
	}

	add_action( 'wp_enqueue_scripts', 'conico_child_enqueue_styles' );
}
// Your php code goes here