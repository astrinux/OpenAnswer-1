<div class="panel-content" id="home">
<?php

				$photo = AuthComponent::user('photo');
				if (!empty($photo)) {
					echo '<div class="avatarS"><img src="data:'.$photo.'"></div>';
				}
				else echo '<div class="avatarS"><i class="fa fa-user fa-5x"></i></div>';
				?>
				<h1 class="fg_blue"> &nbsp;&nbsp;Welcome back, <?php if (AuthComponent::user('firstname')) echo AuthComponent::user('firstname'); else echo AuthComponent::user('user_name')?>!</h1><br><br>

	<div class="content">

		<div class="rectbox animated bounceInDown ">
			<div class="color2"><br>
				<i class="fa fa-sticky-note"></i>
				<h2>Messages</h2>
				</div>
				<div>
					<strong><?php echo $messages_today; ?></strong>
					Today
					<strong><?php echo $messages_current_week; ?></strong>
					This Week
				</div>
		</div>
		
		<div class="rectbox animated bounceInDown">
			<div class="color3"><br>
				<i class="fa fa-list-ol"></i>
				<h2>Calls</h2>
				</div>
				<div>
					<strong><?php echo $calls_today; ?></strong>
					Today
					<strong><?php echo $calls_current_week; ?></strong>
					This week
				</div>
		</div>
		
		<div class="lbreak"></div>  
		
		<div class="rectbox animated bounceInRight ">
			<div class="color4" title="Includes <?php echo implode(', ', $personal_breaks); ?>"><br>
				<i class="fa fa-coffee"></i>
				<h2>Personal Breaks</h2>
				</div>
				<div>
					<strong><?php echo $breaks[0]['cnt']; ?></strong>
					Today
					<strong><?php echo round($breaks[0]['break_len']/60); ?><i>min</i></strong> 
					Duration
				</div>
		</div>
		
		<div class="rectbox animated bounceInLeft " >
			<div class="color1" title="Percentage of audited messages that are free of mistakes!"><br>
					<i class="fa fa-check-square"></i>
				<h2>Accuracy</h2>
				</div>
				<div>
					<strong><?php if (!empty($audited_current_week)) echo sprintf("%0.1f", 100-($mistakes_current_week*100/$audited_current_week)); else echo '0';?>%</strong>
					This week
					<strong><?php if (!empty($audited_last_week)) echo sprintf("%0.1f", 100-($mistakes_last_week*100/$audited_last_week)); else echo '0'?>%</strong>
					Last week
				</div>
		</div>  
		<br><br><a href="#" onclick="window.open('/OpenAnswer/chart/<?php echo $user_id; ?>/','_blank','width=860,height=650,scrollbars=1,resizable=1,location=0,menubar=0,toolbar=0');"><i class="fa fa-line-chart fa-2x"></i> &nbsp;Daily/ Monthly Trends</a>
	</div>
	<div class="content" id="recent_calls">    	  
	
	</div>

</div>
<script>
$(function() {
	$('#recent_calls').load('/CallLogs/my_recent/'+myId);
});
</script>