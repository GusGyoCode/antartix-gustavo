<?php
defined('ABSPATH') or die();

global $post;

$post_id = isset($post->ID) ? $post->ID : '';

$id = BASEMENT_MODALS_TEXTDOMAIN . '_' . $post_id;

if( empty($params) || empty($post_id)) {
	return;
}

?>
<div class="basement_html_param-block" id="<?php echo $id; ?>">
	<div class="basement_html_param-body">
		<?php
		if( $params ) {
			$j = 0;
			foreach ( $params as $key_param1 => $param1 ) {
				foreach($param1['blocks'] as $key_block => $block) { ?>
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
				}
			}
		} ?>
	</div>
</div>