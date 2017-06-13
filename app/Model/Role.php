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
/**
 * Queue Model
 *
 */
class Role extends OaModel 
{
    
    var $hasAndBelongsToMany = array(
        'Permission' => array (
            'className' => 'Permission',
            'joinTable' => 'permissions_roles',
            'foreignKey' => 'role_id',
            'associationForeignKey' => 'permission_id'
        )
    );
    
        public function beforeSave($options = array()){
        foreach (array_keys($this->hasAndBelongsToMany) as $model){
            if(isset($this->data[$this->name][$model])){
				$this->data[$model][$model] = $this->data[$this->name][$model];
				unset($this->data[$this->name][$model]);
			}
		}
		return true;
	}
    

}