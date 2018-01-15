<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Textarea extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Textarea config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$textarea = $this->dom->createElement( 'textarea', $value );
		$textarea->setAttribute( 'value', $value );
		$textarea->setAttribute( 'name', $this->create_name( $name, $name_attr_part ) );

		if ( $class ) {
			$textarea->setAttribute( 'class', $class );
		}

		if ( $id || $id = $this->create_id( $name, $name_attr_part )  ) {
			$textarea->setAttribute( 'id', $id );
		}

		$textarea->setAttribute( 'autocomplete', $autocomplete );

		$textarea = $this->append_dom_node_attributes( $textarea, $attributes );

		if ( !$no_wrapper ) {
			$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
			$wrapper->setAttribute( 'class', $this->textdomain . '_input_wrapper ' );

			if ( !empty( $label_text ) ) {
				$label = $wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );
				$label->setAttribute( 'for', $id  );
			}
			$wrapper->appendChild( $textarea );
			return $wrapper;
		}

		return $textarea;
	}

}
