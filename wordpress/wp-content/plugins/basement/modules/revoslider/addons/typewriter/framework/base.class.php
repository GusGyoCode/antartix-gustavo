<?php

if(!defined('ABSPATH')) exit();

class Basement_RS_AddOn_Base {
	
	const MINIMUM_VERSION = '5.2.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}

		return false;
		
	}
	
	protected function loadClasses() {
		
		$isAdmin = is_admin();
		
		if($isAdmin) {
			
			// Add-Ons page
			add_filter('rev_addon_dash_slideouts', array($this, 'addons_page_content'));
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			// post meta box
			
			require_once(static::$_PluginPath . 'admin/includes/slider.class.php');
			require_once(static::$_PluginPath . 'admin/includes/slide.class.php');
			
			// admin init
			new Basement_RS_Typewriter_Slider_Admin(static::$_PluginTitle);
			new Basement_RS_Typewriter_Slide_Admin(static::$_PluginTitle);
			
		}
		
		add_shortcode('basement-rs-typewriter', array($this, 'post_shortcode'));
		
		/* 
		frontend scripts always enqueued for admin previews
		*/
		require_once(static::$_PluginPath . 'public/includes/slider.class.php');
		require_once(static::$_PluginPath . 'public/includes/slide.class.php');
		
		new RsTypewriterSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
		new Basement_RS_Typewriter_Slide_Front(static::$_PluginTitle);
		
	}
	
	
	
	// AddOn's page slideout panel
	public function addons_page_content() {
		
		include_once(static::$_PluginPath . 'admin/views/admin-display.php');
		
	}
	
	public function enqueue_admin_scripts($hook) {
		
		$_handle = 'basement-rs-' . static::$_PluginTitle . '-admin';
		$_base   = static::$_PluginUrl . 'admin/assets/';
		
		if($hook === 'toplevel_page_revslider' || $hook === 'slider-revolution_page_rev_addon') {
			
			if(!isset($_GET['page'])) return;
			
			$page = $_GET['page'];
			if($page !== 'revslider' && $page !== 'rev_addon') return;
			
			switch($page) {
				
				case 'revslider':
				
					if(isset($_GET['view'])) {
						
						switch($_GET['view']) {
							
							case 'slide':
							
								wp_enqueue_style($_handle, $_base . 'css/' . static::$_PluginTitle . '-slide-admin.css', array(), static::$_Version);
								wp_enqueue_script($_handle, $_base . 'js/' . static::$_PluginTitle . '-slide-admin.js', array('jquery'), static::$_Version, true);
							
							break;
							
						}
						
					}
				
				break;
				
				case 'rev_addon':
					
					wp_enqueue_style($_handle, $_base . 'css/' . static::$_PluginTitle . '-dash-admin.css', array(), static::$_Version);
					wp_enqueue_script($_handle, $_base . 'js/' . static::$_PluginTitle . '-dash-admin.js', array('jquery'), static::$_Version, true);

				break;
				
			}
			
		}
		
	}

	

	
	// Example: [basement-rs-typewriter default="{{title}}"]
	// shortcode that can be added to a post-based slide template Layer 
	// used for multiline compatibility (see meta box above)
	public function post_shortcode($atts) {
		
		extract(shortcode_atts(array('default' => ''), $atts));
		return $default;
		
	}

}
	
?>