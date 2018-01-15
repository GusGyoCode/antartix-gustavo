<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Submit extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		get_submit_button( );
		$submit = $this->dom->createDocumentFragment();
		$submit->appendXML( get_submit_button( null, 'primary' ) );
		return $submit;
	}

}
