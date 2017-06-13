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
//$this->extend('/Common/view');

?>
<style>



</style>

<div id="photo_wrap"><form class="dropzone" id="user-photo"><div class="dz-default dz-message">
  <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
  <span>Drop files here to upload</span>
  </div>
</form>
</div>


<script>  
$(document).ready(function () {

  $("#user-photo").dropzone({ 
    url: "/Users/upload_photo",
    acceptedFiles: 'image/jpeg,image/png,/image/gif',
    init: function() {
      this.on("success", function(file, responseText) {
        console.log(responseText);
        if (responseText == 'failed') {
          $('#dialogWin').dialog('close');       
          alert('Cannot save profile photo to the server, please try again later');
        }        
        else {
          var imghtml = ' <img src="data:'+ responseText +'" width="100%" height="100%">';
          <?php
          if ($user_id == AuthComponent::user('id')) {
            ?>
            $('.avatarL').html(imghtml);
            $('.avatarS').html(imghtml);
            <?php
          }
          else {
            ?>
            $('.avatarL').html(imghtml);
          <?php
          }
          ?>
          $('#dialogWin').dialog('close');       
          alert('Profile photo was successfully saved');
        }
      });      
    }    
  });
  
});
</script>