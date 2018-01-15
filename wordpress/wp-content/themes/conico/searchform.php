<?php
/**
 * The default search form
 *
 * @package Aisconverse
 * @subpackage Conico
 * @since Conico 1.0
 */
?>

<?php do_action('conico_before_search_form'); ?>

	<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label>
			<span class="screen-reader-text"><?php _e('Search for:','conico'); ?></span>
			<input type="text" class="search-field" name="s" placeholder="<?php _e('What you search?', 'conico'); ?>" value="<?php echo get_search_query(); ?>">
		</label>
		<button type="submit" class="search-submit"><i class="icon-search"></i></button>
	</form>

<?php do_action('conico_after_search_form'); ?>