<?php
defined('ABSPATH') or die();

class Basement_Form_Input {

	private $version = '1.0.0';

	protected $textdomain = 'basement_form';
	protected $config = array();
	protected $dom;
	
	public function __construct( $config = array() ) {
		$this->dom = new DOMDocument( '1.0', 'UTF-8' );
		$this->normalize_config( $config );
	}

	public function normalize_config( $config ) {
		$this->config = wp_parse_args( $config, array(
				'type' => 'text',
				'name' => '',
				'name_attr_part' => '',
				'id' => '',
				'value' => '',
				'autocomplete' => 'off',
				'class' => '',
				'attributes' => array(),
				'field_name' => '',
				'field_id' => '',
				'label_text' => '',
				'no_wrapper' => false,
				'no_hidden' => false,
			)
		);
	}

	public function create_name( $name, $prefix = null) {
		if ( $prefix ) {
			return $prefix . '[' . $name . ']';
		}
		return $name;
	}

	public function create_id( $name, $prefix = null ) {
		if ( $prefix ) {
			$name = $this->create_name( $name, $prefix );
		}
		return str_replace(
			array( ' ', '][', '[', ']', '-'),
			array( '_', '_', '_', '', '_'),
			$name);
	}

	public function create_name_and_id( $name, $prefix = null ) {
		$name = $this->create_name( $name, $prefix );
		return array(
			$name,
			$this->create_id( $name )
		);
	}

	public function create() {
		if ( !is_array( $this->config ) ) {
			return $this->dom->createTextNode( __( 'Input config is broken', BASEMENT_TEXTDOMAIN ) );
		}

		extract( $this->config );

		$id = $id ? $id : $this->create_id( $name, $name_attr_part );

		$input = $this->dom->createElement( 'input' );
		$input->setAttribute( 'type', $type );
		$input->setAttribute( 'value', $value );

		if($type === 'number') {
			if(isset($min) && is_numeric($min)) {
				$input->setAttribute( 'min', $min);
			}
			if(isset($max) && is_numeric($max)) {
				$input->setAttribute( 'max', $max );
			}

			if(isset($step) && is_numeric($step)) {
				$input->setAttribute( 'step', $step );
			}
		}

		if(!empty($style)) {
			$input->setAttribute( 'style', $style );
		}

		
		if ( $name ) {
			$input->setAttribute( 'name', $this->create_name( $name, $name_attr_part ) );
		}

		if ( $id || $type != 'hidden' ) {
			$input->setAttribute( 'id', $id );
		}

		if ( $class ) {
			$input->setAttribute( 'class', $class );
		}

		$input->setAttribute( 'autocomplete', $autocomplete );
		$input = $this->append_dom_node_attributes( $input, $attributes );
		if ( !$no_wrapper ) {
			$wrapper = $this->dom->appendChild( $this->dom->createElement( 'div' ) );
			$wrapper->setAttribute( 'class', $this->textdomain . '_input_wrapper ' );

			if ( !empty( $label_text ) ) {
				$label = $wrapper->appendChild( $this->dom->createElement( 'label', $label_text ) );
				$label->setAttribute( 'for', $id  );
			}



			if(isset($help_icon) && !empty($help_icon)) {
					switch ($help_icon) {
						case 'icons' :

							$div = $wrapper->appendChild($this->dom->createElement('div'));
							$div->appendChild($input);
							$div->setAttribute('style','position:relative;');


							$help_icon = $div->appendChild( $this->dom->createElement( 'a', __('?',BASEMENT_TEXTDOMAIN) ) );
							$help_icon->setAttribute('class','basement-help-popup');
							$help_icon->setAttribute('href','#');
							$help_icon->setAttribute('title',__('Help',BASEMENT_TEXTDOMAIN));
							break;
					}

			} else {
				$wrapper->appendChild( $input );
			}



			if(isset($help_text)) {
				if($help_text) {
					$help = $wrapper->appendChild( $this->dom->createElement( 'small', $help_text ) );
				}
			}





			return $wrapper;
		}

		return $input;
	}

	public function append_dom_node_attributes( $node, $attributes ) {
		if ( !(empty( $attributes ) && is_array( $attributes ) ) ) {
			foreach ( $attributes as $name => $value ) {
				$current_values = explode(' ', $node->getAttribute( $name ) );
				$current_values[] = $value;
				$node->setAttribute( $name, trim( implode( ' ', $current_values ), ' ' ) );
			}
		}
		return $node;
	}

	protected function import_dom_node( $node ) {
		return $this->dom->importNode( $node, true );
	}

}