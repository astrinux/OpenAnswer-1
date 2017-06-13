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

App::uses('OaModel', 'Model');


class CallListsSchedule extends OaModel {
	
	public $virtualFields = array(
		'start_time_f' => 'DATE_FORMAT(CallListsSchedule.start_time, "%l:%i %p")',
		'end_time_f' => 'DATE_FORMAT(CallListsSchedule.end_time, "%l:%i %p")',
		'startdate_f' => 'DATE_FORMAT(start_date, "%b %e, %Y %l:%i %p")', 
		'enddate_f' => 'DATE_FORMAT(end_date, \'%b %e, %Y %l:%i %p\')',
		'start_date_f' => 'DATE_FORMAT(start_date, \'%Y-%m-%d %l:%i%p\')',
		'end_date_f' => 'DATE_FORMAT(end_date, \'%Y-%m-%d %l:%i%p\')'        
	);  

	public $belongsTo = array(
		'CallList' => array(
			'foreignKey' => 'call_list_id'
		)
	);  
}
