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
<div class="panel-content">
				<form name="complaint_form" id="complaint-form">
				<input type="hidden" name="url" value="/Complaints/add">
				<?php echo $this->Form->input('Complaint.status', array('options' => $complaint_options));?>				
				<div class="input hrow"><label for="c_caller">Callers name  <i>(optional)</i></label><?php echo $this->Form->input('Complaint.callers_name', array('label' => false, 'div' => false));?></div>
				<div class="input hrow"><label for="c_date">Incident Date</label><?php echo $this->Form->input('Complaint.incident_date', array('type' => 'text', 'div' => false, 'label' => false, 'class' => 'datetimepicker'));?></div>
			
				<?php 
				if (empty($did_id)) {
					echo $this->Form->input('Complaint.did_id', array('id' => 'complaint_did_id', 'type' => 'hidden'));					
					echo $this->Form->input('did_id_txt', array('class' => 'did_find', 'label' => 'Account', 'type' => 'text'));
				}
				else echo $this->Form->input('Complaint.did_id', array('type' => 'hidden'));
				?>
				<?php echo $this->Form->input('Complaint.message_id', array('type' => 'text', 'label' => 'Message# <i>(optional)</i>'));?>
				<input type="hidden" value="2" name="c_type">
				<?php 
				$options = array('Call Handling Issues' =>  'Call Handling Issues',  'Call Length' => 'Call Length', 'Dispatching Issues' => 'Dispatching Issues', 'Hold Time Issues' => 'Hold Time Issues', 'Message Content Issues' => 'Message Content Issues', 'Message Delivery Issues' => 'Message Delivery Issues', 'Message Summary Issues' => 'Message Summary Issues', 'Pronunciation Issues' => 'Pronunciation Issues', 'Rude Operators' => 'Rude Operators', 'Scripting Issues' => 'Scripting Issues', 'Other' => 'Other');
				?>
				<div class="input hrow"><label for="c_caller">Problem</label><?php echo $this->Form->input('Complaint.category', array('type' => 'select', 'empty' => 'Select', 'label' => false, 'options' => $options));?>
				</div>
				<div class="input hrow"><label for="c_other"><em>If 'Other', specify</em></label><?php echo $this->Form->input('Complaint.category_other', array('label' => false, 'type' => 'text'));?></div>
				<div class="input hrow"><div class="input select"><label for="c_opsel">Operator(s)</label><?php echo $this->Form->input('c_opsel', array('label' => false, 'type' => 'hidden', 'id' => 'c_opsel', 'style' => 'width: 300px;'));?></div>
					<!--<input id="c_opsel" name="c_opsel" type="hidden" style="width: 500px;"/>-->
				</div>
				
				<div class="input hrow"><label for="c_notes">Description</label><?php echo $this->Form->input('Complaint.description', array('label' => false, 'type' => 'textarea', 'rows' => 3, 'cols' => 40));?></textarea></div>
   			<div class="input footer"><input type="submit" value="Add" class="submitbtn"></div>
			
				</form>
				</div>
				
				<script>
			$(document).ready(function () {   
				
				<?php 
				if (empty($did_id)) {
					?>
				$( "#complaint-form .did_find" ).autocomplete({
        	source: "/DidNumbers/find/<?php echo (!empty($did_id))? $did_id: ''; ?>",
					select: function(event, ui) {
						$('#complaint_did_id').val(ui.item.id);
					}        	
				});
				<?php
				} 
				?>
				$('#complaint-form .datetimepicker').datetimepicker({
					dateFormat: 'yy-mm-dd'
				});	 ;
				
				$.getJSON('/Users/operators.json', function(json) {
					$("#c_opsel").select2({
						data: json.rows,
						multiple: true
					});

				});
		 		
				
				$('#complaint-form .submitbtn').on('click', function() {
	    	  var formdata = $(this).parents('form').serialize();
					postJson('/Complaints/add',  formdata, function() {
						$('#dialogWin').dialog('close');
					});				
					return false;	
				});				
				
			});
				</script>
