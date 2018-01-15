<?php

if( !defined( 'ABSPATH') ) exit();

require_once(REVO_TYPEWRITER_PATH . 'framework/slider.admin.class.php');

class Basement_RS_Typewriter_Slider_Admin extends Basement_RS_Addon_Slider_Admin {
	
	protected static $_Icon,
					 $_Title,
					 $_Markup,
					 $_JavaScript;
	
	public function __construct($_title) {
		
		static::$_Title = $_title;
		parent::init();
		
	}
	
	protected static function _init($_slider) {
		
		$_enabled  = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_enabled', false) == 'true' ? ' checked' : '';
		$_cursor   = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_cursor_type', 'one');
		
		$_blinking = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_blinking', 'default');
		$_blinking = $_blinking !== 'default' ? $_blinking == 'true' ? ' checked' : '' : ' checked';
		
		if(RevSliderFunctions::getVal($_slider, 'typewriter_defaults_cursor_type', 'one') === 'one') {
			
			$_cursorOne = ' selected';
			$_cursorTwo = '';
			
		}
		else {
			
			$_cursorOne = '';
			$_cursorTwo = ' selected';
			
		}
		
		$_delays         = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_delays', '');
		$_speed          = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_speed', '30');
		$_startDelay     = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_start_delay', '1000');
		$_deletionSpeed  = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_deletion_speed', '20');
		$_blinkingSpeed  = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_blinking_speed', '500');
		$_linebreakDelay = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_linebreak_delay', '60');
		$_newlineDelay   = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_newline_delay', '1000');
		$_deletionDelay  = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_deletion_delay', '1000');
		
		$_looped     = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_looped', false) == 'true' ? ' checked' : '';
		$_sequenced  = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_sequenced', false) == 'true' ? ' checked' : '';
		$_wordDelay  = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_word_delay', false) == 'true' ? ' checked' : '';
		$_hideCursor = RevSliderFunctions::getVal($_slider, 'typewriter_defaults_hide_cursor', false) == 'true' ? ' checked' : '';

		$_showSettings          = $_enabled ? 'block' : 'none';
		$_showBlinkSettings     = $_blinking === ' checked' ? 'block' : 'none';
		$_showSequenceSettings  = $_sequenced === ' checked' ? 'block' : 'none';
		$_showWordDelaySettings = $_wordDelay === ' checked' ? 'block' : 'none';
		
		$_delaysArray   = array('1|100', '2|100', '3|100', '1|250', '2|250', '3|250', '1|500', '2|500', '3|500');
		$_delayPatterns = '';
		
		foreach($_delaysArray as $_delay) {
			
			$_isChecked = $_delay !== $_delays ? '' : ' selected';
			$_delayPatterns .= '<option value="' . $_delay . '"' . $_isChecked . '>' . str_replace('|', ', ', $_delay) . '</option>';
			
		}
		
		static::$_Markup = 
		
		'<span class="label" id="label_typewriter_defaults_enabled" origtitle="' . __("Enable/Disable Typewriter for this Slider", BASEMENT_TEXTDOMAIN) . '">' . __('Use Typewriter', BASEMENT_TEXTDOMAIN) . '</span>
		<input type="checkbox" class="tp-moderncheckbox withlabel" id="typewriter_defaults_enabled" name="typewriter_defaults_enabled"' . $_enabled . ' 
			onchange="document.getElementById(\'typewriter-default-settings\').style.display=this.checked ? \'block\' : \'none\'" />
		
		<div id="typewriter-default-settings" style="display: ' . $_showSettings . '">
		
			<h4>Default Settings</h4>
			
			<span class="label" id="label_typewriter_defaults_blinking" origtitle="' . __('Use a Blinking Cursor to simulate the typing effect', BASEMENT_TEXTDOMAIN) . '">' . __("Blinking Cursor", BASEMENT_TEXTDOMAIN) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="typewriter_defaults_blinking" name="typewriter_defaults_blinking"' . $_blinking . ' 
				onchange="document.getElementById(\'typewriter-blinking-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="typewriter-blinking-settings" class="withsublabels" style="display: ' . $_showBlinkSettings . '">
			
			<span class="label" id="label_typewriter_defaults_cursor_type" origtitle="' . __('Use an &quot;underscore&quot; or &quot;vertical bar&quot; for the Blinking Cursor', BASEMENT_TEXTDOMAIN) . '">' . __("Cursor Type", BASEMENT_TEXTDOMAIN) . '</span>
				<select class="withlabel" id="typewriter_defaults_cursor_type" name="typewriter_defaults_cursor_type">
					<option value="one"' . $_cursorOne . '>' . __('Text',   BASEMENT_TEXTDOMAIN) . ' _</option>
					<option value="two"' . $_cursorTwo . '>' . __('Text',   BASEMENT_TEXTDOMAIN) . ' |</option>
				</select>
				<br>
				
			
				<span class="label" id="label_typewriter_defaults_blinking_speed" origtitle="' . __('Blinking Speed for the Cursor (in milliseconds)', BASEMENT_TEXTDOMAIN) . '">' . __("Blinking Speed", BASEMENT_TEXTDOMAIN) . '</span>
				<input type="text" class="text-sidebar withlabel" id="typewriter_defaults_blinking_speed" name="typewriter_defaults_blinking_speed" value="' . $_blinkingSpeed . '" />
				<span>ms</span>
				<br>
				
				
				<span class="label" id="label_typewriter_defaults_hide_cursor" origtitle="' . __('Hide the Blinking Cursor when typing has completed for the Layer', BASEMENT_TEXTDOMAIN) . '">' . __("Stop when Complete", BASEMENT_TEXTDOMAIN) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="typewriter_defaults_hide_cursor" name="typewriter_defaults_hide_cursor"' . $_hideCursor . ' />
			
			</div>
			
			<span class="label" id="label_typewriter_defaults_sequenced" origtitle="' . __('Allow for multiple/sequenced lines', BASEMENT_TEXTDOMAIN) . '">' . __("Sequenced Lines", BASEMENT_TEXTDOMAIN) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="typewriter_defaults_sequenced" name="typewriter_defaults_sequenced"' . $_sequenced . ' 
				onchange="document.getElementById(\'typewriter-sequence-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="typewriter-sequence-settings" class="withsublabels" style="display: ' . $_showSequenceSettings . '">
			
				<span class="label" id="label_typewriter_defaults_deletion_speed" origtitle="' . __('Character deletion speed (in milliseconds)', BASEMENT_TEXTDOMAIN) . '">' . __("Deletion Speed", BASEMENT_TEXTDOMAIN) . '</span>
				<input type="text" class="text-sidebar withlabel" id="typewriter_defaults_deletion_speed" name="typewriter_defaults_deletion_speed" value="' . $_deletionSpeed . '" />
				<span>ms</span>
				<br>
				
				<span class="label" id="label_typewriter_defaults_deletion_delay" origtitle="' . __('Time before the current line begins to erase itself (in milliseconds)', BASEMENT_TEXTDOMAIN) . '">' . __("Deletion Delay", BASEMENT_TEXTDOMAIN) . '</span>
				<input type="text" class="text-sidebar withlabel" id="typewriter_defaults_deletion_delay" name="typewriter_defaults_deletion_delay" value="' . $_deletionDelay . '" />
				<span>ms</span>
				<br>
				
				<span class="label" id="label_typewriter_defaults_newline_delay" origtitle="' . __('Time before the next line is shown (in milliseconds) once the previous line has been erased', BASEMENT_TEXTDOMAIN) . '">' . __("New Line Delay", BASEMENT_TEXTDOMAIN) . '</span>
				<input type="text" class="text-sidebar withlabel" id="typewriter_defaults_newline_delay" name="typewriter_defaults_newline_delay" value="' . $_newlineDelay . '" />
				<span>ms</span>
				<br>
				
				<span class="label" id="label_typewriter_defaults_looped" origtitle="' . __('Restart the sequence after the last line has been shown', BASEMENT_TEXTDOMAIN) . '">' . __("Constant Loop", BASEMENT_TEXTDOMAIN) . '</span>
				<input type="checkbox" class="tp-moderncheckbox withlabel" id="typewriter_defaults_looped" name="typewriter_defaults_looped"' . $_looped . ' />
			
			</div>
			
			
			<span class="label" id="label_typewriter_defaults_start_delay" origtitle="' . __('Time before the typing officially starts (in milliseconds) after the Layer first appears', BASEMENT_TEXTDOMAIN) . '">' . __("Start Delay", BASEMENT_TEXTDOMAIN) . '</span>
			<input type="text" class="text-sidebar withlabel" id="typewriter_defaults_start_delay" name="typewriter_defaults_start_delay" value="' . $_startDelay . '" />
			<span>ms</span>
			<br>
			
			<span class="label" id="label_typewriter_defaults_speed" origtitle="' . __('Time in which each character is &quot;typed&quot; (in milliseconds)', BASEMENT_TEXTDOMAIN) . '">' . __("Typing Speed", BASEMENT_TEXTDOMAIN) . '</span>
			<input type="text" class="text-sidebar withlabel" id="typewriter_defaults_speed" name="typewriter_defaults_speed" value="' . $_speed . '" />
			<span>ms</span>
			<br>
			
			
			
			<span class="label" id="label_typewriter_defaults_word_delay" origtitle="' . __('Add delays between words to further simulate real-time typing', BASEMENT_TEXTDOMAIN) . '">' . __("Word Delays", BASEMENT_TEXTDOMAIN) . '</span>
			<input type="checkbox" class="tp-moderncheckbox withlabel" id="typewriter_defaults_word_delay" name="typewriter_defaults_word_delay"' . $_wordDelay . ' 
				onchange="document.getElementById(\'typewriter-word-delay-settings\').style.display=this.checked ? \'block\' : \'none\'" />
			
			<div id="typewriter-word-delay-settings" class="withsublabels" style="display: ' . $_showWordDelaySettings . '">
			
				<span class="label" id="label_typewriter_defaults_delays" origtitle="' . __('For every (x) words, delay by (x) milliseconds', BASEMENT_TEXTDOMAIN) . '">' . __("Word Delay Pattern", BASEMENT_TEXTDOMAIN) . '</span>
				<select class="withlabel" id="typewriter_defaults_delays" name="typewriter_defaults_delays">' . $_delayPatterns . '</select>
				<br>
			
			</div>
			
			<span class="label" id="label_typewriter_defaults_linebreak_delay" origtitle="' . __("Add a delay for HTML <br> tags that are included with the Layer&#39;s text", BASEMENT_TEXTDOMAIN) . '">' . __("Line Break Delay", BASEMENT_TEXTDOMAIN) . '</span>
			<input type="text" class="text-sidebar withlabel" id="typewriter_defaults_linebreak_delay" name="typewriter_defaults_linebreak_delay" value="' . $_linebreakDelay . '" />
			<span>ms</span>
			
		</div>';
		
		static::$_Icon = 'eg-icon-indent-right';
		static::$_JavaScript = '';
		
	}
}
?>