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




App::uses('AppModel', 'Model');
 
class OAModel extends AppModel 
{
	
	public $useDbConfig = 'openanswer';  
	private $deletedId;
	public $tablePrefix = OA_TBL_PREFIX;
	
	function beforeSave($options = array()) 
	{
		parent::beforeSave($options);
		if ($this->name == 'CcactClient') 
		{
			if (!$this->data[$this->name]['id']) 
			{
			$this->data[$this->name]['id'] = create_guid();
			}
		}
	}
	
	
	
	function soft_delete($id=null) 
	{
		if (!empty($id)) 
		{
			$this->id = $id;
		}
		
		$id = $this->id;
		$data['id'] = $id;
		$data['deleted'] = '1';
		$data['deleted_ts'] = date('Y-m-d H:i:s');
		if ($this->save($data)) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}



