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

class Account extends OaModel 
{
    public $enc_fields = array("security_question_1","security_question_2","security_question_3","security_answer_1","security_answer_2","security_answer_3");
    
	public $hasMany = array(
		'AccountsEdit' => array(
			'foreignKey' => 'account_id',
			'order' => array('id' => 'desc')
		),	
		'DidNumber' => array(
			'foreignKey' => 'account_id',
			'conditions' => array("DidNumber.deleted" => '0')
			
		)
	);
}
