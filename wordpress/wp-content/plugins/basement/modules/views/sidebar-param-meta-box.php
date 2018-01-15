<?php
defined('ABSPATH') or die();

global $post;

if( empty($params) ) {
	return;
}
?>

<div class="basement-meta-box">
	<div class="basement-meta-box-body">
		<label for="basement-custom-sidebar">
			<?php $custom_sidebar = get_post_meta( $post->ID, '_basement_meta_custom_sidebar', true ); ?>
			<input type="hidden" value="" name="_basement_meta_custom_sidebar" autocomplete="off">
			<input type="checkbox" id="basement-custom-sidebar" name="_basement_meta_custom_sidebar" value=".basement-sidebar-settings" <?php checked($custom_sidebar,'.basement-sidebar-settings'); ?>>
			<?php _e('Use custom settings for Sidebar?', BASEMENT_TEXTDOMAIN);?>

			<?php

			if(Basement_Ecommerce_Woocommerce::enabled()) {
				_e( '<small style="display: block;margin-top: 15px;">It\'s works everywhere except for pages: <strong>Cart</strong>, <strong>Shop</strong>, <strong>Checkout</strong> and <strong>My Account</strong>.</small>', BASEMENT_TEXTDOMAIN );
			}

			?>
		</label>
	</div>
</div>


<div class="basement-meta-box basement-meta-box-aside basement-sidebar-settings">
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
