<?php

class Basement_Hr_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'description' => __( 'Displays the horizontal separator in the footer.', BASEMENT_TEXTDOMAIN ),
			'customize_selective_refresh' => true
		);
		parent::__construct( 'basement_hr_widget', __( 'Horizontal separator', BASEMENT_TEXTDOMAIN ), $widget_ops );
	}


	public function widget( $args, $instance ) {
		global $basement_footer;
		if ( $basement_footer && $basement_footer['place'] === 'footer' ) {
			echo '<hr class="' . esc_attr( $args['before_widget'] ) . '">';
		}
	}


	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['cf'] = $new_instance['cf'];
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}
		$instance['filter'] = ! empty( $new_instance['filter'] );
		return $instance;
	}

	
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = sanitize_text_field( $instance['title'] );
		$filter = isset( $instance['filter'] ) ? $instance['filter'] : 0;
		$cf = isset( $instance['cf'] ) ? $instance['cf'] : '';
	}
}
