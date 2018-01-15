<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Colorpicker extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Color picker group config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$wrapper->setAttribute( 'class', $this->textdomain . '_color ' );

		$wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );

		$classes = array( 'basement_color_picker' );
		
		if ( $class ) {
			$classes[] = $class;
		}

		$input = new Basement_Form_Input( array(
				'class' => implode( ' ', $classes ),
				'name' => $name,
				'name_attr_part' => $name_attr_part,
				'value' => $value,
				'help_text' => isset($help_text) ? $help_text : '',
				'style' => isset($style) ? $style : '',
				'attributes' => $attributes,
			)
		);
		$wrapper->appendChild( $this->import_dom_node( $input->create() ) );


		return $wrapper;
	}

}
