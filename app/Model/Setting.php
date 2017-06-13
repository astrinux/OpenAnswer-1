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

class Setting extends OaModel {
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (!empty($val['Setting']['value'])) {
                $results[$key]['Setting']['value'] = unserialize($val['Setting']['value']);
            }
        }
        return $results;
    }

    // this function always returns an array.  
    public function fetchOptionsByName($name) {
      $res = array();
      $options = $this->findByName($name);      
      if ($options) {
        $res = $options['Setting']['value'];      
      }
      
      // if non-array value, convert into an array
      if (!is_array($res)) {
        $res = array($res => $res);
      }
      
      return $res;
    }
}
