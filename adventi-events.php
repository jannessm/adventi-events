<?php
/**
 * Plugin Name: Adventi Events
 * Description: Automatically import all services from predigtplan.adventisten.de
 */

include_once  dirname(__FILE__) . '/settings.php';
include_once dirname(__FILE__) . '/activation-hooks.php';
include_once dirname(__FILE__) . '/js/enqueue-scripts.php'; // load js scripts
include_once dirname(__FILE__) . '/ajax-handler.php';

register_activation_hook( __FILE__, 'adventi_events_activate_plugin' );
register_deactivation_hook( __FILE__, 'adventi_events_deactivate_plugin' );

add_action( 'admin_enqueue_scripts', 'adventi_events_enqueue_scripts' );
add_action( 'wp_ajax_update_events', 'update_events_handler' );