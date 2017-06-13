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

#dropzone_wrap {
  margin: 30px 0;
  -webkit-box-shadow: 0 0 50px rgba(0,0,0,0.13);
  box-shadow: 0 0 50px rgba(0,0,0,0.13);
  padding: 4px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
}
#dropzone_wrap .dropzone {
  -webkit-box-shadow: none;
  box-shadow: none;
}

#dropzone_wrap form {margin:0px;width:100%;}

</style>
<div id="dropzone_wrap"><form class="dropzone" action="/Files/upload" id="my-dropzone"><div class="dz-default dz-message">
  <input type="hidden" name="did_id" value="<?php echo $did_id; ?>">
  <span>Drop files here to upload</span>
  </div>
</form>
</div>


<script>  
$(document).ready(function () {
  
  /*Dropzone.options.myDropzone = {
    init: function() {
      this.on("success", function(file, responseText) {
        loadPage('/Files/', 'did-content');
      });      
    }
  };*/  
  $("#my-dropzone").dropzone({ 
    url: "/Files/upload",
    init: function() {
      this.on("success", function(file, responseText) {
        loadPage(this, '/Files/index/<?php echo $did_id; ?>', 'did-content');
        didLayout.center.children.layout1.close('east');        
      });      
    }    
  });
  
//  $("#dropzone").addClass('dropzone');  
});
</script>