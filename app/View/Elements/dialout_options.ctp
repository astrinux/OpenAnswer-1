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
		<div id="callBoxCtrl">
			<div id="inbound">
				<input type="hidden" id="callbox_did" value="">
				<input type="hidden" id="callbox_did_id" value="">
				<input type="hidden" id="success_action" value="">
				<input type="hidden" id="callbox_call_id" value="">
				<input type="hidden" id="callbox_contact_id" value="">
				<input type="hidden" id="callbox_schedule_id" value="">
				<h1>INCOMING CALL</h1>
				<button class="actbtn" id="in_talk">Talk</button>
				<button class="actbtn" id="in_hold">Hold</button>  
				<button class="actbtn" id="in_hangup">Hangup</button>	
				&nbsp;<span  id="in_status" class="call_status">No call</span>	
			</div>
			<div id="outbound">
				<h1>OUTBOUND</h1>
				<input type="hidden" name="action_id" id="cb_action_id" value="">
				<input type="hidden" name="opened_by" id="opened_by" value="">
				<input type="hidden" name="org_num_to_dial" id="org_num_to_dial" value="">
				<input type="text" name="num_to_dial" id="num_to_dial" size="14" />&nbsp;<button  class="actbtn" id="out_dial">Dial</button>&nbsp;&nbsp;<span id="num_to_dial_ext"></span><br>
				<button  class="actbtn" id="out_talk">Talk</button>
				<button  class="actbtn" id="out_hold">Hold</button>  
				<button  class="actbtn" id="out_hangup">Hangup</button>	
				&nbsp;<span  id="out_status" class="call_status">No call</span>	
			</div>
					
			<button  class="actbtn" id="btn_patch">Patch</button>
			<button  class="actbtn" id="btn_ret">Return</button>
			<button  class="actbtn" id="btn_cancel">Cancel</button>
			<br><br>
			<div id="enable_all"><a href="#" id="actbtn_enable">+</a></div>
			<div class="ftext">Inbound: <span id="inboundStatus"></span></div>
			<div class="ftext">Outbound: <span id="outboundStatus"></span></div>
			<div class="ftext">Agent: <span id="agentStatus"></span></div>
		</div>