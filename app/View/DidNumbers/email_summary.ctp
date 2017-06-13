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
  body {font-size: 12px; padding:30px; line-height: 140%; color: #333; font-family: Verdana;}
  table td{font-size: 12px}
  h3 {font-size:12px; display:inline;}
  h2 {font-size:16px; margin:45px 0px 10px 0px;}
  h1 {font-size:20px;font-weight: bold;}
	label {font-weight: bold; display: inline-block; width:150px; text-align:right; margin-right: 10px;}
	.unknown {font-style:italic; color: #ccc; font-size:11px;}
	.employees {margin: 20px;}
	.contacts {margin:5px 0px; border-bottom: 1px solid #ccc;padding:5px 0px;}
	.contacts label {width: 150px;}
	.schedule {margin: 10px 20px 10px 20px;}
	.actions {margin: 5px 20px 20px 20px;}
	.action {border-bottom: 1px dotted #aaa;padding: 5px; margin:0px;}
	.prompts {margin-left: 20px;margin-top:10px;}
	input[type=text] {border:1px solid #ccc; padding:2px 4px;}
	form {margin:0px;}
	
@media print {
    .noprint {
        display: none;
    }
}	
</style>
<?php if ($success) {?>
  <br><br><br><br>
  <center>The summary was sent to <?php echo $email; ?>
  <br><br><a href="#" onclick="window.close();return false;">Close this window</a></center>
  <?php
}
else {?>
  <br><br><br><br>
  <center>Cannot send summary to <?php echo $email; ?><br><br>
  <a href="/DidNumbers/summary/<?php echo $id; ?>">Try again</center>
  <?php
}
?>
