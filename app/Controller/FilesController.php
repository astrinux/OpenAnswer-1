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
/**
 * Files Controller
 *
 * @property File $File
 */
class FilesController extends AppController {
  public $paginate;
  public $uses = array('DidFile');

/**
 * index method
 *
 * @return void
 */
 
 
	public function index($did_id) {
	  $this->set('did_id', $did_id);
	  if (!trim($did_id) && !is_numeric($did_id)) {
	    $this->set('files', array());
	  }
	  else {
	    $this->paginate['conditions'] = array('DidFile.did_id' => $did_id, 'DidFile.deleted' => '0');
		  $this->DidFile->recursive = 0;
		  $this->set('files', $this->paginate());
		}
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->DidFile->id = $id;
		if (!$this->DidFile->exists()) {
			throw new NotFoundException(__('Invalid file'));
		}
		$file = $this->DidFile->read(null, $id);

    /*$this->response->body($f['DidFile']['file_content']);
    $this->response->type(array($f['DidFile']['file_extension'] => $f['DidFile']['file_type']));

    //Optionally force file download
    $this->response->download($f['DidFile']['file_name']);

    //Return response object to prevent controller from trying to render a view
    return $this->response;*/
    header("Content-Type: ". $file['DidFile']['file_type']); 
    header("Cache-Control: no-cache, must-revalidate");
    //header("Content-type: ".$file['DidFile']['file_type']);
    header('Content-Disposition: inline; filename='.$file['DidFile']['file_name']);
    //header("Content-Disposition: attachment; filename=$fileName");
    echo $file['DidFile']['file_content'];    
    exit;
	}

/**
 * add method
 *
 * @return void
 */
	public function add($did_id) {
	  $this->set('did_id', $did_id);

	}

  public function upload() {
		if ($this->request->is('post') ) {
			$this->loadModel('DidNumber');
			$did = $this->DidNumber->findById($_POST['did_id']);
      if($_FILES && $_FILES['file']['name']){
        $file_info = pathinfo($_FILES['file']['name']);
           
        //make sure the file has a valid file extension
        if (!in_array(strtolower($file_info['extension']), $this->acceptable_extensions))
          throw new NotFoundException('Extension not allowed');           
          
        $fileName = $_FILES['file']['name'];
        $tmpName  = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        // Slurp the content of the file into a variable
                       
        $fp = fopen($tmpName, 'rb');
        $content = fread($fp, filesize($tmpName));
        fclose($fp);
  		  
  		  $data['DidFile']['did_id'] = $_POST['did_id'];
  		  $data['DidFile']['file_name'] = $fileName;
  		  $data['DidFile']['file_type'] = $fileType;
  		  $data['DidFile']['file_size'] = $fileSize;
  		  $data['DidFile']['file_extension'] = strtolower($file_info['extension']);
  		  $data['DidFile']['file_content'] = $content;
  		  
  			$this->DidFile->create();
  			if ($this->DidFile->save($data)) {
					$id = $this->DidFile->getInsertID();
  				
  				//$this->Session->setFlash(__('The file has been saved'), 'flash_jsongood');
	        $this->clearDidCache($_POST['did_id']);		
	        
	        $e['user_id'] = AuthComponent::user('id');
	        $e['user_username'] = AuthComponent::user('username');
	        $e['old_values'] = '';
	        $e['new_values'] = $fileName;
	        $e['file_id'] = $id;
	        $e['account_id'] = $did['DidNumber']['account_id'];
	        $e['did_id'] = $_POST['did_id'];
	        $e['section'] = 'files';
	        $e['description'] = 'File uploaded: ' . $fileName . ' (ID: '.$id.')';
	        $e['change_type'] = 'add';
          $this->loadModel('FilesEdit');     
      
	        $this->FilesEdit->create();
	        $this->FilesEdit->save($e);	        
  				
  			} else {
          //$this->header('HTTP/1.1 403 Forbidden');
          throw new NotFoundException('Cannot upload file');           
  				//$this->Session->setFlash(__('The file could not be saved. Please, try again.'), 'flash_jsonbad');
  			}          
      }
 

      //$this->render('/Element/json_result');			
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
	public function delete($id = null, $did_id) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$old = $this->DidFile->findById($id);
		$this->loadModel('DidNumber');
		$did = $this->DidNumber->findById($did_id);
		unset($old['file_content']);
		$data['DidFile']['id'] = $id;
		$data['DidFile']['deleted'] = 1;
		$data['DidFile']['deleted_ts'] = date('Y-m-d H:i:s');
		
		if ($this->DidFile->save($data['DidFile'])) {
			$this->Session->setFlash(__('File deleted'), 'flash_jsongood');			
      $this->clearDidCache($did_id);		
      $e['user_id'] = AuthComponent::user('id');
      $e['user_username'] = AuthComponent::user('username');
      $e['old_values'] = serialize($old);
      $e['new_values'] = '';
      $e['file_id'] = $id;
      $e['account_id'] = $did['DidNumber']['account_id'];
      $e['did_id'] = $did_id;
      $e['section'] = 'files';
      $e['description'] = 'File deleted: ' . $old['DidFile']['file_name'] . ' (ID: '.$old['DidFile']['id'].')';
      $e['change_type'] = 'delete'; 
      $this->loadModel('FilesEdit');     
      $this->FilesEdit->create();
      $this->FilesEdit->save($e);				
		}
		else {
		  $this->Session->setFlash(__('File was not deleted'), 'flash_jsonbad');
		}
		$this->render('/Elements/json_result');
	}
}
