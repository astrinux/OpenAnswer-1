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
<?php
$this->Paginator->options(array(
    'update' => '#did-content',
    'evalScripts' => true
));
?>
<script src="/js/lib/RecordRTC7_2_2014.js"></script>
<style>
	#did_edit label {display: inline-block; width:120px; text-align:right; margin-right: 10px;}
	#did_edit label.rlabel {display: inline-block; width:120px; text-align:left; margin-left:5px; vertical-align: top; font-weight: normal;}
	#calltimes {margin:10px 40px;line-height: 200%;}
	#calltimes label {font-weight: normal; text-align:left; display: inline;}
#did_edit .footer {
    overflow-x: hidden;
    overflow-y: hidden;
    padding-bottom: 3px;
    padding-left: 10px;
    padding-right: 10px;
    padding-top: 3px;
    position: relative;
    white-space: nowrap;
}	
</style>

<div id="did_edit" class="did_numbers form"  style="height: 100%; width: 100%;">
<div class="ui-layout-center ">
<?php if ($this->Permissions->isAuthorized('DidnumbersEdit',$permissions)) { ?> 
<div class="rbuttons header" style="padding-right:20px;"><input type="submit" value="Cancel" onclick="$('#did-content').html(''); if ($('#did-list').html().length > 30) didLayout.center.children.layout1.open('west'); return false;" align="right"><input type="submit" value="Save &raquo;" onclick="saveForm(this); return false;" align="right">
</div>
<?php } ?>
<div class="ui-layout-content panel-content">
<?php 
  echo $this->Form->create('DidNumber', array(
    'inputDefaults' => array(
      'size' => '50'
    )
  )); 
  /*if ($list) echo '&laquo; <a href="#" onclick="didLayout.center.children.layout1.open(\'west\');">Back to list</a><br>';
  else echo '<br>';*/
  ?>
  
		<h1><center><i class="fa <?php echo $global_options['icons']['setup']; ?>"></i><?php echo h($this->request->data['Account']['account_num']); ?> - <?php echo h($this->request->data['Account']['account_name']); ?></center></h1>
		<center><a href="#" onclick="window.open('/DidNumbers/Summary/<?php echo $this->request->data['DidNumber']['id']; ?>','<?php echo $this->request->data['Account']['account_num']; ?>', 'toolbar=no, menubar=no,resizable=yes,location=no,scrollbars=1,directories=no,status=no');  return false;">Service Summary</a></center>
	<?php
		echo $this->Form->input('id');
			 echo '<div class="input"><label>ID</label>' . $this->request->data['DidNumber']['id'] . '</div>';
//		echo $this->Form->input('name');
		echo $this->Form->input('date_entered', array('type' => 'text', 'disabled' => true));

		echo $this->Form->input('status', array('label' => 'Taking Calls', 'options' => array('1' => 'Yes', '0' => 'No'), 'size' => 1));
		echo $this->Form->input('DidNumber.service_sku');

		echo $this->Form->input('Account.account_name', array('style' => 'border:0px;', 'disabled' => true));
		echo $this->Form->input('Account.account_num', array('style' => 'border:0px;', 'disabled' => true));
    echo $this->Form->input('DidNumber.billto_account', array('label' => 'Bill to Account'));		

		echo '<div class="input"><label>Phone Numbers</label>';
if ($this->Permissions->isAuthorized('DidnumbersEdit',$permissions)) {
		    echo '<a href="#" onclick="addNumberCheck(\''. $did_id.'\');return false;">Add number</a><br>';
		}
		echo '<div style="margin-left: 130px;" id="all-numbers">';
		foreach ($all_numbers as $k => $num) {
		
		  echo $this->element('formatPhone', array('num' =>  $num['DidNumbersEntry']['number']));
		  if (!empty( $num['DidNumbersEntry']['alias'])) echo '/ ' . $this->element('formatPhone', array('num' =>  $num['DidNumbersEntry']['alias']));
		  
        if ($this->Permissions->isAuthorized('DidnumbersEdit',$permissions)) {

        echo ' <a href="" onclick="deleteNumber('.$num['DidNumbersEntry']['id'].'); return false;"><img title="delete" alt="x" src="/img/delete.png" width="16" height="16"></a><br>';
    }
		}
		echo '</div></div>';
	
		echo $this->Form->input('timezone', array('options' => $global_options['timezone'], 'size' => 1));

		echo $this->Form->input('answerphrase', array('empty' => 'Select', 'label' => 'Answer Phrase', 'onchange' => "$('input[name=\"data[Misc][answerphrase]\"]').val('');", 'options' => $global_options['answerphrases'], 'size' => 1));
		echo $this->Form->input('Misc.answerphrase', array('type' => 'text', 'size' => '80', 'label' => '<i>OR</i> Custom Phrase', 'onchange' => "$('select[name=\"data[DidNumber][answerphrase]\"]').val('');"));
		echo $this->Form->input('did_color', array('label' => 'Answer Phrase Color', 'options' => $global_options['did_colors'], 'size' => 1));
		echo $this->Form->input('difficulty', array('type' => 'select', 'label' => 'Queue Difficulty', 'options' => $global_options['difficulty'], 'size' => 1, 'empty' => 'Select'));

		echo $this->Form->input('contact_name');
		echo $this->Form->input('contact_phone');
		echo $this->Form->input('contact_email');
		echo $this->Form->input('type', array('options' => $global_options['type'], 'size' => 1));
		//echo $this->Form->input('privacy', array('options' => $global_options['privacy'], 'size' => 1));
		echo '<div class="input">';
		echo $this->Form->input('industry', array('div' => false, 'options' => $industries, 'size' => 1, 'onchange' => 'if (this.value.toLowerCase() == \'other\') $(\'#industry_other\').show(); else $(\'#industry_other\').hide();'));
		echo '<span id="industry_other">';
		echo $this->Form->input('Misc.industry_other', array('div' => false, 'type' => 'text', 'size' => 30, 'label' => 'If \'Other\', specify:'));
		echo '</span></div>';
  	echo $this->Form->input('description', array('rows' => 5, 'cols' => 60));
		echo $this->Form->input('scheduling_option', array('size' => 1, 'label' => 'Scheduling Calendar', 'options' => array('0' => 'No', '1' => 'Yes')));

		echo '<br><br><h2>Message Delivery Options</h2>';
		echo $this->Form->input('include_cid', array('label' => 'Include Caller ID', 'size' => 1, 'options' => array('0' => 'No', '1' => 'Yes')));
		echo $this->Form->input('exclude_prompts', array('label' => 'Exclude prompt titles from text msgs', 'size' => 1, 'options' => array('0' => 'No', '1' => 'Yes')));		
		echo $this->Form->input('include_msg_id', array('label' => 'Include Msg ID in messages', 'size' => 1, 'options' => array('0' => 'No', '1' => 'Yes')));				
		echo $this->Form->input('email_format', array('size' => 1, 'options' => $global_options['email_format']));
		echo $this->Form->input('smtp_profile', array('size' => 1, 'options' => $global_options['smtp_profiles']));
		echo '<br><i>leave blank to use default formatting, otherwise %a = account number, %m=message id, %n = company name, %c = calltype, %d = caller id.  Ex: "Message from %d"</i>';
		echo $this->Form->input('email_subject_template', array('size' => 50));

		echo '<br><br><h2>Operator Screen Info</h2>';
		echo '<i>You can select to hide any of the following information from the operator screen by clearing the checkbox</i><br><br>'; 
	  echo '<div class="input">';
	  echo $this->Form->input('company', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('company_visible', array('div' => false, 'label' => array('class' => 'rlabel')));
	  //echo '&nbsp;&nbsp&nbsp;<a href="/DidNumbers/record/'.$did_id.'" onclick="/DidNumbers/record/'.$did_id.'" target=_blank>Record name</a>';
	  echo '</div>';
		if (!empty($audio)) {
		  ?>
		  <div class="input">
		  <label>Recording</label>
		  <audio controls>
		  <source src="/DidNumbers/listen/<?php echo $did_id; ?>?r=<?php echo rand(10000,50000);?>" type="<?php echo $audio['CompanyAudio']['company_audio_type']; ?>">		  
		  </audio>
		  <?php
    if ($this->Permissions->isAuthorized('DidnumbersEditRecording',$permissions)) {
		    ?>
      &nbsp;&nbsp;<a href="#" onclick="openRecorder('<?php echo $did_id; ?>');return false;"><img title="record" alt="record" src="/img/record.png" width="16" height="16" valign="middle"></a>&nbsp;&nbsp;		    
		  <a href="#" onclick="user_confirm('Are you sure you want to delete this recording?', function() {getJson('/DidNumbers/delete_audio/<?php echo $did_id; ?>', null, function() {loadPage(this, '/DidNumbers/edit/<?php echo $did_id; ?>', 'did-content');})}); return false;"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16" valign="middle"></a>
		  <?php
		}
		?>
		  &nbsp;<br>
		  </div>
		  <?php
		}
		else {
		  echo '<div class="input"><label>&nbsp;</label>';
		  echo '<a href="#" onclick="openRecorder(\''.$did_id.'\'); return false;"><img title="record" alt="record" src="/img/record.png" width="16" height="16" valign="middle"></a><br>';
		  echo '</div>';
		}
		
	  echo '<div class="input">';
	  echo $this->Form->input('address1', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('address_visible', array('div' => false, 'label' => array('class' => 'rlabel')));
	  echo '</div>';
		echo $this->Form->input('address2');
		echo $this->Form->input('city');
		echo $this->Form->input('state', array('options' => $global_options['states'], 'empty' => 'Select','size' => 1, 'div' => array('id' => 'dstatediv')));
		echo $this->Form->input('province', array('label' => 'State/Province', 'div' => array('id' => 'dprovincediv')));
		echo $this->Form->input('zip');
		echo $this->Form->input('country', array('options' => $global_options['countries'], 'empty' => 'Select', 'size' => 1, 'onchange' => 'if (this.value != \'US\') {$(\'#dprovincediv\').show();  
    $(\'#dstatediv\').hide();} else {$(\'#dprovincediv\').hide();  
    $(\'#dstatediv\').show();}'));

	  echo '<div class="input">';
		echo $this->Form->input('email', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('email_visible', array('div' => false, 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('main_phone', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('main_phone_visible', array('div' => false, 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('main_fax', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('main_fax_visible', array('div' => false, 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('alt_phone', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('alt_phone_visible', array('div' => false, 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input">';
		echo $this->Form->input('website', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('website_visible', array('div' => false, 'label' => array('class' => 'rlabel')));
	  echo '</div>';

	  echo '<div class="input textarea">';
		echo $this->Form->input('hours', array('div' => false)) . '&nbsp;&nbsp;&nbsp;';
	  echo $this->Form->input('hours_visible', array('div' => false, 'label' => array('class' => 'rlabel')));
	  echo '</div>';

		echo '<br><br><h2>Call Volume Info</h2>';
	  echo '<div class="input">';
		echo '<b>Are we your primary or overflow answering service?</b>&nbsp; ';
		echo $this->Form->input('primary_or_overflow', array('div' => false, 'label' => false, 'options' => array('0' => 'Primary', '1' => 'Overflow'), 'size' => 1)) . "<br>";
	  echo '</div>';

	  echo '<div class="input">';
		echo '<b>Do you do radio/ TV advertising?</b>&nbsp; ';
		echo $this->Form->input('radio_advertising', array('div' => false, 'label' => false, 'options' => array('0' => 'No', '1' => 'Yes'), 'size' => 1)) . "<br>";
	  echo '</div>';

	  echo '<div class="input">';
		echo '<b>How many calls do you get each day?</b>&nbsp; ';
		echo $this->Form->input('calls_per_day', array('div' => false, 'label' => false, 'options' => array('1' => '< 5 calls/day', '2' => '< 20 calls/day', '3' => '< 50 calls/day', '4' => '< 100 calls/day', '5' => '> 100 calls/day', '6' => 'Call volume fluctuates day to day'),  'size' => 1, 'empty' => 'Select')) . "<br>";
	  echo '</div>';
		
		echo '<div class="input">';
		echo '<b>What time of day do your calls come in?</b><br>';
		echo '<div id="calltimes">';
		echo $this->Form->input('Misc.calltime.1', array('type' => 'checkbox', 'hiddenField' => false, 'div' => false, 'value' => '1', 'label' => '365 days, 24/7')) . "<br>";		
		echo $this->Form->input('Misc.calltime.2', array('type' => 'checkbox', 'hiddenField' => false, 'div' => false,  'value' => '2', 'label' => 'Weekdays 8am-8pm')) . "<br>";		
		echo $this->Form->input('Misc.calltime.3', array('type' => 'checkbox', 'hiddenField' => false, 'div' => false,  'value' => '3', 'label' => 'Weekdays after hours')) . "<br>";		
		echo $this->Form->input('Misc.calltime.4', array('type' => 'checkbox', 'hiddenField' => false, 'div' => false,  'value' => '4', 'label' => 'Weekends')) . "<br>";		
		echo $this->Form->input('Misc.calltime.5', array('type' => 'checkbox', 'hiddenField' => false, 'div' => false,  'value' => '5', 'label' => 'Other'));		
		echo $this->Form->input('DidNumber.calls_timing_other', array('type' => 'text', 'div' => false,  'size' => '30', 'label' => false)) ;
		echo '</div></div>';
	?>
<br><br>
</form>
</div>




</div>
</div>

 
      		
<script>

function openRecorder(did_id) {
      $('#record-did-content').load('DidNumbers/record/' + did_id);
  $('#record-did').dialog('open');      
}

function addNumberCheck(did_id) {
  $('#add_did_id').val(did_id);
  $('#add-did').dialog('open'); return false;
}

function addNumber(did_id) {
  var phonenum = $('#number-to-add').val().replace(/[^0-9]/g, '');
  var url = '/DidNumbersEntries/add/' + did_id + '/' + phonenum;
  $('#all-numbers').load(url);
	$('#add-did').dialog('close');
}

function deleteNumber(id) {
  user_confirm('Are you sure you want to delete this number?',   function() {
    var url = '/DidNumbersEntries/delete/' + id;
    $('#all-numbers').load(url);
  });
}

    
 
    
  $(document).ready(function () {
    $('.phonenumber').mask("(999) 999-9999");  
    
$('#did_edit').layout();    
<?php
if (strtolower($this->request->data['DidNumber']['industry']) == 'other') {
  ?>
    $('#industry_other').show();  
  <?php
}
else {
  ?>
    $('#industry_other').hide();  
  <?php
}
?>

    
<?php
if (strtolower($this->request->data['DidNumber']['country']) != 'us' && trim($this->request->data['DidNumber']['province']) != '') {
  ?>
    $('#dprovincediv').show();  
    $('#dstatediv').hide();  
  <?php
}
else {
  ?>
    $('#dprovincediv').hide();  
    $('#dstatediv').show();  
  <?php
}
?>    
  });
  
function saveForm(t) {
    var myform = $('#did_edit form');
    var url = "/DidNumbers/edit/<?php echo $did_id; ?>"
	    $.ajax({
	        url: url,
	        type: 'POST',
	        dataType: 'json',
	        data: myform.serialize()
			}).done(function(data) {    
        if (data.success) {
          loadPage(this, '/DidNumbers/edit/'  + $('#find_did').val(), 'did-content');         
        }
        alert(data.msg);
			});   
}
$('.didbtns').show();
//activeAccount = <?php echo json_encode($this->request->data['DidNumber']); ?>;
</script>
