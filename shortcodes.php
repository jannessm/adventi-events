<?php

add_shortcode('ad_ev_header', 'ad_ev_header');
function ad_ev_header($atts = [], $content = '', $tag = '') {
    $content .= ad_ev_date($atts);
    $content .= ad_ev_preacher($atts);
    $content .= ad_ev_location($atts);
    return $content;
}

add_shortcode('ad_ev_preacher', 'ad_ev_preacher');
function ad_ev_preacher($atts = [], $content = '', $tag = '') {
    $preacher = get_post_meta( get_post()->ID, AD_EV_META . 'preacher', true );
    
    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts(
		array(
			'label' => 'TRUE',
		), $atts, $tag
	);

    return _ad_ev_label_value('Prediger', $preacher, strtoupper($atts['label']) == 'TRUE');
}

add_shortcode('ad_ev_date', 'ad_ev_date');
function ad_ev_date($atts = [], $content = '', $tag = '') {
    $date = get_post_meta( get_post()->ID, AD_EV_META . 'date', true );
    
    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts(
		array(
			'label' => 'TRUE',
            'format' => 'd.m.Y, H:i'
		), $atts, $tag
	);

    $date = new DateTime($date);

    return _ad_ev_label_value('Datum', $date->format($atts['format']), strtoupper($atts['label']) == 'TRUE');
}

add_shortcode('ad_ev_location', 'ad_ev_location');
function ad_ev_location($atts = [], $content = '', $tag = '') {
    $location = get_post_meta( get_post()->ID, AD_EV_META . 'location', true );
    
    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts(
		array(
			'label' => 'TRUE',
		), $atts, $tag
	);

    return _ad_ev_label_value('Ort', $location, strtoupper($atts['label']) == 'TRUE');
}

add_shortcode('ad_ev_map', 'ad_ev_map');
function ad_ev_map($atts = [], $content = '', $tag = '') {
    $post = get_post();
    $options = get_option( 'ad_ev_options' );

    $location = get_post_meta( $post->ID, AD_EV_META . 'location', true );
    $location_lng = get_post_meta( $post->ID, AD_EV_META . 'location_lng', true ); 
    $location_lat = get_post_meta( $post->ID, AD_EV_META . 'location_lat', true ); 

    $location = $location !== '' ? $location : $options[AD_EV_FIELD . 'church_location'];
    $location_lng = $location_lng !== '' ? $location_lng : $options[AD_EV_FIELD . 'church_lng'];
    $location_lat = $location_lat !== '' ? $location_lat : $options[AD_EV_FIELD . 'church_lat'];

    ad_ev_enqueue_leaflet_scripts_read_only();

    wp_localize_script(
        'leaflet-script-read-only',
        'leaflet_options',
        array(
            'location' => $location,
            'location_lng' => $location_lng,
            'location_lat' => $location_lat,
            'map_id' => 'ad_ev_map',
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

    $content .= '<div id="ad_ev_map" class="ad_ev_map" style="height:'.$atts['height'].'"></div>';

    return $content;
}

add_shortcode('ad_ev_sidebar', 'ad_ev_sidebar');
function ad_ev_sidebar($atts = [], $content = '', $tag = '') {
    // normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

    // override default attributes with user attributes
    $atts = shortcode_atts(
        array(
            'event_page' => '?page_id=9',
            'n' => 5,
        ), $atts, $tag
    );

    $event_page = $atts['event_page'];

    ?>
    <div id="ad_ev_sidebar">

        <div class="ad_ev_sidebar_event ad_ev_sidebar_top"><a href="<?php echo $event_page; ?>">VERANSTALTUNGEN</a></div>

    <?php
        $args = array(
            'post_type' => 'event',
            'posts_per_page' => $atts['n'] + 1,
            'orderby' => 'meta_value',
            'meta_key' => AD_EV_META . 'date',
            'meta_query' => [
                [
                    'key' => AD_EV_META . 'date',
                    'value' => (new DateTime('now'))->format('Y-m-d'),
                    'compare' => '>',
                    'type' => 'DATE',
                ]
            ],
            'order' => 'ASC'
        );

        $query = new WP_Query($args);

        $post_counter = 0;
        
        if ($query->have_posts()) :
            while ($query->have_posts() && $post_counter < 5) :
                $query->the_post();
                $event = AdventiEvent::from_post(get_the_ID());
        ?>
                    
                <a href="<?php the_permalink();?>">
                    <div class="ad_ev_sidebar_event">
                        <h4><?php echo strtoupper(the_title('','',false)); ?></h4>
                        <hr>
                        <?php echo $event->date->format('d.m.Y, H:i');?><br>
                        <?php echo $event->location->address; ?>
                    </div>
                </a>

        <?php
                $post_counter++;
            endwhile;
        endif;
        ?>
            <a href="<?php echo $event_page ?>" style="margin: 10px"> Mehr ></a>
        </div>
    <?php
}

add_shortcode('ad_ev_previews', 'ad_ev_previews');
function ad_ev_previews($atts = [], $content = '', $tag = '') {

}

function _ad_ev_label_value($label, $value, $add_label) {
    $o = '<p>';

    if ($add_label) {
        $o .= '<b style="width: 100px; display: inline-block">' . $label . ':</b> ';
    }
    
    return $o . $value . '</p>';
}