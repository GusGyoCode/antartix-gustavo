<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Editor extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Editor config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );
		ob_start();
		
		$editor_params = !empty( $editor_params ) ? $editor_params : array();
		if ( empty( $editor_params[ 'textarea_name'] ) ) {
			$editor_params[ 'textarea_name'] = $this->config['name'];
		}
		wp_editor( $value, $this->create_id( $name, $name_attr_part ), $editor_params );

		$editor = ob_get_clean();

		if ( !empty( $label_text ) ) {
			$editor = '<label>' . $label_text . '</label>' . $editor;
		}

		$editor = '<div class="basement_editor_wrapper">' . $editor . '</div>';

		$fragment = $this->dom->createDocumentFragment();
		$fragment->appendXML( $editor );
		return $fragment;

	}

}
