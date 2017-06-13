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

class Calltype extends OaModel {
  
	public $belongsTo = array(
		'DidNumber' => array(
			'foreignKey' => 'did_id'
		)
	);
	
	public $hasMany = array(
		'Schedule' => array(
			'foreignKey' => 'calltype_id',
			'dependent' => 'true',
			'conditions' => array('Schedule.deleted <>' => '1'),
			'order' => array('Schedule.start_date' => 'desc', 'Schedule.start_day' => 'desc', 'Schedule.check_days' => 'desc')
		)
	);
	

}
