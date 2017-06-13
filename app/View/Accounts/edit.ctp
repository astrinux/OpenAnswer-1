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
if (!$this->request->data) echo '<br><br><br><br><h2>Account not found</h2>';
else if ($this->request->data['Account']['deleted']) echo '<br><br><br><br><h2><center>This account has been deleted</center></h2><br><!--<br><center>Click <a href="#" onclick="return false;">here</a> to attempt to recover the account-->';
else {
?>
<style>
	div.accounts label {display: block; float:left; width:160px; text-align:right; margin-right: 10px;}
	div.sub {margin-left:20px;}
</style>

<div class="accounts form"  id="edit_account" style="height: 100%; width: 100%;">
<div class="ui-layout-center ">
  <div class="rbuttons header" style="padding-right:100px;"><input type="submit" value="Cancel" onclick="$('#acct-content').html(''); return false;" align="right"><input type="submit" value="Save &raquo;" onclick="saveAccountEdit(); return false;" align="right"></div>
<div class="ui-layout-content  panel-content">
  <h1><center><?php echo $this->request->data['Account']['account_num']; ?> - <?php echo $this->request->data['Account']['account_name']; ?></center></h1>
<?php echo $this->Form->create('Account', array('id' => 'editaccount')); ?>
<button id="del_acct" onclick="user_confirm('Are you sure you want to delete this account?', function() {getJson('/Accounts/delete/'  + '<?php echo $this->request->data['Account']['id']; ?>', null, function(data){if (data.success) {$('#acct-content').html('');}});}); return false;">delete this account</button><br><br>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('created', array('type' => 'text', 'disabled' => true));
		echo $this->Form->input('account_name');
		echo $this->Form->input('account_num');
		echo $this->Form->input('status', array('options' => array('1' => 'Active', '0' => 'Inactive')));
		echo $this->Form->input('contact_name');
		
		echo $this->Form->input('billing_address1', array('size' => '40'));
		echo $this->Form->input('billing_address2', array('size' => '40'));
		echo $this->Form->input('billing_city');
		echo $this->Form->input('billing_state', array('options' => $global_options['states'], 'size' => 1, 'empty' => 'Select','div' => array('id' => 'statediv')));
		echo $this->Form->input('billing_province', array('label' => 'Billing Province', 'div' => array('id' => 'provincediv')));
		echo $this->Form->input('billing_zip');
		echo $this->Form->input('billing_country', array('options' => $global_options['countries'], 'empty' => 'Select', 'onchange' => 'if (this.value != \'US\') {$(\'#provincediv\').show();  
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
	<br><br>
	<div class="sub">
	<h2>Sub-Accounts</h2>
	<p><i>Note: each account can contain multiple sub-accounts.  Each sub-account has its own set of calltype instructions and can be assigned multiple phone numbers. In other words, multiple numbers may share the same calltype instructions.</i></p>
	<input type="button" value="Add Sub-Account" onclick="loadPage(this, '/DidNumbers/add/<?php echo $account_id; ?>' , 'acct-detail');  acctLayout.center.children.layout1.open('east'); return false;" /><br><br>
	<table cellpadding="4" cellspacing="0" border="0" class="gentbl">
	  <tr><th width="140" align="left">VN Number</th>
	  <th width="240" align="left">Company/ Business Name</th>
	  <th></th>
	<?php 
	foreach ($numbers as $d) {
	  echo '<tr><td align="left">';
	  $dids = explode(',', $d[0]['numbers']);
	  foreach ($dids as $num) {
	    echo $this->element('formatPhone', array('num' =>  $num)) . '<br>';
	  }
	  echo '</td>';
	  //echo '<td align="left">' .$this->element('formatPhone', array('num' =>  $d['alias_number'])). '</td>';
	  echo '<td align="left">' .$d['DidNumber']['company']. '</td>';
	  echo '<td><a href="#" onclick="editDidNumber('.$d['DidNumber']['id'].', \''. str_replace('\'', '', $d['DidNumber']['company']) .'\');return false;"><img title="edit" alt="edit" src="/img/edit.png" width="16" height="16"></a>';
	  echo '&nbsp;<a href="#" onclick="deleteDidNumber('.$d['DidNumber']['id'].');return false;"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>';
	  echo '</td>';
	}
	?>
	</table>
	</div>
	<br><br>
	
	<br><br>
	<br><br>	
  </div>
  </form>
  	

</div>
<script>
  function deleteDidNumber(did_id) {
    user_confirm('Are you sure you want to delete this sub-account?', function() {
  		var url = '/DidNumbers/delete/' + did_id ;
      $.ajax({
          url: url,
          type: 'get',
          dataType: 'json',
      }).done(function(jsondata) {
        if (jsondata.success) {
          loadPage(this, '/Accounts/edit/'  + $('#find_account').val(), 'acct-content'); 
        }
         
        alert(jsondata.msg);
      
      }); 
    });
  }
  
  function saveAccountEdit() {
		var myform = $('#editaccount');
		var url = '/Accounts/edit/<?php echo $account_id; ?>' ;
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: myform.serialize(),
        success: function(jsondata) {
					if (jsondata.success)
						msgId = jsondata.msg_id;
					alert(jsondata.msg);
        }
    });			        
 		return false;
    
  }



  $(document).ready(function () {
  $('#edit_account').layout();
<?php
$states_array = array_keys($global_options['states']);
if (in_array($this->request->data['Account']['billing_state'], $states_array)) {
  ?>
    $('#provincediv').hide();  
    $('#statediv').show();  
  <?php
}
else if (strtolower($this->request->data['Account']['billing_country']) != 'us' && trim($this->request->data['Account']['billing_province']) != '') {
  ?>
    $('#provincediv').show();  
    $('#statediv').hide();  
  <?php
}
else {
  ?>
    $('#provincediv').hide();  
    $('#statediv').show();  
  <?php
}
?>    
    $( "#del_acct" ).button({
      icons: {
        primary: "ui-icon-trash"
      }
    });   
	});
</script>
<?php
} 
?>
