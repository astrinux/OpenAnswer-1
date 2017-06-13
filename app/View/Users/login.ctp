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
<?php
$this->extend('/Common/view');
?>
<style>
#station_display {text-align: center; font-size:30px; font-weight: normal;color: #707070; margin-top: 40px; margin-bottom: 10px; line-height: 42px;}
#station_num {font-size:50px; font-weight: bold; color: #707070}
#station_display a, #station_display a:hover {text-decoration:none; font-size: 14px; color: #c0c0c0;}
#station_display a:hover {text-decoration: underline;}


h1 {font-size:30px; font-weight: bold; color: #ccc}
.flashmsg {color: #aaa; text-align:center; margin: 60px 0px 0px 0px; font-style:italic;}

#topbar {width:100%; height: 23px; background-color: #0082cb; margin-bottom:62px;}
.users input {font-size:14px !important; padding: 6px !important; margin-bottom: 10px;}
div.users {width:486px; margin: 0px auto; }
div.users .form {width:50%; text-align:left;float:left;padding-top:50px;}
div.users .station {width:50%; float:left;}

#station_input {font-size: 12px; color: #444; margin-top: 10px;}
div.users input[type=submit] {background-color:#c14105; color: white;     
  border:0px;   
       -webkit-border-radius:4px; 
       -moz-border-radius:4px; 
       border-radius:4px; 
       margin-left:120px;};
</style>



<div id="topbar">&nbsp;</div>

<center><img src="/themes/vn/logo.png" width="434" height="76" alt="OpenAnswer"></center>
<div class="flashmsg"><?php echo $this->Session->flash('auth'); ?>&nbsp;</div>

<div class="users form" id="login_form">

  <?php echo $this->Form->create('User', array('name'=>'userform')); ?>
  <div class="form">

        <?php echo $this->Form->input('username', array('id'=>"username", 'placeholder' => 'Username', 'div' => false, 'label' => false)) . '<br>';
        echo $this->Form->input('password', array('id' => 'password', 'placeholder' => 'Password', 'div' => false, 'label' => false));        

    ?>
    <div id="station_input" style="display:none; ">
      <b>Station ID:<br><input type="text" id="stationid" name="User[stationid]" value="">
    </div>
    
    <br>
    <input type="submit" value="Let's Go!" onclick="return storeStationId();" />
  </div>
  <div class="station">

    
    <div id="station_display"  style="display:none;">
      Station<br>
      <div id="station_num">
      </div>
      <a href="#" onclick="clearStationId();return false;" >Re-enter station id</a>
    </div>
   </div>
   </form>
</div>
<div style="display:none;">
    <div id="dialog-alert" title="Info">
      <div id="alert_message"></div>
    </div>    
</div>

<script type="text/javascript">
function clearStationId() {
  localStorage.removeItem("stationId");
  $('#station_input').show();
}

function supports_html5_storage() {
  try {
    return 'localStorage' in window && window['localStorage'] !== null;
  } catch (e) {
    return false;
  }
}

function storeStationId() {
  localStorage.stationId = document.getElementById('stationid').value;
  if (document.getElementById('username').value == '') {
    alert('Please enter your username');
    document.getElementById('username').focus();
    return false;
  }
  if (document.getElementById('password').value == '') {
    alert('Please enter your password');
    document.getElementById('password').focus();
    return false;
  }
  if (document.getElementById('stationid').value== '') {
    alert('Please enter the station ID');
    document.getElementById('stationid').focus();
    return false;
  }  
  return true;
}

$(function () {
  if (!supports_html5_storage()) {
    $('#login_form').html('<br><br><br><h1><center>You must use a modern browser that supports \'local storage\'</center></h1>');
  }
  else {  
    var stationId = localStorage.stationId;
    console.log(stationId);
    if (!stationId) {
      $('#station_id').show();
      $('#station_display').hide();
      $('#station_input').show();      
    }
    else {
      document.userform.stationid.value = stationId;
      $('#station_num').html(stationId);
      $('#station_display').show();
      $('#station_input').hide();
    }
  }
});
</script>