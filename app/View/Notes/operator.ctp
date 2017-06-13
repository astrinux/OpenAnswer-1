
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
$notes_left = $notes_right = $notes_center = '';

foreach ($notes as $n) {
  if (empty($n['n']['bg_color'])) $bg_color = '#ffff80';
  else $bg_color = trim($n['n']['bg_color']);
  $html = '<div style="background-color:'.$bg_color.'">';
  $html .= $n['n']['description'];
  $html .= '</div>';
  if ($n['n']['display_location'] == '0') $notes_left .= $html;
  else if ($n['n']['display_location'] == '1') $notes_center .= $html;
  else if ($n['n']['display_location'] == '2') $notes_right .= $html;
}

$json = array('left' => $notes_left, 'center' => $notes_center, 'right' => $notes_right);
echo json_encode($json);
?>
