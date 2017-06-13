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
  $temp = $num;
  $num = preg_replace('/[^0-9]/', '', $num);
  if (strlen($num) == 10)
    echo '(' . substr($num, 0, 3) . ') ' . substr($num, 3, 3) . '-' . substr($num, 6); 
  else echo $num . ' <span class="mistake"><img src="/img/warning.png" width="16" height="16" alt="!"  title="This number contains less than 10 digits"> '.$temp.'</span>';
}
else echo '';
?>