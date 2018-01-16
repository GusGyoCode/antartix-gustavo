<?php
/**
 * Implement Custom Header functionality for Conico
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

if ( ! function_exists( 'conico_custom_header_setup' ) ) {
	/**
	 * Set up the WordPress core custom header settings.
	 *
	 * @since Conico 1.0
	 */
	function conico_custom_header_setup() {
		/**
		 * Filter Conico custom-header support arguments.
		 *
		 * @since Conico 1.0
		 */
		add_theme_support( 'custom-header', apply_filters( 'conico_custom_header_args', array(
			'header-text'            => false,
			'wp-head-callback' => 'conico_header_style'
		) ) );
	}

	add_action( 'after_setup_theme', 'conico_custom_header_setup' );
}


if ( ! function_exists( 'conico_header_style' ) ) {
	/**
	 * Styles for header
	 *
	 * @since Conico 1.0
	 */
	function conico_header_style() {
		$header_image = get_header_image();
		?>
		<style type="text/css" id="theme-header-css">
			<?php

			if ( ! empty( $header_image ) ) {
				?>

			header.header {
				/*
				 * No shorthand so the Customizer can override individual properties.
				 * @see https://core.trac.wordpress.org/ticket/31460
				 */
				background-image: url(<?php header_image(); ?>);
				background-repeat: no-repeat;
				background-position: 50% 50%;
				-webkit-background-size: cover;
				-moz-background-size:    cover;
				-o-background-size:      cover;
				background-size:         cover;
			}
		<?php } ?>
		</style>
		<?php
	}
} // conico_header_style


if ( ! function_exists( 'conico_header_background_color_css' ) ) {
	/**
	 * Enqueues front-end CSS for the header background color.
	 *
	 * @since Conico 1.0
	 */
	function conico_header_background_color_css() {

		$default_color           = 'ffffff';
		$header_background_color = get_theme_mod( 'header_background_color', $default_color );


		// Don't do anything if the current color is the default.
		if ( $header_background_color === $default_color ) {
			return;
		}

		$css = '
		/* Custom Header Background Color */
		body {
			background-color: %1$s;
		}
	';

		wp_add_inline_style( 'theme-inline-style', sprintf( $css, $header_background_color ) );
	}

	add_action( 'wp_enqueue_scripts', 'conico_header_background_color_css', 11 );
}

