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
    <div>
        <form autocomplete="off" >
            <b>User:</b> </label><input id="find_user" style="width: 300px;"  class="find_user_sel2 auto" type="hidden">&nbsp;&nbsp;&nbsp; <input type="submit" value="Go" onclick="if ($('#find_user').val() != '') {loadPage(this, 'Users/edit/'+$('#find_user').val(), 'user-detail');userLayout.center.children.layout1.open('east');$('.find_user_sel2').select2('val', '')} else {loadPage(this, 'Users/', 'user-content');userLayout.center.children.layout1.close('east');} return false;">
        </form>
        <br>
        <div class="header_btn"><a href="#" onclick="loadPage(this, '/Users/operator/' + myId, 'user-content'); return false;" ><i class="fa fa-key fa-lg"></i> Change my password</a></div>
        <div class="header_btn"><a href="#" onclick="loadPage(this, '/Users/add', 'user-detail');userLayout.center.children.layout1.open('east'); return false;" ><i class="fa fa-user-plus fa-lg"></i> Add New User</a></div>
        <div class="header_btn"><a href="#" onclick="loadPage(this, '/Users/index/', 'user-content'); return false;" ><i class="fa fa-users fa-lg"></i> Users</a></div>
        <div class="header_btn"><a href="#" onclick="loadPage(this, '/Queues', 'user-content'); return false;" ><i class="fa fa-columns fa-lg"></i> Queues</a></div>
    </div>
