<?php
defined( 'ABSPATH' ) or die();


//Cherry plugin constant variables
define( 'WIDGET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WIDGET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CURRENT_THEME_DIR', get_stylesheet_directory() );


// Exclude widget manager for customizer
global $wp_customize;

if ( ! isset( $wp_customize ) ) {
	include_once( 'widgets-manager.php' );
}

class Basement_Widgets {

	private static $instance = null;


	public function __construct() {
		global $wp_customize;

		if ( ! isset( $wp_customize ) ) {
			add_filter( 'in_widget_form', array( &$this, 'widget_form' ), 10, 3 );
			add_filter( 'widget_update_callback', array( &$this, 'widget_update_form' ), 10, 4 );
			add_filter( 'dynamic_sidebar_params', array( &$this, 'widget_display' ) );
			add_filter( 'admin_head', array( &$this, 'clone_script'  )  );
		}
		add_action( 'widgets_init', array( &$this, 'load_widgets' ) );

		#add_action('admin_init',array(&$this, 'widget_settings_form'));
		
		if( defined('DOING_AJAX') && DOING_AJAX ) {
			add_action('wp_ajax_ajax-generate-widget-settings', array( &$this, 'widget_settings_form') );
		}
	}

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Widgets();
		}

		return self::$instance;
	}

	public function clone_script() {
		global $pagenow;

		if( $pagenow != 'widgets.php' )
			return;
		?>
		<script>
            (function($) {
                if(!window.Nine3) window.Nine3 = {};

                Nine3.CloneWidgets = {
                    init: function() {
                        $('body').on('click', '.widget-control-actions .clone-me', Nine3.CloneWidgets.Clone);
                        Nine3.CloneWidgets.Bind();
                    },

                    Bind: function() {
                        $('#widgets-right').off('DOMSubtreeModified', Nine3.CloneWidgets.Bind);
                        $('.widget-control-actions:not(.nine3-cloneable)').each(function() {
                            var $widget = $(this);

                            var $clone = $( '<a>' );
                            var clone = $clone.get()[0];
                            $clone.addClass( 'clone-me nine3-clone-action' )
                                .attr( 'title', 'Clone this Widget' )
                                .attr( 'href', '#' )
                                .html( 'Clone' );


                            $widget.addClass('nine3-cloneable');
                            $clone.insertAfter( $widget.find( '.alignleft .widget-control-remove') );

                            //Separator |
                            clone.insertAdjacentHTML( 'beforebegin', ' | ' );
                        });

                        $('#widgets-right').on('DOMSubtreeModified', Nine3.CloneWidgets.Bind);
                    },

                    Clone: function(ev) {
                        var $original = $(this).parents('.widget');
                        var $widget = $original.clone();

                        // Find this widget's ID base. Find its number, duplicate.
                        var idbase = $widget.find('input[name="id_base"]').val();
                        var number = $widget.find('input[name="widget_number"]').val();
                        var mnumber = $widget.find('input[name="multi_number"]').val();
                        var highest = 0;

                        $('input.widget-id[value|="' + idbase + '"]').each(function() {
                            var match = this.value.match(/-(\d+)$/);
                            if(match && parseInt(match[1]) > highest)
                                highest = parseInt(match[1]);
                        });

                        var newnum = highest + 1;

                        $widget.find('.widget-content').find('input,select,textarea').each(function() {
                            if($(this).attr('name'))
                                $(this).attr('name', $(this).attr('name').replace(number, newnum));
                        });

                        // assign a unique id to this widget:
                        var highest = 0;
                        $('.widget').each(function() {
                            var match = this.id.match(/^widget-(\d+)/);

                            if(match && parseInt(match[1]) > highest)
                                highest = parseInt(match[1]);
                        });
                        var newid = highest + 1;

                        // Figure out the value of add_new from the source widget:
                        var add = $('#widget-list .id_base[value="' + idbase + '"]').siblings('.add_new').val();
                        $widget[0].id = 'widget-' + newid + '_' + idbase + '-' + newnum;
                        $widget.find('input.widget-id').val(idbase+'-'+newnum);
                        $widget.find('input.widget_number').val(newnum);
                        $widget.hide();
                        $original.after($widget);
                        $widget.fadeIn();

                        // Not exactly sure what multi_number is used for.
                        $widget.find('.multi_number').val(newnum);

                        wpWidgets.save($widget, 0, 0, 1);

                        ev.stopPropagation();
                        ev.preventDefault();
                    }
                }

                $(Nine3.CloneWidgets.init);
            })(jQuery);

		</script>
		<?php
	}


	/**
	 * Get form and clear for AJAX
	 */
	public function widget_settings_form() {

		$params = isset($_POST["param"]) ? $_POST["param"] : array();

		$str = $this->get_widget_settings_form($params['id']);
		$str = trim(preg_replace('/\s+/', ' ', $str));

		$regex_grids = '#<\s*?div id="grids-widget-\b[^>]*>(.*?)</div\b[^>]*>#s';
		$regex_borders = '#<\s*?div id="borders-widget-\b[^>]*>(.*?)</div\b[^>]*>#s';

		$regex_grids_mach = array();
		preg_match_all($regex_grids, $str, $regex_grids_mach);


		$regex_borders_mach = array();
		preg_match_all($regex_borders, $str, $regex_borders_mach);

		echo json_encode(array(
			'grids' => $regex_grids_mach[1],
			'borders' => $regex_borders_mach[1]
		));

		die();
	}


	/**
	 * Return HTML widget form for AJAX
	 *
	 * @param $widget_id
	 *
	 * @return string
	 */
	public function get_widget_settings_form($widget_id) {
		global $wp_registered_widgets, $wp_registered_widget_controls, $sidebars_widgets;

		$control = isset($wp_registered_widget_controls[$widget_id]) ? $wp_registered_widget_controls[$widget_id] : array();

		if ( isset( $control['callback'] ) ) {
			ob_start();

			call_user_func_array( $control['callback'], $control['params'] );

			return ob_get_clean();
		}
	}



	/**
	 * Loads up all the widgets defined by this theme. Note that this function will not work for versions of WordPress 2.7 or lower
	 */
	function load_widgets() {

		$widget_files = array(
			'Basement_Image_Widget'            => 'class-basement-widget-image.php',
			'Basement_Horizontal_Links_Widget' => 'class-basement-widget-horizontal-links.php',
			'Basement_Horizontal_Icons_Widget' => 'class-basement-widget-horizontal-icons.php',
			'Basement_Sharing_Widget'          => 'class-basement-widget-sharing.php',
			'Basement_Twitter_Embed_Widget'    => 'class-basement-widget-twitter.php',
			'Basement_Instagram_Widget'        => 'class-basement-widget-instagram.php',
			'Basement_Horizontal_List_Widget'  => 'class-basement-widget-horizontal-list.php',
			'Basement_Flickr_Widget'           => 'class-basement-widget-flickr.php',
			'Basement_Form_Widget'             => 'class-basement-widget-cf.php',
			'Basement_Hr_Widget'               => 'class-basement-widget-hr.php'
		);
		foreach ( $widget_files as $class_name => $file_name ) {
			if ( $class_name === 'Basement_Form_Widget' && ! in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				continue;
			}

			$widget_dir = file_exists( CURRENT_THEME_DIR . '/inc/widgets/' . $file_name ) ? CURRENT_THEME_DIR . '/inc/widgets/' . $file_name : WIDGET_PLUGIN_DIR . 'widgets/' . $file_name;
			include_once( $widget_dir );

			if ( class_exists( $class_name ) ) {
				register_widget( $class_name );
			}
		}


		#unregister_widget( 'WP_Widget_Recent_Comments' ); // Wp Comments
		unregister_widget( 'WC_Widget_Recent_Reviews' ); // Wp Woo Reviews


		$theme_widgets = array(
			#'Basement_Widget_Recent_Comments' => 'class-basement-widget-recent-comments.php'
		);

		if(Basement_Ecommerce_Woocommerce::enabled()) {
			$theme_widgets['Basement_Widget_Recent_Reviews'] = 'class-basement-widget-recent-reviews.php';
		}

		if(!empty($theme_widgets)) {
			foreach ( $theme_widgets as $theme_class_name => $theme_file_name ) {
				$theme_widget_dir = file_exists( CURRENT_THEME_DIR . '/inc/widgets/' . $theme_file_name ) ? CURRENT_THEME_DIR . '/inc/widgets/' . $theme_file_name : WIDGET_PLUGIN_DIR . 'other-widgets/' . $theme_file_name;

				include_once( $theme_widget_dir );

				if ( class_exists( $theme_class_name ) ) {
					register_widget( $theme_class_name );
				}
			}
		}


	}


	public function dom_widget_form() {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$dom->appendChild( $dom->createElement( 'p' ) );

		echo $dom->saveHTML();
	}


	/**
	 * Custom params for widgets
	 *
	 * @param $widget
	 * @param $return
	 * @param $instance
	 *
	 * @return array
	 */
	public function widget_form( $widget, $return, $instance ) {
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			array(
				'key' => 'hide_title',
				'label' => __( 'Hide the title?', BASEMENT_TEXTDOMAIN ),
				'type' => 'checkbox',
				'wrap' => false
			),
			array(
				'key' => 'bottom_line',
				'label' => __( 'Add line below widget?', BASEMENT_TEXTDOMAIN ),
				'type' => 'checkbox',
				'wrap' => array(
					'legend' => __('Aside',BASEMENT_TEXTDOMAIN)
				)
			)
		);

		if($widget->id_base !== 'basement_hr_widget') {

			foreach ($params as $param) {

				$key = !empty($param['key']) ? $param['key'] : false;
				$label = !empty($param['label']) ? $param['label'] : __('Widget Field', BASEMENT_TEXTDOMAIN);
				$type = !empty($param['type']) ? $param['type'] : false;
				$wrap = !empty($param['wrap']) ? $param['wrap'] : false;

				if ( $key ) {

					$field_id = $widget->get_field_id( $key );
					$field_name = $widget->get_field_name( $key );
					$field_value = isset( $instance[$key] ) ? $instance[$key] : 0;
					#$field_uid = substr(uniqid('widget-'), 0, 12);

					if($wrap) {
						$fieldset = $dom->appendChild( $dom->createElement( 'fieldset' ) );
						$fieldset->setAttribute('class','widget-fieldset');
						$fieldset->appendChild( $dom->createElement( 'legend',$wrap['legend'] ) );
						$p = $fieldset->appendChild( $dom->createElement( 'p' ) );
					} else {
						$p = $dom->appendChild( $dom->createElement( 'p' ) );
					}

					if($type) {
						$input = $p->appendChild( $dom->createElement( 'input' ) );
						$input->setAttribute( 'type',$type);
						$input->setAttribute( 'id',$field_id);
						$input->setAttribute( 'name', $field_name);
						if(checked( $field_value, true, false )) {
							$input->setAttribute( 'checked', 'checked');
						}
					}

					$label = $p->appendChild( $dom->createElement( 'label', $label ) );
					$label->setAttribute('for',$field_id);

				}
			}

			$fieldset = $dom->appendChild( $dom->createElement( 'fieldset' ) );
			$fieldset->setAttribute('class','widget-fieldset');
			$fieldset->appendChild( $dom->createElement( 'legend', __('Footer',BASEMENT_TEXTDOMAIN) ) );


			$footer_params = array(
				array(
					'key' => 'grids',
					'label' => __('Appearance Settings',BASEMENT_TEXTDOMAIN)
				),
				array(
					'key' => 'borders',
					'label' =>  __('Border Settings',BASEMENT_TEXTDOMAIN)
				)
			);


			foreach ($footer_params as $footer_param) {
				$label  = !empty($footer_param['label']) ? $footer_param['label'] : __('Widget Label',BASEMENT_TEXTDOMAIN);
				$key = !empty($footer_param['key']) ? $footer_param['key'] : false;

				$toggle_classes = array('wtt');
				$toggle = $fieldset->appendChild( $dom->createElement( 'a', $label ) );
				if(isset( $instance[$key] ) && !empty($instance[$key])) {
					$toggle_classes[] = 'active';
				}
				$toggle->setAttribute('class',implode(' ', $toggle_classes));
				//$toggle->setAttribute('data-widget-toggle', '#' . $id);

				$toggle_checkbox = $fieldset->appendChild( $dom->createElement( 'input' ) );
				//$toggle_checkbox->setAttribute('class','widget-checked-button');
				$toggle_checkbox->setAttribute('id',$widget->get_field_id( $key ));
				$toggle_checkbox->setAttribute('name',$widget->get_field_name( $key ));
				$toggle_checkbox->setAttribute('type','checkbox');
				if(checked( isset( $instance[$key] ) ? $instance[$key] : 0 , true, false )) {
					$toggle_checkbox->setAttribute( 'checked', 'checked');
				}

				$panel_classes = array('wtp', $key .'-block');
				$panel = $fieldset->appendChild( $dom->createElement( 'div' ) );
				//$panel->setAttribute('id',$id);
				if(isset( $instance[$key] ) && !empty($instance[$key])) {
					$panel_classes[] = 'active';
				}
				$panel->setAttribute('class',implode(' ', $panel_classes));

				if($key == 'grids') {
					$table = $panel->appendChild( $dom->createElement( 'table' ) );

					$all_params = array(
						array(
							array(
								'label' => __( 'Large devices', BASEMENT_TEXTDOMAIN ),
								'params'    => array(
									array(
										'name'    => 'widget_lg_width',
										'type'    => 'width',
										'size'    => 'lg',
										'current' => ''
									),
									array(
										'name'    => 'widget_lg_offset',
										'type'    => 'offset',
										'size'    => 'lg',
										'current' => ''
									),
									array(
										'name'    => 'widget_lg_push',
										'type'    => 'push',
										'size'    => 'lg',
										'current' => ''
									),
									array(
										'name'    => 'widget_lg_pull',
										'type'    => 'pull',
										'size'    => 'lg',
										'current' => ''
									)
								)
							),
							array(
								'label' => __( 'Medium devices', BASEMENT_TEXTDOMAIN ),
								'params'    => array(
									array(
										'name'    => 'widget_md_width',
										'type'    => 'width',
										'size'    => 'md',
										'current' => ''
									),
									array(
										'name'    => 'widget_md_offset',
										'type'    => 'offset',
										'size'    => 'md',
										'current' => ''
									),
									array(
										'name'    => 'widget_md_push',
										'type'    => 'push',
										'size'    => 'md',
										'current' => ''
									),
									array(
										'name'    => 'widget_md_pull',
										'type'    => 'pull',
										'size'    => 'md',
										'current' => ''
									)
								)
							),
						),
						array(
							array(
								'label' => __( 'Small devices', BASEMENT_TEXTDOMAIN ),
								'params'    => array(
									array(
										'name'    => 'widget_sm_width',
										'type'    => 'width',
										'size'    => 'sm',
										'current' => ''
									),
									array(
										'name'    => 'widget_sm_offset',
										'type'    => 'offset',
										'size'    => 'sm',
										'current' => ''
									),
									array(
										'name'    => 'widget_sm_push',
										'type'    => 'push',
										'size'    => 'sm',
										'current' => ''
									),
									array(
										'name'    => 'widget_sm_pull',
										'type'    => 'pull',
										'size'    => 'sm',
										'current' => ''
									)
								)
							),
							array(
								'label' => __( 'Extra small devices', BASEMENT_TEXTDOMAIN ),
								'params'    => array(
									array(
										'name'    => 'widget_xs_width',
										'type'    => 'width',
										'size'    => 'xs',
										'current' => ''
									),
									array(
										'name'    => 'widget_xs_offset',
										'type'    => 'offset',
										'size'    => 'xs',
										'current' => ''
									),
									array(
										'name'    => 'widget_xs_push',
										'type'    => 'push',
										'size'    => 'xs',
										'current' => ''
									),
									array(
										'name'    => 'widget_xs_pull',
										'type'    => 'pull',
										'size'    => 'xs',
										'current' => ''
									)
								)
							)
						)
					);

					foreach ($all_params as $sizes) {
						$tr = $table->appendChild( $dom->createElement( 'tr' ) );

						foreach ($sizes as $size) {

							$td = $tr->appendChild( $dom->createElement( 'td' ) );

							$td->appendChild( $dom->createElement( 'label', $size['label'] ) );
							foreach ( $size['params'] as $key => $value ) {

								$select = $td->appendChild( $dom->createElement( 'select' ) );
								$select->setAttribute( 'id', $widget->get_field_id( $value['name'] ) );
								$select->setAttribute( 'name', $widget->get_field_name( $value['name'] ) );
								foreach ( $this->generate_grid( $value['type'], $value['size'] ) as $inner_key => $inner_value ) {
									$option = $select->appendChild( $dom->createElement( 'option', $inner_value ) );
									$option->setAttribute( 'value', $inner_key );
									if ( selected( $inner_key, ! empty( $instance[ $value['name'] ] ) ? $instance[ $value['name'] ] : $value['current'], false ) ) {
										$option->setAttribute( 'selected', 'selected' );
									}
								}
							}
						}
					}

					$p = $panel->appendChild( $dom->createElement( 'p' ) );
					$label = $p->appendChild( $dom->createElement( 'label', __('Height (in px)',BASEMENT_TEXTDOMAIN) ) );
					$label->setAttribute('for',$widget->get_field_id( 'height' ));

					$input = $p->appendChild( $dom->createElement( 'input' ) );
					$input->setAttribute('type','number');
					$input->setAttribute('id',$widget->get_field_id( 'height' ));
					$input->setAttribute('class','widefat');
					$input->setAttribute('name',$widget->get_field_name( 'height' ));
					$input_value = (isset( $instance['height'] ) && !empty($instance['height'])) ? $instance['height'] : '';
					$input->setAttribute('value',$input_value);

					$fieldset->appendChild( $dom->createElement( 'hr' ) );
				} else {

					$border_controls = array(
						array(
							'key' => 'left_line',
							'label' => __( 'Left border of the widget?', BASEMENT_TEXTDOMAIN ),
							'params' => array(
								array(
									array(
										'key'     => 'lg',
										'title'   => __( 'Large devices', BASEMENT_TEXTDOMAIN ),
										'values'  => array(
											''                       => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
											'left-border-visible-lg' => __( 'Show', BASEMENT_TEXTDOMAIN ),
											'left-border-hidden-lg'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
										),
										'current' => ''
									),
									array(
										'key'     => 'md',
										'title'   => __( 'Medium devices', BASEMENT_TEXTDOMAIN ),
										'values'  => array(
											''                       => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
											'left-border-visible-md' => __( 'Show', BASEMENT_TEXTDOMAIN ),
											'left-border-hidden-md'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
										),
										'current' => ''
									),
								),
								array(
									array(
										'key' => 'sm',
										'title' => __( 'Small devices', BASEMENT_TEXTDOMAIN ),
										'values' => array(
											'' => __('&mdash; Select &mdash;',BASEMENT_TEXTDOMAIN),
											'left-border-visible-sm' => __('Show',BASEMENT_TEXTDOMAIN),
											'left-border-hidden-sm' => __('Hide',BASEMENT_TEXTDOMAIN)
										),
										'current' => ''
									),
									array(
										'key' => 'xs',
										'title' => __( 'Extra small devices', BASEMENT_TEXTDOMAIN ),
										'values' => array(
											'' => __('&mdash; Select &mdash;',BASEMENT_TEXTDOMAIN),
											'left-border-visible-xs' => __('Show',BASEMENT_TEXTDOMAIN),
											'left-border-hidden-xs' => __('Hide',BASEMENT_TEXTDOMAIN)
										),
										'current' => ''
									)
								)
							)
						),
						array(
							'key' => 'right_line',
							'label' => __( 'Right border of the widget?', BASEMENT_TEXTDOMAIN ),
							'params' => array(
								array(
									array(
										'key'     => 'lg',
										'title'   => __( 'Large devices', BASEMENT_TEXTDOMAIN ),
										'values'  => array(
											''                        => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
											'right-border-visible-lg' => __( 'Show', BASEMENT_TEXTDOMAIN ),
											'right-border-hidden-lg'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
										),
										'current' => ''
									),
									array(
										'key'     => 'md',
										'title'   => __( 'Medium devices', BASEMENT_TEXTDOMAIN ),
										'values'  => array(
											''                        => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
											'right-border-visible-md' => __( 'Show', BASEMENT_TEXTDOMAIN ),
											'right-border-hidden-md'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
										),
										'current' => ''
									)
								),
								array(
									array(
										'key'     => 'sm',
										'title'   => __( 'Small devices', BASEMENT_TEXTDOMAIN ),
										'values'  => array(
											''                        => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
											'right-border-visible-sm' => __( 'Show', BASEMENT_TEXTDOMAIN ),
											'right-border-hidden-sm'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
										),
										'current' => ''
									),
									array(
										'key'     => 'xs',
										'title'   => __( 'Extra small devices', BASEMENT_TEXTDOMAIN ),
										'values'  => array(
											''                        => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
											'right-border-visible-xs' => __( 'Show', BASEMENT_TEXTDOMAIN ),
											'right-border-hidden-xs'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
										),
										'current' => ''
									)
								)
							)
						)
					);

					foreach ($border_controls as $border_control) {
						$p = $panel->appendChild( $dom->createElement( 'p' ) );
						$key = $border_control['key'];
						$label = $border_control['label'];
						$border_params = $border_control['params'];

						$left_line = isset( $instance[$key] ) ? $instance[$key] : 0;
						$left_line_id = $widget->get_field_id( $key );
						$left_line_name = $widget->get_field_name( $key );

						$input = $p->appendChild( $dom->createElement( 'input' ) );
						$input->setAttribute( 'type','checkbox');
						$input->setAttribute( 'id',$left_line_id );
						$input->setAttribute( 'name',  $left_line_name );
						if(checked( $left_line, true, false )) {
							$input->setAttribute( 'checked', 'checked');
						}
						$label = $p->appendChild( $dom->createElement( 'label', $label ) );
						$label->setAttribute('for',$left_line_id);

						$table = $panel->appendChild( $dom->createElement( 'table' ) );

						foreach ($border_params as $border_param) {
							$tr = $table->appendChild( $dom->createElement( 'tr' ) );
							foreach ( $border_param as $border_size ) {
								$border_size_key = $border_size['key'];
								$border_title = $border_size['title'];
								$border_values = $border_size['values'];
								$td = $tr->appendChild( $dom->createElement( 'td' ) );
								$td->appendChild( $dom->createElement( 'label', $border_title ) );

								$select = $td->appendChild( $dom->createElement( 'select' ) );
								$select->setAttribute( 'id', $widget->get_field_id( $key. '_' . $border_size_key ) );
								$select->setAttribute( 'name', $widget->get_field_name( $key. '_' . $border_size_key ) );
								foreach ( $border_values as $inner_key => $inner_value ) {
									$option = $select->appendChild( $dom->createElement( 'option', $inner_value ) );
									$option->setAttribute( 'value', $inner_key );

									if ( selected( $inner_key, ! empty( $instance[ $key. '_' . $border_size_key ] ) ? $instance[ $key. '_' . $border_size_key ] : '', false ) ) {
										$option->setAttribute( 'selected', 'selected' );
									}
								}
							}
						}
					}
				}

			}


		} else {

			$hr_states = array(
				array(
					'lg' => array(
						'title'   => __( 'Large devices', BASEMENT_TEXTDOMAIN ),
						'values'  => array(
							''              => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
							'hr-visible-lg' => __( 'Show', BASEMENT_TEXTDOMAIN ),
							'hr-hidden-lg'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
						),
						'current' => ''
					),
					'md' => array(
						'title'   => __( 'Medium devices', BASEMENT_TEXTDOMAIN ),
						'values'  => array(
							''              => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
							'hr-visible-md' => __( 'Show', BASEMENT_TEXTDOMAIN ),
							'hr-hidden-md'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
						),
						'current' => ''
					)
				),
				array(
					'sm' => array(
						'title'   => __( 'Small devices', BASEMENT_TEXTDOMAIN ),
						'values'  => array(
							''              => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
							'hr-visible-sm' => __( 'Show', BASEMENT_TEXTDOMAIN ),
							'hr-hidden-sm'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
						),
						'current' => ''
					),
					'xs' => array(
						'title'   => __( 'Extra small devices', BASEMENT_TEXTDOMAIN ),
						'values'  => array(
							''              => __( '&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN ),
							'hr-visible-xs' => __( 'Show', BASEMENT_TEXTDOMAIN ),
							'hr-hidden-xs'  => __( 'Hide', BASEMENT_TEXTDOMAIN )
						),
						'current' => ''
					)
				)
			);

			$dom->appendChild( $dom->createElement( 'p',  __('Visibility horizontal separator', BASEMENT_TEXTDOMAIN) ) );

			$table = $dom->appendChild( $dom->createElement( 'table' ) );
			$table->setAttribute('class','hst');

			foreach ($hr_states as $hr_state) {
				$tr = $table->appendChild( $dom->createElement( 'tr' ) );
				foreach ($hr_state as $key => $value) {
					$td = $tr->appendChild( $dom->createElement( 'td' ) );
					$td->appendChild( $dom->createElement( 'label', $value['title'] ) );


					$select = $td->appendChild( $dom->createElement( 'select' ) );
					$select->setAttribute( 'id', $widget->get_field_id( 'hr_'.$key ) );
					$select->setAttribute( 'name', $widget->get_field_name( 'hr_'.$key ) );
					foreach ( $value['values'] as $inner_key => $inner_value ) {
						$option = $select->appendChild( $dom->createElement( 'option', $inner_value ) );
						$option->setAttribute( 'value', $inner_key );
						if ( selected( $inner_key, ! empty( $instance[ 'hr_'.$key ] ) ? $instance[ 'hr_'.$key ] : $value['current'], false ) ) {
							$option->setAttribute( 'selected', 'selected' );
						}
					}

				}
			}
		}


		echo $dom->saveHTML();

	}


	private function generate_grid( $type, $size ) {
		$array = array();
		switch ( $type ) {
			case 'width' :
				$array[''] = __( 'Don\'t use width', BASEMENT_TEXTDOMAIN );
				for ( $x = 1; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-' . $x ] = __( 'Width ' . $x, BASEMENT_TEXTDOMAIN );
				}
				break;
			case 'offset' :
				$array[''] = __( 'Don\'t use offset', BASEMENT_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-offset-' . $x ] = __( 'Offset ' . $x, BASEMENT_TEXTDOMAIN );
				}
				break;
			case 'push' :
				$array[''] = __( 'Don\'t use push', BASEMENT_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-push-' . $x ] = __( 'Push ' . $x, BASEMENT_TEXTDOMAIN );
				}
				break;
			case 'pull' :
				$array[''] = __( 'Don\'t use pull', BASEMENT_TEXTDOMAIN );
				for ( $x = 0; $x <= 12; $x ++ ) {
					$array[ 'col-' . $size . '-pull-' . $x ] = __( 'Pull ' . $x, BASEMENT_TEXTDOMAIN );
				}
				break;
		}

		return $array;
	}


	/**
	 * Save custom params
	 *
	 * @param $instance
	 * @param $new_instance
	 * @param $old_instance
	 * @param $widget
	 *
	 * @return mixed
	 */
	public function widget_update_form( $instance, $new_instance, $old_instance, $widget ) {
		$instance['hide_title']  = isset( $new_instance['hide_title'] );
		$instance['bottom_line'] = isset( $new_instance['bottom_line'] );

		$sizes = array( 'lg', 'md', 'sm', 'xs' );

		foreach ( $sizes as $value ) {
			$instance[ 'widget_' . $value . '_width' ]  = $new_instance[ 'widget_' . $value . '_width' ];
			$instance[ 'widget_' . $value . '_offset' ] = $new_instance[ 'widget_' . $value . '_offset' ];
			$instance[ 'widget_' . $value . '_push' ]   = $new_instance[ 'widget_' . $value . '_push' ];
			$instance[ 'widget_' . $value . '_pull' ]   = $new_instance[ 'widget_' . $value . '_pull' ];

			$instance['left_line_'.$value]  = $new_instance['left_line_'.$value];
			$instance['right_line_'.$value] = $new_instance['right_line_'.$value];

			$instance['hr_'.$value] = $new_instance['hr_'.$value];
		}

		$instance['left_line']  = isset( $new_instance['left_line'] );
		$instance['right_line'] = isset( $new_instance['right_line'] );

		$instance['borders'] = isset( $new_instance['borders'] );

		$instance['grids'] = isset( $new_instance['grids'] );

		$instance['height'] = $new_instance['height'];

		return $instance;
	}


	/**
	 * Displays custom params
	 *
	 * @param $params
	 *
	 * @return mixed
	 */
	public function widget_display( $params ) {
		global $wp_registered_widgets;
		global $basement_footer;
		global $basement_sidebar;

		$widget_id  = $params[0]['widget_id'];
		$widget_obj = $wp_registered_widgets[ $widget_id ];
		$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
		$widget_num = $widget_obj['params'][0]['number'];

		if($widget_obj['classname'] === 'widget_basement_hr_widget') {

			if ( $basement_footer && $basement_footer['place'] === 'footer' ) {

				$classes = array();
				$dom = new DOMDocument;
				@$dom->loadHTML( $params[0]['before_widget'] );
				foreach ( $dom->getElementsByTagName( 'div' ) as $tag ) {
					$classes[] = $tag->getAttribute( 'class' );
				}

				$sizes = array( 'lg', 'md', 'sm', 'xs' );
				$hr_classes = array();

				foreach ( $sizes as $value ) {
					if ( isset( $widget_opt[ $widget_num ][ 'hr_' . $value ] ) && ! empty( $widget_opt[ $widget_num ][ 'hr_' . $value] ) ) {
						$hr_classes[] = $widget_opt[ $widget_num ][ 'hr_' . $value ];
					}

				}

				$params['0']['before_widget'] = preg_replace('/\bwidget\b/', '', $classes['0']) . ' ' . implode(' ', $hr_classes);
			}
		} else {


			$classes = array();

			$footer_classes = array();

			if ( isset( $widget_opt[ $widget_num ]['hide_title'] ) && ! empty( $widget_opt[ $widget_num ]['hide_title'] ) ) {
				$classes[] = 'widget-hide-title';
			}

			if ( $basement_footer && $basement_footer['place'] === 'footer' ) {

				if ( isset( $widget_opt[ $widget_num ]['height'] ) && ! empty( $widget_opt[ $widget_num ]['height'] ) ) {
					$params[0]['before_widget'] = preg_replace( '/id="/', 'style="min-height:'.absint($widget_opt[ $widget_num ]['height']).'px;" id="', $params[0]['before_widget'], 1 );
				}

				$sizes = array( 'lg', 'md', 'sm', 'xs' );

				if ( isset( $widget_opt[ $widget_num ]['left_line'] ) && ! empty( $widget_opt[ $widget_num ]['left_line'] ) ) {
					$classes[] = 'widget-left-line';
					foreach ( $sizes as $left_borders ) {
						if ( isset( $widget_opt[ $widget_num ][ 'left_line_'.$left_borders ] ) && ! empty( $widget_opt[ $widget_num ][ 'left_line_'.$left_borders ] ) ) {
							$classes[] = $widget_opt[ $widget_num ][ 'left_line_'.$left_borders ];
						}
					}
				}
				if ( isset( $widget_opt[ $widget_num ]['right_line'] ) && ! empty( $widget_opt[ $widget_num ]['right_line'] ) ) {
					$classes[] = 'widget-right-line';
					foreach ( $sizes as $right_borders ) {
						if ( isset( $widget_opt[ $widget_num ][ 'right_line_'.$right_borders ] ) && ! empty( $widget_opt[ $widget_num ][ 'right_line_'.$right_borders ] ) ) {
							$classes[] = $widget_opt[ $widget_num ][ 'right_line_'.$right_borders ];
						}
					}
				}



				foreach ( $sizes as $value ) {
					if ( isset( $widget_opt[ $widget_num ][ 'widget_' . $value . '_width' ] ) && ! empty( $widget_opt[ $widget_num ][ 'widget_' . $value . '_width' ] ) ) {
						$footer_classes[] = $widget_opt[ $widget_num ][ 'widget_' . $value . '_width' ];
					}
					if ( isset( $widget_opt[ $widget_num ][ 'widget_' . $value . '_offset' ] ) && ! empty( $widget_opt[ $widget_num ][ 'widget_' . $value . '_offset' ] ) ) {
						$footer_classes[] = $widget_opt[ $widget_num ][ 'widget_' . $value . '_offset' ];
					}
					if ( isset( $widget_opt[ $widget_num ][ 'widget_' . $value . '_push' ] ) && ! empty( $widget_opt[ $widget_num ][ 'widget_' . $value . '_push' ] ) ) {
						$footer_classes[] = $widget_opt[ $widget_num ][ 'widget_' . $value . '_push' ];
					}
					if ( isset( $widget_opt[ $widget_num ][ 'widget_' . $value . '_pull' ] ) && ! empty( $widget_opt[ $widget_num ][ 'widget_' . $value . '_pull' ] ) ) {
						$footer_classes[] = $widget_opt[ $widget_num ][ 'widget_' . $value . '_pull' ];
					}
				}


				if ( empty( $footer_classes ) ) {
					$classes[] = 'col-lg-3 col-md-4 col-sm-6 col-xs-6';
				} else {
					$classes = array_merge( $classes, $footer_classes );
				}


			} elseif ( $basement_sidebar && $basement_sidebar['place'] === 'aside' ) {
				if ( isset( $widget_opt[ $widget_num ]['bottom_line'] ) && ! empty( $widget_opt[ $widget_num ]['bottom_line'] ) ) {
					$classes[] = 'widget-after-line';
					$params[0]['after_widget'] .= '<hr>';
				}
			}

			$class_output = implode( ' ', $classes );

			$id_w = uniqid();

			$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$class_output} ", $params[0]['before_widget'], 1 );
			$params[0]['before_widget'] = preg_replace( '/id="/', "id=\"{$id_w}-", $params[0]['before_widget'], 1 );
		}

		return $params;
	}

}