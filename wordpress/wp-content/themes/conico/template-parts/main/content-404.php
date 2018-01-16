<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

<div class="text-center row native-page-404">
	<div class="col-sm-2"></div>
	<div class="col-sm-8">
		<h1><?php _e('404', 'conico'); ?></h1>
		<h2><?php _e('Page Not Found','conico'); ?></h2>
		<p><?php _e('Unfortunately the content you are looking for is not here or translation of the page has not been created. There may be a misspelling in your web address or you may have clicked a link for content that no longer exists.','conico'); ?></p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>"><?php _e('BACK TO HOME','conico'); ?></a>
	</div>
	<div class="col-sm-2"></div>
</div>