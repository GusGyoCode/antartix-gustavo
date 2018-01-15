<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "wrapper" div.
 *
 * @package    Aisconverse
 * @subpackage Conico
 * @since      Conico 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="ie9 ie no-js"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="format-detection" content="telephone=no">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php wp_head(); ?>
	</head>
<body <?php body_class(); ?>>

<?php conico_preloader(); ?>

<?php do_action( 'conico_before_wrapper' ); ?>

<div class="wrapper-full">

<?php do_action( 'conico_before_header' ); ?>
	<!-- HEADER -->
	<header <?php basement_header_class( 'header' ); ?> role="banner">
		<?php do_action( 'conico_before_nav' ); ?>
			<nav class="<?php basement_navbar_class( 'navbar' ); ?>">
				<?php
					/**
					 * Displays Header / Elements of Header
					 *
					 * @package    Aisconverse
					 * @subpackage Conico
					 * @since      Conico 1.0
					 */
					do_action( 'conico_content_header' );
				?>
			</nav>
		<?php do_action( 'conico_after_nav' ); ?>
	</header>
	<!-- /.header -->
<?php do_action( 'conico_after_header' ); ?>

<!-- WRAPPER -->
<div class="wrapper" role="main">

	<?php do_action( 'conico_before_page_title' ); ?>
		<!-- PAGE TITLE -->
		<div <?php basement_page_title_class( 'pagetitle' ) ?>>
            <?php
                /**
                 * Displays Elements of Page Title
                 *
                 * @package    Aisconverse
                 * @subpackage Conico
                 * @since      Conico 1.0
                 */
                do_action( 'conico_content_page_title' );
            ?>
		</div>
		<!-- /.page title -->
	<?php do_action( 'conico_after_page_title' ); ?>


	<?php do_action( 'conico_before_page_title_float' ); ?>
		<!-- PAGE TITLE FLOAT -->
		<div <?php basement_page_title_float_class( 'pagetitle-float' ) ?>>
			<div class="container">
				<?php
					/**
					 * Displays Elements of Float Page Title
					 *
					 * @package    Aisconverse
					 * @subpackage Conico
					 * @since      Conico 1.0
					 */
					do_action( 'conico_content_page_title_float' );
				?>
			</div>
		</div>
		<!-- /.page title float -->
	<?php do_action( 'conico_after_page_title_float' ); ?>

