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
    $cookies = $resp['cookies'];

    $data = get_and_parse_csv($plan_url, $cookies, $church);

    $r = set_cookie($plan_url, $cookies);
    $cookies = $r[0];
    $next_epoch = $r[1];

    $data2 = get_and_parse_csv($plan_url, $cookies, $church);
    // date_default_timezone_set('UTC');
    wp_send_json([
        0 => $data,
        $next_epoch => $data2,
        1 => date('Y-m-d h:i:s', $next_epoch)
    ]);
    // wp_send_json($cookies);
}


function set_cookie($base_url, $cookies) {
    
    $next_epoch = new DateTime("now", new DateTimeZone('UTC'));
    $month = date('m');
    $next_epoch = $next_epoch->setDate(
        $next_epoch->format('Y'),
        $next_epoch->format('m'),
        1
    )->setTime(0,0)->modify('+ ' . (6 - ($month-1) % 3) . ' months')->getTimestamp();
    $next_epoch = 1672527600;

    $filter = [
        "xjxfun" => "axFilter",
        "xjxr" => (new DateTime())->getTimestamp(),
        // "xjxargs[]" => urlencode("<xjxobj><e><k>tx_amslocations_pi1</k><v><xjxobj><e><k>filter-1</k><v><xjxobj><e><k>0</k><v>S1</v></e></xjxobj></v></e><e><k>filter-2</k><v><xjxobj><e><k>0</k><v>S5</v></e></xjxobj></v></e><e><k>empty</k><v><xjxobj><e><k>0</k><v>S4</v></e></xjxobj></v></e><e><k>quarter</k><v>S".$next_epoch."</v></e><e><k>send</k><v>Sanzeigen</v></e><e><k>administrationLevels</k><v>S4</v></e></xjxobj></v></e></xjxobj>")
        "xjxargs[]" => urlencode("<xjxobj><e><k>tx_amslocations_pi1</k><v><xjxobj><e><k>filter-1</k><v><xjxobj><e><k>0</k><v>S1</v></e></xjxobj></v></e><e><k>filter-2</k><v><xjxobj><e><k>0</k><v>S5</v></e></xjxobj></v></e><e><k>empty</k><v><xjxobj><e><k>0</k><v>S4</v></e></xjxobj></v></e><e><k>quarter</k><v>S1672527600</v></e><e><k>send</k><v>Sanzeigen</v></e><e><k>administrationLevels</k><v>S4</v></e></xjxobj></v></e></xjxobj>")
    ];

    $resp = wp_remote_post('http://bmv-predigtplan.adventisten.de/nc/plan/ln/-/-/-/-/2861/', [
        'method' => 'POST',
        'cookies' => $cookies,
        'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
        'body' => $filter
    ]);

    $cookies = $resp['cookies'];

    return [$cookies, $next_epoch];
		
}

function get_and_parse_csv($base_url, $cookies, $church) {
    $body = wp_remote_get($base_url . 'plan/data.csv', [
        "cookies" => $cookies
    ])['body'];
    return $body;

    $body = mb_convert_encoding($body, 'utf-8', 'Windows-1252');

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

        if ($line[2] === $church) {
            $preacher = array_slice($line, 3);
            break;
        }
    }

    return ["dates" => $dates, "preacher" => $preacher];
}