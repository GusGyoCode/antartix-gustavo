<?php
defined('ABSPATH') or die();

global $post;

if( empty($params) ) {
	return;
}
?>

<div class="basement-meta-box">
	<div class="basement-meta-box-body">
		<label for="basement-custom-pagetitle">
			<?php $custom_pagetitle = get_post_meta( $post->ID, '_basement_meta_custom_pagetitle', true ); ?>
			<input type="hidden" value="" name="_basement_meta_custom_pagetitle" autocomplete="off">
			<input type="checkbox" id="basement-custom-pagetitle" name="_basement_meta_custom_pagetitle" value=".basement-pagetitle-settings" <?php checked($custom_pagetitle,'.basement-pagetitle-settings'); ?>>
			<?php _e('Use custom settings for Page Title?', BASEMENT_TEXTDOMAIN);?>
		</label>
	</div>
</div>


<div class="basement-meta-box basement-pagetitle-settings">
	<div class="basement-meta-box-body">
		<?php
		if( $params ) {
			foreach ( $params as $key => $value ) {
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
