<?php
defined('ABSPATH') or die();

global $post;

if( empty($params) ) {
	return;
}
?>

<div class="basement-meta-box">
	<div class="basement-meta-box-body">
		<label for="basement-custom-footer">
			<?php $custom_footer = get_post_meta( $post->ID, '_basement_meta_custom_footer', true ); ?>
			<input type="hidden" value="" name="_basement_meta_custom_footer" autocomplete="off">
			<input type="checkbox" id="basement-custom-footer" name="_basement_meta_custom_footer" value=".basement-footer-settings" <?php checked($custom_footer,'.basement-footer-settings'); ?>>
			<?php _e('Use custom settings for Footer?', BASEMENT_TEXTDOMAIN);?>
		</label>
	</div>
</div>


<div class="basement-meta-box basement-meta-box-aside basement-footer-settings">
	<div class="basement-meta-box-body">
		<?php
		if( $params ) {
			foreach ( $params as $key => $value ) {
				?>
				<div class="basement-meta-box-setting cf">
					<?php if(!empty($value['title']) || !empty($value['description'])) { ?>
					<div class="basement-meta-box-info-setting">
						<h3><?php echo $value['title']; ?></h3>
						<i><?php echo $value['description']; ?></i>
					</div>
					<?php } ?>
					<div class="basement-meta-box-action-setting">
						<?php echo $value['input']; ?>
					</div>
				</div>
				<?php
			}
		} ?>
	</div>
</div>
