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
<html>
  <head>
    <style>
    body * {font-size: 12px; font-family: Tahoma;}
    .row {clear:both; padding: 10px; border-bottom: 1px dashed #ccc}
    .leftdiv {float: left; width:100px;clear: both;}
    .rightdiv {float: left; margin-left: 10px; width: 500px;}
    tr.uline td {border-bottom: 1px dashed #ccc;}
    
    </style>
  </head>
  <body>
<?php
//if ($include_coverpage) {
if (1) {
?>
<table class="coverpage" cellpadding="4" cellspacing="0" width="816">
  <tr><td>To:</td><td><?php echo $faxto; ?></td></tr>
  <tr><td>From:</td><td><?php echo $faxfrom; ?></td></tr>
  <tr><td>Fax:</td><td><?php echo $faxnumber; ?></td></tr>
  <tr><td>Phone:</td><td><?php echo $faxphone; ?></td></tr>
  <tr><td>Date:</td><td><?php echo $faxdate; ?></td></tr>
  <tr><td>Re:</td><td><?php echo $faxre; ?></td></tr>
  <tr><td>Status:</td><td><?php echo $faxstatus; ?></td></tr>
  <tr><td>Comments:</td><td><?php echo $faxnote; ?></td></tr>
</table>   
				
<!--NewPage-->
<?php

}
echo '<br><br>';
echo '=================================================<br>';
echo $message;
?>
</body>
</html>
