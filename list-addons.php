<?php

include_once dirname(__FILE__) . '/constants.php';

function ad_ev_add_date_columns($columns) {
    return array_merge(
        $columns,
        array('event_date' => __('Event Datum'), 'preacher' => __('Prediger')),
    );
}
add_filter('manage_event_posts_columns' , 'ad_ev_add_date_columns');

// Add action to the manage post column to display the data
/**
 * Display data in new columns
 *
 * @param  $column Current column
 *
 * @return Data for the column
 */
function ad_ev_custom_columns( $column ) {
    global $post;
	switch ( $column ) {
		case 'event_date':
			echo (new DateTime(get_post_meta( $post->ID, AD_EV_META . 'date', true )))->format('d.m.Y H:i');
            break;
        case 'preacher':
            echo get_post_meta( $post->ID, AD_EV_META . 'preacher', true );
		    break;
	}
}
add_action( 'manage_event_posts_custom_column' , 'ad_ev_custom_columns' );
