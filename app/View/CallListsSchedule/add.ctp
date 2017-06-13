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

 #sortable1, #sortable2 { list-style-type: none; margin: 0; min-height: 40px; padding: 0;  padding: 5px; width: 200px;}
 .sel {display:inline; float: right; text-align:right;}
 #sortable2 li .del {display:none;}
 li .sel a {text-decoration: none;}
 #sortable1 li .add {display:none;}
 
 #sortable1 {background: #eee;}
 #sortable1 li {border: 1px solid #FCEFA1; background: #FBF9EE}
 #sortable1 li, #sortable2 li, #sortable3 li { margin: 5px; padding: 5px;width: 170px; }
  .slist {float: left; margin-right: 10px; }
  .cdisabled * {color: #aaa;}
 </style>
 <div class="panel-content">
 <div class="calltypes form">
  <form id="CallListsScheduleAddForm" method="post" >
	<?php
    if (isset($list_id) && $list_id) {
  		echo $this->Form->input('CallListsSchedule.call_list_id', array('type' => 'hidden', 'value' => $list_id));
  		echo $this->Form->input('CallListsSchedule.employee_ids', array('type' => 'hidden', 'value' => '', 'id' => 'employee_ids'));
  //		echo $this->Form->input('CallList.account_id', array('type' => 'hidden', 'value' => $account_id));
  	    	  
    }
    
    echo $this->element('call_lists_schedule');

    ?>
    
</form>
</div>

</div>

<script>

  
   function addCallListsSchedule(did_id, list_id) {
    var employee_ids = [];
    $('#sortable1 li').each(function() {
    	employee_ids.push($(this).attr('eid'));
    });
    $('#employee_ids').val(employee_ids.join(','));
    $('.choices.cdisabled input').val('');    
    $('.choices.cdisabled input').prop('disabled', false);
    var url = "/CallListsSchedule/add/" + did_id + '/' + list_id
	    $.ajax({
	        url: url,
	        type: 'POST',
	        dataType: 'json',
	        data: $('#CallListsScheduleAddForm').serialize()
			}).done(function(data) {    
        if (data.success) {
					loadPage(this, '/CallLists/index/<?php echo $did_id; ?>', 'did-content');          
          didLayout.center.children.layout1.close('east')					
        }
        alert(data.msg);
			});    

  }   
  
  $(document).ready(function() {  
    $('.choices.cdisabled input').prop('disabled', true);
    $('[title]').tooltip();
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      addCallListsSchedule(<?php echo $did_id; ?>, <?php echo $list_id; ?>);
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
