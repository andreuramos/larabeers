<?php

namespace Larabeers\External;

class FlagRepository
{
    const BASE_URL = "https://www.countryflags.io/";
    const URL_TAIL = "/flat/24.png";

    const COUNTRY_CODES = [
        'argentina' => 'ar',
        'australia' => 'au',
        'austria' => 'at',
        'barbados' => 'bb',
        'belgium' => 'be',
        'bulgary' => 'bg',
        'canada' => 'ca',
        'chile' => 'cl',
        'china' => 'cn',
        'colombia' => 'co',
        'croatia' => 'hr',
        'cuba' => 'cu',
        'czech republic' => 'cz',
        'denmark' => 'de',
        'dominica' => 'dm',
        'dominican republic' => 'do',
        'ecuador' => 'ec',
        'england' => 'gb',
        'estonia' => 'ee',
        'finland' => 'fi',
        'france' => 'fr',
        'germany' => 'de',
        'greece' => 'gr',
        'guadaloupe' => 'fr',
        'guatemala' => 'gt',
        'hungary' => 'hu',
        'iceland' => 'is',
        'india' => 'in',
        'ireland' => 'ie',
        'israel' => 'il',
        'italy' => 'it',
        'japan' => 'jp',
        'latvia' => 'lv',
        'lithuania' => 'lt',
        'malta' => 'mt',
        'mexico' => 'mx',
        'morocco' => 'ma',
        'netherlands' => 'ne',
        'norway' => 'no',
        'panama' => 'pa',
        'perú' => 'pe',
        'poland' => 'pl',
        'portugal' => 'pt',
        'puerto rico' => 'pr',
        'romania' => 'ro',
        'russia' => 'ru',
        'scotland' => 'gb',
        'serbia' => 'rs',
        'singapore' => 'sg',
        'slovak republic' => 'sk',
        'slovenia' => 'sl',
        'spain' => 'es',
        'sri lanka' => 'lk',
        'st vincent & the granadines' => 'vc',
        'sweden' => 'se',
        'thailand' => 'th',
        'trinidad & tobago' => 'tt',
        'turkey' => 'tr',
        'ukraine' => 'ua',
        'usa' => 'us',
        'vietnam' => 'vn',
        'venezuela' => 've'
    ];

    public static function get(string $country_name): ?string
    {
        $country = strtolower($country_name);
        if (!array_key_exists($country, self::COUNTRY_CODES)) {
            return null;
        }
        $code = self::COUNTRY_CODES[$country];

        return self::BASE_URL . $code . self::URL_TAIL;
    }
}