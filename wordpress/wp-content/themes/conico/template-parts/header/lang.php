<?php
/**
 * The template part for displaying a language dropdown in header
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<?php do_action( 'conico_before_lang' ); ?>

<?php if ( conico_wpml_enabled() ) : ?>

	<?php
		if ( function_exists( 'conico_language_switcher' ) ) {
			/**
			 * Displays custom WPML language switcher.
			 *
			 * @package    Aisconverse
			 * @subpackage Conico
			 * @since      Conico 1.0
			 */
			conico_language_switcher();
		}
	?>

<?php endif; ?>

<?php do_action( 'conico_after_lang' ); ?>