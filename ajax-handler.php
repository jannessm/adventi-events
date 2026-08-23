<?php

include_once dirname(__FILE__) . '/data-extractor.php';
include_once dirname(__FILE__) . '/page-manager.php';

/**
 * Handles my AJAX request.
 */
function ad_ev_delete_events_handler() {
    check_ajax_referer( 'update_events' );

    $manager = new AdventiEventsPageManager([]);

    wp_send_json($manager->delete_added_posts());
}

/**
 * Handles my AJAX request.
 */
function ad_ev_update_events_handler() {
    check_ajax_referer( 'update_events' );
	
    $events = ad_ev_update();

    $new_events = ['added' => [], 'updated' => []];

    foreach ($events['added'] as $e) {
        $new_events['added'][$e->original_input] = $e->preacher . ' <-> '. $e->date->format('d.m.Y H:i');
    }
	foreach ($events['updated'] as $e) {
        $new_events['updated'][$e->original_input] = $e->preacher . ' <-> '. $e->date->format('d.m.Y H:i');
    }

    wp_send_json($new_events);
}

function ad_ev_update() {
    $options = get_option( 'ad_ev_options' );

    $plan_url = $options[AD_EV_FIELD . 'preacher_plan'];
    $church = $options[AD_EV_FIELD . 'church_name'];
    $mail = $options[AD_EV_FIELD . 'cron_mail'];

    $extractor = new AdventiEventsDataExtractor($plan_url, $church);

    $events = $extractor->get_data();

    $manager = new AdventiEventsPageManager($events);

    $events = $manager->update();

    if ($mail != '') {
        $message = "Update Bericht:

    Added:
";
		
		if (sizeof($events['added']) == 0) {
			$message .= "        Keine Änderungen";
		}

        foreach ($events['added'] as $e) {
            $message .= ad_ev_event_as_str($e);
        }
		
		$message .="
		
		
    Modified:
";
		
		if (sizeof($events['updated']) == 0) {
			$message .= "        Keine Änderungen";
		}

        foreach ($events['updated'] as $e) {
            $message .= ad_ev_event_as_str($e);
        }

        wp_mail($mail, 'Events Update', $message);
    }

    return $events;
}

function ad_ev_import_csv_events_handler() {
    check_ajax_referer( 'update_events' );

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized', 403);
        return;
    }

    if (!isset($_FILES['csv_file']) || !isset($_FILES['csv_file']['tmp_name']) || $_FILES['csv_file']['tmp_name'] === '') {
        wp_send_json_error('Keine CSV Datei hochgeladen.', 400);
        return;
    }

    $content = file_get_contents($_FILES['csv_file']['tmp_name']);
    if ($content === false || trim($content) === '') {
        wp_send_json_error('CSV Datei ist leer oder ungültig.', 400);
        return;
    }

    $events = ad_ev_events_from_uploaded_csv($content);
    if (is_wp_error($events)) {
        wp_send_json_error($events->get_error_message(), 400);
        return;
    }

    $manager = new AdventiEventsPageManager($events);
    $updated_events = $manager->update();

    $new_events = ['added' => [], 'updated' => []];
    foreach ($updated_events['added'] as $e) {
        $new_events['added'][$e->original_input] = $e->preacher . ' <-> '. $e->date->format('d.m.Y H:i');
    }
    foreach ($updated_events['updated'] as $e) {
        $new_events['updated'][$e->original_input] = $e->preacher . ' <-> '. $e->date->format('d.m.Y H:i');
    }

    wp_send_json($new_events);
}

function ad_ev_events_from_uploaded_csv($content) {
    $rows = preg_split('/\r\n|\n|\r/', trim($content));
    if (!$rows || count($rows) < 2) {
        return new WP_Error('invalid_csv', 'CSV benötigt mindestens zwei Zeilen.');
    }

    $delimiter = (substr_count($rows[0], ';') > substr_count($rows[0], ',')) ? ';' : ',';
    $header = str_getcsv($rows[0], $delimiter);
    $values = str_getcsv($rows[1], $delimiter);

    if (count($header) < 2 || count($values) < 2) {
        return new WP_Error('invalid_csv', 'CSV Format ist ungültig.');
    }

    $options = get_option( 'ad_ev_options' );
    $time_raw = isset($options[AD_EV_FIELD . 'service_start']) ? $options[AD_EV_FIELD . 'service_start'] : '10:00';
    $time = explode(':', $time_raw);
    $hour = intval($time[0]);
    $minute = isset($time[1]) ? intval($time[1]) : 0;

    $dates = array_slice($header, 1);
    $preachers = array_slice($values, 1);
    $plan = [];

    foreach ($dates as $i => $date_str) {
        $date_str = trim($date_str);
        $preacher = isset($preachers[$i]) ? trim($preachers[$i]) : '';

        if ($date_str === '' || $preacher === '') {
            continue;
        }

        try {
            $date = new DateTime($date_str);
        } catch (Exception $e) {
            continue;
        }

        $date->setTime($hour, $minute);
        $plan[] = new AdventiEvent(
            date: $date,
            preacher: $preacher,
            original_input: $date->format('d-m-Y H:i')
        );
    }

    return $plan;
}

function ad_ev_event_as_str($e) {
            $message = 'Prediger: ' . $e->preacher . '
';
            $message .= 'Ort: ' . $e->location->address . '
';
			$message .= 'Zoom: ' . $e->zoom->id . '
';
            $message .= 'Datum: ' . $e->date->format('d.m.Y H:i') . '
';
            $message .= 'Special: ' . $e->special . '
';
	        $message .= 'ist Präsenz: ' . ($e->location->is_real ? 'true' : 'false') . '
';
			$message .= 'ist Zoom: ' . ($e->zoom->is_zoom ? 'true' : 'false') . '

';
	return $message;
}

function ad_ev_zoom_details_handler() {
    $result = hcaptcha_request_verify($_POST['h_response']);

    if ( null !== $result ) {
        wp_send_json(['err' => 'captcha_err', 'res' => $result, 'hres' => $_POST['h_response']]);
        return;
    }

    add_action('rest_api_init', 'ad_ev_register_api_routes');
    function ad_ev_register_api_routes() {
        register_rest_route('adventi-events/v1', '/imported-events', [
            'methods' => 'GET',
            'callback' => 'ad_ev_get_imported_events_endpoint',
            'permission_callback' => 'ad_ev_validate_api_secret'
        ]);
    }

    function ad_ev_validate_api_secret($request) {
        $options = get_option('ad_ev_options');
        $secret = isset($options[AD_EV_FIELD . 'api_secret']) ? $options[AD_EV_FIELD . 'api_secret'] : '';
        $provided_secret = $request->get_param('secret');

        if ($provided_secret === null) {
            $provided_secret = $request->get_header('X-Adventi-Secret');
        }

        if ($secret === '' || $provided_secret === null || !hash_equals($secret, $provided_secret)) {
            return new WP_Error('rest_forbidden', 'Invalid secret.', ['status' => 403]);
        }

        return true;
    }

    function ad_ev_get_imported_events_endpoint($request) {
        $query = new WP_Query([
            'post_type' => 'event',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [[
                'key' => AD_EV_META . 'original_input',
                'compare' => 'EXISTS'
            ]]
        ]);

        $entries = [];
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $original_input = get_post_meta($post_id, AD_EV_META . 'original_input', true);
            if (!$original_input) {
                continue;
            }

            $entries[] = [
                'id' => $post_id,
                'date' => get_post_meta($post_id, AD_EV_META . 'date', true),
                'preacher' => get_post_meta($post_id, AD_EV_META . 'preacher', true),
                'updated_at' => get_post_modified_time('c', true, $post_id),
            ];
        }
        wp_reset_query();

        return [
            'church' => get_option('ad_ev_options')[AD_EV_FIELD . 'church_name'],
            'updated_at' => current_time('c'),
            'entries' => $entries
        ];
    }

    $zoom_pwd = get_post_meta( $_POST['post_id'], AD_EV_META . 'zoom_pwd', true );
    $zoom_link = get_post_meta( $_POST['post_id'], AD_EV_META . 'zoom_link', true );
    $zoom_tel = get_post_meta( $_POST['post_id'], AD_EV_META . 'zoom_tel', true );

    wp_send_json(['pwd' => $zoom_pwd, 'link' => $zoom_link, 'tel' => $zoom_tel]);
    wp_die();
}