<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Radio_Icon extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Radios icons config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$current_value = !empty( $current_value ) ? $current_value : 0;

		$radio = new Basement_Form_Input_Radio( array(
				'name' => $name,
				'name_attr_part' => $name_attr_part,
				'current_value' => $current_value,
				'value' => $value,
				'class' => $this->textdomain . '_icon_radio',
				'attributes' => $attributes,
				'no_wrapper' => true
			) 
		);

		$radio = $this->dom->importNode( $radio->create(), true );

		$icon = $radio->appendChild( $this->dom->createElement( 'span' ) );
		$icon_class = !empty( $icon_class ) ? $icon_class : 'icon';
		$icon->setAttribute( 'class', $this->textdomain . '_radio_icon ' . $icon_class . ' ' . $value );
		return $radio;
	}
}


