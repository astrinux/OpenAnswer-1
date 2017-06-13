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
class CrmsController extends AppController
{
    //assign class name and model name, so that model and class can be used dynamically in functions.
    public $thisclass = 'Crms';
    public $thismodel = "Crm";
    public $parentmodel = "DidNumber";
    public $grandparentmodel = "Accounts";
    
    public $paginate;
    public $components = array('RequestHandler');
    public $helpers = array('Js');
    public $theme;
    public $crm_types = array("Sugarcrm" => "Sugar CRM");
    public $crm_methods = array(
    							"create" => "Create Entry",
    							"search" => "Search Entries",
    							"update" => "Update Entry"
    							);
    public $crm_modules = array(
    							"Leads" => "Leads",
    							"Invoices" => "Invoices",
    							"Employees" => "Employees",
    							"Contacts" => "Contacts",
    							"Cases" => "Cases",
    							);
    
    public function beforeFilter()
    {
        parent::beforeFilter();
    }
    
    public function index($parent_id) {
        $conditions = array($this->thismodel.'.parent_id' => $parent_id, $this->thismodel.'.deleted' => 0);
        $this->paginate['limit'] = 600;
        $this->paginate['conditions'] = $conditions;
        $items = $this->paginate();
        $this->set('items', $items);
        $this->set('parent_id', $parent_id);
        $this->set('thisclass',$this->thisclass);
        $this->set('thismodel',$this->thismodel);
    }

/**
 * edit method, allows editing of an existing record, or adding a new record
 *
 * @return void
 */
    public function edit($parent_id = null,$id = null)
    {
        if ($this->request->is('post') || $this->request->is('put')) {
            $parent_id = $this->request->data['Crm']['parent_id'];
            if (!$id) {
                $this->{$this->thismodel}->create();
            }
            
            //delete all mapping associated with this CRM entry, then resave the ones that remain
            $this->loadModel('CrmMapping');
            $this->CrmMapping->deleteAll(array('CrmMapping.crm_id' => $id), false);
            
            $this->log($this->request->data);
            
            $save_ok = $this->{$this->thismodel}->saveAssociated($this->request->data, array('deep' => true));
            if ($save_ok) {
                $this->Session->setFlash(__('Your changes have been saved'), 'flash_jsongood');
            } else {
                $this->Session->setFlash(__('Your changes could not be saved, try again later '), 'flash_jsonbad');
            }
            $this->render('/Elements/json_result');
        } 
        else {
            if ($id) {
                $this->set('activity',"Edit CRM Hook");
                //$item = $this->{$this->thismodel}->findById($id);
                $item = $this->{$this->thismodel}->read(null,$id);
                $this->request->data[$this->thismodel] = $item[$this->thismodel];
                $this->set('data',$item);
            }
            else {
                $this->set('activity',"New CRM Hook");
                $this->set('data',null);
            }
            $this->set('thismodel',$this->thismodel);
        	$this->set('crm_types', $this->crm_types);
        	$this->set('crm_methods', $this->crm_methods);
        	$this->set('crm_modules', $this->crm_modules);
            $this->set('item_id',$id);
            $this->set('parent_id', $parent_id);
            $this -> render('edit');
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
    public function delete($id = null)
    {
        if (!$this->isAuthorized('CrmsDelete')) {
              $this->Session->setFlash(__('You are not allowed to make changes to this application'), 'flash_jsonbad');
              $this->render('/Elements/json_result');
              return;
        }
        $this->{$this->thismodel}->unbindModel(
            array(
            'hasMany' => array('ApplicationsEdit'),
            )
        );
        $item = $this->{$this->thismodel}->findById($id);
        if (!$item) {
            $msg = "item does not exist";
            $success = true;
        }
        else {
            $success = 'false';
            $del_ok = $this->{$this->thismodel}->soft_delete($id);
            if ($del_ok) {
                $description = $this->thismodel.' \ ID:'.$id . '\' deleted';
                //function _saveChanges($description, $old_values, $new_values, $account_id, $did_id, $section, $change_type='edit', $data = array()) {
                $this->_saveChanges($description, serialize($item), serialize($item), $item[$this->parentmodel]['account_id'] , $item[$this->thismodel]['parent_id'], $this->thismodel, 'delete', null);
                $success = 'true';
                $msg = "The item has been deleted";
            } else {
                $success = 'false';
                $msg = "Failed to delete the item, try again later";
            }
        }
        $this->set('success', $success);
        $this->set('msg', $msg);
        $this->render('result');
        
    }
    
    
    public function crmaction($crm_id) {
        $data = $this->request->input ( 'json_decode', true) ;
        
        //$reload = $data['xx_retrigger'];
        $reload = false;
        //unset($data['xx_retrigger']);
        //$action_option = $data['xx_action_option'];
        //unset($data['xx_action_option']);
        
        
        $conditions = array($this->thismodel.'.id' => $crm_id, $this->thismodel.'.deleted' => 0);
        
        $params = array('conditions' => $conditions);
        
        $crm = $this->{$this->thismodel}->find('first',$params);
        
        $query = array();
        if ((isset($crm['CrmMapping'])) && sizeof($crm['CrmMapping']) > 0) {
            if (isset($data) and sizeof ($data) > 0) {
                foreach ($crm['CrmMapping'] as $mapping) {
                    if (isset($data[$mapping['caption']])) {
                      $query[$mapping['value']] = $data[$mapping['caption']];
                    }
                }
            }
        }
        
        
        $this->crmcomponent = $this->Components->load($crm['Crm']['type']);
        
        $config = array('url' => $crm['Crm']['url'],'username' => $crm['Crm']['username'], 'password' => $crm['Crm']['password'], 'module' => $crm['Crm']['module']);
        
        $this->crmcomponent->configure($config);
        
        if ($crm['Crm']['method'] == "search") {
            if ($this->crmcomponent->search($query)) {
            }
        }
        else if ($crm['Crm']['method'] == "create") {
            if ($this->crmcomponent->create($query)) {
            }
        }
        else if ($crm['Crm']['method'] == "update") {
            if ($this->crmcomponent->update($query)) {
            }
        }

        
        $results = $this->crmcomponent->data;
        
        //if the user searches again from the result page, signal the page to not re-pull the data from
        //the prompts and overwrite what they changed.
        
        if (!$reload) {
	        $this->set('pull_from_prompts',false);
    	}
    	else {
    		$this->set('pull_from_prompts',true);
    	}
        
        $this->set('data',$data);
        $this->set('item', $crm);
        $this->set('mappings', $crm['CrmMapping']);
        $this->set('results',$results);
        $this->set('crm_id',$crm_id);
        $this->set('thisclass',$this->thisclass);
        $this->set('thismodel',$this->thismodel);
        $this->set('activity',"Search for CRM Contact");
        
    }
    
    
    public function savecontact($parent_id) {
        $data = $this->request->input ( 'json_decode', true) ;
        //$this->log($data);
        
        
        $conditions = array($this->thismodel.'.parent_id' => $parent_id, $this->thismodel.'.deleted' => 0, $this->thismodel.'.method' => 'findcontact');
        
        $params = array('conditions' => $conditions);
        
        $crm = $this->{$this->thismodel}->find('first',$params);
        
        
        $query = array();
        if ((isset($crm['CrmMapping'])) && sizeof($crm['CrmMapping']) > 0) {
            if (isset($data) and sizeof ($data) > 0) {
                $this->log($crm['CrmMapping']);
                foreach ($crm['CrmMapping'] as $mapping) {
                    if (isset($data[$mapping['caption']])) {
                      $query[$mapping['value']] = $data[$mapping['caption']];
                    }
                }
            }
        }
        
        
        
        $this->crmcomponent = $this->Components->load($crm['Crm']['type']);
        $this->crmcomponent->configure($crm['Crm']['url'],$crm['Crm']['username'],$crm['Crm']['password']);
        
        if ($this->crmcomponent->set_entry("Leads",$query)) {
            $this->log('success');
        }
        else {
        	$this->log('fail');
        	$this->log($this->crmcomponent->log);
        }
        
        $results = $this->crmcomponent->data;
        $this->set('item', $crm);
        $this->set('mappings', $crm['CrmMapping']);
        $this->set('results',$results);
        $this->set('parent_id', $parent_id);
        $this->set('thisclass',$this->thisclass);
        $this->set('thismodel',$this->thismodel);
        $this->set('activity',"Search for CRM Contact");
        $this->set('results',"The Contact Information Has Been Saved");
    }
    
    
    
    public function gettickets($parent_id) {
        $data = $this->request->input ( 'json_decode', true) ;
        //$this->log($data);
        
        
        $conditions = array($this->thismodel.'.parent_id' => $parent_id, $this->thismodel.'.deleted' => 0, $this->thismodel.'.method' => 'findcontact');
        
        $params = array('conditions' => $conditions);
        
        $crm = $this->{$this->thismodel}->find('first',$params);
        
        $this->log($data);
        
        $query = array();
        if ((isset($crm['CrmMapping'])) && sizeof($crm['CrmMapping']) > 0) {
            if (isset($data) and sizeof ($data) > 0) {
                $this->log($crm['CrmMapping']);
                foreach ($crm['CrmMapping'] as $mapping) {
                    if (isset($data[$mapping['caption']])) {
                      $query[$mapping['value']] = $data[$mapping['caption']];
                    }
                }
            }
        }
        
        $this->log($query);
        
        
        $this->crmcomponent = $this->Components->load('Sugarcrm');
        
        if ($this->crmcomponent->get_entries("Cases",$query)) {
            
        };
        
        $results = $this->crmcomponent->data;
        
        $this->set('item', $crm);
        $this->set('mappings', $crm['CrmMapping']);
        $this->set('results',$results);
        $this->set('parent_id', $parent_id);
        $this->set('thisclass',$this->thisclass);
        $this->set('thismodel',$this->thismodel);
        $this->set('activity',"Search for CRM Contact");
        
    }
    
    function getoptions($schedule_id = '') {
    	
    	//the section where we need to get the crm options only has the schedule_id available, so use that to look up
    	//the did_id for retrieving the CRM options for that did_id.
        $conditions = array('id' => $schedule_id,'deleted' => 0);
        $params = array('conditions' => $conditions);
		
    	$this->loadModel('Schedule');
    	
    	$schedule = $this->Schedule->find('first',$params);
    	$did_id = $schedule['Schedule']['did_id'];
    	
    	$fields = array("id","name");
        $conditions = array($this->thismodel.'.parent_id' => $did_id, $this->thismodel.'.deleted' => 0);
        $params = array('fields'=> $fields,'conditions' => $conditions);
        
        
        $crm = $this->{$this->thismodel}->find('list',$params);
        
        $this->log(print_r($crm,true));
    	
    	$this->layout = 'ajax';
		$this->set('options', $crm);
    }
    
    function getcrmname($crm_id) {
    	
        $conditions = array($this->thismodel.'.id' => $crm_id);
        
        $params = array('conditions' => $conditions);
        
        $crm = $this->{$this->thismodel}->find('first',$params);
        $this->log(print_r($crm,true));
    	
    	$this->layout = 'ajax';
		$this->set('result', $crm['Crm']['name']);
    }
    
    
    
    
    
    
}
