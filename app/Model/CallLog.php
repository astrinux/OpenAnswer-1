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

class CallLog extends OaModel {
    
    public $enc_fields = array("cid_name","cid_number");
    
	public $virtualFields = array(
		'duration' => "IF (end_time <> '0000-00-00', UNIX_TIMESTAMP(start_time) - UNIX_TIMESTAMP(end_time), 'UNKNOWN')",
		'start_time_f' => "DATE_FORMAT(start_time, '%a %m/%d/%Y %l:%i %p')"
	);  
	public $hasMany = array(
		'CallEvent' => array(
			'foreignKey' => 'call_id',
			'order' => 'id asc',
			'dependent' => true
		)
	);    	
	public $hasOne = array(
		'Message' => array(
			'foreignKey' => 'call_id',
			'dependent' => true
		)
	); 	
}
