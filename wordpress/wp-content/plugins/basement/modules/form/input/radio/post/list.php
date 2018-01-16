<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Radio_Post_List extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) || empty( $this->config[ 'posts' ] ) ) {
			return $this->dom->createTextNode( __( 'Posts list radios config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$wrapper_class = !empty( $wrapper_class ) ? $this->textdomain . $wrapper_class : '';
		$radios_wrapper_class = !empty( $radios_wrapper_class ) ? $this->textdomain . $radios_wrapper_class : '';
		

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$wrapper->setAttribute( 'class', $this->textdomain . '_radios_wrapper ' . $wrapper_class );
		$wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );

		$radios = $wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$radios->setAttribute( 'class', $this->textdomain . '_posts_list_radios ' . $radios_wrapper_class );

		if ( !count( $posts ) ) {
			return $wrapper;
		}
		
		global $post;

		foreach ( $posts as $post) {
			setup_postdata( $post );
			$post_container = $radios->appendChild( $this->dom->createElement( 'div' ) );
			$post_container->setAttribute( 'class', $this->textdomain . '_post_radio_container' );

			if ( !empty( $use_meta_value ) ) {
				$value = get_post_meta( $post->ID, $use_meta_value, true );
			} else if ( !empty( $posts_ids_values ) && !empty( $posts_ids_values[ $post->ID ] ) ) {
				$value = $posts_ids_values[ $post->ID ];
			} else {
				$value = $post->ID;
			}

			if ( !isset( $current_value ) ) {
				$current_value = $value;
			}

			$radio = new Basement_Form_Input_Radio( array(
					'name' => $name,
					'name_attr_part' => $name_attr_part,
					'current_value' => $current_value,
					'value' => $value,
					'class' => $this->textdomain . '_posts_list_radio',
					'attributes' => $attributes
				) 
			);

			$radio = $post_container->appendChild( $this->import_dom_node( $radio->create() ) );

			$image = $radio->appendChild( $this->dom->createElement( 'span' ) );
			$image->setAttribute( 'title', __( 'Click to select', BASEMENT_TEXTDOMAIN ) );
			$image_classes = array(
				$this->textdomain . '_posts_list_radio_image',
				'basement_admin_active_border_color_3'
			);
			$post_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			if ( $post_image && isset( $post_image[ 0 ] ) ) {
				$image->setAttribute( 'style', 'background-image: url(' . $post_image[ 0 ] . ')' );
			} else {
				$image_classes[] = 'basement_admin_background_color_1';
			}

			$image->setAttribute( 'class', implode( ' ', $image_classes ) );

			$post_data = $post_container->appendChild( $this->dom->createElement( 'div' ) );
			$post_data->setAttribute( 'class', $this->textdomain . '_posts_list_radio_data' );
			$post_title = $post_data->appendChild( $this->dom->createElement( 
					'div', 
					apply_filters( 'basement_form_input_radio_post_list', get_the_title() )
				) 
			);
			$post_title->setAttribute( 'class', $this->textdomain . '_posts_list_radio_title' );

			$post_type_object = get_post_type_object( $post->post_type );

			$edit_link = $post_data->appendChild( $this->dom->createElement( 'a', $post_type_object->labels->edit_item ) );
			$edit_link->setAttribute( 'href', get_edit_post_link( get_the_ID(), '') );
			$edit_link->setAttribute( 'class', $this->textdomain . '_posts_list_radio_edit_link' );
			$edit_link->setAttribute( 'target', '_blank' );
			$edit_link->setAttribute( 'title', __( 'Will be opened in new tab', BASEMENT_TEXTDOMAIN ) );
		}

		wp_reset_query();

		return $wrapper;
	}

}
