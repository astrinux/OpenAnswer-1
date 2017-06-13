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
$this->Paginator->options(array(
    'update' => '#'. $target,
    'evalScripts' => true
));
?>
<div class="acctedits form">
  <div class="panel-content tblheader">
    <h2>Edits</h2>
    
	  <?php
	  echo $this->Element('paging');
	  ?>
  </div>    
    <table cellpadding="2" cellspacing="0" border="0" class="gentbl" width="100%">
      <tr><th width="120" align="left">Date</th>
        <th width="60" align="left">User</th>
        <?php
        if (empty($account_id)) {
          ?>
          <th width="150" align="left">Account</th>
          <?php
        }
        ?>
        <th width="100" align="left">Section</th>
        <th width="350" align="left">Description</th>
      </tr>
      <?php 
      if ($edits) {
        foreach ($edits as $e) {
          echo '<tr>';
          echo '<td>'.$e['0']['created_f'].'</td>';
          echo '<td>'.$e['AccountsEdit']['user_username'].'</td>';
          if (empty($account_id)) echo '<td>' .$e['Account']['account_num']. ' - ' . $e['Account']['account_name'] . '</td>';
          echo '<td>'.$e['AccountsEdit']['section'].'</td>';
          echo '<td>'.str_replace("\r\n", "<br>", $e['AccountsEdit']['description']);
          if ($e['AccountsEdit']['change_type'] == 'delete' && $e['AccountsEdit']['section'] == 'did') {
            ?>
            <br><a href="#" onclick="user_confirm('Are you sure you want to recover this data?', function() { getJson('/DidNumbers/recover/<?php echo $e['AccountsEdit']['did_id']; ?>', null, null); return false;});">recover</a>         
            <?php 
          }
          echo '</td>';
          echo '</tr>';
        }
      }
      ?>
    </table>
</div>
<?php
echo $this->Js->writeBuffer();

?>

