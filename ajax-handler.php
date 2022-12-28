<?php

include_once dirname(__FILE__) . '/data-extractor.php';
include_once dirname(__FILE__) . '/page-manager.php';

/**
 * Handles my AJAX request.
 */
function ad_ev_update_events_handler() {
    check_ajax_referer( 'update_events' );
	
    $events = ad_ev_update();

    $new_events = [];

    foreach ($events as $e) {
        $new_events[$e->original_input] = $e->preacher . ' <-> '. $e->date->format('d.m.Y H:i');
    }

    wp_send_json($new_events);
}

function ad_ev_update() {
    $options = get_option( 'ad_ev_options' );
    $plan_url = $options[AD_EV_FIELD . 'preacher_plan'];
    $church = $options[AD_EV_FIELD . 'church_name'];
    $mail = $options[AD_EV_FIELD . 'cron_mail'];

    $extractor = new AdventiEventsDataExtractor($plan_url, $church);

    $events = $extractor->get_data();

    $manager = new AdventiEventsPageManager($events);

    $events = $manager->update();

    if ($mail != '') {
        $message = 'Update Bericht:\n\n';

        foreach ($events as $e) {
            $message .= 'Prediger: ' . $e->preacher . '\n';
            $message .= 'Ort: ' . $e->location->address . '\n';
            $message .= 'Datum: ' . $e->date->format('d.m.Y H:i') . '\n';
            $message .= 'Special: ' . $e->special . '\n\n';
        }

        wp_mail($mail, 'Events Update', $message);
    }

    return $events;
}