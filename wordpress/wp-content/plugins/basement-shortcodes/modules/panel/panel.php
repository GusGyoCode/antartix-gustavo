<?php
// TODO: make specific styles and js for panel
defined('ABSPATH') or die();

class Shortcode_Panel {
	private $textdomain = 'basement_shortcodes_panel';
	private $config = array();
	private static $instance = null;

	public function __construct() {
		add_action( 'media_buttons', array( &$this, 'shortcodes_button' ), 100 );
		add_action( 'admin_footer', array( &$this, 'shortcodes_panel' ) );
	}

	public static function init() {
		self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Shortcode_Panel();
		}
		return self::$instance;
	}

	public function shortcodes_button() {
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$open_button = $dom->appendChild( $dom->createElement( 'a', __( 'Shortcodes', BASEMENT_SHORTCODES_TEXTDOMAIN ) ) );
		$open_button->setAttribute( 'class', 'button button-primary basement_shortcodes_panel_open_button' );
		$open_button->setAttribute( 'title', 'Shortcodes' );
		$open_button->setAttribute( 'href', '#' );

		echo $dom->saveHTML();
	}

	public function shortcodes_panel() {
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$overlay = $dom->appendChild( $dom->createElement( 'div' ) );
		$overlay->setAttribute( 'id', 'basement_shortcodes_panel_overlay' );
		$overlay->setAttribute( 'title', __( 'Click to close', BASEMENT_SHORTCODES_TEXTDOMAIN ) );

		$panel = $overlay->appendChild( $dom->createElement( 'div' ) );
		$panel->setAttribute( 'id', $this->textdomain );
		$panel->setAttribute( 'title', '' );

		$shortcode_output = $panel->appendChild( $dom->createElement( 'div' ) );
		$shortcode_output->setAttribute( 'id', 'shortcode_output' );

		$settings_page = $panel->appendChild( $dom->createElement( 'div' ) );
		$settings_page->setAttribute( 'class', 'basement_settings_page' );

		$settings_content = $settings_page->appendChild( $dom->createElement( 'div' ) );
		$settings_content->setAttribute( 'class', 'basement_settings_content' );


		$this->create_menu( $settings_content );
		$this->create_sections( $settings_content );




		echo $dom->saveHTML();
	}

	public function create_menu( $container ) {
		$dom = $container->ownerDocument;




		$settings_panel_menu = $container->appendChild( $dom->createElement( 'div' ) );
		$settings_panel_menu->setAttribute( 'class', 'basement_settings_panel_menu' );

		$groups = Shortcode_Group::all();

		foreach ($groups as $group_name => $group_title ) {
			$settings_panel_menu_item = $settings_panel_menu->appendChild( $dom->createElement( 'div' ) );
			$settings_panel_menu_item->setAttribute( 'class', 'basement_settings_panel_menu_item' );

			$settings_panel_menu_link = $settings_panel_menu_item->appendChild( $dom->createElement( 'a', $group_title ) );
			$settings_panel_menu_link->setAttribute( 'href', '#' );
			$settings_panel_menu_link->setAttribute( 'class', 'basement_admin_hover_background_color_1 basement_admin_hover_color_3 basement_admin_right_after_active_color_2' );
			$settings_panel_menu_link->setAttribute( 'data-section', $group_name );
			$settings_panel_menu_link->setAttribute( 'data-section-name', $group_title );
		}
	}

	public static function create_sections( $container ) {
		$dom = $container->ownerDocument;

		$settings_panel_sections = $container->appendChild( $dom->createElement( 'div' ) );
		$settings_panel_sections->setAttribute( 'class', 'basement_settings_panel_sections basement_admin_border_color_3' );

		$shortcodes = Basement_Shortcodes::instance()->collection();

		foreach ( $shortcodes as $shortcode_tag => $shortcode ) {
			
			if($shortcode_tag !== 'snippets') {
				$section_config = $shortcode->section_config();

				$settings_panel_section = $settings_panel_sections->appendChild( $dom->createElement( 'div' ) );
				$settings_panel_section->setAttribute( 'class', 'basement_settings_panel_section' );
				$settings_panel_section->setAttribute( 'data-section', $shortcode->group() );

				$link = $settings_panel_section->appendChild( $dom->createElement( 'div' ) );
				$link->setAttribute( 'class', 'basement_shortcode_panel_button basement_admin_border_hover_color_3' );
				$link->setAttribute( 'data-tag', $shortcode->tag() );
				$link->setAttribute( 'data-name', $shortcode->name() );

				$link_title = $link->appendChild( $dom->createElement( 'div', $shortcode->title() ) );
				$link_title->setAttribute('class', 'basement_shortcode_panel_button_title' );

				if ( !empty( $section_config[ 'description'] ) ) {
					$link_description = $link->appendChild( $dom->createElement( 'div' ) );
					$link_description->setAttribute('class', 'basement_shortcode_panel_button_description' );
					
					
					$min_desc = $dom->createDocumentFragment();
					$min_desc->appendXML( $section_config[ 'description' ] );
					$link_description->appendChild($min_desc);
					
				}

				$wrapper = $settings_panel_section->appendChild( $dom->createElement( 'div' ) );
				$wrapper->setAttribute( 'class', 'basement_shortcode_panel_shortcode_wrapper' );
				$wrapper->setAttribute( 'data-tag', $shortcode->tag() );
				$wrapper->setAttribute( 'data-name', $shortcode->name() );

				if ( $shortcode->enclosing() ) {
					$wrapper->setAttribute( 'data-shortcode-enclosing', true );
				}

				if ( $shortcode->wrap() ) {
					$wrapper->setAttribute( 'data-wrap', $shortcode->wrap() );
				}

				$header = $wrapper->appendChild( $dom->createElement( 'div' ) );

				$back_button = $header->appendChild( $dom->createElement( 'div', '↩' ) );
				$back_button->setAttribute( 'class', 'button  button-primary button-large basement_shortcodes_back_button' );

				$header->appendChild( $dom->createTextNode( $shortcode->title() ) );
				$header->setAttribute( 'class', 'basement_shortcode_panel_shortcode_header' );

				self::create_section( $section_config, $wrapper );

				$button = $wrapper->appendChild( $dom->createElement( 'a' ) );
				$button->setAttribute( 'class', 'basement_shortcode_builder_button button  button-primary button-large' );
				$button_content = $dom->createDocumentFragment();
				$button_content->appendXML( sprintf( __( 'Insert %s shortcode', BASEMENT_SHORTCODES_TEXTDOMAIN ), '<span></span>' ) );
				$button->appendChild($button_content);
			} else {
				$section_config = $shortcode->section_config();
				
				$snips = new Basement_Snippets_Plugin();
				
			
				$settings_panel_section = $settings_panel_sections->appendChild( $dom->createElement( 'div' ) );
				$settings_panel_section->setAttribute( 'class', 'basement_settings_panel_section' );
				$settings_panel_section->setAttribute( 'data-section', $shortcode->group() );
				
				
				$fragment = $dom->createDocumentFragment();
				$fragment->appendXML( $snips->generate_snippets_builder());
				$settings_panel_section->appendChild( $fragment);
				#$dom->loadHTML($snips->generate_snippets_builder());
				
				self::create_section( $section_config, $wrapper );

			}
		}
	}

	public static function create_section( $config, $container ) {
		$dom = ( $container instanceof DOMDocument ) ? $container : $container->ownerDocument ;

		if ( !empty( $config[ 'description'] ) ) {
				
			$section_description = $container->appendChild( $dom->createElement( 'div' ) );
			$section_description->setAttribute( 'class', 'basement_shortcodes_panel_section_description' );
	
			
			$full_desc = $dom->createDocumentFragment();
			$full_desc->appendXML( $config[ 'description' ] );
			$section_description->appendChild($full_desc);
			
			
		}

		if ( !empty( $config[ 'blocks'] ) ) {
			$blocks = $container->appendChild( $dom->createElement( 'div' ) );
			$blocks->setAttribute( 'class', 'basement_shortcodes_panel_blocks' );

			$form = new Basement_Form();

			foreach ( $config[ 'blocks'] as $block_index => $block ) {
				$block = wp_parse_args( 
					$block, 
					array(
						'type' => 'text',
						'title' => '',
						'description' => '',
						'allow_empty' => true,
						'param' => '',
						'input' => array(),
					)
				);

				if ( 'dom' == $block[ 'type' ] ) {

					$inputs_block = self::create_block( 
						$block[ 'title' ],
						$block[ 'description' ],
						$blocks
					);

					$inputs_block->appendChild( 
						$dom->importNode( 
							$block[ 'input' ],
							true 
						) 
					);
				
				} else {
					/**
					 * Allows to pass plain string as input instead of array
					 */
					if ( !is_array( $block[ 'input' ] ) ) {
						$block[ 'input' ] = array(
							'type' => $block[ 'input' ]
						);
					}

					if ( empty( $block[ 'type' ] ) ) {
						$block[ 'type' ] = 'text';
					}
					
					
					/**
					 * Set proper input type for toggler blocks
					 */
					if ( 'toggler' == $block[ 'type' ] ) {
						$block[ 'input' ][ 'type' ] = 'checkbox';
						if (empty( $block[ 'input' ][ 'label_text' ] ) ) {
							$block[ 'input' ][ 'label_text' ] = __( 'Yes', BASEMENT_SHORTCODES_TEXTDOMAIN );
						}
					} else if ( 'colorpicker' == $block[ 'type' ] ) {
						$block[ 'type' ] = 'text';
						$block[ 'input' ][ 'type' ] = 'colorpicker';
					} else if ( 'radio' == $block[ 'type' ] ) {
						if ( empty( $block[ 'input' ][ 'type' ] ) && !empty( $block[ 'input' ][ 'values' ] ) ) {
							$block[ 'input' ][ 'type' ] = 'radios'; 
						}
					} else if ( 'select' == $block[ 'type' ] ) {
						if ( !empty( $block[ 'input' ][ 'values' ] ) ) {
							$block[ 'input' ][ 'type' ] = 'select'; 
						}
					} else if ( 'textarea' == $block[ 'type' ] ) {
						$block[ 'type' ] = 'text';
						if ( empty( $block[ 'input' ][ 'type' ] ) ) {
							$block[ 'input' ][ 'type' ] = 'textarea'; 
						}
					} else if ( 'checkboxes' == $block[ 'type' ] ) {
						$block[ 'type' ] = 'check';
						if ( !empty( $block[ 'input' ][ 'values' ] ) ) {
							$block[ 'input' ][ 'type' ] = 'checkboxes'; 
						}
					} else if ( 'radios' == $block[ 'type' ] ) {
						$block[ 'type' ] = 'radio';
						if ( empty( $block[ 'input' ][ 'type' ] ) && !empty( $block[ 'input' ][ 'values' ] ) ) {
							$block[ 'input' ][ 'type' ] = 'radios'; 
						}
					} else if ( 'sortable_posts' == $block[ 'type' ] ) {
						$block[ 'type' ] = 'check';
						if ( empty( $block[ 'input' ][ 'type' ] ) ) {
							$block[ 'input' ][ 'type' ] = 'sortable_posts'; 
						}
					} else if ( 'posts' == $block[ 'type' ] ) {
						$block[ 'type' ] = 'radio';
						if ( empty( $block[ 'input' ][ 'type' ] ) ) {
							$block[ 'input' ][ 'type' ] = 'posts'; 
						}
					} 
					
					if ( empty( $block[ 'input' ][ 'type' ] ) ) {
						$block[ 'input' ][ 'type' ] = 'text';
					}

					if ( 'checkbox' == $block[ 'input' ][ 'type' ] && empty( $block[ 'input' ][ 'no_hidden' ]) ) {
						 $block[ 'input' ][ 'no_hidden' ] = true;
					}

					if ( empty(  $block[ 'input' ][ 'name' ] ) ) {
						mt_srand( ( double ) microtime() * 1000000 );

						$block[ 'input' ][ 'name' ] = 'shortcode_param_' . md5( mt_rand(0, 100) . $block[ 'type' ] . $block_index );
					}

					$block[ 'input' ] = Basement_Shortcode::create_config_data_attributes( $block[ 'input' ], $block[ 'type' ], $block[ 'param' ], $block[ 'allow_empty' ] );

					$inputs_block = self::create_block( 
						$block[ 'title' ],
						$block[ 'description' ],
						$blocks
					);

					$inputs_block->appendChild( 
						$dom->importNode( 
							$form->create_from_config(  $block[ 'input' ] ),
							true 
						) 
					);
				}

			}
		}

		return $container;
	}

	public static function create_block( $title, $description, $container ) {
		$block = $container->appendChild( $container->ownerDocument->createElement( 'div' ) );
		$block->setAttribute( 'class', 'basement_shortcodes_panel_block' );
		
		$block_description = $block->appendChild( $container->ownerDocument->createElement( 'div' ) );
		$block_description->setAttribute( 'class', 'basement_shortcodes_panel_block_description' );
		
		if ( $title ) {
			$block_description_title = $block_description->appendChild( $container->ownerDocument->createElement( 'div', $title ) );
			$block_description_title->setAttribute( 'class', 'basement_shortcodes_panel_block_description_title' );
		}

		if ( $description ) {
			$block_description_text = $block_description->appendChild( $container->ownerDocument->createElement( 'div', $description ) );
			$block_description_text->setAttribute( 'class', 'basement_shortcodes_panel_block_description_text' );
		}

		$inputs_block = $block->appendChild( $container->ownerDocument->createElement( 'div' ) );
		$inputs_block->setAttribute( 'class', 'basement_shortcodes_panel_block_inputs' );

		return $inputs_block;
	}


}