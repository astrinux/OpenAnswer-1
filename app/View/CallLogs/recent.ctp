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
<div class="panel-content">
<h2><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['calls']; ?>"></i>Recent Calls &nbsp;&nbsp;<a href="#" onclick="$('#recent_calls').load('/CallLogs/my_recent/'+myId);"><img width="16" height="16" src="/img/icons/recycle.png" align="absmiddle"></a></h2>
</div>

<table class="gentbl" cellspacing="0" width="100%">
  <tr>
    <th width="150" align="left">Date</th>
    <th width="100" align="left">Caller ID</th>
    <th width="200" align="left">Company</th>
    <th align="left">Calltype</th>
    <th width="80">Delivered</th>
  </tr>
<?php
foreach ($calls as $c) {
  if ($c['d']['id']) {
	  $url = '/Messages/edit/' . $c['d']['id'] .'/target:dialogWin';
  	echo '<tr>';		
  	$extra = ' onclick="openFromMinder(\''.$c['d']['did_id'].'\', \''. $c['d']['call_id'] .'\',  \''. $c['d']['id'] .'\',  \''. $c['d']['schedule_id'] .'\'); return false;"';
  }
  else {
    $extra = '';
  	echo '<tr>';
  }
  echo '<td'.$extra.'>'.$c['d']['created_f'].'</td>';
  echo '<td'.$extra.'>'.$c['d']['cid_number'].'</td>';
  echo '<td '.$extra.'>'.$c['d']['account_num'].' - ';
  echo $c['d']['company'].'</td>';
  echo '<td'.$extra.'>'.(isset($c['d']['calltype'])? $c['d']['calltype']: '<i>(No message)</i>').'</td>';
  if (!empty($c['d']['delivered'])) {
  	 echo '<td align="center" '.$extra.'>'.($c['d']['delivered']? 'Yes': 'No').'</td>';
  }
  else if ($c['d']['id'])  echo '<td align="center"><a href="#"  onclick="openFromMinder(\''.$c['d']['did_id'].'\', \''. $c['d']['call_id'] .'\',  \''. $c['d']['id'] .'\',  \''. $c['d']['schedule_id'] .'\'); return false;"><img width="16" height="16" src="/img/view2.png" alt="view message" title="view message"></a>';
  else {
    echo '<td align="center">';
    if (empty($c['d']['calltype'])) {
      if (!empty($c['d']['unique_id']) && $c['d']['unique_id'] != 'TESTCALL' && !$c['d']['delivered'] && $c['d']['end_time'] != '0000-00-00 00:00:00') echo ' <a href="#"  onclick="recreateScreenPop('.$c['d']['did_number'].','.$c['d']['did_id'].', \''.$c['d']['unique_id'].'\'); return false;" title="Re-pop operator screen">&nbsp;&nbsp;<span class="fa fa-lg fa-share-square-o"></span></a>';
    }
  }
  echo '</td>';
  echo '</tr>';
}
  ?>
</table>
