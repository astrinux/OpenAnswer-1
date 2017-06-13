<?php
/**
 *
 * @author          VoiceNation, LLC
 * @copyright       2015-2016, VoiceNation LLC
 * @link            http://www.voicenation.com
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU Affero General Public License as
 *   published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.

 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.

 *   You should have received a copy of the GNU Affero General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<?php

        $day_range = '';
        if ($schedule['start_day'] && $schedule['end_day']) {
          $day1 = substr($schedule['start_day'], 0, -4);
          $ts = strtotime("today " . substr($schedule['start_day'], -4, 2) . ":" . substr($schedule['start_day'], -2) . ":00");
          $mytime1 = date('g:ia', $ts); 		  		  
          $day2 = substr($schedule['end_day'], 0, -4);
          if ($day2 > 7) $day2 -= 7;
          $ts = strtotime("today " . substr($schedule['end_day'], -4, 2) . ":" . substr($schedule['end_day'], -2) . ":00");
          $mytime2 = date('g:ia', $ts); 		  		  
    
          $day_range = $php_daysofweek[$day1] . " " . $mytime1 . " - " . $php_daysofweek[$day2] . " " . $mytime2;
          
        }
	        $schedule['days'] = getDayRanges($schedule, $php_daysofweek);
    		$schedule['day_range'] = $day_range;

	$s_array = array();
		if ($schedule['start_date'] && $schedule['end_date']) {
			$s_array[] = $schedule['startdate'] . ' - ' . $schedule['enddate'];
		}
		/*if ($schedule['start_day'] && $schedule['end_day']) {
			$s_array[] = $daysofweek[$schedule['start_day']] . ' - ' . $daysofweek[$schedule['end_day']] . " " . $schedule['starttime'] . ' - ' . $schedule['endtime'];
		}*/
		if ($schedule['day_range']) echo $schedule['day_range'] . "<br>";
		if (sizeof($schedule['days'])) {
		  $temp = implode(', ', $schedule['days']);
		  if ($schedule['starttime'] && $schedule['endtime']) $temp .= (' ' . $schedule['starttime'] . '-' . $schedule['endtime']);
		  $s_array[] = $temp;
		}
    if (sizeof($s_array)) echo implode('<br>', $s_array);
    else if (!$schedule['day_range']) echo 'Default';
?>