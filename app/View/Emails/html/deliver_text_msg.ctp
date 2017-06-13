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
<?php
echo "\n";
//echo $local_time. "\n";
if (!empty($caller_id)) echo 'From:' . $this->element('formatPhone2', array('num' => $caller_id)) . "\n";
echo $calltype . "\n";

foreach ($prompts as $p):
  if ($exclude_prompt_titles)
	  echo $p['value']. "\n";
  else
	  echo $p['caption']. ': ' . $p['value']. "\n";
endforeach;

if (sizeof($appts) > 0) {
  echo "\n";

  foreach ($appts as $row):
    foreach ($row as $p) {
      if ($exclude_prompt_titles)
    	  echo $p['value']. "\n";
      else
  	  echo $p['caption'].': ' . $p['value']. "\n";
  	}
  	  echo "\n";
  endforeach;
}
?>