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
$this->Paginator->options(array('update' => '#role-content',
    'evalScripts' => true
    ));
?>
<div class="roles index">
  <div class="panel-content tblheader">
    <h2><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['users']; ?>"></i> <?php echo __('Roles'); ?></h2>
  <?php echo $this->element('paging'); ?>    
  </div>
    <table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
    <tr>
            <th width="100" align="left"><?php echo $this->Paginator->sort('role'); ?></th>
            <th  align="left"><?php echo $this->Paginator->sort('description'); ?></th>
            <th width="50" align="right" class="actions"><?php echo __('Actions'); ?></th>
    </tr>
    <?php
    foreach ($roles as $Role): ?>
    <tr>
        <td onclick="editRole(<?php echo $Role['Role']['id']; ?>);"><?php echo h($Role['Role']['role']); ?></td>
        <td onclick="editRole(<?php echo $Role['Role']['id']; ?>);"><?php echo h($Role['Role']['description']); ?></td>
        <td class="actions" align="center">
        <a href="#" onclick="editRole(<?php echo $Role['Role']['id']; ?>);"><img title="edit" alt="edit" src="/img/edit.png" width="16" height="16"></a>
        <a href="#" onclick="user_confirm('Are you sure you want to delete this role?', function() { deleteRole('<?php echo $Role['Role']['id']; ?>')});return false;"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>
        </td>
    </tr>
<?php endforeach; ?>
    </table>


</div>
<script>
    function editRole(id) {
      loadPage(this, '/Roles/edit/' + id, 'role-detail');
      roleLayout.center.children.layout1.open('east');
    }
    function deleteRole(id) {
        $.ajax({
                url: '/Roles/delete/'+id,
                type: 'get',
                dataType: 'json' ,
            }).done(function(data) {
                alert(data.msg);
                if (data.success) {
                    loadPage(this, '/Roles/', 'role-content');
                    roleLayout.center.children.layout1.close('east');
                }
            });
    }
</script>
<?php
echo $this->Js->writeBuffer();
?>

