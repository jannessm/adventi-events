<?php

include_once dirname(__FILE__) . '/data-extractor.php';

/**
 * Handles my AJAX request.
 */
function update_events_handler() {
    check_ajax_referer( 'update_events' );
	
    $plan_url = get_option( 'adventi_events_options' )['adventi_events_field_preacher_plan'];
    $church = get_option( 'adventi_events_options' )['adventi_events_field_church_name'];

    $extractor = new AdventiEventsDataExtractor($plan_url, $church);

    wp_send_json($extractor->get_data());
}
