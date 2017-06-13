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
    .ceditable {min-height: 16px; min-width: 100px;padding:2px;}
    .ceditable:hover {background:#FFD455;}
     div.editable {display:inline;}
    .ceditable .empty {color: #aaa;}
    #empcontacts .handle {text-decoration: none;}
</style>











<?php


?>

<style>
    table.contacts tr td {padding: 10px;}
    .ceditable {min-height: 16px; min-width: 100px;padding:2px;}
    .ceditable:hover {background:#FFD455;}
    div.editable {display:inline;}
    .ceditable .empty {color: #aaa;}
    tr.tbd * {color: #bbb; text-decoration:line-through}
    .employees textarea {vertical-align: top}
    .employees label {display: block; float: left; width: 100px;}
    .trigger {margin: 10px;}
    .trigger a {text-decoration:none;}
    .trigger a:hover {text-decoration:underline;}
    #add-contact, #add-contact ul {text-align:left; width: 50px;}
</style>

<div class="panel-content  crms form" style="position:relative;">
 
<?php echo $this->Form->create('Crm', array('id'=> 'crm_edit')); ?>
        <h1>New CRM Hook</h1>
    <div>
    <?php
        echo $this->Form->input('id', array('type' => 'hidden'));
        echo $this->Form->input('did_id', array('type' => 'hidden', 'value' => $did_id));
        echo $this->Form->input('name', array('label'=>'CRM Hook Name','class' => 'required'));
        echo $this->Form->input('type', array('label' => 'CRM Type','options' => $crm_types));
        echo $this->Form->input('username', array('label'=>'CRM User Name'));
        echo $this->Form->input('password', array('label'=>'CRM Password'));
        echo $this->Form->input('method', array('label' => 'Method','options' => array("createticket" => "Create Ticket","findticket" => "Find Ticket","createcontact" => "Create Contact", "findcontact" => "Find Contact")));
        echo $this->Form->input('url', array('label'=>'API URL','class' => 'required'));

    ?>
        <div id="addmappings" class="trigger">
            <a href="#" data-dropdown="#add-mapping" data-horizontal-offset="30" data-vertical-offset="25">+ Add field mapping</a>      
        </div>
        <table cellpadding="2" cellspacing="0" class="mappings" id="crmmappings">
            <tbody>
                <tr>
                    <th width="15">
                    <th width="80"></th>
                    <th width="110">&nbsp;</th>
                    <th width="570">&nbsp;</th>
                </tr>
    </tbody>
    </table>

    </div>

    <br><br>
    </form>
</div>
<script>
    function addMapping(type, table_id) {
        var count = $('#' + table_id).find('tr').length + 1;
        var duplicate = false;
        var loop = true;
        var cnt = 2;
        var classname = "";
        var fieldsize = '';
        
        fieldsize= '30';
        html =  '';
        html += '<tr onmouseover="showDel(this);" onmouseout="hideDel(this);">';
        html += '    <td>';
        html += '        <input type="text" name="data[CrmMapping]['+count+'][caption]" value="" checked>&nbsp;Call Type Prompt Caption';
        html += '    </td>';
        html += '    <td>';
        html += '        <input type="hidden" name="data[CrmMapping]['+count+'][id]" value="">';
        html += '        <input type="text" name="data[CrmMapping]['+count+'][crmfield]" size="'+fieldsize+'" value="" class="required '+classname+'">&nbsp;Crm Field';
        html += '        <span class="trash is_hidden">';
        html += '            <a href="#" onclick="deleteMapping(this, null)" title="Remove this mapping">';
        html += '                <img src="/img/icons/delete.png" width="12" height="12" align="absmiddle">';
        html += '            </a>';
        html += '        </span>';
        html += '    </td>';
        html += '</tr>';
        $('#crmmappings tbody').append(html);
        $('#crmmappings tbody').sortable({ handle: ".handle" });
        $('#crm_edit .ceditable').blur(function() {
            changeLabel(this);
        });
    }
    
    function deleteMapping(t, id) {
        if (id !== null) {
            var url = '/CrmsMapping/checkDelete/' + id;
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'GET',
            }).done(function(data) {
                if (!data.success) {
                alert(data.msg);
                return false;
            }
            else {
                $(t).parents('tr').addClass('tbd');
                $(t).parents('tr').find('input').prop('disabled', true);
                alert('You must still click on \'Save\' to save your changes');
                return true;
            }
    });
  }
  else {
        $(t).parents('tr').addClass('tbd');
        $(t).parents('tr').find('input').prop('disabled', true);    
        alert('You must still click on \'Save\' to save your changes');
        return true;
  }
}    

  $('.ceditable').blur( function(){
    var thelabel = $(this).html()
    if (thelabel != '') $(this).parent().next().find('.clabel').val(thelabel);
    });  

  $(function() {
    $('#crmmappings tbody').sortable();
    
    $('#did_save_btn').off('click');
    $('#did_save_btn').on('click', function() {
      addCrmSubmit('<?php echo $did_id; ?>');
    });
    

    $('#CrmType').on('change', function() {
        console.log("test");
        }
    );
    
    $('.numeric').mask("?9999",{placeholder:" "});
    
  });
</script>

<div id = "crmbox">
CRM Field mappings map call type prompts to the fields in the customer CRM that hold the same information.
When performing searches, or creating records in the customer CRM, the data used will be mapped from the call type prompt to the appropriate
field in the crm and populated accordingly.
</div>
<div id="mappingbox">
    <div id="add-mapping" class="dropdown dropdown-tip dropdown-relative">
        <ul class="dropdown-menu">
            <li><img src="/img/icons/web.png" align="left"> <a href="#1" onclick="addMapping('', 'crmmappings'); return false;">New Field Mapping</a></li>
        </ul>
    </div>  
    <div>
        The following keys are available for use in parameter values and URLs, they are case sensitive!
        <p>To use them simply insert them anywhere in the URL or Parameter values</p>
        
    </div>
</div>


