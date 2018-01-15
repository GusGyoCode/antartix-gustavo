<?php
defined( 'ABSPATH' ) or die();

class Basement_Shortcode_Blockquote_Tag extends Basement_Shortcode {
	protected $enclosing = true;

	public function section_config( $config = array() ) {

		$config = array(
			'description' => __( 'For quoting blocks of content from another source within your document.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
			'blocks'      => array(
				array(
					'type'        => 'radio',
					'title'       => __( 'Text align.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
					'description' => __( 'Choose alignment.', BASEMENT_SHORTCODES_TEXTDOMAIN ),
					'param'       => 'align',
					'input'       => array(
						'values' => array(
							'0'     => __( 'Left', BASEMENT_SHORTCODES_TEXTDOMAIN ),
							'right' => __( 'Right', BASEMENT_SHORTCODES_TEXTDOMAIN ),
						)
					)
				)
			)
		);

		return $config;

	}

	public function render( $atts = array(), $content = '' ) {

		extract( $atts = wp_parse_args( $atts, array(
			'align' => ''
		) ) );

		$dom            = new DOMDocument( '1.0', 'UTF-8' );
		$blockquote_tag = $dom->appendChild( $dom->createElement( 'blockquote', '%cont' ) );


		if ( ! empty( $align ) ) {
			$blockquote_tag->setAttribute( 'class', 'blockquote-reverse' );
		}

		return str_replace( '%cont', do_shortcode( $content ), $dom->saveHTML() );

	}
}
