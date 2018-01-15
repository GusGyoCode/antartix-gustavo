<?php
defined('ABSPATH') or die();

class Basement_Ecommerce_Woocommerce {

	private static $instance = null;
	private $version = '1.0.0';
	private $parameters_panel_config = array();

	public function __construct() {
		if ( self::enabled() ) {
			add_filter( 'basement_tiles_group_tiles_post_types', array( &$this, 'add_product_type_to_tiles' ) );
			add_filter( 'basement_tiles_custom_content', array( &$this, 'tile_content' ), 10, 2 );
			add_filter( 'basement_tiles_filters', array( &$this, 'tiles_filters' ), 10, 2 );
			add_filter( 'basement_tile_filters', array( &$this, 'tiles_filters' ), 10, 2 );

			add_action( 
				'add_meta_boxes_product',
				array( &$this, 'add_parameters_meta_box' )
			);


		}
	}

	public static function init() {
		return self::instance();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Basement_Ecommerce_Woocommerce();
		}
		return self::$instance;
	}

	public static function woo_position() {
		if ( self::enabled() ) {
			return is_shop() || is_cart() || is_account_page() || is_checkout() || is_edit_account_page() || is_tax( array( 'product_cat', 'product_tag' ) ) || is_product() ? true : false;
		} else {
			return false;
		}
	}

	public static function woo_pages() {
		$woo_pages = array();
		if ( self::enabled() ) {
			$shop     = get_option( 'woocommerce_shop_page_id' ) ? get_option( 'woocommerce_shop_page_id' ) : 'empty';
			$cart     = get_option( 'woocommerce_cart_page_id' ) ? get_option( 'woocommerce_cart_page_id' ) : 'empty';
			$checkout = get_option( 'woocommerce_checkout_page_id' ) ? get_option( 'woocommerce_checkout_page_id' ) : 'empty';
			$account  = get_option( 'woocommerce_myaccount_page_id' ) ? get_option( 'woocommerce_myaccount_page_id' ) : 'empty';
			$terms    = get_option( 'woocommerce_terms_page_id' ) ? get_option( 'woocommerce_terms_page_id' ) : 'empty';


			array_push($woo_pages, $shop, $cart, $checkout, $account, $terms);
		}
		return $woo_pages;
	}

	public static function is_shop() {
		if ( self::enabled() ) {
			return ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) );
		} else {
			return false;
		}
	}

	public static function is_product() {
		if ( self::enabled() ) {
			return is_singular( array( 'product' ) );
		} else {
			return false;
		}
	}

	public static function enabled() {
		return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

	public function add_product_type_to_tiles( $post_types ) {
		$post_types[] = 'product';
		return $post_types;
	}

	public function add_parameters_meta_box() {
		$this->parameters_panel_config = apply_filters(
			'basement_woocommerce_product_panel_config',
			array()
		);

		if ( $this->parameters_panel_config ) {
			add_meta_box( 
				'woocommerce_product_metabox', 
				__( 'Basement Parameters', BASEMENT_TEXTDOMAIN ), 
				array( &$this, '_create_parameters_meta_box' ), 
				'product', 
				'normal', 
				'core' 
			);
		}
	}

	public function _create_parameters_meta_box() {
		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$panel = $dom->appendChild( $dom->createElement( 'div' ) );
		$panel->setAttribute( 'id', 'basement_post_parameters_panel' );

		/**
		 * Setting_Panel config filter: basement_settings_post_panel_config
		 */
		$panel->appendChild( 
			$dom->importNode( 
				Basement_Settings_Panel::instance()->create_panel( 
					$this->parameters_panel_config,
					array( 
						'no_form' => true,
						'no_wrap_class' => true
					)
				), true 
			) 
		);
		echo $dom->saveHTML();
	}
	
	public function tile_content( $content, $post ) {
		if ( 'product' == $post->post_type ) {
			global $product, $woocommerce_loop;
			$woocommerce_loop[ 'basement_tiles' ] = true;
			$product_factory = new WC_Product_Factory();
			$product = $product_factory->get_product( $post->ID );
			ob_start();
			wc_get_template_part( 'content', 'product-cell' ); 
			$content = ob_get_clean();
		}
		return $content;
	}
	
	public function tiles_filters( $filters, $posts ) {
		if ( !is_array( $posts ) ) {
			$posts = array( $posts );
		}
		
		if ( !is_array( $filters ) ) {
			$filters = array( $filters );
		}

		foreach ( $posts as $post ) {
			if ( 'product' == $post->post_type ) {
				$categories = get_the_terms( $post->ID, 'product_cat' );
				foreach ( $categories as $category ) {
					$filters[ sanitize_title( $category->name ) ] = $category->name;
				}
			}
		}
		return $filters;
	}

	public static function print_notices() {
		if ( self::enabled() ) {
			echo '<div class="basement_notifications tatatat">';
				wc_print_notices();
			echo '</div>';
		}
	}

}