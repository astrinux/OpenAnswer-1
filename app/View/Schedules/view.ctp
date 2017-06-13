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
<div class="schedules view">
<h2><?php  echo __(' Schedule'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Account Id'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['account_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __(' Calltype'); ?></dt>
		<dd>
			<?php echo $this->Html->link($Schedule['Calltype']['name'], array('controller' => '_calltypes', 'action' => 'view', $Schedule['Calltype']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Start Date'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['start_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('End Date'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['end_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Start Day'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['start_day']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('End Day'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['end_day']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Start Time'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['start_time']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('End Time'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['end_time']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Instructions'); ?></dt>
		<dd>
			<?php echo h($Schedule['Schedule']['instructions']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit  Schedule'), array('action' => 'edit', $Schedule['Schedule']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete  Schedule'), array('action' => 'delete', $Schedule['Schedule']['id']), null, __('Are you sure you want to delete # %s?', $Schedule['Schedule']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List  Schedules'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New  Schedule'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List  Calltypes'), array('controller' => '_calltypes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New  Calltype'), array('controller' => '_calltypes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List  Actions'), array('controller' => '_actions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New  Action'), array('controller' => '_actions', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related  Actions'); ?></h3>
	<?php if (!empty($Schedule['Action'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Schedule Id'); ?></th>
		<th><?php echo __('Action Text'); ?></th>
		<th><?php echo __('Action Type'); ?></th>
		<th><?php echo __('Eid'); ?></th>
		<th><?php echo __('Account Id'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($Schedule['Action'] as $Action): ?>
		<tr>
			<td><?php echo $Action['id']; ?></td>
			<td><?php echo $Action['sort']; ?></td>
			<td><?php echo $Action['schedule_id']; ?></td>
			<td><?php echo $Action['action_text']; ?></td>
			<td><?php echo $Action['action_type']; ?></td>
			<td><?php echo $Action['eid']; ?></td>
			<td><?php echo $Action['account_id']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => '_actions', 'action' => 'view', $Action['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => '_actions', 'action' => 'edit', $Action['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => '_actions', 'action' => 'delete', $Action['id']), null, __('Are you sure you want to delete # %s?', $Action['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New  Action'), array('controller' => '_actions', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
