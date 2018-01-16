<?php
defined('ABSPATH') or die();

class Basement_Settings_Post {

	private static $instance = null;
	public $textdomain = 'basement_settings_post';
	private $version = '1.0.0';
	private $config = array();

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Settings_Post();
		}
		return self::$instance;
	}

	/**
	 * Init class instance
	 */
	public function __construct() {
		add_action( 'save_post', array( &$this, 'save_metabox_data' ) );
	}

	public function save_metabox_data() {
		global $post;
		if ( !( $post instanceof WP_Post ) ) {
			return;
		}
		if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) ) {
			return $post->ID;
		}

		foreach ( $_POST as $key => $value ) {
			if ( strpos( $key, '_basement_meta' ) === 0 ) {
				update_post_meta( $post->ID, $key, $value );
			}
		}

	}

}




// function basement_cpt_photo_add_metabox() {
// 	add_meta_box( 'basement_cpt_photo_create_preview_metabox', __( 'Preview', BASEMENT_TEXTDOMAIN ), 'basement_cpt_photo_create_preview_metabox', 'photo', 'side', 'default');
// }

// function basement_cpt_photo_create_preview_metabox() {
// 	global $post;
// 	$preview_id = get_post_meta( $post->ID, '_preview', true );
// 	$input = new Basement_Form_Input_Image( array(
// 			'name' => '_preview',
// 			'value' => $preview_id,
// 			'text_buttons' => true,
// 			'upload_text' => __( 'Set photo preview', BASEMENT_TEXTDOMAIN ),
// 			'delete_text' => __( 'Remove photo preview', BASEMENT_TEXTDOMAIN ),
// 			'frame_title' => __( 'Set photo preview', BASEMENT_TEXTDOMAIN ),
// 			'frame_button_text' => __( 'Set photo preview', BASEMENT_TEXTDOMAIN ),
// 		)
// 	);

// 	$dom = new DOMDocument( '1.0', 'UTF-8' );
// 	$dom->appendChild( $dom->importNode( $input->create(), true ) );
// 	echo $dom->saveHTML();
// }