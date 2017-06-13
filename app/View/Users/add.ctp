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
	div.users label {display: block; float:left; width:160px; text-align:right; margin-right: 10px;}
</style>

<div class="users form">
<h1>Add New User</h1>
<?php echo $this->Form->create('User', array('id' => 'addUser', 'autocomplete' => 'off')); ?>
	<?php
		echo $this->Form->input('id', array('type' => 'hidden'));
		echo $this->Form->input('username');
		echo $this->Form->input('password', array('type' => 'password'));
		echo $this->Form->input('firstname');
		echo $this->Form->input('lastname');
		echo $this->Form->input('email');
		echo $this->Form->input('display_stat', array('options' => array('0' => 'No', '1' => 'Yes')));
		echo $this->Form->input('role', array('options' => $roles));
	?>
  <br><br><h2>Queue Assignments</h2><br>
	  <table cellpadding="2" cellspacing="0" border="0" class="gentbl" style="margin-left:30px;">
	  <tr><th align="center"><input type="checkbox" value="" onclick="$('input.qcheck').click(); "></th><th align="left">Queue Name</th></tr>
	  <?php
	  foreach ($queues as $q) {
	    echo '<tr>';
	    echo '<td align="center"><input type="checkbox" name="data[Queue][]" value="'.$q['Queue']['extension'].'" class="qcheck"';
	    if (in_array($q['Queue']['extension'], $users_queues)) echo ' checked';
	    echo '></td>';
	    echo '<td>'.$q['Queue']['extension'] . ' - ' . $q['Queue']['descr'] . '</td></tr>';
	  }
	  
	?>
	  </table>	
</form>
</div>

<script>

  $(document).ready(function() {  
    $('#user_save_btn').off('click');
    $('#user_save_btn').on('click', function() {
      saveUser();
    });
	});   
</script> 
