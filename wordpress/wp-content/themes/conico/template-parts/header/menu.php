<?php
/**
 * The template part for displaying a menu in header
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

if ( function_exists( 'Basement_Header' ) ) {

	$basement_header = Basement_Header();

	$menu_classes   = array( 'navbar-menu' );
	$toggle_classes = array( 'navbar-toggle-menu' );
	$logo_position  = isset( $basement_header['logo_position'] ) ? $basement_header['logo_position'] : '';
	$menu_type      = isset( $basement_header['menu_type'] ) ? $basement_header['menu_type'] : '';

	if ( $logo_position ) {
		switch ( $logo_position ) {
			case 'left' :
				$menu_classes[]   = 'pull-left';
				$toggle_classes[] = 'pull-left';
				break;
			case 'center_right' :
				$menu_classes[]   = 'pull-right';
				$toggle_classes[] = 'pull-right';
				break;
			case 'right' :
				$menu_classes[]   = 'pull-right';
				$toggle_classes[] = 'pull-right';
				break;
			case 'center_left' :
				$menu_classes[]   = 'pull-left';
				$toggle_classes[] = 'pull-left';
				break;
		}
	}

	?>

	<?php do_action( 'conico_before_menu' ); ?>

	<?php

	$conico_custom_menu = get_post_meta( get_the_ID(), '_basement_meta_custom_header', true );

	if ( ! empty( $conico_custom_menu ) ) {
		$conico_menu = get_post_meta( get_the_ID(), '_basement_meta_header_menu', true );
	} else {
		$conico_menu = get_option( 'basement_framework_menu' );
	}

	if ( 'default' == $menu_type ) {

		echo '<div class="' . implode( ' ', $toggle_classes ) . '" ><a data-backdrop="static" data-keyboard="false" href="#conico-modal-menu" data-toggle="modal"><i class="aiscon-icon aiscon-menu-non"></i></a></div>';

		wp_nav_menu( array(
			'theme_location'  => 'header',
			'menu'            => $conico_menu === 'default' ? '' : $conico_menu,
			'container'       => 'div',
			'container_class' => implode( ' ', $menu_classes ),
			'container_id'    => 'navbar',
			'menu_class'      => 'nav navbar-nav navbar-nav-menu',
			'menu_id'         => 'conico-menu',
			'echo'            => true,
			'fallback_cb'     => '__return_empty_string',
			'before'          => '',
			'after'           => '',
			'link_before'     => '<span>',
			'link_after'      => '</span>',
			'items_wrap'      => '<div class="wrapper-navbar-nav"><ul id="%1$s" class="%2$s">%3$s</ul></div>',
			'depth'           => 4,
			'walker'          => new Conico_Default_Menu()
		) );
	} else {
		echo '<div class="' . implode( ' ', $toggle_classes ) . '"><a data-backdrop="static" data-keyboard="false" href="#conico-modal-menu" data-toggle="modal"><i class="aiscon-icon aiscon-menu-non"></i></a></div>';
	}

	?>

	<?php do_action( 'conico_after_menu' ); ?>

<?php }