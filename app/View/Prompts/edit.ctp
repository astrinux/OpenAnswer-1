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
<div class="ccactPrompts form">
<?php echo $this->Form->create('CcactPrompt'); ?>
	<fieldset>
		<legend><?php echo __('Edit Ccact Prompt'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('account_id');
		echo $this->Form->input('action_id');
		echo $this->Form->input('type');
		echo $this->Form->input('options');
		echo $this->Form->input('caption');
		echo $this->Form->input('sort');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('CcactPrompt.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('CcactPrompt.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Ccact Prompts'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Ccact Clients'), array('controller' => 'ccact_clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ccact Client'), array('controller' => 'ccact_clients', 'action' => 'add')); ?> </li>
	</ul>
</div>
