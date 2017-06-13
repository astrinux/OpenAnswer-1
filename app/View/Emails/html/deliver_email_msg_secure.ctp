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
  <tr>
  <td align="right">Message Link:</td>
        <td>
        <a href="https://dashboard.voicenation.com/OaLiveMessages/view/<?php echo $employee_id?>/<?php echo $message_id ?>">Follow this link to access your secure message</a>
        </td>
  </tr>
  <tr class="uline">
    <td colspan="2">&nbsp;</td>
  </tr>
  
<?php

?>
</table>
