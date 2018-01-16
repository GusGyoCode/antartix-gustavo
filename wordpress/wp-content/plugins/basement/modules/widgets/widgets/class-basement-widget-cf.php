<?php

class Basement_Form_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'description' => __( 'Displays contact form 7.', BASEMENT_TEXTDOMAIN ),
			'customize_selective_refresh' => true
		);
		parent::__construct( 'contact_form', __( 'Contact Form', BASEMENT_TEXTDOMAIN ), $widget_ops );
	}


	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __('Contact Form',BASEMENT_TEXTDOMAIN) : $instance['title'], $instance, $this->id_base );
		$widget_text = ! empty( $instance['text'] ) ? $instance['text'] : '';
		$text = apply_filters( 'widget_text', $widget_text, $instance, $this );
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} 
		
		if($instance['cf']) {
			echo '<div class="wrapper-cf-form" style="overflow: hidden;">'.do_shortcode('[contact-form-7 id="'.$instance['cf'].'" title="Widget CF7"]').'</div>';
		}

		if($text) {
			?>
			<div class="textwidget"><?php echo ! empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
			<?php
		}
		
		
		echo $args['after_widget'];
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
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BASEMENT_TEXTDOMAIN); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>


		<?php

		$items = get_posts(array(
			'post_type' => 'wpcf7_contact_form',
			'posts_per_page' => -1
		));

		if($items) {
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'cf' ); ?>"><?php _e( 'Select Form:', BASEMENT_TEXTDOMAIN ); ?></label><br>
				<select id="<?php echo $this->get_field_id( 'cf' ); ?>"
				        name="<?php echo $this->get_field_name( 'cf' ); ?>">
					<option value="0"><?php _e( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ); ?></option>
					<?php foreach ( $items as $value ) { ?>
						<option value="<?php echo esc_attr( $value->ID ); ?>" <?php selected( $cf, $value->ID ); ?>>
							<?php echo esc_html( $value->post_title ); ?>
						</option>
					<?php } ?>
				</select>
			</p>


			<?php 
		} else {
			echo sprintf( __( 'No contact form have been created yet. <a href="%s">Create some</a>.', BASEMENT_TEXTDOMAIN ), esc_attr( 'admin.php?page=wpcf7-new' ) );
		}

		?>

		<p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content:', BASEMENT_TEXTDOMAIN ); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea></p>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', BASEMENT_TEXTDOMAIN); ?></label></p>

		<?php
	}
}
