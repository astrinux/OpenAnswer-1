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
class CellCarriersController extends AppController {
  public $paginate;
  
/**
 * index method
 *
 * @return void
 */
	public function index() {
	  $this->paginate['limit'] = 1000;
	  $this->paginate['order'] = array('name' => 'asc');
		$this->CellCarrier->recursive = 0;
		$this->set('carriers', $this->paginate());
	}


/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
      $this->CellCarrier->create();
      $save_ok = $this->CellCarrier->save($this->request->data);

  		if (!$save_ok) {
  				$this->Session->setFlash(__('Failed adding carrier.'), 'flash_jsonbad');
  		}
  		else {
  				$this->Session->setFlash(__('Carrier was added.'), 'flash_jsongood');
  		}
  		$this->render('/Elements/json_result');
		}
	}
	
	public function save() {
		if ($this->request->is('post')) {
      $save_ok = $this->CellCarrier->save($this->request->data);
  		if (!$save_ok) {
  				$this->Session->setFlash(__('Failed saving carrier.'), 'flash_jsonbad');
  		}
  		else {
  				$this->Session->setFlash(__('Carrier changes were saved.'), 'flash_jsongood');
  		}
		}		
  		$this->render('/Elements/json_result');
		
	}

	public function delete($id = null) {

		$this->CellCarrier->id = $id;
		if (!$this->CellCarrier->exists()) {
			$this->Session->setFlash(__('Carrier was not deleted'), 'flash_jsonbad');
  		$this->render('/Elements/json_result');
			exit;
		}
		
		if ($this->CellCarrier->delete()) {
			$this->Session->setFlash(__('Carrier deleted'), 'flash_jsongood');
		}
		else $this->Session->setFlash(__('Carrier was not deleted'), 'flash_jsonbad');
  	$this->render('/Elements/json_result');
	}
}
