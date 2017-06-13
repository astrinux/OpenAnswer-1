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

App::uses('AppController', 'Controller');
class AppSettingsController extends AppController {
    
    public function beforeFilter() {
    parent::beforeFilter();
    }
    
    public function index() {
    }
    
    public function edit() {
        $this->layout = 'plain';
        if ($this->request->is('post')) {
            if (!$this->isAuthorized('AppsettingsEdit')) {
                $this->Session->setFlash(__('Not Authorized', 'flash_jsongood'));
                $this->render('/Elements/json_result');
            return;
        }
            $conditions = array(
                'OR' => array("AppSetting.user_id = '0'")
            );              
            $settings = $this->AppSetting->find('list', array('fields' => array('field','id'), 'conditions' => $conditions));
            $save_ok = true;
            foreach ($this->request->data['AppSetting'] as $key => $val) {
                if (isset($data['AppSetting'])) {
                    unset($data['AppSetting']);
                }
                if (isset($settings[$key])) {
                    $data['AppSetting']['id'] = $settings[$key];
                    $data['AppSetting']['value'] = $val;
                    $save_ok = $save_ok && $this->AppSetting->save($data['AppSetting']);
                }
            }      
            if ($this->data['section'] == 'highlights') {
                $text = 'The changes have been saved.  <b>ATTENTION: Please refresh the browser</b> for the changes to take effect.';
            }
            else {
                $text = 'The changes have been saved';
            }
            if ($save_ok) {
                $this->Session->setFlash($text, 'flash_jsongood');
            }
            else {
                $this->Session->setFlash(__('Could not save one or more changes, please try again later'), 'flash_jsonbad');
            }
            $this->render('/Elements/json_result');
        }
        else {
            if (!$this->isAuthorized('AppsettingsView')) {
                $this->Session->setFlash(__('Not Authorized'));
                $this->render('/Elements/html_result');
                return;
            }
            $conditions = array(
                'OR' => array("AppSetting.user_id = '0'")
            );              
            $this->request->data['AppSetting'] = $this->AppSetting->find('list', array('fields' => array('field','value'), 'conditions' => $conditions));
        }
    }
}
