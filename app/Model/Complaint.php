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

class Complaint extends OaModel {
    
    //encrypted field list
    public $enc_fields = array("description","callers_name","investigation","resolution");
	
	public $hasMany = array(
		'ComplaintsOperator' => array(
			'foreignKey' => 'complaint_id',
			'order' => 'ComplaintsOperator.id desc'
		)
	);	
	public $virtualFields = array(

		'created_f' => "DATE_FORMAT(Complaint.created, '%c/%d/%Y %l:%i %p')",
		'incident_date_f' => "DATE_FORMAT(Complaint.incident_date, '%c/%d/%Y %l:%i %p')"   	
	);		
}
