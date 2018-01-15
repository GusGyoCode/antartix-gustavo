<?php

if(!defined('ABSPATH')) exit();

require_once(REVO_TYPEWRITER_PATH . 'framework/base.class.php');

class Basement_RS_Typewriter_Base extends Basement_RS_AddOn_Base {
	
	protected static $_PluginPath    = REVO_TYPEWRITER_PATH,
					 $_PluginUrl     = REVO_TYPEWRITER_URL,
					 $_PluginTitle   = 'typewriter',
				     $_FilePath      = __FILE__,
				     $_Version       = '1.0.3';
	
	public function __construct() {
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();

		if($notice) {
			
			require_once(REVO_TYPEWRITER_PATH . 'framework/notices.class.php');
			
			new Basement_RS_AddOn_Notice($notice, static::$_PluginTitle);
			return;
			
		}
		
		parent::loadClasses();
	}
	
	// page/post meta box
	protected static function populateMetaBox($obj) {
		
		echo '<input type="hidden" name="rs_addon_typewriter_meta" value="' . implode(get_post_meta($obj->ID, 'rs-addon-typewriter')) . '" />';
		
	}

}
?>