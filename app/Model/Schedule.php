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

class Schedule extends OaModel {

	public $virtualFields = array(
    'starttime' => 'TIME_FORMAT(start_time, \'%l:%i %p\')',
    'endtime' => 'TIME_FORMAT(end_time, \'%l:%i %p\')',
    'startdate' => 'DATE_FORMAT(start_date, \'%b %e, %Y %l:%i %p\')',
    'enddate' => 'DATE_FORMAT(end_date, \'%b %e, %Y %l:%i %p\')',
    'start_date_f' => 'DATE_FORMAT(start_date, \'%Y-%m-%d %l:%i%p\')',
    'end_date_f' => 'DATE_FORMAT(end_date, \'%Y-%m-%d %l:%i%p\')' 
	);
		
	/*public $belongsTo = array(
		'Calltype' => array(
			'foreignKey' => 'calltype_id'
		)
	);*/

	
	public $hasMany = array(
		'Action' => array(
			'foreignKey' => 'schedule_id',
			'order' => 'Action.sort ASC',
			'dependent' => true
		)
	);	


}
