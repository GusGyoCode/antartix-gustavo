<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Image extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( 'Image input config is broken', BASEMENT_TEXTDOMAIN );
		}

		extract( $this->config );

		$src = '';
		if ( (int)$value ) {
			$src = wp_get_attachment_image_src( $value, 'full' );
			$src = ( $src && isset( $src[ 0 ] ) ) ? $src[0] : '';
		}

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$wrapper_classes = array( $this->textdomain . '_media_uploader_wrapper' );

		if ( $src ) {
			$wrapper_classes[] = 'file_loaded';
		}

		$wrapper->setAttribute( 'class', implode( ' ', $wrapper_classes ) );

		if ( !empty( $label_text ) ) {
			$label = $wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );
			$label->setAttribute( 'for', $id  );
		}

		$image_example = $wrapper->appendChild( $this->dom->createElement( 'img' ) );

		$image_example->setAttribute( 'src', $src );
		$image_example_id = $this->create_id( $name, $name_attr_part ) . '_image';
		$image_example->setAttribute( 'id', $image_example_id );

		$input_id = $this->create_id( $name, $name_attr_part );

		$input = new Basement_Form_Input_Hidden( array(
				'name' => $name,
				'id' => $input_id,
				'name_attr_part' => $name_attr_part,
				'value' => $value
			) 
		);

		$text_buttons = ( !empty( $text_buttons ) && $text_buttons ) ? true : false ;

		$wrapper->appendChild( $this->import_dom_node( $input->create() ) );

		$media_upload_button_attributes = array(
			'data-value-receivers' => '#' . $input_id,
			'data-src-receivers' => '#' . $image_example_id,
		);

		if ( empty( $upload_text ) ) {
			$upload_text = __( 'Choose image', BASEMENT_TEXTDOMAIN );
		}

		if ( empty( $delete_text ) ) {
			$delete_text = __( 'Delete image', BASEMENT_TEXTDOMAIN );
		}

		if ( !empty( $frame_title ) ) {
			$media_upload_button_attributes[ 'data-frame-title' ] = $frame_title;
		}

		if ( !empty( $frame_button_text ) ) {
			$media_upload_button_attributes[ 'data-button-text' ] = $frame_button_text;
		}

		$media_upload_button = new Basement_Form_Input_Media_Button_Upload( array(
				'text' => $upload_text,
				'text_button' => $text_buttons,
				'attributes' => $media_upload_button_attributes
			)
		);

		$wrapper->appendChild( $this->import_dom_node( $media_upload_button->create() ) );

		$media_delete_button = new Basement_Form_Input_Media_Button_Delete( array(
				'text' => $delete_text,
				'text_button' => $text_buttons,
				'attributes' => array(
					'data-action' => 'click',
					'data-value-receivers' => '#' . $input_id,
					'data-src-receivers' => '#' . $image_example_id,
				)
			)
		);

		$wrapper->appendChild( $this->import_dom_node( $media_delete_button->create() ) );

		return $wrapper;
	}

}
