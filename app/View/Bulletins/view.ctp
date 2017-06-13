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
<style>
	div.bbs label {display: block; float:left; width:100px; text-align:right; margin-right: 10px;}
	div.bbs .lcol {width: 60%; float:left; margin-right:10px;}
	div.bbs .rcol {width: 35%; float:right;}
</style>

<div class="bbs form">
  <?php echo $this->Form->create('Bulletin', array('id' => 'addBulletin')); ?>
  <div class="lcol">
<h1>Create New Message</h1>
		<?php
//		echo $this->Form->input('note', array('div' => false, 'disabled' => 'true', 'value' => $bulletin['Bulletin']['note'], 'type' => 'textarea', 'rows' => '10', 'cols' => '40'));
		echo $this->Form->input('id', array('type' => 'hidden'));
		//echo $this->Form->input('note', array('div' => false, 'label' => false, 'class' => 'htmleditor', 'disabled' => 'disabled', 'value' => $bulletin['Bulletin']['note'], 'type' => 'textarea', 'minHeight' => '300'));
		echo $bulletin['Bulletin']['note'];
?>
<br>
</div>
<div class="rcol">
<h2>Recipients</h2>
<a href="#" onclick="toggleCheckboxes('all'); return false;">all</a>&nbsp;|&nbsp;<a href="#" onclick="toggleCheckboxes('operator'); return false;">operators</a>&nbsp;|&nbsp;<a href="#" onclick="toggleCheckboxes('manager'); return false;">managers</a>&nbsp;|&nbsp;<a href="#" onclick="toggleCheckboxes('admin'); return false;">admins</a><br><br>
<table cellpadding="2" cellspacing="0" class="gentbl">
<tr><td><b>User</b></td><td><b>Reviewed</b></td></tr> 
<?php
foreach ($recipients as $r) {
  echo '<tr>';
  echo '<td>' . $r['User']['firstname'] . ' ' . $r['User']['lastname'] . '</td>';
  if ($r['BulletinRecipient']['ack_ts'] && ($r['BulletinRecipient']['ack_ts'] != '0000-00-00 00:00:00')) echo '<td>' . $r['BulletinRecipient']['ack_ts'] . '</td>';
  else echo '<td>No</td>';
  echo '</tr>';
}

?>
</table>
</div>
</form>
</div>

<script>
function toggleCheckboxes(class_name) {
  $('.'+class_name + ' input:checkbox').trigger('click');
}
  $(document).ready(function() {  
    $('.htmleditor').jqte();

    $('#bb_save_btn').off('click');
    $('#bb_save_btn').on('click', function() {
      alert('You cannot edit a bulletin');
    });
	});   
</script> 
