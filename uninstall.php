<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

include_once dirname(__FILE__) . '/constants.php';
include_once dirname(__FILE__) . '/event.php';

global $wpdb; // Must have this or else!

$postmeta_table = $wpdb->postmeta;
$posts_table = $wpdb->posts;

foreach (AdventiEvent::fields as $field) {
	$wpdb->query("DELETE FROM " . $postmeta_table . " WHERE meta_key = '". AD_EV_META . $field . "'");	
}
$wpdb->query("DELETE FROM " . $posts_table . " WHERE post_type = 'event'");