<?php
defined('ABSPATH') or die();

class Basement_Url {

	private static $version = '1.0.0';

	public static function of_file( $file = '' ) {
		if ( !is_string( $file ) ) {
			return '';
		}
		/*$file = realpath( str_replace( '\\', '/', $file ) );
		$content_dir = str_replace( '\\', '/', untrailingslashit( dirname( dirname( get_stylesheet_directory() ) ) ) );
		$content_url = untrailingslashit( dirname( dirname( get_stylesheet_directory_uri() ) ) );
		$url = str_replace( $content_dir, $content_url, $file );*/

		return untrailingslashit($file);
	}

}