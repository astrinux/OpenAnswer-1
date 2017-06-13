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
<div class="clients form">
<?php echo $this->Form->create('Client'); ?>
		<h1><?php echo h($this->request->data['Client']['account_name']); ?> (<?php echo h($this->request->data['Client']['account_num']); ?>)</h1>
	<div>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('account_name');
		echo $this->Form->input('account_num');
	?>
	</div>

	
	<?php
	
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
		echo $this->Form->input('country');
		echo $this->Form->input('billing_address1');
		echo $this->Form->input('billing_address2');
		echo $this->Form->input('billing_city');
		echo $this->Form->input('billing_state');
		echo $this->Form->input('billing_zip');
	?>

<?php echo $this->Form->end(__('Submit')); ?>
</div>
		
