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
	
	if (!$preacher) return '';
    
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
    $is_real = get_post_meta( get_post()->ID, AD_EV_META . 'is_real', true ) == 'true';
    $is_zoom = get_post_meta( get_post()->ID, AD_EV_META . 'is_zoom', true ) == 'true';

	$html = '';
	if ($is_real) {
		$html .= ad_ev_location_present($atts, $content, $tag);
	}
	if ($is_zoom) {
		$html .= ad_ev_zoom($atts, $content, $tag);
	}

    return $html;
}
function ad_ev_location_present($atts = [], $content = '', $tag = '') {
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


function ad_ev_zoom($atts = [], $content = '', $tag = '') {
    $is_zoom = get_post_meta( get_post()->ID, AD_EV_META . 'is_zoom', true ) == 'true';
    $zoom_id = get_post_meta( get_post()->ID, AD_EV_META . 'zoom_id', true );
    $zoom_pwd = get_post_meta( get_post()->ID, AD_EV_META . 'zoom_pwd', true );
    $zoom_link = get_post_meta( get_post()->ID, AD_EV_META . 'zoom_link', true );
    $zoom_tel = get_post_meta( get_post()->ID, AD_EV_META . 'zoom_tel', true );
    
    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts(
		array(
			'label' => 'TRUE',
		), $atts, $tag
	);
    $label = strtoupper($atts['label']) == 'TRUE';

    wp_localize_script(
        'zoom-details',
        'zoom_details',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'post_id' => get_post()->ID,
            'label' => $label
        )
    );

    $html = '';

    if ($is_zoom) {
        if (!!$zoom_id) {
            $html .= _ad_ev_label_value('Zoom ID', $zoom_id, $label);
        }
        if (!!$zoom_pwd || !!$zoom_link || !!$zoom_tel) {
            $html .= '<form id="ad_ev_zoom_details">' . do_shortcode('[hcaptcha]') . '<input id="ad_ev_zoom_details" type="submit" value="Zugangsdaten"></form>';
            $html .= '<div id="ad_ev_details"></div>';
        }
    }

    return $html;
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
            'event_page' => '/veranstaltungen',
            'n' => 5,
        ), $atts, $tag
    );

    $event_page = $atts['event_page'];

    $content = '
    <div id="ad_ev_sidebar">
        <div class="ad_ev_sidebar_event ad_ev_sidebar_top">
            <a href="'. $event_page .'">VERANSTALTUNGEN</a>
        </div>
    ';

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

        $recurrent = [];
        
        if ($query->have_posts()) {
            while ($query->have_posts() && $post_counter < $atts['n']) {
                $query->the_post();
                $event = AdventiEvent::from_post(get_the_ID());

                foreach($recurrent as $r_event) {
                    if ($r_event->date < $event->date && $post_counter < $atts['n']) {
                        $content .= get_event_link($r_event);
                        $post_counter++;
                    }
                    if ($post_counter >= $atts['n']) {
                        break;
                    }
                }

                if ($post_counter >= $atts['n']) {
                    break;
                }

                if ($event->is_recurrent()) {
                    $recurrent[] = $event;
                }

                $content .= get_event_link($event);

                foreach ($recurrent as $r_event) {
                    $date = clone $event->date;
                    $r_event->update_date($date->add(new DateInterval('P1D')));
                }
                $post_counter++;
            }
        }
        
        $content .= '
            <a href="' . $event_page . '" style="margin: 10px"> Mehr ></a>
        </div>';
    
    return $content;
}

function get_event_link($event) {
    $post = get_post($event->post_id);
	$location = '';
	if ($event->location->is_real) {
		$location = $event->location->address;
	} elseif ($event->zoom->is_zoom) {
		$location = 'Zoom';
	}
    return '
    <a href="'. get_permalink($post) .'">
        <div class="ad_ev_sidebar_event">
            <h4>'. strtoupper(get_the_title($post)) .'</h4>
            <hr>
            ' . $event->date->format('d.m.Y, H:i') .'<br>
            ' . $location . '
        </div>
    </a>';
}

add_shortcode('ad_ev_previews', 'ad_ev_previews');
function ad_ev_previews($atts = [], $content = '', $tag = '') {
    // normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

    // override default attributes with user attributes
    $atts = shortcode_atts(
        array(
            'n' => 6,
            'type' => 'all',
            'cols' => 3,
        ), $atts, $tag
    );

    $meta_query = ['relation' => 'OR'];
    if ($atts['type'] == 'special' ||
        $atts['type'] == 'non-normal' ||
        $atts['type'] == 'non-recurrent' ||
        $atts['type'] == 'all') {
        array_push($meta_query, [
            'relation' => 'AND',
            [
                'key' => AD_EV_META . 'special',
                'value' => array_merge(['TRUE'], AdventiEventsSpecials::values()),
                'compare' => 'IN'
            ],
            [
                'key' => AD_EV_META . 'date',
                'value' => (new DateTime('now'))->format('Y-m-d'),
                'compare' => '>',
                'type' => 'DATE',
            ],
			[
				'key' => AD_EV_META . 'recurrence',
				'value' => AdventiEventsIntervals::ONCE->value,
				'compare' => '='
        	]
        ]);
    }
    if ($atts['type'] == 'normal' ||
        $atts['type'] == 'non-special' ||
        $atts['type'] == 'non-recurrent' ||
        $atts['type'] == 'all') {
        array_push($meta_query, [
            'relation' => 'AND',
            [
                'key' => AD_EV_META . 'special',
                'value' => array_merge(['TRUE'], AdventiEventsSpecials::values()),
                'compare' => 'NOT IN'
            ],
            [
                'key' => AD_EV_META . 'date',
                'value' => (new DateTime('now'))->format('Y-m-d'),
                'compare' => '>',
                'type' => 'DATE',
            ],
			[
				'key' => AD_EV_META . 'recurrence',
				'value' => AdventiEventsIntervals::ONCE->value,
				'compare' => '='
        	]
        ]);
    }
    if ($atts['type'] == 'recurrent' ||
        $atts['type'] == 'non-special' ||
        $atts['type'] == 'non-normal' ||
        $atts['type'] == 'all') {
        array_push($meta_query, [
            'key' => AD_EV_META . 'recurrence',
            'value' => AdventiEventsIntervals::ONCE->value,
            'compare' => '!='
        ]);
    }
    
    $args = array(
        'post_type' => 'event',
        'posts_per_page' => $atts['n'] + 1,
        'orderby' => 'meta_value',
        'meta_key' => AD_EV_META . 'date',
        'meta_query' => $meta_query,
        'order' => 'ASC'
    );

    $query = new WP_Query($args);

    $post_counter = 0;

    $formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, 
    IntlDateFormatter::NONE, NULL, NULL, "MMM");

    $element_width = 'calc((100% - 64px * ' . $atts['cols'] - 1 . ') / ' . $atts['cols'] . ')';

    $content = '<div class="ad_events_row">';

    if ($query->have_posts()) {
        while ($query->have_posts() && $post_counter < $atts['n']) {

            $query->the_post();
            $event = AdventiEvent::from_post(get_the_ID());
            $img = wp_get_attachment_image_url($event->image_id, 'medium_size');
            $month = $formatter->format($event->date);

            $location = '';

            if ($event->location->is_real) {
                $location = $event->location->address;
            } elseif ($event->zoom->is_zoom && !!$event->zoom->id) {
                $location = 'Zoom: ' . $event->zoom->id;
            }

            $content .= '
            <a class="ad_event"
            href="'. get_permalink() .'" 
            style="background-image: url('.$img.'); width: '. $element_width .'">
                <div class="ad_event_date">
                <h4 style="margin:0">'. $month .'</h4>
                <h2 style="margin:0">'. $event->date->format('d.') .'</h2>
                </div>
                <div class="ad_event_text">
                    <h4>'. strtoupper(the_title('','',false)) .'</h4>
                    '. wp_trim_words(get_the_excerpt(), 30) .'
                </div>
                <div class="ad_event_location">
                    <div id="ad_ev_loc"></div>
                    <div class="ad_event_location_text">
                        '. strtoupper($location) .'
                    </div>
                </div>
            </a>';

            $post_counter++;
        }
    } else {
        $content .= 'Keine Veranstaltungen geplant.';
    }

    $content .= '</div>';

    return $content;
}

function _ad_ev_label_value($label, $value, $add_label) {
    $o = '<p>';

    if ($add_label) {
        $o .= '<b style="width: 100px; display: inline-block">' . $label . ':</b> ';
    }
    
    return $o . $value . '</p>';
}