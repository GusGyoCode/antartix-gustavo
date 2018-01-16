<?php
/**
 * Plugin Name: Basement Carousel
 * Plugin URI: http://aisconverse.com
 * Description: A Basement Framework carousel with great potential
 * Version: 1.0
 * Author: Aisconverse team
 * Author URI: http://aisconverse.com
 * License: GPL2
 */

defined('ABSPATH') or die();

define('PATCH_CAROUSEL', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));

add_action( 'basement_loaded', 'basement_carousel_init', 999 );

function basement_carousel_init() {
	require 'modules/plugin/plugin.php';
}