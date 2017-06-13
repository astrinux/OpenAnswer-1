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
      <br><br>
      <h2>Specify when this list is to be used:</h2>
      
			<?php
/*			$options = array('0' => 'Non-recurring', '1' => 'Recurring');
			echo '<div id="recurring">' . $this->Form->input('CallList.recurring', array('legend' => false, 'onclick' => 'checkRecurring();', 'fieldset' => false, 'default' => '0', 'type' => 'radio', 'options' => $options)) . '</div>';

			$options = array('1' => 'Every day', '2' => 'Every week', '3' => 'Every month');
			echo '<div class="recurring input is_hidden">';
			echo '<b>This list recurs:</b> ' . $this->Form->input('CallList.interval', array('div' => false, 'legend' => false, 'fieldset' => false, 'default' => '0', 'type' => 'radio', 'options' => $options));
			echo '</div>';*/

			?>
      <div class="input pad"><?php
        $options = array('1' => '<b>All the time (default)</b>');
        if (empty($this->request->data['CallListsSchedule']['list_type'])) 
          echo $this->Form->input('CallListsSchedule.list_type', array('hiddenField' => false, 'type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);', 'checked' => 'checked'));
        else
          echo $this->Form->input('CallListsSchedule.list_type', array('hiddenField' => false, 'type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
      
      </div>
            
      <div class="input pad">
        <?php 

        $options = array('2' => '<b>This list is valid for the following days:</b>');
        echo $this->Form->input('CallListsSchedule.list_type', array('type' => 'radio', 'class' => 'typeradio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
        &nbsp;&nbsp;
        <div class="choices <?php if (!isset($this->request->data['CallListsSchedule']['list_type']) || ($this->request->data['CallListsSchedule']['list_type'] != '2')) echo ' cdisabled' ?>">
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
        echo $this->Form->input('CallListsSchedule.list_type', array('hiddenField' => false, 'class' => 'typeradio', 'type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
        <div class="choices<?php if (!isset($this->request->data['CallListsSchedule']['list_type']) || ($this->request->data['CallListsSchedule']['list_type'] != '3')) echo ' cdisabled' ?>">    
      <?php
        echo $this->Form->input('CallListsSchedule.start_date', array('label' => 'Start Date', 'div' => false, 'empty' => 'Select', 'type' => 'text', 'size' => 12, 'class' => 'datepicker')) . '&nbsp;&nbsp;' . $this->Form->input('Misc.date_time_start', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8')) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo $this->Form->input('CallListsSchedule.end_date', array('label' => 'End Date', 'div' => false, 'type' => 'text', 'size' => 12, 'class' => 'datepicker')) . $this->Form->input('Misc.date_time_end', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8')) ;
        ?>
        </div>
      </div>
      <div class="input pad">
        <?php 
        $options = array('4' => '<b>Day Range</b>  (ex: Fri 5pm - Mon 8am)');
        echo $this->Form->input('CallListsSchedule.list_type', array('hiddenField' => false, 'class' => 'typeradio', 'type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
        <div class="choices<?php if (!isset($this->request->data['CallListsSchedule']['list_type']) || ($this->request->data['CallListsSchedule']['list_type'] != '4')) echo ' cdisabled' ?>">    
      <?php
        echo $this->Form->input('CallListsSchedule.start_day', array('label' => 'Start Day', 'div' => false, 'empty' => 'Select', 'options' => $php_daysofweek));
        echo $this->Form->input('Misc.day_time_start', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8'));
        //echo ' <input class="timepicker" type="text" name="day_time_start" size="8">';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo $this->Form->input('CallListsSchedule.end_day', array('label' => 'End Day', 'div' => false, 'empty' => 'Select', 'options' => $php_daysofweek));
        echo $this->Form->input('Misc.day_time_end', array('label' => false, 'div' => false, 'class' => 'timepicker', 'type'=>'text', 'size' => '8'));

        //echo  ' <input class="timepicker" type="text" name="day_time_end" size="8">';
        ?>
        </div>
      </div>  
      
      <!--<div class="input pad">
        <?php 
        $options = array('5' => '<b title="A \'rotating list\' consists of employees that will rotate from day to day.  For example, the list consists of Tom, Jane, and Pat.  Starting on the effective day of this list, Tom will be on-call.  The next day Jane will be on-call, the day after Pat will be on-call. The day after that, the list will rotate to the beginning and Tom will be on-call again.">Rotating List</b>');
        echo $this->Form->input('CallListsSchedule.list_type', array('hiddenField' => false, 'class' => 'typeradio',  'type' => 'radio', 'options' => $options, 'label' => false, 'div' => false, 'onclick' => 'checkChoice(this);' ));?>
        <div class="choices<?php if (!isset($this->request->data['CallListsSchedule']['list_type']) || ($this->request->data['CallListsSchedule']['list_type'] != '5')) echo ' cdisabled' ?>" >    
        <?php echo $this->Form->input('CallListsSchedule.effective_date', array('label' => 'Effective Date', 'div' => false, 'empty' => 'Select', 'type' => 'text', 'size' => 12, 'class' => 'datepicker'));
        ?>
        </div>
      </div>    -->  
      <br><br>
      <h2>Employees on the list</h2>
      <p>Add employees to your list by clicking next to the '+' next to the employee's name. 
      Use 'click, drag and drop' to reorder employees in your.</p>
            
      <div class="input pad">
        <div class="slist">
          <ul id="sortable1" class="droptrue">
          <?php
          if (isset($this->request->data['CallListsSchedule']['employee_ids'])) {
            $selected = explode(',', $this->request->data['CallListsSchedule']['employee_ids']);
            foreach ($selected as $k) {
              if (isset($employees[$k])) {
                $e = $employees[$k];
                echo '<li eid="'.$e['Employee']['id'].'"><span class="add"><a href="#" onclick="addEmployee(this);return false;">&laquo;</a> </span>'.$e['Employee']['name'].'<div class="del"><a href="" onclick="removeEmployee(this);return false;">x</a></div></li>';
              }
            }
          }
          else $selected = array();
          ?>
          </ul>
        </div>
        <div class="slist" style="max-height: 400px; width: 300px; overflow:auto;">
          <ul id="sortable2" class="dropfalse">
          <?php 
          foreach($employees as $k => $e) {
            //if (!in_array($k, $selected)) {  // disable to display all employees, even those already in list
            if (1) {
              echo '<li eid="'.$e['Employee']['id'].'"><span class="add"><a href="#" onclick="addEmployee(this);return false;" style="text-decoration:none;">+</a> </span>'.$e['Employee']['name'].'<div class="del"><a href="" onclick="removeEmployee(this);return false;" style="text-decoration:none;">x</a></div></li>';
            }
          }
          ?>
          </ul>      
        </div>
        <div style="clear:both;"></div>
      </div>            

