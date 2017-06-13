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
	div.roles label {display: block; float:left; width:160px; text-align:right; margin-right: 10px;}
</style>

<div class="roles form">
<h1></h1>
<?php echo $this->Form->create('Role', array('id' => 'editRole')); ?>
    <?php
        echo $this->Form->input('id', array('type' => 'hidden'));
        echo $this->Form->input('role');
        echo $this->Form->input('description');
        //echo $this->Form->input('Role.Permission', array('multiple' => 'checkbox'));

        
        
    ?>
        <br><br><h2>Allowed Permissions</h2><br>
        <table cellpadding="2" cellspacing="0" border="0" class="gentbl" style="margin-left:30px;">
        <tr>
            <th align="center">
                <!--
                  <input type="checkbox" value="" onclick="$('input.qcheck').click(); ">
                -->
            </th>
            <th align="left">
                Permission
            </th>
            <th align="left">
                Description
            </th>
        </tr>
        <?php
        foreach ($perms as $permission) {
            //echo print_r($permission,true);
            echo '<tr>';
            echo '<td>';
            echo '<input type="checkbox" name="data[Role][Permission][]" value='.$permission['Permission']['id'];
            if (isset($checked[$permission['Permission']['id']])) {
                echo ' checked = "checked"';
            }
            echo '>';
            echo '</td>';
            echo '<td>'.$permission['Permission']['shortname'].'</td>';
            echo '<td>'.$permission['Permission']['desc'].'</td>';
            echo '</tr>';
        }
        ?>

	  </table>
	
</form>
</div>
<script>
  $(document).ready(function() {  
    $('#role_save_btn').off('click');
    $('#role_save_btn').on('click', function() {
      saveRole();      
    });
  });
    
    function saveRole() {
        var $form = $('#role-detail form');
        var url;
        //if (id)
        url = '/Roles/edit/';
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: $form.serialize()
        }).done(function(data) {    
                    if (data.success) {
                        loadPage(this, '/Roles/', 'role-content');  
                        roleLayout.center.children.layout1.close('east');                               
                    }
            alert(data.msg);
        });                 
        return false;
    }

</script> 
