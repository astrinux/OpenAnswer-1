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
    'update' => '#execution_log',
    'evalScripts' => true
));
?>
<div class="tblheader panel-content">
	<?php
	echo $this->Element('paging');
	?> 
</div> 
<br>
    <table cellpadding="2" cellspacing="0" border="0" class="gentbl">
      <tr>
        <th width="120" align="center">Date</th>
        <th width="350" align="left">Sent to</th>
        <th width="80" align="center">Number of messages</th>
      </tr>
      <?php 
        foreach ($data as $d) {
          $e = $d['MessagesSummaryLog'];
          echo '<tr>';
          echo '<td align="center">'.$d[0]['summary_sent_f'].'</td>';
          echo '<td align="left">'.$e['summary_sent_to'].'</td>';
          if (trim($e['message_ids']))
            $msg_ids = explode(',', $e['message_ids']);
          else $msg_ids = array();
          echo '<td align="center">'.count($msg_ids);
          if (count($msg_ids)) {
            echo '<br>';
            foreach ($msg_ids as &$msg_id) {
              ?>
              <a href="#" onclick="loadMessage('<?php echo $msg_id; ?>','<?php echo $e['did_id']; ?>', null, true, null); return false;"><?php echo $msg_id; ?></a>&nbsp;&nbsp;
              <?php
            } 
          }
          else if ($e['no_message_sent'] == '1') echo '<br>No Msg Sent';
          echo '</td>';
          echo '</tr>';
        }
      ?>
    </table>	
    
<?php
echo $this->Js->writeBuffer();

?>