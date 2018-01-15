<?php
/**
 * The template part for displaying a button in header
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

if ( function_exists( 'Basement_Header' ) ) {

	$basement_header = Basement_Header();

	$button_classes = array( 'navbar-button' );

	$logo_position = isset( $basement_header['logo_position'] ) ? $basement_header['logo_position'] : '';

	if ( $logo_position ) {
		switch ( $logo_position ) {
			case 'left' :
			case 'center_left' :
				$button_classes[] = 'pull-right';
				break;
			case 'right' :
			case 'center_right' :
				$button_classes[] = 'pull-left';
				break;
		}
	}
	?>

	<?php do_action( 'conico_before_button' ); ?>

	<div class="<?php echo implode( ' ', $button_classes ); ?>">
		<?php
			if ( function_exists( 'basement_button' ) ) {
				/**
				 * Displays custom button.
				 *
				 * @package    Aisconverse
				 * @subpackage Conico
				 * @since      Conico 1.0
				 */
				basement_button();
			}
		?>
	</div>

	<?php do_action( 'conico_after_button' ); ?>

<?php } ?>
