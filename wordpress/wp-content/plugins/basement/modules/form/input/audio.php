<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Audio extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( 'Audio input config is broken', BASEMENT_TEXTDOMAIN );
		}

		extract( $this->config );

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$wrapper_classes = array( $this->textdomain . '_media_uploader_wrapper' );

		$audio_data = null;
		$data = array();
		if ( (int)$value ) {
			$audio_data = wp_get_attachment_metadata($value);
			if ( $audio_data[ 'artist' ] ) {
				$data[ __( 'Artist', BASEMENT_TEXTDOMAIN ) ] = $audio_data[ 'artist' ];
			}
			if ( $audio_data[ 'album' ] ) {
				$data[ __( 'Album', BASEMENT_TEXTDOMAIN ) ] = $audio_data[ 'album' ];
			}
			if ( $audio_data[ 'title' ] ) {
				$data[ __( 'Title', BASEMENT_TEXTDOMAIN ) ] = $audio_data[ 'title' ];
			}
		}

		if ( !empty( $audio_data ) ) {
			$wrapper_classes[] = 'file_loaded';
		}

		$wrapper->setAttribute( 'class', implode( ' ', $wrapper_classes ) );

		$data_wrapper = $wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$data_wrapper->setAttribute( 'class', 'basement_metabox_audio_data_wrapper' );
		$data_wrapper_id = 'basement_metabox_audio_data_wrapper_' . mt_rand();
		$data_wrapper->setAttribute( 'id', $data_wrapper_id );
		foreach ( $data as $data_key => $data_value ) {
			$data = $data_wrapper->appendChild( $this->dom->createElement( 'div' ) );
			$data->appendChild( $this->dom->createElement( 'b', $data_key . ': ' ) );
			$data->appendChild( $this->dom->createTextNode( $data_value ) );
		}

		if ( !empty( $label_text ) ) {
			$label = $wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );
			$label->setAttribute( 'for', $id  );
		}

		$input_id = $this->create_id( $name, $name_attr_part );

		$input = new Basement_Form_Input_Hidden( array(
				'name' => $name,
				'id' => $input_id,
				'name_attr_part' => $name_attr_part,
				'value' => $value
			) 
		);

		$text_buttons = ( !empty( $text_buttons ) && $text_buttons ) ? true : fasle ;

		$wrapper->appendChild( $this->import_dom_node( $input->create() ) );

		$media_upload_button_attributes = array(
			'data-value-receivers' => '#' . $input_id,
			'data-library-type' => 'audio'
		);

		if ( empty( $upload_text ) ) {
			$upload_text = __( 'Choose audio', BASEMENT_TEXTDOMAIN );
		}

		if ( empty( $delete_text ) ) {
			$delete_text = __( 'Delete audio', BASEMENT_TEXTDOMAIN );
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
				'class' => 'basement_update_nodes',
				'attributes' => array(
					'data-action' => 'click',
					'data-value-receivers' => '#' . $input_id,
					'data-remove-node' => '#' . $data_wrapper_id
				)
			)
		);

		$wrapper->appendChild( $this->import_dom_node( $media_delete_button->create() ) );

		return $wrapper;
	}

}
