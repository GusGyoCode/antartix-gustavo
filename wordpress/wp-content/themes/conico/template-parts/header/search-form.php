<?php
/**
 * The template part for displaying a search modal in header
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<div class="modal fade conico-modal-search" id="conico-modal-search" tabindex="-1" role="dialog">
    <a href="#" class="search-close" data-dismiss="modal" aria-label="Close" title=""><i class="ais-close"></i></a>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="get" class="navbar-search-block" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<div class="navbar-search-field-wrapper">
					<input type="text" placeholder="<?php _e( 'What you search?', 'conico' ); ?>" autofocus value="<?php echo get_search_query(); ?>" name="s">
				</div>
                <button type="submit"><i class="icon-search"></i><?php _e('Search','conico'); ?></button>
			</form>
		</div>
	</div>
</div>