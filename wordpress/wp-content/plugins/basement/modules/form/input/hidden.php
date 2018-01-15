<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Hidden extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( 'Hidden input config is broken', BASEMENT_TEXTDOMAIN );
		}

		extract( $this->config );

		$input = $this->dom->createElement( 'input' );
		$input->setAttribute( 'type', 'hidden' );
		$input->setAttribute( 'value', $value );
		$input->setAttribute( 'name', $this->create_name( $name, $name_attr_part ) );

		if ( ( empty( $no_id ) || !$no_id ) ) {
			$id = $id ? $id : $this->create_id( $name, $name_attr_part );
			if ( $id ) {
				$input->setAttribute( 'id', $id );
			}
		}

		if ( $class ) {
			$input->setAttribute( 'class', $class );
		}

		$input->setAttribute( 'autocomplete', 'off' );
		$input = $this->append_dom_node_attributes( $input, $attributes );
		return $input;
	}

}
