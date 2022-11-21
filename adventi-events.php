<?php
/**
 * Plugin Name: Adventi Events
 * Description: Automatically import all services from predigtplan.adventisten.de
 */

include_once  dirname(__FILE__) . '/settings.php';
include_once dirname(__FILE__) . '/activation-hooks.php';

register_activation_hook( __FILE__, 'adventi_events_activate_plugin' );
register_deactivation_hook( __FILE__, 'adventi_events_deactivate_plugin' );

include_once dirname(__FILE__) . '/css/enqueue-styles.php'; // load js scripts
add_action( 'admin_enqueue_scripts', 'adventi_events_enqueue_leaflet_styles' );

include_once dirname(__FILE__) . '/js/enqueue-scripts.php'; // load js scripts
add_action( 'admin_enqueue_scripts', 'adventi_events_enqueue_admin_scripts' );
add_action( 'admin_enqueue_scripts', 'adventi_events_enqueue_leaflet_scripts' );
add_action( 'admin_enqueue_scripts', 'adventi_events_enqueue_media_scripts' );


include_once dirname(__FILE__) . '/ajax-handler.php';
add_action( 'wp_ajax_update_events', 'update_events_handler' );


include_once dirname(__FILE__) . '/meta-boxes.php';
add_action( 'add_meta_boxes', [ 'Adventi_Events_Meta_Box', 'add' ] );
add_action( 'save_post', [ 'Adventi_Events_Meta_Box', 'save' ] );