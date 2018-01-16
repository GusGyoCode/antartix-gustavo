<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Checkbox extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Checkbox input config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		if ( empty( $current_value ) ) {
			$current_value = 0;
		}

		if ( !empty( $multiple ) && $multiple ) {
			$name_attr_part = $this->create_name( $name, $name_attr_part );
			$name = $value;
		}

		$label = $this->dom->appendChild( $this->dom->createElement( 'label') );
		$label->setAttribute( 'for', $this->create_id( $this->create_name( $name, $name_attr_part ) ) );

		if ( empty( $no_hidden ) || !$no_hidden ) {
			$hidden_value = !isset( $hidden_value ) ? '' : $hidden_value ;
			$hidden = new Basement_Form_Input_Hidden( array(
					'name' => $name,
					'name_attr_part' => $name_attr_part,
					'value' => $hidden_value,
					'no_id' => true
				) 
			);
			$label->appendChild( $this->dom->importNode( $hidden->create(), true ) );
		} 

		$input = new Basement_Form_Input( array(
				'type' => 'checkbox',
				'name' => $name,
				'name_attr_part' => $name_attr_part,
				'value' => $value,
				'class' => $class,
				'attributes' => $attributes,
				'no_wrapper' => true
			)
		);

		$checkbox_input = $label->appendChild( $this->import_dom_node ( $input->create() ) );


		if ( $current_value === $value || ( is_array( $current_value ) && in_array( $value, $current_value ) ) ) {
			$checkbox_input->setAttribute( 'checked', 'checked' );
		}

		if ( !empty( $label_text ) ) {
			$label->appendChild( $this->dom->createElement( 'span', esc_html($label_text) ) );
		}
		return $label;
	}

}
