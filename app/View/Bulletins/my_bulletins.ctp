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
$this->Paginator->options(array('update' => '#bb-content',
    'evalScripts' => true
    ));
?>
<div class="Bulletins index">
  <div class="panel-content tblheader">
	<h2><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['bulletins']; ?>"></i> <?php echo __('Message Bulletins'); ?></h2>
  </div>
	<table cellpadding="0" cellspacing="0" class="gentbl" width="100%">
	<tr>
			<th width="120" align="left"><?php echo $this->Paginator->sort('created_ts', 'Created'); ?></th>
			<th width="320" align="left"><?php echo $this->Paginator->sort('created_by'); ?></th>
	</tr>
	<?php
	foreach ($bulletins as $b): ?>
	<tr onclick="$('#bulletin<?php echo $b['Bulletin']['id']; ?>').toggle(); return false">
		<td><?php echo h($b['Bulletin']['created_ts']); ?></td>
		<td><?php echo $b['User']['firstname'] . " " . $b['User']['lastname']; ?>&nbsp;<br>
		<div id="bulletin<?php echo $b['Bulletin']['id']; ?>" class="is_hidden"><?php echo $b['Bulletin']['note']; ?></div></td>

	</tr>
<?php endforeach; ?>
	</table>


</div>
<?php
echo $this->Js->writeBuffer();
?>

