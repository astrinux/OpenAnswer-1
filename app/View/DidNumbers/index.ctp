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
$this->Paginator->options(array('update' => '#did-list',
    'evalScripts' => true
    ));
?>

<div id="did_index" class="dids index">
  <?php echo $this->element('paging2'); ?>

	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
			<th width="65%" align="left"><?php echo $this->Paginator->sort('company'); ?></th>
			<th width="25%" align="left"><?php echo $this->Paginator->sort('date_entered', 'Created'); ?></th>
			<th width="50"><?php echo $this->Paginator->sort('status', 'Taking Calls'); ?>
			<th width="10%" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($data as $d): ?>
	<tr>
		<td onclick="$('#find_did').select2('val', <?php echo $d['DidNumber']['id']; ?>); $('#find_did_go').trigger('click'); didLayout.center.children.layout1.close('west'); return false;"><?php echo $d['Account']['account_num'] . ' - ' . h($d['DidNumber']['company']); ?>&nbsp;</td>
		<td><?php echo $d['0']['date_entered_f']; ?></td>
		<td><?php echo $d['DidNumber']['status']? 'Yes': 'No'; ?></td>
		<td class="actions" align="center">
        <?php if ($this->Permissions->isAuthorized('DidnumbersDelete',$permissions)) { ?> 
			  ?>
			<a href="#" onclick="user_confirm('Are you sure you want to delete this?',function() {getJson('/DidNumbers/delete/<?php echo $d['DidNumber']['id']; ?>', null, function() {
			  loadPage(this, '/DidNumbers/index', 'did-list');
			});}); return false;"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>
			<?php 
		  } 
		  ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>


</div>

<?php
echo $this->Js->writeBuffer();
?>
