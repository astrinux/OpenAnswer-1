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
if ($Notes) {
  echo '<h3>Notes ('.sizeof($Notes).')</h3>';  
  ?>
<div class="notes index" id="msg_notes">
	<table cellpadding="0" cellspacing="0" class="gentbl">
	<tr>
			<th width="100">Username</th>
			<th width="150">Created</th>
			<th width="200">Note</th>
			<th width="80">Action</th>
	</tr>
	<?php
	foreach ($Notes as $Note): ?>
	<tr>
		<td><?php echo h($Note['Note']['user_username']); ?></td>
		<td><?php echo h($Note['Note']['created']); ?></td>
		<td width="500"><div class="descr"><?php echo $Note['Note']['description']; ?></div></td>
    <td class="actions">
			<a href="#" onclick="openNoteDialog('', '', 'edit', '<?php echo $Note['Note']['id']; ?>', function() {loadMsgNotes('<?php echo $message_id; ?>');}); return false;"><img title="edit" alt="edit" src="/img/edit.png" width="16" height="16"></a>
			<?php echo '<a href="#" onclick="user_confirm(\'Are you sure you want to delete this entry?\', function () {deleteMsgNote('.$Note['Note']['id'].');}); return false; "><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>'; ?>			
		</td>		
	</tr>
<?php endforeach; ?>
	</table>
</div>
<?php
}
else 	  echo '<h3>Notes</h3>';
?>

<script>
function deleteMsgNote(note_id) {
    var url = '/Notes/delete/' + note_id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'json',
  	      	type: 'GET'
  	      }).done(function(data) {
            if (data.success) {
    					loadMsgNotes('<?php echo $message_id; ?>');
            }
            alert(data.msg);
  	      });	
}

$(function () {
  $('#msg_notes .descr').readmore({
    speed: 75,
    maxHeight: 24
  });

});
</script>