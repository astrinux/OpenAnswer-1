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
<div class="panel-content">
<h1><?php echo $calls['DidNumber']['company']; ?></h1>
<table cellpadding="2" cellspacing="0" border="0" class="gentbl">
  <tr>
    <td align="right"><b>Call#</b>:</td>
    <td><?php echo $calls['CallLog']['id']; ?></td>
  </tr>
  <tr>
    <td align="right"><b>Caller</b>:</td>
    <td><?php echo $calls['CallLog']['cid_name'] . ' - ' . $calls['CallLog']['cid_number']; ?></td>
  </tr>
  <tr title="Total call duration (talk time/ wrap-up time)">
    <td align="right"><b>Duration</b>:</td>
    <td><?php echo $this->element('formatDuration', array('t' => ($calls[0]['duration'])+ $calls[0]['wrapup'])); ?> (<?php echo $this->element('formatDuration', array('t' => $calls[0]['duration'])); ?>/ <?php echo $this->element('formatDuration', array('t' => $calls[0]['wrapup'])); ?>)</td>
  </tr>
  <tr>
    <td align="right"><b>Unique ID</b>:</td>
    <td><?php echo $calls['CallLog']['unique_id']; ?></td>
  </tr> 
  <tr>
    <td align="right"><b>Date</b>:</td>
    <td><?php echo date('D n/d/y g:i:s a', strtotime($calls['CallLog']['start_time'])); ?> &nbsp;&nbsp;&nbsp;<b>Operator</b>:&nbsp;<?php echo $calls['User']['firstname'] . " " . $calls['User']['lastname'] . ' (Ext '.$calls['CallLog']['extension']. ')'; ?> &nbsp;&nbsp;&nbsp;<b>Queue</b>:&nbsp;<?php echo $calls['CallLog']['queue']; ?>
    </td>
  </tr>
  <tr>
    <td align="right"><b>Call Ended</b>:</td>
    <td><?php echo date('D n/d/y g:i:s a', strtotime($calls['CallLog']['end_time'])); ?></td>
  </tr> 
  <tr>
    <td align="right"><b>Wrapped-up</b>:</td>
    <td><?php echo date('D n/d/y g:i:s a', strtotime($calls['CallLog']['wrapup_time'])); ?></td>
  </tr> 
  
</table>
<br><br>
<h2>Call Events</h2>
</div>

<table cellpadding="2" cellspacing="0" border="0" class="gentbl" width="100%">
<tr><th>Date</th><th>Username</th><th>Description</th></tr>
<?php 
foreach ($events as $e) {
  $c = $e['CallEvent'];
	if ($c['event_type'] == EVENT_DEBUG) {
	    if (!$this->Permissions->isAuthorized('CalllogsViewDebug',$permissions)) {
		    continue;
	    }
	}  
  echo '<tr><td>' . $c['created'] . '</td>';
  echo '<td>'. $e['User']['username'] . '</td>';
  if ($c['event_type'] == '2') {
    $button_data = unserialize($c['button_data']);
    if (!empty($button_data['emp_name'])) $data = $button_data['emp_name'] . ' ';
    else $data = '';
    if (!empty($button_data['bfulldata'])) {
        $data .= $button_data['bfulldata'];
    }
    else if (!empty($button_data['bdata'])) {
        $data = $button_data['bdata'];
    }
    else $data = '';
    
    echo '<td>[BTN CLICK] '. $button_data['blabel'] . ' - '.$data.'</td>';    
  }
  else
    echo '<td>'. $c['description'] . '</td>';
  echo '</tr>';
}
?>
</table>

<script>


  $(document).ready(function() {  
    $('#did_save_btn').prop('disabled', true);
  });
</script>