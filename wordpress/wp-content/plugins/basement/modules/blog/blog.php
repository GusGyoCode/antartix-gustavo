<?php
defined('ABSPATH') or die();

class Basement_Blog {

	private static $instance = null;

	private $meta = '_basement_meta_blog_';

	/**
	 * Basement_Blog constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ));
	}

	/**
	 * Basement_Blog init
	 *
	 * @return Basement_Blog|null
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Blog();
		}

		return self::$instance;
	}


	/**
	 * Added metabox
	 *
	 * @param $post_type
	 */
	public function add_meta_box( $post_type ) {
		if ( in_array( $post_type, array( 'post' ) ) ) {
			add_meta_box(
				'blog_parameters_meta_box',
				__( 'Post Parameters', BASEMENT_TEXTDOMAIN ),
				array( &$this, 'render_meta_box_content' ),
				$post_type,
				"normal",
				"high",
				null
			);

			add_filter( 'postbox_classes_' . $post_type . '_' . 'blog_parameters_meta_box', array(
				&$this,
				'class_meta_box'
			) );
		}
	}



	/**
	 * Change metabox after load
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function class_meta_box( $classes = array() ) {
		if ( ! in_array( 'closed', $classes ) ) {
			$classes[] = 'closed';
		}
		return $classes;
	}

	/**
	 * Render Meta Box Parameters
	 */
	public function render_meta_box_content( $post ) {
		$view = new Basement_Plugin();
		$view->basement_views( $this->project_settings_generate(), array( 'blog-param-meta-box' ) );
	}


	/**
	 * Generate Panel With Grid Settings
	 *
	 * @param array $config
	 * @return array
	 */
	public function project_settings_generate( $config = array() ) {
		$config = array(
			array(
				'type' => 'dom',
				'title' => __( 'Social sharing', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'description' => __( 'Sets the social sharing block.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'input' => $this->sharing()
			),
			array(
				'type' => 'dom',
				'title' => __( 'Type', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'description' => __( 'Set the view of social sharing block.', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'input' => $this->sharing_type()
			)
		);

		return $config;
	}


	/**
	 * Social sharing
	 *
	 * @return string
	 */
	public function sharing() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params = array(
			'meta_name' => $this->meta . 'sharing',
			'values' => array(
				'yes' => __( 'Yes', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'no'   => __( 'No', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'no'
		);
		$value = get_post_meta( $post->ID, $params['meta_name'], true );

		$radio = new Basement_Form_Input_Radio_Group( array(
			'name' => $params['meta_name'],
			'current_value' => empty( $value ) ? $params['current_value'] : $value,
			'values' => $params['values']
		) );

		$container = $dom->appendChild($dom->importNode( $radio->create(), true  ) );

		return $dom->saveHTML($container);
	}


	/**
	 * Social type sharing
	 *
	 * @return string
	 */
	public function sharing_type() {
		global $post;
		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$params_type = array(
			'meta_name' => $this->meta . 'sharing_type',
			'values' => array(
				'horizontal' => __( 'Horizontal', BASEMENT_PORTFOLIO_TEXTDOMAIN ),
				'dropdown'   => __( 'Dropdown', BASEMENT_PORTFOLIO_TEXTDOMAIN )
			),
			'current_value' => 'horizontal'
		);
		$value_type = get_post_meta( $post->ID, $params_type['meta_name'], true );

		$select = new Basement_Form_Input_Select( array(
			'name' => $params_type['meta_name'],
			'current_value' => empty( $value_type ) ? $params_type['current_value'] : $value_type,
			'values' => $params_type['values']
		) );

		$container = $dom->appendChild($dom->importNode( $select->create(), true  ) );

		return $dom->saveHTML($container);
	}

}

if ( ! function_exists( 'basement_post_share' ) ) {
	/**
	 * Displays Yandex Share Block
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	function basement_post_share($echo = true) {
		$id = get_the_ID();
		$share = '';
		$share_status = get_post_meta( $id, '_basement_meta_blog_sharing', true );
		if ( $share_status === 'yes' ) {
			$type = get_post_meta( $id, '_basement_meta_blog_sharing_type', true );
			if ( is_single() ) {
				$css_class = '';
				$socials       = get_option( 'conico_social_sharing' );
				$socials_clean = array();

				if ( $socials ) {
					foreach ( $socials as $social ) {
						if ( ! empty( $social ) ) {
							$socials_clean[] = $social;
						}
					}
				}

				$share = '';

				$socials = $socials_clean ? implode( ',', $socials_clean ) : 'gplus,facebook,twitter';

				$id = uniqid('vc-share-');


				if ( $type === 'dropdown' ) {
					$btn            = '<i class="icon-share"></i>';
					$share_block    = sprintf( '<div class="theme-share ya-share2" data-services="%1$s"  ></div>', $socials );
					$share_dropdown = sprintf( '<a href="#" class="theme-share-dropdown theme-share" id="'.$id.'">' . $btn . '<div class="share-tooltip">' . $share_block . '</div></a>' );
					if ( ! empty( $title ) ) {
						$share = '<div class="vc_share_'.esc_attr($type).' clearfix theme-share-title ' . esc_attr( $css_class ) . '"><span>' . esc_html( $title ) . '</span>' . $share_dropdown . '</div>';
					} else {
						$share = '<div class="vc_share_'.esc_attr($type).' clearfix theme-share-title ' . esc_attr( $css_class ) . '">' . $share_dropdown . '</div>';
					}
				} elseif ( $type === 'horizontal' ) {
					$share_block      = sprintf( '<div class="theme-share ya-share2" data-services="%1$s"  ></div>', $socials );
					$share_horizontal = sprintf( '<div class="theme-share-horizontal theme-share" id="'.$id.'">' . $share_block . '</div>' );
					if ( ! empty( $title ) ) {
						$share = '<div class="vc_share_'.esc_attr($type).' clearfix theme-share-title ' . esc_attr( $css_class ) . '"><span>' . esc_html( $title ) . '</span>' . $share_horizontal . '</div>';
					} else {
						$share = '<div class="vc_share_'.esc_attr($type).' clearfix theme-share-title ' . esc_attr( $css_class ) . '">' . $share_horizontal . '</div>';
					}
				}

				$share = '<div class="simple-share-block is-'.$type.'">'.$share.'</div>';

			}
		}


		if($echo) {
			echo $share;
		} else {
			return $share;
		}

	}
}








