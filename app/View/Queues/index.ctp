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
<div class="queues index">
  <div class="panel-content tblheader">
	<h2 class="h2_1"><i class="fa fa-columns fa-lg"></i> <?php echo __('Queues'); ?></h2>
	</div>
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
			<th width="100">Queue</th>
			<th width="250" align="left">Description</th>
	</tr>
	<?php
	foreach ($queues as $queue): ?>
	<tr  onclick="loadPage(this, '/Queues/edit/'  + '<?php echo $queue['Queue']['extension']; ?>', 'user-detail');userLayout.center.children.layout1.open('east');">
		<td align="center"><?php echo h($queue['Queue']['extension']); ?>&nbsp;</td>
		<td><?php echo h($queue['Queue']['descr']); ?>&nbsp;</td>

	</tr>
<?php endforeach; ?>
	</table>

</div>
<?php
echo $this->Js->writeBuffer();
?>
