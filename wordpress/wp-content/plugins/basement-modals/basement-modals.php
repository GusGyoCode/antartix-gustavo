<?php
/**
 * Plugin Name: Basement Modal Windows
 * Plugin URI: http://aisconverse.com
 * Description: A Basement Framework dynamic modal windows with great potential
 * Version: 1.0
 * Author: Aisconverse team
 * Author URI: http://aisconverse.com
 * License: GPL2
 */

defined( 'ABSPATH' ) or die();

define( 'PATH_MODALS', WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );

add_action( 'basement_loaded', 'basement_modals_init', 999 );

function basement_modals_init() {
	require 'modules/plugin/plugin.php';
}