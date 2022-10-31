<?php

function adventi_events_enqueue_scripts( $hook ) {
    if ( 'event_page_adventi_events' !== $hook ) {
		return;
	}

    wp_enqueue_script(
        'update-script',
        plugins_url( '/update-events.js', __FILE__),
        array( 'jquery' ),
        '1.0.,0',
        true
    );
}