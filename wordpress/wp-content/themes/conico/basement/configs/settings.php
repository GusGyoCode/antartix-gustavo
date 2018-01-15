<?php
/**
 * Custom Theme Settings
 *
 * @since Conico 1.0
 */

$settings_config = array();


$settings_config['favicon'] = array(
	'title'  => esc_html__( 'Favicon', 'conico' ),
	'blocks' => array(
		array(
			'title'       => esc_html__( 'Favicon', 'conico' ),
			'description' => esc_html__( 'Icon for browser page tab and user bookmarks', 'conico' ),
			'inputs'      => array(
				array(
					'type'              => 'image',
					'name'              => 'conico_favicon',
					'value'             => get_option( 'conico_favicon' ),
					'text_buttons'      => true,
					'upload_text'       => esc_html__( 'Set favicon image', 'conico' ),
					'delete_text'       => esc_html__( 'Remove favicon image', 'conico' ),
					'frame_title'       => esc_html__( 'Set favicon image', 'conico' ),
					'frame_button_text' => esc_html__( 'Set favicon image', 'conico' ),
				)
			)
		)
	)
);


$settings_config['google_api'] = array(
	'title'  => esc_html__( 'Google Map', 'conico' ),
	'blocks' => array(
		array(
			'title'       => esc_html__( 'Api key', 'conico' ),
			'description' => esc_html__( 'To use the Google Map, you must include an API key', 'conico' ),
			'inputs'      => array(
				array(
					'type'  => 'text',
					'name'  => 'conico_api_key',
					'value' => get_option( 'conico_api_key', '' )
				)
			)
		)
	)
);

$settings_config['social_sharing'] = array(
	'title'  => esc_html__( 'Social sharing', 'conico' ),
	'blocks' => array(
		array(
			'title'       => esc_html__( 'Social list', 'conico' ),
			'description' => esc_html__( 'Select a social network', 'conico' ),
			'inputs'      => array(
				array(
					'type'          => 'checkboxes',
					'name'          => 'conico_social_sharing',
					'label_text'    => esc_html__( ' ', 'conico' ),
					'values'        => array(
						'blogger'       => esc_html__( 'Blogger', 'conico' ),
						'delicious'     => esc_html__( 'Delicious', 'conico' ),
						'digg'          => esc_html__( 'Digg', 'conico' ),
						'facebook'      => esc_html__( 'Facebook', 'conico' ),
						'gplus'         => esc_html__( 'Google+', 'conico' ),
						'linkedin'      => esc_html__( 'LinkedIn', 'conico' ),
						'lj'            => esc_html__( 'LiveJournal', 'conico' ),
						'moimir'        => esc_html__( 'My World', 'conico' ),
						'odnoklassniki' => esc_html__( 'Odnoklassniki', 'conico' ),
						'pocket'        => esc_html__( 'Pocket', 'conico' ),
						'qzone'         => esc_html__( 'Qzone', 'conico' ),
						'reddit'        => esc_html__( 'Reddit', 'conico' ),
						'sinaWeibo'     => esc_html__( 'Sina Weibo', 'conico' ),
						'surfingburd'   => esc_html__( 'Surfingbird', 'conico' ),
						'telegram'      => esc_html__( 'Telegram', 'conico' ),
						'tencentWeibo'  => esc_html__( 'Tencent Weibo', 'conico' ),
						'tumblr'        => esc_html__( 'Tumblr', 'conico' ),
						'twitter'       => esc_html__( 'Twitter', 'conico' ),
						'viber'         => esc_html__( 'Viber', 'conico' ),
						'vkontakte'     => esc_html__( 'VK', 'conico' ),
						'whatsapp'      => esc_html__( 'WhatsApp', 'conico' )
					),
					'current_value' => get_option( 'conico_social_sharing', array('facebook', 'gplus', 'twitter') )
				)
			)
		),
		array(
			'title'       => esc_html__( 'Type', 'conico' ),
			'description' => esc_html__( 'Set the view of social sharing block', 'conico' ),
			'inputs'      => array(
				array(
					'type'          => 'radios',
					'name'          => 'conico_social_sharing_type',
					'values'        => array(
						'dropdown'   => esc_html__( 'Dropdown', 'conico' ),
						'horizontal' => esc_html__( 'Horizontal', 'conico' )
					),
					'class'         => 'v-radio',
					'current_value' => get_option( 'conico_social_sharing_type', 'dropdown' )
				)
			)
		)
	)
);


return $settings_config;
