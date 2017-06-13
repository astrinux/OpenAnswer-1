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
<table cellpadding="4" cellspacing="0" border="0" width="816">
  <tr>
    <td>&nbsp;</td><td></td>
  </tr>
  <tr>
    <td align="right">TO:</td>
    <td><?php echo $recipient; ?></td>
  </tr>
  <tr>
    <td align="right">SENT:</td>
    <td><?php echo $now_time; ?></td>
  </tr>
  <tr>
    <td align="right">SUBJECT:</td>
    <td>AUTOMATED MAIL DELIVERY</td>
  </tr>

  <tr class="uline">
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
  
  <?php
  foreach ($messages as $m) {
    ?>
<table cellpadding="4" cellspacing="0" border="0" width="816">
  <tr>
    <td align="right" width="250">Message #:</td>
    <td><?php echo $m['Message']['id']; ?></td>
  </tr>  
  <tr>
    <td align="right" width="250">Taken at:</td>
    <td><?php echo $m['Message']['created']; ?></td>
  </tr>    
  <tr>
    <td align="right" width="250">FOR:</td>
    <td><?php echo $m['Message']['calltype']; ?></td>
  </tr>  
  <tr>
    <td align="right">Delivered to:</td>
    <td>  
  <?php 
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
        
        echo $d['delivered_time'] . ' - ' . $d['delivery_name'] . " - $method<br>";
      }  	
  	
    /*foreach ($m['MessagesDelivery'] as $d) {
      echo $d['delivered_time'] . ': ';
      $contacts = explode(',', $d['delivery_contact']);
      
      
      if ($d['delivery_method'] == CONTACT_FAX || $d['delivery_method'] == CONTACT_PHONE || $d['delivery_method'] == CONTACT_TEXT) {
        foreach($contacts as &$c) {
          $c = $this->element('formatPhone', array('num' =>$c));
        }
      }
      echo str_replace(array(';', ','), array(', ', ', '), $d['delivery_contact']) . "<br>";
    }*/
  }
  else {
    echo $now_time . ': ' . $recipient;
  }
  ?>
    </td>
  </tr>
<?php
    foreach ($m['MessagesPrompt'] as $p):
    	echo '<tr><td align="right">'.$p['caption'].':</td><td> ' . $p['value']. "</td></tr>\r\n";
    endforeach;
    echo "</table><br><br>\r\n";
  }
  
?>
