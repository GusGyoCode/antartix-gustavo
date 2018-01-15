<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Media_Button_Delete extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {

		extract( $this->config );

		if ( !isset( $text ) || !$text ) {
			$text = __( 'Delete', BASEMENT_TEXTDOMAIN );
		}
		
		$button = $this->dom->createElement( 'a', $text );
		$button_classes = array( 'basement_media_delete' );

		if ( !empty( $class ) ) {
			$button_classes[] = $class;
		}

		if ( empty( $text_button ) || !$text_button ) {
			$button_classes[] = 'button-secondary';
		} else {
			$button_classes[] = 'basement_media_button_link';
		}
		$button->setAttribute( 'class', implode( ' ', $button_classes ) );

		if ( !empty( $id ) ) {
			$button->setAttribute( 'id', $id );
		}

		$button = $this->append_dom_node_attributes( $button, $attributes );
		return $button;
	}

}