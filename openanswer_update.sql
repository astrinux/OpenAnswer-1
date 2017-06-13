

ALTER TABLE `ccact_prompts` ADD `verification` TINYINT NOT NULL ;
ALTER TABLE `ccact_messages_prompts` ADD `verification` TINYINT NOT NULL;
/*ALTER TABLE `ccact_did_numbers` DROP `legacy_multi_oncall`
ALTER TABLE `ccact_did_numbers` DROP `legacy_accountcode`*/
ALTER TABLE `ccact_did_numbers` ADD `billto_account` VARCHAR( 64 ) NULL ;
ALTER TABLE `ccact_notes` ADD `display_location` tinyint(4) NOT NULL ;
ALTER TABLE `ccact_users` ADD `add_account_notes` TINYINT( 2 ) NOT NULL; 
ALTER TABLE `ccact_notes` ADD `bg_color` VARCHAR( 16 ) NULL;
ALTER TABLE `ccact_users` ADD `photo` LONGBLOB NULL ;
ALTER TABLE `ccact_mistakes` ADD `message_created` DATETIME NOT NULL AFTER `message_id`;
update ccact_mistakes m left join ccact_messages mm on mm.id=m.message_id set m.message_created = mm.created;

ALTER TABLE `ccact_mistakes` ADD INDEX ( `message_created` ); 
ALTER TABLE `ccact_messages_summary` ADD INDEX ( `tx_interval` ); 
ALTER TABLE `ccact_messages_prompts_edits` CHANGE `value` `value` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 
ALTER TABLE `ccact_messages_prompts` CHANGE `value` `value` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 
ALTER TABLE `ccact_prompts` CHANGE `maxchar` `maxchar` INT UNSIGNED NOT NULL ;
ALTER TABLE `ccact_bulletins` ADD INDEX ( `created_ts` );
ALTER TABLE `ccact_bulletin_recipients` ADD INDEX ( `bulletin_id` );
ALTER TABLE `ccact_bulletin_recipients` ADD INDEX ( `user_id` ) ;
ALTER TABLE `ccact_call_lists_schedules` ADD INDEX ( `call_list_id` ) ;
ALTER TABLE `ccact_call_logs` ADD `sip_call_id` varchar(128) DEFAULT NULL,