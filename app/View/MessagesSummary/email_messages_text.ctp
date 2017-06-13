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
?>
SENT: <?php echo $now_time. "\t\r\n"; ?>
<?php if (!empty($company)) echo 'TO: ' . $company . "\t\r\n"; ?>
<?php if (!empty($account_num)) echo 'ACCOUNT: ' . $account_num . "\t\r\n"; ?>
SUBJECT: AUTOMATED MAIL DELIVERY <?php echo "\t\r\n"; ?>
NUM OF MESSAGES: <?php echo sizeof($messages). "\t\r\n";  ?>
  
<?php
  foreach ($messages as $m) {
?>
Message ID : <?php echo $m['Message']['id']. "\t\r\n"; ?>
Taken at : <?php echo $m['Message']['created']. "\t\r\n"; ?>
FOR : <?php echo $m['Message']['calltype']. "\t\r\n"; ?>
<?php
if ($include_cid) {
?>
FROM: <?php echo $this->element('formatPhone2', array('num' => $m['CallLog']['cid_number'])). "\t\r\n"; ?>
<?php
}

echo 'DELIVERED TO: ';
  
  if (isset($m['MessagesDelivery']) && sizeof($m['MessagesDelivery']) > 0) {
    foreach ($m['MessagesDelivery'] as $d) {
  		if (!empty($d['delivery_method'])) {
  		  if (isset($global_options['contact_types'][$d['delivery_method']])) $method = $global_options['contact_types'][$d['delivery_method']];
  		  else $method = str_replace(array(';', ','), array(', ', ', '), $d['delivery_contact_label']);
  		}
  		else {
        if ($d['delivered_by_userid'] == '0') {
  		    $method = 'Message Summary';
  		  } 		  
  		  else $method = str_replace(array(';', ','), array(', ', ', '),  $d['delivery_contact_label']);
  		}      
      
      echo $d['delivered_time'] . ' - ' . $d['delivery_name'] . " - $method \t\r\n";
    }
  }
  else echo 'None' . "\t\r\n";
  
    foreach ($m['MessagesPrompt'] as $p):
    	echo $p['caption'].': ' . $p['value']. "\t\r\n";
    endforeach;
    echo "\t\r\n";

  if (sizeof($m['appointments'] > 0)) {
    $appts = $m['appointments'];
           if (sizeof($appts['active']) > 0) {
            echo "\t\r\n\t\r\n";
            echo "Appointments\t\r\n";
            
              foreach ($appts['active'] as $row):
                foreach ($row as $p) {
            	    echo $p['caption'].':' . $p['value']. "\t\r\n";
              	}
           	    echo " \t\r\n";
              endforeach;
         	  }
  }

}
?>