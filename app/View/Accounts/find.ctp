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
$accts = Array();

foreach ($accounts as $a) {
  $label = $a['a']['account_num'] . ' - ' .$a['a']['account_name'];
/*  if ($a['d']['did_number']) {
    $label .= ' (' . substr($a['d']['did_number'], 0, 3) . '-' . substr($a['d']['did_number'], 3, 3) . '-' . substr($a['d']['did_number'], 6) . ')';
  }*/
	$accts[] = array('id' => $a['a']['id'], 'text' => $label, 'value' => $a['a']['account_num']. ' - ' .$a['a']['account_name']);
}

echo json_encode($accts);
?>