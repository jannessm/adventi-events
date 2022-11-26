<?php

enum AdventiEventsDataKeys: string {
    case DATES = 'dates';
    case PREACHERS = 'preachers';
}

class AdventiEventsDataExtractor {
    protected $url = null;
    protected $church = null;
    protected $data = [];
    protected $cookies = [];

    public function __construct($url, $church) {
        if (!str_ends_with($url, '/')) {
            $url = $url . '/';
        }
        $this->url = $url;
        $this->church = $church;
    }

    public function get_data() {
        $resp = wp_remote_get($this->url, ['timeout' => 10]);

        if (is_wp_error($resp)) {
            wp_send_json($resp->get_error_message());
            return;
        }   
        
        $this->cookies = $resp['cookies'];
        
        $csv_data = $this->get_csv();
        $data = $this->parse_csv($csv_data);
        
        $this->set_cookie_for_next_quater();
        
        $csv_data = $this->get_csv();
        $data = array_merge($data, $this->parse_csv($csv_data));

        return $data;
    }

    private function get_csv() {
        $body = wp_remote_get($this->url . 'plan/data.csv', [
            "cookies" => $this->cookies,
            'timeout' => 10
        ])['body'];
        
        $lines = explode("\n", $body);
        $lines = array_map(function($line) {
            return array_map(function($entry) {
                return trim($entry, '"');
            }, explode(";", $line));
        }, $lines);

        return $lines;
    }

    private function parse_csv($lines) {
        $dates = array_slice($lines[2], 3);
        $preachers = [];
    
        $lines = array_slice($lines, 4);
        foreach ($lines as $line) {
            if (!is_array($line)) {
                continue;
            }
    
            if (count($line) > 3 && $line[2] === $this->church) {
                $preachers = array_slice($line, 3);
                break;
            }
        }
    
        $plan = [];
        foreach ($dates as $i => $date) {
            $formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, 
                                               IntlDateFormatter::NONE, NULL, NULL, "dd. MMM yy");
            $date = (new DateTime())->setTimestamp($formatter->parse($date));

            $options = get_option( 'adventi_events_options' );
			$time = explode(':', $options[AD_EV_FIELD . 'service_start']);
            $date->setTime($time[0], $time[1]);

            array_push($plan, new AdventiEvent(null, $date, $preachers[$i], null, null, null, null, null,$date->format('d-m-Y H:i') . ',' . $preachers[$i]));
        }

        return $plan;
    }

    private function set_cookie_for_next_quater() {
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

        $resp = wp_remote_post($this->url . 'nc/plan/ln/-/-/-/-/2857/', [
            'cookies' => $this->cookies,
            'headers' => array('content-type' => 'application/x-www-form-urlencoded'),
            'body' => $filter
        ]);

        return $next_epoch;
    }
}