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
<div class="ccactMessagesEvents view">
<h2><?php  echo __('Ccact Messages Event'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($ccactMessagesEvent['CcactMessagesEvent']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Account Id'); ?></dt>
		<dd>
			<?php echo h($ccactMessagesEvent['CcactMessagesEvent']['account_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Message Id'); ?></dt>
		<dd>
			<?php echo h($ccactMessagesEvent['CcactMessagesEvent']['message_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Operator Id'); ?></dt>
		<dd>
			<?php echo h($ccactMessagesEvent['CcactMessagesEvent']['operator_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($ccactMessagesEvent['CcactMessagesEvent']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($ccactMessagesEvent['CcactMessagesEvent']['description']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Ccact Messages Event'), array('action' => 'edit', $ccactMessagesEvent['CcactMessagesEvent']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Ccact Messages Event'), array('action' => 'delete', $ccactMessagesEvent['CcactMessagesEvent']['id']), null, __('Are you sure you want to delete # %s?', $ccactMessagesEvent['CcactMessagesEvent']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Ccact Messages Events'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ccact Messages Event'), array('action' => 'add')); ?> </li>
	</ul>
</div>
