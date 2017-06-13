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
<style>
tr.uline td {border-bottom: 1px dashed #ccc;}
table * {font-size:12px; font-family: Verdana;}
</style>

<table cellpadding="4" cellspacing="0" border="0">
  <tr>
    <td width="200">&nbsp;</td><td></td>
  </tr>
  <tr>
    <td align="right">Date:</td>
    <td><?php echo $now_time; ?></td>
  </tr>
  <tr>
    <td align="right">Subject:</td>
    <td>AUTOMATED MAIL DELIVERY</td>
  </tr>
  <?php if (!empty($account_num)) {
    ?>
  <tr>
    <td align="right">Account Number:</td>
    <td><?php echo $account_num . ' - ' . $company; ?></td>
  </tr>  
  <?php 
  } ?>
  <tr>
    <td align="right">Num of Messages:</td>
    <td><?php echo sizeof($messages); ?></td>
  </tr> 
  <tr class="uline">
    <td colspan="2">&nbsp;</td>
  </tr>
  
  <?php
  foreach ($messages as $m) {
    ?>
  <tr>
    <td align="right">Message ID :</td>
    <td><?php echo $m['Message']['id']; ?></td>
  </tr>      
  <tr>
    <td align="right">Taken at :</td>
    <td><?php echo $m['Message']['created']; ?></td>
  </tr>    
  <tr>
    <td align="right">FOR :</td>
    <td><?php echo $m['Message']['calltype']; ?></td>
  </tr>  
  <?php
  if ($include_cid) {
    ?>
  <tr>
    <td align="right">FROM :</td>
    <td><?php echo $this->element('formatPhone2', array('num' => $m['CallLog']['cid_number'])); ?></td>
  </tr>  
    <?php
  }
  ?>
  <tr>
    <td align="right">DELIVERED TO :</td>  
  <?php
  
  if (isset($m['MessagesDelivery']) && sizeof($m['MessagesDelivery']) > 0) {
    ?>

    <td>
    <?php 
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
    ?>
    </td>
<?php
}
else echo '<td>None</td>';
?>
  </tr>

<?php
  if (isset($m['MessagesPrompt'])) {
    foreach ($m['MessagesPrompt'] as $p):
    	echo '<tr><td align="right">'.$p['caption'].':</td><td>' . $p['value']. "</td></tr>";
    endforeach;
  }
    
  if (sizeof($m['appointments'] > 0)) {
    $appts = $m['appointments'];
           if (sizeof($appts['active']) > 0) {
?>
  <tr>
    <td align="right"></td>
    <td><br><br><b>Appointments</b></td>
  </tr>  
<?php    
            
              foreach ($appts['active'] as $row):
                foreach ($row as $p) {
            	    echo '<tr><td align="right">'.$p['caption'].':</td><td> ' . $p['value']. "</td></tr> \t\r\n";
              	}
           	    echo '<tr><td align="right">&nbsp;</td><td>&nbsp;</td></tr>'." \t\r\n";
              endforeach;
              foreach ($appts['deleted'] as $row):
            	  echo '<tr class="cancelled"><td align="right">&nbsp;</td><td><span class="cancelled">CANCELLED</span></td></tr>'." \t\r\n";
                foreach ($row as $p) {
            	    echo '<tr class="cancelled"><td align="right">'.$p['caption'].':</td><td> ' . $p['value']. "</td></tr> \t\r\n";
              	}
              endforeach;
         	    echo '<tr><td align="right">&nbsp;</td><td>&nbsp;</td></tr>'." \t\r\n";
         	  }
  }
      
    echo '<tr class="uline"><td colspan="2">&nbsp;</td></tr>';
  }
?>
</table>
