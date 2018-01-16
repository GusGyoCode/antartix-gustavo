<?php
class Basement_Horizontal_List_Widget extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname' => 'widget_list',
			'description' => __('A simple list.', BASEMENT_TEXTDOMAIN),
			'customize_selective_refresh' => true
		);
		parent::__construct( 'lists', __( 'Horizontal List', BASEMENT_TEXTDOMAIN ), $widget_ops );

		add_action('admin_enqueue_scripts', array($this,'sllw_load_scripts'));
	}

	public function widget( $args, $instance ) {
		extract($args);

		$title = $before_title . apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Horizontal List', BASEMENT_TEXTDOMAIN ) : $instance['title'], $instance, $this->id_base ) . $after_title;
		$type = empty($instance['type']) ? 'unordered' : $instance['type'] ;
		$reverse = isset($instance['reverse']) ? $instance['reverse'] : false;
		$amount = empty($instance['amount']) ? 3 : $instance['amount'];
		$align = empty($instance['align']) ? 'left' : $instance['align'] ;

		for ($i = 1; $i <= $amount; $i++) {
			$items[$i-1] = $instance['item'.$i];
			$item_texts[$i-1] = $instance['item_text'.$i];
			$item_icons[$i-1] = $instance['item_icon'.$i];

			$item_contents[$i-1] = $instance['item_content'.$i];

			$item_links[$i-1] = $instance['item_link'.$i];

			$item_link_texts[$i-1] = $instance['item_link_text'.$i];

			$item_classes[$i-1] = $instance['item_class'.$i];
			$item_targets[$i-1] = isset($instance['item_target'.$i]) ? $instance['item_target'.$i] : false;
		}

		if($reverse){
			$items = array_reverse($items);
			$item_texts = array_reverse($item_texts);
			$item_icons = array_reverse($item_icons);
			$item_contents = array_reverse($item_contents);
			$item_links = array_reverse($item_links);

			$item_link_texts = array_reverse($item_link_texts);

			$item_classes = array_reverse($item_classes);
			$item_targets = array_reverse($item_targets);
		}

		echo $before_widget . $title;
		if ($type == "ordered") { echo "<div class='table-widget'><table ";} else { echo("<div class='table-widget'><table "); } ?>>

		<?php $i = 0; foreach ($items as $num => $item) {


			if ( $i !== 0 ) {

				$targes = '';

				if ( $item_targets[ $num ] ) {
					$targes = 'target="_blank"';
				}

				echo '<tr class="'.esc_attr($item_classes[ $num ]).'">';

				$icn = '';
				if(!empty($item_icons[$num])) {
					$class_i = esc_attr($item_icons[$num]);
					$icn = '<i class="widget-list-icon '.$class_i.'"></i>';
				}

					echo '<td>'.$icn.esc_html($item).'</td>';

					echo sprintf('<td>%1$s%2$s%3$s</td>',
						!empty($item_texts[$num]) ? '<div class="onerow-text">'.esc_html($item_texts[$num]).'</div>' : '',
						!empty($item_contents[$num]) ? '<div>'.nl2br($item_contents[$num]).'</div>' : '',
						!empty($item_links[$num]) ? '<div><a href="'.$item_links[$num].'" '.$targes.' title="">'.esc_html($item_link_texts[$num]).'</a></div>' : ''
					);

				echo '</tr>';
			}
			$i++;
		}

		if ($type == "ordered") { echo "</table></div>";} else { echo("</table></div>"); }

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance) {
		//$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$amount = $new_instance['amount'];
		$new_item = empty($new_instance['new_item']) ? false : strip_tags($new_instance['new_item']);

		if ( isset($new_instance['position1'])) {
			for($i=1; $i<= $new_instance['amount']; $i++){
				if($new_instance['position'.$i] != -1){
					$position[$i] = $new_instance['position'.$i];
				}else{
					$amount--;
				}
			}
			if($position){
				asort($position);
				$order = array_keys($position);
				if(strip_tags($new_instance['new_item'])){
					$amount++;
					array_push($order, $amount);
				}
			}

		}else{
			$order = explode(',',$new_instance['order']);
			foreach($order as $key => $order_str){
				$num = strrpos($order_str,'-');
				if($num !== false){
					$order[$key] = substr($order_str,$num+1);
				}
			}
		}

		if($order){
			foreach ($order as $i => $item_num) {
				$instance['item'.($i+1)] = empty($new_instance['item'.$item_num]) ? '' : strip_tags($new_instance['item'.$item_num]);

				$instance['item_text'.($i+1)] = empty($new_instance['item_text'.$item_num]) ? '' : strip_tags($new_instance['item_text'.$item_num]);
				$instance['item_icon'.($i+1)] = empty($new_instance['item_icon'.$item_num]) ? '' : strip_tags($new_instance['item_icon'.$item_num]);
				$instance['item_content'.($i+1)] = empty($new_instance['item_content'.$item_num]) ? '' : strip_tags($new_instance['item_content'.$item_num]);
				$instance['item_link'.($i+1)] = empty($new_instance['item_link'.$item_num]) ? '' : strip_tags($new_instance['item_link'.$item_num]);

				$instance['item_link_text'.($i+1)] = empty($new_instance['item_link_text'.$item_num]) ? '' : strip_tags($new_instance['item_link_text'.$item_num]);

				$instance['item_class'.($i+1)] = empty($new_instance['item_class'.$item_num]) ? '' : strip_tags($new_instance['item_class'.$item_num]);
				$instance['item_target'.($i+1)] = empty($new_instance['item_target'.$item_num]) ? '' : strip_tags($new_instance['item_target'.$item_num]);
			}
		}

		$instance['amount'] = $amount;
		$instance['type'] = strip_tags($new_instance['type']);
		$instance['align'] = $new_instance['align'];
		$instance['reverse'] = empty($new_instance['reverse']) ? '' : strip_tags($new_instance['reverse']);

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'title_link' => '' ) );
		$title = strip_tags($instance['title']);
		$amount = empty($instance['amount']) ? 1 : $instance['amount'];

		for ($i = 1; $i <= $amount; $i++) {
			$items[$i] = empty($instance['item'.$i]) ? '' : $instance['item'.$i];
			$item_texts[$i] = empty($instance['item_text'.$i]) ? '' : $instance['item_text'.$i];
			$item_icons[$i] = empty($instance['item_icon'.$i]) ? '' : $instance['item_icon'.$i];

			$item_contents[$i] = empty($instance['item_content'.$i]) ? '' : $instance['item_content'.$i];

			$item_link_texts[$i] = empty($instance['item_link_text'.$i]) ? '' : $instance['item_link_text'.$i];

			$item_links[$i] = empty($instance['item_link'.$i]) ? '' : $instance['item_link'.$i];
			$item_classes[$i] = empty($instance['item_class'.$i]) ? '' : $instance['item_class'.$i];
			$item_targets[$i] = empty($instance['item_target'.$i]) ? '' : $instance['item_target'.$i];
		}
		$title_link = $instance['title_link'];
		$type = empty($instance['type']) ? 'unordered' : $instance['type'] ;
		$reverse = empty($instance['reverse']) ? '' : $instance['reverse'];
		$align = empty($instance['align']) ? 'left' : $instance['align'] ;

		?>
		<div class="wrapper-widget">
			<div class="wrapper-widget-load"></div>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BASEMENT_TEXTDOMAIN); ?></label>
			<input class="widefat " id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<div class="simple-link-list">
			<?php $i = 0; foreach ($items as $num => $item) {
				$item = esc_attr($item);
				$item_text = esc_attr($item_texts[$num]);
				$item_icon = esc_attr($item_icons[$num]);

				$item_content = $item_contents[$num];

				$item_link = esc_attr($item_links[$num]);
				$item_link_text = esc_attr($item_link_texts[$num]);

				$item_class = esc_attr($item_classes[$num]);
				$checked = checked($item_targets[$num], 'on', false);
				?>

				<div id="<?php echo $this->get_field_id($num); ?>" class="list-item <?php if($i === 0) { echo 'template'; $i++; } ?>">
					<h5 class="moving-handle"><span class="number"><?php _e('Item:', BASEMENT_TEXTDOMAIN); ?></span> <span class="item-title"><?php echo $item; ?></span><a class="sllw-action hide-if-no-js"></a></h5>
					<div class="sllw-edit-item">
						<fieldset class="widget-fieldset" style="margin-top:10px;">
							<legend><?php _e('First column', BASEMENT_TEXTDOMAIN); ?></legend>
							<label for="<?php echo $this->get_field_id('item_icon'.$num); ?>"><?php echo __("Icon:", BASEMENT_TEXTDOMAIN); ?></label>

							<div style="position: relative;margin-right: 25px;padding-top: 6px;">
								<input class="widefat" style="margin-bottom: 5px;" id="<?php echo $this->get_field_id('item_icon'.$num); ?>" name="<?php echo $this->get_field_name('item_icon'.$num); ?>" type="text" value="<?php echo $item_icon; ?>" placeholder="<?php _e('e.g.: fa fa-skype', BASEMENT_TEXTDOMAIN) ?>" />
								<a class="basement-help-popup" href="#" title="">?</a>
							</div>

							<small style="display: block;margin-bottom: 7px;"><?php _e('Use only class names from icons library for this field.', BASEMENT_TEXTDOMAIN); ?></small>
							<label for="<?php echo $this->get_field_id('item'.$num); ?>"><?php echo __("Text:", BASEMENT_TEXTDOMAIN); ?></label>
							<input class="widefat text-coper" id="<?php echo $this->get_field_id('item'.$num); ?>" name="<?php echo $this->get_field_name('item'.$num); ?>" type="text" value="<?php echo $item; ?>" />
						</fieldset>
						<fieldset class="widget-fieldset">
							<legend><?php _e('Second column', BASEMENT_TEXTDOMAIN); ?></legend>
							<label for="<?php echo $this->get_field_id('item_text'.$num); ?>"><?php echo __("Text:", BASEMENT_TEXTDOMAIN); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id('item_text'.$num); ?>" name="<?php echo $this->get_field_name('item_text'.$num); ?>" type="text" value="<?php echo $item_text; ?>" />
							<label for="<?php echo $this->get_field_id( 'item_content'.$num ); ?>"><?php _e( 'Content:', BASEMENT_TEXTDOMAIN ); ?></label>
							<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('item_content'.$num); ?>" name="<?php echo $this->get_field_name('item_content'.$num); ?>"><?php echo esc_textarea( $item_content ); ?></textarea>
							<label for="<?php echo $this->get_field_id('item_link_text'.$num); ?>"><?php echo __("Link title:", BASEMENT_TEXTDOMAIN); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id('item_link_text'.$num); ?>" name="<?php echo $this->get_field_name('item_link_text'.$num); ?>" type="text" value="<?php echo $item_link_text; ?>" />
							<label for="<?php echo $this->get_field_id('item_link'.$num); ?>"><?php echo __("Link:", BASEMENT_TEXTDOMAIN); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id('item_link'.$num); ?>" name="<?php echo $this->get_field_name('item_link'.$num); ?>" type="text" value="<?php echo $item_link; ?>" />
							<label style="margin-top: 10px;display: block;margin-bottom: 18px;" for="<?php echo $this->get_field_id('item_target'.$num); ?>"><input type="checkbox" name="<?php echo $this->get_field_name('item_target'.$num); ?>" id="<?php echo $this->get_field_id('item_target'.$num); ?>" <?php echo $checked; ?> /> <?php echo __("Open in new window?", BASEMENT_TEXTDOMAIN); ?></label>
						</fieldset>
						<label for="<?php echo $this->get_field_id('item_class'.$num); ?>"><?php echo __("Custom Style Class:", BASEMENT_TEXTDOMAIN); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('item_class'.$num); ?>" name="<?php echo $this->get_field_name('item_class'.$num); ?>" type="text" value="<?php echo $item_class; ?>" />
						<a class="sllw-delete hide-if-no-js"><?php echo __("Remove", BASEMENT_TEXTDOMAIN); ?></a>
					</div>
				</div>

			<?php }

			if ( isset($_GET['editwidget']) && $_GET['editwidget'] ) : ?>
				<table class='widefat'>
					<thead><tr><th><?php echo __("Item", BASEMENT_TEXTDOMAIN); ?></th><th><?php echo __("Position/Action", BASEMENT_TEXTDOMAIN); ?></th></tr></thead>
					<tbody>
					<?php foreach ($items as $num => $item) : ?>
						<tr>
							<td><?php echo esc_attr($item); ?></td>
							<td>
								<select id="<?php echo $this->get_field_id('position'.$num); ?>" name="<?php echo $this->get_field_name('position'.$num); ?>">
									<option><?php echo __('&mdash; Select &mdash;', BASEMENT_TEXTDOMAIN); ?></option>
									<?php for($i=1; $i<=count($items); $i++) {
										if($i==$num){
											echo "<option value='$i' selected>$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									} ?>
									<option value="-1"><?php echo __("Delete", BASEMENT_TEXTDOMAIN); ?></option>
								</select>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<div class="sllw-row">
					<input type="checkbox" name="<?php echo $this->get_field_name('new_item'); ?>" id="<?php echo $this->get_field_id('new_item'); ?>" /> <label for="<?php echo $this->get_field_id('new_item'); ?>"><?php echo __("Add New Item"); ?></label>
				</div>
			<?php endif; ?>

		</div>
		<div class="sllw-row hide-if-no-js">
			<a class="sllw-add button-secondary"><?php echo __("Add Item", BASEMENT_TEXTDOMAIN); ?></a>
		</div>

		<input type="hidden" id="<?php echo $this->get_field_id('amount'); ?>" class="amount" name="<?php echo $this->get_field_name('amount'); ?>" value="<?php echo $amount ?>" />
		<input type="hidden" id="<?php echo $this->get_field_id('order'); ?>" class="order" name="<?php echo $this->get_field_name('order'); ?>" value="<?php echo implode(',',range(1,$amount)); ?>" />

		</div>
		<?php
	}

	public function sllw_load_scripts($hook) {
		if( $hook != 'widgets.php')
			return;
		if ( !isset($_GET['editwidget'])) {
			wp_enqueue_script( 'sllw-sort-js', Basement::url() . '/assets/javascript/widget-sort.min.js');
		}
	}
}

?>