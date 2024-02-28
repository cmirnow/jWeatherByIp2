<?php
/**
 * jWeather by ip. Module for Joomla 4.x & 5.x
 * @version $Id: mod_jweather_by_ip.php 2015-12-31 $
 * @package: jWeather by ip
 * ===================================================
 * @author
 * Name: Masterpro project, www.masterpro.ws
 * Email: info@masterpro.ws
 * Url: https://masterpro.ws
 * ===================================================
 * @copyright (C) 2015 Alexei Smirnov. All rights reserved.
 * @license GNU GPL 2.0 (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2 as published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

namespace JweatherbyipNamespace\Module\Jweatherbyip\Site\Helper;
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

const WEATHER_CODES_OPEN_METEO = array(
'0' => 'Clear sky',
'1' => 'Mainly clear',
'2' => 'Partly cloudy', 
'3' => 'Overcast',
'45' => 'Fog',
'48' => 'Depositing rime fog',
'51' => 'Light drizzle',
'53' => 'Moderate drizzle',
'55' => 'Drizzle dense intensity',
'56' => 'Light freezing drizzle',
'57' => 'Dense intensity freezing drizzle',
'61' => 'Slight rain', 
'63' => 'Moderate rain', 
'65' => 'Heavy intensity rain',
'66' => 'Light freezing rain',
'67' => 'Heavy intensity freezing rain',
'71' => 'Slight snow fall', 
'73' => 'Moderate snow fall', 
'75' => 'Heavy intensity snow fall',
'77' => 'Snow grains',
'80' => 'Slight rain showers', 
'81' => 'Moderate rain showers', 
'82' => 'Violent rain showers', 
'85' => 'Slight snow showers', 
'86' => 'Heavy snow showers',
'95' => 'Thunderstorm: Slight or moderate',
'96' => 'Thunderstorm with slight hail',
'99' => 'Thunderstorm with heavy hail'
);

class JweatherbyipHelper
{
    public static function getLocation($params)
    {
        $x = $params->get('api_choose');
        switch ($x)
        {
            case 2:
                $loc_array = array(
                    self::getIpgeoloc($params) ["latitude"],
                    self::getIpgeoloc($params) ["longitude"],
                    self::getIpgeoloc($params) ["city"]
                );
            break;
            case 3:
                $loc_array = [$params->get('lat') , $params->get('lon') ];
            break;
            default:
                $loc_array = self::getIpGeoplugin($params);
        }
        return $loc_array;
    }

    public static function getSource($params)
    {
        $x = $params->get('weather_source_choose');
        switch ($x)
        {
            case 0:
                $api_key = $params->get('api_key');
                $num_of_days = 1;
                $coord = self::getLocation($params);
                unset($coord[2]);
                $loc_string = implode(',', $coord);
                $basicurl = sprintf('https://api2.worldweatheronline.com/premium/v1/weather.ashx?key=%s&q=%s&num_of_days=%s', $api_key, $loc_string, intval($num_of_days));
                $xml = simplexml_load_file($basicurl);
                return [$xml
                    ->current_condition->weatherIconUrl, $xml
                    ->current_condition->weatherDesc, $xml
                    ->current_condition->temp_C, $xml
                    ->current_condition->windspeedKmph, $xml
                    ->current_condition->pressure, $xml
                    ->current_condition->humidity, $xml
                    ->current_condition->cloudcover, $xml
                    ->current_condition->visibility, $xml
                    ->current_condition->weatherDesc, $xml
                    ->weather
                    ->astronomy->sunrise, $xml
                    ->weather
                    ->astronomy->sunset, $xml
                    ->weather
                    ->astronomy->moonrise, $xml
                    ->weather
                    ->astronomy->moonset, $xml
                    ->weather->date, $xml
                    ->current_condition->temp_F, $xml
                    ->weather
                    ->astronomy->moon_phase, $xml
                    ->weather
                    ->astronomy->moon_illumination];
            break;
            
            case 1:
                $api_key = $params->get('api_key_owm');
                $num_of_days = 1;
                $json = file_get_contents('https://api.openweathermap.org/data/2.5/weather?lat=' . self::getLocation($params) [0] . '&lon=' . self::getLocation($params) [1] . '&appid=' . $api_key . '&units=metric');
                $obj = json_decode($json, true);
                return [
                $obj['weather']['0']['icon'],
                $obj['weather']['0']['main'],
                $obj['main']['temp'],
                $obj['wind']['speed'],
                $obj['main']['pressure'],
                $obj['main']['humidity'],
                $obj['clouds']['all'],
                $obj['visibility'],
                $obj['weather']['0']['description'],
                HtmlHelper::date(new Date($obj['sys']['sunrise']), Text::_('DATE_FORMAT_FILTER_DATETIME')),
                HtmlHelper::date(new Date($obj['sys']['sunset']), Text::_('DATE_FORMAT_FILTER_DATETIME'))
            ];
            break;

            case 6:
                $json = file_get_contents('https://api.open-meteo.com/v1/forecast?latitude=' . self::getLocation($params) [0] . '&longitude=' . self::getLocation($params) [1] . '&current_weather=true&timezone=auto');
                $obj = json_decode($json, true);
                return [
                $obj['current_weather']['weathercode'],
                WEATHER_CODES_OPEN_METEO[$obj['current_weather']['weathercode']],
                $obj['current_weather']['temperature'],
                $obj['current_weather']['windspeed'],
                $obj['current_weather']['winddirection'],
                $obj['current_weather']['time'],
                $obj['timezone']
            ];
            break;

            case 7:
                $api_key = $params->get('api_key_meteosource');
                $json = file_get_contents('https://www.meteosource.com/api/v1/free/point?lat=' . self::getLocation($params) [0] . '&lon=' . self::getLocation($params) [1] . '&sections=current&language=en&units=metric&key=' . $api_key);
                $obj = json_decode($json, true);
                return [
                $obj['current']['icon_num'],
                $obj['current']['summary'],
                $obj['current']['temperature'],
                $obj['current']['wind']["speed"],
                $obj['current']['wind']['dir'],
                $obj['current']['cloud_cover'],
                $obj['timezone']
            ];
            break;

            case 5:
                $api_key = $params->get('api_key_visualcrossing');
                $json = file_get_contents('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/' . self::getLocation($params) [0] . ',' . self::getLocation($params) [1] . '/today?unitGroup=metric&include=days&key=' . $api_key);
                $obj = json_decode($json, true);
                return [
                $obj["days"][0]['icon'],
                $obj["days"][0]['description'],
                $obj["days"][0]['temp'],
                $obj["days"][0]['windspeed'],
                $obj["days"][0]['pressure'],
                $obj["days"][0]['humidity'],
                $obj["days"][0]['cloudcover'],
                $obj["days"][0]['visibility'],
                $obj["days"][0]['conditions'],
                HtmlHelper::date(new Date($obj["days"][0]['sunriseEpoch']), Text::_('DATE_FORMAT_FILTER_DATETIME')),
                HtmlHelper::date(new Date($obj["days"][0]['sunsetEpoch']), Text::_('DATE_FORMAT_FILTER_DATETIME')),
                $obj["days"][0]['solarradiation'],
                $obj["days"][0]['solarenergy'],
                $obj["days"][0]['moonphase'],
                $obj["days"][0]['uvindex'],
                $obj["days"][0]['severerisk'],
                $obj["days"][0]['dew'],
                $obj["days"][0]['feelslike'],
                $obj["days"][0]['preciptype'][0]
            ];
            break;

            case 3:
                $api_key = $params->get('api_key');
                /* $num_of_days = 1; */
                $coord = self::getLocation($params);
                unset($coord[2]);
                $loc_string = implode(',', $coord);
                $basicurl = sprintf('https://api.worldweatheronline.com/premium/v1/marine.ashx?key=%s&q=%s&tp=24', $api_key, $loc_string);
                $xml = simplexml_load_file($basicurl);
                return [$xml
                    ->weather
                    ->hourly->weatherIconUrl, $xml
                    ->weather
                    ->hourly->weatherDesc, $xml
                    ->weather
                    ->hourly->tempC, $xml
                    ->weather
                    ->hourly->windspeedKmph, $xml
                    ->weather
                    ->hourly->pressure, $xml
                    ->weather
                    ->hourly->humidity, $xml
                    ->weather
                    ->hourly->cloudcover, $xml
                    ->weather
                    ->hourly->visibility, $xml
                    ->weather
                    ->hourly->weatherDesc, $xml
                    ->weather
                    ->astronomy->sunrise, $xml
                    ->weather
                    ->astronomy->sunset, $xml
                    ->weather
                    ->astronomy->moonrise, $xml
                    ->weather
                    ->astronomy->moonset, $xml
                    ->weather->date, $xml
                    ->weather
                    ->hourly->tempF, $xml
                    ->weather
                    ->astronomy->moon_phase, $xml
                    ->weather
                    ->astronomy->moon_illumination, $xml
                    ->weather
                    ->hourly->sigHeight_m, $xml
                    ->weather
                    ->hourly->swellHeight_m, $xml
                    ->weather
                    ->hourly->swellHeight_ft, $xml
                    ->weather
                    ->hourly->swellDir, $xml
                    ->weather
                    ->hourly->swellDir16Point, $xml
                    ->weather
                    ->hourly->swellPeriod_secs, $xml
                    ->weather
                    ->hourly->waterTemp_C, $xml
                    ->weather
                    ->hourly->waterTemp_F];
            break;
            default:
            break;
        }
    }

    public static function getNames($params)
    {
        $main = array(
            'WEATHER_ICON',
            'WEATHER_MAIN',
            'TEMPERC',
            'WINDSPEEDKMPH_FR',
            'PRESSURE_FR',
            'HUMIDITY_FR',
            'CLOUDCOVER_FR',
            'VISIBILITY_FR',
            'WEATHER_DESC'
        );
        $wwo0 = array(
            'SUNRISE_FR',
            'SUNSET_FR',
            'MOONRISE_FR',
            'MOONSET_FR',
            'TODAYSDATE',
            'TEMPER_FR',
            'MOONPHASE',
            'MOONILLUMINATION'
        );
        $wwo3 = array(
            'SIGHEIGHTM',
            'SWELLHEIGHTM',
            'SWELLHEIGHTFT',
            'SWELLDIR',
            'SWELLDIR16POINT',
            'SWELLPERIODSECS',
            'WATERTEMPC',
            'WATERTEMPF'
        );
        $sun = array(
            'SUNRISE',
            'SUNSET'
        );
        $vc = array(
            'SOLARRADIATION',
            'SOLARENERGY',
            'MOONPHASE',
            'UVINDEX',
            'SEVERERISK',
            'DEW',
            'FEELSLIKE',
            'PRECIPTYPE'
        );
        $openmeteo = array(
            'WEATHER_ICON',
            'WEATHER_MAIN',
            'TEMPERC',
            'WINDSPEED',
            'WINDDIRECTION',
            'TIME',
            'TIMEZONE'
        );
        $meteosource = array(
            'WEATHER_ICON',
            'WEATHER_MAIN',
            'TEMPERC',
            'WINDSPEED',
            'WINDDIRECTION',
            'CLOUDCOVER_FR',
            'TIMEZONE'
        );
        $x = $params->get('weather_source_choose');
        switch ($x)
        {
            case 0:
                return array_merge($main, $wwo0);
            break;
            case 1:
                return array_merge($main, $sun);
            break;
            case 3:
                return array_merge($main, $wwo0, $wwo3);
            break;
            case 5:
                return array_merge($main, $sun, $vc);
            break;
            case 6:
                return $openmeteo;
            break;
            case 7:
                return $meteosource;
            break;
            default:
                return $main;
        }
    }

    public static function getValues($params)
    {
        $main = array(
            $params->get('img') ,
            $params->get('title') ,
            $params->get('temp_C') ,
            $params->get('windspeedKmph') ,
            $params->get('pressure') ,
            $params->get('humidity') ,
            $params->get('cloudcover') ,
            $params->get('visibility') ,
            $params->get('weatherDesc')
        );
        $wwo0 = array(
            $params->get('sunrise') ,
            $params->get('sunset') ,
            $params->get('moonrise') ,
            $params->get('moonset') ,
            $params->get('date') ,
            $params->get('temp_F') ,
            $params->get('moonphase') ,
            $params->get('moonillumination')
        );
        $wwo3 = array(
            $params->get('sigheightm') ,
            $params->get('swellheightm') ,
            $params->get('swellheightft') ,
            $params->get('swelldir') ,
            $params->get('swelldir16point') ,
            $params->get('swellperiodsecs') ,
            $params->get('watertempc') ,
            $params->get('watertempf')
        );
        $owm = array(
            $params->get('sunrise') ,
            $params->get('sunset')
        );
        $vc = array(
            $params->get('sunrise_vc') ,
            $params->get('sunset_vc') ,
            $params->get('solarradiation') ,
            $params->get('solarenergy') ,
            $params->get('moonphase_vc') ,
            $params->get('uvindex') ,
            $params->get('severerisk') ,
            $params->get('dew') ,
            $params->get('feelslike') ,
            $params->get('preciptype')
        );
        $openmeteo = array(
            $params->get('img') ,
            $params->get('title') ,
            $params->get('temperature') ,
            $params->get('windspeed') ,
            $params->get('winddirection') ,
            $params->get('time') ,
            $params->get('timezone')
        );
        $meteosource = array(
            $params->get('img') ,
            $params->get('title') ,
            $params->get('temperature') ,
            $params->get('windspeed') ,
            $params->get('winddirection') ,
            $params->get('cloudcover_meteosource') ,
            $params->get('timezone')
        );

        $x = $params->get('weather_source_choose');
        switch ($x)
        {
            case 0:
                return array_merge($main, $wwo0);
            break;
            case 1:
                return array_merge($main, $owm);
            break;
            case 3:
                return array_merge($main, $wwo0, $wwo3);
            break;
            case 5:
                return array_merge($main, $vc);
            break;
            case 6:
                return $openmeteo;
            break;
            case 7:
                return $meteosource;
            break;
            default:
                return $main;
        }
    }

    public static function getLang()
    {
        $lang = Factory::getLanguage()->getTag();
        return substr($lang, 0, 2);
    }

    public static function getIp($params)
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getIpgeoloc($params)
    {
        $apiKey = $params->get('api_key_geoloc');
        $lang = self::getLang($params);
        $fields = "*";
        $excludes = "";
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=" . $apiKey . "&ip=" . self::getIp($params) . "&lang=" . $lang . "&fields=" . $fields . "&excludes=" . $excludes;
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        return json_decode(curl_exec($cURL) , true);
    }

    public static function getIpGeoplugin($params)
    {
        $json = file_get_contents('http://www.geoplugin.net/json.gp?ip=' . self::getIp($params));
        $obj = json_decode($json, true);
        return [$obj['geoplugin_latitude'], $obj['geoplugin_longitude'], $obj['geoplugin_city']];
    }

    public static function icons_open_meteo($x)
    {
  if ($x == '0') {
    return 'clear-day';
} elseif (in_array($x, ['1','2','3'])) { 
    return 'partly-cloudy-day';
} elseif (in_array($x, ['45','48'])) { 
    return 'fog';
} elseif (in_array($x, ['51','53','55','56','57'])) { 
    return 'hail';
} elseif (in_array($x, ['61','63','65','66','67','80','81','82'])) { 
    return 'rain';
} elseif (in_array($x, ['71','73','75','77','85','86'])) { 
    return 'snow';
} elseif (in_array($x, ['95','96','99'])) { 
    return 'thunder';
}
    }
}
