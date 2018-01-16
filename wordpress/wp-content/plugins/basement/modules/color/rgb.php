<?php
defined('ABSPATH') or die();

class Basement_Color_Rgb {
	private static $instance = null;
	private $version = '1.0.0';
	private $r = 0;
	private $g = 0;
	private $b = 0;
	private $opacity = 1;

	public function __construct( $rgb ) {
		if ( is_array( $rgb ) ) {
			$this->r = !empty( $rgb[ 0 ] ) ? (int)$rgb[ 0 ] : 0 ;
			$this->g = !empty( $rgb[ 1 ] ) ? (int)$rgb[ 1 ] : 0 ;
			$this->b = !empty( $rgb[ 2 ] ) ? (int)$rgb[ 2 ] : 0 ;
		}

		$this->check_data();
	}

	public function check_data() {
		$this->r = absint( $this->r );
		if ( $this->r > 255 ) {
			$this->r = 255;
		}

		$this->g = absint( $this->g );
		if ( $this->g > 255 ) {
			$this->g = 255;
		}

		$this->b = absint( $this->b );
		if ( $this->b > 255 ) {
			$this->b = 255;
		}

		$this->check_opacity();

		return $this;
	}

	public function check_opacity() {
		if ( !$this->opacity ) {
			$this->opacity = 1;
		}
		$this->opacity = ( float )$this->opacity;
		if ( $this->opacity < 0 ) {
			$this->opacity = 0;
		} else if ( $this->opacity > 1) {
			$this->opacity = 1;
		}

		return $this;
	}

	public function set_opacity( $opacity ) {
		$this->opacity = $opacity;
		$this->check_opacity();

		return $this;
	}

	public function get_css_value() {
		if ( $this->opacity == 1 ) {
			return 'rgb(' . $this->r . ',' . $this->g . ',' . $this->b . ')';
		} else {
			return 'rgba(' . $this->r . ',' . $this->g . ',' . $this->b . ',' . $this->opacity . ')';
		}
	}
}