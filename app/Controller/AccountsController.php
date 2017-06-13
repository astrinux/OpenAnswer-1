<?php
/**
 * Controller for OpenAnswer Accounts
 *
 * @author          VoiceNation LLC
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
/**
 * Accounts Controller
 *
 * @property Account $Account
 */
 
class AccountsController extends AppController {
    public $components = array('RequestHandler');
    public $helpers = array('Js');
    public $paginate;



/**
 *
 * @return void
 */
    public function index() {
        if (!$this->isAuthorized('AccountsIndex')) {
            $this->Session->setFlash(__('Not Authorized.'));
            $this->render('/Elements/html_result');
            return;
        }
        $this->paginate['limit'] = 30;
        $this->paginate['order'] = array('account_name' => 'asc');
        $this->paginate['conditions'] = array('deleted' => '0');
        $this->Account->recursive = 0;
        $this->set('accounts', $this->paginate());
    }



/**
 *
 * @param string $id
 * @return void
 */
    public function view($id = null) {
        if (!$this->isAuthorized('AccountsView')) {
            $this->Session->setFlash(__('Not Authorized.'));
            $this->render('/Elements/html_result');
            return;
        }
        $this->Account->id = $id;
        if (!$this->Account->exists()) {
            throw new NotFoundException(__('Invalid account'));
        }
        $this->set('account', $this->Account->read(null, $id));
    }



/**
 *
 * @return void
 */
    public function add() {
        if (!$this->isAuthorized('AccountsAdd')) {
            $this->Session->setFlash(__('Not Authorized.'), 'flash_jsonbad');
            $this->render('/Elements/json_result');
            return;
        }
        if ($this->request->is('post')) {
            $conditions = array('Account.account_num' => $this->request->data['Account']['account_num'], 'deleted' => '0');
            $acct_exists = $this->Account->find('all', array('conditions' => $conditions));
            if ($acct_exists) {
                $this->Session->setFlash(__('Cannot save the changes, the account number already exists.'), 'flash_jsonbad');
                $this->render('/Elements/json_result');
                return;
            }
            $this->Account->create();
            if ($this->Account->save($this->request->data)) {
                $new_id = $this->Account->getInsertID();
                $this->_saveChanges('Added account ' . $this->request->data['Account']['account_num'], '', serialize($this->request->data), $new_id, '', 'account', 'add');
                $this->set('new_id', $new_id);
                $this->Session->setFlash(__('The account has been added'), 'flash_add_jsongood');
                
                // see if we need to send out account creation notification
                if (Configure::read('new_account_notification')) {
                    $addr = str_replace(' ', '', Configure::read('new_account_notification'));
                    $content = '';
                    foreach ($this->request->data['Account'] as $k => $val) {
                        $content .= $k . ': ' . $val . "\r\n";
                    }
                    App::uses('CakeEmail', 'Network/Email');
                    CakeEmail::deliver(explode(',', $addr), '[OA] New Account Created', $content, array('from' => 'donotreply@voicenation.com'));
                }
                }
                else {
                    $this->Session->setFlash(__('The account could not be added. Please, try again.'), 'flash_jsonbad');
                }
            $this->render('/Elements/json_result');
        }
    }



/**
 *
 * @param string $id
 * @return void
 */
    public function edit($id = null) {
        $this->set('account_id', $id);
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!$this->isAuthorized('AccountsEdit')) {
                $this->Session->setFlash(__('Not Authorized.'), 'flash_jsonbad');
                $this->render('/Elements/json_result');
                return;
            }
            $old = $this->Account->findById($id);
            if ($this->Account->save($this->request->data)) {
                $this->_compareEdits($id, $this->request->data, $old);            
                $this->Session->setFlash(__('The account has been saved'), 'flash_jsongood');
            }
            else {
                $this->Session->setFlash(__('The account could not be saved. Please, try again.'), 'flash_jsonbad');
            }
            $this->render('/Elements/json_result');
        }
        else {
            if (!$this->isAuthorized('AccountsView')) {
                $this->Session->setFlash(__('Not Authorized.'));
                $this->render('/Elements/html_result');
                return;
            }
            $this->Account->unbindModel(
                array('hasMany' => array('AccountsEdit'))
            );          
            $this->request->data = $this->Account->findById($id);
            $this->loadModel('DidNumber');
            $this->DidNumber->unbindModel(
                array('belongsTo' => array('Account'))
            );    
            $joins = array(
                array('table' =>  OA_TBL_PREFIX . 'did_numbers_entries',
                    'alias' => 'DidNumbersEntry',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DidNumber.id = DidNumbersEntry.did_id',
                    )
                )
            );
            $this->DidNumber->recursive = 0;
            $numbers = $this->DidNumber->find('all', array('joins' => $joins, 'group' => array('DidNumber.id'), 'fields' => array('DidNumber.id', 'DidNumber.company', 'GROUP_CONCAT(DidNumbersEntry.number) as numbers'), 'conditions' => array('account_id' => $id, 'DidNumber.deleted' => '0')));
            $this->set('numbers', $numbers);
        }
    }

/**
 *
 * @param string $id
 * @param array $new
 * @param array $old
 * @return void
 */
    function _compareEdits($id, $new, $old) {
        $text = '';
        foreach ($new['Account'] as $k=>$val) {
        if ($old['Account'][$k] !== $val) {
            if (!is_array($val)) $text .= "'$k' changed from '".$old['Account'][$k]."' to '".$val."'\r\n";
        }
        }
        if ($text) {
        $this->_saveChanges($text , serialize($old), serialize($new), $id, '', 'account', 'edit');
        }
    }



/**
 *
 * @param string $id
 * @return void
 */
    public function delete($id = null) {
        if (!$this->isAuthorized('AccountsDelete')) {
            $this->Session->setFlash(__('Not Authorized.'), 'flash_jsonbad');
            $this->render('/Elements/json_result');
            return;
        }
        $this->Account->recursive = 0;
        $data = $this->Account->findById($id);
        if (!$data) {
            $this->Session->setFlash(__('Invalid account, the account was NOT deleted'), 'flash_jsonbad');
          $this->render('/Elements/json_result');
          return;
        }
        $data['Account']['deleted'] = '1';
        $data['Account']['deleted_ts'] = date('Y-m-d H:i:s');
        
        $this->Account->DidNumber->query("update ".OA_TBL_PREFIX."did_numbers set deleted='1', deleted_ts=NOW() where account_id='$id'");
        
        if ($this->Account->save($data['Account'])) {
            $this->Session->setFlash(__('The account has been deleted'), 'flash_jsongood');
        }
        else {
            $this->Session->setFlash(__('The account was NOT deleted'), 'flash_jsonbad');
        }
        $this->render('/Elements/json_result');
    }



/**
 *
 * Takes an account ID or a posted search term and returns a list of accounts
 *
 * @param string $id
 * @return array List of accounts that satisfy the search requirement
 */

    public function find($id=null) {
        if (!$this->isAuthorized('AccountsFind')) {
            $this->Session->setFlash(__('Not Authorized.'));
            $this->render('/Elements/html_result');
            return;
        }
        
        if ($id) {
            $sql = "select a.account_name, a.account_num, a.id from ".OA_TBL_PREFIX."accounts a where a.id = '$id' and a.deleted='0'";
        }
        else {
            // search through account name, subaccount name, subaccount id, phone numbers
            $search = str_replace("'", "\'", $this->request->query['term']);
            $sql = "select a.account_name, a.account_num, a.id from ".OA_TBL_PREFIX."accounts a left join ".OA_TBL_PREFIX."did_numbers d on a.id=d.account_id left join ".OA_TBL_PREFIX."did_numbers_entries e on d.id=e.did_id where a.deleted='0'  and ((e.number like '%$search%' and d.deleted='0') or a.account_name like '%$search%' or a.account_num like '$search%') group by a.id";
        }
        $accounts = $this->Account->query($sql);
        $this->set('accounts', $accounts);
        
    }
    
    public function security($id) {
        if (!$this->isAuthorized('AccountsSecurity')) {
            $this->Session->setFlash(__('Not Authorized.'));
            $this->render('/Elements/html_result');
            return;
        }
        $this->Account->recursive = 0;
        $data = $this->Account->findById($id);
        $this->set('q1', $data['Account']['security_question_1']);
        $this->set('q2', $data['Account']['security_question_2']);
        $this->set('q3', $data['Account']['security_question_3']);
        $this->set('a1', $data['Account']['security_answer_1']);
        $this->set('a2', $data['Account']['security_answer_2']);
        $this->set('a3', $data['Account']['security_answer_2']);
    }
    
}
