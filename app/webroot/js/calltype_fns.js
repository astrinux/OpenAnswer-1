

function displayAction(action, num) {
  var newul = '<input type="hidden" name="sid['+num+']" value="'+action['schedule_id']+'">';
  newul += '<input type="hidden" name="id['+num+']" value="'+action['id']+'">';
  newul += '<input type="hidden" name="sort['+num+']" value="'+action['sort']+'">';
  newul += '<input type="hidden" name="num[]" value="'+num+'">';
  newul += '<input type="hidden" name="action_text['+num+']" value="'+action['action_text']+'">';
  newul += '<input type="hidden" name="caption1['+num+']" value="'+action['caption1']+'">';
  newul += '<input type="hidden" name="caption2['+num+']" value="'+action['caption2']+'">';
  newul += '<input type="hidden" name="action_type['+num+']" value="'+action['action_type']+'">';
  newul += '<input type="hidden" name="eid['+num+']" value="'+action['eid']+'">';
	if (action['Prompt'].length > 0) {
		newul += '<input type="text" name="action_text['+num+']" value="' + action['action_text'] + '" size="25" class="input_h"><ul class="userprompts">';
		
		for (var j=0; j< action['Prompt'].length; j++) {
			newul += '<li><select name="prompt['+num+'][]" style="width:350px;">';
			sel = false;
	    for (var k in prompts) {
	    	newul += '<option value="'+prompts[k]['caption']+'"';
	    	if (prompts[k]['caption'] == action['Prompt'][j]['caption']) {
	    		newul += ' selected';
	    		sel = true;
	    	}
	    	newul += '>'+prompts[k]['description']+'</option>';
	    }
	    if (!sel) newul += '<option value="'+action['Prompt'][j]['caption']+'" selected>'+action['Prompt'][j]['caption']+'</option>';
	  	newul += '</select></li>';
		}
		newul += '</ul>';
	}
	var tarray;
	if (action['eid'] != '') {
		//get arrays of action recipients
		var e_arr = action['eid'].split(',');
		emp = new Array();
		var temp;
		for (j = 0; j< e_arr.length; j++) {
		  if (e_arr[j] != 'ALL') {
			  emp[j] = jsondata.contacts[e_arr[j]]['name'] + ' (' + jsondata.contacts[e_arr[j]]['contact'] +')';
			}
			else emp[j] = 'Requested Staff';
		}
		newul += ('<p><a href="#" onclick="editAction($(this).parents(\'li\').index(), '+num+'); return false;" title="Edit action"><b>' + action['action_text'].replace('[e]', emp.join(',')).replace('[a]', actiondefs[action['action_type']]) + '</b></a></p>');
		newul = newul + '<div id="editbox_'+num+'"></div>';
	}	  
	//else newul += (action['action_text'].replace('[a]', actiondefs[action['action_type']]));
	if (!action['helper']) action['helper'] = '';
  newul += 'Helper: (<input type="hidden" name="helper['+num+']" value="'+action['helper']+'"><span contenteditable=true class="helper">' + action['helper'] + ' </span>)';
	return newul;
}

function removeAction(t) {
	if (confirm('Are you sure you want to delete this action?')) {
		$(t).parents('.sortable-li').remove();
	}
}
			
function createActions(jsondata) {
	var newul = '<ul id="ul_actions" class="sortables" style="padding:15px 0px;" >';
	var action;
	var id='';
	var sel = false;
	var newhtml = '';
	if (jsondata.actions.length < 1) {
      newul += '<li  class="removeme"><div style="height: 100px;" ><i>drag here to configure your calltype</i></div></li>';
	}
	else {
  	for (var i=0; i< jsondata.actions.length; i++) {
  		action = jsondata.actions[i];
  		newul += '<li  class="sortable-li ui-state-default"><div class="act handle"><a href="#" onclick="return false;" title="Click and drag to reorder">&equiv;</a></div><div class="act"><a href="#" onclick="removeAction(this); return false;">x</a></div>';
  		if (action['eid'] != '') {
  		  newul += '<div class="act"><a href="#" onclick="editAction($(this).parents(\'li\').index()); return false;" title="Edit action">edit</a></div><div class="act_content">';
  		}
  		else {
  		  newul += '<div class="act"><a href="#" onclick="addUserPrompt(this); return false;;" title="Add prompt">+</a></div><div class="act_content">';
  		}
  		newhtml = displayAction(action, (i+1));
      newul += newhtml;
  		newul = newul + '</div></li>';
  	}
  }
	newul += '</ul>';
	actionnum = (i+2);
	return newul;
}

function addUserPrompt(t) {
  var newli = '<li><select name="" style="width:350px;"><option value="">Select</option>';
		    for (var k in prompts) {
		    	newli += '<option value="'+prompts[k]['caption']+'"';
		    	newli += '>'+prompts[k]['description']+'</option>';
		    }
		    newli += '</li>';
	$(t).parents('li').find('.userprompts').append(newli);
}

function getEmployees() {
  var type = $('#actsel').val();
  var index = $('#li_index').val();
  
  
  var children = $('#ul_actions').children('li');
  var li = children[index];


  var eid = $(li).find('input[name^=eid]').val();
  var e_arr = new Array();
  if (eid != '') {
			//get arrays of action recipients
		e_arr = eid.split(',');
	}

  
  var opt_val, opt_txt;
  var el = $('#empsel');
  var typestr;
  var sel; 
  el.empty(); // remove old options
    if ($.inArray('ALL', e_arr) >= 0) sel = true;
    else sel = false; 
    el.append($("<option></option>").attr("value",'ALL').text('Requested Staff').prop('selected', sel));
    $.each(jsondata.employees, function() {
      if (type == '4' && this.email.length) typestr = 'email';
      else if ((type == '2' || type == '1')  && this.phone.length) typestr = 'phone';
      else if (type == '3'  && this.cell.length) typestr = 'cell';
      else if (type == '6'  && this.vmail.length) typestr = 'vmail';
      else typestr = '';
      if (typestr !== '') {
        for (var i=0; i<this[typestr].length; i++) {
          opt_val = this[typestr][i].id;
          opt_txt = this.name + ' - ' +  this[typestr][i].contact;
          if ($.inArray(opt_val, e_arr) >= 0) {
            console.log('selected ' + opt_val);
            console.log(e_arr);
            sel = true;
          }
          else sel = false;
          el.append($("<option></option>").attr("value",opt_val).text(opt_txt).prop('selected', sel));
        }
      }  
      /*if (type == '4' && this.email.length) {
        for (var i=0; i<this.email.length; i++) {
          opt_val = this.email[i].id;
          opt_txt = this.name + ' - ' +  this.email[i].contact;
          el.append($("<option></option>").attr("value",opt_val).text(opt_txt));
        }
      }
      else if ((type == '1' || type == '2') && (this.phone.length)) {
        for (var i=0; i<this.phone.length; i++) {
          opt_val = this.phone[i].id;
          opt_txt = this.name + ' - ' + this.phone[i].contact;
          el.append($("<option></option>").attr("value",opt_val).text(opt_txt));
        }
      }
      else if ((type == '3') && (this.cell.length)) {
        for (var i=0; i<this.cell.length; i++) {
          opt_val = this.cell[i].id;
          opt_txt = this.name + ' - ' + this.cell[i].contact;
          el.append($("<option></option>").attr("value",opt_val).text(opt_txt));
        }
      }              
      else if ((type == '4') && (this.vmail.length)) {
        for (var i=0; i<this.vmail.length; i++) {
          opt_val = this.vmail[i].id;
          opt_txt = this.name + ' - ' + this.vmail[i].contact;
          el.append($("<option></option>").attr("value",opt_val).text(opt_txt));
        }
      }              
      else if ((type == '5') && (this.web.length)) {
        for (var i=0; i<this.web.length; i++) {
          opt_val = this.web[i].id;
          opt_txt = this.name + ' - ' + this.web[i].contact;
          el.append($("<option></option>").attr("value",opt_val).text(opt_txt));
        }
      }  */
    });
 
}        

function editAction(index, num) {
   var children = $('#ul_actions').children('li');
   var li = children[index];

   $('#li_index').val(index); // keep track of which action we're editing 
   
   var caption1 = $(li).find('input[name^=caption1]').val().trim();
   if (caption1 == '') caption1 = emptyText;
   var caption2 = $(li).find('input[name^=caption2]').val().trim();
   if (caption2 == '') caption2 = emptyText;
   $('#caption1').html(caption1);
   $('#caption2').html(caption2);
   $('#actsel').val($(li).find('input[name^=action_type]').val());

   var eid = $(li).find('input[name^=eid]').val();
		if (eid != '') {
			//get arrays of action recipients
			var e_arr = eid.split(',');
/*			emp_sel = new Array();
			for (j = 0; j< e_arr.length; j++) {
			  if (e_arr[j] != 'ALL') {
				  emp_sel.push({id: e_arr[j], text: jsondata.contacts[e_arr[j]]['name'], name: jsondata.contacts[e_arr[j]]['name'] });
				}
			}
      $("#empsel").select2("data", emp_sel);*/
		}	   
   getEmployees();
   $("#empsel").multiselect('refresh');   
   $('#actionBox').dialog('open');
		
}

function saveCalltype(sid) {
  $('#ul_actions').children('li').each(function(index, val) {
    $(this).find('input[name^=sort]').val((index+1));
  });  
  var data = $('#calltype_form').serialize(); 
  $.ajax({
    type: 'POST',
    url: '/Schedules/edit/' + sid, 
    data: data,
    dataType: 'json'
  }).done(function(data) {
    if (data.success) {
      alert('Your changes have been saved');
      didLayout.center.children.layout1.close('east');      
    }
    else alert(data.msg);
  }).fail(function () {
	  alert('Failed to save your changes, try again later');	      
  });

}

function saveAction() {
  var index = $('#li_index').val();
  var children = $('#ul_actions').children('li');
  var li = children[index];
  var action_text = ''
  var caption1;
  var caption2;
  if ($('#caption1').html().trim() != '' && $('#caption1').html().trim() != emptyText) {
    action_text += $('#caption1').html().trim() + ' ';
    caption1 = $('#caption1').html().trim();
  }
  else caption1 = '';
  var eids = '';
  
  if ($('#actsel').val()) action_text += '[a] ' ;
  if ($('#empsel').val().length > 0) {
    action_text += '[e] ';
    eids = $('#empsel').val().join(',');
    $(li).find('input[name^=eid]').val();
  }
  $(li).find('input[name^=action_type]').val($('#actsel').val());
  
  if ($('#caption2').html().trim() != '' && $('#caption2').html().trim() != emptyText) {
    action_text += $('#caption2').html().trim() + ' ';
    caption2 = $('#caption2').html().trim();
  }
  else caption2 = '';
  var num = $(li).find('input[name^=num]').val();
  var myaction = {Prompt: [], num: num, caption1: caption1, caption2: caption2, sid: jsondata.schedule.id, id: '', sort: '', action_text: action_text, eid: eids, action_type: $('#actsel').val()};
  
  /*$(li).find('input[name^=action_text]').val(action_text);
  $(li).find('input[name^=caption1]').val($('#caption1').html().trim());    
  $(li).find('input[name^=caption2]').val($('#caption2').html().trim());    
  $(li).find('input[name^=eid]').val($('#empsel').val());    
  $(li).find('input[name^=action_type]').val($('#actsel').val());    */
  var thehtml = displayAction(myaction, num);
  $(li).find('.act_content').html(thehtml);
  
}

function saveUser(id) {
		var url = '/Users/edit/' + id ;
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: $('#editUser').serialize()
    }).done(function(data) {    
					if (data.success) {
            loadPage(this, 'Users/', 'user-content');	
						userLayout.center.children.layout1.close('east');            					
					}
			alert(data.msg);
		});			        
 		return false;
}

function addUser() {
		var url = '/Users/add/' ;
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: $('#addUser').serialize()
    }).done(function(data) {    
					if (data.success) {
            loadPage(this, 'Users/', 'user-content');	
						userLayout.center.children.layout1.close('east');            					
					}
			alert(data.msg);
		});			        
 		return false;
}

		function addAccount(t) {
		  var myform = $(t).parents('form');
		  $.ajax({
		    url: '/Accounts/add',
		    type: 'post',
		    dataType: 'json',
		    data: myform.serialize()
		  }).done(function(data){
		    if (data.success) {
          loadPage(this, 'Accounts/edit/'  + data.new_id, 'acct-content');						
  			}
		    alert(data.msg);
		  });
		}
		
    function saveAccountAdd(t) {
  		var myform = $(t).parents('form');
  		var url = '/Accounts/add' ;
      $.ajax({
          url: url,
          type: 'POST',
          dataType: 'json',
          data: myform.serialize()
      }).done(function(data) {    
/*  					if (data.success) {
              loadPage(this, 'Accounts/edit/'  + data.new_id, 'acct-content');						
  					}*/
  					alert(data.msg);
  		});			        
   		return false;
      
    }	