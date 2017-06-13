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
<div class="schedules index">
	<h2><?php echo __(' Schedules'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('account_id'); ?></th>
			<th><?php echo $this->Paginator->sort('calltype_id'); ?></th>
			<th><?php echo $this->Paginator->sort('start_date'); ?></th>
			<th><?php echo $this->Paginator->sort('end_date'); ?></th>
			<th><?php echo $this->Paginator->sort('start_day'); ?></th>
			<th><?php echo $this->Paginator->sort('end_day'); ?></th>
			<th><?php echo $this->Paginator->sort('start_time'); ?></th>
			<th><?php echo $this->Paginator->sort('end_time'); ?></th>
			<th><?php echo $this->Paginator->sort('instructions'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($Schedules as $Schedule): ?>
	<tr>
		<td><?php echo h($Schedule['Schedule']['id']); ?>&nbsp;</td>
		<td><?php echo h($Schedule['Schedule']['account_id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($Schedule['Calltype']['name'], array('controller' => '_calltypes', 'action' => 'view', $Schedule['Calltype']['id'])); ?>
		</td>
		<td><?php echo h($Schedule['Schedule']['start_date']); ?>&nbsp;</td>
		<td><?php echo h($Schedule['Schedule']['end_date']); ?>&nbsp;</td>
		<td><?php echo h($Schedule['Schedule']['start_day']); ?>&nbsp;</td>
		<td><?php echo h($Schedule['Schedule']['end_day']); ?>&nbsp;</td>
		<td><?php echo h($Schedule['Schedule']['start_time']); ?>&nbsp;</td>
		<td><?php echo h($Schedule['Schedule']['end_time']); ?>&nbsp;</td>
		<td><?php echo h($Schedule['Schedule']['instructions']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $Schedule['Schedule']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $Schedule['Schedule']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $Schedule['Schedule']['id']), null, __('Are you sure you want to delete # %s?', $Schedule['Schedule']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New  Schedule'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List  Calltypes'), array('controller' => '_calltypes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New  Calltype'), array('controller' => '_calltypes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List  Actions'), array('controller' => '_actions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New  Action'), array('controller' => '_actions', 'action' => 'add')); ?> </li>
	</ul>
</div>
