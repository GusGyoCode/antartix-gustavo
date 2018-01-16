<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Background extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Background input config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );

		$background_option = $wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$background_option->setAttribute( 'class', $this->textdomain . '_custom_background_option ' . $this->textdomain . '_custom_background_image_wrapper' );

		$background_upload_wrapper = $background_option->appendChild( $this->dom->createElement( 'div' ) );
		$background_upload_wrapper->setAttribute( 'class', 'basement_form_media_uploader_wrapper' . ( empty( $options['image'] ) ? '': ' file_loaded' ) );

		$background_example_wrapper = $background_upload_wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$background_example_wrapper->setAttribute( 'class', $this->textdomain . '_background_example_wrapper' );

		$background_example = $background_example_wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$background_example->setAttribute( 'class', $this->textdomain . '_background_example' );
		$background_example_id = $this->create_id( 'background_example', $name_attr_part );
		$background_example->setAttribute( 'id', $background_example_id );
		$background_example_image_src = empty( $options['image'] ) ? '' : wp_get_attachment_url( $options['image'] );
		$background_example->setAttribute( 'style',
			'background-color:' . ( isset( $options[ 'color' ] ) ? $options[ 'color' ] : 'transparent' ) . ';' .
			'background-image: url(' . esc_attr( $background_example_image_src ) . ');' .
			'background-position:' . ( isset( $options[ 'position' ] ) ? $options[ 'position' ] : 'left top' ) . ';' .
			'background-repeat:' . ( isset( $options[ 'repeat' ] ) ? $options[ 'repeat' ] : 'no-repeat' ) . ';' .
			'background-size:' . ( isset( $options[ 'size' ] ) ? $options[ 'size' ] : 'auto' ) . ';' .
			'background-attachment:' . ( isset( $options[ 'attachment' ] ) ? $options[ 'attachment' ] : 'scroll' ) . ';'
		);

		$input = new Basement_Form_Input_Hidden( array(
				'name' => 'image',
				'name_attr_part' => $name_attr_part,
				'value' => empty( $options['image'] ) ? '' : $options['image']
			) 
		);

		$background_upload_wrapper->appendChild( $this->dom->importNode( $input->create(), true ) );

		$background_option_id = $this->create_id( 'image', $name_attr_part );

		$media_upload_button = new Basement_Form_Input_Media_Button_Upload( array(
				'text' => __( 'Choose Image', BASEMENT_TEXTDOMAIN ),
				'attributes' => array(
					'data-value-receivers' => '#' . $background_option_id,
					'data-background-image-receivers' => '#' . $background_example_id
				)
			)
		);

		$background_upload_wrapper->appendChild( $this->dom->importNode( $media_upload_button->create(), true ) );

		$media_delete_button = new Basement_Form_Input_Media_Button_Delete( array(
				'text' => __( 'Delete Image', BASEMENT_TEXTDOMAIN ),
				'attributes' => array(
					'data-action' => 'click',
					'data-value-receivers' => '#' . $background_option_id,
					'data-background-image-receivers' => '#' . $background_example_id,
				)
			)
		);

		$background_upload_wrapper->appendChild( $this->dom->importNode( $media_delete_button->create(), true ) );

		/**
		 * Background color
		 */
		$colorpicker = new Basement_Form_Input_Colorpicker( array(
				'label_text' => __( 'Color', BASEMENT_TEXTDOMAIN ),
				'name' => 'color',
				'name_attr_part' => $name_attr_part,
				'value' => empty( $options[ 'color' ] ) ?  '' : $options['color'],
				'class' => 'basement_update_receivers',
				'attributes' => array(
					'data-background-color-receivers' => '#' . $background_example_id
				)
			) 
		);

		$background_option->appendChild( $this->dom->importNode( $colorpicker->create(), true ) );

		$opacity_input = new Basement_Form_Input( array(
				'label_text' => __( 'Opacity', BASEMENT_TEXTDOMAIN ),
				'name' => 'opacity',
				'name_attr_part' => $name_attr_part,
				'value' => empty( $options[ 'opacity' ] ) ?  '' : ( float )$options['opacity']
			) 
		);

		$background_option->appendChild( $this->dom->importNode( $opacity_input->create(), true ) );

		$row = $background_option->appendChild( $this->dom->createElement( 'div' ) );
		$row->setAttribute( 'class', 'basement_row' );


		// TODO: check options (not working) + change radios to selects
		/**
		 * Background repeat
		 */
		$column = $row->appendChild( $this->dom->createElement( 'div' ) );
		$column->setAttribute( 'class', 'basement_column basement_half_column' );
		
		$radios = new Basement_Form_Input_Radio_Group( array(
				'label_text' => __( 'Repeat', BASEMENT_TEXTDOMAIN ),
				'wrapper_class' => '_background_repeat',
				'radios_wrapper_class' => '_custom_background_option',
				'name' => 'repeat',
				'name_attr_part' => $name_attr_part,
				'values' => array(
					'nope' => __( 'Nope', BASEMENT_TEXTDOMAIN ),
					'no-repeat' => __( 'Do not repeat', BASEMENT_TEXTDOMAIN ),
					'repeat' => __( 'Tile', BASEMENT_TEXTDOMAIN ),
					'repeat-x' => __( 'Tile horizontally', BASEMENT_TEXTDOMAIN ),
					'repeat-y' => __( 'Tile vertically', BASEMENT_TEXTDOMAIN )
				),
				'options' => $options,
				'current_value' => empty( $options[ 'repeat' ] ) ? 'nope' : $options[ 'repeat' ],
				'class' => 'basement_update_receivers',
				'attributes' => array(
					'data-action' => 'change',
					'data-background-repeat-receivers' => '#' . $background_example_id
				)
			) 
		);

		$column->appendChild( $this->dom->importNode( $radios->create(), true ) );

		/**
		 * Background attachment
		 */
		$column = $row->appendChild( $this->dom->createElement( 'div' ) );
		$column->setAttribute( 'class', 'basement_column basement_half_column' );

		$radios = new Basement_Form_Input_Radio_Group( array(
				'label_text' => __( 'Attachment', BASEMENT_TEXTDOMAIN ),
				'wrapper_class' => '_background_attachment',
				'radios_wrapper_class' => '_custom_background_option',
				'name' => 'attachment',
				'name_attr_part' => $name_attr_part,
				'values' => array(
					'nope' => __( 'Nope', BASEMENT_TEXTDOMAIN ),
					'scroll' => __( 'Scroll', BASEMENT_TEXTDOMAIN ),
					'fixed' => __( 'Fixed', BASEMENT_TEXTDOMAIN )
				),
				'options' => $options,
				'current_value' => empty( $options[ 'attachment' ] ) ? 'nope' : $options[ 'attachment' ],
				'class' => 'basement_update_receivers',
				'attributes' => array(
					'data-action' => 'change',
					'data-background-attachment-receivers' => '#' . $background_example_id
				)
			) 
		);

		$column->appendChild( $this->dom->importNode( $radios->create(), true ) );

		$row = $background_option->appendChild( $this->dom->createElement( 'div' ) );
		$row->setAttribute( 'class', 'basement_row' );

		/**
		 * Background position
		 */
		$column = $row->appendChild( $this->dom->createElement( 'div' ) );
		$column->setAttribute( 'class', 'basement_column basement_half_column' );

		$radios = new Basement_Form_Input_Radio_Group( array(
				'label_text' => __( 'Position', BASEMENT_TEXTDOMAIN ),
				'wrapper_class' => '_background_position',
				'radios_wrapper_class' => '_custom_background_option',
				'name' => 'position',
				'name_attr_part' => $name_attr_part,
				'values' => array(
					'nope' => __( 'Nope', BASEMENT_TEXTDOMAIN ),
					'left top' => __( 'Left & top', BASEMENT_TEXTDOMAIN ),
					'left center' => __( 'Left & center', BASEMENT_TEXTDOMAIN ),
					'left bottom' => __( 'Left & bottom', BASEMENT_TEXTDOMAIN ),
					'center top' => __( 'Center & top', BASEMENT_TEXTDOMAIN ),
					'center center' => __( 'Center & center', BASEMENT_TEXTDOMAIN ),
					'center bottom' => __( 'Center & bottom', BASEMENT_TEXTDOMAIN ),
					'right top' => __( 'Right & top', BASEMENT_TEXTDOMAIN ),
					'right center' => __( 'Right & center', BASEMENT_TEXTDOMAIN ),
					'right bottom' => __( 'Right & bottom', BASEMENT_TEXTDOMAIN ),
				),
				'options' => $options,
				'current_value' => empty( $options[ 'position' ] ) ? 'nope' : $options[ 'position' ],
				'class' => 'basement_update_receivers',
				'attributes' => array(
					'data-action' => 'change',
					'data-background-position-receivers' => '#' . $background_example_id
				)
			) 
		);

		$column->appendChild( $this->dom->importNode( $radios->create(), true ) );

		/**
		 * Background size
		 */
		$column = $row->appendChild( $this->dom->createElement( 'div' ) );
		$column->setAttribute( 'class', 'basement_column basement_half_column' );

		$radios = new Basement_Form_Input_Radio_Group( array(
				'label_text' => __( 'Size', BASEMENT_TEXTDOMAIN ),
				'wrapper_class' => '_background_position',
				'radios_wrapper_class' => '_custom_background_option',
				'name' => 'size',
				'name_attr_part' => $name_attr_part,
				'values' => array(
					'nope' => __( 'Nope', BASEMENT_TEXTDOMAIN ),
					'auto' => __( 'Auto', BASEMENT_TEXTDOMAIN ),
					'cover' => __( 'Cover', BASEMENT_TEXTDOMAIN ),
					'contain' => __( 'Contain', BASEMENT_TEXTDOMAIN ),
				),
				'options' => $options,
				'current_value' => empty( $options[ 'size' ] ) ? 'nope' : $options[ 'size' ],
				'class' => 'basement_update_receivers',
				'attributes' => array(
					'data-action' => 'change',
					'data-background-size-receivers' => '#' . $background_example_id
				)
			) 
		);

		$column->appendChild( $this->dom->importNode( $radios->create(), true ) );


		return $wrapper;
	}

}
