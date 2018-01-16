<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Checkbox_Icon extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) || empty( $this->config[ 'values' ] ) ) {
			return $this->dom->createTextNode( __( 'Checkboxes icon config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$current_value = !empty( $current_value ) ? $current_value : 0;
		
		$checkbox = new Basement_Form_Input_Checkbox( array(
				'name' => $name,
				'name_attr_part' => $name_attr_part,
				'current_value' => $current_value,
				'value' => $value,
				'class' => $this->textdomain . '_icon_checkbox',
				'attributes' => $attributes,
				'no_wrapper' => true,
				'multiple' => true
			) 
		);
		$checkbox = $this->dom->importNode( $checkbox->create(), true );

		$icon = $checkbox->appendChild( $this->dom->createElement( 'span' ) );
		$icon_class = !empty( $icon_class ) ? $icon_class : 'icon';
		$icon->setAttribute( 'class', $this->textdomain . '_checkbox_icon ' . $icon_class . ' ' . $value );
		
		return $checkbox;
	}
}


