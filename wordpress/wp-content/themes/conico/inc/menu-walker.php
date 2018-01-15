<?php

class Conico_Default_Menu extends Walker_Nav_Menu {

	protected $megamenu = false;
	protected $megamenu_cols = false;

	function start_lvl( &$output, $depth = 0, $args = array() ) {

		$basement_header = Basement_header();

		$padding_bottom = isset($basement_header['header_padding_bottom']) && is_numeric($basement_header['header_padding_bottom']) ? $basement_header['header_padding_bottom'] : '27';

		$indent  = str_repeat( "\t", $depth );
		$classes = '';
		if ( $depth === 0 ) {
			$classes = '';
		} elseif ( $depth === 1 ) {
			$classes = 'smart-deep-menu ';
		} elseif ( $depth === 2 ) {
			$classes = '';
		}


		if ( $this->megamenu && $depth > 0 ) {
			$output .= '';
		} else {

			$col_builder = '';
			if ( $this->megamenu ) {
				$col_builder = '<div class="divtable container"><div class="divcell col-sm-3 basement-col basement-col-1"><ul></ul></div><div class="divcell col-sm-3 basement-col basement-col-2"><ul></ul></div><div class="divcell col-sm-3 basement-col basement-col-3"><ul></ul></div><div class="divcell col-sm-3 basement-col basement-col-4"><ul></ul></div>';
				$classes .= 'is-mega-menu';
				$g = 'div';
			} else {
				$g = 'ul';
			}

			$output .= "\n$indent<div class=\"wpnav-dropdown sf-mega\" style=\"padding-top: {$padding_bottom}px;\"><{$g} class=\"$classes\">$col_builder\n";
		}


	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );

		if ( $this->megamenu && $depth > 0 ) {
			$output .= '';
		} else {
			$f           = '/ul';
			$col_builder = '';
			if ( $this->megamenu ) {
				$f           = '/div';
				$col_builder = '</div>';
			}
			$output .= "$indent <{$f}>$col_builder</div>\n";

		}
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';


		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$col_status = '';
		$col_id     = '';
		$col_title = '';
		if ( $depth === 1 ) {
			$col_status = get_post_meta( $item->ID, 'menu-item-field-column', true );
			$col_title = get_post_meta( $item->ID, 'menu-item-field-title', true );
			if ( $col_status ) {
				$col_id    = 'data-id="' . substr( $col_status, 4 ) . '"';
				$classes[] = 'mega-menu-col mega-menu-' . $col_status;
			}
			if($col_title) {
				$classes[] = 'this-item-is-title';
			}
		}


		if ( $depth === 0 ) {
			$megamenu_status = get_post_meta( $item->ID, 'menu-item-field-megamenu', true );
			if ( $megamenu_status === 'yes' ) {
				$this->megamenu = true;
				$classes[]      = 'mega-menu dropdown-static';
			} else {
				$this->megamenu = false;
			}
		}


		if ( ( $depth === 0 || $depth === 1 || $depth === 2 ) && ( in_array( 'menu-item-has-children', $classes ) ) ) {
			if ( $this->megamenu && $depth > 0 ) {

			} else {
				$classes[] = 'dropdown';
			}
		}

		if ( $depth === 0 && ( in_array( 'current-menu-ancestor', $classes ) ) ) {
			$classes[] = 'active';
		}

		if ( $depth === 0 && ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true ) ) ) {

			$classes   = array_diff( $classes, array( 'current-menu-item', 'current_page_item', 'active' ) );
			$classes[] = 'active';
		}


		if ( $this->megamenu && $depth > 1 ) {
			return;
		}

		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );


		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';


		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '-default"' : '';


		if ( $col_id ) {
			$r = 'div';
		} else {
			$r = 'li';
		}
		$output .= $indent . '<' . $r . ' ' . $col_id . ' ' . $id . $class_names . '>';

		$arrows         = '';
		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';


		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		if(empty($col_title)) {
			$item_output = $args->before;
			$item_output .= '<a' . $attributes . '>';
			$item_output .= $args->link_before . $title . $args->link_after;
			$item_output .= $arrows . '</a>';
			$item_output .= $args->after;
		} else {
			$item_output = $args->before;
			$item_output .= '<h6>'.$title.'</h6>';
			$item_output .= $args->after;
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if ( $this->megamenu && $depth > 1 ) {
			return;
		}

		$col_id = '';
		$li     = '/li';
		if ( $depth === 1 ) {
			$col_status = get_post_meta( $item->ID, 'menu-item-field-column', true );
			if ( $col_status ) {
				$li = "/div";
			}
		}

		$output .= "<{$li}>\n";
	}
}