<?php

if( !defined( 'ABSPATH') ) exit();

class Basement_RS_Addon_Slide_Admin {
	
	protected function init() {
		
		add_filter('revslider_slide_addons', array($this, 'add_addon_settings'), 10, 3);
		
	}
	
	public function add_addon_settings($_settings, $_slide, $_slider){
		
		// only add to slide editor if enabled from slider settings first
		if($_slider->getParam(static::$_Title . '_defaults_enabled', false) == 'true') {
		
			static::_init($_slider);
			
			$_settings[static::$_Title] = array(
			
				'title'		 => __('Basement Typewriter', BASEMENT_TEXTDOMAIN),
				'markup'	 => static::$_Markup,
				'javascript' => static::$_JavaScript
			   
			);
			
		}
		
		return $_settings;
		
	}
	
}
?>