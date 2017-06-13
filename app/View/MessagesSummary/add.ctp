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
  .msg_summary a, .msg_summary a:visited {font-style:italic; color: #777;}
  .choices {margin: 10px 30px;}
  div.input {padding: 10px 20px !important;}
  .mrow label {width:200px; text-align: right; display:block; float:left;}
</style>
<div class="msg_summary form" id="msgsummary">
<?php echo $this->Form->create('MessagesSummary', array('id' => 'MessagesSummaryAdd')); ?>
<h1>Add Message Summary Schedule</h1><br><br>
        <div class="input">
        <b>This schedule is:</b>	
        <?php echo $this->Form->input('active', array('options' => array('1' => 'Active', '0' => 'Inactive'), 'div' => false, 'label' => false));?>
        </div>
        <div class="input">
        <b>The schedule is valid for the following days:</b>&nbsp;&nbsp; <a href="#" onclick="$('.everyday').prop('checked', true); return false;">every day</a>&nbsp;&nbsp;&nbsp;
        <a href="#" onclick="$('.everyday').prop('checked', false); $('.weekday').prop('checked', true); return false;">weekdays</a>&nbsp;&nbsp;&nbsp;        
        <a href="#" onclick="$('.everyday').prop('checked', false); $('.weekend').prop('checked', true); return false;">weekend</a>&nbsp;&nbsp;&nbsp;<br><br>
        <div class="choices">
				<?php echo $this->Form->input('mon', array('id' => 'd_mon', 'class' => 'weekday everyday', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_mon">Mon</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('tue', array('id' => 'd_tue', 'class' => 'weekday everyday', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_tue">Tue</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('wed', array('id' => 'd_wed', 'class' => 'weekday everyday', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_wed">Wed</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('thu', array('id' => 'd_thu', 'class' => 'weekday everyday', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_thu">Thu</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('fri', array('id' => 'd_fri', 'class' => 'weekday everyday', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_fri">Fri</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('sat', array('id' => 'd_sat', 'class' => 'weekend everyday', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_sat">Sat</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('sun', array('id' => 'd_sun', 'class' => 'weekend everyday', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_sun">Sun</label>&nbsp;&nbsp;&nbsp;
<br><br>
	<?php
		echo $this->Form->input('did_id', array('type' => 'hidden', 'value' => $did_id));
		echo $this->Form->input('tx_interval', array('options' => $interval_options, 'id' => 'tx_interval', 'div' => false, 'label' => 'Send', 'onchange' => 'checkTxInterval(this);')) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	  echo '<span id="notallday">';

		echo '<span id="time_range">' . $this->Form->input('start_time', array('type' => 'text', 'label' => 'from', 'div' => false, 'class' => 'timepicker', 'size' => 10)) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo $this->Form->input('end_time', array('type' => 'text', 'label' => 'to', 'div' => false, 'class' => 'timepicker', 'size' => 10)) . '</span>';
		echo '<span id="send_time" class="is_hidden">' . $this->Form->input('send_time', array('type' => 'text', 'label' => 'at', 'div' => false, 'class' => 'timepicker', 'size' => 10)) . '</span>';
    echo '</span>';    
    echo '<span id="alldaydiv">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this->Form->input('all_day', array('type' => 'checkbox', 'label' => 'All day', 'div' => false, 'onclick' =>"checkAllDay(this)"));
		echo '</span></div></div>'; // end of <div class="choices">
		echo '<br><br>';  // end of <div class="hrow">
		
    echo '<div class="input mrow"><label>Destination</label><select multiple id="contact_ids" name="data[Misc][employee_contact_ids][]" style="width:300px">';

		foreach ($employees as $e) {
		  $contacts = explode(',', $e['0']['contacts']);
		  $contact_labels = explode(',', $e['0']['contact_labels']);
		  $contact_ids = explode(',', $e['0']['contact_ids']);
		  $contact_types = explode(',', $e['0']['contact_types']);
		  echo '<optgroup label="'.$e['e']['name'].'">';
		  foreach ($contacts as $k => $c) {
		    $contact = $c;
		    if ($contact_types[$k] == CONTACT_FAX) {
		      $contact = $this->element('formatPhone', array('num' => $c));
		    }		    
		    echo '<option value="'.$contact_ids[$k].'">'.$contact.' - '.$contact_labels[$k].'</option>';
		  }
		  echo '</optgroup>';
		}
		echo '</select>';
		echo '</div>';		
		//echo $this->Form->input('destination_email', array('div' => array('class' => 'input mrow'), 'type' => 'text', 'size' => 40));
		//echo $this->Form->input('destination_fax', array('div' => array('class' => 'input mrow'), 'type' => 'text', 'size' => 40));
		echo $this->Form->input('msg_type', array('div' => array('class' => 'input mrow'), 'options' => $msg_options, 'label' => 'Deliver'));
		echo '<div class="input mrow" style="margin-left: 40px;" id="no_message"><br>';
		echo $this->Form->input('no_message', array('div' => false, 'label' => false)) . '&nbsp;<b>Send No-Message notification</b>' . ' &nbsp;&nbsp;&nbsp;<span class="no_msg_when">';
//		echo $this->Form->input('no_message_type', array('div' => false, 'id' => 'no_message_when', 'label' => false, 'onchange' => 'checkNoMessage(this); ', 'options' => array('0' => 'at the beginning and end of the scheduled time range', '1' => 'as scheduled above', '2' => 'at the following time:'))).' &nbsp;&nbsp;&nbsp;';
		echo $this->Form->input('no_message_type', array('div' => false, 'id' => 'no_message_when', 'label' => false, 'options' => array('0' => 'at the beginning and end of the scheduled time range', '1' => 'as scheduled above'))).' &nbsp;&nbsp;&nbsp;';
		echo $this->Form->input('no_message_send_time', array('type' => 'text', 'id' => 'no_message_send_time', 'div' => false, 'label' => false, 'size' => 10, 'class' => 'timepicker'));
	  echo '</span></div>';
	  echo '<br><br>'; 

		echo $this->Form->input('destination_email', array('type' => 'hidden', 'div' => 'input tasinput', 'label' => 'TAS Destination email'));
		echo $this->Form->input('destination_fax', array('type' => 'hidden', 'div' => array('class' => 'input tasinput'), 'label' => 'TAS Destination fax'));
		echo '<br><br>'; 

	?>
</form>
</div>

<script>


  $(document).ready(function() {  
    $('#contact_ids').select2({
      allowClear: true,
      placeholder: 'Select destination(s)'
    });    
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      if (checkSummaryForm()) { 
        $('#MessagesSummaryAdd input[type=text]').each(function() {
          if ($(this).is(':hidden')) $(this).val('');
        });
          
        var url = "/MessagesSummary/add/<?php echo $did_id; ?>"
  	    $.ajax({
  	        url: url,
  	        type: 'POST',
  	        dataType: 'json',
  	        data: $('#MessagesSummaryAdd').serialize()
  			}).done(function(data) {    
          if (data.success) {
  					loadPage(this, '/MessagesSummary/index/<?php echo $did_id; ?>', 'did-content');          
            didLayout.center.children.layout1.close('east')					
          }
          alert(data.msg);
  			});          
  		}
    });
    
    $('.timepicker').timepicker({'step': 15, 'minTime': '00:15am', 'maxTime': '11:45pm'});
    $('.datepicker').datepicker({ minDate: -0, dateFormat: 'yy-mm-dd'});    
  });
</script>
