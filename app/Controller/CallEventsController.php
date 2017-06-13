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
class CallEventsController extends AppController {
  public $paginate;
    public $components = array('RequestHandler');
    public $helpers = array('Js');

    
    
    /**
 * add method
 * Creates a new event in the database for a call
 */
    public function add($call_id = null, $event_type=null) {
        if (!$call_id) {
            $this->Session->setFlash(__('No call id specified.'), 'flash_jsonbad');
        }
        if ($this->request->is('post')) {
              $this->request->data['CallEvent']['call_id'] = $call_id;
            $this->request->data['CallEvent']['user_id'] = AuthComponent::user('id');
            $this->request->data['CallEvent']['extension'] = $this->user_extension;          
            $this->request->data['CallEvent']['description'] = $this->request->data['event_txt'];
            $this->request->data['CallEvent']['event_type'] = $event_type;
            $this->request->data['CallEvent']['button_data'] = '';
            if (!empty($this->request->data['level'])) {
                $this->request->data['CallEvent']['level'] = $this->request->data['level'];
            }
            else {
                $this->request->data['CallEvent']['level'] = EVT_LVL_ADMIN;
            }
            $this->CallEvent->create();
            if ($this->CallEvent->save($this->request->data['CallEvent'])) {
                $success = true;
                $msg = "The call event has been saved";
                $row = '<tr><td>'.date('D n/d/y g:i:s A').'</td><td>'.AuthComponent::user('username').'</td><td>'.$this->request->data['event_txt'].'</td></tr>';
            } 
            else {
                $success = false;
                $row = '';
                $msg = "The call event could not be saved. Please, try again";
            }
        }
        else {
            $this->request->data['CallEvent']['call_id'] = $call_id;
        }
        
        $this->set(compact('success', 'msg', 'row'));
        $this->set('_serialize', array('success', 'msg', 'row'));
    }
    
    
    
    
    public function operator_custom($call_id = null) {
        $this->custom($call_id);
        $this->set('reload', false);
        $this->render('custom');
    }    
    
    public function custom($call_id = null) {
        if (!$call_id) {
            $this->Session->setFlash(__('No call id specified.'), 'flash_jsonbad');
        }
        $this->set('reload', true);
        if ($call_id) {
            $this->request->data['CallEvent']['call_id'] = $call_id;
        }
        if ($this->request->is('post')) {
            $this->request->data['CallEvent']['user_id'] = AuthComponent::user('id');
            $this->request->data['CallEvent']['extension'] = $this->user_extension;
            $this->request->data['CallEvent']['description'] = $this->request->data['CallEvent']['description'];
            $this->request->data['CallEvent']['button_data'] = '';
            if (!empty($this->request->data['level'])) {
                $this->request->data['CallEvent']['level'] = $this->request->data['level'];
            }
            else {
                $this->request->data['CallEvent']['level'] = EVT_LVL_ADMIN;
            }
            $this->CallEvent->create();
            if ($this->CallEvent->save($this->request->data['CallEvent'])) {
                $this->Session->setFlash(__('The call event has been saved'), 'flash_jsongood');
            } else {
                $this->Session->setFlash(__('The call event could not be saved. Please, try again.'), 'flash_jsonbad');
            }
            $this->render('/Elements/json_result');    
        }
        else {
            $this->request->data['CallEvent']['call_id'] = $call_id;
        }
    }
    
    public function buttonClick($call_id) {
        // Level:  Visible to 1=Customer, 10=Operator, 20=Manager, 30=Admin, 40=Superuser      
        if (!$call_id) {
                $this->Session->setFlash(__('No call ID specified.'), 'flash_jsonbad');        
                $this->render('/Elements/json_result');                
        }
        if ($this->request->is('post')) {
            $d['CallEvent']['call_id'] = $call_id;
            $d['CallEvent']['user_id'] = AuthComponent::user('id');
            $d['CallEvent']['extension'] = $this->user_extension;          
            $d['CallEvent']['description'] = $this->request->data['event'];
            $d['CallEvent']['button_data'] = serialize($this->request->data['attrs']);
            if (isset($this->request->data['level'])) {
                $d['CallEvent']['level'] = $this->request->data['level'];
            }
            else {
                $d['CallEvent']['level'] = EVT_LVL_ADMIN;
            }
            $d['CallEvent']['event_type'] = '2';
            $this->CallEvent->create();
            if ($this->CallEvent->save($d['CallEvent'])) {
                $this->Session->setFlash(__('The call log has been saved'), 'flash_jsongood');
            } 
            else {
                $this->Session->setFlash(__('The call log could not be saved. Please, try again.'), 'flash_jsonbad');
            }
            $this->render('/Elements/json_result');
        }
    }    

}
