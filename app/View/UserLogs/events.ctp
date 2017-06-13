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
<div class="users view panel-content">
<h2>Operator Events</h2>
<form>
<label><b>Operator:</b></label> <input name="data[Search][user_id]" style="width: 180px;"  class="required report_user_sel2" type="hidden"> <label><b>Date:</b></label> <input name="data[Search][report_date]" type="text" class="required datepicker"> &nbsp;&nbsp;&nbsp;<input type="submit" value="Go" onclick="submitOperatorEvents(this); return false;">
</form>
<br><br>
<div id="userevents">
</div>
<script>
function submitOperatorEvents(t) {
  var myform = $(t).parent('form')
  var missingEntry = false;

  if (missingEntry) return false;
   $.ajax({
        url: '/UserLogs/events/',
        type: 'post',
        dataType: 'html',
        data: myform.serialize()
		}).done(function(data) {    
			$('#userevents').html(data);
		}).fail(function() {
		      alert('Cannot communicate to the OpenAnswer Server, contact Technical Support');
		});     
}
  $(function () {
		$('.datepicker').datepicker({
    	dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true     	
		});	
		    
		$(".report_user_sel2").select2({
      initSelection : function (element, callback) {
        var id=$(element).val();
        if (id!=="") {        
          $.ajax("/Users/find/"+id, {
            dataType: "json"
          }).done(function(data) { 
            if (data.length > 0) {
              callback(data[0]); 
            }
            else {
              $(element).val('');
            }
          });
        }
      },		  
		  placeholder: 'Search operator or extension',
		  minimumInputLength: 2,
      allowClear: true,
      blurOnChange: true,
      openOnEnter: false,		  
		  ajax: {
			  url: "/Users/find/",
		    data: function(term, page) {
		      return {term: term, page: page};
		    },
			  dataType: 'json',
		    results: function (data, page) {
		      return {results: data};
		    }
		  }		  

	  });
	});
</script>
