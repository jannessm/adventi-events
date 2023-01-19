<?php

include_once dirname(__FILE__) . '/constants.php';

class AdventiEvent {

    public const fields = [
        'date',
        'preacher',
        'recurrence',
        'image_id',
        'is_real',
        'location',
        'location_lng',
        'location_lat',
        'is_zoom',
        'zoom_id',
        'zoom_pwd',
        'zoom_tel',
        'zoom_link',
        'special',
        'exclude_dates'
    ];

    public $post_id;
    public $date;
    public $preacher;
    public $recurrence;
    public $image_id;
    public $location;
    public $zoom;
    public $special;
    public $original_input;
    private $options;

    public function __construct(
        $post_id = null,
        $date = null,
        $preacher = null,
        $recurrence = null,
        $image_id = null,
        $is_real = true,
        $location = null,
        $location_lng = null,
        $location_lat = null,
        $special = null,
        $original_input = null,
        $exclude_dates = null,
        $is_zoom = true,
        $zoom_id = null,
        $zoom_pwd = null,
        $zoom_tel = null,
        $zoom_link = null,
    ) {
		$this->options = get_option( 'ad_ev_options' );
		
		if (!$this->options) {
			return;
		}

        $this->post_id = $post_id;
        $this->date = $this->set_value($date, self::default_date(), TRUE);
        $this->preacher = $this->set_value($preacher, '');
        $this->recurrence = $this->set_value($recurrence, AdventiEventsIntervals::ONCE->value);
        $this->image_id = $this->set_value($image_id, $this->options[AD_EV_FIELD . 'default_image']);
        $this->location = new AdventiEventPosition(
            $is_real,
            $this->set_value($location, $this->options[AD_EV_FIELD . 'church_location']),
            $this->set_value($location_lng, $this->options[AD_EV_FIELD . 'church_lng']),
            $this->set_value($location_lat, $this->options[AD_EV_FIELD . 'church_lat']),
        );
        if (!!$is_zoom || !!$zoom_id || !!$zoom_pwd || !!$zoom_tel || !!$zoom_link) {
            $this->zoom = new AdventiEventZoomData(
                $this->set_value($is_zoom, true),
                $this->set_value($zoom_id, ''),
                $this->set_value($zoom_pwd, ''),
                $this->set_value($zoom_tel, ''),
                $this->set_value($zoom_link, '')
            );
        } else {
            $this->zoom = new AdventiEventZoomData(
                $is_zoom,
                $this->set_value($zoom_id, $this->options[AD_EV_FIELD . 'zoom_id']),
                $this->set_value($zoom_pwd, $this->options[AD_EV_FIELD . 'zoom_pwd']),
                $this->set_value($zoom_tel, $this->options[AD_EV_FIELD . 'zoom_tel']),
                $this->set_value($zoom_link, $this->options[AD_EV_FIELD . 'zoom_link'])
            );   
        }
        $this->special = $special;
        
        $this->original_input = $original_input;
        $this->exclude_dates = array_map(fn($i) => trim($i), explode(',', $exclude_dates));

        $this->split_preacher_special();

        if ($this->is_recurrent()) {
            $this->update_date();
        }
    }

    public static function from_post($post_id) {
		$date = get_post_meta(           $post_id, AD_EV_META . 'date', true );
		$preacher = get_post_meta(       $post_id, AD_EV_META . 'preacher', true );
		$recurrence = get_post_meta(     $post_id, AD_EV_META . 'recurrence', true );
		$image_id = get_post_meta(       $post_id, AD_EV_META . 'image_id', true );
		
        $is_real = get_post_meta(        $post_id, AD_EV_META . 'is_real', true ) == "true";
        $location = get_post_meta(       $post_id, AD_EV_META . 'location', true );
		$location_lng = get_post_meta(   $post_id, AD_EV_META . 'location_lng', true ); 
		$location_lat = get_post_meta(   $post_id, AD_EV_META . 'location_lat', true );

		$special = get_post_meta(        $post_id, AD_EV_META . 'special', true );
        $original_input = get_post_meta( $post_id, AD_EV_META . 'original_input', true);
        $exclude_dates = get_post_meta(  $post_id, AD_EV_META . 'exclude_dates', true);
        
        $is_zoom = get_post_meta(        $post_id, AD_EV_META . 'is_zoom', true) == "true";
        $zoom_id = get_post_meta(        $post_id, AD_EV_META . 'zoom_id', true);
        $zoom_pwd = get_post_meta(       $post_id, AD_EV_META . 'zoom_pwd', true);
        $zoom_tel = get_post_meta(       $post_id, AD_EV_META . 'zoom_tel', true);
        $zoom_link = get_post_meta(      $post_id, AD_EV_META . 'zoom_link', true);

        return new AdventiEvent($post_id, $date, $preacher, $recurrence, $image_id, $is_real, $location, $location_lng, $location_lat, $special, $original_input, $exclude_dates, $is_zoom, $zoom_id, $zoom_pwd, $zoom_tel, $zoom_link);
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
            AD_EV_META . 'image_id' => $this->image_id,
            AD_EV_META . 'is_real' => ($this->location->is_real ? "true" : "false"),
            AD_EV_META . 'location' => $this->location->address,
            AD_EV_META . 'location_lng' => $this->location->lng,
            AD_EV_META . 'location_lat' => $this->location->lat,
            AD_EV_META . 'special' => (!!$this->special ? $this->special->value : null),
            AD_EV_META . 'original_input' => $this->original_input,
            AD_EV_META . 'exclude_dates' => join(',', $this->exclude_dates),
            AD_EV_META . 'is_zoom' => ($this->zoom->is_zoom ? "true" : "false"),
            AD_EV_META . 'zoom_id' => $this->zoom->id,
            AD_EV_META . 'zoom_pwd' => $this->zoom->pwd,
            AD_EV_META . 'zoom_tel' => $this->zoom->tel,
            AD_EV_META . 'zoom_link' => $this->zoom->link,
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

    public function update_date($now=null) {
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
            if (!$now) {
                $now = new DateTime();
            }
            while ($now > $this->date) {
                $this->date->add(new DateInterval('P'.$weeks.'W'));
                $date = $this->date->format('d.m.Y');
                while (in_array($date, $this->exclude_dates)) {
                    $this->date->add(new DateInterval('P'.$weeks.'W'));
                    $date = $this->date->format('d.m.Y');
                }
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