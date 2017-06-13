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
$this->Paginator->options(array(
    'update' => '#oncall-2',
    'evalScripts' => true
));
?>
	<?php
	echo $this->Element('paging');
	?> 
	
<h2>Edit History</h2>
    <table cellpadding="2" cellspacing="0" border="0" class="gentbl" width="100%">
      <tr>
        <th width="160">Date</th>
        <th width="100">User</th>
        <th width="350">Description</th>
      </tr>
      <?php 
      if (isset($edits)) {
        foreach ($edits as $e) {
          echo '<tr>';
          echo '<td>'.$e['DidNumbersEdit']['created'].'</td>';
          echo '<td>'.$e['DidNumbersEdit']['user_username'].'</td>';
          echo '<td><div class="descr">'.str_replace("\r\n", "<br>", $e['DidNumbersEdit']['description']);
          /*if ($e['change_type'] == 'delete' && $e['section'] == 'summary') {
          ?>
            <br><a href="#" onclick="user_confirm('Are you sure you want to recover this data?', function() { getJson('/MessagesSummary/recover/<?php echo $e['id']; ?>', null, null); return false;});">recover</a>         
            <?php   
          }*/

          echo '</div></td>';
          echo '</tr>';
        }
      }
      ?>
    </table>	
    
<?php
echo $this->Js->writeBuffer();

?>    
    
