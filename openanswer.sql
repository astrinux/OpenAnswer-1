-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 15, 2015 at 01:20 PM
-- Server version: 5.1.71
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `openanswer`
--

-- --------------------------------------------------------

--
-- Table structure for table `ccact_accounts`


CREATE TABLE IF NOT EXISTS `ccact_accounts` (
  `id` int(36) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `description` text,
  `account_name` varchar(255) DEFAULT NULL,
  `account_num` varchar(255) DEFAULT NULL,
  `contact_name` varchar(64) DEFAULT NULL,
  `billing_address1` varchar(255) DEFAULT NULL,
  `billing_address2` varchar(255) DEFAULT NULL,
  `billing_city` varchar(255) DEFAULT NULL,
  `billing_state` varchar(2) DEFAULT NULL,
  `billing_zip` varchar(12) DEFAULT NULL,
  `billing_province` varchar(64) DEFAULT NULL,
  `billing_country` varchar(3) DEFAULT NULL,
  `billing_phone` varchar(32) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `legacy` tinyint(1) NOT NULL,
  `deleted` tinyint(2) DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_name` (`account_name`),
  KEY `deleted` (`deleted`),
  KEY `account_num` (`account_num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_accounts_edits`
--

CREATE TABLE IF NOT EXISTS `ccact_accounts_edits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `calltype_id` int(11) DEFAULT NULL,
  `call_list_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `note_id` int(11) DEFAULT NULL,
  `messages_summary_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_username` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `old_values` longtext,
  `new_values` longtext,
  `change_type` enum('delete','edit','add','recover') NOT NULL,
  `section` enum('account','did','employee','custom','calltype','schedule','oncall','summary','files','employee_contact','note') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_actions`
--

CREATE TABLE IF NOT EXISTS `ccact_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort` tinyint(4) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `action_text` text,
  `action_type` tinyint(4) DEFAULT '0' COMMENT '1=TXF, 2=BLINDTXF, 3=TXTMSG, 4=EMAIL, 5=WEB6=VMAIL',
  `action_url` varchar(255) DEFAULT NULL,
  `eid` text,
  `did_id` int(11) DEFAULT NULL,
  `helper` text,
  `inactive_ts` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `flag` tinyint(1) DEFAULT '0',
  `inactive` tinyint(1) NOT NULL,
  `dispatch_only` tinyint(2) NOT NULL DEFAULT '0',
  `legacy` tinyint(1) NOT NULL,
  `legacy_type` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`),
  KEY `account_id` (`did_id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `inactive_ts` (`inactive_ts`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_app_settings`
--

CREATE TABLE IF NOT EXISTS `ccact_app_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` varchar(512) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field` (`field`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

INSERT INTO `ccact_app_settings` (`id`, `user_id`, `field`, `value`) VALUES
(1, 0, 'minder_warn_time_1', '60'),
(2, 0, 'minder_warn_color_1', '#81f56a'),
(3, 0, 'minder_warn_time_2', '120'),
(4, 0, 'minder_warn_color_2', '#f5f054'),
(5, 0, 'minder_warn_time_3', '180'),
(6, 0, 'minder_warn_color_3', '#f54949'),
(7, 0, 'call_update_seconds', '20'),
(8, 0, 'msg_update_seconds', '20'),
(9, 0, 'heartbeat_seconds', '60'),
(10, 0, 'agent_update_seconds', '10'),
(11, 0, 'undelivered_minutes', '15');


-- --------------------------------------------------------

--
-- Table structure for table `ccact_breaks`
--

CREATE TABLE IF NOT EXISTS `ccact_breaks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_bulletins`
--

CREATE TABLE IF NOT EXISTS `ccact_bulletins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `note` text NOT NULL,
  `required` tinyint(4) DEFAULT '0' COMMENT 'if set to 1 user must acknowledge before proceeding',
  `created_ts` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `required` (`required`),
  KEY `msg_grouping` (`created_by`,`created_ts`),
  KEY `created_ts` ( `created_ts` ) 
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_bulletin_recipients`
--

CREATE TABLE IF NOT EXISTS `ccact_bulletin_recipients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bulletin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ack_ts` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `bulletin_id` ( `bulletin_id` ),
  KEY `user_id` ( `user_id` )

) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_calltypes`
--

CREATE TABLE IF NOT EXISTS `ccact_calltypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `type` varchar(255) DEFAULT NULL,
  `sort` tinyint(2) DEFAULT '0',
  `verbose` tinyint(1) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` tinyint(2) DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  `legacy` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `did_id` (`did_id`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_call_events`
--

CREATE TABLE IF NOT EXISTS `ccact_call_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `call_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `extension` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `description` text NOT NULL,
  `level` tinyint(2) DEFAULT '0' COMMENT 'Visible to: 1=Customer, 10=Operator, 20=Manager, 30=Admin, 40=Superuser',
  `event_type` smallint(6) DEFAULT NULL COMMENT '1=Text, 2=Button Click, 3=Minder Click, 4=Msg edit,5=delivered, 6=undelivered',
  `button_data` text,
  PRIMARY KEY (`id`),
  KEY `message_id` (`call_id`),
  KEY `created` (`created`),
  KEY `event_type` (`event_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_call_lists`
--

CREATE TABLE IF NOT EXISTS `ccact_call_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `hide_from_operator` tinyint(2) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  `inactive` tinyint(2) NOT NULL DEFAULT '0',
  `legacy` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `account_id` (`account_id`),
  KEY `did_id` (`did_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_call_lists_schedules`
--

CREATE TABLE IF NOT EXISTS `ccact_call_lists_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did_id` int(11) NOT NULL,
  `call_list_id` int(11) NOT NULL,
  `notes` text,
  `start_date` datetime DEFAULT NULL COMMENT 'Specific beginning date/ time, i.e June 29, 2002 11:23pm',
  `end_date` datetime DEFAULT NULL,
  `start_day` int(6) unsigned DEFAULT NULL,
  `end_day` int(6) unsigned DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `instructions` text,
  `active` tinyint(1) DEFAULT '1',
  `check_days` tinyint(1) DEFAULT '0',
  `mon` tinyint(1) DEFAULT '0',
  `tue` tinyint(1) DEFAULT '0',
  `wed` tinyint(1) DEFAULT '0',
  `thu` tinyint(1) DEFAULT '0',
  `fri` tinyint(1) DEFAULT '0',
  `sat` tinyint(1) DEFAULT '0',
  `sun` tinyint(1) DEFAULT '0',
  `list_type` varchar(4) DEFAULT NULL COMMENT '2=rotating',
  `employee_ids` text,
  `effective_date` date DEFAULT NULL,
  `created` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_ts` datetime NOT NULL,
  `legacy` tinyint(1) DEFAULT NULL,
  `legacy_list` text,
  PRIMARY KEY (`id`),
  KEY `call_list_id` (`call_list_id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `start_day` (`start_day`),
  KEY `end_day` (`end_day`),
  KEY `did_id` (`did_id`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_call_logs`
--

CREATE TABLE IF NOT EXISTS `ccact_call_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) NOT NULL,
  `did_number` varchar(16) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `extension` varchar(16) DEFAULT NULL,
  `queue` varchar(16) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `wrapup_time` datetime NOT NULL,
  `cid_name` varchar(128) DEFAULT NULL,
  `cid_number` varchar(255) DEFAULT NULL,
  `unique_id` varchar(64) NOT NULL,
  `sip_call_id` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `did_id` (`did_id`),
  KEY `account_id` (`account_id`),
  KEY `start_time` (`start_time`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `ccact_cids`
--

CREATE TABLE IF NOT EXISTS `ccact_cids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number_name` (`number`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_company_audio`
--

CREATE TABLE IF NOT EXISTS `ccact_company_audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did_id` int(11) NOT NULL,
  `company_audio` longblob,
  `company_audio_type` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_complaints`
--

CREATE TABLE IF NOT EXISTS `ccact_complaints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `category_other` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime NOT NULL,
  `incident_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_username` varchar(32) DEFAULT NULL,
  `user_ext` varchar(32) DEFAULT NULL,
  `message_id` int(11) DEFAULT NULL,
  `callers_name` varchar(255) NOT NULL,
  `investigation` text,
  `resolution` text,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `did_id` (`did_id`),
  KEY `message_id` (`message_id`),
  KEY `operator_id` (`operator_id`),
  KEY `incident_date` (`incident_date`),
  KEY `status` (`status`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_complaints_operators`
--

CREATE TABLE IF NOT EXISTS `ccact_complaints_operators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operator_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `operator_username` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_contacts`
--

CREATE TABLE IF NOT EXISTS `ccact_contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `contact_type` tinyint(4) DEFAULT NULL COMMENT '1=phone,2=email,3=text,4=fax, 5=pager',
  `data1` varchar(255) DEFAULT NULL,
  `data2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_did_numbers`
--

CREATE TABLE IF NOT EXISTS `ccact_did_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '0=inactive,1=active',
  `did_number` varchar(16) DEFAULT NULL,
  `industry` varchar(64) DEFAULT NULL,
  `alias_number` varchar(16) DEFAULT NULL,
  `difficulty` varchar(16) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `company_visible` tinyint(1) NOT NULL,
  `businesstype` varchar(64) NOT NULL,
  `date_entered` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `modified_user_id` char(36) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `description` text,
  `assigned_user_id` char(36) DEFAULT NULL,
  `timezone` varchar(100) DEFAULT '1',
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address_visible` tinyint(1) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(12) DEFAULT NULL,
  `province` varchar(64) DEFAULT NULL,
  `main_phone` varchar(16) DEFAULT NULL,
  `main_phone_visible` tinyint(1) NOT NULL,
  `main_fax` varchar(255) DEFAULT NULL,
  `main_fax_visible` tinyint(1) NOT NULL,
  `alt_phone` varchar(255) DEFAULT NULL,
  `alt_phone_visible` tinyint(1) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `website_visible` tinyint(1) NOT NULL,
  `hours` text,
  `hours_visible` tinyint(1) NOT NULL,
  `email` varchar(225) NOT NULL,
  `email_visible` tinyint(1) NOT NULL,
  `type` varchar(100) DEFAULT '1',
  `privacy` varchar(100) DEFAULT '1',
  `country` varchar(255) DEFAULT NULL,
  `answerphrase` varchar(255) DEFAULT NULL,
  `did_color` varchar(1) DEFAULT NULL,
  `has_oncall` tinyint(4) NOT NULL DEFAULT '0',
  `legacy` tinyint(1) DEFAULT NULL,
  `legacy_accountcode` varchar(16) DEFAULT NULL,
  `legacy_dispatch` text,
  `deleted` tinyint(2) DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  `legacy_multi_oncall` tinyint(1) DEFAULT NULL,
  `overflow` int(11) NOT NULL,
  `primary_or_overflow` tinyint(2) NOT NULL COMMENT '0=primary,1=overflow',
  `radio_advertising` tinyint(1) NOT NULL,
  `calls_per_day` smallint(6) NOT NULL,
  `calls_timing` smallint(6) NOT NULL,
  `calls_timing_other` varchar(512) DEFAULT NULL,
  `service_sku` varchar(32) DEFAULT NULL,
  `email_format` tinyint(2) NOT NULL COMMENT '0=html,1=text',
  `email_subject_template` varchar(512) DEFAULT NULL,
  `include_cid` tinyint(2) NOT NULL,
  `exclude_prompts` tinyint(2) NOT NULL,
  `include_msg_id` tinyint(2) NOT NULL,
  `include_call_events` tinyint(2) NOT NULL,
  `scheduling_option` TINYINT NOT NULL, 
  `advanced_setup` tinyint(4) NOT NULL,
  `billto_account` VARCHAR(64) NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `account_id` (`account_id`),
  KEY `did_number` (`did_number`),
  KEY `alias_number` (`alias_number`),
  KEY `legacy` (`legacy`),
  KEY `overflow` (`overflow`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Triggers `ccact_did_numbers`
--
DROP TRIGGER IF EXISTS `update_timezone`;
DELIMITER //
CREATE TRIGGER `update_timezone` AFTER UPDATE ON `ccact_did_numbers`
 FOR EACH ROW BEGIN
    if (NEW.`timezone` <> OLD.`timezone`) THEN
      UPDATE ccact_messages_summary
      SET `did_tz` = NEW.`timezone`
      WHERE `did_id` = NEW.`id`;
    END IF;
  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_did_numbers_entries`
--

CREATE TABLE IF NOT EXISTS `ccact_did_numbers_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did_id` int(11) NOT NULL,
  `number` varchar(32) NOT NULL,
  `alias` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `number` (`number`),
  KEY `did_id` (`did_id`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_emails`
--

CREATE TABLE IF NOT EXISTS `ccact_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `did_id` int(11) NOT NULL,
  `call_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `content_html` text NOT NULL,
  `content_text` text NOT NULL,
  `format` varchar(32) NOT NULL,
  `recipients` text NOT NULL,
  `processed` tinyint(2) NOT NULL COMMENT '0=new,1=processed,2=failed',
  `template` varchar(32) NOT NULL,
  `date_sent` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `processed` (`processed`),
  KEY `created` (`created`),
  KEY `did_id` (`did_id`),
  KEY `call_id` (`call_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_employees`
--

CREATE TABLE IF NOT EXISTS `ccact_employees` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `gender` tinyint(2) DEFAULT '0' COMMENT '0=unknown, 1=female, 2=male',
  `hide_hold` tinyint(1) NOT NULL DEFAULT '0',
  `hide_unsure` tinyint(1) NOT NULL DEFAULT '0',
  `special_instructions` text,
  `deleted` tinyint(1) DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  `legacy` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `account_id` (`account_id`),
  KEY `did_id` (`did_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_employees_contacts`
--

CREATE TABLE IF NOT EXISTS `ccact_employees_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort` tinyint(4) DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `contact_type` int(11) NOT NULL COMMENT '1=phone, 2=cell, 3=email, 4=voicemail, 5=text, 6=web, 7=pager, 8=fax',
  `primary` int(11) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `label` varchar(32) DEFAULT NULL,
  `options` text,
  `flag` tinyint(1) NOT NULL,
  `ext` varchar(10) DEFAULT NULL,
  `carrier` varchar(64) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `legacy` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `did_id` (`did_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_faxes`
--

CREATE TABLE IF NOT EXISTS `ccact_faxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src_fax` varchar(14) NOT NULL,
  `dst_fax` varchar(25) NOT NULL,
  `created` datetime NOT NULL,
  `fax_processed` tinyint(2) NOT NULL COMMENT '0=new,1=processed,2=failed',
  `fax_processed_ts` datetime DEFAULT NULL,
  `fax_retry` smallint(6) NOT NULL,
  `fax_text` text,
  `fax_tif` longblob NOT NULL,
  `account_num` varchar(15) NOT NULL,
  `format` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `src_fax` (`src_fax`),
  KEY `dst_fax` (`dst_fax`),
  KEY `fax_processed` (`fax_processed`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_files`
--

CREATE TABLE IF NOT EXISTS `ccact_files` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `did_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL DEFAULT '',
  `file_name` varchar(250) NOT NULL DEFAULT '',
  `file_type` varchar(15) NOT NULL DEFAULT '',
  `file_size` varchar(45) NOT NULL DEFAULT '',
  `file_content` longblob NOT NULL,
  `file_extension` varchar(10) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `deleted` tinyint(2) NOT NULL,
  `deleted_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `did_id` (`did_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_keys`
--

CREATE TABLE IF NOT EXISTS `ccact_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `public_key` varchar(128) NOT NULL,
  `private_key` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `public_key` (`public_key`,`private_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_logins`
--

CREATE TABLE IF NOT EXISTS `ccact_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(128) NOT NULL,
  `login_type` tinyint(3) NOT NULL,
  `subaccount_id` text,
  `employee_id` text,
  `account_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_messages`
--

CREATE TABLE IF NOT EXISTS `ccact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(64) DEFAULT NULL,
  `extension` int(11) NOT NULL,
  `call_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `calltype` varchar(255) DEFAULT NULL,
  `calltype_id` int(11) DEFAULT NULL,
  `calltype_instructions` text,
  `notes` text,
  `created` datetime DEFAULT NULL,
  `active_ts` datetime DEFAULT NULL,
  `minder` tinyint(1) NOT NULL DEFAULT '0',
  `minder_ts` datetime NOT NULL,
  `unsure` tinyint(1) NOT NULL DEFAULT '0',
  `urgent` tinyint(1) NOT NULL DEFAULT '0',
  `instructions` text,
  `last_eid` int(11) NOT NULL,
  `delivered` tinyint(1) NOT NULL DEFAULT '0',
  `hold` tinyint(4) NOT NULL DEFAULT '0',
  `hold_until` datetime DEFAULT NULL,
  `audited` tinyint(1) NOT NULL,
  `summary_last_sent` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `did_id` (`did_id`),
  KEY `call_id` (`call_id`),
  KEY `user_id` (`user_id`),
  KEY `audited` (`audited`),
  KEY `calltype` (`calltype`),
  KEY `created` (`created`),
  KEY `active_ts` (`active_ts`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_messages_delivery`
--

CREATE TABLE IF NOT EXISTS `ccact_messages_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `delivery_name` varchar(255) DEFAULT NULL,
  `delivery_contact` varchar(255) DEFAULT NULL,
  `delivery_contact_id` text,
  `delivery_contact_label` varchar(32) NOT NULL,
  `delivered_time` datetime DEFAULT NULL,
  `delivered_by_userid` varchar(64) DEFAULT NULL,
  `delivered_by_ext` varchar(16) NOT NULL,
  `delivery_method` varchar(16) DEFAULT NULL,
  `delivered` tinyint(4) NOT NULL,
  `hold` tinyint(4) NOT NULL,
  `urgent` tinyint(4) NOT NULL,
  `employee_id` text,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_messages_prompts`
--

CREATE TABLE IF NOT EXISTS `ccact_messages_prompts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `caption` varchar(1024) NOT NULL,
  `value` varchar(1024) NOT NULL,
  `sort` smallint(4) NOT NULL,
  `ptype` int(11) NOT NULL,
  `maxchar` int(11) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_messages_prompts_edits`
--

CREATE TABLE IF NOT EXISTS `ccact_messages_prompts_edits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `caption` varchar(1024) NOT NULL,
  `value` varchar(1024) NOT NULL,
  `sort` smallint(4) NOT NULL,
  `ptype` int(11) NOT NULL,
  `maxchar` int(11) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(64) DEFAULT NULL,
  `edit_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_messages_summary`
--

CREATE TABLE IF NOT EXISTS `ccact_messages_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) NOT NULL,
  `did_tz` varchar(128) NOT NULL COMMENT 'automatically updated by DB trigger ''update_timezone''',
  `destination_email` varchar(512) NOT NULL,
  `destination_fax` varchar(512) NOT NULL,
  `employee_contact_ids` varchar(512) DEFAULT NULL,
  `all_day` tinyint(1) DEFAULT '0',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `send_time` time DEFAULT NULL,
  `last_sent` datetime DEFAULT '0000-00-00 00:00:00',
  `last_run` datetime DEFAULT NULL,
  `active` tinyint(2) DEFAULT '1',
  `mon` tinyint(1) DEFAULT '0',
  `tue` tinyint(1) DEFAULT '0',
  `wed` tinyint(1) DEFAULT '0',
  `thu` tinyint(1) DEFAULT '0',
  `fri` tinyint(1) DEFAULT '0',
  `sat` tinyint(1) DEFAULT '0',
  `sun` tinyint(1) DEFAULT '0',
  `tx_interval` smallint(6) DEFAULT NULL COMMENT 'interval in minutes',
  `msg_type` tinyint(4) DEFAULT NULL,
  `unreviewed` tinyint(1) NOT NULL,
  `no_message` tinyint(1) NOT NULL,
  `no_message_type` tinyint(4) NOT NULL,
  `no_message_send_time` time DEFAULT NULL,
  `no_message_last_sent` datetime DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  `legacy` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `did_id` (`did_id`),
  KEY `account_id` (`account_id`),
  KEY `all_day` (`all_day`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `send_time` (`send_time`),
  KEY `last_sent` (`last_sent`),
  KEY `active` (`active`),
  KEY `tx_interval` (`tx_interval`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_messages_summary_log`
--

CREATE TABLE IF NOT EXISTS `ccact_messages_summary_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_summary_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) NOT NULL,
  `did_tz` varchar(128) NOT NULL COMMENT 'automatically updated by DB trigger ''update_timezone''',
  `destination_email` varchar(512) NOT NULL,
  `destination_fax` varchar(512) NOT NULL,
  `all_day` tinyint(1) DEFAULT '0',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `send_time` time DEFAULT NULL,
  `last_sent` datetime DEFAULT '0000-00-00 00:00:00',
  `last_run` datetime DEFAULT NULL,
  `active` tinyint(2) DEFAULT '1',
  `mon` tinyint(1) DEFAULT '0',
  `tue` tinyint(1) DEFAULT '0',
  `wed` tinyint(1) DEFAULT '0',
  `thu` tinyint(1) DEFAULT '0',
  `fri` tinyint(1) DEFAULT '0',
  `sat` tinyint(1) DEFAULT '0',
  `sun` tinyint(1) DEFAULT '0',
  `tx_interval` smallint(6) DEFAULT NULL COMMENT 'interval in minutes',
  `msg_type` tinyint(4) DEFAULT NULL,
  `unreviewed` tinyint(1) NOT NULL,
  `no_message` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  `legacy` tinyint(1) NOT NULL,
  `message_ids` text NOT NULL,
  `summary_sent` datetime NOT NULL,
  `summary_sent_to` text,
  `no_message_sent` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `did_id` (`did_id`),
  KEY `account_id` (`account_id`),
  KEY `created` (`created`),
  KEY `last_run` (`last_run`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_messages_summary_sent`
--

CREATE TABLE IF NOT EXISTS `ccact_messages_summary_sent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `messages_summary_id` int(11) NOT NULL,
  `summary_last_sent` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  KEY `summary_last_sent` (`summary_last_sent`),
  KEY `messages_summary_id` (`messages_summary_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_mistakes`
--

CREATE TABLE IF NOT EXISTS `ccact_mistakes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `category_other` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_username` varchar(32) DEFAULT NULL,
  `user_ext` varchar(32) DEFAULT NULL,
  `message_id` int(11) DEFAULT NULL,
  `message_created` datetime NOT NULL,
  `mistake_recipient` smallint(11) NOT NULL,
  `recipient_username` varchar(64) DEFAULT NULL,
  `deleted` tinyint(2) NOT NULL,
  `deleted_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_userid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `did_id` (`did_id`),
  KEY `created` (`created`),
  KEY `message_id` (`message_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_notes`
--

CREATE TABLE IF NOT EXISTS `ccact_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `did_id` int(11) DEFAULT NULL,
  `description` text,
  `created` datetime NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_username` varchar(32) DEFAULT NULL,
  `user_ext` varchar(32) DEFAULT NULL,
  `message_id` int(11) DEFAULT NULL,
  `visible` tinyint(4) NOT NULL,
  `display_location` tinyint(4) NOT NULL,
  `bg_color` VARCHAR( 16 ) NULL,   
  PRIMARY KEY (`id`),
  KEY `did_id` (`did_id`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_outbound`
--

CREATE TABLE IF NOT EXISTS `ccact_outbound` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `extension` varchar(64) DEFAULT NULL,
  `called_num` varchar(64) DEFAULT NULL,
  `created` datetime NOT NULL,
  `call_end` datetime DEFAULT NULL,
  `incoming_unique_id` varchar(64) NOT NULL,
  `did_id` int(11) NOT NULL,
  `call_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `did_id` (`did_id`),
  KEY `created` (`created`),
  KEY `user_id` (`user_id`),
  KEY `call_id` (`call_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_overflow_centers`
--

CREATE TABLE IF NOT EXISTS `ccact_overflow_centers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_prompts`
--

CREATE TABLE IF NOT EXISTS `ccact_prompts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action_id` int(11) DEFAULT NULL,
  `did_id` int(11) NOT NULL,
  `ptype` int(11) DEFAULT NULL COMMENT '1=text, 2=textarea, 3=select',
  `options` text,
  `caption` varchar(512) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `maxchar` int(10) unsigned NOT NULL,
  `required` tinyint(4) NOT NULL,
  `inactive` tinyint(1) NOT NULL,
  `legacy` tinyint(1) NOT NULL,
  `verification` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`),
  KEY `did_id` (`did_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_queues`
--

CREATE TABLE IF NOT EXISTS `ccact_queues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `queue` (`queue`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_schedules`
--

CREATE TABLE IF NOT EXISTS `ccact_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `did_id` int(11) NOT NULL,
  `calltype_id` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL COMMENT 'Specific beginning date/ time, i.e June 29, 2002 11:23pm',
  `end_date` datetime DEFAULT NULL,
  `start_day` int(6) unsigned DEFAULT NULL,
  `end_day` int(6) unsigned DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `instructions` text,
  `active` tinyint(2) DEFAULT '1',
  `check_days` tinyint(1) DEFAULT '0',
  `mon` tinyint(1) DEFAULT '0',
  `tue` tinyint(1) DEFAULT '0',
  `wed` tinyint(1) DEFAULT '0',
  `thu` tinyint(1) DEFAULT '0',
  `fri` tinyint(1) DEFAULT '0',
  `sat` tinyint(1) DEFAULT '0',
  `sun` tinyint(1) DEFAULT '0',
  `type` varchar(8) DEFAULT NULL,
  `show_employee_picker` tinyint(2) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `deleted_ts` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `legacy` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `calltype_id` (`calltype_id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `end_day` (`end_day`),
  KEY `did_id` (`did_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_sms_carriers`
--

CREATE TABLE IF NOT EXISTS `ccact_sms_carriers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `addr` varchar(50) NOT NULL,
  `rank` int(11) NOT NULL,
  `prefix` text,
  PRIMARY KEY (`id`),
  KEY `carriername` (`name`,`addr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=234 ;


INSERT INTO `ccact_sms_carriers` (`id`, `name`, `addr`, `rank`, `prefix`) VALUES
(1, '3 River Wireless', 'sms.3rivers.net', 0, NULL),
(2, 'ACS Wireless / SBC Ameritech', 'paging.acswireless.com', 0, NULL),
(3, 'Alltel ', 'message.alltel.com', 0, NULL),
(8, 'Ameritech Clearpath', 'clearpath.acswireless.com', 0, NULL),
(9, 'AT&T/ Cingular', 'txt.att.net', 10, NULL),
(19, 'Bell South ', 'sms.bellsouth.com', 10, NULL),
(25, 'Bluegrass Cellular ', 'sms.bluecell.com', 0, NULL),
(26, 'Boost', 'myboostmobile.com', 10, NULL),
(27, 'BPL mobile', 'bplmobile.com', 0, NULL),
(28, 'Carolina West Wireless', 'cwwsms.com', 0, NULL),
(33, 'Cellular One', 'mobile.celloneusa.com', 0, NULL),
(39, 'Cellular One West', 'mycellone.com', 0, NULL),
(40, 'Cellular South', 'csouth1.com', 0, NULL),
(41, 'Centennial Wireless', 'cwemail.com', 0, NULL),
(43, 'CenturyTel', 'messaging.centurytel.net', 0, NULL),
(46, 'Cincinnati Bell Wireless', 'gocbw.com', 0, NULL),
(55, 'Clearnet', 'msg.clearnet.com', 0, NULL),
(56, 'Comcast ', 'comcastpcs.textmsg.com', 0, NULL),
(68, 'Edge Wireless', 'sms.edgewireless.com', 0, NULL),
(78, 'GTE ', 'messagealert.com', 0, NULL),
(80, 'Houston Cellular', 'text.houstoncellular.net', 0, NULL),
(81, 'Idea Cellular', 'ideacellular.net', 0, NULL),
(85, 'Kerala Escotel', 'escotelmobile.com', 0, NULL),
(86, 'Kolkata Airtel', 'airtelkol.com', 0, NULL),
(87, 'LMT', 'smsmail.lmt.lv', 0, NULL),
(90, 'Manitoba Telecom Systems', 'text.mtsmobility.com', 0, NULL),
(91, 'MCI Phone ', 'mci.com', 0, NULL),
(92, 'MCI ', 'pagemci.com', 0, NULL),
(93, 'Metrocall', 'page.metrocall.com', 0, NULL),
(95, 'Metro PCS ', 'mymetropcs.com', 10, NULL),
(217, 'Cricket', 'sms.mycricket.com', 0, NULL),
(98, 'Midwest Wireless', 'clearlydigital.com', 0, NULL),
(100, 'Mobilecomm', 'mobilecomm.net', 0, NULL),
(114, 'Nextel', 'messaging.nextel.com', 0, NULL),
(122, 'Omnipoint', 'omnipointpcs.com', 0, NULL),
(131, 'Pacific Bell', 'pacbellpcs.net', 0, NULL),
(136, 'PageOne NorthWest', 'page1nw.com', 0, NULL),
(137, 'PCS One', 'pcsone.net', 0, NULL),
(138, 'Pioneer / Enid Cellular', 'msg.pioneerenidcellular.com', 0, NULL),
(143, 'Primeco/US Cellular', 'email.uscc.net', 0, NULL),
(147, 'Qwest', 'qwestmp.com', 0, NULL),
(160, 'Southern LINC', 'page.southernlinc.com', 0, NULL),
(161, 'Southwestern Bell', 'email.swbw.com', 0, NULL),
(219, 'Verizon MMS', 'vzwpix.com', 0, NULL),
(163, 'Sprint PCS/ TracFone', 'messaging.sprintpcs.com', 10, NULL),
(168, 'Surewest Communicaitons', 'mobile.surewest.com', 0, NULL),
(170, 'T-Mobile', 'tmomail.net', 10, NULL),
(218, 'Verizon PCS or Straight Talk', 'vtext.com', 0, NULL),
(181, 'Telus', 'msg.telus.com', 0, NULL),
(192, 'US West', 'uswestdatamail.com', 0, NULL),
(199, 'Virgin Mobile', 'vmobl.com', 0, NULL),
(205, 'VoiceStream / T-Mobile/ Powertel', 'voicestream.net', 0, NULL),
(208, 'West Central Wireless', 'sms.wcc.net', 0, NULL),
(209, 'Western Wireless', 'cellularonewest.com', 0, NULL),
(210, 'Wyndtell', 'wyndtell.com', 0, NULL),
(220, 'Sprint MMS', 'pm.sprint.com', 2, NULL),
(221, 'AT&T MMS', 'mms.att.net', 0, NULL),
(222, 'Virgin Mobile MMS', 'vmpix.com', 2, NULL),
(223, '3 River MMS', 'mms.3rivers.net', 2, NULL),
(224, 'Cellcom', 'cellcom.quiktxt.com', 0, NULL),
(225, 'Cricket MMS', 'mms.cricketwireless.net', 1, NULL),
(226, 'Straight Talk MMS', 'mypixmessages.com', 0, NULL),
(227, 'Metro PCS (Alt)', 'metropcs.sms.us', 0, NULL),
(228, 'Metro PCS (Alt2)', 'tmomail.net', 0, '1'),
(229, 'PCS Rogers', 'pcs.rogers.com', 0, NULL),
(230, 'Entel', 'entelpcs.cl', 0, NULL),
(231, 'nTelos Wireless', 'pcs.ntelos.com', 0, NULL),
(232, 'Sprint', 'sprintpaging.com', 0, NULL),
(233, 'Telestial', 'telestial.com', 0, '');
-- --------------------------------------------------------

--
-- Table structure for table `ccact_users`
--

CREATE TABLE IF NOT EXISTS `ccact_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL,
  `extension` varchar(16) NOT NULL,
  `role` char(1) NOT NULL,
  `firstname` varchar(128) NOT NULL,
  `lastname` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `subaccount_id` text,
  `employee_id` text,
  `created` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL,
  `deleted_ts` datetime NOT NULL,
  `operator` tinyint(4) NOT NULL,
  `display_stat` tinyint(2) NOT NULL,
  `alias` varchar(64) DEFAULT NULL,
  `monitor` tinyint(2) NOT NULL,  
  `add_account_notes` TINYINT( 2 ) NOT NULL,
  `photo` LONGBLOB NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`,`password`,`extension`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;


INSERT INTO `ccact_users` (`id`, `username`, `password`, `name`, `extension`, `role`, `firstname`, `lastname`, `email`, `account_id`, `subaccount_id`, `employee_id`, `created`, `deleted`, `deleted_ts`, `operator`, `display_stat`, `alias`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', '', '', 'A', 'Admin', 'User', '', NULL, NULL, NULL, NOW(), 0, '0000-00-00 00:00:00', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ccact_users_queues`
--

CREATE TABLE IF NOT EXISTS `ccact_users_queues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `penalty` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `operator_id` (`user_id`,`queue`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_user_groups`
--

CREATE TABLE IF NOT EXISTS `ccact_user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `user_ids` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_user_log`
--

CREATE TABLE IF NOT EXISTS `ccact_user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `type` enum('login','logout','break','start_shift','end_shift','taking_calls','not_taking_calls','leave_break','not_taking_calls_btn','taking_calls_btn','refresh_browser') DEFAULT NULL,
  `log_type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `extension` varchar(16) NOT NULL,
  `break_reason` varchar(255) DEFAULT NULL,
  `break_end` datetime DEFAULT NULL,
  `session` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`),
  KEY `user_id` (`user_id`),
  KEY `extension` (`extension`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ccact_welcome_msgs`
--

CREATE TABLE IF NOT EXISTS `ccact_welcome_msgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

INSERT INTO `ccact_welcome_msgs` (`id`, `created`, `note`) VALUES
(3, '2014-10-13 00:00:00', '"If you are going to achieve excellence in big things, you develop the habit in little matters. Excellence is not an exception, it is a prevailing attitude."\n- Colin Powell'),
(2, '2014-10-13 10:24:18', '"Hope lies in dreams, in imagination, and in the courage of those who dare to make dreams into reality."\n<br> - Jonas Salk'),
(4, '2014-10-13 00:00:00', '"The three great essentials to achieve anything worth while are: Hard work, Stick-to-itiveness, and Common sense."\n- Thomas A. Edison'),
(5, '2014-10-13 00:00:00', '"How we think shows through in how we act. Attitudes are mirrors of the mind. They reflect thinking."\n- David Joseph Schwartz'),
(6, '2014-10-13 00:00:00', '"If your actions inspire others to dream more, learn more, do more and become more, you are a leader."\n- John Quincy Adams');






