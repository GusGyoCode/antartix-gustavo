<?php
/**
 * Plugin Name: Basement Portfolio
 * Plugin URI: http://aisconverse.com
 * Description: A Basement Framework portfolio
 * Version: 1.0
 * Author: Aisconverse team
 * Author URI: http://aisconverse.com
 * License: GPL2
 */

defined('ABSPATH') or die();

define( 'BASEMENT_PORTFOLIO_FILE', __FILE__ );
define( 'BASEMENT_PORTFOLIO_BASENAME', plugin_basename( __FILE__ ) );
if ( !defined( 'THEME_VERSION' ) ) {
	define( 'THEME_VERSION', '1.0' );
}
define( 'BASEMENT_PORTFOLIO_URL', WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
define( 'BASEMENT_PORTFOLIO_TEXTDOMAIN','basement_portfolio');


add_action( 'basement_loaded', 'basement_portfolio_init', 999 );

function basement_portfolio_init() {
	require 'modules/plugin/plugin.php';
}