<?php
/**
 * The template part for displaying a simple menu in header
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_simple_menu' ); ?>

<?php

$conico_custom_menu = get_post_meta( get_the_ID(), '_basement_meta_custom_header', true );

if ( ! empty( $conico_custom_menu ) ) {
	$conico_menu = get_post_meta( get_the_ID(), '_basement_meta_header_menu', true );
} else {
	$conico_menu = get_option( 'basement_framework_menu' );
}

?>

<div class="modal fade conico-modal-menu" id="conico-modal-menu" tabindex="-1" role="dialog">

    <div class="menu-simple-controls">
        <a href="#" class="simple-menu-back fade"><ins class="prev-lvl"></ins><i class="ais-b1l arrow-lvl"></i></a><ins class="current-lvl"><?php _e('01', 'conico'); ?></ins>
    </div>


    <a href="#" class="simple-menu-close" data-dismiss="modal" aria-label="Close" title=""><i class="ais-close"></i></a>
	<div class="modal-dialog" role="document">

		<div class="modal-content">
			<div class="simple-menu-pages">
				<?php
					wp_nav_menu( array(
						'theme_location'  => 'header',
						'menu'            => $conico_menu === 'default' ? '' : $conico_menu,
						'container'       => false,
						'echo'            => true,
						'menu_class'      => 'simple-menu-element simple-menu-nav',
						'menu_id'         => 'simple-menu-root',
						'fallback_cb'     => '__return_empty_string',
						'items_wrap'      => '<div id="%1$s" class="%2$s" data-depth="root">%3$s</div>',
						'depth'           => 4,
						'walker'          => new Conico_Simple_Menu()
					) );
				?>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'conico_after_simple_menu' ); ?>
