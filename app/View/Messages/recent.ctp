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
/*	    var html = '<table class="mindertbl" cellspacing="0" width="100%"><tr><th>Account</th><th align="left">Date</th></tr>';
	    
	    for (var i=0; i<rows.length; i++) {
	      if (rows[i]['minder_age'] > parseInt(settings['minder_warn_time_3'])) rclass = 'minder_warn_color_3';
	      else if (rows[i]['minder_age'] > parseInt(settings['minder_warn_time_2'])) rclass = 'minder_warn_color_2';
	      else if (rows[i]['minder_age'] > parseInt(settings['minder_warn_time_1'])) rclass = 'minder_warn_color_1';
	      else rclass = '';
	      url = '/Messages/edit/' + rows[i]['id'] +'/target:dialogWin';
	      html += '<tr onclick="openFromMinder(\''+url+'\', \''+rows[i]['call_id']+'\'); return false;" ';
	      if (rclass != '') html += ' class="'+rclass+'"';
	      
	      html += '><td>'+rows[i]['account_num']+'</td><td>'+secondsToTime(rows[i]['minder_age'])+'/'+ secondsToTime(rows[i]['msg_age']) +'</td></tr>';
	    }
	    html += '</table>';*/
	    
	    //print_r($messages);
?>
<table class="gentbl" cellspacing="0" width="100%">
  <tr>
    <th width="150" align="left">Date</th>
    <th width="200" align="left">Company</th>
    <th align="left">Calltype</th>
    <th width="80">Delivered</th>
  </tr>
<?php

foreach ($messages as $m) {
  $url = '/Messages/edit/' . $m['Message']['id'] .'/target:dialogWin';
  echo '<tr onclick="openFromMinder(\''.$url.'\', \''. $m['Message']['call_id'] .'\'); return false;">';
  echo '<td>'.$m[0]['created_f'].'</td>';
  echo '<td>'.$m['Account']['account_num'].' - ';
  echo $m['DidNumber2']['company'].'</td>';
  echo '<td>'.$m['Message']['calltype'].'</td>';
  echo '<td align="center">'.($m['Message']['delivered']? 'Yes': 'No').'</td>';
  echo '</tr>';
}
  ?>
</table>