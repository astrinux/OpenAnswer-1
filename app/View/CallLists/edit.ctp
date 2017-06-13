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
  #CallListEdit .choices {margin: 10px 30px;}
  #CallListEdit h2 {margin-top:30px; margin-bottom: 10px; font-size: 16px; font-weight: normal}
  #CallListEdit div.input {padding: 10px 20px !important;}

 #CallListEdit #sortable1, #CallListEdit #sortable2 { list-style-type: none; margin: 0; min-height: 40px; padding: 0;  padding: 5px; width: 200px;}
 #CallListEdit .del {display:inline; float: right; text-align:right;}
 #CallListEdit #sortable2 li .del {display:none;}
 #CallListEdit li .del a {text-decoration: none;}
 #CallListEdit #sortable1 li .add {display:none;}
 
 #CallListEdit #sortable1 {background: #eee;}
 #CallListEdit #sortable1 li {border: 1px solid #FCEFA1; background: #FBF9EE}
 #CallListEdit #sortable1 li, #CallListEdit #sortable2 li, #CallListEdit #sortable3 li { margin: 5px; padding: 5px;width: 170px; }
  #CallListEdit .slist {float: left; margin-right: 10px; }
  #CallListEdit .cdisabled * {color: #aaa;}
 </style>
 <div class="panel-content">
 <div class="calltypes form">
  <form id="CallListEdit" method="post" >
	<?php

		echo $this->Form->input('CallList.id', array('type' => 'hidden', 'value' => ''));
		echo $this->Form->input('CallListsSchedule.employee_ids', array('type' => 'hidden', 'value' => '', 'id' => 'employee_ids'));
//		echo $this->Form->input('CallList.account_id', array('type' => 'hidden', 'value' => $account_id));
		echo $this->Form->input('CallList.did_id', array('type' => 'hidden', 'value' => $did_id));
	  echo '<h2>Adding a Call List</h2>';
		echo $this->Form->input('CallList.title', array('size' => 40));

    ?>
    
      <h2>Specify when this list is to be used:</h2>
      
			<?php
/*			$options = array('0' => 'Non-recurring', '1' => 'Recurring');
			echo '<div id="recurring">' . $this->Form->input('CallList.recurring', array('legend' => false, 'onclick' => 'checkRecurring();', 'fieldset' => false, 'default' => '0', 'type' => 'radio', 'options' => $options)) . '</div>';

			$options = array('1' => 'Every day', '2' => 'Every week', '3' => 'Every month');
			echo '<div class="recurring input is_hidden">';
			echo '<b>This list recurs:</b> ' . $this->Form->input('CallList.interval', array('div' => false, 'legend' => false, 'fieldset' => false, 'default' => '0', 'type' => 'radio', 'options' => $options));
			echo '</div>';*/

			?>
      
      <div class="input pad">
        <?php 

        $options = array('1' => '<b>This list is valid for the following days:</b>');
        echo $this->Form->input('CallList.list_type', array('type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
        &nbsp;&nbsp;
        <div class="choices cdisabled">
				<?php echo $this->Form->input('CallListsSchedule.mon', array('id' => 'd_mon', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_mon">Mon</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('CallListsSchedule.tue', array('id' => 'd_tue', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_tue">Tue</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('CallListsSchedule.wed', array('id' => 'd_wed', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_wed">Wed</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('CallListsSchedule.thu', array('id' => 'd_thu', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_thu">Thu</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('CallListsSchedule.fri', array('id' => 'd_fri', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_fri">Fri</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('CallListsSchedule.sat', array('id' => 'd_sat', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_sat">Sat</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('CallListsSchedule.sun', array('id' => 'd_sun', 'type' => 'checkbox', 'label' => false, 'div' => false));?> <label for="d_sun">Sun</label><br><br>From: <?php echo $this->Form->input('CallListsSchedule.start_time', array('type' => 'text', 'class' => 'timepicker', 'label' => false, 'div' => false, 'size' => 10));?>&nbsp;&nbsp;&nbsp;&nbsp;To: <?php echo $this->Form->input('CallListsSchedule.end_time', array('type' => 'text', 'class' => 'timepicker', 'label' => false, 'div' => false, 'size' => 10));?>
        </div>
      </div>
      <div class="input pad">
        <?php 
        $options = array('3' => '<b>Date Range</b>');
        echo $this->Form->input('CallList.list_type', array('type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
        <div class="choices cdisabled">    
      <?php
        echo $this->Form->input('CallListsSchedule.start_date', array('label' => 'Start Date', 'div' => false, 'empty' => 'Select', 'type' => 'text', 'size' => 12, 'class' => 'datepicker')) . ' <input class="timepicker" type="text" name="date_time_start" size="8">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo $this->Form->input('CallListsSchedule.end_date', array('label' => 'End Date', 'div' => false, 'type' => 'text', 'size' => 12, 'class' => 'datepicker')) . ' <input class="timepicker" type="text" name="date_time_end" size="8">';
        ?>
        </div>
      </div>
      <div class="input pad">
        <?php 
        $options = array('2' => '<b>Day Range</b>  (ex: Fri 5pm - Mon 8am)');
        echo $this->Form->input('CallList.list_type', array('type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
        <div class="choices cdisabled">    
      <?php
        echo $this->Form->input('CallListsSchedule.start_day', array('label' => 'Start Day', 'div' => false, 'empty' => 'Select', 'options' => $php_daysofweek));
        echo $this->Form->input('CallListsSchedule.day_time_start', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8'));
        //echo ' <input class="timepicker" type="text" name="day_time_start" size="8">';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo $this->Form->input('CallListsSchedule.end_day', array('label' => 'End Day', 'div' => false, 'empty' => 'Select', 'options' => $php_daysofweek));
        echo $this->Form->input('CallListsSchedule.day_time_end', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8'));

        //echo  ' <input class="timepicker" type="text" name="day_time_end" size="8">';
        ?>
        </div>
      </div>  
      
      <div class="input pad">
        <?php 
        $options = array('4' => '<b title="A \'rotating list\' consists of employees that will rotate from day to day.  For example, the list consists of Tom, Jane, and Pat.  Starting on the effective day of this list, Tom will be on-call.  The next day Jane will be on-call, the day after Pat will be on-call. The day after that, the list will rotate to the beginning and Tom will be on-call again.">Rotating List</b>');
        echo $this->Form->input('CallListsSchedule.list_type', array('type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
        <div class="choices cdisabled" >    
        <?php echo $this->Form->input('CallListsSchedule.effective_date', array('label' => 'Effective Date', 'div' => false, 'empty' => 'Select', 'type' => 'text', 'size' => 12, 'class' => 'datepicker'));
        ?>
        </div>
      </div>      
      <br><br>
      <h2>Employees on the list</h2>
<p>Add employees to your list by dragging the list of employees from the right into the box below.</p>
      
      <div class="input pad">
<div class="slist">
<ul id="sortable1" class="droptrue">
</ul>
</div>
<div class="slist">
<ul id="sortable2" class="dropfalse">
<?php 
foreach($employees as $e) {
  echo '<li eid="'.$e['Employee']['id'].'"><span class="add"><a href="#" onclick="addEmployee(this);return false;">&laquo;</a> </span>'.$e['Employee']['name'].'<div class="del"><a href="" onclick="removeEmployee(this);return false;">x</a></div></li>';
}
?>
</ul>      
</div>
<div style="clear:both;"></div>
      </div>            
    </div>
    <?php
		//echo $this->Form->input('type');
		//echo $this->element('CallList_edit');
	?>
</form>
</div>

</div>

<script>

  
 
  
  $(document).ready(function() {  
    $('.choices input').prop('disabled', true);
    $('[title]').tooltip();
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      saveCallList(<?php echo $did_id; ?>);
    //console.log($('#calltype_form').serialize());
    });    
    $('.timepicker').timepicker({'step': 5});
    $('.datepicker').datepicker({ minDate: -0, dateFormat: 'yy-mm-dd'});    
 $( "ul.droptrue" ).sortable({
connectWith: "ul"
});
$( "ul.dropfalse" ).sortable({
connectWith: "ul",
dropOnEmpty: true
});
    
  });
</script>
