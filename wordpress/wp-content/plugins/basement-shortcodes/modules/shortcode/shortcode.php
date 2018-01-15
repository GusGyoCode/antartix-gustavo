<?php
defined('ABSPATH') or die();

abstract class Basement_Shortcode {

	protected $form;
	protected $enclosing = false;
	protected $wrap = false;
	protected $tag = null;
	protected $inner = null;
	protected $override = true;
	protected $name = 'basement';
	protected $title = 'Name is not defined';
	protected $params = array();
	protected $textdomain = 'basement_shortcode';

	public function __construct( $params = array() ) {
		$this->form = Basement_Form::instance();
		$this->title = __( 'Name is not defined', BASEMENT_SHORTCODES_TEXTDOMAIN );
		$this->init_params( $params );

		if ( !is_admin() ) {
			if ( $this->params[ 'override' ] ) {
				add_shortcode( $this->tag, array( $this, 'render' ) );
			}
			if( $this->params['inner'] ) {
				$nest_tags = $this->inner;  # Nested tags
				foreach ($nest_tags as $shortcode) {
					add_shortcode( $shortcode, array( $this, 'render_nested' ) );
				}
			}
		}

	}


	protected function init_params( $params ) {
		$this->params = array_merge( array(
			'title' => $this->title,
			'tag' => $this->tag,
			'enclosing' => $this->enclosing,
			'name' => $this->name,
			'override' => $this->override,
			'params_aliases' => array(),
			'group' => 'default',
			'inner' => $this->inner
		), $this->params );

		$this->params = wp_parse_args( $params, $this->params );
		
		$this->title = $this->params[ 'title' ];

		$this->inner = $this->params[ 'inner' ];

		$this->tag = $this->params[ 'tag' ] ? $this->params[ 'tag' ] : $this->params[ 'name' ];

		$this->name = $this->params[ 'name' ];

		if ( $this->params[ 'enclosing' ] ) {
			$this->enclosing = $this->params[ 'enclosing' ];
		}

	}

	public function section_config( $config = array() ) {
		return $config;
	}

	// TODO: remove
	public function panel_markup_wrapper( $container ) {
		$container_dom = $container->ownerDocument;

		$button = $container->appendChild( $container_dom->createElement( 'div' ) );
		$button->setAttribute( 'class', $this->textdomain . '_panel_button' );
		$button->setAttribute( 'data-tag', $this->tag );
		$button->setAttribute( 'data-name', $this->name );

		$button_title = $button->appendChild( $container_dom->createElement( 'div', $this->title ) );
		$button_title->setAttribute('class', $this->textdomain . '_panel_button_title' );

		$wrapper = $container->appendChild( $container_dom->createElement( 'div' ) );
		$wrapper->setAttribute( 'class', $this->textdomain . '_panel_shortcode_wrapper' );
		$wrapper->setAttribute( 'data-tag', $this->tag );
		$wrapper->setAttribute( 'data-name', $this->name );
		if ( $this->enclosing ) {
			$wrapper->setAttribute( 'data-shortcode-enclosing', true );
		}
		if ( $this->wrap ) {
			$wrapper->setAttribute( 'data-wrap', $this->wrap );
		}

		$header = $wrapper->appendChild( $container_
			->createElement( 'div', $this->title ) );
		$header->setAttribute( 'class', $this->textdomain . '_panel_shortcode_header' );

		$this->panel_markup_dom( $wrapper );

		$button = $wrapper->appendChild( $container_dom->createElement( 'a' ) );
		$button->setAttribute( 'class', 'basement_shortcode_builder_button button  button-primary button-large' );
		$button_content = $container->ownerDocument->createDocumentFragment();
		$button_content->appendXML( __( sprintf( 'Insert %s shortcode', '<span></span>' ), BASEMENT_SHORTCODES_TEXTDOMAIN ) );
		$button->appendChild($button_content);

		return $container;
	}

	protected function panel_markup_dom( $container ) {
		return '';
	}

	public static function create_config_data_attributes( $config, $data_type, $data_key = '', $data_may_be_empty = true ) {
		if ( !isset( $config[ 'attributes' ] ) || !is_array( $config[ 'attributes' ] ) ) {
			$config[ 'attributes' ] = array();
		}

		$config[ 'attributes' ] = self::create_data_attributes( $config[ 'attributes' ], $data_type, $data_key, $data_may_be_empty );

		return $config;
	}

	public static function create_data_attributes( $attributes, $data_type = 'text', $data_key = '', $data_may_be_empty = false ) {
		$attributes[ 'data-type' ] = $data_type;

		if ( $data_key ) {
			$attributes[ 'data-key' ] = $data_key;
		}
		if ( $data_may_be_empty ) {
			$attributes[ 'data-may-be-empty' ] = $data_may_be_empty;
		}

		return $attributes;
	}

	public function render( $atts = array(), $content = '' ) {
		return $content;
	}


	public function render_nested( $atts = array(), $content = '' ) {
		return $content;
	}

	protected function get_posts_by_ids( $ids, $post_type = 'any', $default_args = array() ) {
		$query_args = wp_parse_args( array(
			'post_type' => $post_type,
			'posts_per_page' => -1
		), $default_args );

		$ids = wp_parse_id_list( $ids );

		if ( count( $ids ) ) {
			$query_args[ 'post__in' ] = $ids;
			$query_args[ 'orderby' ] = 'post__in';
		}
		return get_posts( $query_args );
	}

	protected function param_on( $key ) {
		return isset( $this->params[ $key ] ) && $this->params[ $key ];
	}

	// TODO: remake all SC to use this method on params creating
	protected function param_key( $key ) {
		return isset( $this->params[ 'params_aliases' ][ $key ] ) ? $this->params[ 'params_aliases' ][ $key ] : $key;
	}

	protected function parse_args( $args, $defaults ) {
		$args = wp_parse_args( $args, $defaults );
		if ( count( $this->params[ 'params_aliases' ] ) ) {
			foreach ( $this->params[ 'params_aliases' ] as $key => $alias ) {
				if ( isset( $args[ $alias ] ) ) {
					$args[ $key ] = $args[ $alias ];
					unset( $args[ $alias ] );
				}
			}
		}
		return $args;
	}

	public function tag() {
		return $this->tag;
	}

	public function name() {
		return $this->name;
	}

	public function title() {
		return $this->title;
	}

	public function wrap() {
		return $this->wrap;
	}

	public function enclosing() {
		return $this->enclosing;
	}

	public function group() {
		return $this->params[ 'group' ];
	}


	public function inner() {
		return $this->params[ 'inner' ];
	}
	
}