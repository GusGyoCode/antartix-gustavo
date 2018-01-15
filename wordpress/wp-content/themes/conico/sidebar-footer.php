<?php
/**
 * The sidebar containing the footer widget area
 *
 * Displays on posts, pages and woo.
 *
 * If no active widgets are in this sidebar, hide it completely.
 *
 * @package Aisconverse
 * @subpackage Conico
 * @since Conico 1.0
 */

global $basement_footer;

if ( is_active_sidebar( $basement_footer['sidebar'] ) ) : ?>
	<div class="container">
		<div class="row footer-widget-row">
			<?php dynamic_sidebar( $basement_footer['sidebar'] ); ?>
		</div>
	</div>
<?php endif; ?>