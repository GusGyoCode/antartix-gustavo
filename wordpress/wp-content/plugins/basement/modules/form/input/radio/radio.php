<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Radio extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Checkbox input config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		if ( empty( $current_value ) ) {
			$current_value = 0;
		}

		$id = $this->create_id( $this->create_name( $name, $name_attr_part ) . '_' . $value );
		$label = $this->dom->appendChild( $this->dom->createElement( 'label') );
		$label->setAttribute( 'for', $id  );

		$current_value = (string)$current_value;
		$value = (string)$value;

		if ( $current_value === $value ) {
			$attributes[ 'checked' ] = "checked";
		}

		$input = new Basement_Form_Input( array(
				'type' => 'radio',
				'name' => $name,
				'name_attr_part' => $name_attr_part,
				'id' => $id,
				'value' => $value,
				'class' => $class,
				'attributes' => $attributes,
				'no_wrapper' => true
			)
		);

		$radio_input = $label->appendChild( $this->import_dom_node( $input->create() ) );

		if ( !empty( $label_text ) ) {
			$label->appendChild( $this->dom->createElement( 'span', esc_html( $label_text ) ) );
		}

		return $label;
	}

}
