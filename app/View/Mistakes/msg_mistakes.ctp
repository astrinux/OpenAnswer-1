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
	  echo '<h3>Mistakes</h3>';
	  ?>
  	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
    <tr><td colspan="4" align="right"> <a href="#" onclick="loadMsgMistakes(<?php echo $message_id; ?>); return false;"><img src="/img/icons/recycle.png" width="16" height="16"></a></td></tr>
    </table>	  
	  <?php
	}
	else {
	  echo '<h3>Mistakes ('.sizeof($data).')</h3>';
	  ?>
	<table cellpadding="0" cellspacing="0" class="gentbl">
    <tr><td colspan="4" align="right"> <a href="#" onclick="loadMsgMistakes(<?php echo $message_id; ?>); return false;"><img src="/img/icons/recycle.png" width="16" height="16"></a></td></tr>
    <tr>
			<th width="160" align="left">Date</th>
			<th width="80" align="left">Auditor</th>
			<th width="100" align="left">Recipient</th>
			<th width="350" align="left">Mistake</th>
		</tr>
	<?php
	foreach ($data as $d) {
		echo '<tr><td>'.$d['Mistake']['created'].'</td>';
		echo '<td>'.$operators[$d['Mistake']['user_id']].'</td>';
		echo '<td>'.$operators[$d['Mistake']['mistake_recipient']].'</td>';
		echo '<td>'.$d['Mistake']['category'];
		if (trim($d['Mistake']['category_other'])) echo ' - ' . $d['Mistake']['category_other'];
		echo '<br>' . $d['Mistake']['description']; 
		echo '</td>';
		echo '</tr>';
	}
	?>
	</table>
	<?php
}
?>