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
<div class="panel-content tblheader">
<h2>Recent Bulletin Messages</h2>
</div>
<?php

if (sizeof($bulletins)) {
  ?>
  <div style="border:0px; overflow:auto; max-height: 300px; float: left; ">
  <table cellpadding="2" cellspacing="0" class="gentbl" width="100%">
    <tr><th>Date</th><th>From</th><th>&nbsp;</th></tr>
    <?php
    foreach ($bulletins as $k => $b) {
      echo '<tr>';
      echo '<td>'.$b[0]['created'].'</td>';
      echo '<td>'.$b['User']['firstname'] . ' ' . $b['User']['lastname'] .'</td>';
      echo '<td><a href="#" onclick="loadBulletin('.$b['Bulletin']['id'].'); return false">view</a></td>';
      echo '</tr>';
    }
    ?>
  </table>
  </div>
  <div style="float:left; margin-left:30px; width:440px; max-height: 300px; overflow:auto;" id="bb-detail2">
  </div>
  <div style="clear:both;"></div>
  <?php
}
else {
  echo '<i>No recent bulletins found</i><br><br>';
}

?>
<script>
function loadBulletin(id) {
  $('#bb-detail2').load('/Bulletins/fetch/' + id);
}
</script>

