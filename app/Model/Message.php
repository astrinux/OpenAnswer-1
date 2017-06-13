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

class Message extends OaModel {
    
    //encrypted field list
    public $enc_fields = array();

	public $belongsTo = array(
		'DidNumber' => array(
			'foreignKey' => 'did_id'
		),
		'CallLog' => array(
			'foreignKey' => 'call_id'
		)		
	);	
	
	public $hasMany = array(

		'MessagesPrompt' => array(
			'foreignKey' => 'message_id',
			'order' => 'MessagesPrompt.action_num asc, MessagesPrompt.sort asc',
			'dependent' => true
		),
		'MessagesDelivery' => array(
			'foreignKey' => 'message_id',
			'order' => 'MessagesDelivery.id desc',
			'dependent' => true
		),
		'Mistake' => array(
			'foreignKey' => 'message_id',
			'order' => 'Mistake.id desc',
			'dependent' => true
		),		
		'Complaint' => array(
			'foreignKey' => 'message_id',
			'order' => 'Complaint.id desc',
			'dependent' => true
		)					
	);
	

}
