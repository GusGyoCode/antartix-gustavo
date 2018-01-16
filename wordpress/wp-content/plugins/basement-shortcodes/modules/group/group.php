<?php

defined('ABSPATH') or die();

class Shortcode_Group {

	public static function all() {
		$initial_groups = apply_filters( 
			'basement_shortcodes_groups_config', 
			require dirname( __FILE__ ) . '/../../configs/groups.php'  
		);



		$groups = array();
		$shortcodes = Basement_Shortcodes::instance()->collection();



		foreach ( $shortcodes as $shortcode ) {
			$shortcode_group = $shortcode->group();
			if ( !in_array( $shortcode_group, $groups ) ) {
				if ( !empty( $initial_groups[ $shortcode_group ] ) )  {
					$shortcode_group_title = $initial_groups[ $shortcode_group ];
				} else {
					$shortcode_group_title = $shortcode_group;
				}
				$groups[ $shortcode_group ] = $shortcode_group_title;
			}
		}

		# Sort shortcodes groups
		$groups = self::sort_groups($groups);

		

		return $groups;
	}


	public static function sort_groups( $group ) {
		$order = array('basement_bootstrap','default','elements','basement_icons','basement_carousel','basement_gallery','basement_portfolio','basement_snippets');
		$ordered = array();

		if(!empty($group)) {

			foreach ($order as $key) {
				if(array_key_exists($key, $group)) {
					$ordered[ $key ] = $group[ $key ];
				}
			}
		}

		return $ordered;
	}

}