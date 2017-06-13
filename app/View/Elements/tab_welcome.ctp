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
  							    <ul class="subtabs">
                    <li><a href="#" onclick="$('#welcome-detail').load('/OpenAnswer/welcome/'+myId);">Welcome</a></li>
                    <li><a href="#" onclick="loadPage(this, '/Bulletins/my_bulletins/'+myId, 'welcome-detail'); return false;">My Bulletins</a></li>  							  
                    </ul>
  	        </div>
          	<div class="ui-layout-center">
          		<div class="ui-layout-content" id="welcome-detail">
          		
              </div>
          	</div>              
