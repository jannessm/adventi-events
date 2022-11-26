<?php

include_once dirname(__FILE__) . '/constants.php';

class AdventiEvent {

    public const fields = [
        'date',
        'preacher',
        'recurrence',
        'image_id',
        'location',
        'location_point',
        'special'
    ];

    public $post_id;
    public $date;
    public $preacher;
    public $recurrence;
    public $image_id;
    public $location;
    public $special;
    public $original_input;
    private $options;

    public function __construct(
        $post_id = null,
        $date = null,
        $preacher = null,
        $recurrence = null,
        $image_id = null,
        $location = null,
        $location_point = null,
        $special = null,
        $original_input = null,
    ) {
		$this->options = get_option( 'adventi_events_options' );

        $this->post_id = $post_id;
        $this->date = $this->set_value($date, self::default_date(), TRUE);
        $this->preacher = $this->set_value($preacher, '');
        $this->recurrence = $this->set_value($recurrence, AdventiEventsIntervals::ONCE->value);
        $this->image_id = $this->set_value($image_id, $this->options[AD_EV_FIELD . 'default_image']);
        $this->location = new AdventiEventPosition(
            $this->set_value($location, $this->options[AD_EV_FIELD . 'church_location']),
            $this->set_value($location_point, self::default_location_point())
        );
        $this->special = $this->set_value($special, '');
        
        $this->original_input = $original_input;

        $this->split_preacher_special();

        if ($this->is_recurrent()) {
            $this->update_date();
        }
    }

    public static function from_post($post_id) {
		$date = get_post_meta(           $post_id, AD_EV_META . 'date', true );
		$preacher = get_post_meta(       $post_id, AD_EV_META . 'preacher', true );
		$recurrence = get_post_meta(     $post_id, AD_EV_META . 'recurrence', true );
		$image_id = get_post_meta(       $post_id, AD_EV_META . 'image', true );
		$location = get_post_meta(       $post_id, AD_EV_META . 'location', true );
		$location_point = get_post_meta( $post_id, AD_EV_META . 'location_point', true ); 
		$special = get_post_meta(        $post_id, AD_EV_META . 'special', true ) === "true";
        $original_input = get_post_meta( $post_id, AD_EV_META . 'original_input', true);
        
        return new AdventiEvent($post_id, $date, $preacher, $recurrence, $image_id, $location, $location_point, $special, $original_input);
    }

    public function is_recurrent() {
        return $this->recurrence !== AdventiEventsIntervals::ONCE->value;
    }

    public function is_special() {
        return !!$this->special;
    }

    public function get_meta_array() {
        return [
            AD_EV_META . 'date' => $this->date->format('Y-m-d\\TH:i'),
            AD_EV_META . 'preacher' => $this->preacher,
            AD_EV_META . 'recurrence' => $this->recurrence,
            AD_EV_META . 'image' => $this->image_id,
            AD_EV_META . 'location' => $this->location->address,
            AD_EV_META . 'location_point' => $this->location->point_str(),
            AD_EV_META . 'special' => $this->special,
            AD_EV_META . 'original_input' => $this->original_input,
        ];
    }

    private function split_preacher_special() {
        preg_match('/(.*)\s+\((.+)\)/', $this->preacher, $matches);

        if (count($matches) > 0 && str_contains($matches[2], ':')) {
            $this->preacher = trim($matches[1]);

			$time = explode(':', $matches[2]);
            $this->date->setTime($time[0], $time[1]);
        
        } elseif (count($matches) > 0 && str_contains($matches[2], 'Uhr')) {
            $this->preacher = trim($matches[1]);

			preg_match('/\d+/', $matches[2], $time_matches);
            if (count($time_matches) > 0) {
                $this->date->setTime($time_matches[0], 0);
            }
        
        } elseif (count($matches) > 0 && !$this->special) {
            $this->preacher = trim($matches[1]);
            $this->special = AdventiEventsSpecials::tryFromName($matches[2]);
        }
    }

    public function update_date() {
        $weeks = 0;
        switch ($this->recurrence) {
            case AdventiEventsIntervals::WEEKLY->value:
                $weeks = 1;
                break;

            case AdventiEventsIntervals::BIWEEKLY->value:
                $weeks = 2;
                break;

            case AdventiEventsIntervals::THREE_WEEKS->value:
                $weeks = 3;
                break;

            case AdventiEventsIntervals::FOUR_WEEKS->value:
                $weeks = 4;
                break;
        }
        
        if ($weeks > 0) {
            $now = new DateTime();
            while ($now > $this->date) {
                $this->date->add(new DateInterval('P'.$weeks.'W'));
            }
        }
    }

    private function default_date() {
        if (isset($this->options[AD_EV_FIELD . 'service_start'])) {
			$default_date = new DateTime();
			$time = explode(':', $this->options[AD_EV_FIELD . 'service_start']);
			$default_date = $default_date->setTimestamp(strtotime('next saturday'))->setTime($time[0], $time[1]);
            return $default_date;
		}
        return null;
    }

    private function default_location_point() {
        $default_point = [0,0];
		if (isset($this->options[AD_EV_FIELD . 'church_long']) &&
            isset($this->options[AD_EV_FIELD . 'church_lat'])
        ) {
			$default_point = [$this->options[AD_EV_FIELD . 'church_long'],
                              $this->options[AD_EV_FIELD . 'church_lat']];
		}
        return $default_point;
    }

    private function set_value($value, $default, $is_date=FALSE) {
        if (!!!$value) {
            return $default;
        } elseif ($is_date && is_string($value)) {
            return new DateTime($value);
        } else {
            return $value;
        }
    }
}