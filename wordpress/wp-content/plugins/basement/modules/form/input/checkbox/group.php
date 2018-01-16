<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Checkbox_Group extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) || empty( $this->config[ 'values' ] ) ) {
			return $this->dom->createTextNode( __( 'Checkboxes group config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$wrapper_class = !empty( $wrapper_class ) ? $this->textdomain . $wrapper_class : '';
		$checkboxes_wrapper_class = !empty( $checkboxes_wrapper_class ) ? $this->textdomain . $checkboxes_wrapper_class : '';

		$current_value = !empty( $current_value ) ? $current_value : 0;

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$wrapper->setAttribute( 'class', $this->textdomain . '_checkboxes_wrapper ' . $this->textdomain . $wrapper_class );
		$wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );
		$checkboxes = $wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$checkboxes->setAttribute( 'class', $this->textdomain . '_checkboxes ' . $checkboxes_wrapper_class );

		foreach ( $values as $value => $label_text) {
			$hr = $checkboxes->appendChild( $this->dom->createElement( 'div' ) );
			$hr->setAttribute('style','height:5px;');
			$checkbox = new Basement_Form_Input_Checkbox( array(
					'label_text' => $label_text,
					'name' => $name,
					'name_attr_part' => $name_attr_part,
					'current_value' => $current_value,
					'value' => $value,
					'attributes' => $attributes,
					'multiple' => true
				)
			);
			$checkboxes->appendChild( $this->dom->importNode( $checkbox->create(), true ) );
			$hr = $checkboxes->appendChild( $this->dom->createElement( 'div' ) );
			$hr->setAttribute('style','height:5px;');
		}
		return $wrapper;
	}
}
