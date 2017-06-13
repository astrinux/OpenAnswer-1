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
if ($num) {
  $num = preg_replace('/[^0-9]/', '', $num);
  if (strlen($num) == 10)
    echo '(' . substr($num, 0, 3) . ') ' . substr($num, 3, 3) . '-' . substr($num, 6); 
  else if (strlen($num) == 11 && substr($num, 0, 1) == '1')
    echo '1 (' . substr($num, 1, 3) . ') ' . substr($num, 4, 3) . '-' . substr($num, 5); 
  else echo $num;
}
else echo '';
?>