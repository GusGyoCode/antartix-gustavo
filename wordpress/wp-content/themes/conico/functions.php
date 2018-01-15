<?php

/**
 * Define Version Constant
 */
if ( ! defined( 'CONICO_VERSION' ) ) {
	define( 'CONICO_VERSION', '1.0' );
}

/**
 * Define Language TextDomain
 */
if ( ! defined( 'CONICO_TEXTDOMAIN' ) ) {
	define( 'CONICO_TEXTDOMAIN', wp_get_theme()->get_template() );
}

/**
 * Define Template URL
 */
if ( ! defined( 'CONICO_TEMPLATE_URL' ) ) {
	define( 'CONICO_TEMPLATE_URL', trailingslashit( get_template_directory_uri() ) );
}

/**
 * Define Images URL
 */
if ( ! defined( 'CONICO_IMG_PATH' ) ) {
	define( 'CONICO_IMG_PATH', CONICO_TEMPLATE_URL . 'assets/images/' );
}

/**
 * Define CSS URL
 */
if ( ! defined( 'CONICO_CSS_PATH' ) ) {
	define( 'CONICO_CSS_PATH', CONICO_TEMPLATE_URL . 'assets/css/' );
}

/**
 * Define JS URL
 */
if ( ! defined( 'CONICO_JS_PATH' ) ) {
	define( 'CONICO_JS_PATH', CONICO_TEMPLATE_URL . 'assets/js/' );
}

/**
 * Define Template Dir
 */
if ( ! defined( 'CONICO_DIR' ) ) {
	define( 'CONICO_DIR', trailingslashit( get_template_directory() ) );
}

/**
 * Define WooCommerce Path
 */
if ( ! defined( 'CONICO_WOO_PATH' ) ) {
	define( 'CONICO_WOO_PATH', CONICO_TEMPLATE_URL . 'woocommerce/' );
}


/**
 * Check Basement Enable
 *
 * @since Conico 1.0
 */
if ( ! basement_enabled() ) {
	require( trailingslashit( get_template_directory() ) . 'inc/no-basement/common.php' );
}
function basement_enabled() {
	return in_array( 'basement/basement.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}


/**
 * Check if breadcrumbs plugin is enable
 *
 * @since Conico 1.0
 */
function conico_breadcrumbs_navtx_enable() {
	return in_array( 'breadcrumb-navxt/breadcrumb-navxt.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}


/**
 * Check VisualComposer Enable
 *
 * @since Conico 1.0
 */
function conico_vc_enabled() {
	return in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}


/**
 * Check WPML Enable
 *
 * @since Conico 1.0
 */
function conico_wpml_enabled() {
	$wpml_options = get_option( 'icl_sitepress_settings' ); // hack is language not set

	$default_lang = isset( $wpml_options['default_language'] ) ? $wpml_options['default_language'] : '';
	if ( $default_lang ) {
		$default_lang = true;
	} else {
		$default_lang = false;
	}

	return (in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && $default_lang) || defined('FAKE_WPML');
}


/**
 * Check WooCommerce Enable
 *
 * @since Conico 1.0
 */
function conico_woo_enabled() {
	return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}


/**
 * Conico only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}


/**
 * Set up the content width value based on the theme's design.
 *
 * @since Conico 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 500;
}


/**
 * Main Settings
 *
 * @since Conico 1.0
 */
require( trailingslashit( get_template_directory() ) . 'inc/settings.php' );


/**
 * Include JS/CSS
 *
 * @since Conico 1.0
 */
require( trailingslashit( get_template_directory() ) . 'inc/wp-enqueue.php' );


/**
 * Custom template tags for this theme.
 *
 * @since Conico 1.0
 */
require( trailingslashit( get_template_directory() ) . 'inc/template-tags.php' );


/**
 * Implement the Custom Header feature.
 *
 * @since Conico 1.0
 */
require( trailingslashit( get_template_directory() ) . 'inc/custom-header.php' );


/**
 * Visual Composer Settings
 *
 * @since Conico 1.0
 */
require( trailingslashit( get_template_directory() ) . 'inc/visual-composer.php' );


/**
 * Load TGM
 *
 * @since Conico 1.0
 */
require( trailingslashit( get_template_directory() ) . 'inc/tgm/tgm.php' );