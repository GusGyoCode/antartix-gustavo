<?php
class Conico_Simple_Menu extends Walker_Nav_Menu {

	public $panels = array();


	public function walk( $elements, $max_depth ) {
		$args = array_slice(func_get_args(), 2);
		$output = '';

		//invalid parameter or nothing to walk
		if ( $max_depth < -1 || empty( $elements ) ) {
			return $output;
		}

		$parent_field = $this->db_fields['parent'];

		// flat display
		if ( -1 == $max_depth ) {
			$empty_array = array();
			foreach ( $elements as $e )
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			return $output;
		}

		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e) {
			if ( empty( $e->$parent_field ) )
				$top_level_elements[] = $e;
			else
				$children_elements[ $e->$parent_field ][] = $e;
		}


		if ( empty($top_level_elements) ) {

			$first = array_slice( $elements, 0, 1 );
			$root = $first[0];

			$top_level_elements = array();
			$children_elements  = array();
			foreach ( $elements as $e) {
				if ( $root->$parent_field == $e->$parent_field )
					$top_level_elements[] = $e;
				else
					$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		foreach ( $top_level_elements as $e )
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );

		if ( ( $max_depth == 0 ) && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans )
				foreach ( $orphans as $op )
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
		}

		$this->display_panels();

		return $output;
	}


	public function display_panels() {
		if(!empty($this->panels)) {

			foreach ($this->panels as $depth => $panels) {


				foreach ($panels as $key => $panel) {
					$depth_key = $depth + 2;
					$div = "<div id=\"simple-menu-id-{$key}\" data-depth=\"{$depth_key}\" class=\"%s\" data-id=\"{$key}\">%s</div>";

					$cols = "<div class=\"row\"><div class=\"col-xs-6 col-sm-6 col-md-6 col-lg-3\">%s</div><div class=\"col-xs-6 col-sm-6 col-md-6 col-lg-3\">%s</div><div class=\"col-xs-6 col-sm-6 col-md-6 col-lg-3 col-moved\">%s</div><div class=\"col-xs-6 col-sm-6 col-md-6 col-lg-3 col-moved\">%s</div></div>";
					$i = '';

					$mega_cols = array();

					foreach ($panel as $item) {

						$col_title = $item['col_title'];
						$col_status = $item['col_status'];

						if(!empty($col_status)) {
							if('yes' === $col_title) {
								$col_content = '<div id="menu-item-'.$item['id'].'" class="'.$item['classes'].' simple-menu-title">' . $item['title'] . '</div>';
							} else {
								$col_content = '<div id="menu-item-'.$item['id'].'" class="'.$item['classes'].' simple-mega-link" data-id="'.$item['id'].'"><a href="' . $item['url'] . '">' . $item['title'] . '</a></div>';
							}
							if(isset($mega_cols[$col_status])) {
								$mega_cols[$col_status] .= $col_content;
							} else {
								$mega_cols[$col_status] = $col_content;
							}

						} else {
							$i .= '<div id="menu-item-'.$item['id'].'" class="'.$item['classes'].'"  data-id="'.$item['id'].'"><a href="' . $item['url'] . '">' . $item['title'] . '</a></div>';
						}
					}


					if(!empty($mega_cols)) {
						$col_1 = isset($mega_cols['col-1']) ? $mega_cols['col-1'] : '';
						$col_2 = isset($mega_cols['col-2']) ? $mega_cols['col-2'] : '';
						$col_3 = isset($mega_cols['col-3']) ? $mega_cols['col-3'] : '';
						$col_4 = isset($mega_cols['col-4']) ? $mega_cols['col-4'] : '';
						$cols = sprintf($cols, $col_1, $col_2, $col_3, $col_4);
						echo sprintf($div, 'simple-menu-element simple-menu-mega fade out simple-menu-sub', $cols);
					} else {
						echo sprintf($div, 'simple-menu-element simple-menu-default fade out simple-menu-sub', $i);
					}

				}
			}
		}
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}



		$id_field = $this->db_fields['id'];
		$id       = $element->$id_field;

		//display this element
		$this->has_children = ! empty( $children_elements[ $id ] );
		if ( isset( $args[0] ) && is_array( $args[0] ) ) {
			$args[0]['has_children'] = $this->has_children; // Back-compat.
		}

		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		if($depth == 0) {
			call_user_func_array( array( $this, 'start_el' ), $cb_args );
		}

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach ( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					#call_user_func_array(array($this, 'start_lvl'), $cb_args);
				}

				$classes = empty( $child->classes ) ? array() : (array) $child->classes;
				$classes[] = 'menu-item-' . $child->ID;


				$args = apply_filters( 'nav_menu_item_args', $args, $child, $depth );

				$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $child, $args, $depth ) );
				$col_status = get_post_meta( $child->ID, 'menu-item-field-column', true );
				$col_title = get_post_meta( $child->ID, 'menu-item-field-title', true );

				$this->panels[$depth][$id][] = array(
					'title' => $child->title,
					'url' => $child->url,
					'classes' => $class_names,
					'id' => $child->ID,
					'col_status' => $col_status,
					'col_title' => $col_title
				);


				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			#call_user_func_array(array($this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		if($depth == 0) {
			call_user_func_array( array( $this, 'end_el' ), $cb_args );
		}

	}

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		$output .= "{$n}{$indent}<div class=\"hide\">{$n}";
	}


	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		$output .= "$indent</div>{$n}";
	}


	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;


		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';


		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$data_id = $item->ID;
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<div' . $id . $class_names .' data-id="'.$data_id.'" >';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';


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

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;


		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}


	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$output .= "</div>{$n}";
	}
}