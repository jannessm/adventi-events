<?php

const AD_EV_META = '_ad_ev_meta_';
const AD_EV_FIELD = 'ad_ev_field_';

enum AdventiEventsIntervals: string {
    case ONCE = 'einmalig';
    case WEEKLY = 'jede Woche';
    case BIWEEKLY = 'alle 2 Wochen';
    case THREE_WEEKS = 'alle 3 Wochen';
    case FOUR_WEEKS = 'alle 4 Wochen';
	
	public static function tryFromName(string $name): ?static {
        $reflection = new ReflectionEnum(static::class);

        return $reflection->hasCase($name)
            ? $reflection->getCase($name)->getValue()
            : null;
    }

    public static function values(): array {
        return array_column(self::cases(), 'value');
    }
}

enum AdventiEventsSpecials: string {
    case A = 'Abendmahl';
    case E = 'Erntedank';
    case T = 'Taufgottesdienst';
    case J = 'Jugendsabbat';
    case G = 'Gottesdienst mit Gemeindestunde';
    case W = 'Waldgottesdienst';

    public static function tryFromName(string $name): ?static {
        $reflection = new ReflectionEnum(static::class);

        return $reflection->hasCase($name)
            ? $reflection->getCase($name)->getValue()
            : null;
    }

    public static function values(): array {
        return array_column(self::cases(), 'value');
    }
}


class AdventiEventPosition {
    public $is_real = TRUE;
    public $address = '';
    public $lng = 0.0;
    public $lat = 0.0;

    public function __construct($is_real, $address, $lng, $lat) {
        $this->is_real = $is_real;
        $this->address = $address;
        $this->lng = floatval($lng);
        $this->lat = floatval($lat);
    }
}

class AdventiEventZoomData {
    public $is_zoom = TRUE;
    public $id = '';
    public $pwd = '';
    public $tel = '';
    public $link = '';

    public function __construct($is_zoom=TRUE, $id='', $pwd='', $tel='', $link='') {
        $this->is_zoom = $is_zoom;
        $this->id = $id;
        $this->pwd = $pwd;
        $this->tel = $tel;
        $this->link = $link;
    }
}