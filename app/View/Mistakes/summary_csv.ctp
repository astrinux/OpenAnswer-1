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

	$rows = array();
	
	$row1 = array("Message Date","Recipient","Category","Description");
	$row2 = array("","","","");
	
	$rows[] = $row1;
	$rows[] = $row2;
	
	
	foreach ($Mistakes as $Mistake) {
		$element1 ='"'.$Mistake['0']['created_f'].'"';
		$element2 ='"'.$Mistake['Mistake']['recipient_username'].'"';
		$element3 ='"'.$Mistake['Mistake']['category'].'"';
		$element4 ='"'.str_replace('"','',$Mistake['Mistake']['description']).'"';


		$rows[] = array($element1,$element2,$element3,$element4);
	}
header("Content-type: text/csv"); 
header("Content-Disposition: attachment; filename=mistakes.csv");
$csv = array();
foreach ($rows as $row) {
   $csv[]  = implode(',', $row);
}

echo implode("\r\n", $csv);
?>