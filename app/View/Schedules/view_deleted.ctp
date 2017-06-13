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
$this->extend('/Common/view');
?>
<div class="schedules index">
	<h2><?php echo __(' Schedules'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
			<th width="100"><?php echo $this->Paginator->sort('id'); ?></th>
			<th width="150"><?php echo $this->Paginator->sort('deleted'); ?></th>
			<th><?php echo $this->Paginator->sort('calltype'); ?></th>
			<th><?php echo $this->Paginator->sort('schedule'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	
	foreach ($schedules as $Schedule): ?>
	<tr>
		<td><?php echo $Schedule['Schedule']['id']; ?>&nbsp;</td>
		<td><?php echo date('D m/d/y g:ia', strtotime($Schedule['Schedule']['deleted_ts'])); ?>&nbsp;</td>
		<td><?php echo $Schedule['Calltype']['title']; ?></td>
		<td><?php echo h($Schedule['Schedule']['schedule']); ?>&nbsp;</td>
		<td class="actions">
			<a href="#" onclick="window.open('/Schedules/view_script/<?php  echo $Schedule['Schedule']['id']; ?>','_blank','width=800,height=700,resizable=1,scrollbars=1,location=yes,menubar=yes,toolbar=yes');return false;">view</a>
			<?php echo $this->Form->postLink(__('undelete'), array('action' => 'undelete', $Schedule['Schedule']['id']), null, __('Are you sure you want to undelete # %s?', $Schedule['Schedule']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
