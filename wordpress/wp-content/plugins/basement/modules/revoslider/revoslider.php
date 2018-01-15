<?php
defined( 'ABSPATH' ) or die();

if ( ! in_array( 'revslider/revslider.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}


define( 'REVOPATH', WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );
define( 'REVOURL', str_replace( 'index.php', '', plugins_url( 'index.php', __FILE__ ) ) );


define( 'REVO_TYPEWRITER_PATH', REVOPATH . 'addons/typewriter/');
define( 'REVO_TYPEWRITER_URL', REVOURL . 'addons/typewriter/' );

class Basement_Revoslider {

	private static $instance = null;

	protected $post = array( 'page' );
	protected $name_metabox = '_revslider_metabox';
	protected $name_input_metabox = 'revlider_content_meta';

	/**
	 * Enable 'true' if need custom settings style RS
	 *
	 * @var bool
	 */
	private $debug = false;

	/**
	 * Basement_Revoslider constructor.
	 */
	public function __construct() {

		if ( in_array( 'revslider/revslider.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ), 10, 2 );
			add_action( 'save_post', array( &$this, 'save_meta_box' ) );

			// Only for developers!
			if ( $this->debug && is_admin() ) {
				add_action( 'admin_menu', array( &$this, 'rs_menu' ) );
			}

			add_filter( 'revslider_mod_default_navigations', array( &$this, 'add_navigate_style' ) );

			add_action( 'admin_enqueue_scripts', array( &$this, 'load_back_scripts' ) );

			add_filter( 'body_class', array( &$this, 'body_revclasses' ), 10, 2 );

			add_shortcode( 'revslider_avatar', array( &$this,'revslider_author_avatar') );
		}
	}

	public function revslider_author_avatar($atts) {
		extract( shortcode_atts( array (
			'author' => 'admin',
			'size'   => '54',
			'link'   => ''

		), $atts ) );

		global $wpdb;
		$user = $wpdb->get_results( "SELECT ID FROM $wpdb->users WHERE display_name = '$author'" );

		$author_id     = $user[0]->ID;
		$author_link   = get_author_posts_url( $author_id );
		$author_avatar = get_avatar( $author_id, $size, '', $author );

		$html = '';
		if( $link === 'yes' ) $html .= '<a href="' . $author_link . '" title="' . $author . '">';
		$html .= $author_avatar;
		if( $link === 'yes' ) $html .= '</a>';

		return $html;
	}


	/**
	 * Displays custom classes for Revolution Slider
	 *
	 * @param $classes
	 * @param $class
	 *
	 * @return array
	 */
	public function body_revclasses( $classes, $class ) {
		if(!class_exists('RevSlider'))
			return $classes;

		$id = get_the_ID();

		if ( Basement_Ecommerce_Woocommerce::enabled() ) {
			if ( is_shop() && ! is_tax( array( 'product_cat', 'product_tag' ) ) ) {
				$id = get_option( 'woocommerce_shop_page_id' );
			}
		}

		if ( is_page() || is_single() || Basement_Ecommerce_Woocommerce::is_shop() ) {
			$meta_position = get_post_meta( $id, 'basement_rev_position', true );
			$shortcode     = get_post_meta( $id, 'revlider_content_meta', true );

			$sld     = new RevSlider();
			$sliders = count( $sld->getArrSliders() );

			if ( ! empty( $shortcode ) && ! empty( $sliders ) ) {
				if ( ( 'before_content' === $meta_position ) ) {
					$classes[] = 'theme_revslider_before_content';
				}
				if ( ( 'header_content' === $meta_position ) ) {
					$classes[] = 'theme_revslider_header_content';
				}
			}
		}

		return $classes;
	}

	/**
	 * Basement_Revoslider init
	 *
	 * @return Basement_Revoslider|null
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Revoslider();
		}

		return self::$instance;
	}

	/**
	 * Added metabox
	 *
	 * @param $post_type
	 */
	public function add_meta_box( $post_type, $post ) {
		$post_ID = $post->ID;
		if ( $post_ID != get_option( 'page_for_posts' ) ) {
			if ( in_array( $post_type, array( 'page', 'product', 'single_project' ) ) ) {
				add_meta_box(
					"basement-revslider-meta-box",
					__( 'Slider Revolution', BASEMENT_TEXTDOMAIN ),
					array( &$this, 'render_meta_box' ),
					$post_type,
					"normal",
					"high",
					null
				);

				add_filter( 'postbox_classes_' . $post_type . '_' . 'basement-revslider-meta-box', array(
					&$this,
					'class_meta_box'
				) );
			}
		}

	}


	/**
	 * Register js/css in backend
	 */
	public function load_back_scripts() {
		$page = '';
		if(isset($_GET['page'])) {
			$page = $_GET['page'];
		} elseif (isset($_POST['page'])) {
			$page = $_POST['page'];
		}

		if ( $page === 'revslider' ) {
			wp_enqueue_style( BASEMENT_TEXTDOMAIN . '-revoeditor-style', plugins_url( 'revoslider-editor.css', __FILE__ ) );
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

		$content = get_post_meta( get_the_ID(), $this->name_input_metabox, true );

		if ( ! $content ) {
			if ( ! in_array( 'closed', $classes ) ) {
				$classes[] = 'closed';
			}
		}

		return $classes;
	}


	/**
	 * Save metabox
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function save_meta_box( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( 'page' == isset($_POST['post_type']) ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}


		if ( isset( $_POST[ $this->name_input_metabox ] ) ) {

			$mydata = $_POST[ $this->name_input_metabox ];

			update_post_meta( $post_id, $this->name_input_metabox, $mydata );
		}


		if ( isset( $_POST['basement_rev_alias'] ) ) {

			$basement_rev_alias = $_POST['basement_rev_alias'];

			update_post_meta( $post_id, 'basement_rev_alias', $basement_rev_alias );
		}

		if ( isset( $_POST['basement_rev_position'] ) ) {

			$basement_rev_alias = $_POST['basement_rev_position'];

			update_post_meta( $post_id, 'basement_rev_position', $basement_rev_alias );
		}

	}


	/**
	 * Render metabox
	 *
	 * @param $post
	 */
	public function render_meta_box( $post ) {
		if ( ! class_exists( 'RevSlider' ) )
			echo '';

		$sld           = new RevSlider();
		$sliders       = $sld->getArrSliders();
		$count_sliders = !empty($sliders) ? count( $sliders ) : false;
		$post_id = !empty($post->ID) ? $post->ID : '';

		$balias = get_post_meta( $post_id, 'basement_rev_alias', true );
		$slider = get_post_meta( $post_id, $this->name_input_metabox, true );


		if ( ! empty( $count_sliders ) ) { ?>

			<p style="font-size: 14px;"><?php _e( 'Select the slider.', BASEMENT_TEXTDOMAIN ); ?></p>

			<input type="hidden" id="basement-revslider" name="<?php echo $this->name_input_metabox; ?>"
			       value="<?php echo esc_attr( $slider ); ?>">
			<input type="hidden" id="basement-alias" name="basement_rev_alias"
			       value="<?php echo esc_attr( $balias ); ?>">

			<select style="display: none;">
				<option value="-1"
				        selected="selected"><?php _e( '--- Choose Slider ---', BASEMENT_TEXTDOMAIN ); ?></option>
				<?php
				$sl           = array();
				$sliders_info = array();
				if ( ! empty( $sliders ) ) {
					foreach ( $sliders as $slider ) {
						$alias        = $slider->getParam( 'alias', 'false' );
						$title        = $slider->getTitle();
						$type         = $slider->getParam( 'source_type', 'gallery' );
						$slider_type  = $slider->getParam( 'slider-type', 'standard' );
						$active_slide = $slider->getParam( 'hero_active', - 1 );
						$sliderID     = $slider->getID();

						if ( $type == 'gallery' ) {
							$slides = $slider->getSlides();
						} elseif ( $type == 'specific_posts' ) {
							$slides = $slider->getSlidesFromPosts();
						}

						if ( ! empty( $slides ) ) {
							$sliders_info[ $sliderID ] = array();

							foreach ( $slides as $slide ) {
								$bg_extraClass = '';
								$bg_fullstyle  = '';

								$urlImageForView = $slide->getThumbUrl();

								$bgt = $slide->getParam( 'background_type', 'transparent' );
								if ( $type == 'woocommerce' ) {
								}
								if ( $bgt == 'image' || $bgt == 'streamvimeo' || $bgt == 'streamyoutube' || $bgt == 'streaminstagram' ) {
									switch ( $type ) {
										case 'posts':
											$urlImageForView = RS_PLUGIN_URL . 'public/assets/assets/sources/post.png';
											break;
										case 'woocommerce':
											$urlImageForView = RS_PLUGIN_URL . 'public/assets/assets/sources/wc.png';
											break;
										case 'facebook':
											$urlImageForView = RS_PLUGIN_URL . 'public/assets/assets/sources/fb.png';
											break;
										case 'twitter':
											$urlImageForView = RS_PLUGIN_URL . 'public/assets/assets/sources/tw.png';
											break;
										case 'instagram':
											$urlImageForView = RS_PLUGIN_URL . 'public/assets/assets/sources/ig.png';
											break;
										case 'flickr':
											$urlImageForView = RS_PLUGIN_URL . 'public/assets/assets/sources/fr.png';
											break;
										case 'youtube':
											$urlImageForView = RS_PLUGIN_URL . 'public/assets/assets/sources/yt.png';
											break;
										case 'vimeo':
											$urlImageForView = RS_PLUGIN_URL . 'public/assets/assets/sources/vm.png';
											break;
									}
								}

								if ( $bgt == 'image' || $bgt == 'vimeo' || $bgt == 'youtube' || $bgt == 'html5' || $bgt == 'streamvimeo' || $bgt == 'streamyoutube' || $bgt == 'streaminstagram' ) {
									$bg_style = ' ';
									if ( $slide->getParam( 'bg_fit', 'cover' ) == 'percentage' ) {
										$bg_style .= "background-size: " . $slide->getParam( 'bg_fit_x', '100' ) . '% ' . $slide->getParam( 'bg_fit_y', '100' ) . '%;';
									} else {
										$bg_style .= "background-size: " . $slide->getParam( 'bg_fit', 'cover' ) . ";";
									}
									if ( $slide->getParam( 'bg_position', 'center center' ) == 'percentage' ) {
										$bg_style .= "background-position: " . intval( $slide->getParam( 'bg_position_x', '0' ) ) . '% ' . intval( $slide->getParam( 'bg_position_y', '0' ) ) . '%;';
									} else {
										$bg_style .= "background-position: " . $slide->getParam( 'bg_position', 'center center' ) . ";";
									}
									$bg_style .= "background-repeat: " . $slide->getParam( 'bg_repeat', 'no-repeat' ) . ";";
									$bg_fullstyle = ' style="background-image:url(' . $urlImageForView . ');' . $bg_style . '" ';
								}

								if ( $bgt == 'solid' ) {
									$bg_fullstyle = ' style="background-color:' . $slide->getParam( 'slide_bg_color', 'transparent' ) . ';" ';
								}
								if ( $bgt == 'trans' ) {
									$bg_extraClass = 'mini-transparent';
								}
								if ( $slide->getParam( 'thumb_for_admin', 'off' ) == "on" ) {
									$bg_fullstyle = ' style="background-image:url(' . $slide->getParam( 'slide_thumb', '' ) . ');background-size:cover;background-position:center center" ';
								}

								$sliders_info[ $sliderID ][] = array(
									'id'            => $slide->getID(),
									'slider_type'   => $slider_type,
									'title'         => $slide->getTitle(),
									'slidertitle'   => $title,
									'slideralias'   => $alias,
									'sliderid'      => $sliderID,
									'state'         => $slide->getParam( 'state', 'published' ),
									'slide_thumb'   => $slide->getParam( 'slide_thumb', '' ),
									'bg_fullstyle'  => $bg_fullstyle,
									'bg_extraClass' => $bg_extraClass,
									'active_slide'  => $active_slide
								);

								if ( $active_slide == - 1 ) {
									$active_slide = - 99;
								} //do this so that we are hero, only the first slide will be active if no hero slide was yet set
							}
						}

						$sl[ $type ][] = array( 'alias' => $alias, 'title' => $title, 'id' => $sliderID );
					}

					if ( ! empty( $sl ) ) {
						foreach ( $sl as $type => $slider ) {
							$mtype = ( $type == 'specific_posts' ) ? 'Specific Posts' : $type;
							echo '<option disabled="disabled">--- ' . ucfirst( esc_attr( $mtype ) ) . ' ---</option>';
							foreach ( $slider as $values ) {
								if ( $values['alias'] != 'false' ) {
									echo '<option data-sliderid="' . esc_attr( $values['id'] ) . '" data-slidertype="' . esc_attr( $type ) . '" value="' . esc_attr( $values['alias'] ) . '">' . esc_attr( $values['title'] ) . '</option>' . "\n";
								}
							}
						}
					}
				}
				?>
			</select>
			<ul id="basement-slider-list" style="margin-bottom: 0;">
				<?php
				if ( ! empty( $sliders_info ) ) {
					foreach ( $sliders_info as $type => $slider ) {
						foreach ( $slider as $values ) {
							?>
							<li id="basement_slider_list_item_<?php echo $values['sliderid']; ?>"
							    class="rs-slider-modify-li <?php if ( $balias === $values['slideralias'] ) {
								    echo 'selected';
							    } ?>" data-sliderid="<?php echo esc_attr( $values['sliderid'] ); ?>"
							    data-slideralias="<?php echo esc_attr( $values['slideralias'] ); ?>">
								<span class="mini-transparent mini-as-bg"></span>
								<div
									class="rs-slider-modify-container <?php echo $values['bg_extraClass']; ?>" <?php echo $values['bg_fullstyle']; ?>></div>
								<i class="slide-link-forward eg-icon-forward"></i>

								<span
									class="rs-slider-modify-title">#<?php echo $values['sliderid'] . ' ' . $values['slidertitle']; ?></span>
							</li>
							<?php
							break;
						}
					}
				}
				?>
				<span style="clear:both;width:100%;display:block"></span>
			</ul>
			<span style="clear:both;width:100%;display:block"></span>

			<hr style="margin-top: 25px;">
			<p style="font-size: 14px;margin-top: 25px;"><?php _e( 'Select the placement.', BASEMENT_TEXTDOMAIN ); ?></p>

			<?php
			$placement_name    = 'basement_rev_position';
			$placement_current = 'before_content';
			$placement_value = get_post_meta( $post->ID, $placement_name, true );
			$placement         = ! empty( $placement_value ) ? $placement_value : $placement_current;

			$positions = array(
				'before_content' => __( 'Above content', BASEMENT_TEXTDOMAIN ),
				'header_content' => __( 'Under header', BASEMENT_TEXTDOMAIN )
			)

			?>
			<ul>
				<?php foreach ( $positions as $radio_key => $radio_value ) { ?>
					<li>
						<label for="<?php echo $radio_key; ?>">
							<input type="radio" id="<?php echo esc_attr( $radio_key ); ?>"
							       name="<?php echo esc_attr( $placement_name ); ?>" <?php checked( $radio_key, $placement ); ?>
							       value="<?php echo esc_attr( $radio_key ); ?>">
							<strong><?php echo $radio_value; ?></strong>
						</label>
					</li>
				<?php } ?>

			</ul>

			<hr style="margin-top: 25px;">


			<p style="font-size: 14px;margin-top: 25px;margin-bottom: 15px !important;">

				<label for="basement-meta-hidden-content">
					<?php $hide_content = get_post_meta( $post->ID, '_basement_meta_hide_content', true ); ?>
					<input type="hidden" value="" name="_basement_meta_hide_content" autocomplete="off">
					<input type="checkbox" id="basement-meta-hidden-content" name="_basement_meta_hide_content" value="hide" <?php checked($hide_content,'hide'); ?>>
					<?php _e( 'Remove content area', BASEMENT_TEXTDOMAIN ); ?>
				</label>

				</p>

			<?php
		} else {
			echo '<p style="font-size: 14px;"><a href="admin.php?page=revslider" title="">' . __( 'Create your first slider', BASEMENT_TEXTDOMAIN ) . '</a></p>';
		}
	}

	/**
	 * Settings RS slider (only for develop)
	 */
	public function rs_menu() {
		add_menu_page( __( 'Theme RS Settings', BASEMENT_TEXTDOMAIN ), __( 'RS Settings', BASEMENT_TEXTDOMAIN ), 'manage_options', basename( __FILE__ ), array(
			&$this,
			'revslider_form'
		), 'dashicons-hammer', '21' );
	}


	/**
	 * Add new nav style from *.json
	 *
	 * @param $navigations
	 *
	 * @return mixed
	 */
	public function add_navigate_style( $navigations ) {

		$json_file = file_get_contents( REVOPATH . 'theme-bg.json' );


		if ( ! empty( $json_file ) ) {

			$json = json_decode( $json_file, true );

			$json[0]['markup']   = json_encode( $json[0]['markup'] );
			$json[0]['css']      = json_encode( $json[0]['css'] );
			$json[0]['settings'] = json_encode( $json[0]['settings'] );

			$navigations[] = $json[0];
		}


		$json_file_medium = file_get_contents( REVOPATH . 'theme-nobg.json' );

		if ( ! empty( $json_file_medium ) ) {

			$json_medium = json_decode( $json_file_medium, true );

			$json_medium[0]['markup']   = json_encode( $json_medium[0]['markup'] );
			$json_medium[0]['css']      = json_encode( $json_medium[0]['css'] );
			$json_medium[0]['settings'] = json_encode( $json_medium[0]['settings'] );

			$navigations[] = $json_medium[0];
		}


		$json_file_big = file_get_contents( REVOPATH . 'theme-bullets.json' );

		if ( ! empty( $json_file_big ) ) {

			$json_big = json_decode( $json_file_big, true );

			$json_big[0]['markup']   = json_encode( $json_big[0]['markup'] );
			$json_big[0]['css']      = json_encode( $json_big[0]['css'] );
			$json_big[0]['settings'] = json_encode( $json_big[0]['settings'] );

			$navigations[] = $json_big[0];
		}

		return $navigations;
	}


	/**
	 * Settings form
	 */
	public function revslider_form() {
		$json_file = file_get_contents( REVOPATH . 'params.json' );
		$p         = 'basement_';

		if ( empty( $json_file ) ) {
			return;
		}

		$json = json_decode( $json_file, true );


		if ( isset( $_POST['update_revsettings'] ) ) {

			$json[0]['id']      = isset( $_POST[ $p . 'id' ] ) ? absint( $_POST[ $p . 'id' ] ) : 1488;
			$json[0]['default'] = isset( $_POST[ $p . 'default' ] ) ? (bool) $_POST[ $p . 'default' ] : true;
			$json[0]['name']    = isset( $_POST[ $p . 'name' ] ) ? $_POST[ $p . 'name' ] : '';
			$json[0]['handle']  = isset( $_POST[ $p . 'handle' ] ) ? $_POST[ $p . 'handle' ] : '';

			$json[0]['markup']['arrows']  = isset( $_POST[ $p . 'markup_arrows' ] ) ? $_POST[ $p . 'markup_arrows' ] : '';
			$json[0]['markup']['bullets'] = isset( $_POST[ $p . 'markup_bullets' ] ) ? $_POST[ $p . 'markup_bullets' ] : '';


			$json[0]['css']['arrows']  = isset( $_POST[ $p . 'css_arrows' ] ) ? $_POST[ $p . 'css_arrows' ] : '';
			$json[0]['css']['bullets'] = isset( $_POST[ $p . 'css_bullets' ] ) ? $_POST[ $p . 'css_bullets' ] : '';


			$json[0]['settings']['width']['thumbs']  = isset( $_POST[ $p . 'settings_width_thumbs' ] ) ? $_POST[ $p . 'settings_width_thumbs' ] : '';
			$json[0]['settings']['width']['arrows']  = isset( $_POST[ $p . 'settings_width_arrows' ] ) ? $_POST[ $p . 'settings_width_arrows' ] : '';
			$json[0]['settings']['width']['bullets'] = isset( $_POST[ $p . 'settings_width_bullets' ] ) ? $_POST[ $p . 'settings_width_bullets' ] : '';
			$json[0]['settings']['width']['tabs']    = isset( $_POST[ $p . 'settings_width_tabs' ] ) ? $_POST[ $p . 'settings_width_tabs' ] : '';


			$json[0]['settings']['height']['thumbs']  = isset( $_POST[ $p . 'settings_height_thumbs' ] ) ? $_POST[ $p . 'settings_height_thumbs' ] : '';
			$json[0]['settings']['height']['arrows']  = isset( $_POST[ $p . 'settings_height_arrows' ] ) ? $_POST[ $p . 'settings_height_arrows' ] : '';
			$json[0]['settings']['height']['bullets'] = isset( $_POST[ $p . 'settings_height_bullets' ] ) ? $_POST[ $p . 'settings_height_bullets' ] : '';
			$json[0]['settings']['height']['tabs']    = isset( $_POST[ $p . 'settings_height_tabs' ] ) ? $_POST[ $p . 'settings_height_tabs' ] : '';


			$json[0]['settings']['original']['css']['arrows']  = isset( $_POST[ $p . 'settings_original_css_arrows' ] ) ? $_POST[ $p . 'settings_original_css_arrows' ] : '';
			$json[0]['settings']['original']['css']['bullets'] = isset( $_POST[ $p . 'settings_original_css_bullets' ] ) ? $_POST[ $p . 'settings_original_css_bullets' ] : '';


			$json[0]['settings']['original']['markup']['arrows']  = isset( $_POST[ $p . 'settings_original_markup_arrows' ] ) ? $_POST[ $p . 'settings_original_markup_arrows' ] : '';
			$json[0]['settings']['original']['markup']['bullets'] = isset( $_POST[ $p . 'settings_original_markup_bullets' ] ) ? $_POST[ $p . 'settings_original_markup_bullets' ] : '';


			file_put_contents( REVOPATH . 'params.json', json_encode( $json, JSON_PRETTY_PRINT ) );

		}


		?>
		<div class="wrap">
			<h1><?php _e( 'Theme RS Settings', BASEMENT_TEXTDOMAIN ); ?></h1>
			<form method="post">
				<h2><?php _e( 'Parameters', BASEMENT_TEXTDOMAIN ); ?></h2>
				<?php
				foreach ( $json[0] as $key => $value ) {
					switch ( $key ) {
						case 'id' :
						case 'default' :
						case 'name' :
						case 'handle' :
							echo '<div class="basement-rs-item"><label><strong>' . strtoupper( $key ) . '</strong><br><input type="text" name="' . $p . $key . '" value="' . $value . '"></label></div>';
							break;
						case 'markup' :
						case 'css' :
							echo '<h2><ins>' . ucfirst( $key ) . '</ins></h2>';
							foreach ( $value as $inner_key => $inner_value ) {
								echo '<div class="basement-rs-item"><label><strong>' . strtoupper( $inner_key ) . '</strong><br><textarea name="' . $p . $key . '_' . $inner_key . '" cols="109" rows="10">' . stripslashes( $inner_value ) . '</textarea></label></div>';
							}
							break;
						case 'settings' :
							echo '<h2><ins>' . ucfirst( $key ) . '</ins></h2>';
							foreach ( $value as $inner_key => $inner_value ) {
								switch ( $inner_key ) {
									case 'width':
									case 'height':
										echo '<h3 style="font-size: 15px;">' . ucfirst( $inner_key ) . '</h3>';
										foreach ( $inner_value as $deep_key => $deep_value ) {
											echo '<div class="basement-rs-item"><label><strong>' . strtoupper( $deep_key ) . '</strong><br><input type="text" name="' . $p . $key . '_' . $inner_key . '_' . $deep_key . '" value="' . $deep_value . '"></label></div>';
										}
										break;
									case 'original' :
										foreach ( $inner_value as $deep_key => $deep_value ) {
											echo '<h3 style="font-size: 15px;">' . ucfirst( $deep_key ) . '</h3>';
											foreach ( $deep_value as $last_key => $last_value ) {
												echo '<div class="basement-rs-item"><label><strong>' . strtoupper( $last_key ) . '</strong><br><textarea name="' . $p . $key . '_' . $inner_key . '_' . $deep_key . '_' . $last_key . '" cols="109" rows="10">' . $last_value . '</textarea></label></div>';
											}
										}
										break;
								}
							}
							break;
					}

				}
				?>
				<input type="submit" name="update_revsettings" class="button button-primary button-large"
				       value="Update">
			</form>
		</div>
		<?php
	}
}


if ( ! function_exists( 'basement_revslider' ) ) {
	/**
	 * Displays Slider Revolution
	 *
	 * @param string $position
	 * @param bool   $echo
	 *
	 * @return mixed
	 */
	function basement_revslider( $position = '', $echo = true ) {
		$id = get_the_ID();

		if ( Basement_Ecommerce_Woocommerce::enabled() ) {
			if ( is_shop() && ! is_tax( array( 'product_cat', 'product_tag' ) ) ) {
				$id = get_option( 'woocommerce_shop_page_id' );
			}
		}

		$meta_position = get_post_meta( $id, 'basement_rev_position', true );
		$shortcode     = get_post_meta( $id, 'revlider_content_meta', true );


		if ( class_exists( 'RevSlider' ) ) {
			$sld     = new RevSlider();
			$sliders = count( $sld->getArrSliders() );


			if ( empty( $position ) && ! empty( $shortcode ) && ! empty( $sliders ) ) {

				if ( $echo ) {
					echo do_shortcode( $shortcode );
				} else {
					return $shortcode;
				}
			} else {
				if ( ( $position . '_content' === $meta_position ) && ! empty( $shortcode ) && ! empty( $sliders ) ) {
					if ( $echo ) {
						echo do_shortcode( $shortcode );
					} else {
						return $shortcode;
					}
				}
			}
		} else {
			if ( $echo ) {
				echo '';
			} else {
				return '';
			}
		}
	}
}


if ( ! function_exists( 'basement_action_theme_before_content' ) ) {
	/**
	 * Displays params before Content
	 */
	function basement_action_theme_before_content() {
		ob_start();
	}
	add_action('conico_before_content', 'basement_action_theme_before_content');
}


if ( ! function_exists( 'basement_action_theme_after_content' ) ) {
	/**
	 * Displays params after Content
	 */
	function basement_action_theme_after_content() {
		$id = get_the_ID();

		if(Basement_Ecommerce_Woocommerce::enabled()) {
			if ( is_shop() && ! is_tax( array( 'product_cat', 'product_tag' ) ) ) {
				$id = get_option( 'woocommerce_shop_page_id' );
			}
		}

		$content_visible = get_post_meta( $id, '_basement_meta_hide_content', true );

		$content = ob_get_contents();
		ob_end_clean();


		if(empty($content_visible)) {
			echo $content;
		}
	}
	add_action('conico_after_content', 'basement_action_theme_after_content');
}




include_once( REVOPATH . 'addons/typewriter/typewriter.php' );