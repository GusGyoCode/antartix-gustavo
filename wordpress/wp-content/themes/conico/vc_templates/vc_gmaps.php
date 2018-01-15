<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $link
 * @var $size
 * @var $el_class
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gmaps
 */
$title = $link = $size = $el_class = $css = $css_animation = '';
$xs_vertical_offset = $xs_horizontal_offset = $sm_vertical_offset = $sm_horizontal_offset = $md_vertical_offset = $md_horizontal_offset = $lg_vertical_offset = $lg_horizontal_offset = 0;
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$embed_map = isset($embed_map) ? $embed_map : '';


$size = str_replace( array(
	'px',
	' ',
), array(
	'',
	'',
), $size );

if ( is_numeric( $size ) ) {
	$link = preg_replace( '/height="[0-9]*"/', 'height="' . $size . '"', $link );
}


$class_to_filter = 'wpb_gmaps_widget wpb_content_element' . ( '' === $size ? ' vc_map_responsive' : '' );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );


if($embed_map === 'yes') {

$zoom = 14; // deprecated 4.0.2. In 4.6 was moved outside from shortcode_atts
$type = 'm'; // deprecated 4.0.2
$bubble = ''; // deprecated 4.0.2

if ( '' === $link ) {
	return null;
}
$link = trim( vc_value_from_safe( $link ) );
$bubble = ( '' !== $bubble && '0' !== $bubble ) ? '&amp;iwloc=near' : '';

?>
<div class="<?php echo esc_attr( $css_class ); ?>">
	<?php echo wpb_widget_title( array(
		'title' => $title,
		'extraclass' => 'wpb_map_heading',
	) ); ?>
	<div class="wpb_wrapper">
		<div class="wpb_map_wraper">
			<?php

			if ( preg_match( '/^\<iframe/', $link ) ) {
				if($size === 'standard') {
					$link = preg_replace( '/height="[0-9]*"/', 'height="660"', $link );
				}

				printf('%s', $link);
			} else {
				// TODO: refactor or remove outdated/deprecated attributes that is not mapped in gmaps.
				echo sprintf('<%1$s width="100%" height="' . $size . '" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="' . $link . '&amp;t=' . $type . '&amp;z=' . $zoom . '&amp;output=embed' . $bubble . '"></%1$s>',
					'iframe'
					);
			}
			?>
		</div>
	</div>
</div>


<?php } elseif ($embed_map === 'no') {

	$latitude = isset($latitude) && is_numeric($latitude) ? $latitude : '';
	$longitude = isset($longitude) && is_numeric($longitude) ? $longitude : '';
	$zoom = isset($zoom) && is_numeric($zoom) ? $zoom : 12;
	$full_screen  = isset($full_screen) ? $full_screen : '';
	$style = isset($style) ? $style : '';
	$marker_position = isset($marker_position) ? $marker_position : false;

	$dom = new DOMDocument();

	$root_map_wrapper = $dom->appendChild( $dom->createElement( 'div' ) );
	$root_map_wrapper->setAttribute( 'class', esc_attr( $css_class )  );

	if(!empty($title)) {
		$h2 = $root_map_wrapper->appendChild( $dom->createElement( 'h2', esc_attr($title) ) );
		$h2->setAttribute( 'class', esc_attr( 'wpb_heading wpb_map_heading' ) );
	}

	$map_wrapper = $root_map_wrapper->appendChild( $dom->createElement( 'div' ) );
	$map_wrapper->setAttribute( 'class', esc_attr('google-map map-wrap' )  );

	if(is_numeric($size)) {
		$map_wrapper->setAttribute( 'style', "height:{$size}px;" );
	}

	$map = $map_wrapper->appendChild( $dom->createElement( 'div' ) );
	$map->setAttribute( 'class', esc_attr( 'google-map-container' ) );

	if ( ! empty( $style ) && $style !== 'default' ) {

		$map->setAttribute( 'data-style-name', $style  );

		switch ( $style ) {
			case 'shades_gray' :
				$style = '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]';
				break;
			case 'ultra_light_labels' :
				$style = '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
				break;
			case 'pastel_tones' :
				$style = '[{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":60}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"visibility":"on"},{"lightness":30}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ef8c25"},{"lightness":40}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#b6c54c"},{"lightness":40},{"saturation":-40}]},{}]';
				break;
		}

		$map->setAttribute( 'data-style', $style  );

	}


	if(!empty($marker_position)) {

		$lg_vertical_offset = (int)$lg_vertical_offset;
		$lg_horizontal_offset = $lg_horizontal_offset < 0 ? abs($lg_horizontal_offset) : -$lg_horizontal_offset ;


		$md_vertical_offset = (int)$md_vertical_offset;
		$md_horizontal_offset = $md_horizontal_offset < 0 ? abs($md_horizontal_offset) : -$md_horizontal_offset ;


		$sm_vertical_offset = (int)$sm_vertical_offset;
		$sm_horizontal_offset = $sm_horizontal_offset < 0 ? abs($sm_horizontal_offset) : -$sm_horizontal_offset ;

		$xs_vertical_offset = (int)$xs_vertical_offset;
		$xs_horizontal_offset = $xs_horizontal_offset < 0 ? abs($xs_horizontal_offset) : -$xs_horizontal_offset ;

		$positions = array(
			'lg' => "{$lg_horizontal_offset},{$lg_vertical_offset}",
			'md' => "{$md_horizontal_offset},{$md_vertical_offset}",
			'sm' => "{$sm_horizontal_offset},{$sm_vertical_offset},",
			'xs' => "{$xs_horizontal_offset},{$xs_vertical_offset},"
		);

		$map->setAttribute( 'data-position', wp_json_encode( $positions ) );
	}

	if ( $full_screen ) {

		$a = $map_wrapper->appendChild( $dom->createElement( 'a' ) );

		$a->setAttribute( 'class', esc_attr( 'a-map' ) );
		$a->setAttribute( 'href', esc_attr( '#map-modal' ) );
		$a->setAttribute( 'data-toggle', esc_attr( 'modal' ) );

		$i = $a->appendChild( $dom->createElement( 'i' ) );
		$i->setAttribute( 'class', esc_attr( ' si-arrows-right-corner' ) );

	}


	$center_map = '51.513447,-0.1159143';
	if ( $longitude && $latitude ) {
		$center_map = $latitude . ',' . $longitude;
	}
	$map->setAttribute( 'data-center', esc_attr( $center_map ) );

	$map->setAttribute( 'data-markers', esc_attr( $center_map ) );

	$map->setAttribute( 'data-zoom', is_numeric($zoom) ? esc_attr( $zoom ) : '12' );


	add_action( 'wp_footer', function() {

		$dom = new DOMDocument( '1.0', 'UTF-8' );

		$container = $dom->appendChild( $dom->createElement( 'div' ) );
		$container->setAttribute( 'class', esc_attr( 'modal map-modal' ) );
		$container->setAttribute( 'id', esc_attr('map-modal') );

		$close = $container->appendChild( $dom->createElement( 'a' ) );
		$close->setAttribute( 'href', esc_attr( '#' ) );
		$close->setAttribute( 'class', esc_attr( 'map-close' ) );
		$close->setAttribute( 'data-dismiss', esc_attr( 'modal' ) );

		$i = $close->appendChild( $dom->createElement( 'i' ) );
		$i->setAttribute( 'class', esc_attr( 'icon-cross' ) );

		$divtable = $container->appendChild( $dom->createElement( 'div' ) );
		$divtable->setAttribute( 'class', 'google-map-popup' );

		printf('%s', $dom->saveHTML());

	} );

	printf('%s', $dom->saveHTML());
}

?>