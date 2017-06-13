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
	#did_add label {display: inline-block; width:120px; text-align:right; margin-right: 10px;}
	#did_add label.rlabel {display: inline-block; width:120px; text-align:left; margin-left:5px; vertical-align: top; font-weight: normal;}
</style>

<div id="did_add" class="did_numbers form panel-content">
<h1>Add Sub-account to Account# <?php echo $account_id; ?></h1>
<?php 
echo $this->Form->create('DidNumber', array('id' => 'add_did')); ?>
	<?php
		echo $this->Form->input('account_id', array('type' => 'hidden', 'value' => $account_id));
		echo $this->Form->input('DidNumbersEntry.did_number', array('label' => 'Phone Number', 'class' => 'phonenumber', 'onkeypress' => 'return validateNumber(event)'));
		echo $this->Form->input('timezone', array('options' => $global_options['timezone'] , 'size' => 1));
		echo $this->Form->input('status', array('label' => 'Taking Calls', 'options' => array('1' => 'Yes', '0' => 'No'), 'size' => 1, 'default' => '0'));
		
		echo $this->Form->input('company');
		echo $this->Form->input('answerphrase', array('empty' => 'Select', 'options' => $global_options['answerphrases'], 'label' => 'Answer Phrase', 'size' => 1));
		echo $this->Form->input('Misc.answerphrase', array('type' => 'text', 'size' => '80', 'label' => '<i>OR</i> Custom Phrase', 'onchange' => "$('select[name=\"data[DidNumber][answerphrase]\"]').val('');"));
		
		echo $this->Form->input('did_color', array('label' => 'Answer Phrase Color', 'options' => $global_options['did_colors'], 'size' => 1));
		echo $this->Form->input('difficulty', array('type' => 'select', 'label' => 'Queue Difficulty', 'options' => $global_options['difficulty'], 'size' => 1, 'empty' => 'Select'));		
		
		echo $this->Form->input('contact_name');
		echo $this->Form->input('contact_phone');
		echo $this->Form->input('contact_email');		
		echo $this->Form->input('type', array('options' => $global_options['type'], 'size' => 1));
		echo '<div class="input">';
		echo $this->Form->input('industry', array('div' => false, 'empty' => 'Select', 'options' => $industries, 'size' => 1, 'onchange' => 'if (this.value.toLowerCase() == \'other\') $(\'#industry_other\').show(); else $(\'#industry_other\').hide();'));
		echo $this->Form->input('Misc.industry_other', array('div' => false, 'type' => 'text', 'size' => 30, 'label' => 'If \'Other\', specify:'));
		echo '</div>';
  	echo $this->Form->input('description', array('rows' => 5, 'cols' => 60));

		echo '<br><br><h2>Message Delivery Options</h2>';
		echo $this->Form->input('include_cid', array('label' => 'Include Caller ID', 'size' => 1, 'options' => array('0' => 'No', '1' => 'Yes')));
		echo $this->Form->input('exclude_prompts', array('label' => 'Exclude prompt titles from text msgs', 'size' => 1, 'options' => array('0' => 'No', '1' => 'Yes')));		
		echo $this->Form->input('include_msg_id', array('label' => 'Include Msg ID in messages', 'size' => 1, 'options' => array('0' => 'No', '1' => 'Yes')));				
		echo $this->Form->input('email_format', array('size' => 1, 'options' => $global_options['email_format']));
		echo $this->Form->input('smtp_profile', array('size' => 1, 'options' => $global_options['smtp_profiles']));
		echo '<br><i>leave blank to use default formatting, otherwise %a = account number, %m=message id, %n = company name, %c = calltype, %d = caller id.  Ex: "Message from %d"</i>';
		echo $this->Form->input('email_subject_template', array('size' => 50));
		
		echo '<br><h2>Operator Screen Info</h2>';
		echo '<i>You can select to hide any of the following information from the operator screen by clearing the checkbox</i>'; 
	  
	  
	  echo '<div class="input">';
	  echo $this->Form->input('address1', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('address_visible', array('div' => false, 'default' => '1', 'label' => array('class' => 'rlabel')));
	  echo '</div>';
		echo $this->Form->input('address2');
		echo $this->Form->input('city');
		echo $this->Form->input('state', array('options' => $global_options['states'], 'empty' => 'Select','size' => 1, 'div' => array('id' => 'dstatediv')));
		echo $this->Form->input('province', array('label' => 'State/Province', 'div' => array('id' => 'dprovincediv')));
		echo $this->Form->input('zip', array('onkeypress' => 'return validateNumber(event)'));
		echo $this->Form->input('country', array('options' => $global_options['countries'], 'empty' => 'Select', 'size' => 1, 'default' => 'US', 'onchange' => 'if (this.value != \'US\') {$(\'#dprovincediv\').show();  
    $(\'#dstatediv\').hide();} else {$(\'#dprovincediv\').hide();  
    $(\'#dstatediv\').show();}'));
		
	  echo '<div class="input">';
		echo $this->Form->input('email', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('email_visible', array('div' => false, 'default' => '1', 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('main_phone', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('main_phone_visible', array('div' => false, 'default' => '1', 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('main_fax', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('main_fax_visible', array('div' => false, 'default' => '1', 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('alt_phone', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('alt_phone_visible', array('div' => false, 'default' => '1', 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('website', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('website_visible', array('div' => false, 'default' => '1', 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('hours', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('hours_visible', array('div' => false, 'default' => '1', 'label' => array('class' => 'rlabel')));
	  echo '</div>';		

	?>
<br><br>
</form>
</div>
		
<script>
  $(document).ready(function () {
    $('.phonenumber').mask("(999) 999-9999");  
    $('#acct_save_btn').off('click');  
    $('#acct_save_btn').on('click', didNumberAdd);  
  });

function didNumberAdd(t) {
    
    var url = "/DidNumbers/add/<?php echo $account_id; ?>"
	    $.ajax({
	        url: url,
	        type: 'POST',
	        dataType: 'json',
	        data: $('#add_did').serialize()
			}).done(function(data) {    
        if (data.success) {
          if (accountSpecified()) loadPage(this, '/Accounts/edit/'  + $('#find_account').val(), 'acct-content');   
          acctLayout.center.children.layout1.close('east');                 
        }
        alert(data.msg);
			});   
}
</script>
