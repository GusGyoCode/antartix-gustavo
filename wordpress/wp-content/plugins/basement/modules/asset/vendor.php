<?php
defined('ABSPATH') or die();

class Basement_Asset_Vendor {

	private static $instance = null;
	private static $assets = array();
	private static $handle_prefix = 'basement_asset_vendor_';
	private $version = '1.0.0';

	public static function init() {
		return self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Asset_Vendor();
		}
		return self::$instance;
	}

	public function __construct() {

	}

	public static function handler( $name) {
		return self::$handle_prefix . $name;
	}

	public static function enqueue( $name ) {
		if ( empty( self::$assets[ $name ] ) ) {
			return;
		}

		foreach ( self::$assets[ $name ] as $asset) {
			self::_enqueue( $asset );
		}
	}

	private static function _enqueue( $asset ) {
		if ( empty( $asset[ 'src' ] ) ) {
			return;
		}

		$asset = wp_parse_args( $asset, array(
			'handle' => md5( time() ),
			'deps' => array(),
			'ver' => 1
		) );

		if ( ( !empty( $asset[ 'type' ] ) && 'js' == $asset[ 'type' ] ) || '.js' == substr( $asset[ 'src' ], strlen( $asset[ 'src' ] ) - 3 ) ) {
			$asset = wp_parse_args( $asset, array(
				'in_footer' => true
			) );
			Basement_Asset::add_script( $asset[ 'handle'], $asset[ 'src' ], $asset[ 'deps' ], $asset[ 'ver' ], $asset[ 'in_footer' ] );
		} else {
			$asset = wp_parse_args( $asset, array(
				'media' => null
			) );
			Basement_Asset::add_style( $asset[ 'handle'], $asset[ 'src' ], $asset[ 'deps' ], $asset[ 'ver' ], $asset[ 'media' ] );
		}
	}
}

Basement_Asset_Vendor::init();

