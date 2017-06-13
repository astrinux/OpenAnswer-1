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





?>

<script>




function showDel(t) {
    $(t).find('.trash').show();
}

function hideDel(t) {
    $(t).find('.trash').hide();
}
function showDel(t) {
    $(t).find('.trash').show();
}

function hideDel(t) {
    $(t).find('.trash').hide();
}


function itemLoad(parent_id, item_id) {
    var url = '';
    if (item_id == null) {
        url = '/<?php echo $thisclass ?>/edit/' + parent_id;
    }
    else {
        url = '/<?php echo $thisclass ?>/edit/' + parent_id + '/' + item_id;
    }
     $.ajax({
            url: url,
            dataType: 'html',
            type: 'GET',
          }).done(function(data) {
                    $('#did-detail').html(data);
                    didLayout.center.children.layout1.open('east');
          });
}


function itemDelete(id) {
    var url = '/<?php echo $thisclass ?>/delete/' + id;
    $.ajax({
        url: url,
        dataType: 'json',
        type: 'GET',
    }).done(function(data) {
        if (data.success) {
            loadPagePost(null, '/<?php echo $thisclass ?>/index/'+<?php echo $parent_id ?>, 'did-content', 'target=did-content&detail=did-detail', null);
        }
        else {
            alert(data.msg);
        }
    });
}


</script>

<?php
$this->Paginator->options(array(
    'update' => '#did-content',
    'evalScripts' => true
));
?>

<div>
    <div class="index">
        <div class=" panel-content tblheader">
            <h2><i class="fa <?php echo $global_options['icons']['setup']; ?>"></i> <?php echo __(' CRM Hooks'); ?></h2>
            <b>Search:</b>
            <input type="text" size="20" value="" onkeyup="searchFilter('<?php echo $thisclass ?>table', this.value)">
            <a href="#" onclick="itemLoad(<?php echo $parent_id ?>,null); return false;">Add CRM Hook</a>
            <br><br>
<?php
    echo $this->Element('paging');
?>
        </div>            
        <table cellpadding="0" cellspacing="0" class="gentbl" id="'<?php echo $thisclass ?>table'" width="100%">
            <thead>
                <tr>
                    <th align="left" width="10"><?php echo $this->Paginator->sort('id'); ?></th>
                    <th align="left" width="200"><?php echo $this->Paginator->sort('name'); ?></th>
                    <th align="left" width="200"><?php echo $this->Paginator->sort('type'); ?></th>
                    <th align="left" width="200"><?php echo $this->Paginator->sort('module'); ?></th>
                    <th align="left" width="200"><?php echo $this->Paginator->sort('method'); ?></th>
                    <th class="actions" filter="false"><?php echo __('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($items as $item) : ?>
<?php
 $item_id = $item[$thismodel]['id'];
 $item_parent_id = $item[$thismodel]['parent_id'];
 $item_name = $item[$thismodel]['name'];
 $item_type = $item[$thismodel]['type'];
 $item_module = $item[$thismodel]['module'];
 $item_method = $item[$thismodel]['method'];
?>
                <tr>
                    <td onclick="itemLoad('<?php echo $parent_id; ?>','<?php echo $item_id; ?>', 'did-detail'); return false;">
                        <?php echo $item_id; ?>
                    </td>
                    <td onclick="itemLoad('<?php echo $parent_id; ?>','<?php echo $item_id; ?>', 'did-detail'); return false;">
                        <?php echo $item_name;?>
                    </td>
                    <td onclick="itemLoad('<?php echo $parent_id; ?>','<?php echo $item_id; ?>', 'did-detail'); return false;">
                        <?php echo $item_type;?>
                    </td>
                    <td onclick="itemLoad('<?php echo $parent_id; ?>','<?php echo $item_id; ?>', 'did-detail'); return false;">
                        <?php echo $item_module;?>
                    </td>
                    <td onclick="itemLoad('<?php echo $parent_id; ?>','<?php echo $item_id; ?>', 'did-detail'); return false;">
                        <?php echo $item_method;?>
                    </td>

                    <td class="actions">
                        <a href="#" onclick="user_confirm('Are you sure you want to delete this item?', function() { itemDelete('<?php echo $item_id; ?>'); return false;});">
                            <img title="delete" alt="delete" src="/img/delete.png" width="16" height="16">
                        </a>
                        <a href="#" onclick="itemLoad('<?php echo $parent_id; ?>','<?php echo $item_id; ?>', 'did-detail'); return false;">
                            <img title="edit" alt="edit" src="/img/edit.png" width="16" height="16">
                        </a>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>



