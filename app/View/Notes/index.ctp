
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
$this->Paginator->options(array(
    'update' => '#did_content',
    'evalScripts' => true
));
?>
<div class="notesdiv ">
  <div class="panel-content tblheader" id="notes_index">
	<h2><?php echo __(' Notes'); ?></h2>
	<div class="actionbox"><a href="#" onclick="openNoteDialog('<?php echo $account_id; ?>', '<?php echo $did_id; ?>', 'add', '', function () {loadPage(null, '/Notes/index/<?php echo $did_id; ?>' , 'did-content');}); return false;">Add Note</a></div>
	<?php
	echo $this->Element('paging');
	?>	
  </div>	
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
			<th width="100" align="left"><?php echo $this->Paginator->sort('user_username', 'User'); ?></th>
			<th width="160"><?php echo $this->Paginator->sort('visible', 'Visible to operators'); ?></th>
			<th width="100"><?php echo $this->Paginator->sort('created'); ?></th>
			<th width="500"><?php echo $this->Paginator->sort('description'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($Notes as $Note): ?>
	<tr>
		<td align="center"><?php echo h($Note['Note']['user_username']); ?></td>
		<td align="center"><?php echo $Note['Note']['visible']? '<b>Yes</b>': 'No'; ?>
		<?php
		if (!empty($Note['Note']['start_date']) && !empty($Note['Note']['end_date'])) {
		  echo '<br>' . date('D M j, Y g:i a', strtotime($Note['Note']['start_date'])) . '<br>to<br>' . date('D M j, Y g:i a', strtotime($Note['Note']['end_date']));
		}
		$description = strip_tags($Note['Note']['description'], '<br>');
		?>
		</td>
		<td align="center"><?php echo h($Note['Note']['created_f']); ?></td>
		
		<td><div class="descr"><?php echo $description; ?></div></td>
		<td class="actions">
			<a href="#" onclick="openNoteDialog('', '', 'edit', '<?php echo $Note['Note']['id']; ?>', function() {loadPage(null, '/Notes/index/<?php echo $did_id; ?>?' , 'did-content');}); return false;"><img title="edit" alt="edit" src="/img/edit.png" width="16" height="16"></a>
			<?php echo '<a href="#" onclick="user_confirm(\'Are you sure you want to delete this entry?\', function () {deleteNote('.$Note['Note']['id'].');}); return false; "><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>'; ?>			
		</td>
	</tr>
<?php endforeach; ?>
	</table>



</div>
<?php
echo $this->Js->writeBuffer();
?>
<script>
function deleteNote(note_id) {
    var url = '/Notes/delete/' + note_id;
  	      $.ajax({
  	      	url: url,
  	      	dataType: 'json',
  	      	type: 'GET'
  	      }).done(function(data) {
            if (data.success) {
    					loadPage(this, '/Notes/index/<?php echo $did_id; ?>', 'did-content');          
            }
            alert(data.msg);
  	      });	
}

$(function () {
  $('#notes_index .descr').readmore({
    speed: 75,
    maxHeight: 100
  });
});
</script>
