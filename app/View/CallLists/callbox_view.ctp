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

foreach ($employees as $e) {
  if (isset($e['id'])) {
    if (!empty($e['gender'])) $class = strtolower($e['gender']);
    else $class = '';
    
    echo '<p><span class="'.$class.'">&nbsp;&nbsp;</span>&nbsp;<a href="#" onclick="$(\'#show_emp_picker\').val(\''.$e['id'].'\'); $(\'#show_emp_picker\').trigger(\'change\'); $(this).html($(this).html() + \'&#10003\'); return false;">' .$e['name']. ' </a></p>';
  }
}
?>
