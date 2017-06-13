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
	div.accounts label {display: block; float:left; width:160px; text-align:right; margin-right: 10px;}
	div.dids {margin-left:20px;}
</style>

<div class="accounts form" id="add_account"  style="height: 100%; width: 100%;">
<div class="ui-layout-center ">
	<div class="header rbuttons"  style="padding-right:100px;">
	<input type="submit" value="Cancel" onclick="$('#acct-content').html(''); return false;" align="right"><input type="submit" value="Save &raquo;" onclick="addAccount(); return false;" align="right"></div>


<div class="ui-layout-content  panel-content">
<h1>Add a New Account</h1>

<?php echo $this->Form->create('Account', array('id' => 'addaccount')); 
		echo $this->Form->input('id');
		echo $this->Form->input('account_name', array('id'=> 'acctname'));
		echo $this->Form->input('account_num', array('id' => 'acctnum'));
		echo $this->Form->input('status', array('options' => array('1' => 'Active', '0' => 'Inactive'), 'default' => '1'));
		
		//echo $this->Form->input('date_entered', array('label' => 'Created:', 'type' => 'text', 'class' => 'datepicker'));
		echo $this->Form->input('contact_name');
		echo $this->Form->input('billing_address1', array('size' => '40'));
		echo $this->Form->input('billing_address2', array('size' => '40'));
		echo $this->Form->input('billing_city');
		echo $this->Form->input('billing_state', array('options' => $global_options['states'], 'size' => 1, 'empty' => 'Select','div' => array('id' => 'statediv')));
		echo $this->Form->input('billing_province', array('label' => 'Billing Province', 'div' => array('id' => 'provincediv')));
		echo $this->Form->input('billing_zip');
		echo $this->Form->input('billing_country', array('options' => $global_options['countries'], 'empty' => 'US', 'onchange' => 		
'if (this.value != \'US\') {$(\'#provincediv\').show();  
    $(\'#statediv\').hide();} else {$(\'#provincediv\').hide();  
    $(\'#statediv\').show();}'));
    echo $this->Form->input('billing_phone');    
    echo $this->Form->input('security_question_1', array('options' => $global_options['security_questions']));
    echo $this->Form->input('security_answer_1');
    echo $this->Form->input('security_question_2', array('options' => $global_options['security_questions']));
    echo $this->Form->input('security_answer_2');
    echo $this->Form->input('security_question_3', array('options' => $global_options['security_questions']));
    echo $this->Form->input('security_answer_3');
	?>
  </form>
  </div>

  </div>  
</div>
<script>

		function addAccount() {
		  if ($.trim($('#acctname').val()) == '') {
		    alert('You must specify an account name');
		    return false;
		  }
		  if ($.trim($('#acctnum').val()) == '') {
		    alert('You must specify an account number');
		    return false;
		  }
		  var myform = $('#addaccount');
		  $.ajax({
		    url: '/Accounts/add',
		    type: 'post',
		    dataType: 'json',
		    data: myform.serialize()
		  }).done(function(data){
		    if (data.success) {
          //loadPage(this, 'Accounts/edit/'  + data.new_id, 'acct-content');				
          //acctLayout.center.children.layout1.close('east');          		
          $('#find_account').select2('val', data.new_id); $('#find_account_go').trigger('click');          
  			}
		    alert(data.msg);
		  });
		}
  
  $(document).ready(function () {
    $('#provincediv').hide();     
    $('#add_account').layout();
	});
</script>
