<?php

function ad_ev_init() {
    $labels = array(
        'name'               => _x( 'Veranstaltungen', 'post type general name', 'adventi-events' ),
        'singular_name'      => _x( 'Veranstaltung', 'post type singular name', 'adventi-events' ),
        'menu_name'          => _x( 'Veranstaltungen', 'admin menu', 'adventi-events' ),
        'name_admin_bar'     => _x( 'Veranstaltungen', 'add new on admin bar', 'adventi-events' ),
        'add_new'            => _x( 'Veranstaltung hinzufügen', 'event', 'adventi-events' ),
        'add_new_item'       => __( 'Neue Veranstatung', 'adventi-events' ),
        'new_item'           => __( 'Neue Veranstatung', 'adventi-events' ),
        'edit_item'          => __( 'Veranstatung bearbeiten', 'adventi-events' ),
        'view_item'          => __( 'Veranstatung betrachten', 'adventi-events' ),
        'all_items'          => __( 'Alle Veranstaltungen', 'adventi-events' ),
        'search_items'       => __( 'Veranstaltung suchen', 'adventi-events' ),
        'parent_item_colon'  => __( 'Parent Veranstaltungen:', 'adventi-events' ),
        'not_found'          => __( 'Keine Veranstatung gefunden.', 'adventi-events' ),
        'not_found_in_trash' => __( 'Keine Veranstatung im Trash.', 'adventi-events' )
    );
    
    register_post_type('event', array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'adventi-events' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'		 => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'event' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-buddicons-groups',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'id' )
    ));
}
add_action('init', 'ad_ev_init');

function ad_ev_activate_plugin() {
    ad_ev_init();
    ad_ev_settings_init();
    // Clear the permalinks after the post type has been registered.
	flush_rewrite_rules();
}

function ad_ev_deactivate_plugin() {
    unregister_post_type('event');
    // Clear the permalinks after the post type has been registered.
	flush_rewrite_rules();
}