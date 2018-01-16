<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Checkbox_Icon_Group extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) || empty( $this->config[ 'values' ] ) ) {
			return $this->dom->createTextNode( __( 'Checkboxes icons config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$wrapper_class = !empty( $wrapper_class ) ? $this->textdomain . $wrapper_class : '';
		$checkboxes_wrapper_class = !empty( $checkboxes_wrapper_class ) ? $this->textdomain . $checkboxes_wrapper_class : '';
		$current_value = !empty( $current_value ) ? $current_value : 0;
		$icon_class = !empty( $icon_class ) ? $icon_class : 'icon';

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$wrapper->setAttribute( 'class', $this->textdomain . '_checkboxes_wrapper ' . $wrapper_class );
		$wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );
		$checkboxes = $wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$checkboxes->setAttribute( 'class', $this->textdomain . '_icons_checkboxes ' . $checkboxes_wrapper_class );

		foreach ( $values as $value) {
			$checkbox = new Basement_Form_Input_Checkbox_Icon( array(
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
			$checkboxes->appendChild( $this->dom->importNode( $checkbox->create(), true ) );
		}
		return $wrapper;
	}
}


