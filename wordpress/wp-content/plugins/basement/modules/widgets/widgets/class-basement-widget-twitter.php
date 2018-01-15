<?php
$error = array();
class Basement_Twitter_Embed_Widget extends WP_Widget {

	/* constructor */
	public function __construct() {
		$widget_ops = array(
			'description' => __( 'Widget for Twitter embed content.', BASEMENT_TEXTDOMAIN ),
			'customize_selective_refresh' => true
		);
		parent::__construct( 'twitter', __( 'Twitter', BASEMENT_TEXTDOMAIN ), $widget_ops );
	}

	/** @see WP_Widget::widget */
	public function widget($args, $instance) {
		extract(array_merge($args , $instance));

		$lang = get_bloginfo('language');


		$output = $before_widget;
		$output .= $before_title . apply_filters( 'widget_title', empty( $title ) ?  __('Twitter',BASEMENT_TEXTDOMAIN) : $title, $instance, $this->id_base ) . $after_title;

		if($widgetId) {
			if ( $type === 'simple' ) {
				$id = uniqid( 'twitter-' );
				$output .= '<div class="twitter-widget" data-id="' . esc_attr( $id ) . '" data-cid="' . esc_attr( $widgetId ) . '" data-tweet="' . absint( $limit ) . '"><div id="' . esc_attr( $id ) . '"></div></div>';
			} else {
				$limit_string = 'data-tweet-limit="2"';
				if ( $limit ) {
					$limit_string = 'data-tweet-limit="' . absint( $limit ) . '"';
				}
				$userName = isset($userName) ? $userName : '';
				if ( $instance['error'] === '' ) {
					$output .= '<a class="twitter-timeline" data-screen-name="' . esc_attr( $userName ) . '" data-theme="' . esc_attr( $color_scheme ) . '" data-widget-id="' . esc_attr( $widgetId ) . '" ' . $limit_string . '></a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
				} else {
					$output .= '<div class="alert alert-danger fade in">' . __( 'There is some errors. Please check widgets page.', BASEMENT_TEXTDOMAIN ) . '</div>';
				}

			}
		}

		$output .= $after_widget;

		echo $output;
	}

	/** @see WP_Widget::update */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// keep all of the fields passed from the new instance
		foreach ( $new_instance as $key => $value ){
			$instance[ $key ] = strip_tags($value);
		}

		if ( empty( $instance['widgetId'] ) ) {
			$instance['error'] = __('Field is empty', BASEMENT_TEXTDOMAIN);
		} else {
			if ( !is_numeric ( $instance['widgetId'] ) ) {
				$instance['error'] = __('Widget ID can contains only numbers', BASEMENT_TEXTDOMAIN);
			} else {
				$instance['error'] = '';
			}
		}

		return $instance;
	}

	/** @see WP_Widget::form */
	public function form($instance) {
		$defaults = array(
			'title' => '',
			'widgetId' => '',
			'userName' => '',
			'color_scheme' => 'light',
			'limit' => '',
			'error' => '',
			'type' => 'simple'
		);

		extract(array_merge($defaults, $instance));

		$form_field_type = array(
			'title' => array('type' => 'text', 'class' => 'widefat', 'inline_style' => '',  'title' => __('Widget Title', BASEMENT_TEXTDOMAIN), 'description' => '', 'value' => $title),
			'type' => array(
				'type' => 'select',
				'class' => 'widefat',
				'inline_style' => '',
				'title' => __('Type', BASEMENT_TEXTDOMAIN),
				'description' => '',
				'value' => $type,
				'value_options' => array(
					'simple' => __('Simple', BASEMENT_TEXTDOMAIN),
					'embed' => __('Embed', BASEMENT_TEXTDOMAIN)
				)
			),
			'widgetId' => array('type' => 'number', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Widget ID', BASEMENT_TEXTDOMAIN), 'description' => __('ID of your widget. First of all you need make a new one widget on page https://twitter.com/settings/widgets/new. After it copy and paste here ID of widget. You can find it after creating of widget in browser\'s URL field. It contents only numbers.', BASEMENT_TEXTDOMAIN), 'value' => $widgetId),
			#'userName' => array('type' => 'text', 'class' => 'widefat', 'inline_style' => '', 'title' => __('User Name', BASEMENT_TEXTDOMAIN), 'description' => __('Twitter username you need to show. If empty, then will shown twitter that you set in Twitter\'s configure page.', BASEMENT_TEXTDOMAIN), 'value' => $userName),
			'color_scheme' => array('type' => 'select', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Color Scheme', BASEMENT_TEXTDOMAIN), 'description' => __('This option only works for the Embed type.',BASEMENT_TEXTDOMAIN), 'value' => $color_scheme, 'value_options' => array('light' => __('Light', BASEMENT_TEXTDOMAIN), 'dark' => __('Dark', BASEMENT_TEXTDOMAIN)) ),
			'limit' => array('type' => 'number', 'class' => 'widefat', 'inline_style' => '', 'title' => __('Tweet limit', BASEMENT_TEXTDOMAIN), 'description' => __('Number of tweets. Leave blank if you don\'t want to limit tweet\'s number.', BASEMENT_TEXTDOMAIN), 'value' => $limit),
		);
		$output = '';

		$title = esc_attr($title);

		if ($error) {
			#$output .= '<div style="margin:20px 0 15px;padding:10px;background:#ff9b9b">'.$error.'</div>';
		}

		foreach ($form_field_type as $key => $args) {

			$field_id = esc_attr($this->get_field_id($key));
			$field_name = esc_attr($this->get_field_name($key));

			$field_class = $args['class'];
			$field_title = $args['title'];
			$field_description = $args['description'];
			$field_value = $args['value'];
			$field_options = isset($args['value_options']) ? $args['value_options'] : array() ;
			$inline_style = $args['inline_style'] ? 'style="'.$args['inline_style'].'"' : '' ;

			$output .= '<p>';

			switch ($args['type']) {
				case 'text':
				case 'number':
					$output .= '<label for="'.$field_id.'">'.$field_title.': <input '.$inline_style.' class="'.$field_class.'" id="'.$field_id.'" name="'.$field_name.'" type="'.$args['type'].'" value="'.esc_attr($field_value).'"/></label>';
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
}
?>
