<?php
/**
 * The template part for displaying a alternative title in page title
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_title_alternative' ); ?>

<?php if ( function_exists( 'basement_the_title_alternative' ) ) {
	echo '<h1 class="main-page-title main-page-title-alternative"><span>' . basement_the_title_alternative( true ) . '</span></h1>';
} ?>

<?php do_action( 'conico_after_title_alternative' ); ?>
