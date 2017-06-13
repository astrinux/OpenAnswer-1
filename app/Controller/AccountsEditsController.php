<?php
/**
 * Controller for OpenAnswer Accounts Edits
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

class AccountsEditsController extends AppController {
    public $paginate;
    public $components = array('RequestHandler');
    public $helpers = array('Js');

    function index($account_id=null) {
        if (!$this->isAuthorized('AccountseditsIndex')) {
            $this->Session->setFlash(__('You are not allowed to view this information, please check with an Administrator if this is something you should be allowed to access.'));
            $this->render('/Elements/html_result');
            return;
        }
        $this->set('account_id', $account_id);
        if ($account_id) {
            $conditions = array('account_id' => $account_id);
            $this->set('target', 'acct-content');
        }
        else {
            $this->set('target', 'report-detail');
            $conditions = array();
        }
        $joins = array(
            array(
                'table' => OA_TBL_PREFIX . 'accounts',
                'alias' => 'Account',
                'type' => 'LEFT',
                'conditions' => array('`AccountsEdit`.`account_id` = `Account`.`id`')
            )
        );
        $this->paginate['limit'] = 100;
        $this->paginate['conditions'] = $conditions;
        $this->paginate['joins'] = $joins;
        $this->paginate['order'] = array('id' => 'desc');
        $this->paginate['fields'] = array('AccountsEdit.*', 'Account.account_num', 'Account.account_name', "DATE_FORMAT(AccountsEdit.created, '%a %c/%d/%y %l:%i %p') as created_f");
        $this->AccountsEdit->recursive = 0;

        $d = $this->paginate();
        foreach ($d as $k=>$e) {
            if ($e['AccountsEdit']['change_type'] == 'edit') {
                $changes = @unserialize($e['AccountsEdit']['description']);
                $text = '';
                if (isset($changes['label'])) {
                    foreach($changes['label'] as $j => $label) {
                        $text .= '<b>' . $label . '</b> changed from <i>'.$changes['old_values'][$j].'</i> to <i>'.$changes['new_values'][$j].'</i><br>';
                    }
                }
                $d[$k]['AccountsEdit']['description'] = $text;
            }
        }
        $this->set('edits', $d);
        $this->set('account_id', $account_id);    
    }
}
    ?>