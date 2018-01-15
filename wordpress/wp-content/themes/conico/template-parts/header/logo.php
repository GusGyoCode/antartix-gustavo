<?php
/**
 * The template part for displaying a logo in header
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_logo' ); ?>

	<?php
		if ( function_exists( 'basement_logo' ) ) {
			/**
			 * Displays custom logo.
			 *
			 * @package    Aisconverse
			 * @subpackage Conico
			 * @since      Conico 1.0
			 */
			basement_logo();
		}
	?>

<?php do_action( 'conico_after_logo' ); ?>