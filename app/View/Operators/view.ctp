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
<div class="operators view">
<h2><?php  echo __('Operator'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($operator['Operator']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Username'); ?></dt>
		<dd>
			<?php echo h($operator['Operator']['username']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Password'); ?></dt>
		<dd>
			<?php echo h($operator['Operator']['password']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($operator['Operator']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Extension'); ?></dt>
		<dd>
			<?php echo h($operator['Operator']['extension']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Operator'), array('action' => 'edit', $operator['Operator']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Operator'), array('action' => 'delete', $operator['Operator']['id']), null, __('Are you sure you want to delete # %s?', $operator['Operator']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Operators'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Operator'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Queues'), array('controller' => 'queues', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Queue'), array('controller' => 'queues', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Queues'); ?></h3>
	<?php if (!empty($operator['Queue'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Queue'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($operator['Queue'] as $queue): ?>
		<tr>
			<td><?php echo $queue['id']; ?></td>
			<td><?php echo $queue['queue']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'queues', 'action' => 'view', $queue['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'queues', 'action' => 'edit', $queue['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'queues', 'action' => 'delete', $queue['id']), null, __('Are you sure you want to delete # %s?', $queue['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Queue'), array('controller' => 'queues', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
