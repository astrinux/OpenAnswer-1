<?php echo $appointment_service . "\r\n"; ?>
<?php echo _('#') . ': ' . $confirmation . "\r\n"; ?>
<?php echo _('Provider') . ': ' . $appointment_provider . "\r\n"; ?>
<?php echo _('Start') . ': ' . $appointment_start_date . "\r\n"; ?>
<?php echo _('End') . ': ' . $appointment_end_date . "\r\n"; ?>
<?php echo _('Notes') . ': ' . $notes . "\r\n"; ?>
<?php
            foreach ($prompts as $p) {
            		echo $p['caption'] . ' ' . $p['value'] . "\r\n"; 
						}
						?>
