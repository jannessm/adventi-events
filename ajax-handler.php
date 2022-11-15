<?php

/**
 * Handles my AJAX request.
 */
function update_events_handler() {
    check_ajax_referer( 'update_events' );
	
    $plan_url = get_option( 'adventi_events_options' )['adventi_events_field_preacher_plan'];
    $church = get_option( 'adventi_events_options' )['adventi_events_field_church_name'];

    if (!str_ends_with($plan_url, '/')) {
        $plan_url = $plan_url . '/';
    }

    $resp = wp_remote_get($plan_url);

    if (is_wp_error($resp)) {
        wp_send_json($resp->get_error_message());
        return;
    }   

    $cookies = $resp['cookies'];

    $data = get_and_parse_csv($plan_url, $cookies, $church);

    $next_epoch = set_cookie($plan_url, $cookies);

    $data2 = get_and_parse_csv($plan_url, $cookies, $church);

    wp_send_json($data);
}


function set_cookie($base_url, $cookies) {
    
    $next_epoch = new DateTime();
    $month = date('m');
    $next_epoch = $next_epoch->setDate(
        $next_epoch->format('Y'),
        $next_epoch->format('m'),
        1
    )->setTime(0,0)->modify('+ ' . (3 - ($month-1) % 3) . ' months')->getTimestamp();

    $filter = array(
        "xjxfun" => "axFilter",
        "xjxr" => (new DateTime())->getTimestamp(),
        "xjxargs[]" => "<xjxobj><e><k>tx_amslocations_pi1</k><v><xjxobj><e><k>filter-1</k><v><xjxobj><e><k>0</k><v>S1</v></e></xjxobj></v></e><e><k>filter-2</k><v><xjxobj><e><k>0</k><v>S5</v></e></xjxobj></v></e><e><k>empty</k><v><xjxobj><e><k>0</k><v>S4</v></e></xjxobj></v></e><e><k>quarter</k><v>S".$next_epoch."</v></e><e><k>send</k><v>Sanzeigen</v></e><e><k>administrationLevels</k><v>S4</v></e></xjxobj></v></e></xjxobj>"
    );

    $resp = wp_remote_post($base_url . 'nc/plan/ln/-/-/-/-/2857/', [
        'cookies' => $cookies,
        'headers' => array('content-type' => 'application/x-www-form-urlencoded'),
        'body' => $filter
    ]);

    return $next_epoch;
}

function get_and_parse_csv($base_url, $cookies, $church) {
    $body = wp_remote_get($base_url . 'plan/data.csv', [
        "cookies" => $cookies
    ])['body'];

    $lines = explode("\n", $body);
    $lines = array_map(function($line) {
        return array_map(function($entry) {
            return trim($entry, '"');
        }, explode(";", $line));
    }, $lines);

    $dates = array_slice($lines[2], 3);
    $preacher = [];

    $lines = array_slice($lines, 4);
    foreach ($lines as $line) {
        if (!is_array($line)) {
            continue;
        }

        if (count($line) > 3 && $line[2] === $church) {
            $preacher = array_slice($line, 3);
            break;
        }
    }

    $plan = [];
    foreach ($dates as $i => $date) {
        $plan[$date] = $preacher[$i];
    }

    return $plan;
}