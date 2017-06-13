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

class DidNumber extends OaModel {
	
	public $belongsTo = array(
		'Account' => array(
			'foreignKey' => 'account_id'
		)
	);
	
	public $hasMany = array(
		'Calltype' => array(
			'foreignKey' => 'did_id',
			'conditions' => array("Calltype.deleted !=" => '1'),
			'order' => array('Calltype.sort' => 'asc')
		),
		'DidFile' => array(
			'foreignKey' => 'did_id',
			'order' => array('DidFile.file_name' => 'asc'),
			'conditions' => array("DidFile.deleted <>" => '1'),
			'fields' => array('DidFile.file_extension', 'DidFile.file_name', 'DidFile.id')
		),
		'DidNumbersEntry' => array(
			'foreignKey' => 'did_id'
		),		
		'Employee' => array(
			'foreignKey' => 'did_id'
		),
		'DidNumbersEdit' => array(
			'foreignKey' => 'did_id',
			'className' => 'AccountsEdit',
			'order' => array('id' => 'desc'),
			'limit' => 50,
			'conditions' => 'DidNumbersEdit.did_id is not null'
		)				
	);	
	
	public function beforeSave($options = array()) {
		if (!empty($this->data['DidNumber']['did_number']))
			$this->data['DidNumber']['did_number'] = preg_replace('/[^0-9]/', '', $this->data['DidNumber']['did_number']);
	}
}
?>