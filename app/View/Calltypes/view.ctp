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
.Calltypes table { border-bottom:1px solid #888 !important;}
.no {color: red;}
.yes {color: green;}
.data a {color: #999; font-style:italic;}
</style>


<div class="Calltypes index" id="ctlist">
    <div class="panel-content tblheader fg_grey">
    <h2><i class="fa <?php echo $global_options['icons']['setup']; ?>"></i> <?php echo __('Call Types') . '<i> - ' . $timezone . '</i>';  ?></h2>
    <?php if ($this->Permissions->isAuthorized('CalltypesAdd',$permissions)) { ?> 
    <a href="#" data-did="<?php echo $did_id; ?>" onclick="loadPage(null, '/Calltypes/add/<?php echo $did_id; ?>', 'did-detail'); didLayout.center.children.layout1.open('east');return false;"><i class="fa fa-plus"></i> Add Call Type</a>&nbsp;&nbsp;&nbsp;&nbsp;<button id="save_order" value="Save Order" class="is_hidden" onclick="saveOrder(); return false;">Save Order</button>
    <a href="#" onclick="loadPage(null, '/Calltypes/template/<?php echo $did_id; ?>', 'did-detail'); didLayout.center.children.layout1.open('east'); return false;"><i class="fa fa-plus"></i> Add Call Types from Template</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="#" onclick="window.open('/Schedules/view_deleted/<?php echo $did_id; ?>','_blank','width=800,height=900,resizable=1,scrollbars=1,location=yes,menubar=yes,toolbar=yes');return false;"><i class="fa fa-plus"></i> View Deleted Scripts</a>&nbsp;&nbsp;&nbsp;&nbsp;    
    <button id="save_order" value="Save Order" class="is_hidden" onclick="saveOrder(); return false;">Save Order</button>
    </div>
    <?php
    }
    ?>
    <table cellpadding="4" cellspacing="0" class="data" width="100%">
    <tbody>
    <tr>
            <th width="155" align="left">Call type</th>
            <th width="85">Type</th>
            <th width="240" align="left">Schedule</th>
<?php if ($this->Permissions->isAuthorized('CalltypesEdit',$permissions)) { ?> 
            <th width="50" align="center">Template</th>
            <?php }?>
            <th width="30" align="center">Active</th>
            <th width="100" class="actions"><?php echo __('Actions'); ?></th>
    </tr>
    </table>
    <div id="ct_mainlist">
    <?php
    $old_title = '';
    foreach ($Calltypes as $k => $Calltype): 
        if ($k == (count($Calltypes)-1) || $Calltype['Calltype']['title'] != $Calltypes[$k+1]['Calltype']['title']) $class='bborder';
        else $class='';
        
    ?>
        <div class="ct_list" calltype_id="<?php echo $Calltype['Calltype']['id']; ?>">
            <table cellpadding="4" cellspacing="0" class="data" width="100%">
            <tbody>
            <?php 
            $firstrow = true;
            foreach($Calltype['Schedule'] as $s) {
            ?>
            <tr class="<?php echo $class; ?> ctlist_tr" data-sid="<?php echo $s['id']; ?>">
                <td width="155">
                <?php if ($firstrow) {
                    echo '<div class="title">'.h($Calltype['Calltype']['title']).'</div>'; 
                    echo '<div class="desc">' . $Calltype['Calltype']['description'] . '</div><a href="#" data-id="'. $Calltype['Calltype']['id'].'" class="edit_desc" title="What you enter here will show up below the calltype name on the operator screen">(edit description)</a>';
                }
                else echo '&nbsp;';
                ?></td>
                <td width="85" align="center" ><?php 

                echo $s['type']; ?></td>
                <td width="240" >   
                <?php
                if ($firstrow) {
                    echo '<a href="#" class="add_ts" data-id="'. $Calltype['Calltype']['id'].'" data-did-id="'. $Calltype['Calltype']['did_id'].'">Add time sensitive instructions</a><br>';
                    
                }           
                echo $s['schedule'];
                ?>
                </td>
                <td align="center" width="40"><?php
                if ($firstrow) { 
                    if ($Calltype['Calltype']['template']) {
                        $checked = 'checked';
                    }
                    else $checked = '';
                if ($this->Permissions->isAuthorized('CalltypesEdit',$permissions)) {
                        echo '<input type="checkbox" value="'. $Calltype['Calltype']['id'] .'" ' . $checked . ' class="cttemplate">'; 
                    }
                    $firstrow = false;
                }
                ?></td>
                
                <td align="center" width="40"><?php 
                    if ($s['active']) {
                        $checked = 'checked';
                    }
                    else $checked = '';
                    echo '<input type="checkbox" value="'. $s['id'] .'" ' . $checked . ' class="ctactive">'; 

                ?></td>
                <td class="actions" width="100" align="center" >
                  <a href="#" class="edit_script" ><img title="edit" alt="edit" src="/img/edit.png" width="16" height="16"></a>                
<?php if ($this->Permissions->isAuthorized('CalltypesDelete',$permissions)) { ?> 
                    <a href="#" class="delete_ct" data-sid="<?php echo $s['id']; ?>"><img title="delete" alt="delete" src="/img/delete.png" width="16" height="16"></a>         
                    <a href="#" class="edit_schedule"  data-sid="<?php echo $s['id']; ?>"  title="<?php echo $Calltype['Calltype']['title']; ?>"><img title="edit schedule" alt="edit schedule" src="/img/clock.png" width="16" height="16"></a>      
			        <a href="#" class="view_ct" onclick="window.open('/Schedules/view_script/<?php echo $s['id']; ?>','_blank','width=800,height=700,resizable=1,scrollbars=1,location=yes,menubar=yes,toolbar=yes');return false;">view script</a>
                    
    <?php
    }
    ?>

                    <?php
                    if (0) {
                    ?>
                    <a href="#" onclick="if (confirm('Are you sure you want to PERMANENTLY delete this calltype?')) deleteCalltype('<?php echo $Calltype['Calltype']['id'];?>'); return false;">DELETE</a>          
                    <?php
                    }
                    ?>
        
                    <!--<a href="#" onclick="loadPage('/Schedules/duplicate/<?php echo $s['id']; ?>', '<?php echo $this->request->data['detail']; ?>'); return false;" title="<?php echo $Calltype['Calltype']['title']; ?>">copy schedule</a>          -->
                </td>
            </tr>
            <?php
            }
            ?>
            </table>
        </div>
        
    <?php endforeach; ?>
    </div>
</div>

<div id="ct-dialog">
<form name="ctform" id="ctform" style="display:none;">
    <input id='cttitle' name='cttitle' value="<?php echo $Calltype['Calltype']['title']; ?>">
    <textarea id="cteditor" name="cteditor" class="htmleditor" style="minHeight:300px; minWidth: 500px;"></textarea>
</form>
</div>

<script type="text/javascript">
$(function() {

    $('#ctlist .ctlist_tr td').on('click', function(event) {
        var t = event.target;
        if ($(t).hasClass('delete_ct') || $(t).parent().hasClass('delete_ct') ) {
            var sid = $(t).attr('data-sid') || $(t).parent().attr('data-sid') ;
            user_confirm('Are you sure you want to delete this schedule?', function() {
                getJson('/Schedules/delete/' + sid, null, function() {
                    if (didSpecified()) loadCalltypes($('#find_did').val(), 'did-content', 'did-detail')
                    event.stopPropagation();
                });
            }); 
            event.stopPropagation();
            return false;
        }
        if ($(t).hasClass('view_ct') || $(t).parent().hasClass('view_ct') ) {

            event.stopPropagation();
            return false;
        }        
        else if ($(t).hasClass('edit_schedule')|| $(t).parent().hasClass('edit_schedule') ) {
            var sid = $(t).attr('data-sid') || $(t).parent().attr('data-sid');
            $('#did-detail').html('');
            didLayout.center.children.layout1.open('east');
            var url = '/Calltypes/schedule_edit/' + sid;
            $.ajax({
                url: url,
                dataType: 'html',
                type: 'GET'
            }).done(function(data) {
                $('#did-detail').html(data);
            }); 
        }
        else if ($(t).hasClass('add_ts')) {
					loadPage(null,"/Calltypes/add/" + $(t).attr('data-did-id') + "/" + $(t).attr('data-id'), 'did-detail'); didLayout.center.children.layout1.open('east');
        }
        else if ($(t).hasClass('edit_desc')) {
					editDesc(t, + $(t).attr('data-id'));
        }
        else {
            var el = $(t).parents('.ctlist_tr');
            var sid = $(el).attr('data-sid');
            
            if (dragging) {
                dragging = false;
            }
            else {
                $('#did-detail').html('');
                didLayout.center.children.layout1.open('east');
                var url = '/Schedules/edit/' + sid;
                $.ajax({
                    url: url,
                    dataType: 'html',
                    type: 'GET'
                }).done(function(data) {
                    $('#did-detail').html(data);
                });                 
    
            }            
        }
        event.stopPropagation();
    });
    
    $('#ctlist .ctactive').on('click', function(event) {
        event.stopPropagation();        
        $checkbox = $(this);
        if (this.checked) user_confirm('Are you sure you want to activate this calltype schedule?', function() {
                $.ajax({
                        url: '/Schedules/status/' + $checkbox.attr('value') + '/' + 1,
                        type: 'POST',
                        dataType: 'json'
                }).done(function(data) {    
                    if (data.success) {
                        createToast('info', data.msg);                  
                    }
                    else {
                        alert(data.msg);
                        $checkbox.prop('checked', false);                
                    }
                }).fail(function() {
                    alert( "Cannot save changes, please try again later" );
                    $checkbox.prop('checked', false);                
                });
                             
            }, function() {$checkbox.prop('checked', false); return false;});
        else user_confirm('Are you sure you want to de-activate this calltype schedule?', function() {
                $.ajax({
                        url: '/Schedules/status/' + $checkbox.attr('value') + '/' + 0,
                        type: 'POST',
                        dataType: 'json',
                        data: 'desc=' + $('#ctform .jqte_editor').html()
                }).done(function(data) {    
                    if (data.success) {
                        createToast('info', data.msg);                  
                    }
                    else {
                        alert(data.msg);
                        $checkbox.prop('checked', true);                
                    }
                }).fail(function() {
                    alert( "Cannot save changes, please try again later" );
                    $checkbox.prop('checked', true);                
                });       
            
            }, function() {$checkbox.prop('checked', true); return false;});
              
                    
    });
    $('#ctlist .cttemplate').on('change', function() {
        $checkbox = $(this);
        if (this.checked) user_confirm('This will make the CallType available as a template, are you sure you want to do this?', function() {
                $.ajax({
                        url: '/Calltypes/status/' + $checkbox.attr('value') + '/' + 1,
                        type: 'POST',
                        dataType: 'json'
                }).done(function(data) {    
                    if (data.success) {
                        createToast('info', data.msg);                  
                    }
                    else {
                        alert(data.msg);
                        $checkbox.prop('checked', false);                
                    }
                }).fail(function() {
                    alert( "Cannot save changes, please try again later" );
                    $checkbox.prop('checked', false);                
                });
                             
            }, function() {$checkbox.prop('checked', false); return false;});
        else user_confirm('This will make the CallType Unavailable as a template, it will not remove the CallType from any accounts that the template was already copied to. Are you sure you want to do this?', function() {
                $.ajax({
                        url: '/Calltypes/status/' + $checkbox.attr('value') + '/' + 0,
                        type: 'POST',
                        dataType: 'json',
                        data: 'desc=' + $('#ctform .jqte_editor').html()
                }).done(function(data) {    
                    if (data.success) {
                        createToast('info', data.msg);                  
                    }
                    else {
                        alert(data.msg);
                        $checkbox.prop('checked', true);                
                    }
                }).fail(function() {
                    alert( "Cannot save changes, please try again later" );
                    $checkbox.prop('checked', true);                
                });       
            
            }, function() {$checkbox.prop('checked', true); return false;});
    });
    

 
    var dragging = false;

    $('#ct_mainlist').sortable({
        start: function(event, ui) {
                dragging = true;;
        },     
        change: function(event, ui) {
            $('#save_order').show(function() {
                
                $('#save_order').effect('highlight');
            });          
        }
    });
    $('#ct_mainlist tbody').disableSelection();
    $('#cteditor').jqte({p: false});              
    

});

   function editDesc(t, id) {
        $('#cteditor').jqteVal($(t).siblings('.desc').html());
        $('#cttitle').val($(t).siblings('.title').html());
        $('#ct-dialog').dialog({
            resizable: true,
            autoOpen: true,
            height:350,
            width:540,
            modal: true,
            buttons: {
                "Save": function() {
                    var newhtml = $('#ctform .jqte_editor').html();
                    $(t).siblings('.desc').html(newhtml);
                    var newtitle = $('#cttitle').val();
                    $(t).siblings('.title').html(newtitle);
                    var url = "/Calltypes/description/" + id;
                    $.ajax({
                            url: url,
                            type: 'POST',
                            dataType: 'json',
                            data: 'desc=' + $('#ctform .jqte_editor').html()+ '&title=' + $('#cttitle').val()
                    }).done(function(data) {    
                        if (data.success) {
                            $(t).siblings('.desc').html(newhtml);
                            $('#ct-dialog').dialog('close');
                            alert(data.msg);
                        }
                        else {
                            alert(data.msg);
                        }
                    }); 

                    
                },
                "Cancel": function() {
                    $('#ct-dialog').dialog('close');
                }
            }, 
            open: function() {
                $('#ctform').show();

            },
            close: function() {

                $('#ctform').hide();
                $('#ct-dialog').dialog('destroy');
            }
        });       
        
    }
    
    function saveAddScheduling(t,id) {
        var myform = $(t).parents('form');
        var url = "/Calltypes/add/" + id;
            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: $('#CalltypeScheduleEditForm').serialize()
            }).done(function(data) {    
                if (data.success) {
                    $('#dialogWin').dialog('close');
                    loadCalltypes($('#find_did').val(), 'did-content', 'did-detail');          
                    alert(data.msg);
//          didLayout.center.children.layout1.close('east')                 
                    var url = '/Schedules/edit/' + data.new_id;
                    loadPage(null, url, 'did-detail'); 
                    didLayout.center.children.layout1.open('east');
                }
                else {
                    alert(data.msg);
                }
            });    
    }  
    function saveTemplate(t,id) {
        var myform = $(t).parents('form');
        var url = "/Calltypes/template/" + id;
            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: $('#CalltypeTemplateEditForm').serialize()
            }).done(function(data) {    
                if (data.success) {
                    $('#dialogWin').dialog('close');
                    loadCalltypes($('#find_did').val(), 'did-content', 'did-detail');          
                    alert(data.msg);
                    didLayout.center.children.layout1.close('east')
                }
                else {
                    alert(data.msg);
                }
            });    
    }  

    function saveOrder() {
        var ordered_list = new Object();
        $('.ct_list').each(function(index) {
            ordered_list[index] = $(this).attr('calltype_id');
        });
        
        $.ajax({
            type: 'POST',
            url: '/Calltypes/reorder', 
            data: {list: ordered_list},
            dataType: 'json'
        }).done(function(data) {
            if (data.success) {
                alert('Your changes have been saved');
                $('#save_order').hide();
            }
            else alert(data.msg);
        }).fail(function () {
            alert('Failed to save your changes, try again later');        
        });
    }
                
</script>
