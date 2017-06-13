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
<div class="clients view">
<h2><?php  echo __(' Client'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date Entered'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['date_entered']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date Modified'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['date_modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified User Id'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['modified_user_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created By'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['created_by']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['description']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Deleted'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['deleted']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Assigned User Id'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['assigned_user_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Account Name'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['account_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Account Num'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['account_num']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Timezone'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['timezone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Contact Name'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['contact_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Contact Phone'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['contact_phone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Contact Email'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['contact_email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Address1'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['address1']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Address2'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['address2']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('City'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['city']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('State'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['state']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Zip'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['zip']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Main Phone'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['main_phone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Main Fax'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['main_fax']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Alt Phone'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['alt_phone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Website'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['website']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Hours'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['hours']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Privacy'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['privacy']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Country'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['country']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Billing Address1'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['billing_address1']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Billing Address2'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['billing_address2']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Billing City'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['billing_city']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Billing State'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['billing_state']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Billing Zip'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['billing_zip']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Answerphrase'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['answerphrase']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Account Color'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['account_color']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Did'); ?></dt>
		<dd>
			<?php echo h($Client['Client']['did']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit  Client'), array('action' => 'edit', $Client['Client']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete  Client'), array('action' => 'delete', $Client['Client']['id']), null, __('Are you sure you want to delete # %s?', $Client['Client']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List  Clients'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New  Client'), array('action' => 'add')); ?> </li>
	</ul>
</div>


