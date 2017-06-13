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
$this->Paginator->options(array('update' => '#user-content',
    'evalScripts' => true
    ));
?>
<div class="users index">
  <div class="panel-content tblheader">
	<h2><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['users']; ?>"></i> <?php echo __('Users'); ?></h2>
  <?php echo $this->element('paging'); ?>	
  </div>
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
			<th width="50" align="left">&nbsp;</th>
			<th width="100" align="left"><?php echo $this->Paginator->sort('username'); ?></th>
			<th width="120" align="left"><?php echo $this->Paginator->sort('firstname'); ?></th>
			<th width="120" align="left"><?php echo $this->Paginator->sort('lastname'); ?></th>
			<th width="80"><?php echo $this->Paginator->sort('role'); ?></th>
			<th width="80"><?php echo $this->Paginator->sort('display_stat'); ?></th>
			<th width="160"><?php echo $this->Paginator->sort('queues'); ?></th>
			<!--<th width="80"><?php echo $this->Paginator->sort('operator', 'Call Center Staff'); ?></th>-->
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($users as $User): ?>
	<tr>
	<?php
	$photo = trim($User['User']['photo']);
	if (!empty($photo)) {
    ?>
	  <td><div class="avatarS"><img src="data:<?php echo $photo; ?>" width="100%" height="100%"></div></td>
	  <?php
	}
	else {
    ?>
	  <td >&nbsp;</td>
	  <?php
	}
	?>	
		<td onclick="editUser(<?php echo $User['User']['id']; ?>);"><?php echo h($User['User']['username']); ?></td>
		<td onclick="editUser(<?php echo $User['User']['id']; ?>);"><?php echo h($User['User']['firstname']); ?></td>
		<td onclick="editUser(<?php echo $User['User']['id']; ?>);"><?php echo h($User['User']['lastname']); ?></td>
		<td align="center" onclick="editUser(<?php echo $User['User']['id']; ?>);"><?php echo $global_options['roles'][$User['User']['role']]; ?></td>
		<td align="center" onclick="editUser(<?php echo $User['User']['id']; ?>);"><?php if ($User['User']['display_stat'] == '1') echo 'Yes'; else echo 'No'; ?></td>
		<td onclick="editUser(<?php echo $User['User']['id']; ?>);"><?php echo h($User['0']['queues']); ?></td>
		<td class="actions" align="center">
		<?php 
		  $name = str_replace('"', "", $User['User']['firstname']);
		  ?>
		  <a href="#" onclick="user_confirm('Are you sure you want to delete <?php echo $name; ?>?', function() { deleteUser('<?php echo $User['User']['id']; ?>')});return false;"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>
		</td>
	</tr>
<?php endforeach; ?>
	</table>


</div>
<script>
    function editUser(id) {
      loadPage(this, '/Users/edit/'  + id, 'user-detail');
      userLayout.center.children.layout1.open('east');      
    }
    function deleteUser(id) {
			//var myform = $(t).parents('form');    	
			
	    $.ajax({
	        url: '/Users/delete/'+id,
	        type: 'get',
	        dataType: 'json' ,
			}).done(function(data) {    
  				alert(data.msg);
  				if (data.success) {
            loadPage(this, '/Users/', 'user-content');	
						userLayout.center.children.layout1.close('east');     
  				}
  		});    	
    }
</script>
<?php
echo $this->Js->writeBuffer();
?>

