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


class MessagesSummary extends OaModel {
	public $useTable = 'messages_summary';
	public $virtualFields = array(
		'created_f' => 'DATE_FORMAT(MessagesSummary.created, "%c/%d/%Y %l:%i %p")',
		'start_time_f' => 'DATE_FORMAT(MessagesSummary.start_time, "%l:%i %p")',
		'end_time_f' => 'DATE_FORMAT(MessagesSummary.end_time, "%l:%i %p")',
		'send_time_f' => 'DATE_FORMAT(MessagesSummary.send_time, "%l:%i %p")'
		
	);  
	
	public $hasMany = array(
		'DidNumbersEdit' => array(
			'foreignKey' => 'messages_summary_id',
			'className' => 'DidNumbersEdit',
			'order' => array('id' => 'desc'),
			'limit' => 100
		),
		
		'MessagesSummaryLog' => array(
			'foreignKey' => 'message_summary_id',
			'className' => 'MessagesSummaryLog',
			'fields' => array('MessagesSummaryLog.*', 'DATE_FORMAT(MessagesSummaryLog.summary_sent, "%c/%d/%Y %l:%i %p") as summary_sent_f' ),
			'order' => array('id' => 'desc'),
			'dependent' => true,
			'limit' => 50
		)			
	);	  
}
