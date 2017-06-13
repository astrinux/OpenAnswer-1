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

class ReviewRequestsController extends AppController {
    public $components = array('RequestHandler');
    public $helpers = array('Js');  
    public $paginate;
        
    public function index() {
        $this->paginate['fields'] = array('DidNumber.company', 'Account.account_num', 'ReviewRequest.*', 'User.username');
        $this->paginate['limit'] = 100;
        $this->paginate['order'] = array('created' => 'desc');
        $this->paginate['joins'] = array(
            array('table' => OA_TBL_PREFIX . 'did_numbers',
                'alias' => 'DidNumber',
                'type' => 'left',
                'conditions' => array('ReviewRequest.did_id=DidNumber.id')
            ),
            array('table' => OA_TBL_PREFIX . 'accounts',
                'alias' => 'Account',
                'type' => 'left',
                'conditions' => array('Account.id=DidNumber.account_id')
            ),
            array('table' => OA_TBL_PREFIX . 'users',
                'alias' => 'User',
                'type' => 'left',
                'conditions' => array('User.id=ReviewRequest.user_id')
            )           
        );
        
        $this->set('reviews', $this->paginate());
    }


    public function edit($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('You must specify a request to edit.'), 'flash_jsonbad');
            $this->render('/Elements/json_result');
        }
        if ($this->request->is('post') || $this->request->is('put')) {


            if ($this->ReviewRequest->save($this->request->data)) {
                $this->Session->setFlash(__('The changes have been saved'), 'flash_jsongood');
            } 
            else {
                $this->Session->setFlash(__('The changes could not be saved. Please, try again.'), 'flash_jsonbad');
            }
            $this->render('/Elements/json_result');
        } else {
            $joins = array(
                array('table' => OA_TBL_PREFIX . 'did_numbers',
                    'alias' => 'DidNumber',
                    'type' => 'left',
                    'conditions' => array('ReviewRequest.did_id=DidNumber.id')
                ),
                array('table' => OA_TBL_PREFIX . 'accounts',
                    'alias' => 'Account',
                    'type' => 'left',
                    'conditions' => array('Account.id=DidNumber.account_id')
                ),
                array('table' => OA_TBL_PREFIX . 'users',
                    'alias' => 'User',
                    'type' => 'left',
                    'conditions' => array('User.id=ReviewRequest.user_id')
                )           
            );        
            $fields = array('ReviewRequest.*', 'Account.account_num', 'DidNumber.company');
            $this->request->data = $this->ReviewRequest->find('first', array('conditions' => array('ReviewRequest.id' => $id), 'joins' => $joins, 'fields' => $fields));
            
        }


    }
    
    
    public function add($did_id=null) {
        $this->loadModel('DidNumber');
        if ($did_id) $this->request->data = $this->DidNumber->find('first', array('recursive' => 0, 'conditions' => array('DidNumber.id' => $did_id)));
        if ($this->request->is('post') || $this->request->is('put')) {
            $data['ReviewRequest']['did_id'] = $this->request->data['DidNumber']['id'];
            $data['ReviewRequest']['description'] = $this->request->data['Misc']['reason'];
            $data['ReviewRequest']['user_id'] = AuthComponent::user('id');
            $this->ReviewRequest->create();
            if ($this->ReviewRequest->save($data)) {
              $content = array();
              if (Configure::read('review_request_email')) {
                  $content[] = 'Account #: ' . $this->request->data['Account']['account_num'];
                  $content[] = 'Company: ' . $this->request->data['DidNumber']['company'];
                  $content[] = 'Requested by: ' . AuthComponent::user('username');
                  $content[] = 'Details: ' . $this->request->data['Misc']['reason'];
                  mail(Configure::read('review_request_email'), 'Request for scripting review', implode("\r\n", $content));
              }
              
              $this->Session->setFlash(__('Request was successfully submitted'), 'flash_jsongood');
              $this->render('/Elements/json_result');
            } 
            else {
              $this->Session->setFlash(__('Request cannot be submitted, try again later'), 'flash_jsonbad');
              $this->render('/Elements/json_result');
            }
            
        }
    }    

}
