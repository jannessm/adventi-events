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
        $added = [];

        foreach ($this->events as $e) {
            //check if event exists and add if missing
            if (!!$e->original_input && !in_array($e->original_input, $existing_inputs)) {
                $this->add_event($e);
                array_push($added, $e);
            }
        }

        return $added;
    }

    private function add_event($event) {
        $page_slug = $event->is_special() ? $event->special->value : 'Gottesdienst'; // Slug of the Post

        $new_page = array(
            'post_type'     => 'event', 				// Post Type Slug eg: 'page', 'post'
            'post_title'    => $page_slug,	        // Title of the Content
            'post_content'  => $this->get_default_content($event),	// Content
            'post_status'   => 'publish',			// Post Status
            'post_author'   => 1,					// Post Author ID
            'post_name'     => $page_slug,			// Slug of the Post
            'meta_input'    => $event->get_meta_array(),
        );

        $new_page_id = wp_insert_post($new_page);
    }

    private function get_default_content($event) {
		$options = get_option( 'ad_ev_options' );
        
        switch ($event->special) {
            case AdventiEventsSpecials::A:
                return $options[AD_EV_FIELD . 'default_communion_text'];
            case AdventiEventsSpecials::E:
                return $options[AD_EV_FIELD . 'default_thanks_giving_text'];
            case AdventiEventsSpecials::T:
                return $options[AD_EV_FIELD . 'default_baptism_text'];
            case AdventiEventsSpecials::J:
                return $options[AD_EV_FIELD . 'default_youth_service_text'];
            case AdventiEventsSpecials::G:
                return $options[AD_EV_FIELD . 'default_community_hour_text'];
            case AdventiEventsSpecials::W:
                return $options[AD_EV_FIELD . 'default_forest_service_text'];
            default:
                return $options[AD_EV_FIELD . 'default_text'];
        }
        return '';
    }
}