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
<div class="ccactPrompts view">
<h2><?php  echo __('Ccact Prompt'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($ccactPrompt['CcactPrompt']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ccact Client'); ?></dt>
		<dd>
			<?php echo $this->Html->link($ccactPrompt['CcactClient']['name'], array('controller' => 'ccact_clients', 'action' => 'view', $ccactPrompt['CcactClient']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Action Id'); ?></dt>
		<dd>
			<?php echo h($ccactPrompt['CcactPrompt']['action_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($ccactPrompt['CcactPrompt']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Options'); ?></dt>
		<dd>
			<?php echo h($ccactPrompt['CcactPrompt']['options']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Caption'); ?></dt>
		<dd>
			<?php echo h($ccactPrompt['CcactPrompt']['caption']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Sort'); ?></dt>
		<dd>
			<?php echo h($ccactPrompt['CcactPrompt']['sort']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Ccact Prompt'), array('action' => 'edit', $ccactPrompt['CcactPrompt']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Ccact Prompt'), array('action' => 'delete', $ccactPrompt['CcactPrompt']['id']), null, __('Are you sure you want to delete # %s?', $ccactPrompt['CcactPrompt']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Ccact Prompts'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ccact Prompt'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Ccact Clients'), array('controller' => 'ccact_clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ccact Client'), array('controller' => 'ccact_clients', 'action' => 'add')); ?> </li>
	</ul>
</div>
