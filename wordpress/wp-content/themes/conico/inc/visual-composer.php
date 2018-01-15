<?php
/**
 * Custom Settings For Visual Composer
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

if ( !in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	return;


if ( ! function_exists( 'conico_vc_scripts' ) ) {
	/**
	 * Enqueues VC styles/scripts.
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_scripts() {
		// Register new styles
		wp_deregister_style('js_composer_front');
		wp_register_style( 'js_composer_front', CONICO_CSS_PATH . 'js_composer.min.css', array(), CONICO_VERSION );

		wp_deregister_style('vc_tta_style');
		wp_register_style( 'vc_tta_style', CONICO_CSS_PATH . 'js_composer_tta.min.css', array(), CONICO_VERSION );

		wp_enqueue_style( 'js_composer_front' );
		wp_enqueue_style( 'vc_tta_style' );
	}
	add_action( 'wp_enqueue_scripts', 'conico_vc_scripts', 999);
}


if ( ! function_exists( 'conico_vc_after_init_actions' ) ) {
	/**
	 * VC after init actions
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_after_init_actions() {
		if ( ! class_exists( 'WPBMap' ) )
			return;

		$vc_shortcodes = array(
			'vc_separator'      => array( 'color' ),
			'vc_btn'      => array( 'color', 'i_type', 'gradient_color_1', 'gradient_color_2', 'style' ),
			'vc_icon'           => array( 'type', 'color', 'size', 'align' ),
			'vc_row' => array('gap'),
			'vc_tta_accordion' => array('c_icon'),
			'vc_tta_section' => array('i_type'),
			'vc_message' => array('color','message_box_color','icon_type'),
			'vc_progress_bar' => array('bgcolor')
		);

		foreach ( $vc_shortcodes as $key => $value ) {

			foreach ( $value as $param_value ) {
				$param = WPBMap::getParam( $key, $param_value );
				switch ( $key ) {
					case 'vc_tta_accordion' :
						$param['value'][ __( 'Arrow', 'conico' ) ]  = 'arrow';
						break;
					case 'vc_progress_bar' :
						$param['std'] = 'bar_black';
						break;
					case 'vc_message' :
						if($param_value === 'color') {
							$param['options'] = array(
								array(
									'label'  => __( 'Custom', 'conico' ),
									'value'  => '',
									'params' => array(),
								),
								array(
									'label'  => __( 'Informational', 'conico' ),
									'value'  => 'info',
									'params' => array(
										'message_box_color' => 'info',
										'icon_type'         => 'feather',
										'icon_feather'  => 'icon-bell',
									),
								),
								array(
									'label'  => __( 'Warning', 'conico' ),
									'value'  => 'warning',
									'params' => array(
										'message_box_color' => 'warning',
										'icon_type'         => 'feather',
										'icon_feather'  => 'icon-flag',
									),
								),
								array(
									'label'  => __( 'Success', 'conico' ),
									'value'  => 'success',
									'params' => array(
										'message_box_color' => 'success',
										'icon_type'         => 'feather',
										'icon_feather'  => 'icon-circle-check',
									),
								),
								array(
									'label'  => __( 'Error', 'conico' ),
									'value'  => 'danger',
									'params' => array(
										'message_box_color' => 'danger',
										'icon_type'         => 'feather',
										'icon_feather'  => 'icon-circle-cross',
									),
								),
							);
						} elseif ($param_value === 'icon_type') {
							$param['value'][ __( 'Feather', 'conico' ) ]     = 'feather';
							$param['value'][ __( 'Bootstrap', 'conico' ) ]   = 'bootstrap_font';
							$param['value'][ __( 'Aisconverse', 'conico' ) ] = 'aisconverse_font';
							$param['value'][ __( 'Theme', 'conico' ) ]       = 'theme_font';
						} else {
							if(function_exists('getVcShared')) {
								$custom_colors  = array(
									__( 'Informational', 'conico' ) => 'info',
									__( 'Warning', 'conico' )       => 'warning',
									__( 'Success', 'conico' )       => 'success',
									__( 'Error', 'conico' )         => 'danger'
								);
								$param['value'] = $custom_colors + getVcShared( 'colors' );
							}
						}
						break;
					case 'vc_row' :
						$param['value']['60px'] = '60';
						break;
					case 'vc_tta_section' :
					case 'vc_btn' :
						if ($param_value === 'style') {
							$param['value'] = array(
								__( 'Flat', 'conico' ) => 'flat',
								__( 'Modern', 'conico' ) => 'modern',
								__( 'Underlined', 'conico' ) => 'underlined',
								__( 'Classic', 'conico' ) => 'classic',
								__( 'Outline', 'conico' ) => 'outline',
								__( '3d', 'conico' ) => '3d',
								__( 'Custom', 'conico' ) => 'custom',
								__( 'Outline custom', 'conico' ) => 'outline-custom',
								__( 'Gradient', 'conico' ) => 'gradient',
								__( 'Gradient Custom', 'conico' ) => 'gradient-custom',
							);
						} else {
							if ( $param_value !== 'i_type' ) {
								if($key === 'vc_btn') {

									$param['value'] = array(
										// Btn1 Colors
										__( 'Primary', 'conico' ) => 'inverse',
										__( 'Success', 'conico' ) => 'success',
										__( 'Warning', 'conico' ) => 'warning',
										__( 'Info', 'conico' ) => 'info',
										__( 'Danger', 'conico' ) => 'danger',
										__( 'Classic Grey', 'conico' ) => 'default',
										__( 'Classic Blue', 'conico' ) => 'primary',
										__( 'Blue', 'conico' ) => 'blue',
										__( 'Turquoise', 'conico' ) => 'turquoise',
										__( 'Pink', 'conico' ) => 'pink',
										__( 'Violet', 'conico' ) => 'violet',
										__( 'Peacoc', 'conico' ) => 'peacoc',
										__( 'Chino', 'conico' ) => 'chino',
										__( 'Mulled Wine', 'conico' ) => 'mulled-wine',
										__( 'Vista Blue', 'conico' ) => 'vista-blue',
										__( 'Black', 'conico' ) => 'black',
										__( 'Grey', 'conico' ) => 'grey',
										__( 'Orange', 'conico' ) => 'orange',
										__( 'Sky', 'conico' ) => 'sky',
										__( 'Green', 'conico' ) => 'green',
										__( 'Juicy pink', 'conico' ) => 'juicy-pink',
										__( 'Sandy brown', 'conico' ) => 'sandy-brown',
										__( 'Purple', 'conico' ) => 'purple',
										__( 'White', 'conico' ) => 'white',
									);
									$param['std'] = 'inverse';
								}
							} else {
								$param['value'][ __( 'Feather', 'conico' ) ]     = 'feather';
								$param['value'][ __( 'Bootstrap', 'conico' ) ]   = 'bootstrap_font';
								$param['value'][ __( 'Aisconverse', 'conico' ) ] = 'aisconverse_font';
								$param['value'][ __( 'Theme', 'conico' ) ]       = 'theme_font';
								#$param['weight'] = 90;
							}
						}
						break;
					case 'vc_separator' :
						if ($param_value === 'color') {
							$param['value'][ __( 'Light gray', 'conico' ) ] = 'light_gray';
							$param['value'][ __( 'Dark gray', 'conico' ) ] = 'dark_gray';
						}
						break;
					case 'vc_icon' :

						if ( $param_value === 'type' ) {
							$param['value'][ __( 'Feather', 'conico' ) ]     = 'feather';
							$param['value'][ __( 'Bootstrap', 'conico' ) ]   = 'bootstrap_font';
							$param['value'][ __( 'Aisconverse', 'conico' ) ] = 'aisconverse_font';
							$param['value'][ __( 'Theme', 'conico' ) ] = 'theme_font';

							if ( in_array( 'basement-shortcodes/basement-shortcodes.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
								$param['value'][ __( 'Sharpicons', 'conico' ) ] = 'sharpicons';
							}

							$param['weight'] = 90;
						} elseif ($param_value === 'color') {
							$param['std'] = 'black';
						} elseif ($param_value === 'size') {
							$param['std'] = 'xl';
						} elseif ($param_value === 'align') {
							$param['std'] = 'center';
						}
						break;
				}

				if(function_exists('vc_update_shortcode_param')) {
					vc_update_shortcode_param( $key, $param );
				}
			}
		}

		/*
		 * WooCommerce Remove Params
		 */
		if ( function_exists( 'vc_remove_param' ) ) {
			vc_remove_param( 'recent_products', 'columns' );
			vc_remove_param('featured_products', 'columns');
			vc_remove_param('products', 'columns');
			vc_remove_param('product_category','columns');
			vc_remove_param('product_categories','columns');
			vc_remove_param('sale_products','columns');
			vc_remove_param('best_selling_products','columns');
			vc_remove_param('top_rated_products','columns');
		}


		/*Messages*/
		if ( function_exists( 'vc_remove_param' ) ) {
			vc_remove_param( "vc_message", "css_animation" );
			vc_remove_param( "vc_message", "content" );
			vc_remove_param( "vc_message", "el_class" );
			vc_remove_param( "vc_message", "css" );
		}
		vc_add_params( 'vc_message',
			array(
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'icon_feather',
					'value'       => "icon-eye",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 131,
						'type'         => 'feather'
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'feather',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'icon_bootstrap_font',
					'value'       => "glyphicon glyphicon-asterisk",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'bootstrap_font'
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'bootstrap_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'icon_aisconverse_font',
					'value'       => "aisconverse_icon armchair_chair_streamline",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'aisconverse_font'
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'aisconverse_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'icon_theme_font',
					'value'       => "icond-thin-0310-support-help-talk-call",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'theme_font'
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'theme_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type' => 'textarea_html',
					'holder' => 'div',
					'class' => 'messagebox_text',
					'heading' => __( 'Message text', 'conico' ),
					'param_name' => 'content',
					'value' => __( '<p>I am message box. Click edit button to change this text.</p>', 'conico' ),
				),
				vc_map_add_css_animation( false ),
				array(
					'type' => 'textfield',
					'heading' => __( 'Extra class name', 'conico' ),
					'param_name' => 'el_class',
					'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'conico' ),
				),
				array(
					'type' => 'css_editor',
					'heading' => __( 'CSS box', 'conico' ),
					'param_name' => 'css',
					'group' => __( 'Design Options', 'conico' ),
				)
			)
		);

		/*Tabs*/
		if ( function_exists( 'vc_remove_param' ) ) {
			vc_remove_param( "vc_tta_section", "el_class" );
		}
		vc_add_params( 'vc_tta_section',
			array(
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'i_icon_feather',
					'value'       => "icon-eye",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 131,
						'type'         => 'feather'
					),
					'dependency'  => array(
						'element' => 'i_type',
						'value'   => 'feather',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'i_icon_bootstrap_font',
					'value'       => "glyphicon glyphicon-asterisk",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'bootstrap_font'
					),
					'dependency'  => array(
						'element' => 'i_type',
						'value'   => 'bootstrap_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'i_icon_aisconverse_font',
					'value'       => "aisconverse_icon armchair_chair_streamline",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'aisconverse_font'
					),
					'dependency'  => array(
						'element' => 'i_type',
						'value'   => 'aisconverse_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'i_icon_theme_font',
					'value'       => "icond-thin-0310-support-help-talk-call",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'theme_font'
					),
					'dependency'  => array(
						'element' => 'i_type',
						'value'   => 'theme_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Extra class name', 'conico' ),
					'param_name' => 'el_class',
					'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'conico' ),
				)
			)
		);


		/*Single Image params*/
		vc_add_params('vc_single_image',
			array(
				array(
					'type' => 'dropdown',
					'heading' => __( 'On hover action', 'conico' ),
					'param_name' => 'onhover',
					'weight'      => 10,
					'value' => array(
						__( 'None', 'conico' ) => '',
						__( 'Scroll image', 'conico' ) => 'img_scroll'
					),
					'description' => __( 'Select action for hover action.', 'conico' ),
					'std' => ''
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Label', 'conico' ),
					'param_name' => 'label',
					'weight'      => 9,
					'description' => __( 'Sets the label for the image.', 'conico' )
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Height', 'conico' ),
					'param_name' => 'height',
					'weight'      => 8,
					'description' => __( 'Sets the height of the container around the image in px.', 'conico' )
				)
			)
		);

		/*Gmaps params*/
		if ( function_exists( 'vc_remove_param' ) ) {
			vc_remove_param( "vc_gmaps", "link" );
		}

		vc_add_params( 'vc_gmaps',
			array(
				array(
					'type' => 'dropdown',
					'heading' => __( 'Embed Map?', 'conico' ),
					'description' => __( 'Choose embed map or not.', 'conico' ),
					'param_name' => 'embed_map',
					'weight'      => 20,
					'value' => array(
						__( 'Yes', 'conico' ) => 'yes',
						__( 'No', 'conico' ) => 'no',
					),
				),
				array(
					'type' => 'textarea_safe',
					'heading' => __( 'Map embed iframe', 'conico' ),
					'param_name' => 'link',
					'weight'      => 2,
					'value' => sprintf('<%1$s src="%2$s" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></%1$s>', 'iframe','//www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6304.829986131271!2d-122.4746968033092!3d37.80374752160443!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808586e6302615a1%3A0x86bd130251757c00!2sStorey+Ave%2C+San+Francisco%2C+CA+94129!5e0!3m2!1sen!2sus!4v1435826432051'),
					'description' => sprintf( __( 'Visit %s to create your map (Step by step: 1) Find location 2) Click the cog symbol in the lower right corner and select "Share or embed map" 3) On modal window select "Embed map" 4) Copy iframe code and paste it).', 'conico' ), '<a href="//www.google.com/maps" target="_blank">' . __( 'Google maps', 'conico' ) . '</a>' ),
					'dependency'  => array(
						'element' => 'embed_map',
						'value'   => 'yes'
					)
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Latitude', 'conico' ),
					'param_name' => 'latitude',
					'weight'      => 4,
					'description' => __( 'Set map center point latitude. Creates google map with custom parameters. You can get latitude and longitude here <a href="//www.latlong.net/" target="_blank" title="">http://www.latlong.net/</a>', 'conico' ),
					'dependency'  => array(
						'element' => 'embed_map',
						'value'   => 'no'
					)
				),
				array(
					'type' => 'textfield',
					'weight'      => 3,
					'heading' => __( 'Longitude', 'conico' ),
					'param_name' => 'longitude',
					'description' => __( 'Set map center point longitude.', 'conico' ),
					'dependency'  => array(
						'element' => 'embed_map',
						'value'   => 'no'
					)
				),
				array(
					'type' => 'textfield',
					'weight'      => 5,
					'heading' => __( 'Zoom', 'conico' ),
					'param_name' => 'zoom',
					'description' => __( 'Use zoom value from 0 to 19.', 'conico' ),
					'dependency'  => array(
						'element' => 'embed_map',
						'value'   => 'no'
					)
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Style', 'conico' ),
					'description' => __( 'Choose the map style.', 'conico' ),
					'param_name' => 'style',
					'weight'      => 6,
					'value' => array(
						__( 'Default', 'conico' ) => 'default',
						__( 'Shades of Grey', 'conico' ) => 'shades_gray',
						__( 'Ultra Light with Labels', 'conico' ) => 'ultra_light_labels',
						__( 'Pastel Tones', 'conico' ) => 'pastel_tones'
					),
					'dependency'  => array(
						'element' => 'embed_map',
						'value'   => 'no'
					)
				),
				array(
					'type' => 'checkbox',
					'weight'      => 7,
					'heading' => __( 'Full screen controls', 'conico' ),
					'description' => __( 'Enable map full screen controlls.', 'conico' ),
					'param_name' => 'full_screen',
					'dependency'  => array(
						'element' => 'embed_map',
						'value'   => 'no'
					)
				),



				array(
					'type' => 'textfield',
					'weight'      => 17,
					'value' => '0',
					'heading' => __( 'Horizontal offset on large devices', 'conico' ),
					'param_name' => 'lg_horizontal_offset',
					'dependency'  => array(
						'element' => 'marker_position',
						'value'   => 'true'
					),
					'group' => __('Marker position', 'conico')
				),
				array(
					'type' => 'textfield',
					'weight'      => 18,
					'value' => '0',
					'heading' => __( 'Vertical offset on large devices', 'conico' ),
					'param_name' => 'lg_vertical_offset',
					'dependency'  => array(
						'element' => 'marker_position',
						'value'   => 'true'
					),
					'group' => __('Marker position', 'conico')
				),



				array(
					'type' => 'textfield',
					'weight'      => 15,
					'value' => '0',
					//'heading' => __( 'Left/Right offset on medium devices', 'conico' ),
					'param_name' => 'md_horizontal_offset',
					'heading' => __( 'Horizontal offset on medium devices', 'conico' ),
					'dependency'  => array(
						'element' => 'marker_position',
						'value'   => 'true'
					),
					'group' => __('Marker position', 'conico')
				),
				array(
					'type' => 'textfield',
					'weight'      => 16,
					'value' => '0',
					'heading' => __( 'Vertical offset on medium devices', 'conico' ),
					'param_name' => 'md_vertical_offset',
					'dependency'  => array(
						'element' => 'marker_position',
						'value'   => 'true'
					),
					'group' => __('Marker position', 'conico')
				),



				array(
					'type' => 'textfield',
					'weight'      => 13,
					'value' => '0',
					//'heading' => __( 'Left/Right offset on medium devices', 'conico' ),
					'param_name' => 'sm_horizontal_offset',
					'heading' => __( 'Horizontal offset on small devices', 'conico' ),
					'dependency'  => array(
						'element' => 'marker_position',
						'value'   => 'true'
					),
					'group' => __('Marker position', 'conico')
				),
				array(
					'type' => 'textfield',
					'weight'      => 14,
					'value' => '0',
					'heading' => __( 'Vertical offset on small devices', 'conico' ),
					'param_name' => 'sm_vertical_offset',
					'dependency'  => array(
						'element' => 'marker_position',
						'value'   => 'true'
					),
					'group' => __('Marker position', 'conico')
				),





				array(
					'type' => 'textfield',
					'weight'      => 11,
					'value' => '0',
					'heading' => __( 'Horizontal offset on extra small devices', 'conico' ),
					'param_name' => 'xs_horizontal_offset',
					'dependency'  => array(
						'element' => 'marker_position',
						'value'   => 'true'
					),
					'group' => __('Marker position', 'conico'),
					'description' => __( 'All values are set in pixels (negative/positive values in a rectangular coordinate system). 0 - default value at which the marker is centered strictly on the map.', 'conico' ),
				),
				array(
					'type' => 'textfield',
					'weight'      => 12,
					'value' => '0',
					'heading' => __( 'Vertical offset on extra small devices', 'conico' ),
					'param_name' => 'xs_vertical_offset',
					'dependency'  => array(
						'element' => 'marker_position',
						'value'   => 'true'
					),
					'group' => __('Marker position', 'conico')
				),




				array(
					'type' => 'checkbox',
					'weight'      => 19,
					'heading' => __( 'Control the marker position?', 'conico' ),
					'param_name' => 'marker_position',
					'description' => __( 'Marker positions (in px) on different screen sizes.', 'conico' ),
					'dependency'  => array(
						'element' => 'embed_map',
						'value'   => 'no'
					)
				)
			)
		);


		/*Button params*/
		if ( function_exists( 'vc_remove_param' ) ) {
			vc_remove_param( "vc_btn", "css_animation" );
			vc_remove_param( "vc_btn", "el_class" );
			vc_remove_param( "vc_btn", "custom_onclick" );
			vc_remove_param( "vc_btn", "custom_onclick_code" );
			vc_remove_param( "vc_btn", "css" );
		}
		vc_add_params( 'vc_btn',
			array(
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'i_icon_feather',
					'value'       => "icon-eye",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 131,
						'type'         => 'feather'
					),
					'dependency'  => array(
						'element' => 'i_type',
						'value'   => 'feather',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'i_icon_bootstrap_font',
					'value'       => "glyphicon glyphicon-asterisk",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'bootstrap_font'
					),
					'dependency'  => array(
						'element' => 'i_type',
						'value'   => 'bootstrap_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'i_icon_aisconverse_font',
					'value'       => "aisconverse_icon armchair_chair_streamline",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'aisconverse_font'
					),
					'dependency'  => array(
						'element' => 'i_type',
						'value'   => 'aisconverse_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'conico' ),
					'param_name'  => 'i_icon_theme_font',
					'value'       => "icond-thin-0310-support-help-talk-call",
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 500,
						'type'         => 'theme_font'
					),
					'dependency'  => array(
						'element' => 'i_type',
						'value'   => 'theme_font',
					),
					'description' => __( 'Select icon from library.', 'conico' ),
					'integrated_shortcode' => 'vc_icon',
					'integrated_shortcode_field' => 'i_'
				),
				vc_map_add_css_animation( true ),
				array(
					'type' => 'textfield',
					'heading' => __( 'Extra class name', 'conico' ),
					'param_name' => 'el_class',
					'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'conico' ),
				),
				array(
					'type' => 'checkbox',
					'heading' => __( 'Advanced on click action', 'conico' ),
					'param_name' => 'custom_onclick',
					'description' => __( 'Insert inline onclick javascript action.', 'conico' ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'On click code', 'conico' ),
					'param_name' => 'custom_onclick_code',
					'description' => __( 'Enter onclick action code.', 'conico' ),
					'dependency' => array(
						'element' => 'custom_onclick',
						'not_empty' => true,
					),
				),
				array(
					'type' => 'css_editor',
					'heading' => __( 'CSS box', 'conico' ),
					'param_name' => 'css',
					'group' => __( 'Design Options', 'conico' ),
				)
			)
		);





		/*Icon params*/
		$vc_icon_params = array(
			array(
				'type' => 'dropdown',
				'heading' => __( 'Display Type', 'conico' ),
				'param_name' => 'display',
				'value' => array(
					__( 'Block', 'conico' ) => '',
					__( 'Inline', 'conico' ) => 'inline'
				),
				'description' => __( 'Select the icon display type.', 'conico' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Vertical align', 'conico' ),
				'param_name' => 'vertical_align',
				'value' => array(
					__( 'Top', 'conico' ) => 'top',
					__( 'Middle', 'conico' ) => 'middle',
					__( 'Bottom', 'conico' ) => 'bottom',
					__( 'Baseline', 'conico' ) => 'baseline',
					__( 'Super', 'conico' ) => 'super',
					__( 'Sub', 'conico' ) => 'sub',
					__( 'Text top', 'conico' ) => 'text-top',
					__( 'Text bottom', 'conico' ) => 'text-bottom',
					__( 'Inherit', 'conico' ) => 'inherit'
				),
				'description' => __( 'Sets the vertical alignment of the elements relative to each other.', 'conico' ),
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => __( 'Icon', 'conico' ),
				'param_name'  => 'icon_feather',
				'value'       => "icon-eye",
				'settings'    => array(
					'emptyIcon'    => false,
					'iconsPerPage' => 131,
					'type'         => 'feather'
				),
				'weight'      => 80,
				'dependency'  => array(
					'element' => 'type',
					'value'   => 'feather',
				),
				'description' => __( 'Select icon from library.', 'conico' )
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => __( 'Icon', 'conico' ),
				'param_name'  => 'icon_bootstrap_font',
				'value'       => "glyphicon glyphicon-asterisk",
				'settings'    => array(
					'emptyIcon'    => false,
					'iconsPerPage' => 500,
					'type'         => 'bootstrap_font'
				),
				'weight'      => 81,
				'dependency'  => array(
					'element' => 'type',
					'value'   => 'bootstrap_font',
				),
				'description' => __( 'Select icon from library.', 'conico' )
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => __( 'Icon', 'conico' ),
				'param_name'  => 'icon_aisconverse_font',
				'value'       => "aisconverse_icon armchair_chair_streamline",
				'settings'    => array(
					'emptyIcon'    => false,
					'iconsPerPage' => 500,
					'type'         => 'aisconverse_font'
				),
				'weight'      => 82,
				'dependency'  => array(
					'element' => 'type',
					'value'   => 'aisconverse_font',
				),
				'description' => __( 'Select icon from library.', 'conico' )
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => __( 'Icon', 'conico' ),
				'param_name'  => 'icon_theme_font',
				'value'       => "icond-thin-0310-support-help-talk-call",
				'settings'    => array(
					'emptyIcon'    => false,
					'iconsPerPage' => 500,
					'type'         => 'theme_font'
				),
				'weight'      => 83,
				'dependency'  => array(
					'element' => 'type',
					'value'   => 'theme_font',
				),
				'description' => __( 'Select icon from library.', 'conico' )
			)
		);
		if ( in_array( 'basement-shortcodes/basement-shortcodes.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$vc_icon_params[] = array(
				'type'        => 'iconpicker',
				'heading'     => __( 'Icon', 'conico' ),
				'param_name'  => 'icon_sharpicons',
				'value'       => "si-music-add-mic",
				'settings'    => array(
					'emptyIcon'    => false,
					'iconsPerPage' => 90,
					'type'         => 'sharpicons'
				),
				'weight'      => 84,
				'dependency'  => array(
					'element' => 'type',
					'value'   => 'sharpicons',
				),
				'description' => __( 'Select icon from library.', 'conico' )
			);
		}
		vc_add_params( 'vc_icon', $vc_icon_params);

	}

	add_action( 'vc_after_init', 'conico_vc_after_init_actions', 999 );
}


if ( ! function_exists( 'conico_vc_iconpicker_type_bootstrap_font' ) ) {
	/**
	 * Bootstrap font for VC icon_picker
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_iconpicker_type_bootstrap_font( $icons ) {

		$icons = array(
			array( 'glyphicon glyphicon-asterisk' => __('Asterisk','conico' ) ),
			array( 'glyphicon glyphicon-plus' => __('Plus','conico' ) ),
			array( 'glyphicon glyphicon-euro' => __('Euro','conico' ) ),
			array( 'glyphicon-eur' => __('Eur','conico' ) ),
			array( 'glyphicon glyphicon-minus' => __('Minus','conico' ) ),
			array( 'glyphicon glyphicon-cloud' => __('Cloud','conico' ) ),
			array( 'glyphicon glyphicon-envelope' => __('Envelope','conico' ) ),
			array( 'glyphicon glyphicon-pencil' => __('Pencil','conico' ) ),
			array( 'glyphicon glyphicon-glass' => __('Glass','conico' ) ),
			array( 'glyphicon glyphicon-music' => __('Music','conico' ) ),
			array( 'glyphicon glyphicon-search' => __('Search','conico' ) ),
			array( 'glyphicon glyphicon-heart' => __('Heart','conico' ) ),
			array( 'glyphicon glyphicon-star' => __('Star','conico' ) ),
			array( 'glyphicon glyphicon-star-empty' => __('Star empty','conico' ) ),
			array( 'glyphicon glyphicon-user' => __('User','conico' ) ),
			array( 'glyphicon glyphicon-film' => __('Film','conico' ) ),
			array( 'glyphicon glyphicon-th-large' => __('Th large','conico' ) ),
			array( 'glyphicon glyphicon-th' => __('Th','conico' ) ),
			array( 'glyphicon glyphicon-th-list' => __('Th list','conico' ) ),
			array( 'glyphicon glyphicon-ok' => __('Ok','conico' ) ),
			array( 'glyphicon glyphicon-remove' => __('Remove','conico' ) ),
			array( 'glyphicon glyphicon-zoom-in' => __('Zoom in','conico' ) ),
			array( 'glyphicon glyphicon-zoom-out' => __('Zoom out','conico' ) ),
			array( 'glyphicon glyphicon-off' => __('Off','conico' ) ),
			array( 'glyphicon glyphicon-signal' => __('Signal','conico' ) ),
			array( 'glyphicon glyphicon-cog' => __('Cog','conico' ) ),
			array( 'glyphicon glyphicon-trash' => __('Trash','conico' ) ),
			array( 'glyphicon glyphicon-home' => __('Home','conico' ) ),
			array( 'glyphicon glyphicon-file' => __('File','conico' ) ),
			array( 'glyphicon glyphicon-time' => __('Time','conico' ) ),
			array( 'glyphicon glyphicon-road' => __('Road','conico' ) ),
			array( 'glyphicon glyphicon-download-alt' => __('Download alt','conico' ) ),
			array( 'glyphicon glyphicon-download' => __('Download','conico' ) ),
			array( 'glyphicon glyphicon-upload' => __('Upload','conico' ) ),
			array( 'glyphicon glyphicon-inbox' => __('Inbox','conico' ) ),
			array( 'glyphicon glyphicon-play-circle' => __('Play circle','conico' ) ),
			array( 'glyphicon glyphicon-repeat' => __('Repeat','conico' ) ),
			array( 'glyphicon glyphicon-refresh' => __('Refresh','conico' ) ),
			array( 'glyphicon glyphicon-list-alt' => __('List alt','conico' ) ),
			array( 'glyphicon glyphicon-lock' => __('Lock','conico' ) ),
			array( 'glyphicon glyphicon-flag' => __('Flag','conico' ) ),
			array( 'glyphicon glyphicon-headphones' => __('Headphones','conico' ) ),
			array( 'glyphicon glyphicon-volume-off' => __('Volume off','conico' ) ),
			array( 'glyphicon glyphicon-volume-down' => __('Volume down','conico' ) ),
			array( 'glyphicon glyphicon-volume-up' => __('Volume up','conico' ) ),
			array( 'glyphicon glyphicon-qrcode' => __('Qrcode','conico' ) ),
			array( 'glyphicon glyphicon-barcode' => __('Barcode','conico' ) ),
			array( 'glyphicon glyphicon-tag' => __('Tag','conico' ) ),
			array( 'glyphicon glyphicon-tags' => __('Tags','conico' ) ),
			array( 'glyphicon glyphicon-book' => __('Book','conico' ) ),
			array( 'glyphicon glyphicon-bookmark' => __('Bookmark','conico' ) ),
			array( 'glyphicon glyphicon-print' => __('Print','conico' ) ),
			array( 'glyphicon glyphicon-camera' => __('Camera','conico' ) ),
			array( 'glyphicon glyphicon-font' => __('Font','conico' ) ),
			array( 'glyphicon glyphicon-bold' => __('Bold','conico' ) ),
			array( 'glyphicon glyphicon-italic' => __('Italic','conico' ) ),
			array( 'glyphicon glyphicon-text-height' => __('Text height','conico' ) ),
			array( 'glyphicon glyphicon-text-width' => __('Text width','conico' ) ),
			array( 'glyphicon glyphicon-align-left' => __('Align left','conico' ) ),
			array( 'glyphicon glyphicon-align-center' => __('Align center','conico' ) ),
			array( 'glyphicon glyphicon-align-right' => __('Align right','conico' ) ),
			array( 'glyphicon glyphicon-align-justify' => __('Align justify','conico' ) ),
			array( 'glyphicon glyphicon-list' => __('List','conico' ) ),
			array( 'glyphicon glyphicon-indent-left' => __('Indent left','conico' ) ),
			array( 'glyphicon glyphicon-indent-right' => __('Indent right','conico' ) ),
			array( 'glyphicon glyphicon-facetime-video' => __('Facetime video','conico' ) ),
			array( 'glyphicon glyphicon-picture' => __('Picture','conico' ) ),
			array( 'glyphicon glyphicon-map-marker' => __('Map marker','conico' ) ),
			array( 'glyphicon glyphicon-adjust' => __('Adjust','conico' ) ),
			array( 'glyphicon glyphicon-tint' => __('Tint','conico' ) ),
			array( 'glyphicon glyphicon-edit' => __('Edit','conico' ) ),
			array( 'glyphicon glyphicon-share' => __('Share','conico' ) ),
			array( 'glyphicon glyphicon-check' => __('Check','conico' ) ),
			array( 'glyphicon glyphicon-move' => __('Move','conico' ) ),
			array( 'glyphicon glyphicon-step-backward' => __('Step backward','conico' ) ),
			array( 'glyphicon glyphicon-fast-backward' => __('Fast backward','conico' ) ),
			array( 'glyphicon glyphicon-backward' => __('Backward','conico' ) ),
			array( 'glyphicon glyphicon-play' => __('Play','conico' ) ),
			array( 'glyphicon glyphicon-pause' => __('Pause','conico' ) ),
			array( 'glyphicon glyphicon-stop' => __('Stop','conico' ) ),
			array( 'glyphicon glyphicon-forward' => __('Forward','conico' ) ),
			array( 'glyphicon glyphicon-fast-forward' => __('Fast forward','conico' ) ),
			array( 'glyphicon glyphicon-step-forward' => __('Step forward','conico' ) ),
			array( 'glyphicon glyphicon-eject' => __('Eject','conico' ) ),
			array( 'glyphicon glyphicon-chevron-left' => __('Chevron left','conico' ) ),
			array( 'glyphicon glyphicon-chevron-right' => __('Chevron right','conico' ) ),
			array( 'glyphicon glyphicon-plus-sign' => __('Plus sign','conico' ) ),
			array( 'glyphicon glyphicon-minus-sign' => __('Minus sign','conico' ) ),
			array( 'glyphicon glyphicon-remove-sign' => __('Remove sign','conico' ) ),
			array( 'glyphicon glyphicon-ok-sign' => __('Ok sign','conico' ) ),
			array( 'glyphicon glyphicon-question-sign' => __('Question sign','conico' ) ),
			array( 'glyphicon glyphicon-info-sign' => __('Info sign','conico' ) ),
			array( 'glyphicon glyphicon-screenshot' => __('Screenshot','conico' ) ),
			array( 'glyphicon glyphicon-remove-circle' => __('Remove circle','conico' ) ),
			array( 'glyphicon glyphicon-ok-circle' => __('Ok circle','conico' ) ),
			array( 'glyphicon glyphicon-ban-circle' => __('Ban circle','conico' ) ),
			array( 'glyphicon glyphicon-arrow-left' => __('Arrow left','conico' ) ),
			array( 'glyphicon glyphicon-arrow-right' => __('Arrow right','conico' ) ),
			array( 'glyphicon glyphicon-arrow-up' => __('Arrow up','conico' ) ),
			array( 'glyphicon glyphicon-arrow-down' => __('Arrow down','conico' ) ),
			array( 'glyphicon glyphicon-share-alt' => __('Share alt','conico' ) ),
			array( 'glyphicon glyphicon-resize-full' => __('Resize full','conico' ) ),
			array( 'glyphicon glyphicon-resize-small' => __('Resize small','conico' ) ),
			array( 'glyphicon glyphicon-exclamation-sign' => __('Exclamation sign','conico' ) ),
			array( 'glyphicon glyphicon-gift' => __('Gift','conico' ) ),
			array( 'glyphicon glyphicon-leaf' => __('Leaf','conico' ) ),
			array( 'glyphicon glyphicon-fire' => __('Fire','conico' ) ),
			array( 'glyphicon glyphicon-eye-open' => __('Eye open','conico' ) ),
			array( 'glyphicon glyphicon-eye-close' => __('Eye close','conico' ) ),
			array( 'glyphicon glyphicon-warning-sign' => __('Warning sign','conico' ) ),
			array( 'glyphicon glyphicon-plane' => __('Plane','conico' ) ),
			array( 'glyphicon glyphicon-calendar' => __('Calendar','conico' ) ),
			array( 'glyphicon glyphicon-random' => __('Random','conico' ) ),
			array( 'glyphicon glyphicon-comment' => __('Comment','conico' ) ),
			array( 'glyphicon glyphicon-magnet' => __('Magnet','conico' ) ),
			array( 'glyphicon glyphicon-chevron-up' => __('Chevron up','conico' ) ),
			array( 'glyphicon glyphicon-chevron-down' => __('Chevron down','conico' ) ),
			array( 'glyphicon glyphicon-retweet' => __('Retweet','conico' ) ),
			array( 'glyphicon glyphicon-shopping-cart' => __('Shopping cart','conico' ) ),
			array( 'glyphicon glyphicon-folder-close' => __('Folder close','conico' ) ),
			array( 'glyphicon glyphicon-folder-open' => __('Folder open','conico' ) ),
			array( 'glyphicon glyphicon-resize-vertical' => __('Resize vertical','conico' ) ),
			array( 'glyphicon glyphicon-resize-horizontal' => __('Resize horizontal','conico' ) ),
			array( 'glyphicon glyphicon-hdd' => __('Hdd','conico' ) ),
			array( 'glyphicon glyphicon-bullhorn' => __('Bullhorn','conico' ) ),
			array( 'glyphicon glyphicon-bell' => __('Bell','conico' ) ),
			array( 'glyphicon glyphicon-certificate' => __('Certificate','conico' ) ),
			array( 'glyphicon glyphicon-thumbs-up' => __('Thumbs up','conico' ) ),
			array( 'glyphicon glyphicon-thumbs-down' => __('Thumbs down','conico' ) ),
			array( 'glyphicon glyphicon-hand-right' => __('Hand right','conico' ) ),
			array( 'glyphicon glyphicon-hand-left' => __('Hand left','conico' ) ),
			array( 'glyphicon glyphicon-hand-up' => __('Hand up','conico' ) ),
			array( 'glyphicon glyphicon-hand-down' => __('Hand down','conico' ) ),
			array( 'glyphicon glyphicon-circle-arrow-right' => __('Circle arrow-right','conico' ) ),
			array( 'glyphicon glyphicon-circle-arrow-left' => __('Circle arrow-left','conico' ) ),
			array( 'glyphicon glyphicon-circle-arrow-up' => __('Circle arrow-up','conico' ) ),
			array( 'glyphicon glyphicon-circle-arrow-down' => __('Circle arrow-down','conico' ) ),
			array( 'glyphicon glyphicon-globe' => __('Globe','conico' ) ),
			array( 'glyphicon glyphicon-wrench' => __('Wrench','conico' ) ),
			array( 'glyphicon glyphicon-tasks' => __('Tasks','conico' ) ),
			array( 'glyphicon glyphicon-filter' => __('Filter','conico' ) ),
			array( 'glyphicon glyphicon-briefcase' => __('Briefcase','conico' ) ),
			array( 'glyphicon glyphicon-fullscreen' => __('Fullscreen','conico' ) ),
			array( 'glyphicon glyphicon-dashboard' => __('Dashboard','conico' ) ),
			array( 'glyphicon glyphicon-paperclip' => __('Paperclip','conico' ) ),
			array( 'glyphicon glyphicon-heart-empty' => __('Heart empty','conico' ) ),
			array( 'glyphicon glyphicon-link' => __('Link','conico' ) ),
			array( 'glyphicon glyphicon-phone' => __('Phone','conico' ) ),
			array( 'glyphicon glyphicon-pushpin' => __('Pushpin','conico' ) ),
			array( 'glyphicon glyphicon-usd' => __('Usd','conico' ) ),
			array( 'glyphicon glyphicon-gbp' => __('Gbp','conico' ) ),
			array( 'glyphicon glyphicon-sort' => __('Sort','conico' ) ),
			array( 'glyphicon glyphicon-sort-by-alphabet' => __('Sort by-alphabet','conico' ) ),
			array( 'glyphicon glyphicon-sort-by-alphabet-alt' => __('Sort by-alphabet-alt','conico' ) ),
			array( 'glyphicon glyphicon-sort-by-order' => __('Sort by-order','conico' ) ),
			array( 'glyphicon glyphicon-sort-by-order-alt' => __('Sort by-order-alt','conico' ) ),
			array( 'glyphicon glyphicon-sort-by-attributes' => __('Sort by-attributes','conico' ) ),
			array( 'glyphicon glyphicon-sort-by-attributes-alt' => __('Sort by-attributes-alt','conico' ) ),
			array( 'glyphicon glyphicon-unchecked' => __('Unchecked','conico' ) ),
			array( 'glyphicon glyphicon-expand' => __('Expand','conico' ) ),
			array( 'glyphicon glyphicon-collapse-down' => __('Collapse down','conico' ) ),
			array( 'glyphicon glyphicon-collapse-up' => __('Collapse up','conico' ) ),
			array( 'glyphicon glyphicon-log-in' => __('Log in','conico' ) ),
			array( 'glyphicon glyphicon-flash' => __('Flash','conico' ) ),
			array( 'glyphicon glyphicon-log-out' => __('Log out','conico' ) ),
			array( 'glyphicon glyphicon-new-window' => __('New window','conico' ) ),
			array( 'glyphicon glyphicon-record' => __('Record','conico' ) ),
			array( 'glyphicon glyphicon-save' => __('Save','conico' ) ),
			array( 'glyphicon glyphicon-open' => __('Open','conico' ) ),
			array( 'glyphicon glyphicon-saved' => __('Saved','conico' ) ),
			array( 'glyphicon glyphicon-import' => __('Import','conico' ) ),
			array( 'glyphicon glyphicon-export' => __('Export','conico' ) ),
			array( 'glyphicon glyphicon-send' => __('Send','conico' ) ),
			array( 'glyphicon glyphicon-floppy-disk' => __('Floppy disk','conico' ) ),
			array( 'glyphicon glyphicon-floppy-saved' => __('Floppy saved','conico' ) ),
			array( 'glyphicon glyphicon-floppy-remove' => __('Floppy remove','conico' ) ),
			array( 'glyphicon glyphicon-floppy-save' => __('Floppy save','conico' ) ),
			array( 'glyphicon glyphicon-floppy-open' => __('Floppy open','conico' ) ),
			array( 'glyphicon glyphicon-credit-card' => __('Credit card','conico' ) ),
			array( 'glyphicon glyphicon-transfer' => __('Transfer','conico' ) ),
			array( 'glyphicon glyphicon-cutlery' => __('Cutlery','conico' ) ),
			array( 'glyphicon glyphicon-header' => __('Header','conico' ) ),
			array( 'glyphicon glyphicon-compressed' => __('Compressed','conico' ) ),
			array( 'glyphicon glyphicon-earphone' => __('Earphone','conico' ) ),
			array( 'glyphicon glyphicon-phone-alt' => __('Phone alt','conico' ) ),
			array( 'glyphicon glyphicon-tower' => __('Tower','conico' ) ),
			array( 'glyphicon glyphicon-stats' => __('Stats','conico' ) ),
			array( 'glyphicon glyphicon-sd-video' => __('Sd video','conico' ) ),
			array( 'glyphicon glyphicon-hd-video' => __('Hd video','conico' ) ),
			array( 'glyphicon glyphicon-subtitles' => __('Subtitles','conico' ) ),
			array( 'glyphicon glyphicon-sound-stereo' => __('Sound stereo','conico' ) ),
			array( 'glyphicon glyphicon-sound-dolby' => __('Sound dolby','conico' ) ),
			array( 'glyphicon glyphicon-sound-5-1' => __('Sound 5-1','conico' ) ),
			array( 'glyphicon glyphicon-sound-6-1' => __('Sound 6-1','conico' ) ),
			array( 'glyphicon glyphicon-sound-7-1' => __('Sound 7-1','conico' ) ),
			array( 'glyphicon glyphicon-copyright-mark' => __('Copyright mark','conico' ) ),
			array( 'glyphicon glyphicon-registration-mark' => __('Registration mark','conico' ) ),
			array( 'glyphicon glyphicon-cloud-download' => __('Cloud download','conico' ) ),
			array( 'glyphicon glyphicon-cloud-upload' => __('Cloud upload','conico' ) ),
			array( 'glyphicon glyphicon-tree-conifer' => __('Tree conifer','conico' ) ),
			array( 'glyphicon glyphicon-tree-deciduous' => __('Tree deciduous','conico' ) ),
			array( 'glyphicon glyphicon-cd' => __('Cd','conico' ) ),
			array( 'glyphicon glyphicon-save-file' => __('Save file','conico' ) ),
			array( 'glyphicon glyphicon-open-file' => __('Open file','conico' ) ),
			array( 'glyphicon glyphicon-level-up' => __('Level up','conico' ) ),
			array( 'glyphicon glyphicon-copy' => __('Copy','conico' ) ),
			array( 'glyphicon glyphicon-paste' => __('Paste','conico' ) ),
			array( 'glyphicon glyphicon-alert' => __('Alert','conico' ) ),
			array( 'glyphicon glyphicon-equalizer' => __('Equalizer','conico' ) ),
			array( 'glyphicon glyphicon-king' => __('King','conico' ) ),
			array( 'glyphicon glyphicon-queen' => __('Queen','conico' ) ),
			array( 'glyphicon glyphicon-pawn' => __('Pawn','conico' ) ),
			array( 'glyphicon glyphicon-bishop' => __('Bishop','conico' ) ),
			array( 'glyphicon glyphicon-knight' => __('Knight','conico' ) ),
			array( 'glyphicon glyphicon-baby-formula' => __('Baby formula','conico' ) ),
			array( 'glyphicon glyphicon-tent' => __('Tent','conico' ) ),
			array( 'glyphicon glyphicon-blackboard' => __('Blackboard','conico' ) ),
			array( 'glyphicon glyphicon-bed' => __('Bed','conico' ) ),
			array( 'glyphicon glyphicon-apple' => __('Apple','conico' ) ),
			array( 'glyphicon glyphicon-erase' => __('Erase','conico' ) ),
			array( 'glyphicon glyphicon-hourglass' => __('Hourglass','conico' ) ),
			array( 'glyphicon glyphicon-lamp' => __('Lamp','conico' ) ),
			array( 'glyphicon glyphicon-duplicate' => __('Duplicate','conico' ) ),
			array( 'glyphicon glyphicon-piggy-bank' => __('Piggy bank','conico' ) ),
			array( 'glyphicon glyphicon-scissors' => __('Scissors','conico' ) ),
			array( 'glyphicon glyphicon-bitcoin' => __('Bitcoin','conico' ) ),
			array( 'glyphicon glyphicon-btc' => __('Btc','conico' ) ),
			array( 'glyphicon glyphicon-xbt' => __('Xbt','conico' ) ),
			array( 'glyphicon glyphicon-yen' => __('Yen','conico' ) ),
			array( 'glyphicon glyphicon-jpy' => __('Jpy','conico' ) ),
			array( 'glyphicon glyphicon-ruble' => __('Ruble','conico' ) ),
			array( 'glyphicon glyphicon-rub' => __('Rub','conico' ) ),
			array( 'glyphicon glyphicon-scale' => __('Scale','conico' ) ),
			array( 'glyphicon glyphicon-ice-lolly' => __('Ice lolly','conico' ) ),
			array( 'glyphicon glyphicon-ice-lolly-tasted' => __('Ice lolly-tasted','conico' ) ),
			array( 'glyphicon glyphicon-education' => __('Education','conico' ) ),
			array( 'glyphicon glyphicon-option-horizontal' => __('Option horizontal','conico' ) ),
			array( 'glyphicon glyphicon-option-vertical' => __('Option vertical','conico' ) ),
			array( 'glyphicon glyphicon-menu-hamburger' => __('Menu hamburger','conico' ) ),
			array( 'glyphicon glyphicon-modal-window' => __('Modal window','conico' ) ),
			array( 'glyphicon glyphicon-oil' => __('Oil','conico' ) ),
			array( 'glyphicon glyphicon-grain' => __('Grain','conico' ) ),
			array( 'glyphicon glyphicon-sunglasses' => __('Sunglasses','conico' ) ),
			array( 'glyphicon glyphicon-text-size' => __('Text size','conico' ) ),
			array( 'glyphicon glyphicon-text-color' => __('Text color','conico' ) ),
			array( 'glyphicon glyphicon-text-background' => __('Text background','conico' ) ),
			array( 'glyphicon glyphicon-object-align-top' => __('Object align-top','conico' ) ),
			array( 'glyphicon glyphicon-object-align-bottom' => __('Object align-bottom','conico' ) ),
			array( 'glyphicon glyphicon-object-align-horizontal' => __('Object align-horizontal','conico' ) ),
			array( 'glyphicon glyphicon-object-align-left' => __('Object align-left','conico' ) ),
			array( 'glyphicon glyphicon-object-align-vertical' => __('Object align-vertical','conico' ) ),
			array( 'glyphicon glyphicon-object-align-right' => __('Object align-right','conico' ) ),
			array( 'glyphicon glyphicon-triangle-right' => __('Triangle right','conico' ) ),
			array( 'glyphicon glyphicon-triangle-left' => __('Triangle left','conico' ) ),
			array( 'glyphicon glyphicon-triangle-bottom' => __('Triangle bottom','conico' ) ),
			array( 'glyphicon glyphicon-triangle-top' => __('Triangle top','conico' ) ),
			array( 'glyphicon glyphicon-console' => __('Console','conico' ) ),
			array( 'glyphicon glyphicon-superscript' => __('Superscript','conico' ) ),
			array( 'glyphicon glyphicon-subscript' => __('Subscript','conico' ) ),
			array( 'glyphicon glyphicon-menu-left' => __('Menu left','conico' ) ),
			array( 'glyphicon glyphicon-menu-right' => __('Menu right','conico' ) ),
			array( 'glyphicon glyphicon-menu-down' => __('Menu down','conico' ) ),
			array( 'glyphicon glyphicon-menu-up' => __('Menu up', 'conico' ) )
		);

		return $icons;
	}

	add_filter( 'vc_iconpicker-type-bootstrap_font', 'conico_vc_iconpicker_type_bootstrap_font', 999 );
}


if ( ! function_exists( 'conico_vc_iconpicker_type_feather' ) ) {
	/**
	 * Feather font for VC icon_picker
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_iconpicker_type_feather( $icons ) {

		$icons = array(
			array( 'icon-eye' => __('Eye','conico') ),
			array( 'icon-paper-clip' => __('Paper clip','conico') ),
			array( 'icon-mail' => __('Mail','conico') ),
			array( 'icon-toggle' => __('Toggle','conico') ),
			array( 'icon-layout' => __('Layout','conico') ),
			array( 'icon-link' => __('Link','conico') ),
			array( 'icon-bell' => __('Bell','conico') ),
			array( 'icon-lock' => __('Lock','conico') ),
			array( 'icon-unlock' => __('Unlock','conico') ),
			array( 'icon-ribbon' => __('Ribbon','conico') ),
			array( 'icon-image' => __('Image','conico') ),
			array( 'icon-signal' => __('Signal','conico') ),
			array( 'icon-target' => __('Target','conico') ),
			array( 'icon-clipboard' => __('Clipboard','conico') ),
			array( 'icon-clock' => __('Clock','conico') ),
			array( 'icon-clock' => __('Clock','conico') ),
			array( 'icon-watch' => __('Watch','conico') ),
			array( 'icon-air-play' => __('Air play','conico') ),
			array( 'icon-camera' => __('Camera','conico') ),
			array( 'icon-video' => __('Video','conico') ),
			array( 'icon-disc' => __('Disc','conico') ),
			array( 'icon-printer' => __('Printer','conico') ),
			array( 'icon-monitor' => __('Monitor','conico') ),
			array( 'icon-server' => __('Server','conico') ),
			array( 'icon-cog' => __('Cog','conico') ),
			array( 'icon-heart' => __('Heart','conico') ),
			array( 'icon-paragraph' => __('Paragraph','conico') ),
			array( 'icon-align-justify' => __('Align justify','conico') ),
			array( 'icon-align-left' => __('Align left','conico') ),
			array( 'icon-align-center' => __('Align center','conico') ),
			array( 'icon-align-right' => __('Align right','conico') ),
			array( 'icon-book' => __('Book','conico') ),
			array( 'icon-layers' => __('Layers','conico') ),
			array( 'icon-stack' => __('Stack','conico') ),
			array( 'icon-stack-2' => __('Stack 2','conico') ),
			array( 'icon-paper' => __('Paper','conico') ),
			array( 'icon-paper-stack' => __('Paper stack','conico') ),
			array( 'icon-search' => __('Search','conico') ),
			array( 'icon-zoom-in' => __('Zoom in','conico') ),
			array( 'icon-zoom-out' => __('Zoom out','conico') ),
			array( 'icon-reply' => __('Reply','conico') ),
			array( 'icon-circle-plus' => __('Circle plus','conico') ),
			array( 'icon-circle-minus' => __('Circle minus','conico') ),
			array( 'icon-circle-check' => __('Circle check','conico') ),
			array( 'icon-circle-cross' => __('Circle cross','conico') ),
			array( 'icon-square-plus' => __('Square plus','conico') ),
			array( 'icon-square-minus' => __('Square minus','conico') ),
			array( 'icon-square-check' => __('Square check','conico') ),
			array( 'icon-square-cross' => __('Square cross','conico') ),
			array( 'icon-microphone' => __('Microphone','conico') ),
			array( 'icon-record' => __('Record','conico') ),
			array( 'icon-skip-back' => __('Skip back','conico') ),
			array( 'icon-rewind' => __('Rewind','conico') ),
			array( 'icon-play' => __('Play','conico') ),
			array( 'icon-pause' => __('Pause','conico') ),
			array( 'icon-stop' => __('Stop','conico') ),
			array( 'icon-fast-forward' => __('Fast forward','conico') ),
			array( 'icon-skip-forward' => __('Skip forward','conico') ),
			array( 'icon-shuffle' => __('Shuffle','conico') ),
			array( 'icon-repeat' => __('Repeat','conico') ),
			array( 'icon-folder' => __('Folder','conico') ),
			array( 'icon-umbrella' => __('Umbrella','conico') ),
			array( 'icon-moon' => __('Moon','conico') ),
			array( 'icon-thermometer' => __('Thermometer','conico') ),
			array( 'icon-drop' => __('Drop','conico') ),
			array( 'icon-sun' => __('Sun','conico') ),
			array( 'icon-cloud' => __('Cloud','conico') ),
			array( 'icon-cloud-upload' => __('Cloud upload','conico') ),
			array( 'icon-cloud-download' => __('Cloud download','conico') ),
			array( 'icon-upload' => __('Upload','conico') ),
			array( 'icon-download' => __('Download','conico') ),
			array( 'icon-location' => __('Location','conico') ),
			array( 'icon-location-2' => __('Location 2','conico') ),
			array( 'icon-map' => __('Map','conico') ),
			array( 'icon-battery' => __('Battery','conico') ),
			array( 'icon-head' => __('Head','conico') ),
			array( 'icon-briefcase' => __('Briefcase','conico') ),
			array( 'icon-speech-bubble' => __('Speech bubble','conico') ),
			array( 'icon-anchor' => __('Anchor','conico') ),
			array( 'icon-globe' => __('Globe','conico') ),
			array( 'icon-box' => __('Box','conico') ),
			array( 'icon-reload' => __('Reload','conico') ),
			array( 'icon-share' => __('Share','conico') ),
			array( 'icon-marquee' => __('Marquee','conico') ),
			array( 'icon-marquee-plus' => __('Marquee plus','conico') ),
			array( 'icon-marquee-minus' => __('Marquee minus','conico') ),
			array( 'icon-tag' => __('Tag','conico') ),
			array( 'icon-power' => __('Power','conico') ),
			array( 'icon-command' => __('Command','conico') ),
			array( 'icon-alt' => __('Alt','conico') ),
			array( 'icon-esc' => __('Esc','conico') ),
			array( 'icon-bar-graph' => __('Bar graph','conico') ),
			array( 'icon-bar-graph-2' => __('Bar graph-2','conico') ),
			array( 'icon-pie-graph' => __('Pie graph','conico') ),
			array( 'icon-star' => __('Star','conico') ),
			array( 'icon-arrow-left' => __('Arrow left','conico') ),
			array( 'icon-arrow-right' => __('Arrow right','conico') ),
			array( 'icon-arrow-up' => __('Arrow up','conico') ),
			array( 'icon-arrow-down' => __('Arrow down','conico') ),
			array( 'icon-volume' => __('Volume','conico') ),
			array( 'icon-mute' => __('Mute','conico') ),
			array( 'icon-content-right' => __('Content right','conico') ),
			array( 'icon-content-left' => __('Content left','conico') ),
			array( 'icon-grid' => __('Grid','conico') ),
			array( 'icon-grid-2' => __('Grid 2','conico') ),
			array( 'icon-columns' => __('Columns','conico') ),
			array( 'icon-loader' => __('Loader','conico') ),
			array( 'icon-bag' => __('Bag','conico') ),
			array( 'icon-ban' => __('Ban','conico') ),
			array( 'icon-flag' => __('Flag','conico') ),
			array( 'icon-trash' => __('Trash','conico') ),
			array( 'icon-expand' => __('Expand','conico') ),
			array( 'icon-contract' => __('Contract','conico') ),
			array( 'icon-maximize' => __('Maximize','conico') ),
			array( 'icon-minimize' => __('Minimize','conico') ),
			array( 'icon-plus' => __('Plus','conico') ),
			array( 'icon-minus' => __('Minus','conico') ),
			array( 'icon-check' => __('Check','conico') ),
			array( 'icon-cross' => __('Cross','conico') ),
			array( 'icon-move' => __('Move','conico') ),
			array( 'icon-delete' => __('Delete','conico') ),
			array( 'icon-menu' => __('Menu','conico') ),
			array( 'icon-archive' => __('Archive','conico') ),
			array( 'icon-inbox' => __('Inbox','conico') ),
			array( 'icon-outbox' => __('Outbox','conico') ),
			array( 'icon-file' => __('File','conico') ),
			array( 'icon-file-add' => __('File add','conico') ),
			array( 'icon-file-subtract' => __('File subtract','conico') ),
			array( 'icon-help' => __('Help','conico') ),
			array( 'icon-open' => __('Open','conico') ),
			array( 'icon-ellipsis' => __('Ellipsis','conico') )
		);

		return $icons;
	}

	add_filter( 'vc_iconpicker-type-feather', 'conico_vc_iconpicker_type_feather', 999 );
}


if ( ! function_exists( 'conico_vc_iconpicker_type_aisconverse_font' ) ) {
	/**
	 * Aisconverse font for VC icon_picker
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_iconpicker_type_aisconverse_font( $icons ) {

		$icons = array(
			array( 'aisconverse_icon armchair_chair_streamline' => __('Armchair chair streamline','conico') ),
			array( 'aisconverse_icon arrow_streamline_target' => __('Arrow streamline target','conico') ),
			array( 'aisconverse_icon backpack_streamline_trekking' => __('Backpack streamline trekking','conico') ),
			array( 'aisconverse_icon bag_shopping_streamline' => __('Bag shopping streamline','conico') ),
			array( 'aisconverse_icon barbecue_eat_food_streamline' => __('Barbecue eat food streamline','conico') ),
			array( 'aisconverse_icon barista_coffee_espresso_streamline' => __('Barista coffee espresso streamline','conico') ),
			array( 'aisconverse_icon bomb_bug' => __('Bomb bug','conico') ),
			array( 'aisconverse_icon book_dowload_streamline' => __('Book dowload streamline','conico') ),
			array( 'aisconverse_icon book_read_streamline' => __('Book read streamline','conico') ),
			array( 'aisconverse_icon caddie_shop_shopping_streamline' => __('Caddie shop shopping streamline','conico') ),
			array( 'aisconverse_icon caddie_shopping_streamline' => __('Caddie shopping streamline','conico') ),
			array( 'aisconverse_icon camera_photo_polaroid_streamline' => __('Camera photo polaroid streamline','conico') ),
			array( 'aisconverse_icon camera_photo_streamline' => __('Camera photo streamline','conico') ),
			array( 'aisconverse_icon camera_streamline_video' => __('Camera streamline video','conico') ),
			array( 'aisconverse_icon chaplin_hat_movie_streamline' => __('Chaplin hat movie streamline','conico') ),
			array( 'aisconverse_icon chef_food_restaurant_streamline' => __('Chef food restaurant streamline','conico') ),
			array( 'aisconverse_icon clock_streamline_time' => __('Clock streamline time','conico') ),
			array( 'aisconverse_icon cocktail_mojito_streamline' => __('Cocktail mojito streamline','conico') ),
			array( 'aisconverse_icon computer_network_streamline' => __('Computer network streamline','conico') ),
			array( 'aisconverse_icon computer_streamline' => __('Computer streamline','conico') ),
			array( 'aisconverse_icon cook_pan_pot_streamline' => __('Cook pan pot streamline','conico') ),
			array( 'aisconverse_icon crop_streamline' => __('Crop streamline','conico') ),
			array( 'aisconverse_icon crown_king_streamline' => __('Crown king streamline','conico') ),
			array( 'aisconverse_icon danger_death_delete_destroy_skull_stream' => __('Danger death delete destroy skull stream','conico') ),
			array( 'aisconverse_icon dashboard_speed_streamline' => __('Dashboard speed streamline','conico') ),
			array( 'aisconverse_icon database_streamline' => __('Database streamline','conico') ),
			array( 'aisconverse_icon delete_garbage_streamline' => __('Delete garbage streamline','conico') ),
			array( 'aisconverse_icon earth_globe_streamline' => __('Earth globe streamline','conico') ),
			array( 'aisconverse_icon eat_food_fork_knife_streamline' => __('Eat food fork knife streamline','conico') ),
			array( 'aisconverse_icon eat_food_hotdog_streamline' => __('Eat food hotdog streamline','conico') ),
			array( 'aisconverse_icon edit_modify_streamline' => __('Edit modify streamline','conico') ),
			array( 'aisconverse_icon email_mail_streamline' => __('Email mail streamline','conico') ),
			array( 'aisconverse_icon envellope_mail_streamline' => __('Envellope mail streamline','conico') ),
			array( 'aisconverse_icon eye_dropper_streamline' => __('Eye dropper streamline','conico') ),
			array( 'aisconverse_icon factory_lift_streamline_warehouse' => __('Factory lift streamline warehouse','conico') ),
			array( 'aisconverse_icon first_aid_medecine_shield_streamline' => __('First aid medecine shield streamline','conico') ),
			array( 'aisconverse_icon happy_smiley_streamline' => __('Happy smiley streamline','conico') ),
			array( 'aisconverse_icon headset_sound_streamline' => __('Headset sound streamline','conico') ),
			array( 'aisconverse_icon home_house_streamline' => __('Home house streamline','conico') ),
			array( 'aisconverse_icon ibook_laptop' => __('Ibook laptop','conico') ),
			array( 'aisconverse_icon ink_pen_streamline' => __('Ink pen streamline','conico') ),
			array( 'aisconverse_icon ipad_streamline' => __('Ipad streamline','conico') ),
			array( 'aisconverse_icon iphone_streamline' => __('Iphone streamline','conico') ),
			array( 'aisconverse_icon ipod_mini_music_streamline' => __('Ipod mini music streamline','conico') ),
			array( 'aisconverse_icon ipod_music_streamline' => __('Ipod music streamline','conico') ),
			array( 'aisconverse_icon link_streamline' => __('Link streamline','conico') ),
			array( 'aisconverse_icon lock_locker_streamline' => __('Lock locker streamline','conico') ),
			array( 'aisconverse_icon locker_streamline_unlock' => __('Locker streamline unlock','conico') ),
			array( 'aisconverse_icon macintosh' => __('Macintosh','conico') ),
			array( 'aisconverse_icon magic_magic_wand_streamline' => __('Magic magic wand streamline','conico') ),
			array( 'aisconverse_icon magnet_streamline' => __('Magnet streamline','conico') ),
			array( 'aisconverse_icon man_people_streamline_user' => __('Man people streamline user','conico') ),
			array( 'aisconverse_icon map_pin_streamline' => __('Map pin streamline','conico') ),
			array( 'aisconverse_icon map_streamline_user' => __('Map streamline user','conico') ),
			array( 'aisconverse_icon notebook_streamline' => __('Notebook streamline','conico') ),
			array( 'aisconverse_icon paint_bucket_streamline' => __('Paint bucket streamline','conico') ),
			array( 'aisconverse_icon painting_pallet_streamline' => __('Painting pallet streamline','conico') ),
			array( 'aisconverse_icon painting_roll_streamline' => __('Painting roll streamline','conico') ),
			array( 'aisconverse_icon pen_streamline' => __('Pen streamline','conico') ),
			array( 'aisconverse_icon pen_streamline_1' => __('Pen streamline 1','conico') ),
			array( 'aisconverse_icon pen_streamline_2' => __('Pen streamline 2','conico') ),
			array( 'aisconverse_icon pen_streamline_3' => __('Pen streamline 3','conico') ),
			array( 'aisconverse_icon photo_pictures_streamline' => __('Photo pictures streamline','conico') ),
			array( 'aisconverse_icon settings_streamline' => __('Settings streamline','conico') ),
			array( 'aisconverse_icon settings_streamline_1' => __('Settings streamline 1','conico') ),
			array( 'aisconverse_icon settings_streamline_2' => __('Settings streamline 2','conico') ),
			array( 'aisconverse_icon shoes_snickers_streamline' => __('Shoes snickers streamline','conico') ),
			array( 'aisconverse_icon speech_streamline_talk_user' => __('Speech streamline talk user','conico') ),
			array( 'aisconverse_icon stamp_streamline' => __('Stamp streamline','conico') ),
			array( 'aisconverse_icon streamline_suitcase_travel' => __('Streamline suitcase travel','conico') ),
			array( 'aisconverse_icon streamline_sync' => __('Streamline sync','conico') ),
			array( 'aisconverse_icon streamline_umbrella_weather' => __('Streamline umbrella weather','conico') ),
			array( 'aisconverse_icon browser_streamline_window' => __('Browser streamline window','conico') ),
			array( 'aisconverse_icon brush_paint_streamline' => __('Brush paint streamline','conico') ),
			array( 'aisconverse_icon bubble_comment_streamline_talk' => __('Bubble comment streamline talk','conico') ),
			array( 'aisconverse_icon bubble_love_streamline_talk' => __('Bubble love streamline talk','conico') ),
			array( 'aisconverse_icon coffee_streamline' => __('Coffee streamline','conico') ),
			array( 'aisconverse_icon computer_imac' => __('Computer imac','conico') ),
			array( 'aisconverse_icon computer_imac_2' => __('Computer imac 2','conico') ),
			array( 'aisconverse_icon computer_macintosh_vintage' => __('Computer macintosh vintage','conico') ),
			array( 'aisconverse_icon design_graphic_tablet_streamline_tablet' => __('Design graphic tablet streamline tablet','conico') ),
			array( 'aisconverse_icon design_pencil_rule_streamline' => __('Design pencil rule streamline','conico') ),
			array( 'aisconverse_icon diving_leisure_sea_sport_streamline' => __('Diving leisure sea sport streamline','conico') ),
			array( 'aisconverse_icon drug_medecine_streamline_syringue' => __('Drug medecine streamline syringue','conico') ),
			array( 'aisconverse_icon food_ice_cream_streamline' => __('Food ice cream streamline','conico') ),
			array( 'aisconverse_icon frame_picture_streamline' => __('Frame picture streamline','conico') ),
			array( 'aisconverse_icon grid_lines_streamline' => __('Grid lines streamline','conico') ),
			array( 'aisconverse_icon handle_streamline_vector' => __('Handle streamline vector','conico') ),
			array( 'aisconverse_icon ipod_streamline' => __('Ipod streamline','conico') ),
			array( 'aisconverse_icon japan_streamline_tea' => __('Japan streamline tea','conico') ),
			array( 'aisconverse_icon laptop_macbook_streamline' => __('Laptop macbook streamline','conico') ),
			array( 'aisconverse_icon like_love_streamline' => __('Like love streamline','conico') ),
			array( 'aisconverse_icon micro_record_streamline' => __('Micro record streamline','conico') ),
			array( 'aisconverse_icon monocle_mustache_streamline' => __('Monocle mustache streamline','conico') ),
			array( 'aisconverse_icon music_note_streamline' => __('Music note streamline','conico') ),
			array( 'aisconverse_icon music_speaker_streamline' => __('Music speaker streamline','conico') ),
			array( 'aisconverse_icon picture_streamline' => __('Picture streamline','conico') ),
			array( 'aisconverse_icon picture_streamline_1' => __('Picture streamline 1','conico') ),
			array( 'aisconverse_icon receipt_shopping_streamline' => __('Receipt shopping streamline','conico') ),
			array( 'aisconverse_icon remote_control_streamline' => __('Remote control streamlin','conico') )
		);

		return $icons;
	}

	add_filter( 'vc_iconpicker-type-aisconverse_font', 'conico_vc_iconpicker_type_aisconverse_font', 999 );
}


if ( ! function_exists( 'conico_vc_iconpicker_type_theme_font' ) ) {
	/**
	 * Theme font for VC icon_picker
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_iconpicker_type_theme_font( $icons ) {

		$icons = array(
			array( 'icond-thin-0310-support-help-talk-call' => __('Support','conico') ),
			array( 'icond-thin-0322-mail-post-box' => __('Mail','conico') ),
			array( 'icond-thin-0852-tea-coffee-hot' => __('Tea','conico') ),
			array( 'icond-thin-0593-video-play-youtube' => __('Video','conico') ),
			array( 'icond-thin-0629-photo-camera-tripod-stand' => __('Photo','conico') ),
			array( 'icond-thin-0672-crop-image' => __('Crop','conico') ),
			array( 'icond-thin-0213-hand-touch-click-press-one-finger' => __('Hand','conico') ),
			array( 'icond-thin-0691-wall-paint-color' => __('Wall','conico') ),
			array( 'icond-thin-0413-money-coins-jettons-chips' => __('Money','conico') ),
			array( 'icond-thin-0442-shopping-cart-basket-store' => __('Shopping','conico') ),
			array( 'icond-thin-0586-movie-video-camera-recording' => __('Movie','conico') ),
			array( 'icond-thin-0618-album-picture-image-photo' => __('Album','conico') ),
			array( 'icond-thin-0464-shipping-box-delivery' => __('Shipping','conico') ),
			array( 'icond-thin-0465-shopping-cart-basket-store' => __('Shopping','conico') ),
			array( 'icond-thin-0993-dress' => __('Dress','conico') ),
			array( 'icond-thin-0998-women-high-heels-shoe' => __('Women','conico') ),
			array( 'icond-thin-0986-t-shirt' => __('T-shirt','conico') ),
			array( 'icond-thin-0072-document-file-paper-text' => __('Document','conico') ),
			array( 'icond-thin-0111-folder-files-documents-1' => __('Folder','conico') ),
			array( 'icond-thin-0289-mobile-phone-call-ringing-nfc-1' => __('Mobile Phone','conico') ),
			array( 'icond-thin-0595-music-note-playing-sound-song-1' => __('Music','conico') ),
			array( 'icond-thin-0622-wall-picture-image-photo-1' => __('Picture','conico') ),
			array( 'icond-thin-0994-underwear-1' => __('Underwear','conico') ),
			array( 'icond-thin-0996-baseball-cap-1' => __('Baseball','conico') ),
			array( 'icond-thin-0999-sneakers-freetime-shoe-1' => __('Sneakers','conico') ),
			array( 'icond-thin-0704-users-profile-group-couple-man-woman' => __('Users','conico') ),
			array( 'icond-thin-0105-download-clipboard-box' => __('Download','conico') ),
			array( 'icond-thin-0329-computer-laptop-user-login' => __('Computer','conico') ),
			array( 'icond-thin-0408-wallet-money-payment' => __('Wallet','conico') ),
			array( 'icond-thin-1041-mathematics-curve-coordinate-system' => __('Curve','conico') )
		);

		return $icons;
	}

	add_filter( 'vc_iconpicker-type-theme_font', 'conico_vc_iconpicker_type_theme_font', 999 );
}


if ( ! function_exists( 'conico_vc_iconpicker_type_openiconic' ) ) {
	/**
	 * Theme font for VC icon_picker
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_iconpicker_type_openiconic( $icons ) {

		$icons = array(
			array('oi oi-account-login' => __('Account login','conico')),
			array('oi oi-account-logout' => __('Account logout','conico')),
			array('oi oi-action-redo' => __('Action redo','conico')),
			array('oi oi-action-undo' => __('Action undo','conico')),
			array('oi oi-align-center' => __('Align center','conico')),
			array('oi oi-align-left' => __('Align left','conico')),
			array('oi oi-align-right' => __('Align right','conico')),
			array('oi oi-aperture' => __('Aperture','conico')),
			array('oi oi-arrow-bottom' => __('Arrow bottom','conico')),
			array('oi oi-arrow-circle-bottom' => __('Arrow circle-bottom','conico')),
			array('oi oi-arrow-circle-left' => __('Arrow circle-left','conico')),
			array('oi oi-arrow-circle-right' => __('Arrow circle-right','conico')),
			array('oi oi-arrow-circle-top' => __('Arrow circle-top','conico')),
			array('oi oi-arrow-left' => __('Arrow left','conico')),
			array('oi oi-arrow-right' => __('Arrow right','conico')),
			array('oi oi-arrow-thick-bottom' => __('Arrow thick-bottom','conico')),
			array('oi oi-arrow-thick-left' => __('Arrow thick-left','conico')),
			array('oi oi-arrow-thick-right' => __('Arrow thick-right','conico')),
			array('oi oi-arrow-thick-top' => __('Arrow thick-top','conico')),
			array('oi oi-arrow-top' => __('Arrow top','conico')),
			array('oi oi-audio-spectrum' => __('Audio spectrum','conico')),
			array('oi oi-audio' => __('Audio','conico')),
			array('oi oi-badge' => __('Badge','conico')),
			array('oi oi-ban' => __('Ban','conico')),
			array('oi oi-bar-chart' => __('Bar chart','conico')),
			array('oi oi-basket' => __('Basket','conico')),
			array('oi oi-battery-empty' => __('Battery empty','conico')),
			array('oi oi-battery-full' => __('Battery full','conico')),
			array('oi oi-beaker' => __('Beaker','conico')),
			array('oi oi-bell' => __('Bell','conico')),
			array('oi oi-bluetooth' => __('Bluetooth','conico')),
			array('oi oi-bold' => __('Bold','conico')),
			array('oi oi-bolt' => __('Bolt','conico')),
			array('oi oi-book' => __('Book','conico')),
			array('oi oi-bookmark' => __('Bookmark','conico')),
			array('oi oi-box' => __('Box','conico')),
			array('oi oi-briefcase' => __('Briefcase','conico')),
			array('oi oi-british-pound' => __('British pound','conico')),
			array('oi oi-browser' => __('Browser','conico')),
			array('oi oi-brush' => __('Brush','conico')),
			array('oi oi-bug' => __('Bug','conico')),
			array('oi oi-bullhorn' => __('Bullhorn','conico')),
			array('oi oi-calculator' => __('Calculator','conico')),
			array('oi oi-calendar' => __('Calendar','conico')),
			array('oi oi-camera-slr' => __('Camera slr','conico')),
			array('oi oi-caret-bottom' => __('Caret bottom','conico')),
			array('oi oi-caret-left' => __('Caret left','conico')),
			array('oi oi-caret-right' => __('Caret right','conico')),
			array('oi oi-caret-top' => __('Caret top','conico')),
			array('oi oi-cart' => __('Cart','conico')),
			array('oi oi-chat' => __('Chat','conico')),
			array('oi oi-check' => __('Check','conico')),
			array('oi oi-chevron-bottom' => __('Chevron bottom','conico')),
			array('oi oi-chevron-left' => __('Chevron left','conico')),
			array('oi oi-chevron-right' => __('Chevron right','conico')),
			array('oi oi-chevron-top' => __('Chevron top','conico')),
			array('oi oi-circle-check' => __('Circle check','conico')),
			array('oi oi-circle-x' => __('Circle x','conico')),
			array('oi oi-clipboard' => __('Clipboard','conico')),
			array('oi oi-clock' => __('Clock','conico')),
			array('oi oi-cloud-download' => __('Cloud download','conico')),
			array('oi oi-cloud-upload' => __('Cloud upload','conico')),
			array('oi oi-cloud' => __('Cloud','conico')),
			array('oi oi-cloudy' => __('Cloudy','conico')),
			array('oi oi-code' => __('Code','conico')),
			array('oi oi-cog' => __('Cog','conico')),
			array('oi oi-collapse-down' => __('Collapse down','conico')),
			array('oi oi-collapse-left' => __('Collapse left','conico')),
			array('oi oi-collapse-right' => __('Collapse right','conico')),
			array('oi oi-collapse-up' => __('Collapse up','conico')),
			array('oi oi-command' => __('Command','conico')),
			array('oi oi-comment-square' => __('Comment square','conico')),
			array('oi oi-compass' => __('Compass','conico')),
			array('oi oi-contrast' => __('Contrast','conico')),
			array('oi oi-copywriting' => __('Copywriting','conico')),
			array('oi oi-credit-card' => __('Credit card','conico')),
			array('oi oi-crop' => __('Crop','conico')),
			array('oi oi-dashboard' => __('Dashboard','conico')),
			array('oi oi-data-transfer-download' => __('Data transfer-download','conico')),
			array('oi oi-data-transfer-upload' => __('Data transfer-upload','conico')),
			array('oi oi-delete' => __('Delete','conico')),
			array('oi oi-dial' => __('Dial','conico')),
			array('oi oi-document' => __('Document','conico')),
			array('oi oi-dollar' => __('Dollar','conico')),
			array('oi oi-double-quote-sans-left' => __('Double quote-sans-left','conico')),
			array('oi oi-double-quote-sans-right' => __('Double quote-sans-right','conico')),
			array('oi oi-double-quote-serif-left' => __('Double quote-serif-left','conico')),
			array('oi oi-double-quote-serif-right' => __('Double quote-serif-right','conico')),
			array('oi oi-droplet' => __('Droplet','conico')),
			array('oi oi-eject' => __('Eject','conico')),
			array('oi oi-elevator' => __('Elevator','conico')),
			array('oi oi-ellipses' => __('Ellipses','conico')),
			array('oi oi-envelope-closed' => __('Envelope closed','conico')),
			array('oi oi-envelope-open' => __('Envelope open','conico')),
			array('oi oi-euro' => __('Euro','conico')),
			array('oi oi-excerpt' => __('Excerpt','conico')),
			array('oi oi-expand-down' => __('Expand down','conico')),
			array('oi oi-expand-left' => __('Expand left','conico')),
			array('oi oi-expand-right' => __('Expand right','conico')),
			array('oi oi-expand-up' => __('Expand up','conico')),
			array('oi oi-external-link' => __('External link','conico')),
			array('oi oi-eye' => __('Eye','conico')),
			array('oi oi-eyedropper' => __('Eyedropper','conico')),
			array('oi oi-file' => __('File','conico')),
			array('oi oi-fire' => __('Fire','conico')),
			array('oi oi-flag' => __('Flag','conico')),
			array('oi oi-flash' => __('Flash','conico')),
			array('oi oi-folder' => __('Folder','conico')),
			array('oi oi-fork' => __('Fork','conico')),
			array('oi oi-fullscreen-enter' => __('Fullscreen enter','conico')),
			array('oi oi-fullscreen-exit' => __('Fullscreen exit','conico')),
			array('oi oi-globe' => __('Globe','conico')),
			array('oi oi-graph' => __('Graph','conico')),
			array('oi oi-grid-four-up' => __('Grid four-up','conico')),
			array('oi oi-grid-three-up' => __('Grid three-up','conico')),
			array('oi oi-grid-two-up' => __('Grid two-up','conico')),
			array('oi oi-hard-drive' => __('Hard drive','conico')),
			array('oi oi-header' => __('Header','conico')),
			array('oi oi-headphones' => __('Headphones','conico')),
			array('oi oi-heart' => __('Heart','conico')),
			array('oi oi-home' => __('Home','conico')),
			array('oi oi-image' => __('Image','conico')),
			array('oi oi-inbox' => __('Inbox','conico')),
			array('oi oi-infinity' => __('Infinity','conico')),
			array('oi oi-info' => __('Info','conico')),
			array('oi oi-italic' => __('Italic','conico')),
			array('oi oi-justify-center' => __('Justify center','conico')),
			array('oi oi-justify-left' => __('Justify left','conico')),
			array('oi oi-justify-right' => __('Justify right','conico')),
			array('oi oi-key' => __('Key','conico')),
			array('oi oi-laptop' => __('Laptop','conico')),
			array('oi oi-layers' => __('Layers','conico')),
			array('oi oi-lightbulb' => __('Lightbulb','conico')),
			array('oi oi-link-broken' => __('Link broken','conico')),
			array('oi oi-link-intact' => __('Link intact','conico')),
			array('oi oi-list-rich' => __('List rich','conico')),
			array('oi oi-list' => __('List','conico')),
			array('oi oi-location' => __('Location','conico')),
			array('oi oi-lock-locked' => __('Lock locked','conico')),
			array('oi oi-lock-unlocked' => __('Lock unlocked','conico')),
			array('oi oi-loop-circular' => __('Loop circular','conico')),
			array('oi oi-loop-square' => __('Loop square','conico')),
			array('oi oi-loop' => __('Loop','conico')),
			array('oi oi-magnifying-glass' => __('Magnifying glass','conico')),
			array('oi oi-map-marker' => __('Map marker','conico')),
			array('oi oi-map' => __('Map','conico')),
			array('oi oi-media-pause' => __('Media pause','conico')),
			array('oi oi-media-play' => __('Media play','conico')),
			array('oi oi-media-record' => __('Media record','conico')),
			array('oi oi-media-skip-backward' => __('Media skip-backward','conico')),
			array('oi oi-media-skip-forward' => __('Media skip-forward','conico')),
			array('oi oi-media-step-backward' => __('Media step-backward','conico')),
			array('oi oi-media-step-forward' => __('Media step-forward','conico')),
			array('oi oi-media-stop' => __('Media stop','conico')),
			array('oi oi-medical-cross' => __('Medical cross','conico')),
			array('oi oi-menu' => __('Menu','conico')),
			array('oi oi-microphone' => __('Microphone','conico')),
			array('oi oi-minus' => __('Minus','conico')),
			array('oi oi-monitor' => __('Monitor','conico')),
			array('oi oi-moon' => __('Moon','conico')),
			array('oi oi-move' => __('Move','conico')),
			array('oi oi-musical-note' => __('Musical note','conico')),
			array('oi oi-paperclip' => __('Paperclip','conico')),
			array('oi oi-pencil' => __('Pencil','conico')),
			array('oi oi-people' => __('People','conico')),
			array('oi oi-person' => __('Person','conico')),
			array('oi oi-phone' => __('Phone','conico')),
			array('oi oi-pie-chart' => __('Pie chart','conico')),
			array('oi oi-pin' => __('Pin','conico')),
			array('oi oi-play-circle' => __('Play circle','conico')),
			array('oi oi-plus' => __('Plus','conico')),
			array('oi oi-power-standby' => __('Power standby','conico')),
			array('oi oi-print' => __('Print','conico')),
			array('oi oi-project' => __('Project','conico')),
			array('oi oi-pulse' => __('Pulse','conico')),
			array('oi oi-puzzle-piece' => __('Puzzle piece','conico')),
			array('oi oi-question-mark' => __('Question mark','conico')),
			array('oi oi-rain' => __('Rain','conico')),
			array('oi oi-random' => __('Random','conico')),
			array('oi oi-reload' => __('Reload','conico')),
			array('oi oi-resize-both' => __('Resize both','conico')),
			array('oi oi-resize-height' => __('Resize height','conico')),
			array('oi oi-resize-width' => __('Resize width','conico')),
			array('oi oi-rss-alt' => __('Rss alt','conico')),
			array('oi oi-rss' => __('Rss','conico')),
			array('oi oi-script' => __('Script','conico')),
			array('oi oi-share-boxed' => __('Share boxed','conico')),
			array('oi oi-share' => __('Share','conico')),
			array('oi oi-shield' => __('Shield','conico')),
			array('oi oi-signal' => __('Signal','conico')),
			array('oi oi-signpost' => __('Signpost','conico')),
			array('oi oi-sort-ascending' => __('Sort ascending','conico')),
			array('oi oi-sort-descending' => __('Sort descending','conico')),
			array('oi oi-spreadsheet' => __('Spreadsheet','conico')),
			array('oi oi-star' => __('Star','conico')),
			array('oi oi-sun' => __('Sun','conico')),
			array('oi oi-tablet' => __('Tablet','conico')),
			array('oi oi-tag' => __('Tag','conico')),
			array('oi oi-tags' => __('Tags','conico')),
			array('oi oi-target' => __('Target','conico')),
			array('oi oi-task' => __('Task','conico')),
			array('oi oi-terminal' => __('Terminal','conico')),
			array('oi oi-text' => __('Text','conico')),
			array('oi oi-thumb-down' => __('Thumb down','conico')),
			array('oi oi-thumb-up' => __('Thumb up','conico')),
			array('oi oi-timer' => __('Timer','conico')),
			array('oi oi-transfer' => __('Transfer','conico')),
			array('oi oi-trash' => __('Trash','conico')),
			array('oi oi-underline' => __('Underline','conico')),
			array('oi oi-vertical-align-bottom' => __('Vertical align-bottom','conico')),
			array('oi oi-vertical-align-center' => __('Vertical align-center','conico')),
			array('oi oi-vertical-align-top' => __('Vertical align-top','conico')),
			array('oi oi-video' => __('Video','conico')),
			array('oi oi-volume-high' => __('Volume high','conico')),
			array('oi oi-volume-low' => __('Volume low','conico')),
			array('oi oi-volume-off' => __('Volume off','conico')),
			array('oi oi-warning' => __('Warning','conico')),
			array('oi oi-wifi' => __('Wifi','conico')),
			array('oi oi-wrench' => __('Wrench','conico')),
			array('oi oi-x' => __('X','conico')),
			array('oi oi-yen' => __('Yen','conico')),
			array('oi oi-zoom-in' => __('Zoom in','conico')),
			array('oi oi-zoom-out' => __('Zoom out','conico'))

		);

		return $icons;
	}

	add_filter( 'vc_iconpicker-type-openiconic', 'conico_vc_iconpicker_type_openiconic', 999 );
}



if ( ! function_exists( 'conico_vc_iconpicker_base_register_css' ) ) {
	/**
	 * Registration backend VC fonts
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_iconpicker_base_register_css() {
		wp_register_style( 'feather', CONICO_CSS_PATH . 'feather.min.css' );
		wp_register_style( 'bootstrap_font', CONICO_CSS_PATH . 'bootstrap-fonts.min.css' );
		wp_register_style( 'aisconverse_font', CONICO_CSS_PATH . 'aisconverse.min.css' );
		wp_register_style( 'theme_font', CONICO_CSS_PATH . 'theme.min.css' );
		wp_deregister_style('vc_openiconic');
		wp_register_style( 'vc_openiconic', CONICO_CSS_PATH . 'open-iconic-bootstrap.min.css' );
	}

	add_action( 'vc_base_register_front_css', 'conico_vc_iconpicker_base_register_css' );
	add_action( 'vc_base_register_admin_css', 'conico_vc_iconpicker_base_register_css' );
}


if ( ! function_exists( 'conico_vc_iconpicker_editor_jscss' ) ) {
	/**
	 * Enqueue backend VC fonts
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_iconpicker_editor_jscss() {
		wp_enqueue_style( 'feather' );
		wp_enqueue_style( 'bootstrap_font' );
		wp_enqueue_style( 'aisconverse_font' );
		wp_enqueue_style( 'theme_font' );
	}

	add_action( 'vc_backend_editor_enqueue_js_css', 'conico_vc_iconpicker_editor_jscss' );
	add_action( 'vc_frontend_editor_enqueue_js_css', 'conico_vc_iconpicker_editor_jscss' );
}



if ( ! function_exists( 'conico_vc_before_init_actions' ) ) {
	/**
	 * New VC elements/shortcodes
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_before_init_actions() {

		if ( function_exists( 'vc_remove_element' ) ) {
			vc_remove_element( 'vc_wp_search' );
			vc_remove_element( 'vc_wp_meta' );
			vc_remove_element( 'vc_wp_recentcomments' );
			vc_remove_element( 'vc_wp_custommenu' );
			vc_remove_element( 'vc_wp_text' );
			vc_remove_element( 'vc_wp_posts' );
			vc_remove_element( 'vc_wp_pages' );
			vc_remove_element( 'vc_wp_categories' );
			vc_remove_element( 'vc_wp_archives' );
			vc_remove_element( 'vc_widget_sidebar' );
			vc_remove_element( 'vc_wp_calendar' );
			vc_remove_element( 'vc_wp_rss' );
			vc_remove_element( 'vc_wp_tagcloud' );
			vc_remove_element('vc_cta');
			vc_remove_element('vc_flickr');
			vc_remove_element('vc_round_chart');
			vc_remove_element('vc_line_chart');
			vc_remove_element('vc_basic_grid');
			vc_remove_element('vc_media_grid');
			vc_remove_element('vc_masonry_grid');
			vc_remove_element('vc_masonry_media_grid');
			vc_remove_element('product_categories');
			vc_remove_element('product_attribute');
			vc_remove_element('vc_gallery');
			vc_remove_element('vc_images_carousel');
			vc_remove_element('vc_posts_slider');
			vc_remove_element('vc_tta_pageable');
			vc_remove_element('vc_pie');

			vc_remove_element('vc_zigzag');
			vc_remove_element('vc_hoverbox');
		}
	}

	add_action( 'vc_before_init', 'conico_vc_before_init_actions' );
}


if ( ! function_exists( 'conico_vc_tta' ) ) {
	/**
	 * Detect tour/tabs
	 *
	 * @since Conico 1.0
	 */
	function conico_vc_tta( $html, $atts, $content, $tta ) {


		$class = ! empty( $tta ) ? get_class( $tta ) : '';

		if ( ! empty( $class ) ) {
			switch ( $class ) {
				case 'WPBakeryShortCode_VC_Tta_Tour' :
					$class = 'vc_tta_type_tour';
					break;
				case 'WPBakeryShortCode_VC_Tta_Tabs' :
					$class = 'vc_tta_type_tabs';
					break;
			}
			$html = str_replace( '<div class="vc_tta-tabs-container">', "<div class=\"vc_tta-tabs-container {$class}\">", $html );
		}

		return $html;
	}

	add_filter( 'vc-tta-get-params-tabs-list', 'conico_vc_tta', 10, 4 );
}