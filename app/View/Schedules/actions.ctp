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
	$myhtml = '<ul id="sortable" class="sortables">';
	foreach ($this->request->data['Action'] as $action) {        
		$myhtml .= '<li  class="ui-state-default"><div class="del"><a href="#">x</a></div>' . $this->element('calltype_schedule_edit', array('employees' => $this->request->data['Employee'], 'action' => $action, 'showlinks' => false)) . '</li>';
	}
	$myhtml .= '</ul>';
$jsondata['html'] = $myhtml;
$jsondata['json']['actions'] = $this->request->data['Action'];
$jsondata['json']['employees'] = $this->request->data['Employee'];

echo json_encode($jsondata);
?>
