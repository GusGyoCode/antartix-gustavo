<?php
/**
 * The template part for displaying a search in header
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

if ( function_exists( 'Basement_Header' ) ) {

	$basement_header = Basement_Header();

	$search_classes = array( 'navbar-search' );

	$logo_position = isset( $basement_header['logo_position'] ) ? $basement_header['logo_position'] : '';

	if ( $logo_position ) {
		switch ( $logo_position ) {
			case 'left' :
			case 'center_left' :
				$search_classes[] = 'pull-right';
				break;
			case 'right' :
			case 'center_right' :
				$search_classes[] = 'pull-left';
				break;
		}
	}
	?>

	<?php do_action( 'conico_before_search' ); ?>

	<div class="<?php echo implode( ' ', $search_classes ); ?>" role="search">
		<a href="#conico-modal-search" data-backdrop="static" data-keyboard="false" data-toggle="modal" title="<?php _e('Open Search Modal Window','conico'); ?>"><i class="icon-search"></i></a>
	</div>

	<?php do_action( 'conico_after_search' ); ?>

<?php } ?>
