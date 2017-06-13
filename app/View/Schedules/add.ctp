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
<div class="schedules form">
<?php echo $this->Form->create('Schedule'); ?>
	<fieldset>
		<legend><?php echo __('Add  Schedule'); ?></legend>
	<?php
		echo $this->Form->input('account_id');
		echo $this->Form->input('calltype_id');
		echo $this->Form->input('start_date');
		echo $this->Form->input('end_date');
		echo $this->Form->input('start_day');
		echo $this->Form->input('end_day');
		echo $this->Form->input('start_time');
		echo $this->Form->input('end_time');
		echo $this->Form->input('instructions');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List  Schedules'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List  Calltypes'), array('controller' => '_calltypes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New  Calltype'), array('controller' => '_calltypes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List  Actions'), array('controller' => '_actions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New  Action'), array('controller' => '_actions', 'action' => 'add')); ?> </li>
	</ul>
</div>
