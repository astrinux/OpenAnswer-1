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

<div class="ccactCallEvents form panel-content">
<?php echo $this->Form->create('HoldTil', array('id' => 'holdtil')); ?>
		<br><br><br>
	<?php
		echo $this->Form->input('Message.id', array('type' => 'hidden', 'value' => $message_id));
		echo $this->Form->input('Message.hold', array('type' => 'hidden', 'value' => '2'));
		?>
		<h2>Current date/time: &nbsp;</b> <?php echo $current_time; ?></h2><br>
		<?php
		echo $this->Form->input('Message.hold_until', array('interval' => 15, 'label' => 'Hold this message until: '));
	?>
	<br><br>
  <input type="submit" value="Save" onclick="saveHold('/Messages/hold_until/<?php echo $message_id; ?>/<?php echo $call_id?>');return false"/>
  </form>
</div>
<script>
function saveHold(url) {
      var data = $('#holdtil').serialize(); 
      
      $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json'
      }).done(function(data) {
        if (data.success) {
          createToast('info', "Message is held");
          $('#dialogWin').dialog('close');
          if (msgLayout) msgLayout.destroy();
          $('#msgDialogWin').html('');
          msgLayout = null;          
     		  var url = '/Messages/edit/<?php echo $message_id; ?>/target:msgDialogWin';
          openMsgDialogWindow(url, 'Message Review', null, true, 'msg');        

        }
        else alert(data.msg);
      }).fail(function () {
    	  alert('Failed to save your changes, try again later');	      
      });  
}
</script>
