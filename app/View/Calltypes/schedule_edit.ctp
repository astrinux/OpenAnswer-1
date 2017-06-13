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
  .choices {margin: 10px 30px;}
  h2 {margin-top:30px; margin-bottom: 10px; font-size: 16px; font-weight: normal}
  div.input {padding: 10px 20px !important;}
  .slabel label {width: 20px; text-align:left;}  
  .llabel label {display: inline; }  
 </style>
 <div class="panel-content">
 <div class="calltypes form">
  <form  id="CalltypeScheduleEditForm" method="post" accept-charset="utf-8">
	<?php
		echo $this->Form->input('Schedule.id', array('type' => 'hidden'));
		echo $this->Form->input('Calltype.account_id', array('type' => 'hidden', 'value' => $account_id));
		echo $this->Form->input('Schedule.did_id', array('type' => 'hidden'));
		echo $this->Form->input('Calltype.id', array('type' => 'hidden'));
	  echo '<h2>Specify the Call Type</h2>';
		echo '<div class="input llabel">';
		echo $this->Form->input('Calltype.title', array('autocomplete' => 'off', 'empty' => 'Select', 'label' => 'Call Type', 'div' => false, 'options' => $calltype_options)) . '&nbsp;&nbsp;&nbsp;';
		echo $this->Form->input('Misc.title_custom', array('autocomplete' => 'off', 'div' => false, 'style' =>  'width: 200px;', 'label' => '<b>OR</b> Enter a custom type:', 'type' => 'text'));
		echo '</div>';
		echo $this->Form->input('Schedule.active', array('label' => 'Make this call type active?', 'div' => array('class' => 'input llabel'), 'options' => array('0' => 'No', '1' => 'Yes'))) . '&nbsp;&nbsp;&nbsp;';
		
		echo $this->Form->input('Misc.timesensitive', array('id' => 'checkts', 'label' => 'The instructions will vary depending on time of day', 'div' => array('class' => 'input llabel'), 'options' => array('0' => 'No', '1' => 'Yes'), 'onmouseup' => 'checkTS()')) . '&nbsp;&nbsp;&nbsp;';
    ?>
    
    <div id="timesensitive" class="is_hidden">
      <h2>Specify when these instructions are to be used:</h2>
      <div class="input pad">
        <b>These instructions are valid for the following days:</b>
        <div class="choices slabel">
				<?php echo $this->Form->input('Schedule.mon', array('id' => 'd_mon', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_mon">Mon</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.tue', array('id' => 'd_tue', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_tue">Tue</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.wed', array('id' => 'd_wed', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_wed">Wed</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.thu', array('id' => 'd_thu', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_thu">Thu</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.fri', array('id' => 'd_fri', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_fri">Fri</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.sat', array('id' => 'd_sat', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_sat">Sat</label>&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.sun', array('id' => 'd_sun', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_sun">Sun</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;From: <?php echo $this->Form->input('Schedule.start_time', array('id' => 'ts_start_time', 'type' => 'text', 'class' => 'timepicker', 'label' => false, 'div' => false, 'size' => 10));?>&nbsp;&nbsp;&nbsp;&nbsp;To: <?php echo $this->Form->input('Schedule.end_time', array('id' => 'ts_end_time', 'type' => 'text', 'class' => 'timepicker', 'label' => false, 'div' => false, 'size' => 10));?>
        </div>
      </div>
      <div class="input pad"><b>Date range:</b>
        <div class="choices">    
      <?php
        echo $this->Form->input('Schedule.start_date', array('label' => 'Start Date', 'div' => false, 'empty' => 'Select', 'type' => 'text', 'size' => 12, 'class' => 'datepicker')) . '&nbsp;&nbsp;'. $this->Form->input('Misc.date_time_start', array('label' => false, 'div' => false, 'type' => 'text', 'size' => 8, 'class' => 'timepicker')) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo $this->Form->input('Schedule.end_date', array('label' => 'End Date', 'div' => false, 'type' => 'text', 'size' => 12, 'class' => 'datepicker')) . '&nbsp;&nbsp;'. $this->Form->input('Misc.date_time_end', array('label' => false, 'div' => false, 'type' => 'text', 'size' => 8, 'class' => 'timepicker'));
        ?>
        </div>
      </div>
      <div class="input pad"><b>Day range (ex: Fri 5pm - Mon 8am):</b>
        <div class="choices">    
      <?php
        echo $this->Form->input('Schedule.start_day', array('label' => 'Start Day', 'div' => false, 'empty' => 'Select', 'options' => $php_daysofweek)) . '&nbsp;&nbsp;';
        echo $this->Form->input('Misc.day_time_start', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8'));
        //echo ' <input class="timepicker" type="text" name="day_time_start" size="8">';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo $this->Form->input('Schedule.end_day', array('label' => 'End Day', 'div' => false, 'empty' => 'Select', 'options' => $php_daysofweek));
        echo $this->Form->input('Misc.day_time_end', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8'));

        //echo  ' <input class="timepicker" type="text" name="day_time_end" size="8">';
        ?>
        </div>
      </div>              
    </div>
    <?php
		//echo $this->Form->input('type');
		//echo $this->element('schedule_edit');
	?>
<!--<input type="submit" value="Save" onclick="saveCallType(this); return false;">-->
</form>
</div>

</div>

<script>
  function saveScheduling(t) {
    var url = "/Calltypes/schedule_edit/<?php echo $this->request->data['Schedule']['id']; ?>"
	    $.ajax({
	        url: url,
	        type: 'POST',
	        dataType: 'json',
	        data: $('#CalltypeScheduleEditForm').serialize()
			}).done(function(data) {    
        if (data.success) {
          $('#dialogWin').dialog('close');
					loadCalltypes($('#find_did').val(), 'did-content', 'did-detail', 'didLayout');          
          didLayout.center.children.layout1.close('east')					
        }
        alert(data.msg);
			});    
  }
  
  function checkTS(t) {
    if ($('#checkts').val() == '1') {
    	$('#timesensitive').show();
    	$('#timesensitive input').prop('disabled', false);
    }
    else {
    	$('#timesensitive').hide();
    	$('#timesensitive input').prop('disabled', true);
    }
  }
  
  $(document).ready(function() {  
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      saveScheduling();
    //console.log($('#calltype_form').serialize());
    });
    $('#ts_end_time').on('change', function() {
    	var temp1 = new Date('07/04/2015 ' + $('#ts_start_time').val().replace('am', ' am').replace('pm', ' pm'));
    	var temp2 = new Date('07/04/2015 ' +  $('#ts_end_time').val().replace('am', ' am').replace('pm', ' pm'));
    	start_time = temp1.getHours() * 100 + temp1.getMinutes();
    	end_time = temp2.getHours() * 100 + temp2.getMinutes();
      
      if (start_time != '' && end_time != '') {      
          
        if ($('#ts_start_time').val()!= '' && (end_time < start_time)) {
          alert('Please specify an end time that is later than the start time');
          $('#ts_end_time').val($('#ts_start_time').val());
          $('#ts_end_time').focus();
          
        }
      }
    });     

    $('#ts_start_time').on('change', function() {
    	
    	var temp1 = new Date('07/04/2015 ' + $('#ts_start_time').val().replace('am', ' am').replace('pm', ' pm'));
    	var temp2 = new Date('07/04/2015 ' +  $('#ts_end_time').val().replace('am', ' am').replace('pm', ' pm'));
    	start_time = temp1.getHours() * 100 + temp1.getMinutes();
    	end_time = temp2.getHours() * 100 + temp2.getMinutes();
      if (start_time != '' && end_time != '') {       
        if ($('#ts_end_time').val()!= '' && (end_time < start_time)) {
          alert('Please specify a start time that is earlier than the end time');
          $('#ts_start_time').val($('#ts_end_time').val());
          $('#ts_start_time').focus();
          
        }
      }
    });     
   
    
    $('.timepicker').timepicker({'step': 5});
    checkTS();
    $('.datepicker').datepicker({ minDate: -0, dateFormat: 'yy-mm-dd'});    
  });
</script>
