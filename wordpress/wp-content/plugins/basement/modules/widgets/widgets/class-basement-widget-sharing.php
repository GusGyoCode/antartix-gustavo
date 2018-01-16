<?php

class Basement_Sharing_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'description' => __( 'Displays simple social sharing block.', BASEMENT_TEXTDOMAIN ),
			'customize_selective_refresh' => true
		);

		parent::__construct( 'share', __( 'Social sharing', BASEMENT_TEXTDOMAIN ), $widget_ops );
	}


	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __('Social sharing',BASEMENT_TEXTDOMAIN ) : $instance['title'], $instance, $this->id_base );

		$widget_text = ! empty( $instance['text'] ) ? $instance['text'] : '';

		$text = apply_filters( 'widget_text', $widget_text, $instance, $this );
		$align = empty($instance['align']) ? 'left' : $instance['align'] ;
		$type = empty($instance['type']) ? 'dropdown' : $instance['type'] ;

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}



		$socials = get_option( THEME_TEXTDOMAIN . '_social_sharing' );
		$socials_clean = array();

		if($socials) {
			foreach ($socials as $social) {
				if(!empty($social)) {
					$socials_clean[] = $social;
				}
			}
		}

		$share = '';

		$socials = $socials_clean ? implode(',',$socials_clean) : 'gplus,facebook,twitter';


		if($text) { ?>
			<div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php }


		if($type === 'dropdown') {
			$btn = '<i class="icon-share"></i>';
			$share_block = sprintf('<div class="theme-share ya-share2" data-services="%1$s"></div>',
				$socials
			);
			$share_dropdown = sprintf('<a href="#" class="theme-share-dropdown theme-share">' . $btn . '<div class="share-tooltip">' . $share_block . '</div></a>');
			$share = $share_dropdown;
		} elseif($type === 'horizontal') {
			$share_block = sprintf('<div class="theme-share ya-share2" data-services="%1$s"></div>',
				$socials
			);
			$share_horizontal = sprintf('<div class="theme-share-horizontal theme-share">' . $share_block . '</div>');
			$share = $share_horizontal;
		}


		echo '<div class="text-'.esc_attr($align).' share-area">';

		echo $share;

		echo '</div>';

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}
		$instance['filter'] = ! empty( $new_instance['filter'] );
		$instance['align'] = $new_instance['align'];
		$instance['type'] = $new_instance['type'];
		return $instance;
	}


	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$filter = isset( $instance['filter'] ) ? $instance['filter'] : 0;
		$title = sanitize_text_field( $instance['title'] );
		$align = empty($instance['align']) ? 'left' : $instance['align'] ;
		$type = empty($instance['type']) ? 'dropdown' : $instance['type'] ;
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BASEMENT_TEXTDOMAIN); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content:', BASEMENT_TEXTDOMAIN ); ?></label>
			<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea></p>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', BASEMENT_TEXTDOMAIN); ?></label></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Set the view of social sharing block:', BASEMENT_TEXTDOMAIN ); ?></label><br>
			<select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
				<option value="dropdown" <?php selected($type,'dropdown') ?> ><?php _e( 'Dropdown', BASEMENT_SHORTCODES_TEXTDOMAIN ); ?></option>
				<option value="horizontal" <?php selected($type,'horizontal') ?> ><?php _e( 'Horizontal', BASEMENT_SHORTCODES_TEXTDOMAIN ); ?></option>
			</select>
		</p>

		<div class="sllw-row" style="text-align: left;">
			<p style="margin-bottom: 5px;"><?php _e('Alignment the sharing block:',BASEMENT_TEXTDOMAIN); ?></p>
			<label for="<?php echo $this->get_field_id('left'); ?>"><input type="radio" name="<?php echo $this->get_field_name('align'); ?>" value="left" id="<?php echo $this->get_field_id('left'); ?>" <?php checked($align, "left"); ?> />  <?php echo __("Left", BASEMENT_TEXTDOMAIN); ?></label><br>
			<label for="<?php echo $this->get_field_id('center'); ?>"><input type="radio" name="<?php echo $this->get_field_name('align'); ?>" value="center" id="<?php echo $this->get_field_id('center'); ?>" <?php checked($align, "center"); ?> /> <?php echo __("Center", BASEMENT_TEXTDOMAIN); ?></label><br>
			<label for="<?php echo $this->get_field_id('right'); ?>"><input type="radio" name="<?php echo $this->get_field_name('align'); ?>" value="right" id="<?php echo $this->get_field_id('right'); ?>" <?php checked($align, "right"); ?> /> <?php echo __("Right", BASEMENT_TEXTDOMAIN); ?></label>
		</div>
		<?php
	}
}
