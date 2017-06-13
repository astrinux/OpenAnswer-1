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
TO: <?php echo $recipient . "\t\n"; ?>
SENT: <?php echo $local_time . "\t\n"; ?>
SUBJECT: AUTOMATED MAIL DELIVERY <?php echo  "\t\n"; ?>
FOR: <?php echo $calltype . "\t\n"; ?>
<?php 
if (!empty($caller_id)) {
  ?>
FROM: <?php echo $this->element('formatPhone2', array('num' => $caller_id)) . "\t\n"; 
}
?>
<?php 
if (!empty($msg_id)) {
  ?>
MSG ID: <?php echo $msg_id . "\t\n"; 
}
?>
----------------------------------------------------------
<?php
foreach ($prompts as $p) {
  echo $p['caption'] .':  ' . $p['value'] . "\t\n"; ?>
<?php
}

if (sizeof($appts) > 0) {
  echo "\t\n----------------------------------------------------------\t\nAppointment\t\n";

  foreach ($appts as $row):
    foreach ($row as $p) {
  	  echo $p['caption'].': ' . $p['value']. "\t\r\n";
  	}
  endforeach;
}

?>