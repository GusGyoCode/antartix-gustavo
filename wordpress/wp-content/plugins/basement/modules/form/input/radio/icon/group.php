<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Radio_Icon_Group extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) || empty( $this->config[ 'values' ] ) ) {
			return $this->dom->createTextNode( __( 'Radios icons group config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$wrapper_class = !empty( $wrapper_class ) ? $this->textdomain . $wrapper_class : '';
		$radios_wrapper_class = !empty( $radios_wrapper_class ) ? $this->textdomain . $radios_wrapper_class : '';
		$current_value = !empty( $current_value ) ? $current_value : 0;
		$icon_class = !empty( $icon_class ) ? $icon_class : 'icon';

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$wrapper->setAttribute( 'class', $this->textdomain . '_radios_wrapper ' . $wrapper_class );
		$wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );
		$radios = $wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$radios->setAttribute( 'class', $this->textdomain . '_icons_radios ' . $radios_wrapper_class );

		foreach ( $values as $value) {
			$radio = new Basement_Form_Input_Radio_Icon( array(
					'name' => $name,
					'name_attr_part' => $name_attr_part,
					'current_value' => $current_value,
					'value' => $value,
					'class' => $this->textdomain . '_icon_radio',
					'attributes' => $attributes,
					'no_wrapper' => true,
					'icon_class' => $icon_class
				) 
			);

			$radios->appendChild( $this->dom->importNode( $radio->create(), true ) );
		}
		return $wrapper;
	}

}
