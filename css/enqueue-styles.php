<?php
function adventi_events_enqueue_leaflet_styles($hook) {
    if ( 'post-new.php' !== $hook && 'post.php' !== $hook ) {
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