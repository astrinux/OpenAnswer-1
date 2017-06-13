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
    'update' => '#' .$this->request->data['target'],
    'evalScripts' => true
));
?>
<div class="notesdiv">
<div class="notes index">
	<h2><?php echo __(' Notes'); ?></h2>
	<div class="actionbox"><a href="#" onclick="openNoteDialog('<?php echo $account_id; ?>', '<?php echo $did_id; ?>', 'add', '', null); return false;">Add Note</a></div>
	<table cellpadding="0" cellspacing="0" class="gentbl">
	<tr>
			<th><?php echo $this->Paginator->sort('user_username'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($Notes as $Note): ?>
	<tr>
		<td><?php echo h($Note['Note']['user_username']); ?>&nbsp;</td>
		<td><?php echo h($Note['Note']['created']); ?>&nbsp;</td>
		<td><?php echo h($Note['Note']['description']); ?>&nbsp;</td>
		<td class="actions">
			<a href="#" onclick="openNoteDialog('', '', '<?php echo $Note['Note']['id']; ?>', 'edit'); return false;">edit</a>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '&nbsp|&nbsp;'));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>

</div>
<?php
echo $this->Js->writeBuffer();
?>