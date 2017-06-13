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
class FaxQueuesController extends AppController {
  function beforeFilter() {
    parent::beforeFilter();
    $this->Auth->allow('process');
  }
 
	public function index($did_id) {
	}
  
  public function process() {
    $faxes = $this->FaxQueue->find('all', array('limit' => 100, 'conditions' => array('fax_processed' => 0)));
    foreach ($faxes as $fax) {
      $tifcontent = $this->_sendfax($fax['FaxQueue']['fax_text'], $fax['FaxQueue']['dst_fax'], $fax['FaxQueue']['src_fax'],$fax['FaxQueue']['account_num'],$fax['FaxQueue']['src_fax'], $fax['FaxQueue']['format']);
      if ($tifcontent){
        $fax['FaxQueue']['fax_tif'] = $tifcontent;
        $fax['FaxQueue']['fax_processed'] = '1';
        $fax['FaxQueue']['fax_processed_ts'] = date('Y-m-d H:i:s');
        $this->FaxQueue->save($fax['FaxQueue']);
      }
      else {
        $fax['FaxQueue']['fax_retry'] = $fax['FaxQueue']['fax_retry'] + 1;
        if ($fax['FaxQueue']['fax_retry'] > 10) {
          $fax['FaxQueue']['fax_processed'] = '2';
          $fax['FaxQueue']['fax_processed_ts'] = date('Y-m-d H:i:s');
        }
        $this->FaxQueue->save($fax['FaxQueue']);
      }
      unset($tifcontent);
    }
    echo 'done'; exit;
  }
  
	function _sendfax($content, $dst_fax, $src_fax, $account, $did_number, $format) {
	  $fax_dir = Configure::read('fax_directory');
	  $faxnum = preg_replace('/[^0-9]/','', $dst_fax);
	  $content = str_replace('"', '\"', $content);
    $fname1 = tempnam($fax_dir, 'fax');	  
    $fname2 = tempnam($fax_dir, 'fax');	  
    $fname1ps = $fname1. ".ps";
    $fname2tf = $fname2. ".tiff";
    
    // user enscript to convert to .ps if enscript fails, then clean up and return false;
    if ($format == 'html') {
	    exec('echo "'.$content.'" | html2ps >'.$fname1ps . ' 2>&1', $enscript_output, $returnval);
    }
    else {
	    exec('echo "'.$content.'" | enscript -p '.$fname1ps . ' 2>&1', $enscript_output, $returnval);
	  }
	  $num_pages = '0';
    if ($returnval !== 0) {
      @unlink($fname1);
      @unlink($fname2);
      @unlink($fname1ps);
      return false;
    }
    
    //html2ps doesn't output page number like enscript
    /*if ($format != 'html') {
      $matches[1] = '';
      //parse enscript output for number of pages
      foreach ($enscript_output as $line) {
        if (strpos($line, 'pages') !== false) {
          if (preg_match('/^\[\s*([0-9]{1,}) pages/', $line, $matches)) {
            if ($matches[1]) {
              $num_pages = $matches[1];
              fb("num of pages $num_pages");
            }
          }
        }
      }
    }*/
    
    //now convert postscript to tiff file
	  exec('gs -dNOPAUSE -dBATCH -sDEVICE=tiffg4 -sPAPERSIZE=letter -g1728x2150 -sOutputFile='.$fname2tf.' ' . $fname1ps, $output, $returnval);
	  //echo 'gs -dNOPAUSE -dBATCH -sDEVICE=tiffg4 -sPAPERSIZE=letter -g1728x2150 -sOutputFile='.$fname2tf.' ' . $fname1ps;
	  // if ghostscript conversion fails then clean up and return false;
	  if ($returnval !== 0) {
      @unlink($fname1);
      @unlink($fname2);
      @unlink($fname1ps);
      @unlink($fname2tf);
	    return false;
	  }
	  unset($output);
    
    // get the number of pages using tiffinfo and filtering the output 
    exec("tiffinfo $fname2tf |  grep \"Page Number\"", $output, $returnval);
    if ($returnval === 0) {
      $num_pages = sizeof($output);
    }

    $tifcontent = file_get_contents($fname2tf);
    if ($tifcontent) {
      $this->loadModel('Fax');
      $data['Fax']['src_fax'] = '6783181302';
      $data['Fax']['dst_fax'] = preg_replace('/[^0-9]/', '', $faxnum);
      $data['Fax']['fax_sent'] = 'N';
      $data['Fax']['fax'] = $tifcontent;
//      $data['Fax']['fax_size'] = strlen($tifcontent);
      $data['Fax']['fax_size'] = '0';

      $data['Fax']['fax_pages'] = $num_pages;
      $data['Fax']['fax_attempts'] = '0';
      $data['Fax']['account_no'] = ''; // leave blank, filled in by asterisk fax processor
      $data['Fax']['conversion_start'] = '';
      $data['Fax']['conversion_end'] = '';
      $this->Fax->create();
      $this->Fax->save($data['Fax']);      
      unlink($fname1);
      unlink($fname2);
      unlink($fname1ps);
      unlink($fname2tf);
      return $tifcontent;
  	}
  	else {
      unlink($fname1);
      unlink($fname2);
  	  return false;
  	}
	  //exec('gs -dNOPAUSE -dBATCH -sDEVICE=tiffg4 -sPAPERSIZE=letter -g1728x2150 -sOutputFile=/var/www/faxes/test.tiff /var/www/faxes/test.ps', $output, $returnval);
	  //fb($returnval);
	  //fb($output);
	  /*
		try {
			App::uses('CakeEmail', 'Network/Email');		
			$Email = new CakeEmail();
			$Email->config('default');
      $Email->to($faxnum.'@fax.voicenation.com');
			$Email->emailFormat('text');
			$Email->subject($subject);
			$Email->send($content);
			return true;
		}	catch (Exception $e) {
			return false;
		}*/
	}	  
}
