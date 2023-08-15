<?php

include_once dirname(__FILE__) . '/constants.php';

function ad_ev_add_date_columns($columns) {
	$date_col = $columns['date'];
	unset( $columns['date'] );
	unset( $columns['comments'] );
	
    return array_merge(
        $columns,
        array('event_date' => __('Event Datum'),
			  'preacher' => __('Prediger'),
			  'date' => $date_col),
    );
}
add_filter('manage_event_posts_columns' , 'ad_ev_add_date_columns');

function ad_ev_sortable_columns( $columns ) {
	$columns['event_date'] = 'event_date';
	$columns['preacher'] = 'preacher';
	return $columns;
}
add_filter('manage_edit-event_sortable_columns', 'ad_ev_sortable_columns');

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

function ad_ev_custom_column_query( $query ) {
	$orderby = $query->get( 'orderby' );

    if ( 'event_date' == $orderby ) {

        $meta_query = array(
            'relation' => 'OR',
            array(
                'key' => AD_EV_META . 'date',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key' => AD_EV_META . 'date',
            ),
        );

        $query->set( 'meta_query', $meta_query );
        $query->set( 'orderby', 'meta_value' );
    }
}
add_action( 'pre_get_posts', 'ad_ev_custom_column_query');
