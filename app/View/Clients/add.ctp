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
<div class="ccactClients form">
<?php echo $this->Form->create('CcactClient'); ?>
	<fieldset>
		<legend><?php echo __('Add Ccact Client'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('date_entered');
		echo $this->Form->input('date_modified');
		echo $this->Form->input('modified_user_id');
		echo $this->Form->input('created_by');
		echo $this->Form->input('description');
		echo $this->Form->input('deleted');
		echo $this->Form->input('assigned_user_id');
		echo $this->Form->input('account_name');
		echo $this->Form->input('account_num');
		echo $this->Form->input('timezone');
		echo $this->Form->input('contact_name');
		echo $this->Form->input('contact_phone');
		echo $this->Form->input('contact_email');
		echo $this->Form->input('address1');
		echo $this->Form->input('address2');
		echo $this->Form->input('city');
		echo $this->Form->input('state');
		echo $this->Form->input('zip');
		echo $this->Form->input('main_phone');
		echo $this->Form->input('main_fax');
		echo $this->Form->input('alt_phone');
		echo $this->Form->input('website');
		echo $this->Form->input('hours');
		echo $this->Form->input('type');
		echo $this->Form->input('privacy');
		echo $this->Form->input('country');
		echo $this->Form->input('billing_address1');
		echo $this->Form->input('billing_address2');
		echo $this->Form->input('billing_city');
		echo $this->Form->input('billing_state');
		echo $this->Form->input('billing_zip');
		echo $this->Form->input('answerphrase');
		echo $this->Form->input('account_color');
		echo $this->Form->input('did');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Ccact Clients'), array('action' => 'index')); ?></li>
	</ul>
</div>
