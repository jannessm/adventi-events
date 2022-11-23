<?php

const AD_EV_META = '_adventi_events_meta_';
const AD_EV_FIELD = 'adventi_events_field_';

enum AdventiEventsIntervals: string {
    case ONCE = 'einmalig';
    case WEEKLY = 'jede Woche';
    case BIWEEKLY = 'alle 2 Wochen';
    case THREE_WEEKS = 'alle 3 Wochen';
    case FOUR_WEEKS = 'alle 4 Wochen';

    public static function values(): array {
        return array_column(self::cases(), 'value');
    }
}

enum AdventiEventsSpecials: string {
    case A = 'Abendmahl';
    case E = 'Erntedank';
    case T = 'Taufgottesdienst';
    case J = 'Jugendsabbat';
    case G = 'Gemeindestunde';
    case W = 'Waldgottesdienst';

    public static function tryFromName(string $name): ?static {
        $reflection = new ReflectionEnum(static::class);

        return $reflection->hasCase($name)
            ? $reflection->getCase($name)->getValue()
            : null;
    }
}


class AdventiEventPosition {
    public $address = '';
    public $lng = 0.0;
    public $lat = 0.0;

    public function __construct($address, $lng_lat) {
        $this->address = $address;

        if (is_array($lng_lat)) {
            $this->lng = $lng_lat[0];
            $this->lat = $lng_lat[1];
        } else {
            $lng_lat = explode(',', trim($lng_lat, "[]"));
            $this->lng = floatval($lng_lat[0]);
            $this->lat = floatval($lng_lat[1]);
        }
    }

    public function point_str() {
        return '['.$this->lng.','.$this->lat.']';
    }
}

