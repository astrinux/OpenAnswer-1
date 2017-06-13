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
<style>
tr.uline td {border-bottom: 1px dashed #ccc;}
table {font-family: Verdana; font-size:12px;}
</style>

<table cellpadding="4" cellspacing="0" border="0">
  <tr>
    <td width="200">&nbsp;</td><td></td>
  </tr>
  <tr>
    <td align="right">TO:</td>
    <td><?php echo $recipient; ?></td>
  </tr>
<?php echo "\t\r\n"; ?>  
  <tr>
    <td align="right">SENT:</td>
    <td><?php echo $local_time; ?></td>
  </tr>
<?php echo "\t\r\n"; ?>  
  <tr>
    <td align="right">SUBJECT:</td>
    <td>AUTOMATED MAIL DELIVERY</td>
  </tr>
<?php echo "\t\r\n"; ?>  
  <tr>
    <td align="right">FOR:</td>
    <td><?php echo $calltype; ?></td>
  </tr>
<?php echo "\t\r\n"; ?>  
<?php 
if (!empty($caller_id)) {
  ?>
  <tr>
    <td align="right">FROM:</td>
    <td><?php echo $this->element('formatPhone2', array('num' => $caller_id)); ?></td>
  </tr>
<?php 
  echo "\t\r\n"; 
}
?>
<?php 
if (!empty($msg_id)) {
  ?>
  <tr>
    <td align="right">MSG ID:</td>
    <td><?php echo $msg_id; ?></td>
  </tr>
<?php 
  echo "\t\r\n"; 
}
?>
  <tr class="uline">
    <td colspan="2">&nbsp;</td>
  </tr>
  
<?php
foreach ($prompts as $p):
	echo '<tr valign="top"><td align="right">'.$p['caption'].':</td><td> ' . str_replace("\r\n", "<br>", $p['value']). "</td></tr> \t\r\n";
endforeach;


if (sizeof($appts) > 0) {
  echo '<tr><td align="right"><br><b>Appointment</b></td><td></td></tr>';
  foreach ($appts as $row):
    foreach ($row as $p) {
  	  echo '<tr valign="top"><td align="right">'.$p['caption'].':</td><td> ' . str_replace("\r\n", "<br>", $p['value']). "</td></tr> \t\r\n";
  	}
    echo '<tr><td align="right">&nbsp;</td><td>&nbsp;</td></tr>';
  endforeach;
}

?>
</table>
