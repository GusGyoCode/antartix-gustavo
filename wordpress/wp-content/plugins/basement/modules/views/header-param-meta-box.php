<?php
defined('ABSPATH') or die();

global $post;

if( empty($params) ) {
	return;
}
?>

<div class="basement-meta-box">
	<div class="basement-meta-box-body">
		<label for="basement-custom-header">
			<?php $custom_header = get_post_meta( $post->ID, '_basement_meta_custom_header', true ); ?>
			<input type="hidden" value="" name="_basement_meta_custom_header" autocomplete="off">
			<input type="checkbox" id="basement-custom-header" name="_basement_meta_custom_header" value=".basement-header-settings" <?php checked($custom_header,'.basement-header-settings'); ?>>
			<?php _e('Use custom settings for Header?', BASEMENT_TEXTDOMAIN);?>
		</label>
		<!-- <a href="#" id="basement_export_header_params" style="opacity: 0;cursor: default;" title="">export</a> -->
	</div>
</div>


<div class="basement-meta-box basement-header-settings">
	<div class="basement-meta-box-body">
		<?php
		if( $params ) {
			foreach ( $params as $key => $value ) {
				if(
					$value['key'] !== 'menu' &&
					$value['key'] !== 'menu_type' &&
					$value['key'] !== 'header_off' &&
					$value['key'] !== 'header_helper' &&
					$value['key'] !== 'header_elements' &&
					$value['key'] !== 'header_style' &&
					$value['key'] !== 'position_logo' &&
					$value['key'] !== 'header_bg' &&
					$value['key'] !== 'header_border' &&
					$value['key'] !== 'header_padding' &&
					$value['key'] !== 'header_button' &&
					$value['key'] !== 'header_size' &&
					$value['key'] !== 'header_global_border' &&
					$value['key'] !== 'header_global_border_size' &&
					$value['key'] !== 'header_global_border_color'
				)
					continue;
				 ?>
					<div class="basement-meta-box-setting cf">
						<div class="basement-meta-box-info-setting">
							<h3><?php echo $value['title']; ?></h3>
							<i><?php echo $value['description']; ?></i>
						</div>
						<div class="basement-meta-box-action-setting">
							<?php echo $value['input']; ?>
						</div>
					</div>
				<?php
			}
		} ?>
	</div>
</div>
