<?php
defined('ABSPATH') or die();

global $post;

if( empty($params) ) {
	return;
}
?>

<div class="basement-meta-box">
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
