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
class BulletinsController extends AppController {
    public $paginate;
    
/**
 * index method
 *
 * @return void
 */
    public function index($userId=null) {
        $this->Bulletin->recursive = 0;
        
        // search by userId if one is specified
        if (!empty($userId)) {
            $this->paginate['conditions'][] = array('BulletinRecipient.user_id' => $userId);
        }
        $this->paginate['order'] = array('Bulletin.created_ts' => 'desc');
        $this->paginate['group'] = array('BulletinRecipient.bulletin_id');
        $this->paginate['fields'] = array('User.firstname', 'User.lastname', 'Bulletin.id', 'Bulletin.created_by, Bulletin.created_ts, Bulletin.note', 'count(*) as cnt');
        $this->paginate['limit'] = 5;
        $this->paginate['maxlimit'] = 5;
        $this->paginate['joins'] = array(
            array(
                'table' => OA_TBL_PREFIX . 'users',
                'alias' => 'User',
                'type' => 'LEFT',
                'conditions' => array(
                    'User.id = Bulletin.created_by'
                )
            ),
            array(
                'table' => OA_TBL_PREFIX . 'bulletin_recipients',
                'alias' => 'BulletinRecipient',
                'type' => 'LEFT',
                'conditions' => array(
                    'Bulletin.id = BulletinRecipient.bulletin_id'
                )
            )          
        );
        $this->log("before paginate");
        //$this->paginate['group'] = array('Bulletin.created_by, Bulletin.created_ts');
        $this->set('bulletins', $this->paginate());
        $this->log("after paginate");
        //$this->set('bulletins',array());
    }

    public function my_bulletins($userId) {
        $this->index($userId);
    }

/**
 * add method
 *
 * @return void
 */
    public function add() {
        $this->loadModel('User');
        $users = $this->User->fetchCCStaff();
        $this->set('users', $users);
        $failed_recipients = array();
        if ($this->request->is('post')) {
            $ts = date('Y-m-d H:i:s');
            $data['Bulletin']['note'] = $this->request->data['Bulletin']['note'];
            $data['Bulletin']['created_by'] = AuthComponent::user('id');
            $data['Bulletin']['required'] = '1';
            $data['Bulletin']['created_ts'] = $ts;
            $recipients = array();
            
            // save the list of recipients for this bulletin
            foreach ($this->request->data['Bulletin']['uid'] as $u) {
                if ($u) {
                    $row = array();
                    $row['user_id'] = $u;
                    $row['ack'] = 0;
                    $recipients[] = $row;
                }
            }
            $data['BulletinRecipient'] = $recipients;
            $this->Bulletin->create();
            $save_ok = $this->Bulletin->saveAssociated($data);

            if (!$save_ok) {
                    $this->Session->setFlash(__('Failed sending to one or more recipients.'), 'flash_jsonbad');
            }
            else {
                    $this->Session->setFlash(__('Message has been sent to all recipients.'), 'flash_jsongood');
            }
            $this->render('/Elements/json_result');

        }
    }

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function view($id = null) {

        $joins = array(
            array(
                'table' => OA_TBL_PREFIX . 'users',
                'alias' => 'User',
                'type' => 'LEFT',
                'conditions' => array(
                    'User.id = BulletinRecipient.user_id'
                )
            )
 
        );
        $this->set('bulletin', $this->Bulletin->find('first', array('conditions' => array('id' => $id), 'recursive' => 0)));
        $this->set('recipients',  $this->Bulletin->BulletinRecipient->find('all', array('fields' => array('BulletinRecipient.*', 'User.firstname', 'User.lastname', 'BulletinRecipient.ack_ts'), 'joins' => $joins, 'conditions' => array('BulletinRecipient.bulletin_id' => $id))));
        
    }

    public function fetch($id) {
        if (!$id) $this->set('html', $html);
        
        $data = $this->Bulletin->read(null, $id);
        $this->set('data', $data);
    }

    // set the acknowledge flag for a bulletin that has been read by the intended user
    public function acknowledge($bulletin_recipient_id) {
        $b = $this->Bulletin->query("select * from  ".OA_TBL_PREFIX."bulletin_recipients where id='$bulletin_recipient_id' and user_id='".AuthComponent::user('id')."'");
        if ($b) {
            $data['BulletinRecipient']['id'] = $bulletin_recipient_id;
            $data['BulletinRecipient']['ack_ts'] = date('Y-m-d H:i:s');
            $query_ok = $this->Bulletin->BulletinRecipient->save($data['BulletinRecipient']);

            if ($query_ok) {
                $success = 'true';
                $msg = "OK";
            }
            else {
                $success = 'false';
                $msg = 'Cannot acknowledge message read';
            }

            $joins = array(
                array(
                    'table' => OA_TBL_PREFIX . 'bulletin_recipients',
                    'alias' => 'BulletinRecipient',
                    'type' => 'RIGHT',
                    'conditions' => array(
                        'Bulletin.id = BulletinRecipient.bulletin_id'
                    )
                ),
                array(
                    'table' => OA_TBL_PREFIX . 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = Bulletin.created_by'
                    )
                ),
            );      
            
            // return a list of bulletins that require acknowledgements from the user
            $conditions = array(
                'BulletinRecipient.user_id' => $this->Auth->user('id'),
                'Bulletin.required' => '1',
                'BulletinRecipient.ack_ts' => '0000-00-00 00:00:00'
            );
            
            $fields = array('Bulletin.*', 'BulletinRecipient.id', 'BulletinRecipient.ack_ts', 'DATE_FORMAT(Bulletin.created_ts, \'%a %e/%e/%y %l:%i %p\') as created', 'User.firstname', 'User.lastname');
            $bulletins = $this->Bulletin->find('all', array('joins' => $joins, 'conditions' => $conditions, 'fields' => $fields, 'recursive' => false));
            $this->set('success', $success);
            $this->set('msg', $msg);
            $this->set('required', $bulletins);
        }
    }
/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Bulletin->id = $id;
        if (!$this->Bulletin->exists()) {
            throw new NotFoundException(__('Invalid bulletin'));
        }
        if ($this->Bulletin->delete()) {
            $this->Session->setFlash(__('Bulletin has been deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Cannot delete bulletin, please try again later'));
        $this->redirect(array('action' => 'index'));
    }
}
