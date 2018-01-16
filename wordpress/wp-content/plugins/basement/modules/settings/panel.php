<?php
defined('ABSPATH') or die();

class Basement_Settings_Panel {

	private static $instance = null;
	public $textdomain = 'basement_settings_panel';
	private $version = '1.0.0';

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Settings_Panel();
		}
		return self::$instance;
	}

	public function create_panel_with_name( $panel_name, $params = array() ) {
		$panel_config = apply_filters( $panel_name . '_panel_config', array() );
		$params = apply_filters( $panel_name . '_params', $params );

		return $this->create_panel( $panel_config, $params );
	}

	public function create_panel( $panel_config, $params = array() ) {

		if ( !is_array( $panel_config ) ) {
			return;
		}

		$form_builder = new Basement_Form();

		if ( !is_array( $params ) ) {
			$params = array();
		}

		$params = wp_parse_args( $params, array(
			'form_type' => '',
			'option_page' => 'basement_theme_options',
			'no_form' => false,
			'no_submit' => false,
			'no_wrap_class' => false
		) );

		$panel_hash = md5( serialize( $panel_config ) );

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$wrapper = $dom->appendChild( $dom->createElement( 'div') );
		if ( !$params[ 'no_wrap_class' ] ) {
			$wrapper->setAttribute( 'class', 'wrap' );
		}

		$settings_page = $wrapper->appendChild( $dom->createElement( 'div' ) );
		$settings_page->setAttribute( 'class', 'basement_settings_page' );
		$settings_page->setAttribute( 'title', '' );

		apply_filters( $this->textdomain . '_' . $panel_hash . '_header', $settings_page );

		if ( !$params[ 'no_form' ] ) {
			$form = $settings_page->appendChild( $dom->createElement( 'form' ) );
			if ( $params[ 'form_type' ] == 'simple_options' ) {
				$form->setAttribute( 'action', 'options.php' );
				$setting_fields = $dom->createDocumentFragment();
				ob_start();
				settings_fields( $params[ 'option_page' ] );
				$setting_fields->appendXML( ob_get_clean() );
				$form->appendChild( $setting_fields );
			} else {
				$form->setAttribute( 'action', '' );
			}
			$form->setAttribute( 'method', 'POST' );
		} else {
			$form = $settings_page->appendChild( $dom->createElement( 'div' ) );
		}

		$settings_content = $form->appendChild( $dom->createElement( 'div' ) );
		$settings_content->setAttribute( 'class', 'basement_settings_content' );

		$settings_panel_menu = $settings_content->appendChild( $dom->createElement( 'div' ) );
		$settings_panel_menu->setAttribute( 'class', $this->textdomain . '_menu' );
		$settings_panel_menu->setAttribute( 'data-panel', $panel_hash );

		$settings_panel_sections = $settings_content->appendChild( $dom->createElement( 'div' ) );
		$settings_panel_sections->setAttribute( 'class', $this->textdomain . '_sections basement_admin_border_color_3' );

		$panel_markup = '';

		self::proceed_config( $panel_config, $settings_panel_menu, $settings_panel_sections );



		apply_filters( $this->textdomain . '_' . $panel_hash . '_append_to_settings_panel_sections', $settings_panel_sections );

		if ( !$params[ 'no_form' ] && !$params[ 'no_submit' ] ) {
			$settings_panel_sections->appendChild( $dom->importNode( $form_builder->create_submit(), true ) );
		}

		return $wrapper;
	}

	protected static function proceed_config( $panel_config, $settings_panel_menu, $settings_panel_sections ) {
		$dom = $settings_panel_menu->ownerDocument;

		foreach ( $panel_config as $section_name => $section_config ) {

			if ( !$section_config && !is_array( $section_config ) ) {
				continue;
			}

			if ( empty( $section_config[ 'title' ] ) || !is_string( $section_config[ 'title' ] ) ) {
				$section_config[ 'title' ] = $section_name;
			}

			$settings_panel_menu_item = $settings_panel_menu->appendChild( $dom->createElement( 'div' ) );
			$settings_panel_menu_item->setAttribute( 'class', 'basement_settings_panel_menu_item' );

			$settings_panel_menu_link = $settings_panel_menu_item->appendChild( $dom->createElement( 'a', $section_config[ 'title' ] ) );
			$settings_panel_menu_link->setAttribute( 'href', '#' );

			// $settings_panel_menu_link->setAttribute( 'class', 'basement_admin_hover_background_color_1 basement_admin_active_background_color_1' );
			$settings_panel_menu_link->setAttribute( 'class', 'basement_admin_hover_background_color_1 basement_admin_hover_color_3 basement_admin_right_after_active_color_2' );
			$settings_panel_menu_link->setAttribute( 'data-section', $section_name );
			$settings_panel_menu_link->setAttribute( 'data-section-name', $section_config[ 'title' ] );

			if ( !empty( $section_config[ 'group' ] ) &&
				 !empty( $section_config[ 'items' ] ) && $section_config[ 'group' ] ) {
				self::proceed_config( $section_config[ 'items' ], $settings_panel_menu_item, $settings_panel_sections );
				continue;
			}

			$settings_panel_section = $settings_panel_sections->appendChild( $dom->createElement( 'div' ) );
			$settings_panel_section->setAttribute( 'class', 'basement_settings_panel_section' );
			$settings_panel_section->setAttribute( 'data-section', $section_name );


			if ( !empty( $section_config[ 'markup' ] ) && is_string( $section_config[ 'markup' ] ) ) {
				$fragment = $dom->createDocumentFragment();
				$fragment->appendXML( $section_config[ 'markup' ] );
				$settings_panel_section->appendChild( $fragment );
			} else if ( !empty( $section_config[ 'callback' ] ) &&
				is_callable( $section_config[ 'callback' ] ) ) {
					$object = array_shift( $section_config[ 'callback' ] );
					$method = array_shift( $section_config[ 'callback' ] );
					$object->$method( $section_config, $settings_panel_section );
			} else if ( !empty( $section_config[ 'blocks' ] ) ) {
				self::create_section( $section_config, $settings_panel_section );
			} else if ( !empty( $section_config[ 'filter' ] ) && is_string( $section_config[ 'filter' ] ) ) {
				apply_filters( $section_config[ 'filter' ], $settings_panel_section );
			} else {
				$settings_panel_section->appendChild( $dom->createTextNode( 'Broken object configuration' ) );
			}

		}
	}

	public static function create_section( $config, $container ) {
		extract( $config );

		$dom = ( $container instanceof DOMDocument ) ? $container : $container->ownerDocument ;

		$container = $container->appendChild( $dom->createElement( 'div' ) );

		$form = new Basement_Form();

		if ( !empty( $blocks ) ) {
			foreach ($blocks as $block ) {
				$description = '';
				extract( $block );

				if ( 'dom' == isset($block[ 'type' ]) ) {

					$inputs_block = self::create_block(
							empty($title) ? '' : $title,
							empty($description) ? '' : $description,
							$container
					);

					$inputs_block->appendChild(
							$dom->importNode(
									$block[ 'input' ],
									true
							)
					);

				} else {

					$inputs_block = self::create_block(
							empty($title) ? '' : $title,
							empty($description) ? '' : $description,
							$container
					);

					if (!empty($inputs)) {
						foreach ($inputs as $input) {
							// TODO: move, register setting in Settings_Theme

							$inputs_block->appendChild(
									$dom->importNode(
											$form->create_from_config($input),
											true
									)
							);
						}
					} else if (!empty($input)) {
						$inputs_block->appendChild(
								$dom->importNode(
										$form->create_from_config($input),
										true
								)
						);
					}
				}
			}
		}

		return $dom;
	}

	public static function create_block( $title, $description, $container ) {
		
		$dom = ( $container instanceof DOMDocument ) ? $container : $container->ownerDocument ;
		
		$block = $container->appendChild( $container->ownerDocument->createElement( 'div' ) );
		$block->setAttribute( 'class', 'basement_settings_panel_block' );

		$block_description = $block->appendChild( $container->ownerDocument->createElement( 'div' ) );
		$block_description->setAttribute( 'class', 'basement_settings_panel_block_description' );

		if ( $title ) {
			$block_description_title = $block_description->appendChild( $container->ownerDocument->createElement( 'div', $title ) );
			$block_description_title->setAttribute( 'class', 'basement_settings_panel_block_description_title' );
		}

		if ( $description ) {
			
			
			$field_html = $dom->createDocumentFragment();
			$field_html->appendXML($description);
			
			
			$block_description_text = $block_description->appendChild( $container->ownerDocument->createElement( 'div' ) );
			$block_description_text->setAttribute( 'class', 'basement_settings_panel_block_description_text' );
			$block_description_text->appendChild($field_html);
			
			
			/*$block_description_text = $block_description->appendChild( $container->ownerDocument->createElement( 'div', $description ) );
			$block_description_text->setAttribute( 'class', 'basement_settings_panel_block_description_text' );
			*/
			
			
			
		}

		$inputs_block = $block->appendChild( $container->ownerDocument->createElement( 'div' ) );
		$inputs_block->setAttribute( 'class', 'basement_settings_panel_block_inputs' );

		return $inputs_block;
	}

}