<?php

namespace LePlugin\Core;

use DateTime;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 * Utility class that contains common methods that is unclassified as of the moment.
 */
class Utils
{

    private static $encryption_key = 'kQ@DotLg!isQp#9&UHX7*&%AvHvG6&';

    public static function encrypt($value, $key = null)
    {
        if ($key === null) {
            $key = self::$encryption_key;
        }
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $value, MCRYPT_MODE_CBC,
            md5(md5($key))));
    }

    public static function decrypt($enc_value, $key = null)
    {
        if ($key === null) {
            $key = self::$encryption_key;
        }
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($enc_value),
            MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    }

    public static function getMainDomain($host_only = false)
    {
        $url = parse_url(site_url());
        $domain = explode('.', $url['host']);
        if (count($domain) < 2) {
            return false;
        }
        if ($host_only) {
            return $domain[count($domain) - 2] . '.' . $domain[count($domain) - 1];
        }
        return $url['scheme'] . '://' . $domain[count($domain) - 2] . '.' . $domain[count($domain) - 1];
    }

    public static function get_domain($domain, $debug = false)
    {
        $original = $domain = strtolower($domain);
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            return $domain;
        }
        $arr = array_slice(array_filter(explode('.', $domain, 4), function ($value) {
            return $value !== 'www';
        }), 0); //rebuild array indexes
        if (count($arr) > 2) {
            $count = count($arr);
            $_sub = explode('.', $count === 4 ? $arr[3] : $arr[2]);
            if (count($_sub) === 2) // two level TLD
            {
                $removed = array_shift($arr);
                if ($count === 4) // got a subdomain acting as a domain
                {
                    $removed = array_shift($arr);
                }
            } elseif (count($_sub) === 1) // one level TLD
            {
                $removed = array_shift($arr); //remove the subdomain
                if (strlen($_sub[0]) === 2 && $count === 3) // TLD domain must be 2 letters
                {
                    array_unshift($arr, $removed);
                } else {
                    // non country TLD according to IANA
                    $tlds = array(
                        'aero',
                        'arpa',
                        'asia',
                        'biz',
                        'cat',
                        'com',
                        'coop',
                        'edu',
                        'gov',
                        'info',
                        'jobs',
                        'mil',
                        'mobi',
                        'museum',
                        'name',
                        'net',
                        'org',
                        'post',
                        'pro',
                        'tel',
                        'travel',
                        'xxx',
                        'local'
                    );
                    if (count($arr) > 2 && in_array($_sub[0], $tlds) !== false) //special TLD don't have a country
                    {
                        array_shift($arr);
                    }
                }
            } else // more than 3 levels, something is wrong
            {
                for ($i = count($_sub); $i > 1; $i--) {
                    $removed = array_shift($arr);
                }
            }
        } elseif (count($arr) === 2) {
            $arr0 = array_shift($arr);
            if (strpos(join('.', $arr), '.') === false
                && in_array($arr[0], array('localhost', 'test', 'invalid')) === false
            ) // not a reserved domain
            {
                // seems invalid domain, restore it
                array_unshift($arr, $arr0);
            }
        }
        return join('.', $arr);
    }

    public static function str_replace_first($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

    public static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function utc_date($format, $time = null)
    {
        return self::tz_date('UTC', $format, $time);
    }

    public static function tz_date($tz, $format, $time = null)
    {
        $old_tz = date_default_timezone_get();
        date_default_timezone_set($tz);
        $tz_time = null;
        if ($time === null) {
            $tz_time = date($format);
        } else {
            $tz_time = date($format, $time);
        }
        date_default_timezone_set($old_tz);
        return $tz_time;
    }

    public static function utc_strtotime($str, $time = null)
    {
        return self::tz_strtotime('UTC', $str, $time);
    }

    public static function tz_strtotime($tz, $str, $time = null)
    {
        $old_tz = date_default_timezone_get();
        date_default_timezone_set($tz);
        $tz_time = null;
        if ($time === null) {
            $tz_time = strtotime($str);
        } else {
            $tz_time = strtotime($str, $time);
        }
        date_default_timezone_set($old_tz);
        return $tz_time;
    }

    public static function parseCustomFields($custom)
    {
        $returnUrlParts = parse_url("?" . $custom);
        $params = [];
        parse_str($returnUrlParts['query'], $params);
        return $params;
    }

    public static function buildNotice($message, $type = 'updated', $inline = true)
    {

        $notice = '<div class="' . $type . ' notice is-dismissible ' . ($inline ? 'inline' : '') . '">'
            . '<p><strong>' . $message . '</strong></p>'
            . '</div>';
        return $notice;
    }

    public static function buildTimezoneDropdown($name, $selected = '', array $otherAttrs = [])
    {
        $otherAttrsFlat = '';
        if (count($otherAttrs) > 0) {
            foreach ($otherAttrs as $key => $value) {
                $otherAttrsFlat .= ' ' . $key . '="' . $value . '" ';
            }
        }
        $select = '<select name="' . $name . '" ' . $otherAttrsFlat . '>';
        $select .= '<option value="">-- Select Timezone --</option>';
        foreach (timezone_identifiers_list() as $zone) {

            if ($selected !== '' && $selected !== null && $selected == $zone) {
                $selectedAttr = ' selected="selected" ';
            } else {
                $selectedAttr = '';
            }
            $select .= '<option ' . $selectedAttr . ' value="' . $zone . '">' . $zone . '</option>';
        }
        $select .= '</select>';
        return $select;
    }

    public static function buildUtcTimezoneDropdown($name, $selected = '', array $otherAttrs = [])
    {
        $otherAttrsFlat = '';
        if (count($otherAttrs) > 0) {
            foreach ($otherAttrs as $key => $value) {
                $otherAttrsFlat .= ' ' . $key . '="' . $value . '" ';
            }
        }
        $offsets = [-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5,
            -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5, 0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5,
            5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75,
            14];
        $select = '<select name="' . $name . '" ' . $otherAttrsFlat . '>';
        $select .= '<option value="">-- Select Timezone --</option>';
        foreach ($offsets as $offset) {
            if (0 <= $offset) {
                $offset_name = '+' . $offset;
            } else {
                $offset_name = '' . $offset;
            }
            $offset_name = 'UTC' . str_replace(['.25', '.5', '.75'], [':25', ':30', ':45'],
                    $offset_name);

            if ($selected !== '' && $selected !== null && $selected == $offset) {
                $selectedAttr = ' selected="selected" ';
            } else {
                $selectedAttr = '';
            }

            $select .= '<option ' . $selectedAttr . ' value="' . $offset . '">' . $offset_name . '</option>';
        }

        $select .= '</select>';
        return $select;
    }

    public static function buildCountryDropDown($name, $selected = '', array $otherAttrs = [])
    {
        $otherAttrsFlat = '';
        if (count($otherAttrs) > 0) {
            foreach ($otherAttrs as $key => $value) {
                $otherAttrsFlat .= ' ' . $key . '="' . $value . '" ';
            }
        }
        $select = '<select name="' . $name . '" ' . $otherAttrsFlat . '>';
        $select .= '<option value="">-- Select Country --</option>';
        foreach (self::$COUNTRY_ARRAY as $code => $country) {
            if ($selected == $code) {
                $selectedAttr = ' selected="selected" ';
            } else {
                $selectedAttr = '';
            }
            $select .= '<option value="' . $code . '" ' . $selectedAttr . '>' . $country . '</option>';
        }
        $select .= '</select>';
        return $select;
    }

    static $COUNTRY_ARRAY = array("AF" => "Afghanistan",
        "AX" => "Åland Islands",
        "AL" => "Albania",
        "DZ" => "Algeria",
        "AS" => "American Samoa",
        "AD" => "Andorra",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AQ" => "Antarctica",
        "AG" => "Antigua and Barbuda",
        "AR" => "Argentina",
        "AM" => "Armenia",
        "AW" => "Aruba",
        "AU" => "Australia",
        "AT" => "Austria",
        "AZ" => "Azerbaijan",
        "BS" => "Bahamas",
        "BH" => "Bahrain",
        "BD" => "Bangladesh",
        "BB" => "Barbados",
        "BY" => "Belarus",
        "BE" => "Belgium",
        "BZ" => "Belize",
        "BJ" => "Benin",
        "BM" => "Bermuda",
        "BT" => "Bhutan",
        "BO" => "Bolivia",
        "BA" => "Bosnia and Herzegovina",
        "BW" => "Botswana",
        "BV" => "Bouvet Island",
        "BR" => "Brazil",
        "IO" => "British Indian Ocean Territory",
        "BN" => "Brunei Darussalam",
        "BG" => "Bulgaria",
        "BF" => "Burkina Faso",
        "BI" => "Burundi",
        "KH" => "Cambodia",
        "CM" => "Cameroon",
        "CA" => "Canada",
        "CV" => "Cape Verde",
        "KY" => "Cayman Islands",
        "CF" => "Central African Republic",
        "TD" => "Chad",
        "CL" => "Chile",
        "CN" => "China",
        "CX" => "Christmas Island",
        "CC" => "Cocos (Keeling) Islands",
        "CO" => "Colombia",
        "KM" => "Comoros",
        "CG" => "Congo",
        "CD" => "Congo, The Democratic Republic of The",
        "CK" => "Cook Islands",
        "CR" => "Costa Rica",
        "CI" => "Cote D'ivoire",
        "HR" => "Croatia",
        "CU" => "Cuba",
        "CY" => "Cyprus",
        "CZ" => "Czech Republic",
        "DK" => "Denmark",
        "DJ" => "Djibouti",
        "DM" => "Dominica",
        "DO" => "Dominican Republic",
        "EC" => "Ecuador",
        "EG" => "Egypt",
        "SV" => "El Salvador",
        "GQ" => "Equatorial Guinea",
        "ER" => "Eritrea",
        "EE" => "Estonia",
        "ET" => "Ethiopia",
        "FK" => "Falkland Islands (Malvinas)",
        "FO" => "Faroe Islands",
        "FJ" => "Fiji",
        "FI" => "Finland",
        "FR" => "France",
        "GF" => "French Guiana",
        "PF" => "French Polynesia",
        "TF" => "French Southern Territories",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GE" => "Georgia",
        "DE" => "Germany",
        "GH" => "Ghana",
        "GI" => "Gibraltar",
        "GR" => "Greece",
        "GL" => "Greenland",
        "GD" => "Grenada",
        "GP" => "Guadeloupe",
        "GU" => "Guam",
        "GT" => "Guatemala",
        "GG" => "Guernsey",
        "GN" => "Guinea",
        "GW" => "Guinea-bissau",
        "GY" => "Guyana",
        "HT" => "Haiti",
        "HM" => "Heard Island and Mcdonald Islands",
        "VA" => "Holy See (Vatican City State)",
        "HN" => "Honduras",
        "HK" => "Hong Kong",
        "HU" => "Hungary",
        "IS" => "Iceland",
        "IN" => "India",
        "ID" => "Indonesia",
        "IR" => "Iran, Islamic Republic of",
        "IQ" => "Iraq",
        "IE" => "Ireland",
        "IM" => "Isle of Man",
        "IL" => "Israel",
        "IT" => "Italy",
        "JM" => "Jamaica",
        "JP" => "Japan",
        "JE" => "Jersey",
        "JO" => "Jordan",
        "KZ" => "Kazakhstan",
        "KE" => "Kenya",
        "KI" => "Kiribati",
        "KP" => "Korea, Democratic People's Republic of",
        "KR" => "Korea, Republic of",
        "KW" => "Kuwait",
        "KG" => "Kyrgyzstan",
        "LA" => "Lao People's Democratic Republic",
        "LV" => "Latvia",
        "LB" => "Lebanon",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "Libyan Arab Jamahiriya",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "MO" => "Macao",
        "MK" => "Macedonia, The Former Yugoslav Republic of",
        "MG" => "Madagascar",
        "MW" => "Malawi",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malta",
        "MH" => "Marshall Islands",
        "MQ" => "Martinique",
        "MR" => "Mauritania",
        "MU" => "Mauritius",
        "YT" => "Mayotte",
        "MX" => "Mexico",
        "FM" => "Micronesia, Federated States of",
        "MD" => "Moldova, Republic of",
        "MC" => "Monaco",
        "MN" => "Mongolia",
        "ME" => "Montenegro",
        "MS" => "Montserrat",
        "MA" => "Morocco",
        "MZ" => "Mozambique",
        "MM" => "Myanmar",
        "NA" => "Namibia",
        "NR" => "Nauru",
        "NP" => "Nepal",
        "NL" => "Netherlands",
        "AN" => "Netherlands Antilles",
        "NC" => "New Caledonia",
        "NZ" => "New Zealand",
        "NI" => "Nicaragua",
        "NE" => "Niger",
        "NG" => "Nigeria",
        "NU" => "Niue",
        "NF" => "Norfolk Island",
        "MP" => "Northern Mariana Islands",
        "NO" => "Norway",
        "OM" => "Oman",
        "PK" => "Pakistan",
        "PW" => "Palau",
        "PS" => "Palestinian Territory, Occupied",
        "PA" => "Panama",
        "PG" => "Papua New Guinea",
        "PY" => "Paraguay",
        "PE" => "Peru",
        "PH" => "Philippines",
        "PN" => "Pitcairn",
        "PL" => "Poland",
        "PT" => "Portugal",
        "PR" => "Puerto Rico",
        "QA" => "Qatar",
        "RE" => "Reunion",
        "RO" => "Romania",
        "RU" => "Russian Federation",
        "RW" => "Rwanda",
        "SH" => "Saint Helena",
        "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia",
        "PM" => "Saint Pierre and Miquelon",
        "VC" => "Saint Vincent and The Grenadines",
        "WS" => "Samoa",
        "SM" => "San Marino",
        "ST" => "Sao Tome and Principe",
        "SA" => "Saudi Arabia",
        "SN" => "Senegal",
        "RS" => "Serbia",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SG" => "Singapore",
        "SK" => "Slovakia",
        "SI" => "Slovenia",
        "SB" => "Solomon Islands",
        "SO" => "Somalia",
        "ZA" => "South Africa",
        "GS" => "South Georgia and The South Sandwich Islands",
        "ES" => "Spain",
        "LK" => "Sri Lanka",
        "SD" => "Sudan",
        "SR" => "Suriname",
        "SJ" => "Svalbard and Jan Mayen",
        "SZ" => "Swaziland",
        "SE" => "Sweden",
        "CH" => "Switzerland",
        "SY" => "Syrian Arab Republic",
        "TW" => "Taiwan, Province of China",
        "TJ" => "Tajikistan",
        "TZ" => "Tanzania, United Republic of",
        "TH" => "Thailand",
        "TL" => "Timor-leste",
        "TG" => "Togo",
        "TK" => "Tokelau",
        "TO" => "Tonga",
        "TT" => "Trinidad and Tobago",
        "TN" => "Tunisia",
        "TR" => "Turkey",
        "TM" => "Turkmenistan",
        "TC" => "Turks and Caicos Islands",
        "TV" => "Tuvalu",
        "UG" => "Uganda",
        "UA" => "Ukraine",
        "AE" => "United Arab Emirates",
        "GB" => "United Kingdom",
        "US" => "United States",
        "UM" => "United States Minor Outlying Islands",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan",
        "VU" => "Vanuatu",
        "VE" => "Venezuela",
        "VN" => "Viet Nam",
        "VG" => "Virgin Islands, British",
        "VI" => "Virgin Islands, U.S.",
        "WF" => "Wallis and Futuna",
        "EH" => "Western Sahara",
        "YE" => "Yemen",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe");

}
