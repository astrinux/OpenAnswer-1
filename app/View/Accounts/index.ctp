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
$this->Paginator->options(array('update' => '#acct-list',
    'evalScripts' => true
    ));
?>
<div id="accounts_index" class="accounts index">
  <?php echo $this->element('paging2'); ?>

	<table cellpadding="0" cellspacing="0" class="gentbl">
	<tr>
			<th width="200" align="left"><?php echo $this->Paginator->sort('account_name'); ?></th>
			<th width="100"><?php echo $this->Paginator->sort('account_num'); ?></th>
			<th width="100"><?php echo $this->Paginator->sort('created'); ?></th>
<!--			<th><?php echo $this->Paginator->sort('status'); ?></th>-->
			<th width="80" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($accounts as $account): ?>
	<tr>
		<td onclick="$('#find_account').select2('val', <?php echo $account['Account']['id']; ?>); $('#find_account_go').trigger('click'); acctLayout.center.children.layout1.close('west'); return false;"><?php echo h($account['Account']['account_name']); ?>&nbsp;</td>
		<td align="center" onclick="$('#find_account').select2('val', <?php echo $account['Account']['id']; ?>); $('#find_account_go').trigger('click'); acctLayout.center.children.layout1.close('west'); return false;"><?php echo h($account['Account']['account_num']); ?>&nbsp;</td>
		<td><?php echo date('m/d/Y g:ia', strtotime($account['Account']['created'])); ?>&nbsp;</td>
		<td class="actions" align="center">
		<a href="#" onclick="user_confirm('Are you sure you want to delete this account?', function() {getJson('/Accounts/delete/'  + '<?php echo $account['Account']['id']; ?>', null, function(){if (!accountSpecified()) {loadPage(this, '/Accounts/', 'acct-list'); }});}); return false;"><img src="/img/delete.png" width="16" height="16" title="delete" alt="delete"></a>
		</td>
	</tr>
<?php endforeach; ?>
	</table>


</div>

<?php
echo $this->Js->writeBuffer();
?>

