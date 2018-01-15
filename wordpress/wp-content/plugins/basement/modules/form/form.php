<?php
defined('ABSPATH') or die();

class Basement_Form {
	public static $instance = null;
	private $textdomain = 'basement_form';
	private $version = '1.0.0';
	private $dom;

	public function __construct() {
		$this->dom = new DOMDocument( '1.0', 'UTF-8' );
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Form();
		}
		return self::$instance;
	}

	public function create_input( $config ) {
		$input = new Basement_Form_Input( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_hidden_input( $config ) {
		$input = new Basement_Form_Input_Hidden( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_select( $config ) {
		$input = new Basement_Form_Input_Select( $config );
		return $this->dom->importNode( $input->create(), true );
	}
	public function create_selectext( $config ) {
		$input = new Basement_Form_Input_Selectext( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_checkbox( $config ) {
		$input = new Basement_Form_Input_Checkbox( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_radio( $config ) {
		$input = new Basement_Form_Input_Radio( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_checkboxes( $config ) {
		$input = new Basement_Form_Input_Checkbox_Group( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_radios( $config ) {
		$input = new Basement_Form_Input_Radio_Group( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_icons_radios( $config ) {
		$input = new Basement_Form_Input_Radio_Icon_Group( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_icons_checkboxes( $config ) {
		$input = new Basement_Form_Input_Checkbox_Icon_Group( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_sortable_posts_list_checkboxes( $config ) {
		$input = new Basement_Form_Input_Checkbox_Post_List_Sortable( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_posts_list_radios( $config ) {
		$input = new Basement_Form_Input_Radio_Post_List( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_textarea( $config ) {
		$input = new Basement_Form_Input_Textarea( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_submit( $config = array() ) {
		$input = new Basement_Form_Input_Submit( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_colorpicker( $config ) {
		$input = new Basement_Form_Input_Colorpicker( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_editor( $config ) {
		$input = new Basement_Form_Input_Editor( $config );
		return $this->dom->importNode( $input->create(), true );
		
	}

	public function create_code_editor( $config ) {
		$input = new Basement_Form_Input_Codeeditor( $config );
		return $this->dom->importNode( $input->create(), true );
		
	}

	public function create_media_upload_button( $config ) {
		$input = new Basement_Form_Input_Media_Button_Upload( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_media_delete_button( $config ) {
		$input = new Basement_Form_Input_Media_Button_Delete( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_background_input( $config ) {
		$input = new Basement_Form_Input_Background( $config );
		return $this->dom->importNode( $input->create(), true );
	}


	public function create_background_bootstrap_input( $config ) {
		$input = new Basement_Form_Input_Shbackground( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_googlefonts_block($config) {
		$input = new Basement_Form_Input_Googlefonts( $config );
		return $this->dom->importNode( $input->create(), true );
	}


	public function create_image_upload( $config ) {
		$input = new Basement_Form_Input_Image( $config );
		return $this->dom->importNode( $input->create(), true );
	}

	public function create_from_config( $config ) {
		$config[ 'type' ] = !empty( $config[ 'type' ] ) ? $config[ 'type' ] : 'text' ;
		switch ( $config[ 'type' ] ) {
			case 'hidden':
				$input = $this->create_hidden_input( $config );
				break;
			case 'checkbox':
				$input = $this->create_checkbox( $config );
				break;
			case 'image':
				$input = $this->create_image_upload( $config );
				break;
			case 'colorpicker':
				$input = $this->create_colorpicker( $config );
				break;
			case 'editor':
				$input = $this->create_editor( $config );
				break;
			case 'codeeditor':
				$input = $this->create_code_editor( $config );
				break;
			case 'radios':
				$input = $this->create_radios( $config );
				break;
			case 'checkboxes':
				$input = $this->create_checkboxes( $config );
				break;
			case 'textarea':
				$input = $this->create_textarea( $config );
				break;
			case 'select':
				$input = $this->create_select( $config );
				break;
			case 'icons':
				$input = $this->create_icons_radios( $config );
				break;
			case 'background':
				$input = $this->create_background_input( $config );
				break;
			case 'shbackground':
				$input = $this->create_background_bootstrap_input( $config );
				break;
			case 'googlefonts':
				$input = $this->create_googlefonts_block( $config );
				break;
			case 'posts':
				$input = $this->create_posts_list_radios( $config );
				break;
			case 'sortable_posts':
				$input = $this->create_sortable_posts_list_checkboxes( $config );
				break;
			case 'text':
			case 'email':
			case 'url':
			case 'date':
				$input = $this->create_input( $config );
				break;
			default:
				$input = $this->dom->createTextNode( __( 'Input config is broken', BASEMENT_TEXTDOMAIN ) );
				break;
		}
		
		return $input;
	}
}