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
	if (sizeof($data) < 1) {
	  echo '<h3>Deliveries (0)</h3>';
	  ?>
	<table cellpadding="0" cellspacing="0" class="gentbl msg_deliveries">
    <tr><td colspan="5" align="right"> <a href="#" onclick="$('.msg_deliveries').parent('div').load('/Messages/msg_deliveries/<?php echo $message_id; ?>'); return false;"><img src="/img/icons/recycle.png" width="16" height="16"></a></td></tr>
		<tr>
	</table>
	<?php
	}
	else {
	  echo '<h3>Deliveries ('.sizeof($data).')</h3>';
	  ?>
	<table cellpadding="0" cellspacing="0" class="gentbl">
    <tr><td colspan="5" align="right"> <a href="#" onclick="loadMsgDeliveries(<?php echo $message_id; ?>); return false;"><img src="/img/icons/recycle.png" width="16" height="16"></a></td></tr>
		<tr>
			<th width="160" align="left">Date</th>
			<th width="80" align="left">User</th>
			<th width="130" align="left">Recipient</th>
			<th width="130" align="left">Method</th>
			<th width="300" align="left">Number/ Address</th>
		</tr>
	<?php
	foreach ($data as $d) {
	  $e = $d['MessagesDelivery'];
		echo '<tr><td>'.$d[0]['delivered_time_f'].'</td>';
		echo '<td>'.($d['User']['username']? $d['User']['username']: 'Auto').'</td>';
		echo '<td>'.str_replace(',', ', ', $e['delivery_name']).'</td>';
		echo '<td>';
		if (!empty($e['delivery_method'])) {
		  if (isset($global_options['contact_types'][$e['delivery_method']])) echo $global_options['contact_types'][$e['delivery_method']];
		  else echo str_replace(',', ', ', "[BTN] " . $e['delivery_contact_label']).'</td>';
		}
		else {
      if ($e['delivered_by_userid'] == '0') {
		    echo 'Message Summary</td>';
		  } 		  
		  else echo str_replace(',', ', ', "[BTN] " . $e['delivery_contact_label']).'</td>';
		}
		echo '<td>'.str_replace(',', ', ', $e['delivery_contact']).'</td></tr>';
	}
	?>
	</table>
	<?php
}
?>