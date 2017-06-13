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
<div class="ccactMessages form">
<?php echo $this->Form->create('CcactMessage'); ?>
	<fieldset>
		<legend><?php echo __('Add Ccact Message'); ?></legend>
	<?php
		echo $this->Form->input('account_id');
		echo $this->Form->input('operator_id');
		echo $this->Form->input('call_id');
		echo $this->Form->input('delivered');
		echo $this->Form->input('delivered_to_name');
		echo $this->Form->input('delivered_to_enumber');
		echo $this->Form->input('delivered_time');
		echo $this->Form->input('delivered_by_userid');
		echo $this->Form->input('delivery_method');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Ccact Messages'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Ccact Messages Events'), array('controller' => 'ccact_messages_events', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ccact Messages Event'), array('controller' => 'ccact_messages_events', 'action' => 'add')); ?> </li>
	</ul>
</div>
