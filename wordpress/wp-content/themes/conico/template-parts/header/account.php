<?php
/**
 * The template part for displaying a account link in header
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */

if ( function_exists( 'Basement_Header' ) ) {

	$basement_header = Basement_Header();

	$account_classes = array( 'navbar-account' );

	$logo_position = isset( $basement_header['logo_position'] ) ? $basement_header['logo_position'] : '';

	if ( $logo_position ) {
		switch ( $logo_position ) {
			case 'left' :
			case 'center_left' :
				$account_classes[] = 'pull-right';
				break;
			case 'right' :
			case 'center_right' :
				$account_classes[] = 'pull-left';
				break;
		}
	}
	?>

	<?php do_action( 'conico_before_account' ); ?>
		<?php if ( is_user_logged_in() ) { ?>
			<div class="<?php echo implode( ' ', $account_classes ); ?>">
					<?php
					$current_user = wp_get_current_user();

					$userName = !empty( $current_user->display_name ) ? $current_user->display_name : __( 'User', 'conico' );

					$link_account = '#';
					$link_class = array('user-is-auth');
					if ( conico_woo_enabled() ) {
						$link_account = esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
					} else {
						$link_class[] = 'disable-link';
					}
					?>

					<a href="<?php echo esc_attr($link_account); ?>" class="<?php echo esc_attr( implode( ' ', $link_class ) ); ?>" title="<?php _e( 'My Account', 'conico' ); ?>">
						<i class="icon-head"></i>
						<?php if ( preg_match( '/\s/', $userName ) ) {
							$separate = explode( " ", $userName );
							$last     = array_pop( $separate );
							$first    = mb_strimwidth( $separate[0], 0, 15 );
							$last = isset($last['0']) ? $last[0] : '';
							$full_name = $first . " " . $last . ".";
							echo esc_html($full_name);
						} else {
							$first = mb_strimwidth( $userName, 0, 15 );
							echo esc_html($first);
						} ?>
					</a>
			</div>
		<?php }  ?>
	<?php do_action( 'conico_after_account' ); ?>

<?php } ?>
