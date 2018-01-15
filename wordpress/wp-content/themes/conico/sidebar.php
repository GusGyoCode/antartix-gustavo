<?php
/**
 * The sidebar containing the main widget area
 *
 * Displays on posts, pages and woo.
 *
 * If no active widgets are in this sidebar, hide it completely.
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

global $basement_sidebar;

if ( is_active_sidebar( $basement_sidebar['sidebar'] ) ) : ?>
	<aside <?php basement_sidebar_classes( 'sidebar' ); ?> role="complementary">
		<div class="sidebar-body">
			<?php dynamic_sidebar( $basement_sidebar['sidebar'] ); ?>
		</div>
	</aside>
<?php endif; ?>