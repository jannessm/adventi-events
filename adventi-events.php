<?php
/**
 * Plugin Name: Adventi Events
 * Description: Automatically import all services from predigtplan.adventisten.de
 */

include_once  dirname(__FILE__) . '/settings.php';
include_once dirname(__FILE__) . '/activation-hooks.php';

register_activation_hook( __FILE__, 'adventi_events_activate_plugin' );
register_deactivation_hook( __FILE__, 'adventi_events_deactivate_plugin' );
