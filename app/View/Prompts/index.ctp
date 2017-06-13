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
<div class="ccactPrompts index">
	<h2><?php echo __('Ccact Prompts'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('account_id'); ?></th>
			<th><?php echo $this->Paginator->sort('action_id'); ?></th>
			<th><?php echo $this->Paginator->sort('type'); ?></th>
			<th><?php echo $this->Paginator->sort('options'); ?></th>
			<th><?php echo $this->Paginator->sort('caption'); ?></th>
			<th><?php echo $this->Paginator->sort('sort'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($ccactPrompts as $ccactPrompt): ?>
	<tr>
		<td><?php echo h($ccactPrompt['CcactPrompt']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($ccactPrompt['CcactClient']['name'], array('controller' => 'ccact_clients', 'action' => 'view', $ccactPrompt['CcactClient']['id'])); ?>
		</td>
		<td><?php echo h($ccactPrompt['CcactPrompt']['action_id']); ?>&nbsp;</td>
		<td><?php echo h($ccactPrompt['CcactPrompt']['type']); ?>&nbsp;</td>
		<td><?php echo h($ccactPrompt['CcactPrompt']['options']); ?>&nbsp;</td>
		<td><?php echo h($ccactPrompt['CcactPrompt']['caption']); ?>&nbsp;</td>
		<td><?php echo h($ccactPrompt['CcactPrompt']['sort']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $ccactPrompt['CcactPrompt']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $ccactPrompt['CcactPrompt']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $ccactPrompt['CcactPrompt']['id']), null, __('Are you sure you want to delete # %s?', $ccactPrompt['CcactPrompt']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Ccact Prompt'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Ccact Clients'), array('controller' => 'ccact_clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ccact Client'), array('controller' => 'ccact_clients', 'action' => 'add')); ?> </li>
	</ul>
</div>
