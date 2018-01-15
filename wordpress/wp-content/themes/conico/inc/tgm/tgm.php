<?php

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once( trailingslashit( get_template_directory() ) . 'inc/tgm/class-tgm-plugin-activation.php' );

if ( ! function_exists( 'conico_register_required_plugins' ) ) {
	function conico_register_required_plugins() {
		$plugins = array(
			array(
				'name'     => __('Basement Framework','conico'),
				'slug'     => 'basement',
				'source'   => get_template_directory() . '/inc/install/basement.zip',
				'required' => true
			),
			array(
				'name'     => __('Basement Carousel','conico'),
				'slug'     => 'basement-carousel',
				'source'   => get_template_directory() . '/inc/install/basement-carousel.zip',
				'required' => true
			),
			array(
				'name'     => __('Basement Gallery','conico'),
				'slug'     => 'basement-gallery',
				'source'   => get_template_directory() . '/inc/install/basement-gallery.zip',
				'required' => true
			),
			array(
				'name'     => __('Basement Modal Windows','conico'),
				'slug'     => 'basement-modals',
				'source'   => get_template_directory() . '/inc/install/basement-modals.zip',
				'required' => true
			),
			array(
				'name'     => __('Basement Portfolio','conico'),
				'slug'     => 'basement-portfolio',
				'source'   => get_template_directory() . '/inc/install/basement-portfolio.zip',
				'required' => true
			),
			array(
				'name'     => __('Basement Shortcodes','conico'),
				'slug'     => 'basement-shortcodes',
				'source'   => get_template_directory() . '/inc/install/basement-shortcodes.zip',
				'required' => true
			),

			array(
				'name'     => __('Breadcrumb NavXT','conico'),
				'slug'     => 'breadcrumb-navxt',
				'required' => false
			),
			array(
				'name'     => __('Contact Form 7','conico'),
				'slug'     => 'contact-form-7',
				'required' => false
			),
			array(
				'name'     => __('Contact Form 7 MailChimp Extension','conico'),
				'slug'     => 'contact-form-7-mailchimp-extension',
				'required' => false
			),
			array(
				'name'     => __('MCE Table Buttons','conico'),
				'slug'     => 'mce-table-buttons',
				'required' => true
			),
			array(
				'name'         => __('WPBakery Visual Composer','conico'),
				'slug'         => 'js_composer',
				'source'       => get_template_directory() . '/inc/install/js_composer.zip',
				'required'     => true,
				'external_url' => 'https://vc.wpbakery.com/'
			),
			/*array(
				'name'         => __('WordPress Importer Redux','conico'),
				'slug'         => 'WordPress-Importer',
				'source'       => 'https://github.com/humanmade/WordPress-Importer/archive/master.zip',
				'external_url' => 'https://github.com/humanmade/WordPress-Importer',
				'required'     => false
			),*/
			array(
				'name'         => __('One Click Demo Import','conico'),
				'slug'         => 'one-click-demo-import',
				'required'     => true,
			),
			array(
				'name'         => __('Widget Importer & Exporter','conico'),
				'slug'         => 'widget-importer-exporter',
				'required'     => true,
			),
			array(
				'name'         => __('Slider Revolution','conico'),
				'slug'         => 'revslider',
				'source'       => get_template_directory() . '/inc/install/revslider.zip',
				'required'     => true,
				'external_url' => 'https://revolution.themepunch.com/'
			)
		);


		$config = array(
			'id'           => 'conico',
			'default_path' => '',
			'menu'         => 'tgmpa-install-plugins',
			'has_notices'  => true,
			'dismissable'  => true,
			'dismiss_msg'  => '',
			'is_automatic' => false,
			'message'      => '',
			'strings'      => array(
				'notice_can_install_recommended' => _n_noop(
					'This theme recommends the following plugins: <a href="//wpml.org/purchase/" target="_blank" title=""><i>WPML</i></a>, %1$s.',
					'This theme recommends the following plugins:  <a href="//wpml.org/purchase/" target="_blank" title=""><i>WPML</i></a>, %1$s.',
					'conico'
				)
			)
		);

		tgmpa( $plugins, $config );
	}

	add_action( 'tgmpa_register', 'conico_register_required_plugins' );
}