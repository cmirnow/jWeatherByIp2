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
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

if ($greeting == 1)
{
?>
<!-- BEGIN: Masterpro jWeather By IP -->
<div style="color:<?php echo $color_title;?>;font-size:<?php echo $font_size_title;?>;">
   <script>
      var h=(new Date()).getHours();
      if (h > 23 || h <7) document.write("<?php
         echo $good_night . $location[2];
         ?>");
      if (h > 6 && h < 12) document.write("<?php
         echo $good_morning . $location[2];
         ?>");
      if (h > 11 && h < 19) document.write("<?php
         echo $good_day . $location[2];
         ?>");
      if (h > 18 && h < 24) document. write("<?php
         echo $good_evening . $location[2];
         ?>"); 
   </script>
</div>
<?php
}
echo '<div style="color:' . $color_weather . ';font-size:' . $font_size_weather . ';">';
if ($weather_source_choose == 4)
{
$html = $html_for_balloon;
}
else
{
$html = '<table class="jwbyip">';
$i = 1;
if (in_array(1, $values))
{
    switch ($weather_source_choose)
    {
        case 1:
            $html .= '<tr><td><img src="https://openweathermap.org/img/w/' . $source[0] . '.png" title="' . $source[1] . '"></td></tr>';
        break;
        case 5:
            $html .= '<tr><td><img src="/modules/mod_jweather_by_ip/img/icons/' . $source[0] . '.png" title="' . $source[1] . '"></td></tr>';
        break;
        case 6:
            $html .= '<tr><td><img src="/modules/mod_jweather_by_ip/img/icons/' . $icon_open_meteo . '.png" title="' . $source[1] . '"></td></tr>';
        break;
        case 7:
            $html .= '<tr><td><img src="/modules/mod_jweather_by_ip/img/meteosource_icons/' . $icon_meteosource . '.png" title="' . $source[1] . '"></td></tr>';
        break;
        default:
            $html .= '<tr><td><img src="' . $source[0] . '" title="' . $source[1] . '"></td></tr>';
    }
}
foreach (array_combine($names, $source) as $names => $source):
    $html .= '<tr>';
    if (in_array($i, $values) and $i > 1)
    {
        $html .= '<td>' . Text::_($names) . '</td>';
        $html .= '<td>' . $source . '</td>';
    }
    $html .= '</tr>';
    $i++;
endforeach;
$html .= '</table>';
}
switch ($map)
{
    case 31:
?>
   <div id="map_ya" style="width:100%;height:<?php echo $height_map; ?>"></div>
   <script src="https://api-maps.yandex.ru/2.1/?lang=<?php echo $lang; ?>&apikey=<?php echo $api_yandexmap; ?>"></script>
   <script type="text/javascript"> 
      var myMap; 
      ymaps.ready(init); 
      function init () {
      var myMap = new ymaps.Map("map_ya", { 
      center: [<?php echo $location[0] . ',' . $location[1]; ?>], 
      zoom: <?php echo $zoom; ?>,
      controls: ['zoomControl','fullscreenControl']
      }); 
      myPlacemark = new ymaps.Placemark([<?php echo $location[0] . ',' . $location[1]; ?>], {
      balloonContent: '<?php echo $html ?>'}); 
      myMap.geoObjects 
      .add(myPlacemark);
      myPlacemark.balloon.open();
      } 
   </script> 

<?php
    break;
    case 30:
?>
<div id="map" style="width:100%;height:<?php echo $height_map; ?>"></div>
<script>
   function initMap() {
     var coord = {lat: <?php echo $location[0]; ?>, lng: <?php echo $location[1]; ?>};
     var map = new google.maps.Map(document.getElementById('map'), {
       zoom: <?php echo $zoom; ?>,
       center: coord
     });
     var contentString = '<div id="content">'+
         '<div id="bodyContent">'+
         '<p><?php echo $html ?></p>'+
         '</div></div>';
     var infowindow = new google.maps.InfoWindow({
       content: contentString
     });
     var marker = new google.maps.Marker({
       position: coord,
       map: map
     });
     marker.addListener('click', function() {
       infowindow.open(map, marker);
     });
     infowindow.open(map, marker);
   }
</script>
<script async defer
   src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_googlemap; ?>&callback=initMap&language=<?php echo $lang; ?>"></script>
<?php
    break;
    default:
        echo $html;
}
?>
</div>
<style>
table.jwbyip td {
<?php echo $table_td_style ?>
}
</style>
<!-- END: Masterpro jWeather By IP -->
