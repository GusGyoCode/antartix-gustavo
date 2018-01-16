<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "wrapper" div and all content after.
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>

	</div> <!-- /.wrapper -->
<div class="h-footer"></div>

</div> <!-- /.wrapper-full -->

<?php do_action( 'conico_before_footer_row' ); ?>
	<!-- FOOTER -->
	<footer role="contentinfo" <?php basement_footer_class( 'footer' ); ?>>
		<div class="footer-row">
			<?php
			/**
			 * Displays Footer Sidebar
			 *
			 * @package    Aisconverse
			 * @subpackage Conico
			 * @since      Conico 1.0
			 */
			do_action( 'conico_content_footer' );
			?>
		</div>

	</footer>
	<!-- /.footer -->
<?php do_action( 'conico_after_footer_row' ); ?>

<!-- ScrollTop -->
<a href="#" class="scrolltop"><i class="ais-arrow-top"></i></a>

<?php wp_footer(); ?>

</body>
</html>