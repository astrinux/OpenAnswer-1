				<div id="operator_btns">
				<button class="is_hidden" id="unavailbtn" ><div>MY STATUS:<span>NOT TAKING CALLS</span></div> <img src="/themes/vn/off-toggle.png" width="50" height="35"></button>      
				<button class="is_hidden" id="availbtn"><div>MY STATUS:<span>TAKING CALLS</span></div> <img src="/themes/vn/on-toggle.png" width="50" height="35"></button>      
				<button class="break_btn is_hidden" id="offbreakbtn"><i class="fa fa-coffee"></i>&nbsp;&nbsp;Break</button>
				<button class="break_btn is_hidden" id="onbreakbtn">On Break</button>
			</div>				
			
			<script>
			$(function() {
			  $('#unavailbtn').on('click', function() {
			    localStorage.setItem('oa_taking_calls', true); 
			    logTakingCalls(true); 
			    takingCalls(true, false); 
			    return false;
			  });  
			
			  $('#availbtn').on('click', function() {
			    localStorage.setItem('oa_taking_calls', false); 
			    logTakingCalls(false); 
			    takingCalls(false, false); 
			    return false;
			  });  				
			});
			</script>
