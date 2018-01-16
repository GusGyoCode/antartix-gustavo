<?php
/**
 * The template part for displaying a icon in page title
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_page_title_icon' ); ?>

	<?php
		if ( function_exists( 'basement_page_title_icon' ) ) {
			/**
			 * Displays custom icon.
			 *
			 * @package    Aisconverse
			 * @subpackage Conico
			 * @since      Conico 1.0
			 */
			basement_page_title_icon();
		}
	?>

<?php do_action( 'conico_after_page_title_icon' ); ?>