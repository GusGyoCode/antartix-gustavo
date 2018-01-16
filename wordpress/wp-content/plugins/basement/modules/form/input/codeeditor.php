<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Codeeditor extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Code editor config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$editor = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$editor->setAttribute( 'class', 'basement_form_note_code_wrapper ' );
		$editor->appendChild( $this->dom->createElement( 'label', $label_text ) );
		$textarea = new Basement_Form_Input_Textarea( array(
				'name' => $name,
				'name_attr_part' => $name_attr_part,
				'class' => 'basement_code_editor',
				'value' => $value,
				'attributes' => array(
					'data-editor-mode' => $editor_mode ? $editor_mode : 'text/html'
				)
			) 
		);

		$editor->appendChild( $this->dom->importNode( $textarea->create(), true ) );

		if ( !empty( $note ) ) {
			$note = $editor->appendChild( $this->dom->createElement( 'div', $note ) );
			$note->setAttribute( 'class', 'basement_form_note' );
		}

		return $editor;
	}

}
