<?php
/**
 * The template part for displaying a breadcrumb in page title
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_breadcrumbs' ); ?>

<?php if ( function_exists( 'bcn_display' ) ) : ?>

	<?php echo '<div class="breadcrumb-block"><ol class="breadcrumb">' . bcn_display_list( true ) . '</ol></div>'; ?>

<?php endif; ?>

<?php do_action( 'conico_after_breadcrumbs' ); ?>