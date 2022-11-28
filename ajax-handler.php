<?php

include_once dirname(__FILE__) . '/data-extractor.php';
include_once dirname(__FILE__) . '/page-manager.php';

/**
 * Handles my AJAX request.
 */
function update_events_handler() {
    check_ajax_referer( 'update_events' );
	
    $plan_url = get_option( 'ad_ev_options' )['ad_ev_field_preacher_plan'];
    $church = get_option( 'ad_ev_options' )['ad_ev_field_church_name'];

    $extractor = new AdventiEventsDataExtractor($plan_url, $church);

    $events = $extractor->get_data();

    $manager = new AdventiEventsPageManager($events);

    $events = $manager->update();

    $new_events = [];

    foreach ($events as $e) {
        $new_events[$e->original_input] = $e->preacher . '---'. $e->date->format('d.m.Y H:i');
    }

    wp_send_json($new_events);
}
