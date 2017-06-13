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
.timerow td {border-top: 2px solid #555}
</style>
<br><br>
<table cellpadding="4" cellspacing="0" class="gentbl" align="center">
<?php

foreach($edits as $e) {
  $edit_time = $e[0]['edit_time'];
  $ptitles = explode('|', $e['0']['ptitles']);
  $pvalues = explode('|', $e['0']['pvalues']);
  echo '<tr class="timerow"><td>' . $edit_time;
  echo '</td>';
  echo '<td>' . $e[0]['edit_title'] . '</td></tr>';
  foreach($ptitles as $k => $t) {
    echo '<tr>';
    echo '<td align="right" width="150">'.$t.':</td>';
    echo '<td width="200">'.(isset($pvalues[$k])? $pvalues[$k]: '').'</td></tr>';
  }
  echo '</tr>';
}
?>
