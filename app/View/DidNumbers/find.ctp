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
$accts = Array();
$max_length=100;

foreach ($accounts as $a) {
  $label = $a['a']['account_num'] . ' - ' .$a['d']['company'];
  if ($a['0']['numbers']) {
    $numbers = explode(',', $a['0']['numbers']);
    $aliases = explode(',', $a['0']['aliases']);
    foreach ($numbers as $k => $num) {
  		$num = preg_replace('/[^0-9]/', '', $num);
  		$alias = preg_replace('/[^0-9]/', '', $aliases[$k]);
    	$label .= ' (' . substr($num, 0, 3) . '-' . substr($num, 3, 3) . '-' . substr($num, 6);
    	if (!empty($alias) && $alias != 'null') $label .= '/ '. substr($alias, 0, 3) . '-' . substr($alias, 3, 3) . '-' . substr($alias, 6);
    	$label .= ')';
    }
  }
  if (strlen($label) > $max_length) $label = substr($label, 0, $max_length) . "...";
	$accts[] = array('id' => $a['d']['id'], 'text' => substr($label,0, $max_length), 'value' => $a['a']['account_num']. ' - ' .$a['d']['company']);
}

echo json_encode($accts);
?>