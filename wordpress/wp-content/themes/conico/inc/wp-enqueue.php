<?php
if ( ! function_exists( 'conico_assets' ) ) {
	/**
	 * Enqueues scripts and styles.
	 *
	 * @since Conico 1.0
	 */
	function conico_assets() {

		// Add custom fonts, used in the main stylesheet.
		$conico_gfonts = conico_google_fonts(
			array(
				'Chivo' => '300,300i,400,400i,700,700i,900,900i',
				'Nova+Mono' => '400'
			),
			'latin-ext'
		);

		wp_enqueue_style( 'conico_googlefonts', $conico_gfonts, array(), null );

		// Load the Bootstrap stylesheet
		wp_enqueue_style( 'conico_bootstrap', CONICO_CSS_PATH . 'bootstrap.min.css', array(), '3.3.7' );

		// Load Developer stylesheet.
		wp_enqueue_style( 'conico_development', CONICO_CSS_PATH . 'development.min.css', array(), CONICO_VERSION );


		// All icons for Theme
		wp_enqueue_style( 'conico_icons', CONICO_CSS_PATH . 'theme-icons.min.css', array(), CONICO_VERSION );


		// Theme stylesheet.
		wp_enqueue_style( 'conico_style', get_stylesheet_uri());


		// Load the Bootstrap JavaScript
		wp_enqueue_script( 'conico_bootstrap', CONICO_JS_PATH . 'vendor/bootstrap.min.js', array( 'jquery' ), '3.3.7', true );

		// Theme Plugins
		wp_enqueue_script( 'conico_plugins', CONICO_JS_PATH . 'vendor/plugins.min.js', array( 'jquery' ), CONICO_VERSION, true );


		// Theme JavaScript
		wp_register_script( 'conico_javascript', CONICO_JS_PATH . 'custom.js', array( 'jquery', 'conico_plugins' ), CONICO_VERSION, true );

		// Add Theme JavaScript
		wp_enqueue_script( 'conico_javascript' );

		// Localizes a registered script with data for a JavaScript variable
		wp_localize_script( 'conico_javascript', 'conico_ajax', array(
			'url' => admin_url( 'admin-ajax.php' )
		) );


		// Load the html5 shiv
		wp_enqueue_script( 'conico_html5shiv', CONICO_JS_PATH . 'vendor/html5shiv.min.js', array(), '3.7.3' );
		wp_script_add_data( 'conico_html5shiv', 'conditional', 'lt IE 9' );

		// Load the respond
		wp_enqueue_script( 'conico_respond', CONICO_JS_PATH . 'vendor/respond.src.min.js', array(), '1.4.2' );
		wp_script_add_data( 'conico_respond', 'conditional', 'lt IE 9' );


		// Load comment reply script
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	add_action( 'wp_enqueue_scripts', 'conico_assets' );
}