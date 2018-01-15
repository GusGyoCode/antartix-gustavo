<?php

class Basement_Image_Widget extends WP_Widget {
	/* constructor */
	public function __construct() {

		$widget_ops = array(
			'description' => __( 'Displays image and custom text.', BASEMENT_TEXTDOMAIN ),
			'customize_selective_refresh' => true
		);

		parent::__construct( 'image', __( 'Extended Image', BASEMENT_TEXTDOMAIN ), $widget_ops );

		add_action( 'admin_enqueue_scripts', array( $this, 'upload_scripts' ) );
		add_action( 'admin_enqueue_styles', array( $this, 'upload_styles' ) );
	}

	/** @see WP_Widget::widget */
	public function widget( $args, $instance ) {
		extract( array_merge( $args, $instance ) );

		$fill = false;

		if ( ! empty( $instance['fill'] ) && $instance['fill'] == 'on' ) {
			$fill = true;
		}

		$target = '';


		if ( $fill ) {
			$target = 'target="_blank"';
		}

		$output = $before_widget;
		$output .= $before_title . apply_filters( 'widget_title', empty( $title ) ? __( 'Extended Image', BASEMENT_TEXTDOMAIN ) : $title, $instance, $this->id_base ) . $after_title;

		$output .= '<div class="banner_wrapper" >';
		$output .= ! empty( $link_url ) ? '<a href="' . esc_url( $link_url ) . '" ' . $target . '>' : '';
		$output .= ( $image_url ) ? '<img src="' . esc_url( $image_url ) . '" class="img-responsive" alt="">' : '';
		$output .= ! empty( $link_url ) ? '</a>' : '';
		$output .= ! empty( $description_text ) ? '<p>' . esc_textarea($description_text) . '</p>' : '';
		$output .= '</div>';

		$output .= $after_widget;

		echo $output;
	}

	/** @see WP_Widget::update */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/** @see WP_Widget::form */
	public function form( $instance ) {
		$defaults = array(
			'title'            => '',
			'description_text' => '',
			'image_url'        => '',
			'fill'             => 'on',
			'link_url'         => '',
		);

		extract( array_merge( $defaults, $instance ) );

		$form_field_type = array(
			'title' => array(
				'type'         => 'text',
				'class'        => 'widefat',
				'inline_style' => '',
				'title'        => __( 'Title', BASEMENT_TEXTDOMAIN ),
				'description'  => '',
				'value'        => $title,
			),

			'image_url'        => array(
				'type'         => 'upload',
				'class'        => 'widefat',
				'inline_style' => '',
				'title'        => __( 'Image URL', BASEMENT_TEXTDOMAIN ),
				'description'  => '',
				'value'        => $image_url,
			),
			'description_text' => array(
				'type'         => 'textarea',
				'class'        => 'widefat',
				'inline_style' => '',
				'title'        => __( 'Banner description', BASEMENT_TEXTDOMAIN ),
				'description'  => '',
				'value'        => $description_text,
			),
			'link_url'         => array(
				'type'         => 'text',
				'class'        => 'widefat',
				'inline_style' => '',
				'title'        => __( 'Link URL', BASEMENT_TEXTDOMAIN ),
				'description'  => '',
				'value'        => $link_url,
			),
			'fill'             => array(
				'type'         => 'checkbox',
				'class'        => '',
				'inline_style' => '',
				'title'        => __( 'Open in new window?', BASEMENT_TEXTDOMAIN ),
				'description'  => '',
				'value'        => $fill,
			)
		);

		$output = '';

		foreach ( $form_field_type as $key => $args ) {

			$field_id          = esc_attr( $this->get_field_id( $key ) );
			$field_name        = esc_attr( $this->get_field_name( $key ) );
			$field_class       = $args['class'];
			$field_title       = $args['title'];
			$field_description = $args['description'];
			$field_value       = $args['value'];
			$field_options     = isset( $args['value_options'] ) ? $args['value_options'] : array();
			$inline_style      = $args['inline_style'] ? 'style="' . $args['inline_style'] . '"' : '';

			$output .= '<p>';

			switch ( $args['type'] ) {
				case 'text':
					$output .= '<label for="' . $field_id . '">' . $field_title . ': <input ' . $inline_style . ' class="' . $field_class . '" id="' . $field_id . '" name="' . $field_name . '" type="text" value="' . esc_attr( $field_value ) . '" /></label>';
					break;

				case 'checkbox':
					$checked = isset( $instance[ $key ] ) ? 'checked' : '';
					$output .= '<label for="' . $field_id . '"><input value="on" ' . $inline_style . ' class="' . $field_class . '" id="' . $field_id . '" name="' . $field_name . '" type="checkbox" ' . $checked . ' />' . $field_title . '</label>';
					break;

				case 'select':
					$output .= '<label for="' . $field_id . '">' . $field_title . ': <select id="' . $field_id . '" name="' . $field_name . '" ' . $inline_style . ' >';
					if ( ! empty( $field_options ) ) {
						foreach ( $field_options as $key_options => $value_options ) {
							$selected = $key_options == $field_value ? ' selected' : '';
							$output .= '<option value="' . $key_options . '" ' . $selected . '>' . $value_options . '</option>';
						}
					}
					$output .= '</select></label>';
					break;

				case 'textarea':
					$output .= '<textarea class="' . $field_class . '" rows="16" cols="10" id="' . $field_id . '" name="' . $field_name . '">' . $field_value . '</textarea>';
					break;

				case 'upload':
					$output .= '<label for="' . $field_id . '">' . $field_title . ':</label>';
					$output .= '<input name="' . $field_name . '" id="' . $field_id . '"  ' . $inline_style . ' class="' . $field_class . '" type="text" size="36"  value="' . $field_value . '" />';
					$output .= '<input style="margin: 10px 0" class="upload_image_button button button-primary" type="button" value="' . __( 'Select Image', BASEMENT_TEXTDOMAIN ) . '" />';
					break;
			}
			$output .= '<br><small>' . $field_description . '</small>';
			$output .= '</p>';
		}

		echo $output;
	}

	public function upload_scripts( $hook ) {

		if ( 'widgets.php' !== $hook ) {
			return;
		}

		wp_enqueue_media();
		// wp_print_media_templates();
		wp_enqueue_script(
			'upload_media_widget',
			Basement::url() . '/assets/javascript/upload-media-files.min.js',
			array( 'jquery' ),
			'1.0',
			true
		);
	}

	public function upload_styles() {
		wp_enqueue_style( 'thickbox' );
	}
}

?>