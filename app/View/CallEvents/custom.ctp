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

.ccactCallEvents label{display:inline-block; margin-right:10px; text-align:right; }
</style>

<div class="ccactCallEvents form">
<?php echo $this->Form->create('CcactCallEvent', array('id' => 'addcustomevent')); ?>
		<h1><?php echo __('Add Custom Call Event'); ?></h1><br><br>
	<?php
		echo $this->Form->input('CallEvent.call_id', array('type' => 'hidden'));
		echo $this->Form->input('CallEvent.description', array('size' => '60'));
		echo $this->Form->input('CallEvent.event_type', array('type'=> 'hidden', 'value' => EVENT_CUSTOM));
	?>
	<br><br>
	<div class="is_hidden">
  <input type="submit" value="Add" onclick="saveCustomEvent('/CallEvents/custom/<?php echo $this->request->data['CallEvent']['call_id']; ?>');return false"/>
  </div>
  </form>
</div>
<script>
function saveCustomEvent(url) {
      var data = $('#addcustomevent').serialize(); 
      
      $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json'
      }).done(function(data) {
        if (data.success) {
          alert('Your changes have been saved');
          <?php if ($reload) {
          ?>
            loadCallEvents('<?php echo $this->request->data['CallEvent']['call_id']; ?>');
          <?php 
          }
          ?>
          $('#dialogWin').dialog('close');
        }
        else alert(data.msg);
      }).fail(function () {
    	  alert('Failed to save your changes, try again later');	      
      });  
}
</script>
