<?php
/**
* jWeather by ip. Module for Joomla 4.x
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
**/
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
// No direct access
defined('_JEXEC') or die;
use JweatherbyipNamespace\Module\Jweatherbyip\Site\Helper\JweatherbyipHelper;
use Joomla\CMS\Helper\ModuleHelper;

$color_title       = $params->get('color_title');
$font_size_title   = $params->get('font_size_title');
$color_weather     = $params->get('color_weather');
$font_size_weather = $params->get('font_size_weather');
$good_day     = $params->get('good_day');
$good_evening = $params->get('good_evening');
$good_night   = $params->get('good_night');
$good_morning = $params->get('good_morning');
$height_map = $params->get('height_map');
$api_yandexmap = $params->get('api_yandexmap');
$api_googlemap = $params->get('api_googlemap');
$weather_source_choose = $params->get('weather_source_choose');
$greeting = $params->get('greeting');
$map = $params->get('map');
$html_for_balloon = $params->get('html_for_balloon');
$zoom = $params->get('zoom');
$table_td_style = $params->get('table_td_style');
$start = JweatherbyipHelper::getStart($params);
$names = JweatherbyipHelper::getNames($params);
$source = JweatherbyipHelper::getSource($params);
$values = JweatherbyipHelper::getValues($params);
$lang = JweatherbyipHelper::getLang($params);
$icon_open_meteo = JweatherbyipHelper::icons_open_meteo($source[0]);

require ModuleHelper::getLayoutPath('mod_jweather_by_ip', $params->get('layout', 'default'));
