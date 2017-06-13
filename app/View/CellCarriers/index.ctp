
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
	#cellcarriers .edited {border:1px solid red;}
</style>

<div id="cellcarriers">
	<h2><?php echo 'Cell Carriers'; ?></h2>
	<b>Search:</b> <input type="text" size="20" value="" onkeyup="searchFilter('carriertbl', this.value)">
<br><br>
	<table cellpadding="0" cellspacing="0" class="gentbl" id="carrieradd" width="100%">
	<thead>
	<tr>
			<th align="left" title="Cell carrier">Carrier</th>
			<th align="left" title="Mail-to-SMS gateway">Address</th>
			<th align="left" title="Digit(s) dialed prior to dialing cell number">Prefix</th>
			<th align="left" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tr>
		<td width="300" align="center"><input type="hidden" name="data[CellCarrier][id]" value="">
		<input type="text" name="data[CellCarrier][name]" size="30" value="" placeholder="Carrier">
		</td>
		<td width="300" align="center"><input type="text" name="data[CellCarrier][addr]" size="30" value=""  placeholder="Address"></td>
		<td width="80" align="center"><input type="text" name="data[CellCarrier][prefix]" size="2" value=""  placeholder="Prefix"></td>
		<td class="actions">
			<input type="submit" value="Add"  onclick="addCarrier(this);">
		</td>
	</tr>	
  </table>
	<table cellpadding="0" cellspacing="0" class="gentbl" id="carriertbl" width="100%">
	<tbody>
	<?php
	foreach ($carriers as $c): ?>
	<tr>
		<td width="300" align="center"><input type="hidden" name="data[CellCarrier][id]" value="<?php echo $c['CellCarrier']['id']; ?>">
		<input type="text" name="data[CellCarrier][name]" size="30" value="<?php echo $c['CellCarrier']['name']; ?>" onchange="saveCarrier(this);"><span class="is_hidden"><?php echo $c['CellCarrier']['name']; ?></span>
		</td>
		<td width="300" align="center"><input type="text" name="data[CellCarrier][addr]" size="30" value="<?php echo $c['CellCarrier']['addr']; ?>" onchange="saveCarrier(this);"><span class="is_hidden"><?php echo $c['CellCarrier']['addr']; ?></span></td>
		<td width="80" align="center"><input type="text" name="data[CellCarrier][prefix]" size="2" value="<?php echo $c['CellCarrier']['prefix']; ?>" onchange="saveCarrier(this);"><span class="is_hidden"><?php echo $c['CellCarrier']['prefix']; ?></span></td>
		<td class="actions"><a href="#" onclick="user_confirm('Are you sure you want to delete this entry?', function() {deleteCarrier('<?php echo $c['CellCarrier']['id']; ?>');}); return false;">delete</a>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
</div>

<script>
function saveCarrier(t) {
	var $elements = $(t).parents('tr').find('input');
	var data = $elements.serialize();
	$elements.addClass('edited');
	postJson('/CellCarriers/save', data, function() {
		$elements.removeClass('edited');
	}) 
	
}
function addCarrier(t) {
	var $elements = $(t).parents('tr').find('input');
	var data = $elements.serialize();
	$elements.addClass('edited');
	postJson('/CellCarriers/save', data, function() {
		$('#dialogWin').load('/CellCarriers');
	}) 
	
}

function deleteCarrier(id) {
	postJson('/CellCarriers/delete/' + id, null, function() {
		$('#dialogWin').load('/CellCarriers');
	}) 
	
}

</script>
