<?php

add_shortcode('adventi_events_header', 'adventi_events_header');
function adventi_events_header($atts = [], $content = '', $tag = '') {
    $content .= adventi_events_date($atts) . '<br>';
    $content .= adventi_events_preacher($atts) . '<br>';
    $content .= adventi_events_location($atts) . '<br>';
    return $content;
}

add_shortcode('adventi_events_preacher', 'adventi_events_preacher');
function adventi_events_preacher($atts = [], $content = '', $tag = '') {
    $preacher = get_post_meta( get_post()->ID, '_adventi_events_meta_preacher', true );
    
    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts(
		array(
			'label' => TRUE,
		), $atts, $tag
	);

    if ($atts['label']) {
        return 'Prediger: ' . $preacher;
    } else {
        return $preacher;
    }
}

add_shortcode('adventi_events_date', 'adventi_events_date');
function adventi_events_date($atts = [], $content = '', $tag = '') {
    $date = get_post_meta( get_post()->ID, '_adventi_events_meta_date', true );
    
    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts(
		array(
			'label' => TRUE,
            'format' => 'd.m.Y, H:i'
		), $atts, $tag
	);

    $date = new DateTime($date);

    if ($atts['label']) {
        return 'Datum: ' . $date->format($atts['format']);
    } else {
        return $date->format($atts['format']);
    }
}

add_shortcode('adventi_events_location', 'adventi_events_location');
function adventi_events_location($atts = [], $content = '', $tag = '') {
    $location = get_post_meta( get_post()->ID, '_adventi_events_meta_location', true );
    
    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts(
		array(
			'label' => TRUE,
		), $atts, $tag
	);

    if ($atts['label']) {
        return 'Ort: ' . $location;
    } else {
        return $location;
    }
}

add_shortcode('adventi_events_map', 'adventi_events_map');
function adventi_events_map($atts = [], $content = '', $tag = '') {
    $post = get_post();
    $options = get_option( 'adventi_events_options' );

    $location = get_post_meta( $post->ID, '_adventi_events_meta_location', true );
    $location_point = get_post_meta( $post->ID, '_adventi_events_meta_location_point', true ); 

    $location = $location !== '' ? $location : $options['adventi_events_field_church_location'];
    $location_point = $location_point !== '' ? $location_point : $default_point;

    adventi_events_enqueue_leaflet_scripts_read_only();

    wp_localize_script(
        'leaflet-script-read-only',
        'leaflet_options',
        array(
            'location' => $location,
            'location_point' => $location_point,
            'map_id' => 'adventi_events_map',
        )
    );

    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts(
		array(
			'height' => '300px',
		), $atts, $tag
	);

    $content .= '<div id="adventi_events_map" class="adventi_events_map" style="height:'.$atts['height'].'"></div>';

    return $content;
}