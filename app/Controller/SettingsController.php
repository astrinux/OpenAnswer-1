<?php
App::uses('AppController', 'Controller');

/**
 * Settings Controller
 *
 */
class SettingsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $settings = $this->paginate();

        $this->set(compact('settings'));
        $this->set('_serialize', array('settings'));
    }

    /**
     * View method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $setting = $this->Settings->findById($id);

        $this->set('setting', $setting);
        $this->set('_serialize', array('setting'));
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->_serializeArray();
            
            unset($this->data['Setting']['value_array']);
            if ($this->Setting->save($this->data)) {
                $this->Session->setFlash(__('Your changes have been saved'), 'flash_jsongood');

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The setting cannot be saved '), 'flash_jsonbad');
                $this->render('/Elements/json_result');
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->_serializeArray();
            unset($this->request->data['Setting']['value_array']);
            
            if ($this->Setting->save($this->request->data)) {
                $this->Session->setFlash(__('Your changes have been saved'), 'flash_jsongood');

                return $this->redirect(array('action' => 'index'));
            } else {

                
                $this->Flash->error(__('The setting could not be saved. Please, try again.'));
            }
        }
        else {
            $this->request->data = $this->Setting->findById($id);
            $vals = $this->request->data['Setting']['value'];
            $this->request->data['Setting']['value_array'] = array();
            if (is_array($vals)) {            
                foreach ($vals as $k => $v) {
                  if ($k !== $v) {
                    $this->request->data['Setting']['value_array'][] = $k . '|' . $v;
                    
                  }
                  else $this->request->data['Setting']['value_array'][] = $v;
                }
            }
            else $this->request->data['Setting']['value_array'][] = $vals;
        }

    }


    function _serializeArray() {
        $vals = array();
        if (sizeof($this->request->data['Setting']['value_array']) > 1) {
            
            foreach ($this->request->data['Setting']['value_array'] as $k => $val) {
                if (empty($val)) continue;
                $temp = explode('|', $val);
                if (sizeof($temp) > 1) {
                  $vals[$temp[0]] = $temp[1];
                 
                }
                else $vals[$val] = $val;
              
              
            }
            
            $this->request->data['Setting']['value'] = serialize($vals);
        }
        else {
            $this->request->data['Setting']['value'] = serialize($this->request->data['Setting']['value_array'][0]);
        }        
    }
    /**
     * Delete method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $setting = $this->Setting->findById($id);
        if ($setting) {
            if ($this->Setting->delete($id)) {
  		          $this->Session->setFlash(__('Setting was successfully deleted'));
            } else {
  		          $this->Session->setFlash(__('Setting could not be deleted'));
            }
        }

        return $this->redirect(array('action' => 'index'));
    }
    
    function createOption() {
      exit;
        $name = 'options_timezones';
        $the_array = array ('America/New_York' => 'Eastern Time', 'EST' => 'Eastern Time (no DST)', 'America/Chicago' => 'Central Time', 'America/Boise' => 'Mountain Time', 'MST' => 'Mountain Time (no DST)','America/Los_Angeles' => 'Pacific Time', 'Pacific/Honolulu' => 'Hawaii', 'America/Anchorage' => 'Alaska', 'Europe/Berlin' => 'Central Europe', 'Australia/Sydney' => 'Australia', 'Asia/Hong_Kong' => 'China', 'Pacific/Guam' => 'Guam');
        $data['Setting']['name'] = $name;
        $data['Setting']['value'] = serialize($the_array);
        $this->Setting->create();
        $this->Setting->save($data);
        echo 'done'; exit;
    }
}
