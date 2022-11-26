<?php

include_once dirname(__FILE__) . '/event.php';

class AdventiEventsPageManager {
    private $events = [];
    private $existing_events = [];

    public function __construct($events) {
        $this->events = $events;
        $this->existing_events = $this->get_existing_events();
    }

    public function get_existing_events() {
        $events = [];

        $query = new WP_Query(array(
            'post_type' => 'event',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ));
        
        
        while ($query->have_posts()) {
            $query->the_post();
            array_push($events, AdventiEvent::from_post(get_the_ID()));
        }
        
        wp_reset_query();

        return $events;
    }

    public function delete_past_events() {
        $this->events = array_filter($this->events, function ($e) {return $e->date > new DateTime();});
        
        $to_delete = array_filter($this->existing_events, function ($e) {return $e->date < new DateTime();});
        $this->existing_events = array_filter($this->existing_events, function ($e) {return $e->date > new DateTime();});

        foreach($to_delete as $post) {
            wp_trash_post( $post->post_id );
        }
    }

    public function update() {
        $this->delete_past_events();
        $existing_inputs = array_column($this->existing_events, 'original_input');

        foreach ($this->events as $e) {
            //check if event exists and add if missing
            if (!!$e->original_input && !in_array($e->original_input, $existing_inputs)) {
                $this->add_event($e);
            }
        }

        return $this->events;
    }

    private function add_event($event) {
        $page_slug = !!$event->special ? $event->special : 'Gottesdienst'; // Slug of the Post
        $new_page = array(
            'post_type'     => 'event', 				// Post Type Slug eg: 'page', 'post'
            'post_title'    => $page_slug,	        // Title of the Content
            'post_content'  => $this->get_default_content($event),	// Content
            'post_status'   => 'publish',			// Post Status
            'post_author'   => 1,					// Post Author ID
            'post_name'     => $page_slug,			// Slug of the Post
            'meta_input'    => $event->get_meta_array()
        );

        $new_page_id = wp_insert_post($new_page);
    }

    private function get_default_content($event) {
        return '';
    }
}