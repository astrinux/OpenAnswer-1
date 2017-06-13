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
    		<form name="mistake_form" id="note-form">
    		<br><br>
    		<?php
				echo $this->Form->input('Note.id', array('id' => 'editnote_id', 'type' => 'hidden'));  		
				echo $this->Form->input('Note.account_id', array('type' => 'hidden'));
				echo $this->Form->input('Note.did_id', array('type' => 'hidden'));
				echo $this->Form->input('Note.message_id', array('type' => 'hidden'));
    		
    		echo $this->Form->input('Note.description', array('label' => false, 'type' => 'textarea', 'class' => 'htmleditor', 'maxHeight' => '600', 'minHeight' => '300'));?>
				<div class="input hrow">	
				<?php echo $this->Form->input('Note.visible', array('id' => 'note_visible', 'div' => false, 'type' => 'checkbox', 'label' => false, 'onclick' => "check_note_visibility();"));?>&nbsp;&nbsp;Make this note visible on the operator screen
        </div>
        <div id="note_location" class="is_hidden">
				<i>If visible on the operator screen, this setting will determine where the note will be displayed</i>
				<?php echo $this->Form->input('Note.display_location', array('options' => array('0' => 'left', '1' => 'center', '2' => 'right')));?>  
				<?php echo $this->Form->input('Note.bg_color', array('default' => '#FFFF80', 'class' => 'minicolor'));?>   		
        </div>        
        
				<div class="input hrow is_hidden" id="when_cont">	
				<?php
				echo $this->Form->input('Note.extra_class', array('type' => 'hidden', 'value' => ''));
				echo $this->Form->input('Note.extra_class', array('div' => false,'label' => false, 'type' => 'checkbox', 'value' => 'blinkdiv'));?>&nbsp;&nbsp;Flash this note to increase visibility<br>
				
				<?php echo $this->Form->input('Misc.visible_when', array('id' => 'note_visible_when', 'div' => false, 'type' => 'checkbox', 'label' => false, 'onclick' => "check_note_visibility();"));?>&nbsp;&nbsp;Make this note visible all the time
				 
				
        </div>

   		<?php
    		echo '<div id="note_time" class="is_hidden"><div class="input">';
				echo $this->Form->input('Misc.start_date', array('type' => 'text', 'div' => false, 'class' => 'datepicker'));
				echo '&nbsp;&nbsp;&nbsp;&nbsp;';
				echo $this->Form->input('Misc.start_time', array('type' => 'time',  'interval' => '15', 'div' => false, 'label' => false));
				echo ' EST</div><div class="input">';
				echo $this->Form->input('Misc.end_date', array('type' => 'text', 'div' => false, 'class' => 'datepicker'));
				echo '&nbsp;&nbsp;&nbsp;&nbsp;';
				echo $this->Form->input('Misc.end_time', array('type' => 'time', 'interval' => '15', 'div' => false, 'label' => false));
				echo ' EST</div></div>';
   		?>

    		</form>
    		</div>
    		
<script>
  function check_note_visibility() {
    if (document.getElementById('note_visible').checked) {
      $('#note_location').show();
      $('#when_cont').show();
    }
    else  {
      $('#note_location').hide();
      $('#when_cont').hide();
    }    
    if (document.getElementById('note_visible').checked && !document.getElementById('note_visible_when').checked) $('#note_time').show();
    else $('#note_time').hide();
  }    
  
  $(document).ready(function() {  
    
    $('#note-form .minicolor').minicolors();

    function change_editor_background() {
      $("#note-form .jqte_editor").css("background-color", $('#note-form .minicolor').val());    
    }    
    $('#note-form .htmleditor').jqte();
    $('.datepicker').datepicker({ minDate: -0, dateFormat: 'yy-mm-dd'});    
    check_note_visibility();
    change_editor_background();
    
    $('#note-form .minicolor').on('change', change_editor_background);    
	});   
</script> 
