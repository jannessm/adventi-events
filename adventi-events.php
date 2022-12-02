<?php
/**
 * Plugin Name: Adventi Events
 * Description: Automatically import all services from predigtplan.adventisten.de
 */

include_once  dirname(__FILE__) . '/settings/settings.php';
include_once dirname(__FILE__) . '/activation-hooks.php';

register_activation_hook( __FILE__, 'ad_ev_activate_plugin' );
register_deactivation_hook( __FILE__, 'ad_ev_deactivate_plugin' );

include_once dirname(__FILE__) . '/css/enqueue-styles.php'; // load js scripts
add_action( 'admin_enqueue_scripts', 'ad_ev_enqueue_leaflet_styles' );
add_action( 'wp_enqueue_scripts', 'ad_ev_enqueue_leaflet_styles_read_only' );
add_action( 'wp_enqueue_scripts', 'ad_ev_enqueue_sidebar_styles' );

include_once dirname(__FILE__) . '/js/enqueue-scripts.php'; // load js scripts
add_action( 'wp_enqueue_scripts', 'ad_ev_enqueue_leaflet_lib' );
add_action( 'wp_enqueue_scripts', 'ad_ev_enqueue_leaflet_scripts_read_only' );
add_action( 'admin_enqueue_scripts', 'ad_ev_enqueue_leaflet_lib' );
add_action( 'admin_enqueue_scripts', 'ad_ev_enqueue_admin_scripts' );
add_action( 'admin_enqueue_scripts', 'ad_ev_enqueue_leaflet_scripts' );
add_action( 'admin_enqueue_scripts', 'ad_ev_enqueue_media_scripts' );


include_once dirname(__FILE__) . '/ajax-handler.php';
add_action( 'wp_ajax_update_events', 'ad_ev_update_events_handler' );


include_once dirname(__FILE__) . '/meta-boxes.php';
add_action( 'add_meta_boxes', [ 'AdventiEventsMetaBox', 'add' ] );
add_action( 'save_post', [ 'AdventiEventsMetaBox', 'save' ] );

include_once dirname(__FILE__) . '/shortcodes.php';
include_once dirname(__FILE__) . '/list-addons.php';
include_once dirname(__FILE__) . '/cron.php';