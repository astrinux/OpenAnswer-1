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
          	<div class="ui-layout-north panel-content searchbox">
                <div id="choices_operator_stats" class="choices is_hidden">
                </div>
                <div id="choices_operator_events" class="choices is_hidden">
                </div>
                <div id="choices_account_edits" class="choices is_hidden">
                <form name="account_edits">
                </form>
                </div>
	
  							<div class="reportbtns">
  							    <ul class="subtabs">
                    <li><a href="#" onclick="loadPage(this, '/QueueStatistics/queues/', 'report-detail'); return false;">Queue Stats</a></li>  							  
                    <li><a href="#" onclick="if (0) alert('Cannot communicate with OpenConnector server'); else {
      	          socket.emit('updateAgents', {queue: 'all'}); if (agentCheckTimer) clearInterval(agentCheckTimer);
    agentCheckTimer = setInterval(updateOperatorStats,settings['agent_update_seconds'] * 1000);}return false;">Operators</a></li>
                    <li><a href="#" onclick="$('.choices').hide(); $('#choices_account_edits').show(); loadPage(this,'/UserLogs/events/', 'report-detail'); return false;">Operator Events</a></li>
                    <li><a href="#" onclick="loadPage(this,'/AccountsEdits/index/', 'report-detail'); return false;">Account Edits</a></li>
                    <li><a href="#" onclick="loadPage(this,'/Users/audit/', 'report-detail'); return false;">Call Auditing</a></li>
                    <li><a href="#" onclick="loadPage(this,'/ReviewRequests/index/', 'report-detail'); return false;">Scripting Review</a></li>
                    </ul>
                 </div>
  	        </div>
          	<div class="ui-layout-center">
          		<div class="ui-layout-content" id="report-detail">
                <div class="empty_content"><i class="fa fa-bar-chart"></i> Reports</div>          		
              </div>
          	</div>  	        
  	        
