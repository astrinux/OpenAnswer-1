<html>
<head>
    <title><?php echo _('Appointment Details'); ?></title>
      <style>
        body, table td {font-family: Verdana; font-size: 12px; padding: 6px;}
        h2 {font-size:15px; margin-top:30px;font-weight: normal;}
        .label {text-align:right;}
      </style>
</head>
<body>
    <div class="email-container" style="width: 650px; border: 1px solid #eee;">


        <div id="content" style="padding: 10px 15px;">
            <?php echo $email_title; ?>
            
            <h2><?php echo _('Appointment Details'); ?></h2>
            <table id="appointment-details" cellpadding="6" cellspacing="0" border="0">
                <tr>
                    <td class="label" ><?php echo _('Service'); ?></td>
                    <td><?php echo $appointment_service; ?></td>
                </tr>
                <tr>
                    <td class="label" ><?php echo _('Confirmation #'); ?></td>
                    <td><?php echo $confirmation; ?></td>
                </tr>                
                <tr>
                    <td class="label" ><?php echo _('Provider'); ?></td>
                    <td><?php echo $appointment_provider; ?></td>
                </tr>
                <tr>
                    <td class="label" ><?php echo _('Start'); ?></td>
                    <td><?php echo $appointment_start_date; ?></td>
                </tr>
                <tr>
                    <td class="label" ><?php echo _('End'); ?></td>
                    <td><?php echo $appointment_end_date; ?></td>
                </tr>
                <tr>
                    <td class="label" ><?php echo _('Notes'); ?></td>
                    <td><?php echo str_replace("\r\n", "<br>", $notes); ?></td>
                </tr>                
            </table>
            
            <h2>Customer Details</h2>
            <table id="customer-details" cellpadding="6" cellspacing="0" border="0">
           	<?php
           	if (isset($prompts)) {
            	foreach ($prompts as $p) {
            		?>
                <tr>
                    <td class="label" ><?php echo $p['caption']; ?></td>
                    <td><?php echo $p['value']; ?></td>
                </tr>
							<?php
						  }
					  }
						?>
            </table>
            
        </div>

    </div>
</body>
</html>