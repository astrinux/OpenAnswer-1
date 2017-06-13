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
table * {font-size:12px;font-family: Verdana;}
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
  <tr>
    <td align="right">Account Number:</td>
    <td><?php echo $account_num . ' - ' . $company; ?></td>
  </tr>  

  <tr class="uline">
    <td colspan="2">&nbsp;</td>
  </tr>
    
  <tr class="uline">
    <td></td><td><b>NO MESSAGES</b></td>
  </tr>
</table>
