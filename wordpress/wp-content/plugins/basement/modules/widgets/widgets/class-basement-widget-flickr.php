<?php
// =============================== My Flickr widget  ======================================
class Basement_Flickr_Widget extends WP_Widget {
	/* constructor */
	function __construct() {
		$widget_ops = array(
			'description' => __( 'Widget for popular social network Flickr.', BASEMENT_TEXTDOMAIN ),
			'customize_selective_refresh' => true
		);
		parent::__construct( 'flickr', $name = __( 'Flickr', BASEMENT_TEXTDOMAIN ), $widget_ops );
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract( array_merge( $args, $instance ) );


		$output = $before_widget;
		$output .= $before_title . apply_filters( 'widget_title', empty( $title ) ? __( 'Flickr', BASEMENT_TEXTDOMAIN ) : $title, $instance, $this->id_base ) . $after_title;

		if( $flickr_id ) {
			$id = uniqid( 'flickr-' );
			$output .= '<div class="flickr-widget" data-id="' . esc_attr($id) . '" data-cid="' . esc_attr($flickr_id) . '" data-flick="' . absint( $image_amount ) . '"><div id="' . esc_attr($id) . '"></div></div>';
		}

		$output .= $after_widget;

		echo $output;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => '', 'flickr_id' => '', 'image_amount' => '6', 'linktext' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title     = esc_attr($instance['title']);
		$flickr_id = esc_attr($instance['flickr_id']);
		$amount    = intval($instance['image_amount']);
		$linktext  = esc_attr($instance['linktext']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', BASEMENT_TEXTDOMAIN); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('flickr_id'); ?>"><?php _e('Flickr ID', BASEMENT_TEXTDOMAIN).':'; ?> <input class="widefat" id="<?php echo $this->get_field_id('flickr_id'); ?>" name="<?php echo $this->get_field_name('flickr_id'); ?>" type="text" value="<?php echo $flickr_id; ?>" /></label>

			<br><small>Follow this <a href="http://idgettr.com/" target="_blank">link</a> and get the Flickr ID.</small>
		</p>
		<p><label for="<?php echo $this->get_field_id('image_amount'); ?>"><?php _e('Images count', BASEMENT_TEXTDOMAIN).':'; ?> <input class="widefat" id="<?php echo $this->get_field_id('image_amount'); ?>" name="<?php echo $this->get_field_name('image_amount'); ?>" type="number" value="<?php echo $amount; ?>" /></label></p>
	<?php }
} ?>