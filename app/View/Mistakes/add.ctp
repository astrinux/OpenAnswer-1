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
    		<form name="mistake_form" id="mistake-form">
    		<input type="hidden" name="url" value="/Mistakes/add">
    		
    		<br><br>
				<?php echo $this->Form->input('Mistake.did_id', array('type' => 'hidden'));?>    		
				<?php echo $this->Form->input('Mistake.message_id', array('type' => 'hidden'));?>    		
				<?php echo $this->Form->input('Mistake.message_created', array('type' => 'hidden'));?>    		
    		<div class="input hrow"><label>Category: </label><?php echo $this->Form->input('Mistake.category', array('id' => 'mistake_category', 'type' => 'select', 'label' => false, 'options' => $mistake_categories));?>
    		</div>
    		<div class="input hrow"><label><em>If 'Other', specify:</em></label> <?php echo $this->Form->input('Mistake.category_other', array('label' => false, 'type' => 'text'));?></div>
   			<div class="input hrow"><label>Operator: </label>
    			<?php echo $this->Form->input('Mistake.mistake_recipient', array('label' => false, 'type' => 'hidden', 'id' => 'm_opsel', 'style' => 'width: 300px;'));?>
    			
    		</div>
    		
    		<div class="input hrow"><label>Description:</label><?php echo $this->Form->input('Mistake.description', array('label' => false, 'type' => 'textarea', 'rows' => 3, 'cols' => 40));?></textarea></div>
   		
    		</form>
    		
    		<script>
  		$(document).ready(function () {   

  			 		
				$.getJSON('/Users/operators.json', function(json) {
					$("#m_opsel").select2({
						data: json.rows,
						multiple: false
					});

				});
								
        $.ui.dialog.prototype._allowInteraction = function(e) {
          return !!$(e.target).closest('.ui-dialog, .ui-datepicker, .select2-drop').length;
        };				
			});
    		</script>
