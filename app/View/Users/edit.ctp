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
<h1><?php echo $this->request->data['User']['firstname'] . ' ' . $this->request->data['User']['lastname']; ?></h1>
<?php echo $this->Form->create('User', array('id' => 'editUser')); ?>
    <?php
        if (!empty($this->request->data['User']['photo'])) {
        echo '<div class="input"><label>&nbsp;</label><div><div class="avatarL"><img src="data:'.$photo_base64.'" width="100%" height="100%"></div></div>';
      }
      else {
         $initials = substr(trim($this->request->data['User']['firstname']), 0, 1) . substr(trim($this->request->data['User']['lastname']), 0, 1);
         
        echo '<div class="input"><label>&nbsp;</label><div class="avatarL">'.$initials.'</div></div>';
    }
      echo '<div class="input"><label>&nbsp;</label><a href="#" onclick="openDialogWindow(\'/Users/upload_photo/'.$this->request->data['User']['id'].'\' , \'Upload Profile Photo\', null, null, 490, 490);">upload new photo</a></div>';    
      
        echo $this->Form->input('id', array('type' => 'hidden'));
        echo $this->Form->input('username');
        echo $this->Form->input('Misc.password', array('type' => 'password'));
        echo $this->Form->input('firstname');
        echo $this->Form->input('lastname');
        echo $this->Form->input('email');
          $options = $global_options['roles'];
      echo $this->Form->input('role', array('options' => $options));
      
        echo $this->Form->input('display_stat', array('options' => array('0' => 'No', '1' => 'Yes')));
        //echo $this->Form->input('extension');
    
      ?><br><br><h2>Queue Assignments</h2><br>
      <table cellpadding="2" cellspacing="0" border="0" class="gentbl" style="margin-left:30px;">
      <tr><th align="center"><input type="checkbox" value="" onclick="$('input.qcheck').click(); "></th><th align="left">Queue Name</th><th align="left">Penalty</th></tr>
      <?php
      foreach ($queues as $q) {
        echo '<tr>';
        echo '<td align="center"><input type="checkbox" name="data[Queue][]" value="'.$q['Queue']['extension'].'" class="qcheck"';
        if (in_array($q['Queue']['extension'], $users_queues)) echo ' checked';
        echo '></td>';
        
        echo '<td>'.$q['Queue']['extension'] . ' - ' . $q['Queue']['descr'] . '</td>';
        echo '<td><input type="text" class="penalty" name="data[Penalty]['.$q['Queue']['extension'].']" value="'.(isset($users_penalty[$q['Queue']['extension']])? $users_penalty[$q['Queue']['extension']]: '0').'" size="2" maxlength="3">';
        echo '</tr>';
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
    //console.log($('#calltype_form').serialize());
    });
    
    
  $('.penalty').mask("9?99",{placeholder:""});
    });   
</script> 
