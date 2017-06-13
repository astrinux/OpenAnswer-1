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
<div class="panel-content tblheader searchbox fg_grey">
<h2><i class="fa <?php echo $global_options['icons']['setup']; ?>"></i> Documents</h2> 
<a href="" onclick="loadPage(this, '/Files/add/'  + $('#find_did').val(), 'did-detail'); didLayout.center.children.layout1.open('east'); return false;"><i class="fa fa-plus"></i> add file</a>
</div>
<table cellpadding="2" cellspacing="0" class="gentbl" width="100%">
<tr><th width="200" align="left">Name</th><th width="80" >Size</th><th width="180">Created</th><th>&nbsp;</th></tr>
<?php
foreach ($files as $f) {
  echo '<tr>';
  echo '<td>'.$f['DidFile']['file_name'] . '</td>';
  echo '<td align="center">'.number_format($f['DidFile']['file_size']/1024, 1, '.', ',') . ' KB</td>';
  echo '<td>'.$f['DidFile']['created_f'] . '</td>';
  echo '<td><a href="/Files/view/'.$f['DidFile']['id'].'" target="_blank"><img title="view" alt="view" src="/img/view.png" width="14" height="14"></a><a href="#" onclick="deleteFile('.$f['DidFile']['id'].', '.$f['DidFile']['did_id'].'); return false;"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a></td></tr>';
}
?>

</table>
</div>
<script>
function deleteFile(id, did_id) {
				var url = '/Files/delete/' + id + '/' + did_id;
		    $.ajax({
		        url: url,
		        type: 'post',
		        dataType: 'json'
		    }).done(function(jsondata) {
							if (jsondata.success) {
							  loadPage(this, 'Files/index/<?php echo $did_id; ?>', 'did-content');
							}
							else alert(jsondata.msg);
		        });	  
}
</script>

