<?php
function ad_ev_enqueue_leaflet_styles($hook) {
    if ( 'post-new.php' !== $hook && 'post.php' !== $hook && 'event_page_adventi_events' !== $hook) {
		return;
	}

    wp_enqueue_style(
        'leaflet-styles',
        plugins_url( '/leaflet@1.9.2.css', __FILE__),
        array(),
        '1.9.2'
    );

    wp_enqueue_style(
        'meta-box-styles',
        plugins_url( '/meta-box-styles.css', __FILE__),
        array(),
        '1.0.0'
    );
}

function ad_ev_enqueue_leaflet_styles_read_only($hook) {
    wp_enqueue_style(
        'leaflet-styles',
        plugins_url( '/leaflet@1.9.2.css', __FILE__),
        array(),
        '1.9.2'
    );
}

function ad_ev_enqueue_sidebar_styles() {
    wp_enqueue_style(
        'sidbar-styles',
        plugins_url('/sidebar.css', __FILE__),
        array(),
        '1.0.0'
    );
}

function ad_ev_enqueue_preview_styles() {
    wp_enqueue_style(
        'preview-styles',
        plugins_url('/preview.css', __FILE__),
        array(),
        '1.0.0'
    );
}