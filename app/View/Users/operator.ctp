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

<div class="users form panel-content">
<h1><?php echo $this->request->data['User']['firstname'] . ' ' . $this->request->data['User']['lastname']; ?></h1>
<?php echo $this->Form->create('User', array('id' => 'editOperator')); ?>
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
		echo $this->Form->input('username', array('disabled' => true));
		echo $this->Form->input('Misc.password', array('type' => 'password'));
		echo $this->Form->input('firstname');
		echo $this->Form->input('lastname');
    
	?>
	<button onclick="saveOperator(); return false;">Save</button>
</form>
</div>

<script>
  function saveOperator() {
    var $form = $('#editOperator');
    var id = $form.find('input[name="data[User][id]"]').val();
    var url;

	  url = '/Users/operator/' + id ;


    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: $form.serialize()
    }).done(function(data) {    

			alert(data.msg);
		});			        
 		return false;
  }
   
</script> 
