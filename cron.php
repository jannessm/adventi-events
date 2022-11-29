<?php

include_once dirname(__FILE__) . '/ajax-handler.php';
include_once dirname(__FILE__) . '/constants.php';

add_action( 'ad_ev_cron_hook', 'ad_ev_cron_exec' );
function ad_ev_cron_exec() {
    $options = get_option( 'ad_ev_options' );

    if ( ! wp_next_scheduled( 'ad_ev_cron_hook' ) ) {
        wp_schedule_event( time(), 'weekly', 'ad_ev_cron_hook' );
    }

    if ($options[AD_EV_FIELD . 'cron'] != '') {
       ad_ev_update();
    }
}