<?php
defined('ABSPATH') or die();

global $post;

if( empty($params) ) {
	return;
}
?>
<div class="basement_html_param-block" id="<?php echo BASEMENT_PORTFOLIO_TEXTDOMAIN . '_' . $post->ID; ?>">
	<div class="basement_html_param-body">
		<?php
		if( $params ) {
			$j = 0;
			foreach ( $params as $key_param1 => $param1 ) {
				foreach($param1['blocks'] as $key_block => $block) {
					if ($key_block !== 'image_gallery_type' && $key_block !== 'video_gallery_type') { ?>
						<div class="basement_html_param-setting cf">
							<div class="basement_html_param-info-setting">
								<h3><?php echo $block['title']; ?></h3>
								<i><?php echo $block['description']; ?></i>
							</div>
							<div class="basement_html_param-action-setting">
								<?php echo $block['input']; ?>
							</div>
						</div>
						<?php
					} else { ?>
						<div class="basement_html_param-setting cf" id="<?php echo $key_block; ?>">
							<?php foreach ($block as $key_inner => $inner_block) { ?>
								<div class="basement_html_param-setting cf">
									<div class="basement_html_param-info-setting">
										<h3><?php echo $inner_block['title']; ?></h3>
										<i><?php echo $inner_block['description']; ?></i>
									</div>
									<div class="basement_html_param-action-setting">
										<?php echo $inner_block['input']; ?>
									</div>
								</div>
							<?php } ?>
						</div>
					<?php }
				}
			}
		} ?>
	</div>
</div>