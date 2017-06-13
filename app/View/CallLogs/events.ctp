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
	if (sizeof($events) < 1) {
	  echo '<h3>Events</h3>';
	}
	else {
	  echo '<h3>Events ('.sizeof($events).')</h3>';
?>
<table cellpadding="2" cellspacing="0" border="0" class="gentbl calllog_eventlist" width="100%">
<tr><td colspan="3" align="right"> <a href="#" onclick="$('.calllog_eventlist').parent('div').load('/CallLogs/events/<?php echo $call_id; ?>', function(response) {
				//var html = $('#tab-events h3:first').html();
				//$('#msg_center li[aria-controls=tab-events] a').text(html);    
				//msgWinLayout.resizeAll();
			} );   return false;"><img src="/img/icons/recycle.png" width="16" height="16"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" value="1" onclick=" $('.call_event').toggle(); "> hide details</a></td></tr>
<tr><th>Date</th><th>Username</th><th>Description</th></tr>
<?php 
foreach ($events as $e) {
  $c = $e['CallEvent'];
  if ($c['event_type'] == EVENT_FILL_PROMPT ||  $c['event_type'] == EVENT_MINDERCLICK || $c['event_type'] == EVENT_DEBUG) $class = ' class="call_event "';
  else $class = '';
  echo '<tr'.$class.'><td>' . $e[0]['createdf'] . '</td>';
  echo '<td>'. $e['User']['username'] . '</td>';
  if ($c['event_type'] == '2') {
    $button_data = unserialize($c['button_data']);
    if (isset($button_data['button_type']) && $button_data['button_type']) 
      echo '<td>['.$button_data['button_type'].' BTN] ';
    else
      echo '<td>[BTN CLICK] ';
    if (isset($button_data['emp_name']) && $button_data['emp_name']) echo $button_data['emp_name'] . ' - ' ;
    echo $button_data['blabel'];
    if (isset($button_data['bfulldata'])) echo ' - '.$button_data['bfulldata'].'</td>';    
  }
  else
    echo '<td>'. $c['description'] . '</td>';
  echo '</tr>';
}
?>
</table>
<?php
}
?>
