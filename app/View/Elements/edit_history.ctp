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
    <table cellpadding="2" cellspacing="0" border="0" class="gentbl">
      <tr><th width="120">Date</th>
        <th width="100">User</th>
        <th width="350" align="left">Description</th>
      </tr>
      <?php 
      if ($edits) {
        foreach ($edits as $e) {
          echo '<tr>';
          echo '<td align="center">'.$e['DidNumbersEdit']['created'].'</td>';
          echo '<td align="center">'.$e['DidNumbersEdit']['user_username'].'</td>';
          echo '<td>'.str_replace("\r\n", "<br>", $e['DidNumbersEdit']['description']);
          if ($e['DidNumbersEdit']['change_type'] == 'delete' && $e['DidNumbersEdit']['section'] == 'employee') {
          ?>
            <br><a href="#" onclick="user_confirm('Are you sure you want to recover this data?', function() { getJson('/Employees/recover/<?php echo $e['DidNumbersEdit']['id']; ?>', null, null); return false;});">recover</a>         <?php   
          }
          else if ($e['DidNumbersEdit']['change_type'] == 'delete' && $e['DidNumbersEdit']['section'] == 'summary') {
          ?>
            <br><a href="#" onclick="user_confirm('Are you sure you want to recover this data?', function() { getJson('/MessagesSummary/recover/<?php echo $e['DidNumbersEdit']['id']; ?>', null, null); return false;});">recover</a>         <?php   
          }          
          echo '</td>';
          echo '</tr>';
        }
      }
      ?>
    </table>
