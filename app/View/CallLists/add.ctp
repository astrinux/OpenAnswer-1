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

 #sortable1 { list-style-type: none; margin: 0; min-height: 40px;  padding: 5px; width: 200px;}
 #sortable2 { list-style-type: none; margin: 0; min-height: 40px; }
 .del {display:inline; float: right; text-align:right;}
 #sortable2 li .del {display:none;}
 li .del a {text-decoration: none;}
 #sortable1 li .add {display:none;}
 
 #sortable1 {background: #eee;}
 #sortable1 li {border: 1px solid #FCEFA1; background: #FBF9EE}
 #sortable1 li, #sortable3 li { margin: 5px; padding: 5px;width: 170px; }
 #sortable2 li { margin: 0px 5px; padding: 2px 5px;width: 170px; }
 
  .slist {float: left; margin-right: 10px; }
  .cdisabled * {color: #aaa;}
 </style>
 <div class="panel-content">
 <div class="calltypes form">
  <form id="CallListAddForm" method="post" >
	<?php
    if (isset($list_id) && $list_id) {
  		echo $this->Form->input('CallListsSchedule.call_list_id', array('type' => 'hidden'));
  		echo $this->Form->input('CallListsSchedule.employee_ids', array('type' => 'hidden', 'value' => '', 'id' => 'employee_ids'));
  //		echo $this->Form->input('CallList.account_id', array('type' => 'hidden', 'value' => $account_id));
  	    	  
    }
    else {
  		echo $this->Form->input('CallList.id', array('type' => 'hidden'));
  		echo $this->Form->input('CallListsSchedule.employee_ids', array('type' => 'hidden', 'value' => '', 'id' => 'employee_ids'));
  //		echo $this->Form->input('CallList.account_id', array('type' => 'hidden', 'value' => $account_id));
  		echo $this->Form->input('CallList.did_id', array('type' => 'hidden', 'value' => $did_id));
  	  
  	  
  	  echo '<h2>Adding a Call List</h2>';
  		echo $this->Form->input('CallList.title', array('size' => 40));
  		
  		echo '<br><label>&nbsp;</label>';
  		echo $this->Form->input('CallList.hide_from_operator', array('type' => 'checkbox', 'label' => false, 'div' => false)) . '&nbsp;&nbsp;hide from operator<br><br>';
  		
    }
    
    echo $this->element('call_lists_schedule');

    ?>
    
</form>
</div>

</div>

<script>

  
 
  
  $(document).ready(function() {  
    $('.choices.cdisabled input').prop('disabled', true);
    $('[title]').tooltip();
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      saveCallList(<?php echo $did_id; ?>);
    //console.log($('#calltype_form').serialize());
    });    
    $('.timepicker').timepicker({'step': 5});
    $('.datepicker').datepicker({ minDate: -0, dateFormat: 'yy-mm-dd'});    
    $( "ul.droptrue" ).sortable({
      revert: 'invalid',
    receive: function(e,ui) {
      copyHelper= null;
    }      
    });
    $( "ul.dropfalse" ).sortable({
      connectWith: "ul.droptrue",
      dropOnEmpty: true,
      helper: 'clone',
        helper: function(e,li) {
            copyHelper= li.clone().insertAfter(li);
            return li.clone();
        },
        stop: function() {
            copyHelper && copyHelper.remove();
        }
    });
    
  });
</script>
