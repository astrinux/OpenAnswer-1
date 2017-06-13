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
  .oncall-1 a, .oncall-1 a:visited {font-style:italic; color: #777;}
  #oncalllist {border:0px;} 
  .choices {margin: 10px 30px;}
  h2 {margin-top:30px; margin-bottom: 10px; font-size: 16px; font-weight: normal}
  div.input {padding: 10px 20px !important;}
  div.textarea label {text-align:top}

 #sortable1, #sortable2 { list-style-type: none; margin: 0; min-height: 40px; padding: 0;  padding: 5px; width: 200px;}
 #sortable2 li .del {display:none;}
 li .sel a {text-decoration: none;}
 #sortable1 li .add {display:none;}
 
 #sortable1 {background: #eee;}
 #sortable1 li {border: 1px solid #FCEFA1; background: #FBF9EE}
 #sortable1 li, #sortable2 li, #sortable3 li { margin: 5px; padding: 5px;width: 170px; }
  .slist {float: left; margin-right: 10px; }
  .cdisabled * {color: #aaa;}
 </style>
 <div class="panel-content" id="oncalllist">
  <ul>
    <li><a href="#oncall-1">Setup</a></li>
    <li><a href="#oncall-2">Edit History</a></li>
  </ul> 
   
  <div class="calltypes form" id="oncall-1">
    <form id="CallListsScheduleEditForm" method="post" >
  	<?php
  
  		echo $this->Form->input('CallList.id', array('type' => 'hidden'));
  		echo $this->Form->input('CallListsSchedule.id', array('type' => 'hidden'));
  		echo $this->Form->input('CallListsSchedule.employee_ids', array('type' => 'hidden', 'id' => 'employee_ids'));
  //		echo $this->Form->input('CallList.account_id', array('type' => 'hidden', 'value' => $account_id));
  		echo $this->Form->input('CallList.did_id', array('type' => 'hidden', 'value' => $did_id));
  	  echo '<h2>On-Call List</h2>';
  		echo $this->Form->input('CallList.title', array('size' => 40));
  		if ($this->request->data['CallListsSchedule']['legacy_list']) echo $this->Form->input('CallListsSchedule.legacy_list', array('label' => "TAS On-Call List<br><span style=\"color:red\">Delete this if you want OA-style call list to appear on the operator screen.</span>", 'rows' => '6', 'cols' => '30'));
  
      echo $this->element('call_lists_schedule');
      ?>
      
    </form>
  </div>
  <div id="oncall-2">
    <h2>Edit History</h2>
    <table cellpadding="2" cellspacing="0" border="0" class="gentbl">
      <tr>
        <th width="160">Date</th>
        <th width="100">User</th>
        <th width="350">Description</th>
      </tr>
    </table>  
  </div>

</div>

<script>

  
  function saveCallListsSchedule(did_id) {
    var employee_ids = [];
    $('#sortable1 li').each(function() {
    	employee_ids.push($(this).attr('eid'));
    });
    $('#employee_ids').val(employee_ids.join(','));
    $('.choices.cdisabled input').val('');    
    $('.choices.cdisabled input').prop('disabled', false);
    var url = "/CallListsSchedule/edit/" + did_id
	    $.ajax({
	        url: url,
	        type: 'POST',
	        dataType: 'json',
	        data: $('#CallListsScheduleEditForm').serialize()
			}).done(function(data) {    
        if (data.success) {
					loadPage(this, '/CallLists/index/<?php echo $did_id; ?>', 'did-content');          
          didLayout.center.children.layout1.close('east')					
        }
        alert(data.msg);
			});    

  }    
  
  $(document).ready(function() {  
    $( "#oncalllist" ).tabs({
 			activate: function( event, ui) {
				if (ui.newPanel.attr('id') == 'oncall-1') {
        }
      }
    });    
    
    $('.choices.cdisabled input').prop('disabled', true);
    $('[title]').tooltip();
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      saveCallListsSchedule(<?php echo $did_id; ?>);
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
    $('#oncall-2').load( "/DidNumbersEdits/changes/oncall/"+<?php echo $id; ?>);  
    
  });
</script>
