<?php
/**
 * Plugin Name: Basement Shortcodes
 * Plugin URI: http://aisconverse.com
 * Description: A Basement Framework shortcodes builder
 * Version: 1.0
 * Author: Aisconverse team
 * Author URI: http://aisconverse.com
 * License: GPL2
 */


defined('ABSPATH') or die();


define( 'BASEMENT_SHORTCODES_FILE', __FILE__ );
define( 'BASEMENT_SHORTCODES_BASENAME', plugin_basename( __FILE__ ) );
if ( !defined( 'THEME_VERSION' ) ) {
	define( 'THEME_VERSION', '1.0' );
}
define( 'BASEMENT_SHORTCODES_URL', WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));

define( 'BASEMENT_SHORTCODES_IMG', BASEMENT_SHORTCODES_URL . 'assets/images/');

define ('BASEMENT_SORTCODES_DIR', plugin_dir_path( __FILE__ ));

add_action( 'basement_loaded', 'basement_shortcodes_init', 999 );

function basement_shortcodes_init() {
	require 'modules/plugin/plugin.php';
}
