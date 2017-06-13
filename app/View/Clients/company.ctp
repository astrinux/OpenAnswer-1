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
<style>
	div.clients label {display: block; float:left; width:100px; text-align:right; margin-right: 10px;}
</style>

<div class="clients form">
<?php echo $this->Form->create('Client'); ?>
		<h1><?php echo h($this->request->data['Client']['account_name']); ?> (<?php echo h($this->request->data['Client']['account_num']); ?>)</h1>
<?php	
		echo $this->Form->input('timezone');
		echo $this->Form->input('main_phone');
		echo $this->Form->input('main_fax');
		echo $this->Form->input('alt_phone');
		echo $this->Form->input('website');
		echo $this->Form->input('hours');
		echo $this->Form->input('type');
		echo $this->Form->input('privacy');
		echo $this->Form->input('country');
		echo $this->Form->input('answerphrase', array('options' => $global_options['answerphrases']));
		echo $this->Form->input('account_color');
		echo $this->Form->input('did', array('label' => 'DID'));
	?>
<br>,br>
<?php echo $this->Form->end(__('Submit')); ?>
</div>




<script type="text/javascript">

        $('ul.leftnav li').on('hover', function() {$(this).addClass('ct_over'); });        
        $('ul.leftnav li').on('mouseout', function() {$(this).removeClass('ct_over')});        
        
</script>
