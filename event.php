<?php

include_once dirname(__FILE__) . '/constants.php';

class AdventiEvent {

    public static $fields = [
        'date',
        'preacher',
        'recurrence',
        'image_id',
        'location',
        'location_point',
        'special'
    ];

    public $date;
    public $preacher;
    public $recurrence;
    public $image_id;
    public $location;
    public $special;
    private $options;

    public function __construct(
        $date = null,
        $preacher = null,
        $recurrence = null,
        $image_id = null,
        $location = null,
        $location_point = null,
        $special = null
    ) {
		$this->options = get_option( 'adventi_events_options' );

        $this->date = $this->set_value($date, self::default_date(), TRUE);
        $this->preacher = $this->set_value($preacher, '');
        $this->recurrence = $this->set_value($recurrence, AdventiEventsIntervals::ONCE->value);
        $this->image_id = $this->set_value($image_id, $this->options[AD_EV_FIELD . 'default_image']);
        $this->location = new AdventiEventPosition(
            $this->set_value($location, $this->options[AD_EV_FIELD . 'church_location']),
            $this->set_value($location_point, self::default_location_point())
        );
        $this->special = $this->set_value($special, '');

        $this->split_preacher_special();
    }

    public static function from_post($post_id) {
		$date = get_post_meta(           $post_id, AD_EV_META . 'date', true );
		$preacher = get_post_meta(       $post_id, AD_EV_META . 'preacher', true );
		$recurrence = get_post_meta(     $post_id, AD_EV_META . 'recurrence', true );
		$image_id = get_post_meta(       $post_id, AD_EV_META . 'image', true );
		$location = get_post_meta(       $post_id, AD_EV_META . 'location', true );
		$location_point = get_post_meta( $post_id, AD_EV_META . 'location_point', true ); 
		$special = get_post_meta(        $post_id, AD_EV_META . 'special', true ) === "true";
        
        return new AdventiEvent($date, $preacher, $recurrence, $image_id, $location, $location_point, $special);
    }

    public function is_recurrent() {
        return $this->recurrence === AdventiEventsIntervals::ONCE->value;
    }

    public function is_special() {
        return !!$this->special;
    }

    private function split_preacher_special() {
        preg_match('/(.*)\s+\((.+)\)/', $this->preacher, $matches);

        if (count($matches) > 0 && !$this->special) {
            $this->preacher = trim($matches[1]);
            $this->special = AdventiEventsSpecials::tryFromName($matches[2]);
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
        } elseif ($is_date) {
            return new DateTime($value);
        } else {
            return $value;
        }
    }
}