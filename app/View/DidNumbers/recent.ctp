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
<div class="panel-content">
  
  <table cellpadding="0" border="0" cellspacing="0" class="gentbl">
    <thead>
      <tr>
        <th>Account Number</th>
        <th>Company</th>
        <th>Difficulty</th>
        <th>Created</th>
      </tr>
    
    </thead>
    <tbody>
  <?php 
  //print_r($data); 
  
  foreach ($data as $d) {
    ?>
      <tr onclick="manualScreenPop('<?php echo  $d['DidNumber']['id']; ?>', null);">
        <td><?php echo $d['Account']['account_num']; ?></td>
        <td><?php echo $d['DidNumber']['company']; ?></td>
        <td><?php echo $d['DidNumber']['difficulty']; ?></td>
        <td><?php echo $d['0']['created']; ?></td>
      </tr>
    <?php
  }
  ?>
    </tbody>
  </table>
</div>
