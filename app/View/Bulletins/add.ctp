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

<div class="bbs form" id="bbs_form">
  <?php echo $this->Form->create('Bulletin', array('id' => 'addBulletin')); ?>
  <div class="lcol">
<h1><i class="fa <?php echo $global_options['icons']['bulletins']; ?>"></i> Create New Bulletin</h1>
		<?php
		echo $this->Form->input('id', array('type' => 'hidden'));
		echo $this->Form->input('note', array('div' => false, 'label' => false, 'class' => 'htmleditor', 'type' => 'textarea', 'minHeight' => '300'));
?>
<br>
</div>
<div class="rcol">
<h2>Select Recipients</h2>
<a href="#" onclick="$('#bbs_form .all input:checkbox').prop('checked', false); return false;">clear all</a>&nbsp;|&nbsp;<a href="#" onclick="$('#bbs_form .all  input:checkbox').prop('checked', true);  return false;">select all</a>&nbsp;|&nbsp;<a href="#" onclick="toggleCheckboxes('operator'); return false;">operators</a>&nbsp;|&nbsp;<a href="#" onclick="toggleCheckboxes('manager'); return false;">managers</a>&nbsp;|&nbsp;<a href="#" onclick="toggleCheckboxes('admin'); return false;">admins</a><br><br>
<table cellpadding="2" cellspacing="0" class="gentbl">
<tr><td></td><td><b>User</b></td><td><b>Role</b></td></tr> 
<?php
foreach ($users as $k=>$u) {
  $classes = array();
  $classes[] = 'all';
  if ($u['User']['role'] == 'A') $classes[] = 'admin';
  else if ($u['User']['role'] == 'O') $classes[] = 'operator';
  else if ($u['User']['role'] == 'M') $classes[] = 'manager';
  
  echo '<tr class="'.implode(' ' , $classes).'">';
  echo '<td>' . $this->Form->input('Bulletin.uid.'.$k, array('hiddenField' => false, 'type' => 'checkbox', 'value' => $u['User']['id'], 'div' => false, 'label' => false)) . '</td>';
  echo '<td>' . $u['User']['firstname'] . ' ' . $u['User']['lastname'] . '</td>';
  echo '<td>' . $global_options['roles'][$u['User']['role']] . '</td>';
  echo '</tr>';
}
?>
</table>
</div>
</form>
</div>

<script>
function toggleCheckboxes(class_name) {
  $('#bbs_form .'+class_name + ' input:checkbox').prop('checked', true);
}
  $(document).ready(function() {  
    $('#bbs_form .htmleditor').jqte();
    $('#bb_save_btn').off('click');
    $('#bb_save_btn').on('click', function() {
      addBulletin();
    });
	});   
</script> 
