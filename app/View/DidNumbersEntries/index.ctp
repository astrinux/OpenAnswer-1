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
        <?php echo $this->Session->flash(); ?>
        <?php
        
        foreach ($all_numbers as $k => $num) {
            echo $this->element('formatPhone', array('num' =>  $num['DidNumbersEntry']['number']));
            if (!empty( $num['DidNumbersEntry']['alias'])) {
                echo '/ ' . $this->element('formatPhone', array('num' =>  $num['DidNumbersEntry']['alias']));
            }
            echo ' <a href="" onclick="deleteNumber('.$num['DidNumbersEntry']['id'].'); return false;"><img title="delete" alt="x" src="/img/delete.png" width="16" height="16"></a><br>';
        }
        ?>