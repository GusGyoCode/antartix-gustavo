<?php
defined('ABSPATH') or die();

class Basement_Form_Input_Selectext extends Basement_Form_Input {

    private $version = '1.0.0';

    public function create() {
        if ( !is_array( $this->config ) || empty( $this->config[ 'values' ] ) ) {
            return $this->dom->createTextNode( __( 'Select group config is broken', BASEMENT_TEXTDOMAIN ) );
        }

        extract( $this->config );


        $wrapper_class = !empty( $wrapper_class ) ? $this->textdomain . $wrapper_class : '';
        $radios_wrapper_class = !empty( $radios_wrapper_class ) ? $this->textdomain . $radios_wrapper_class : '';

        $current_value = !empty( $current_value ) ? $current_value : 0;

        $label = $this->dom->appendChild( $this->dom->createElement( 'label' ) );


        if($this->config['dom_change'] !== 'remove_span_before_select') {
            $label->appendChild( $this->dom->createElement( 'span', $label_text ) );
        }


        /*if($this->config['name'] !== 'col_style_border' && $this->config['name'] !== 'col_unit_border' && $this->config['name'] !== 'row_style_border' && $this->config['name'] !== 'row_unit_border') {
            $label->appendChild( $this->dom->createElement( 'span', $label_text ) );

        }*/

        $select = $label->appendChild( $this->dom->appendChild( $this->dom->createElement( 'select' ) ) );
        $select->setAttribute( 'autocomplete', 'off' );

        if ( $name ) {
            $select->setAttribute( 'name', $this->create_name( $name, $name_attr_part ) );
        }

        if ( $id ) {
            $select->setAttribute( 'id', $id );
        }

        if ( $class ) {
            $select->setAttribute( 'class', $class );
        }

        $this->append_dom_node_attributes( $select, $attributes );




        foreach ( $values as $value => $option_text) {
            $option = $this->dom->createElement( 'option', $option_text );
            $option->setAttribute( 'value', $value );
            if ( $current_value === $value ) {
                $option->setAttribute( 'selected', 'selected' );
            }

            $select->appendChild( $option );
        }
        return $label;
    }
}
