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
	$class1 = 'ct';
	$class2 = 'ct';
	$class3 = 'ct';
	$class4 = 'ct';
	$class5 = 'ct';
	
	if ($section == '1') $class1 = 'ct_sel';
	else if ($section == '2') $class2 = 'ct_sel';
	else if ($section == '3') $class3 = 'ct_sel';
	else if ($section == '4') $class4 = 'ct_sel';
	else if ($section == '5') $class5 = 'ct_sel';

	?>
	<ul class="leftnav">
		<li class="<?php echo $class1; ?> ajaxlink" href="/Clients/edit/<?php echo $id;?>">Billing Info</li>
		<li class="<?php echo $class2; ?> ajaxlink" href="/Clients/company/<?php echo $id;?>">Company Info</li>
		<li class="<?php echo $class3; ?> ajaxlink" href="/Calltypes/view/<?php echo $id;?>">Call Types</li>
		<li class="<?php echo $class4; ?> ajaxlink" href="/Employees/">Employees</li>
		<li class="<?php echo $class5; ?> ajaxlink" href="/Messages/index/<?php echo $id;?>">Messages</li>
	</ul>
	
	<script type="text/javascript">
	attachNavListener();
	</script>