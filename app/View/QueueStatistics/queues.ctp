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
<div id="qstatsdiv" class="Mistakes index" style="height: 100%; width: 100%;">
  <div class="ui-layout-center" style="overflow:hidden">
    <div class="panel-content tblheader">
      <h2>Queue Statistics</h2>
      <p><a href="#" onclick="var url = '/QueueStatistics/queues/'; $('#queuestat').load(url); return false;">Refresh</a></p>
    </div>
	  <div class="tableWrapper tblheader">    
        <table cellpadding="4" cellspacing="0" class="gentbl" width="100%">
    			<COL class="col170">
    			<COL class="col150">
    			<COL class="col80">
    			<COL class="col80">
    			<COL class="col80">	        
    			<COL class="col80">	        
    			<COL class="col80">	        
        	<tr>
        		<th class="col170">Queue</th>
        		<th class="col150">Timestamp</th>
        		<th class="col80" align="center">Pending Calls</th>
        		<th class="col80" align="center">Connected calls</th>
        		<th class="col80" align="center">Avg. Hold Time</th>
        		<th class="col80" align="center">Completed</th>
        		<th class="col80" align="center">Abandoned</th>
        	</tr>
        </table>
    </div>
    
    <div class="data tableWrapper">
      <div class="innerWrapper">
        <table cellpadding="4" cellspacing="0" class="gentbl" width="100%">
    			<COL class="col170">
    			<COL class="col150">
    			<COL class="col80">
    			<COL class="col80">
    			<COL class="col80">	        
    			<COL class="col80">	        
    			<COL class="col80">	        
    			
        <?php
        foreach ($queues as $q) {
        	echo '<tr>';
        	echo '<td class="col170">'.$q['queue'] . ' - ' . $q['description'] .'</td>';
        	echo '<td class="col150">'.$q['created'].'</td>';
        	echo '<td class="col80" align="center">'.$q['pending_calls'].'</td>';
        	echo '<td class="col80" align="center">'.$q['connected_calls'].'</td>';
        	echo '<td class="col80" align="center">'.$q['qholdtime'].'</td>';
        	echo '<td align="center" class="col80">'.$q['qcompleted'].'</td>';
        	if (($q['qabandoned']+$q['qcompleted']) > 0) echo '<td align="center">'.$q['qabandoned']. ' ('.round(($q['qabandoned']*100)/($q['qabandoned']+$q['qcompleted'])).'%)</td>';
        	else echo '<td align="center" class="col80">'.$q['qabandoned'].'</td>';
        	echo '</tr>';
        }
        ?>
        </table>      
      </div>
    </div>
</div>
</div>

<script>
$(document).ready(function() {
    $('.tblheader').css('margin-right', scrollbarWidth);  
  // create the layout - with data-table wrapper as the layout-content element  
  $('#qstatsdiv').layout({
    center__contentSelector: '#qstatsdiv div.data',
    resizeWhileDragging: true
  });
  
});
</script>
