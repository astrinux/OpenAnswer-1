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
  .llabel label {display: inline; }  
  
 </style>
 <div class="panel-content">
 <div class="calltypes form">
  <form id="CalltypeScheduleEditForm" method="post" accept-charset="utf-8">
	<?php
    if (!empty($calltype_id)) {
  		echo $this->Form->input('Calltype.id', array('type' => 'hidden', 'value' => $calltype['Calltype']['id']));
  	  //echo '<h2>Add Time Sensitive Schedule</h2>';
  	  echo '<h2>Calltype:</label> ' . $calltype['Calltype']['title'] . "</h2>";
  		echo $this->Form->input('Misc.timesensitive', array('id' => 'checkts', 'div' => array('class' => 'input llabel'), 'label' => 'The instructions will vary depending on time of day', 'options' => array('0' => 'No', '1' => 'Yes'), 'onmouseup' => 'checkTS()')) . '&nbsp;&nbsp;&nbsp;';
    }
    else {
  		echo $this->Form->input('Schedule.id', array('type' => 'hidden', 'value' => ''));
  		echo $this->Form->input('Calltype.account_id', array('type' => 'hidden', 'value' => $account_id));
  		echo $this->Form->input('Calltype.did_id', array('type' => 'hidden', 'value' => $did_id));
  	  echo '<h2>Specify the Call Type</h2>';
  		echo $this->Form->input('Calltype.title', array('autocomplete' => 'off', 'empty' => 'Select', 'div' => array('class' => 'input llabel'), 'label' => 'Call Type',  'options' => $calltype_options));
  		echo $this->Form->input('Misc.title_custom', array('autocomplete' => 'off', 'style' =>  'width: 200px;', 'div' => array('class' => 'input llabel'),'label' => '<b>OR</b> Enter a custom type', 'type' => 'text'));	
  		echo $this->Form->input('Misc.timesensitive', array('id' => 'checkts', 'div' => array('class' => 'input llabel'), 'label' => 'The instructions will vary depending on time of day', 'options' => array('0' => 'No', '1' => 'Yes'), 'onmouseup' => 'checkTS()')) . '&nbsp;&nbsp;&nbsp;';
  	}
    ?>
    
    <div id="timesensitive" class="is_hidden">
      <h2>Specify when these instructions are to be used:</h2>
      <div class="input pad llabel">
        <b>These instructions are valid for the following days:</b>
        <div class="choices">
				<?php echo $this->Form->input('Schedule.mon', array('id' => 'd_mon', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_mon">Mon</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.tue', array('id' => 'd_tue', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_tue">Tue</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.wed', array('id' => 'd_wed', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_wed">Wed</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.thu', array('id' => 'd_thu', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_thu">Thu</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.fri', array('id' => 'd_fri', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_fri">Fri</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.sat', array('id' => 'd_sat', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_sat">Sat</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('Schedule.sun', array('id' => 'd_sun', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_sun">Sun</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;From: <?php echo $this->Form->input('Schedule.start_time', array('type' => 'text', 'class' => 'timepicker', 'label' => false, 'div' => false, 'size' => 10));?>&nbsp;&nbsp;&nbsp;&nbsp;To: <?php echo $this->Form->input('Schedule.end_time', array('type' => 'text', 'class' => 'timepicker', 'label' => false, 'div' => false, 'size' => 10));?>
        </div>
      </div>
      <div class="input pad"><b>Date range:</b>
        <div class="choices">    
      <?php
        echo $this->Form->input('Schedule.start_date', array('label' => 'Start Date', 'div' => false, 'empty' => 'Select', 'type' => 'text', 'size' => 12, 'class' => 'datepicker')) . ' <input class="timepicker" type="text" name="data[Misc][date_time_start]" size="8">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo $this->Form->input('Schedule.end_date', array('label' => 'End Date', 'div' => false, 'type' => 'text', 'size' => 12, 'class' => 'datepicker')) . ' <input class="timepicker" type="text" name="data[Misc][date_time_end]" size="8">';
        ?>
        </div>
      </div>
      <div class="input pad"><b>Day range (ex: Fri 5pm - Mon 8am):</b>
        <div class="choices">    
      <?php
        echo $this->Form->input('Schedule.start_day', array('label' => 'Start Day', 'div' => false, 'empty' => 'Select', 'options' => $php_daysofweek)) . '&nbsp;&nbsp;';
        echo $this->Form->input('Misc.day_time_start', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8'));
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo $this->Form->input('Schedule.end_day', array('label' => 'End Day', 'div' => false, 'empty' => 'Select', 'options' => $php_daysofweek)) . '&nbsp;&nbsp;';
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
</form>
</div>

</div>

<script>

  
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
      saveAddScheduling(this, <?php echo $did_id; ?>);
    //console.log($('#calltype_form').serialize());
    });    
    $('.timepicker').timepicker({'step': 5});
    checkTS();
    $('.datepicker').datepicker({ minDate: -0, dateFormat: 'yy-mm-dd'});    
  });
</script>
