<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Checkbox_Post_List_Sortable extends Basement_Form_Input {

	private $version = '1.0.0';

	public function create() {
		if ( !is_array( $this->config ) || empty( $this->config[ 'posts' ] ) ) {
			return $this->dom->createTextNode( __( 'Sortable posts list checboxes config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$wrapper_class = !empty( $wrapper_class ) ? $this->textdomain . $wrapper_class : '';
		$checkboxes_wrapper_class = !empty( $checkboxes_wrapper_class ) ? $this->textdomain . $checkboxes_wrapper_class : '';
		$current_value = !empty( $current_value ) ? $current_value : 0;

		$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
		$wrapper->setAttribute( 'class', $this->textdomain . '_checkboxes_wrapper ' . $wrapper_class );
		$wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );

		$checkboxes = $wrapper->appendChild( $this->dom->createElement( 'div' ) );
		$checkboxes->setAttribute( 'class', $this->textdomain . '_sortable_posts_list_checkboxes sortable-contaner ' . $checkboxes_wrapper_class );

		if ( !count( $posts ) ) {
			return $wrapper;
		}
		
		global $post;

		foreach ( $posts as $post) {
			setup_postdata( $post );
			$post_container = $checkboxes->appendChild( $this->dom->createElement( 'div' ) );

			$class = $this->textdomain . '_post_checkbox_container';
			
			if ( !empty( $filters_hash ) ) {
				$class .= ' ' . $filters_hash . '_filter_' . $post->post_type;
			}

			$post_container->setAttribute( 'class', $class );
			$post_container->setAttribute( 'title', __( "Drag'n'drop to change order", BASEMENT_TEXTDOMAIN ) );

			$checkbox = new Basement_Form_Input_Checkbox( array(
					'name' => $name,
					'name_attr_part' => $name_attr_part,
					'current_value' => $current_value,
					'value' => $post->ID,
					'class' => $this->textdomain . '_sortable_posts_list_checkbox',
					'attributes' => $attributes,
					'no_wrapper' => true,
					'no_hidden' => !empty( $no_hidden ) ? $no_hidden : false,
					'multiple' => true
				) 
			);

			$checkbox = $post_container->appendChild( $this->import_dom_node( $checkbox->create() ) );

			$image = $checkbox->appendChild( $this->dom->createElement( 'span' ) );
			$image->setAttribute( 'title', __( 'Click to select/deselect', BASEMENT_TEXTDOMAIN ) );
			$image_classes = array(
				$this->textdomain . '_sortable_posts_list_checkbox_image',
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
			$post_data->setAttribute( 'class', $this->textdomain . '_sortable_posts_list_checkbox_data' );
			$post_title = $post_data->appendChild( $this->dom->createElement( 
					'div', 
					apply_filters( 'basement_form_input_checkbox_post_list_sortable', get_the_title() )
				) 
			);
			$post_title->setAttribute( 'class', $this->textdomain . '_sortable_posts_list_checkbox_title' );

			$post_type_object = get_post_type_object( $post->post_type );

			$edit_link = $post_data->appendChild( $this->dom->createElement( 'a', $post_type_object->labels->edit_item ) );
			$edit_link->setAttribute( 'href', get_edit_post_link( get_the_ID(), '') );
			$edit_link->setAttribute( 'class', $this->textdomain . '_sortable_posts_list_checkbox_edit_link' );
			$edit_link->setAttribute( 'target', '_blank' );
			$edit_link->setAttribute( 'title', __( 'Will be opened in new tab', BASEMENT_TEXTDOMAIN ) );
		}

		wp_reset_query();

		return $wrapper;
	}

}
