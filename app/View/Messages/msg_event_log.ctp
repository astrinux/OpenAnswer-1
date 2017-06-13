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
	<table cellpadding="0" cellspacing="0" class="data">
		<tr>
			<th width="120">Date</th>
			<th width="80">Operator</th>
			<th width="80">User</th>
			<th width="300">Description</th>
		</tr>
	<?php
	foreach ($data as $d) {
	  $e = $d['MessagesEvent'];
		echo '<tr><td>'.$e['created'].'</td>';
		echo '<td>'.$e['operator_id'].'</td>';
		echo '<td>'.$e['user'].'</td>';
		echo '<td>'.$e['description'].'</td></tr>';
	}
	?>
	</table>
