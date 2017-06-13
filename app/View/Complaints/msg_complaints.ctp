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
  	if (sizeof($Complaints) < 1) {
  	  echo '<h3>Complaints</h3>';
  	  ?>
  	<table cellpadding="0" cellspacing="0" class="data" width="100%">
    <tr><td colspan="3" align="right"> <a href="#" onclick="loadMsgComplaints(<?php echo $message_id; ?>); return false;"><img src="/img/icons/recycle.png" width="16" height="16"></a></td></tr>
    </table>
    <?php
  	}
  	else {
  	  echo '<h3>Complaints ('.sizeof($Complaints).')</h3>';
  	  ?>
  	<table cellpadding="0" cellspacing="0" class="data" width="100%">
    <tr><td colspan="3" align="right"> <a href="#" onclick="loadMsgComplaints(<?php echo $message_id; ?>); return false;"><img src="/img/icons/recycle.png" width="16" height="16"></a></td></tr>
    <tr>
  			<th width="160">Incident date</th>
  			<th width="240">Category</th>
  		</tr>
  	<?php
  	foreach ($Complaints as $e) {
  		echo '<tr onclick="openDialogWindow(\'/Complaints/edit/'.$e['Complaint']['id'].'\' , \'Complaint edit\', null, function() {
					loadMsgComplaints(\''. $message_id.'\'); return false;
				}, 900, 600);">';
  		echo '<td align="center">'.$e['Complaint']['incident_date_f'].'</td>';
  		echo '<td align="center">'.$e['Complaint']['category'].'</td></tr>';
  	}
  	?>
  	</table>	
    <?php } ?>