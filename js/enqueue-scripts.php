<?php

function adventi_events_enqueue_admin_scripts( $hook ) {
    if ( 'event_page_adventi_events' !== $hook ) {
		return;
	}

    wp_enqueue_script(
        'update-script',
        plugins_url( '/update-events.js', __FILE__),
        array( 'jquery' ),
        '1.0.0',
        true
    );
}

function adventi_events_enqueue_leaflet_scripts($hook) {
    if ( 'post-new.php' !== $hook && 'post.php' !== $hook ) {
		return;
	}

    wp_enqueue_script(
        'leaflet-script',
        plugins_url( '/leaflet-script.js', __FILE__),
        array( 'jquery', 'leaflet' ),
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'leaflet',
        plugins_url( '/leaflet@1.9.2.js', __FILE__),
        array(),
        '1.9.2',
        true
    );
}

function adventi_events_enqueue_media_scripts($hook) {
    if ( 'post-new.php' !== $hook && 'post.php' !== $hook && 'event_page_adventi_events' !== $hook) {
		return;
	}

    wp_enqueue_media();

    wp_enqueue_script(
        'image-select',
        plugins_url( '/image-select.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );
}