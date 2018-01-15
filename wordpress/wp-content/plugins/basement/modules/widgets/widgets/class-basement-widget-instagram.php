<?php
class Basement_Instagram_Widget extends WP_Widget {
	/* constructor */
	public function __construct() {
		$widget_ops = array(
			'description' => __( 'Widget for popular social network Instagram.', BASEMENT_TEXTDOMAIN ),
			'customize_selective_refresh' => true
		);
		parent::__construct( 'instagram', __( 'Instagram', BASEMENT_TEXTDOMAIN ), $widget_ops );
	}

	/** @see WP_Widget::widget */
	public function widget( $args, $instance ) {
		extract( array_merge( $args, $instance ) );


		$output = $before_widget;
		$output .= $before_title . apply_filters( 'widget_title', empty( $title ) ?  __('Instagram',BASEMENT_TEXTDOMAIN) : $title, $instance, $this->id_base ) . $after_title;


		if( $client_id && $button_text ) {
			$id = uniqid( 'instagram-' );
			$output .= '<div class="instagram-widget" data-id="' . esc_attr( $id ) . '" data-cid="' . esc_attr( $client_id ) . '" data-token="' . esc_attr( $button_text ) . '" data-insta="' . absint( $image_counter ) . '"><div id="' . esc_attr( $id ) . '" class="clearfix"></div></div>';
		}

		if($user_name) {
			$output .= '<span class="instagram-username">'.esc_html($user_name).'</span>';
		}

		$output .= $after_widget;

		echo $output;
	}

	/** @see WP_Widget::update */
	public function update( $new_instance, $old_instance ) {
		delete_transient( 'cherry_plugin_instagram_user_id' );
		delete_transient( 'cherry_plugin_instagram_photos' );
		return $new_instance;
	}

	/** @see WP_Widget::form */
	public function form($instance) {
		$defaults = array(
			'title'               => '',
			'endpoints'           => 'hashtag', // hashtag or self
			'user_name'           => '',
			'tag'                 => '',
			'client_id'           => '',
			'image_counter'       => '6',
			'image_size'          => 'thumbnail',
			'display_description' => 'on',
			'display_comments'    => 'on',
			'display_likes'       => 'on',
			'display_time'        => 'on',
			'link'                => 'on',
			'button_text'         => '',
		);

		extract( array_merge( $defaults, $instance ) );

		$form_field_type = array(
			'title' => array(
				'type' => 'text', 'class' => 'widefat', 'inline_style' => '',  'title' => __('Widget Title', BASEMENT_TEXTDOMAIN), 'description' => '', 'value' => $title
			),
			'user_name' => array(
				'type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('User Name', BASEMENT_TEXTDOMAIN), 'description' => '', 'value' => $user_name
			),
			'client_id' => array(
				'type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('Client ID', BASEMENT_TEXTDOMAIN), 'description' => __('Follow this <a href="https://smashballoon.com/instagram-feed/find-instagram-user-id/" target="_blank">link</a> and get the Client ID.', BASEMENT_TEXTDOMAIN), 'value' => $client_id
			),
			'button_text' => array(
				'type' => 'text', 'class' => 'widefat',  'inline_style' => '', 'title' => __('Access Token', BASEMENT_TEXTDOMAIN), 'description' => __('The Instagram Access Token is a long string of characters unique to your account that grants other applications access to your Instagram feed. <a href="http://instagram.pixelunion.net/" target="_blank" title="">Get Your Instagram Access Token</a>.',BASEMENT_TEXTDOMAIN), 'value' => $button_text
			),
			'image_counter' => array(
				'type' => 'number', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Number of displayed images', BASEMENT_TEXTDOMAIN), 'description' => '', 'value' => $image_counter
			),
		);

		$output = '';

		foreach ($form_field_type as $key => $args) {
			$field_id          = esc_attr($this->get_field_id($key));
			$field_name        = esc_attr($this->get_field_name($key));
			$field_class       = $args['class'];
			$field_title       = $args['title'];
			$field_description = $args['description'];
			$field_value       = $args['value'];
			$field_options     = isset($args['value_options']) ? $args['value_options'] : array() ;
			$inline_style      = $args['inline_style'] ? 'style="'.$args['inline_style'].'"' : '' ;

			$output .= '<p>';
			switch ($args['type']) {
				case 'text':
				case 'number':
					$output .= '<label for="'.$field_id.'">'.$field_title.': <input '.$inline_style.' class="'.$field_class.'" id="'.$field_id.'" name="'.$field_name.'" type="'.$args['type'].'" value="'.esc_attr($field_value).'" /></label>';
					break;
				case 'checkbox':
					$checked = isset($instance[$key]) ? 'checked' : '' ;
					$output .= '<label for="'.$field_id.'"><input value="on" '.$inline_style.' class="'.$field_class.'" id="'.$field_id.'" name="'.$field_name.'" type="checkbox" '.$checked.' />'.$field_title.'</label>';

					break;
				case 'select':
					$output .= '<label for="'.$field_id.'">'.$field_title.':</label>';
					$output .= '<select id="'.$field_id.'" name="'.$field_name.'" '.$inline_style.' class="'.$field_class.'">';
					if(!empty($field_options)){
						foreach ($field_options as $key_options => $value_options) {
							$selected = $key_options == $field_value ? ' selected' : '' ;
							$output .= '<option value="'.$key_options.'" '.$selected.'>'.$value_options.'</option>';
						}
					}
					$output .= '</select>';
					break;
			}
			$output .= '<br><small>'.$field_description.'</small>';
			$output .= '</p>';
		}
		echo $output;
	}

	function get_user_id( $user_name, $client_id ) {
		$cached = get_transient( 'cherry_plugin_instagram_user_id' );

		if ( false !== $cached ) {
			return $cached;
		}

		$url = add_query_arg(
			array( 'q' => esc_attr( $user_name ), 'client_id' => esc_attr( $client_id ) ),
			'https://api.instagram.com/v1/users/search/'
		);
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || empty( $response ) || $response ['response']['code'] != '200' ) {
			set_transient( 'cherry_plugin_instagram_user_id', false, HOUR_IN_SECONDS );
			return false;
		}

		$result  = json_decode( wp_remote_retrieve_body( $response ), true );
		$user_id = false;

		foreach ( $result['data'] as $key => $data ) {

			if ( $user_name != $data['username'] ) {
				continue;
			}

			$user_id = $data['id'];
		}

		set_transient( 'cherry_plugin_instagram_user_id', $user_id, HOUR_IN_SECONDS );

		return $user_id;
	}

	function get_photos( $data, $client_id, $img_counter, $config ) {
		$cached = get_transient( 'cherry_plugin_instagram_photos' );

		if ( false !== $cached ) {
			return $cached;
		}

		if ( 'self' == $config['endpoints'] ) {
			$old_url = 'https://api.instagram.com/v1/users/' . $data . '/media/recent/';
		} else {
			$old_url = 'https://api.instagram.com/v1/tags/' . $data . '/media/recent/';
		}

		$url = add_query_arg(
			array( 'client_id' => esc_attr( $client_id ) ),
			$old_url
		);

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || empty( $response ) || $response ['response']['code'] != '200' ) {
			set_transient( 'cherry_plugin_instagram_photos', false, HOUR_IN_SECONDS );
			return false;
		}

		$result  = json_decode( wp_remote_retrieve_body( $response ), true );
		$photos  = array();
		$counter = 1;

		foreach ( $result['data'] as $photo ) {

			if ( $counter > $img_counter ) {
				break;
			}

			if ( 'image' != $photo['type'] ) {
				continue;
			}

			$_photo = array();

			if ( in_array( 'link', $config ) )
				$_photo = array_merge( $_photo, array( 'link' => esc_url( $photo['link'] ) ) );

			if ( in_array( 'comments', $config ) )
				$_photo = array_merge( $_photo, array( 'comments' => absint( $photo['comments']['count'] ) ) );

			if ( in_array( 'likes', $config ) )
				$_photo = array_merge( $_photo, array( 'likes' => absint( $photo['likes']['count'] ) ) );

			if ( in_array( 'time', $config ) )
				$_photo = array_merge( $_photo, array( 'time' => sanitize_text_field( $photo['created_time'] ) ) );

			if ( in_array( 'description', $config ) )
				$_photo = array_merge( $_photo, array( 'description' => wp_trim_words( $photo['caption']['text'], 10 ) ) );

			if ( array_key_exists( 'thumb', $config ) ) {
				$size   = ( 'large' == $config['thumb'] ) ? 'standard_resolution' : 'thumbnail';
				$_photo = array_merge( $_photo, array( 'thumb' => $photo['images'][ $size ]['url'] ) );
			}

			if ( ! empty( $_photo ) ) {
				array_push( $photos, $_photo );
			}

			$counter++;
		}

		set_transient( 'cherry_plugin_instagram_photos', $photos, HOUR_IN_SECONDS );

		return $photos;
	}
}
?>