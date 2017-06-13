<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
define('ACTION_TXF', '1');
define('ACTION_BLINDTXF', '2');
define('ACTION_TXTMSG', '3');
define('ACTION_EMAIL', '4');
define('ACTION_WEB', '5');
define('ACTION_VMOFFER', '6');
define('ACTION_HOLD', '7');
define('ACTION_EMAIL_DELIVER', '8');
define('ACTION_TEXT_DELIVER', '9');
define('ACTION_DELIVER', '10');
define('ACTION_LMR', '11');
define('ACTION_DISPATCH', '12');
define('ACTION_EMAIL_MINDER', '13');
define('ACTION_PROMPTS', '30');
define('ACTION_LABEL', '31');
define('ACTION_NONE', '40');
define('ACTION_INFO', '45');
define('ACTION_LEGACY', '50');
define('ACTION_TXF_DELIVER', '14');
define('ACTION_BLINDTXF_DELIVER', '15');
define('ACTION_FAX', '16');
define('ACTION_FAX_DELIVER', '17');
define('ACTION_LMR_DELIVER', '18');
define('ACTION_HOLDTIL', '19');
define('ACTION_VM', '20');
define('ACTION_VM_DELIVER', '21');
define('ACTION_VMOFFER_DELIVER', '22');
define('ACTION_TXF_NO_ANNOUNCEMENT', '23');
define('ACTION_TXF_NO_ANNOUNCEMENT_DELIVER', '24');
define('ACTION_CALENDAR', '25');
define('ACTION_CALENDAR_DELIVER', '26');
//Actions 6* are assigned for CRM interactions only
define('ACTION_CRM', '60');

define('BREAK_REASON1', 'Bathroom Break');
define('BREAK_REASON2', 'Snack Break');
define('BREAK_REASON3', 'Dispatching Duties');
define('BREAK_REASON4', 'Lunch');
define('BREAK_REASON5', 'Mentor Meeting');
define('BREAK_REASON6', 'Staff Meeting');
define('BREAK_REASON7', 'Start of Shift');
define('BREAK_REASON8', 'End of Shift');
define('BREAK_REASON9', 'Technical Issue');
define('BREAK_REASON10', 'Training');
define('BREAK_REASON11', 'Other');
define('BREAK_REASON12', 'EVP');

define('EVENT_TEXT', 1);
define('EVENT_BTNCLICK', 2);
define('EVENT_MINDERCLICK', 3);
define('EVENT_MSGEDITED', 4);
define('EVENT_DELIVER', 5);
define('EVENT_UNDELIVER', 6);
define('EVENT_MINDER', 7);
define('EVENT_UNMINDER', 8);

define('EVENT_CALLSTART', 10);
define('EVENT_CALLEND', 11);
define('EVENT_TRANSFER', 12);
define('EVENT_DELIVERY', 13);
define('EVENT_HANGUP', 14);
define('EVENT_ADMIN', 15);
define('EVENT_CUSTOM', 16);
define('EVENT_ACTIONCLICK', 17);
define('EVENT_DIALOUT', 18);
define('EVENT_PATCH', 19);
define('EVENT_HOLD', 21);
define('EVENT_UNHOLD', 22);
define('EVENT_FILL_PROMPT', 23);
define('EVENT_CALLTYPE', 24);
define('EVENT_AUDIT', 25);
define('EVENT_CALENDAR', 26);
define('EVENT_REPOP', 27);
define('EVENT_OTHER', 99);
define('EVENT_DEBUG', 100);

define('USEREVT_LOGIN' , 1);
define('USEREVT_LOGOUT' , 2);
define('USEREVT_BREAK' , 3);
define('USEREVT_START_SHIFT' , 4);
define('USEREVT_END_SHIFT' , 5);
define('USEREVT_TAKING_CALLS' , 6);
define('USEREVT_NOT_TAKING_CALLS' , 7);
define('USEREVT_LEAVE_BREAK' , 8);
define('USEREVT_NOT_TAKING_CALLS_BTN' , 9);
define('USEREVT_TAKING_CALLS_BTN' , 10);
define('USEREVT_REFRESH_BROWSER' , 11);

define('CONTACT_PHONE', '1');
define('CONTACT_CELL', '2');
define('CONTACT_EMAIL', '3');
define('CONTACT_VMAIL', '4');
define('CONTACT_TEXT', '5');
define('CONTACT_WEB', '6');
define('CONTACT_PAGER', '7');
define('CONTACT_FAX', '8');
define('CONTACT_LMR', '11');
define('CONTACT_CALENDAR', '12');
define('BUTTON_DISPATCH', '9');
define('BUTTON_DELIVER', '10');

define('EVT_LVL_CUSTOMER', 1);
define('EVT_LVL_OPERATOR', 10);
define('EVT_LVL_MANAGER', 20);
define('EVT_LVL_ADMIN', 30);
define('EVT_LVL_SUPERUSER', 40);

define('FAX_EMAIL', '');
define('PROMPT_NAME', 'First and Last Name');
define('PROMPT_MISC', 'Misc');
define('PROMPT_PHONE', 'Phone Number'); 
define('CALLOUT_SUCCESS', '1');

define('MSG_EMAIL_SUBJECT', 'Answering Service Message');

App::uses('Controller', 'Controller');
App::import('Lib', 'DebugKit.FireCake');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $helpers = array('Permissions');
    public $theme;
    public $layout;
    public $user_extension;
    public $acceptable_extensions = array("pdf", "jpg", "png", "gif", "doc", "xls", "xlsx", "txt", "csv", "docx", "mp4", "mpeg", "mp3", "wav");
    public $customer_actions = array('index', 'edit', 'add', 'delete');
    
    public $components = array(
        'Session',
        'RequestHandler',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'openAnswer', 'action' => 'index'),
            'authError' => 'Please enter your username and password...',
            'authenticate' => array (
                'Form' => array('scope' => array('User.deleted' => 0))
            )
        )
     
    );
    
    public $js_daysofweek = array('0' => 'Sunday', '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday');
    
    public $php_daysofweek = array('1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday');  

    public $contact_types = array(CONTACT_PHONE => 'phone', CONTACT_CELL => 'cell', CONTACT_EMAIL => 'email', CONTACT_VMAIL => 'voicemail', CONTACT_TEXT => 'text', CONTACT_LMR => 'LMR');
    
    var $slabel_general = "Request to speak with an employee";
    var $slabel_general_id = "1";
    var $slabel_emergency_id = "7";
    var $scaption_general = "Req Staff";
    var $slabel_emergency = "Emergencies (fire, flood, or medical ONLY)";
    var $slabel_default = "Message";
    var $slabel_id = '12';
    var $slabel_generic = '10';
    var $slabel_urgent_id = '25';
    var $slabel_unknown_id = '24';
    public $all_actions = array( 
            ACTION_LEGACY => '', 
            ACTION_PROMPTS => '',  
            ACTION_NONE => '',  
            ACTION_INFO => '',  
            ACTION_TXF =>  'Transfer to', 
            ACTION_TXF_DELIVER =>  'Transfer (and DELIVER)',
            ACTION_BLINDTXF =>  'Blind Transfer to',
            ACTION_BLINDTXF_DELIVER =>  'Blind Transfer to (and DELIVER)', 
            ACTION_EMAIL =>  'Email',
            ACTION_EMAIL_DELIVER =>  'Email (and DELIVER)',
            ACTION_VMOFFER =>  'Offer voicemail of',
            ACTION_VMOFFER_DELIVER =>  'Offer voicemail of (and DELIVER)' ,
            ACTION_VM =>  'Send to voicemail of',
            ACTION_VM_DELIVER =>  'Send to voicemail of (and DELIVER)',
            ACTION_HOLD =>  'Save & Hold',
            ACTION_TXTMSG =>  'Text to',
            ACTION_TEXT_DELIVER =>  'Text (and DELIVER)',
            ACTION_DELIVER =>  'Mark as DELIVERED',
            ACTION_LMR =>  'LMR to',
            ACTION_LMR_DELIVER =>  'LMR to (and DELIVER)',
            ACTION_DISPATCH =>  'Send to DISPATCH',
            ACTION_WEB =>  'Fill web form',
            ACTION_FAX =>  'Fax',
            ACTION_FAX_DELIVER =>  'Fax (and DELIVER)',
            ACTION_TXF_NO_ANNOUNCEMENT => 'Transfer w/o announcement',
            ACTION_TXF_NO_ANNOUNCEMENT_DELIVER => 'Transfer w/o announcement (AND DELIVER)',
            ACTION_CALENDAR => 'Schedule appointment for',
      ACTION_CALENDAR_DELIVER => 'Schedule (and DELIVER) appointment for',
      ACTION_CRM => 'CRM'
        );
    public $dashboard_actions = array( 
            ACTION_TXF =>  'Transfer to', 
            ACTION_BLINDTXF =>  'Blind Transfer to',
            ACTION_EMAIL =>  'Email',
            ACTION_VM =>  'Send to voicemail of',
            ACTION_VMOFFER =>  'Offer voicemail of',
            ACTION_HOLD =>  'Save & Hold',
            ACTION_TXTMSG =>  'Text to',
            ACTION_INFO =>  'Do not take a message',
            ACTION_WEB =>  'Fill web form'
        );
    public $dashboard_actions_msg = array( 
            ACTION_EMAIL =>  'Email',
            ACTION_HOLD =>  'Save & Hold',
            ACTION_TXTMSG =>  'Text to',
            ACTION_INFO =>  'Do not take a message'
        );    

    public $wizard_actions = array( 
            ACTION_TXF =>  'Transfer the call', 
            ACTION_BLINDTXF =>  'Blind Transfer the call (transfer without collecting caller information)',
            ACTION_EMAIL =>  'Collect information and send an email',
            ACTION_VMOFFER =>  'Offer voicemail',
            ACTION_VM =>  'Send to voicemail of',
            ACTION_TXTMSG =>  'Collect information and send a text',
            ACTION_WEB =>  'Fill out a web form'
        );
    
    public $global_options = array(
        'security_questions' => array(
            'Pin Number' => 'Pin Number',
            'Last 4 of SSN' => 'Last 4 of SSN',
            'What city were you born in?' => 'What city were you born in?'
        ),
        'email_format' => array(
            '0' => 'html',
            '1' => 'text'
        ),
        'smtp_profiles' => array(
            'default' => 'default',
            'alternate' => 'alternate',
            'admin2' => 'alternate',
            'secure_only' => 'secure_only',
        ),
        'callout_results' => array(
            CALLOUT_SUCCESS => 'Successful', 
            '2' => 'Left Message on Voicemail', 
            '3' => 'Left Message to Call Back on Voicemail', 
            '4' => 'Recipient asked to Redirect', 
            '5' => 'No Answer/ Out of Range', 
            '6' => 'Busy Signal', 
            '7' => 'Out of Order/ Invalid Number', 
            '8' => 'Disconnected', 
            '9' => 'Number in Pager', 
            '10' => 'Paged to Service',
            '11' => 'Looped'
         ),    
        'cancel_reasons' => array(
            'Caller hungup' => 'The caller hung up', 
            'Dead air' => 'Dead air',
            'Automated call' => 'Automated call',
            'Telemarketer' => 'Telemarketer',
            'No-message calltype' => 'No-message calltype',
            'Wrong number' => 'Wrong number', 
            'Wanted basic info' => 'Wanted basic info',
            'Forwarding lines' => 'Forwarding lines',
            'Test call' => 'Test call', 
            'Unruly caller' => 'Unruly caller',
            'Repeat caller refused' => 'Repeat caller refused',
            'Client retrieving MSG' => 'Client retrieving MSG',
            'Caller will try alt contact' => 'Caller will try alt contact',
            'Looped'=> 'Looped',
            'Webform' => 'Webform',
            'Will call back' => 'Caller will call back'
         ),           
         'icons' => array(
            'welcome' => 'fa-home',
            'accounts' => 'fa-bars',
            'setup' => 'fa-cogs',
            'reports' => 'fa-bar-chart',
            'calls' => 'fa-list',
            'messages' => 'fa-envelope', 
            'complaints' => 'fa-thumbs-down',
            'mistakes' => 'fa-times',
            'bulletins' => 'fa-thumb-tack',
            'campaigns' => 'fa-share-square-o',
            'users' => 'fa-users',
            'settings' => 'fa-wrench',
            'logout' => 'fa-power-off'
         ),
        'contact_types' => array(
            CONTACT_PHONE => 'Phone',
            CONTACT_CELL => 'Cell',
            CONTACT_EMAIL => 'Email',
            CONTACT_VMAIL => 'Vmail',
            CONTACT_TEXT => 'Text',
            CONTACT_FAX => 'Fax',
            CONTACT_WEB => 'Webform',
            CONTACT_LMR => 'LMR',
            CONTACT_CALENDAR => 'Calendar'
        ),
        'actions' => array( 
            ACTION_LEGACY => array('label' => '', 'need_eid' => false, 'show' => false, 'type' => 'ACTION_LEGACY'), 
            ACTION_PROMPTS => array('label' => '', 'need_eid' => false, 'show' => false, 'type' => 'ACTION_PROMPTS'),  
            ACTION_NONE => array('label' => '', 'need_eid' => false, 'show' => false,'type' => 'ACTION_NONE'),  
            ACTION_INFO => array('label' => '', 'need_eid' => false, 'show' => false,'type' => 'ACTION_INFO'),  
            ACTION_TXF => array('action_label' => 'Transfer', 'label' => 'Transfer to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TXF'), 
            ACTION_TXF_DELIVER => array('action_label' => 'Transfer', 'label' => 'Transfer (and DELIVER) to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TXF_DELIVER'),
            ACTION_BLINDTXF => array('action_label' => 'Transfer', 'label' => 'Blind Transfer to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_BLINDTXF'),
            ACTION_BLINDTXF_DELIVER => array('action_label' => 'Transfer', 'label' => 'Blind Transfer to (and DELIVER)', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_BLINDTXF_DELIVER'), 
            ACTION_TXF_NO_ANNOUNCEMENT => array('action_label' => 'Transfer', 'label' => 'Txf w/o announcement', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TXF_NO_ANNOUNCEMENT'),
            ACTION_TXF_NO_ANNOUNCEMENT_DELIVER => array('action_label' => 'Transfer', 'label' => 'Txf w/o announcement (and DELIVER)', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TXF_NO_ANNOUNCEMENT_DELIVER'), 
            ACTION_EMAIL => array('action_label' => 'Email', 'label' => 'Email', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_EMAIL'),
            ACTION_EMAIL_DELIVER => array('action_label' => 'Email', 'label' => 'Email (and DELIVER)', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_EMAIL_DELIVER'),
            ACTION_VMOFFER => array('label' => 'Offer voicemail of', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_VMOFFER'),
            ACTION_VMOFFER_DELIVER => array('label' => 'Offer voicemail of (AND DELIVER)', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_VMOFFER_DELIVER'),
            ACTION_VM => array('action_label' => 'Voicemail', 'label' => 'Send to voicemail of', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_VM'),
            ACTION_VM_DELIVER => array('action_label' => 'Transfer', 'label' => 'Send to voicemail of (and DELIVER)', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_VM_DELIVER'),
            ACTION_HOLD => array('action_label' => 'Save and Hold', 'label' => 'Save & Hold', 'need_eid' => false, 'show' => true, 'type' => 'ACTION_HOLD'),
            ACTION_TXTMSG => array('action_label' => 'Text', 'label' => 'Text to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TXTMSG'),
            ACTION_TEXT_DELIVER => array('action_label' => 'Text', 'label' => 'Text (and DELIVER) to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TEXT_DELIVER'),
            ACTION_DELIVER => array('label' => 'Mark as DELIVERED', 'need_eid' => false, 'show' => true, 'type' => 'ACTION_DELIVER'),
            ACTION_LMR => array('action_label' => 'LMR', 'label' => 'LMR to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_LMR'),
            ACTION_LMR_DELIVER => array('action_label' => 'LMR', 'label' => 'LMR to (and DELIVER)', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_LMR_DELIVER'),
            ACTION_DISPATCH => array('label' => 'Send to DISPATCH', 'need_eid' => false, 'show' => true, 'type' => 'ACTION_DISPATCH'),
            ACTION_WEB => array('action_label' => 'Web form', 'label' => 'Fill web form', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_WEB'),
            ACTION_FAX => array('action_label' => 'Fax', 'label' => 'Fax', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_FAX'),
            ACTION_FAX_DELIVER => array('action_label' => 'Fax', 'label' => 'Fax (and DELIVER)', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_FAX_DELIVER'),
            ACTION_CALENDAR => array('label' => 'Schedule appointment for', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_CALENDAR'),
            ACTION_CRM => array('action_label'=> 'Contacts','label' => 'CRM', 'need_eid' => false, 'show' => true, 'type' => 'ACTION_CRM')
        ),
        'actions_customers' => array( 
            ACTION_LEGACY => array('label' => '', 'need_eid' => false, 'show' => false, 'type' => 'ACTION_LEGACY'), 
            ACTION_PROMPTS => array('label' => '', 'need_eid' => false, 'show' => false, 'type' => 'ACTION_PROMPTS'),  
            ACTION_NONE => array('label' => '', 'need_eid' => false, 'show' => false,'type' => 'ACTION_NONE'),  
            ACTION_INFO => array('label' => '', 'need_eid' => false, 'show' => false,'type' => 'ACTION_INFO'),  
            ACTION_TXF => array('label' => 'Transfer the call to to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TXF'), 
            ACTION_TXF_DELIVER => array('label' => 'Transfer the call to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TXF_DELIVER'),
            ACTION_BLINDTXF => array('label' => 'Blind transfer to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_BLINDTXF'),
            ACTION_BLINDTXF_DELIVER => array('label' => 'Blind transfer the call to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_BLINDTXF_DELIVER'), 
            ACTION_EMAIL => array('label' => 'Email', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_EMAIL'),
            ACTION_EMAIL_DELIVER => array('label' => 'Email caller info to ', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_EMAIL_DELIVER'),
            ACTION_VMOFFER => array('label' => 'Offer voicemail of', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_VMOFFER'),
            ACTION_VM => array('label' => 'Send to voicemail of', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_VM'),
            ACTION_VM_DELIVER => array('label' => 'Send to voicemail of (and DELIVER)', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_VM_DELIVER'),
            ACTION_HOLD => array('label' => 'Save & Hold', 'need_eid' => false, 'show' => true, 'type' => 'ACTION_HOLD'),
            ACTION_TXTMSG => array('label' => 'Text caller info to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TXTMSG'),
            ACTION_TEXT_DELIVER => array('label' => 'Text caller info to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_TEXT_DELIVER'),
            ACTION_DELIVER => array('label' => 'Mark as DELIVERED', 'need_eid' => false, 'show' => true, 'type' => 'ACTION_DELIVER'),
            ACTION_LMR => array('label' => 'LMR to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_LMR'),
            ACTION_LMR_DELIVER => array('label' => 'Live message relay to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_LMR_DELIVER'),
            ACTION_DISPATCH => array('label' => 'Send to DISPATCH', 'need_eid' => false, 'show' => true, 'type' => 'ACTION_DISPATCH'),
            ACTION_WEB => array('label' => 'Fill web form', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_WEB'),
            ACTION_FAX => array('label' => 'Fax', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_FAX'),
            ACTION_FAX_DELIVER => array('label' => 'Fax caller info to', 'need_eid' => true, 'show' => true, 'type' => 'ACTION_FAX_DELIVER')
        ),    
        'privacy' => array('1' => 'Public', '2' => 'Private'), 
        'type' => array('1' => 'Receptionist', '2' => 'Answering Service'),
/*        'php_timezone' => array ('1' => 'America/New_York', '2' => 'America/Chicago', '3' => 'America/Boise', '4' => 'America/Los_Angeles', '5' => 'Pacific/Honolulu', '6' => 'America/Anchorage', '7' => 'Europe/Berlin', '8' => 'Australia/Sydney'),*/
        'timezone' => array ('America/New_York' => 'Eastern Time', 'EST' => 'Eastern Time (no DST)', 'America/Chicago' => 'Central Time', 'America/Boise' => 'Mountain Time', 'MST' => 'Mountain Time (no DST)','America/Los_Angeles' => 'Pacific Time', 'Pacific/Honolulu' => 'Hawaii', 'America/Anchorage' => 'Alaska', 'Europe/Berlin' => 'Central Europe', 'Australia/Sydney' => 'Australia', 'Asia/Hong_Kong' => 'China', 'Pacific/Guam' => 'Guam'),
        'difficulty' => array('1'=>'1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10','11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20'),
        'answerphrases' => array(
            'Thank you for calling [Company Name]. How may I help you?' => 'Thank you for calling [Company Name]. How may I help you?',
            'Thank you for calling [Company Name]. How may I direct your call?' => 'Thank you for calling [Company Name]. How may I direct your call?',
            'Thank you for calling [Company Name].' => 'Thank you for calling [Company Name].',
            'Thank you for calling [Company Name].  This is [o], how may I help you?' =>   'Thank you for calling [Company Name].  This is [Operator Name], how may I help you?',
            '[Company Name] how may I help you?' => '[Company Name] how may I help you?',
            '[Company Name] how may I direct your call?' => '[Company Name] how may I direct your call?',
            '[Company Name] this is [o].  How may I help you?' => '[Company Name] this is [Operator Name].  How may I help you?',
            '[Company Name], this is [o].' => '[Company Name], this is [Operator Name].',
            '[Company Name].' => '[Company Name].',
            '[Company Name] answering service.  This is [o].  How may I help you?' => '[Company Name] answering service.  This is [Operator Name].  How may I help you?',
            '[Company Name] answering service.  How may I help you?' => '[Company Name] answering service.  How may I help you?',
            'Good [m], thank you for calling [Company Name].  This is [o].  How may I help you?'=>'Good [Afternoon/Morning], thank you for calling [Company Name].  This is [Operator Name].  How may I help you?',
            'Good [m], thank you for calling [Company Name].  How may I help you?' => 'Good [Afternoon/Morning], thank you for calling [Company Name].  How may I help you?',
            'Good [m], this is [o], how may I help you?' => 'Good [Afternoon/Morning], this is [Operator Name], how may I help you?',
            'Thank you for calling [Company Name] After Hours.  This is [o].  How may I help you?' => 'Thank you for calling [Company Name] After Hours.  This is [Operator Name]',
            'Thank you for calling, [Company Name], this is [o]. How may I direct your call?' => 'Thank you for calling, [Company Name], this is [Operator Name]. How may I direct your call?'
        
            ),
            'gender' => array('0' => 'Not specified', '1' => 'Female', '2' => 'Male'),
            'did_colors' => array('G' => 'Green', 'B' => 'Blue', 'W' => 'White', 'P' => 'Purple', 'R' => 'Red', 'Y' => 'Yellow', 'O' => 'Orange'),
            'industries' => array(
                'Accounting' => 'Accounting',
                'Automotive' => 'Automotive',
                'Aviation' => 'Aviation',
                'Construction' => 'Construction',
                'Consulting' => 'Consulting',
                'Dance Studio' => 'Dance Studio',
                'Dental' => 'Dental',
                'Disaster Recovery' => 'Disaster Recovery',
                'Education' => 'Education',
                'Electrical' => 'Electrical',
                'Entertainment' => 'Entertainment',
                'Excavation/ Demolition' => 'Excavation/ Demolition',
                'Finance' => 'Finance',
                'Government' => 'Government',
                'Health & Fitness' => 'Health & Fitness',
                'Hospitality' => 'Hospitality',
                'HVAC/Heating and Air' => 'HVAC/Heating and Air',
                'Insurance' => 'Insurance',
                'Information Technology' => 'Information Technology',
                'Janitorial' => 'Janitorial',
                'Landscaping' => 'Landscaping',
                'Legal' => 'Legal',
                'Maintenance' => 'Maintenance',
                'Manufacturing' => 'Manufacturing',
                'Marketing' => 'Marketing',
                'Media' => 'Media',
                'Medical' => 'Medical',
                'Music Studio' => 'Music Studio',
                'Non-Profit' => 'Non-Profit',
                'Oil & Gas' => 'Oil & Gas',
                'Plumbing' => 'Plumbing',
                'Property Management' => 'Property Management',
                'Real Estate' => 'Real Estate',
                'Restaurant' => 'Restaurant',
                'Retail' => 'Retail',
                'Security' => 'Security',
                'Software' => 'Software',
                'Web Hosting' => 'Web Hosting',
                'Other' => 'Other'
            ),
            'prompts'  => array(
                '1' => array('caption' => PROMPT_NAME, 'description' => 'First and Last Name', 'class' => 'ALL'),
                '2' => array('caption' => PROMPT_PHONE, 'description' =>  'Phone Number', 'class' => 'ALL'),
                '4' => array('caption' => 'Account #', 'description' => 'Account Number'),
                '5' => array('caption' => 'Additional Questions', 'description' => 'Additional Questions', 'class' => 'ALL'),
                '6' => array('caption' => 'Alternate Number', 'description' => 'Alternate Number', 'class' => 'ALL'),
                '7' => array('caption' => 'Appt Req Date', 'description' => 'Appointment Request Date', 'class' => 'ALL'),
                '8' => array('caption' => 'Appt Req Time', 'description' => 'Appointment Request Time', 'class' => 'ALL'),
                '9' => array('caption' => 'Arrival Date', 'description' => 'Arrival Date'),
                '10' => array('caption' =>  'Best Time to Call', 'description' => 'Best Time to Call', 'class' => 'ALL'),
                '11' => array('caption' =>  'Caller is a Buyer (Y/N)', 'description' => 'Caller is a Buyer (Y/N)'),
                '12' => array('caption' =>  'Caller is a Seller (Y/N)','description' => 'Caller is a Seller (Y/N)'),    
                '14' => array('caption' =>  'City, State, Zip', 'description' => 'City, State, Zip', 'class' => 'CONSTRUCTION DISASTER PLUMBING PROPMGMT REALESTATE LANDSCAPE'),
                '15' => array('caption' =>  'Company Name', 'description' => 'Company Name', 'class' => 'ALL'),
                '16' => array('caption' =>  'Current Client (Y/N)', 'description' => 'Current Client (Y/N)', 'class' => 'ALL'),
                '17' => array('caption' =>  'Departure Date', 'description' => 'Departure Date' ),
                '18' => array('caption' =>  'Email Address', 'description' => 'Email Address', 'class' => 'ALL'),
                '29' => array('caption' =>  'Friend/Fam', 'description' => "Friend/Family"),
                '20' => array('caption' =>  'Hotel Name', 'description' => 'Hotel Name' ),
                '21' => array('caption' =>  'How Did You Hear Abt Us', 'description' => 'How Did You Hear About Us', 'class' => 'ALL'),
                '22' => array('caption' =>  'Move In Date', 'description' => 'Move In Date', 'class' => 'REALESTATE'),
                '23' => array('caption' =>  'Nature of Emergency', 'description' => 'Nature of Emergency', 'class' => 'DISASTER HVAC PLUMBING PROPMGMT LANDSCAPE'),
                '24' => array('caption' =>  'New Client (Y/N)', 'description' => 'New Client (Y/N)', 'class' => 'ALL'),
                '25' => array('caption' =>  'New Court Date', 'description' => 'New Court Date'),
                '26' => array('caption' =>  'Order #', 'description' => 'Order Number'),
                '27' => array('caption' =>  'Patient Name', 'description' => 'Patient Name', 'class' => 'MEDICAL'),
                '28' => array('caption' =>  'PO#', 'description' => 'PO #' ),
                '29' => array('caption' =>  'Price Range', 'description' => 'Price Range', 'class' => 'REALESTATE'),
                '30' => array('caption' =>  'Product', 'description' => 'Product' ),
                '31' => array('caption' =>  'Property Address', 'description' => 'Property Address', 'class' => 'CONSTRUCTION DISASTER PLUMBING PROPMGMT REALESTATE LANDSCAPE'),
                '46' => array('caption' =>  'Property Name', 'description' => 'Property Name', 'class' => 'CONSTRUCTION DISASTER PLUMBING PROPMGMT REALESTATE LANDSCAPE'),
                '3' => array('caption' => 'Regarding', 'description' => 'Regarding', 'class' => 'ALL'),
                '32' => array('caption' =>  'Residential/Commercial', 'description' => 'Residential/ Commercial', 'class' => 'CONSTRUCTION DISASTER HVAC PLUMBING PROPMGMT REALESTATE LANDSCAPE'),
                '33' => array('caption' =>  'Requested Start Date', 'description' => 'Requested Start Date', 'class' => 'CONSTRUCTION DISASTER HVAC PLUMBING PROPMGMT LANDSCAPE'),
                '34' => array('caption' =>  'State', 'description' => 'State' ),
                '35' => array('caption' =>  'Street Address', 'description' => 'Street Address', 'class' => 'CONSTRUCTION DISASTER HVAC PLUMBING PROPMGMT REALESTATE LANDSCAPE'),
                '36' => array('caption' =>  'Store #', 'description' => 'Store #' ),
                '37' => array('caption' =>  'Store Name', 'description' => 'Store Name' ),
                '38' => array('caption' =>  'Technical Issue', 'description' => 'Technical Issue', 'class' => 'IT SOFTWARE HOSTING'),
                '39' => array('caption' =>  'Type of Service', 'description' => 'Type of Service', 'class' => 'CONSTRUCTION DISASTER HVAC PLUMBING PROPMGMT LANDSCAPE'),
                '40' => array('caption' =>  'User Name', 'description' => 'User Name', 'class' => 'IT SOFTWARE HOSTING'),
                '41' => array('caption' =>  'What Machine', 'description' => 'What Machine' ),
                '42' => array('caption' =>  'Vehicle Type', 'description' => 'Vehicle Type'),
                '43' => array('caption' =>  'Work To Be Done', 'description' => 'Work to be done', 'class' => 'CONSTRUCTION DISASTER HVAC PLUMBING PROPMGMT LANDSCAPE'),
                '44' => array('caption' =>  'Work Order #', 'description' => 'Work Order #' ),
                '45' => array('caption' =>  '1,2 or 3 Bdrm', 'description' => '1, 2, or 3 Bedroom', 'class' => 'REALESTATE')
            ),
            'states' => array(
                                'AL' => "Alabama",
                                'AK' => "Alaska", 
                                'AZ' => "Arizona", 
                                'AR' => "Arkansas", 
                                'CA' => "California", 
                                'CO' => "Colorado", 
                                'CT' => "Connecticut", 
                                'DE' => "Delaware", 
                                'DC' => "District Of Columbia", 
                                'FL' => "Florida", 
                                'GA' => "Georgia", 
                                'HI' => "Hawaii", 
                                'ID' => "Idaho", 
                                'IL' => "Illinois", 
                                'IN' => "Indiana", 
                                'IA' => "Iowa", 
                                'KS' => "Kansas", 
                                'KY' => "Kentucky", 
                                'LA' => "Louisiana", 
                                'ME' => "Maine", 
                                'MD' => "Maryland", 
                                'MA' => "Massachusetts", 
                                'MI' => "Michigan", 
                                'MN' => "Minnesota", 
                                'MS' => "Mississippi", 
                                'MO' => "Missouri", 
                                'MT' => "Montana",
                                'NE' => "Nebraska",
                                'NV' => "Nevada",
                                'NH' => "New Hampshire",
                                'NJ' => "New Jersey",
                                'NM' => "New Mexico",
                                'NY' => "New York",
                                'NC' => "North Carolina",
                                'ND' => "North Dakota",
                                'OH' => "Ohio", 
                                'OK' => "Oklahoma", 
                                'OR' => "Oregon", 
                                'PA' => "Pennsylvania", 
                                'RI' => "Rhode Island", 
                                'SC' => "South Carolina", 
                                'SD' => "South Dakota",
                                'TN' => "Tennessee", 
                                'TX' => "Texas", 
                                'UT' => "Utah", 
                                'VT' => "Vermont", 
                                'VA' => "Virginia", 
                                'WA' => "Washington", 
                                'WV' => "West Virginia", 
                                'WI' => "Wisconsin", 
                                'WY' => "Wyoming"
             ),
            'countries' => array(
                        'US' => 'United States',
                        'AF' =>    'Afganistan',
                        'AL' =>    'Albania',
                        'DZ' =>    'Algeria',
                        'AS' => 'American Samoa',
                        'AD' => 'Andorra', 
                        'AO' => 'Angola',
                        'AI' => 'Anguilla',
                        'AQ' => 'Antarctica',
                        'AG' => 'Antigua and Barbuda', 
                        'AR' => 'Argentina', 
                        'AM' => 'Armenia', 
                        'AW' => 'Aruba', 
                        'AU' => 'Australia', 
                        'AT' => 'Austria', 
                        'AZ' => 'Azerbaijan',
                        'BS' => 'Bahamas', 
                        'BH' => 'Bahrain', 
                        'BD' => 'Bangladesh',
                        'BB' => 'Barbados',
                        'BY' => 'Belarus', 
                        'BE' => 'Belgium', 
                        'BZ' => 'Belize',
                        'BJ' => 'Benin', 
                        'BM' => 'Bermuda', 
                        'BT' => 'Bhutan',
                        'BO' => 'Bolivia', 
                        'BA' => 'Bosnia and Herzegowina',
                        'BW' => 'Botswana',
                        'BV' => 'Bouvet Island', 
                        'BR' => 'Brazil',
                        'IO' => 'British Indian Ocean Territory',
                        'BN' => 'Brunei Darussalam', 
                        'BG' => 'Bulgaria',
                        'BF' => 'Burkina Faso',
                        'BI' => 'Burundi', 
                        'KH' => 'Cambodia',
                        'CM' => 'Cameroon',
                        'CA' => 'Canada',
                        'CV' => 'Cape Verde',
                        'KY' => 'Cayman Islands',
                        'CF' => 'Central African Republic',
                        'TD' => 'Chad',
                        'CL' => 'Chile', 
                        'CN' => 'China',
                        'CX' => 'Christmas Island',    
                        'CC' => 'Cocos (Keeling) Islands', 
                        'CO' => 'Colombia',
                        'KM' => 'Comoros', 
                        'CG' => 'Congo', 
                        'CD' => 'Congo, the Democratic Republic of the', 
                        'CK' => 'Cook Islands',
                        'CR' => 'Costa Rica',
                        'CI' => 'Cote d\'Ivoire', 
                        'HR' => 'Croatia (Hrvatska)',
                        'CU' => 'Cuba',
                        'CY' => 'Cyprus',
                        'CZ' => 'Czech Republic',
                        'DK' => 'Denmark', 
                        'DJ' => 'Djibouti',
                        'DM' => 'Dominica',
                        'DO' => 'Dominican Republic',
                        'TP' => 'East Timor',
                        'EC' => 'Ecuador', 
                        'EG' => 'Egypt', 
                        'SV' => 'El Salvador', 
                        'GQ' => 'Equatorial Guinea', 
                        'ER' => 'Eritrea', 
                        'EE' => 'Estonia', 
                        'ET' => 'Ethiopia',
                        'FK' => 'Falkland Islands (Malvinas)', 
                        'FO' => 'Faroe Islands', 
                        'FJ' => 'Fiji',
                        'FI' => 'Finland',
                        'FR' => 'France',
                        'FX' => 'France, Metropolitan',
                        'GF' => 'French Guiana', 
                        'PF' => 'French Polynesia',
                        'TF' => 'French Southern Territories', 
                        'GA' => 'Gabon', 
                        'GM' => 'Gambia',
                        'GE' => 'Georgia', 
                        'DE' => 'Germany', 
                        'GH' => 'Ghana', 
                        'GI' => 'Gibraltar', 
                        'GR' => 'Greece',
                        'GL' => 'Greenland', 
                        'GD' => 'Grenada', 
                        'GP' => 'Guadeloupe',
                        'GU' => 'Guam',
                        'GT' => 'Guatemala', 
                        'GN' => 'Guinea',
                        'GW' => 'Guinea-Bissau', 
                        'GY' => 'Guyana',
                        'HT' => 'Haiti', 
                        'HM' => 'Heard and Mc Donald Islands', 
                        'VA' => 'Holy See (Vatican City State)', 
                        'HN' => 'Honduras',
                        'HK' => 'Hong Kong', 
                        'HU' => 'Hungary', 
                        'IS' => 'Iceland', 
                        'IN' => 'India', 
                        'ID' => 'Indonesia', 
                        'IR' => 'Iran (Islamic Republic of)',
                        'IQ' => 'Iraq',
                        'IE' => 'Ireland', 
                        'IL' => 'Israel',
                        'IT' => 'Italy', 
                        'JM' => 'Jamaica', 
                        'JP' => 'Japan',
                        'JO' => 'Jordan',
                        'KZ' => 'Kazakhstan',
                        'KE' => 'Kenya', 
                        'KI' => 'Kiribati',
                        'KP' => 'Korea, Democratic People\'s Republic of',
                        'KR' => 'Korea, Republic of',
                        'KW' => 'Kuwait',
                        'KG' => 'Kyrgyzstan',
                        'LA' => 'Lao People\'s Democratic Republic',
                        'LV' => 'Latvia',
                        'LB' => 'Lebanon',
                        'LS' => 'Lesotho', 
                        'LR' => 'Liberia', 
                        'LY' => 'Libyan Arab Jamahiriya',
                        'LI' => 'Liechtenstein', 
                        'LT' => 'Lithuania',
                        'LU' => 'Luxembourg',
                        'MO' => 'Macau', 
                        'MK' => 'Macedonia, The Former Yugoslav Republic of',
                        'MG' => 'Madagascar',
                        'MW' => 'Malawi',
                        'MY' => 'Malaysia',
                        'MV' => 'Maldives',
                        'ML' => 'Mali',
                        'MT' => 'Malta',
                        'MH' => 'Marshall Islands',
                        'MQ' => 'Martinique',
                        'MR' => 'Mauritania',
                        'MU' => 'Mauritius',
                        'YT' => 'Mayotte', 
                        'MX' => 'Mexico',
                        'FM' => 'Micronesia, Federated States of',
                        'MD' => 'Moldova, Republic of',
                        'MC' => 'Monaco',
                        'MN' => 'Mongolia',
                        'MS' => 'Montserrat',
                        'MA' => 'Morocco',
                        'MZ' => 'Mozambique',
                        'MM' => 'Myanmar',
                        'NA' => 'Namibia',
                        'NR' => 'Nauru', 
                        'NP' => 'Nepal', 
                        'NL' => 'Netherlands',
                        'AN' => 'Netherlands Antilles',
                        'NC' => 'New Caledonia',
                        'NZ' => 'New Zealand', 
                        'NI' => 'Nicaragua', 
                        'NE' => 'Niger', 
                        'NG' => 'Nigeria', 
                        'NU' => 'Niue',
                        'NF' => 'Norfolk Island',
                        'MP' => 'Northern Mariana Islands',
                        'NO' => 'Norway',
                        'OM' => 'Oman',
                        'PK' => 'Pakistan',
                        'PW' => 'Palau',
                        'PA' => 'Panama',
                        'PG' => 'Papua New Guinea',
                        'PY' => 'Paraguay',
                        'PE' => 'Peru',
                        'PH' => 'Philippines',
                        'PN' => 'Pitcairn',
                        'PL' => 'Poland',
                        'PT' => 'Portugal',
                        'PR' => 'Puerto Rico',
                        'QA' => 'Qatar',
                        'RE' => 'Reunion',
                        'RO' => 'Romania',
                        'RU' => 'Russian Federation',
                        'RW' => 'Rwanda',
                        'KN' => 'Saint Kitts and Nevis', 
                        'LC' => 'Saint LUCIA', 
                        'VC' => 'Saint Vincent and the Grenadines',
                        'WS' => 'Samoa', 
                        'SM' => 'San Marino',
                        'ST' => 'Sao Tome and Principe',
                        'SA' => 'Saudi Arabia',
                        'SN' => 'Senegal',
                        'SC' => 'Seychelles',
                        'SL' => 'Sierra Leone',
                        'SG' => 'Singapore', 
                        'SK' => 'Slovakia (Slovak Republic)',
                        'SI' => 'Slovenia',
                        'SB' => 'Solomon Islands',
                        'SO' => 'Somalia', 
                        'ZA' => 'South Africa',
                        'GS' => 'South Georgia and the South Sandwich Islands',
                        'ES' => 'Spain',
                        'LK' => 'Sri Lanka',
                        'SH' => 'St. Helena',
                        'PM' => 'St. Pierre and Miquelon', 
                        'SD' => 'Sudan', 
                        'SR' => 'Suriname',
                        'SJ' => 'Svalbard and Jan Mayen Islands',
                        'SZ' => 'Swaziland', 
                        'SE' => 'Sweden',
                        'CH' => 'Switzerland', 
                        'SY' => 'Syrian Arab Republic',
                        'TW' => 'Taiwan, Province of China',
                        'TJ' => 'Tajikistan',
                        'TZ' => 'Tanzania, United Republic of',
                        'TH' => 'Thailand',
                        'TG' => 'Togo',
                        'TK' => 'Tokelau',
                        'TO' => 'Tonga', 
                        'TT' => 'Trinidad and Tobago', 
                        'TN' => 'Tunisia', 
                        'TR' => 'Turkey',
                        'TM' => 'Turkmenistan',
                        'TC' => 'Turks and Caicos Islands',
                        'TV' => 'Tuvalu',
                        'UG' => 'Uganda',
                        'UA' => 'Ukraine',
                        'AE' => 'United Arab Emirates',
                        'GB' => 'United Kingdom',
                        'UM' => 'United States Minor Outlying Islands',
                        'UY' => 'Uruguay', 
                        'UZ' => 'Uzbekistan',
                        'VU' => 'Vanuatu', 
                        'VE' => 'Venezuela',
                        'VN' => 'Viet Nam',
                        'VG' => 'Virgin Islands (British)',
                        'VI' => 'Virgin Islands (U.S.)', 
                        'WF' => 'Wallis and Futuna Islands', 
                        'EH' => 'Western Sahara',
                        'YE' => 'Yemen', 
                        'YU' => 'Yugoslavia',
                        'ZM' => 'Zambia',
                        'ZW' => 'Zimbabwe'            
                        )

    );
    
    
    public $permissions = array();
    
    public $system_configs = '';
    
    
    
    
    function _getStartOfWeek() {
        $today = date('D'); //Sun, Mon, etc...
        if ($today == Configure::read('start_of_week')) {
            $weekstart_date = date('Y-m-d');
        }
        else {
            $weekstart_date = date('Y-m-d', strtotime('last Sunday'));
        }
        return $weekstart_date;
    }
    
    public function beforeFilter() 
    {
        $this->loadPermissions(AuthComponent::user('role'));
        #$this->loadPermissions(AuthComponent::user('role'));
        //Make the google api key available to the view if it exists
        if (Configure::read('google_api_key')) {
            $google_api_key = Configure::read('google_api_key');
        }
        else {
            $google_api_key = '';
        }
        $this->set('google_api_key', $google_api_key);
        
        
        $this->global_options['actions_all'] = $this->global_options['actions'];
        
        //If Faxing is not enabled, remove the options for it in other areas.
        if (!Configure::read('fax_enabled')) {
            unset($this->global_options['actions'][ACTION_FAX]);
            unset($this->global_options['actions'][ACTION_FAX_DELIVER]);
            unset($this->global_options['actions_customers'][ACTION_FAX]);
            unset($this->global_options['actions_customers'][ACTION_FAX_DELIVER]);
            unset($this->global_options['contact_types'][CONTACT_FAX]);
            $this->set('fax_enabled', false);
        }
        else {
            $this->set('fax_enabled', true);
        }
        
        $controller = ($this->params['controller']);
        $action = $this->params['action'];
        
        //change the default layout used based on the request type
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
        }
        else {
            $this->layout = 'openanswer';
        }
        
        //TODO: Legacy VN Dashboard access, Remove and replace with API calls
        if ($this->getLoginRole() == 'Customer') {
            if (substr($action,0,10) != 'dashboard_') {
                $this->Session->setFlash(__('You are not allowed to view this page'));
                $this->render('/Elements/html_result');
                return;
            }
            if (!$this->dashboard_authenticate($did_id)) {
                $this->Session->setFlash(__('You are not allowed to view this page'));
                $this->render('/Elements/html_result');        
            }
        }
        else if (substr($action,0,3) == 'my_') {
            $user_id = $this->request['pass'][0];
            if ($user_id != AuthComponent::user('id')) 
            {
                $this->Session->setFlash(__('You are not allowed to view this page'));
                $this->render('/Elements/html_result');
                return;
            }
        }
        
        if (isset($this->request->data['Search'])) {
            foreach($this->request->data['Search'] as $k => &$val) {
                if (!is_array($val)) {
                    $val = str_replace("'", "\'", $val);
                }
            }
            $this->Session->write('Search.' . $controller . "_" . $action, $this->request->data['Search']);
        }
        else 
        {
            if (isset($this->request->data)) 
            {
                $this->request->data['Search'] = $this->Session->read('Search.' . $controller . "_" . $action);      
            }
        }
        
        
        $this->set('start_of_week', $this->_getStartOfWeek());
        $this->set('stime', microtime(true));
        $this->set('oa_title', Configure::read('title'));
        $this->global_options['calltypes'] = array(
            $this->slabel_generic => array('description' => "ALL (Other) Calls",'caption' => 'Generic', 'class' => 'ALL onlyOne'),
            $this->slabel_general_id => array('description' => $this->slabel_general, 'caption' => $this->scaption_general, 'class' => 'ALL onlyOne'),
            $this->slabel_unknown_id => array('description' => "Unknown employee",'caption' => 'Unk Staff', 'class' => 'ALL'),
            '26' => array('description' => "Accounting", 'caption' => 'Accounting', 'class' => 'ALL'),
            '2' => array('description' => "Appointments", 'caption' => 'Appts', 'class' => 'ALL'),
            '3' => array('description' => "Billing", 'caption' => 'Billing', 'class' => 'ALL'),
            '4' => array('description' => "Cancel Appointment", 'caption' => 'Cancel Appt', 'class' => 'ALL'),
            '5' => array('description' => "Cancellation", 'caption' => 'Cancellation', 'class' => 'ALL'),
            '6' => array('description' => "Current Client", 'caption' => 'Cur Client', 'class' => 'ALL'),
            $this->slabel_emergency_id => array('description' => $this->slabel_emergency,'caption' => 'ER', 'class' => 'ALL onlyOne'),
            '8' => array('description' => "Employment",'caption' => 'Employment', 'class' => 'ALL'),
            '9' => array('description' => "Estimate",'caption' => 'Estimate', 'class' => 'MORE'),
            '29' => array('description' => "Friends or Family",'caption' => 'Friend/Fam', 'class' => 'ALL'),
            '11' => array('description' => "Human Resources (HR)",'caption' => 'HR', 'class' => 'ALL'),
            /*$this->slabel_id => array('description' => $this->slabel_default, 'caption' => "Message", 'class' => 'ALL'),*/
            '13' => array('description' => "New Appointment",'caption' => 'New Appt', 'class' => 'ALL'),
            '14' => array('description' => "New Client",'caption' => 'New Client', 'class' => 'ALL'),
            '30' => array('description' => "New Leads", 'caption' => 'New Leads', 'class' => 'ALL'),
            '16' => array('description' => "Order",'caption' => 'Order', 'class' => 'MORE'),
            '15' => array('description' => "Parts",'caption' => 'Parts', 'class' => 'MORE'),
            '17' => array('description' => "Property Inquiry",'caption' => 'Prop Inquiry', 'class' => 'MORE'),
            '18' => array('description' => "Roadside Help",'caption' => 'Roadside', 'class' => 'MORE'),
            '27' => array('description' => "Reschedule Appointment",'caption' => 'Reschedule Appt', 'class' => 'ALL'),
            '19' => array('description' => "Sales",'caption' => 'Sales', 'class' => 'ALL'),
            '20' => array('description' => "Service",'caption' => 'Service', 'class' => 'ALL'),
            '21' => array('description' => "Support",'caption' => 'Support', 'class' => 'ALL'),
            '22' => array('description' => "Telemarketers",'caption' => 'Telemarketer', 'class' => 'ALL'),
            '23' => array('description' => "Towing",'caption' => 'Towing', 'class' => 'MORE'),
            /*'28' => array('description' => "Transfer",'caption' => 'Transfer', 'class' => 'ALL'),*/
            $this->slabel_urgent_id => array('description' => "Urgent",'caption' => 'Urgent', 'class' => 'ALL')
        );
        $this->set('global_options', $this->global_options);
        $this->set('permissions', $this->permissions);
        $this->set('js_daysofweek', $this->js_daysofweek);
        $this->set('php_daysofweek', $this->php_daysofweek);
        $this->set('loginRole', $this->getLoginRole());
        $this->set('user_extension', $this->Session->read('User_extension'));
        //$this->set('system_configs', $this->system_configs);
        $this->user_extension = $this->Session->read('User_extension');
        $theme = $this->request->header('oatheme');
        
        //Authenticate the user
        if (!$this->Auth->user() && !empty($theme)) {
            $this->theme = $theme;
            if ($this->_authenticate(array('username' => $this->request->header('username'), 'password' => $this->request->header('password')))) {
                $this->loadPermissions(AuthComponent::user('role'));
                //authentication successful
            }
            else {
                //authentication not successful
                echo 'Not Authorized';
                exit;
            }
        }
        else {
            $oa_theme = Configure::read('oa_theme');
            if ( !empty($oa_theme)) 
            {
                $this->theme = $oa_theme;
            }
        }
    }
    
    
    public function clear_cache() {
        if (Cache::clear(false, 'short')) echo 'done';
        else echo 'not done';
        if (Cache::clear(false, 'long')) echo 'done';
        else echo 'not done';
        exit;
    }

    function _getInstructions($did_id, $schedule_id, $include_prompts = false) {
        $data = $this->_instructions($did_id, null, $schedule_id);
        
        // older calltype instructions will not have sections defined
        // if not defined, then just create a default first section that is visible by default
        if (isset($data['sections'][$schedule_id])) $sections = $data['sections'][$schedule_id];
        else {
            $sections[1]['visible'] = 1;
        }
        
        // grab the actions for the specified schedule
        if (isset($data['ct_actions'][$schedule_id])) $actions = $data['ct_actions'][$schedule_id];
        else $actions = array();
        ksort($actions);
        $view = new View();
        $s_html = '';
        $current_section = '';        
        
        // mark first section as visible by default
        $sections[1]['visible'] = 1; 
        $section_num = 1;        

        // mark which sections should be visible initially
        while (isset($sections[$section_num]) && $sections[$section_num]['section_action'] == '1') {
            $goto_section = $sections[$section_num]['section_num'];
            $sections[$goto_section]['visible'] = 1;
            $section_num = $goto_section;
        }
        
        // accomodate agent scripts set up prior to addition of logic-driven scriptinng
        foreach ($actions as $ak => $a) {
            if ($a['section'] == 0) $actions[$ak]['section'] = 1;        
 
        }
        $old_section = 0;
        $cnt = 0;
        foreach ($actions as $ak => $a) {
            if ($a['section'] != $old_section) {
                if ($sections[$a['section']]['visible'] == 0) $temp = " is_hidden";
                else {
                    $temp = '';
                }

                $s_html .= ('<div id="msg_section_'.($a['section']).'" class="script_section'.$temp.'" data-section="'.$a['section'].'" data-action="'.$sections[$a['section']]['section_action'].'" data-section-num="'.$sections[$a['section']]['section_num'].'"');
                $s_html .= '><div class="section_title">'.$section_title.'</div>'; 
                
            }
                $current_section = $a['section'];
                if (substr($a['eid'], 0, 6) == 'ONCALL') $oncall_list_id = str_replace('ONCALL_', '', $a['eid']);
                $employee_select = false;
                
                if ($include_prompts && trim($a['prompts'])) {
                    $s_html .= ('<div class="msgstep" data-action="'.$cnt.'">' .  $view->element('calltype_schedule_msg', array('idx' => $ak, 'actions' => $actions, 'json' => $data, 'global_options' => $this->global_options)));
                            
                            $s_html .= '<div class="prompts">';
                            $prompts = $data['prompts'][$a['id']];
                            
                            if (!is_array($prompts)) {
                                $prompts = array();
                            }
                            
                            foreach ($prompts as $k => $p) {
                                $class = '';
                                if (isset($p['value'])) $val = $p['value'];
                                else $val = '';
                                if ($p['required']) {
                                        $title = '* ' . $p['caption'];
                                        $class .= " required";
                                }
                                else {
                                        $title = $p['caption'];
                                }
                                if (trim($p['caption']) == 'Phone Number') $class .= ' phone_field';
                                            $extra = '';
                                if ($p['ptype'] == '3') {
                                        $options = explode('|', $p['options']);
                                        $extra = ' - Dropdown: ' .  implode(', ', $options);
                                }
                                else if ($p['ptype'] == '4') {
                                        $temp = explode('||', $p['options']);
                                        $options = explode('|', $temp[0]);
                                        $goto = explode('|', $temp[1]);
                                        $class .= " conditional";
                                        
                                        $extra = ' - Conditional: ';
                                        foreach ($options as $k => $v) {
                                                if ($goto[$k] > 0) $temp2[] = "$v -> Go to <i>" . $sections[$goto[$k]]['title'] . '</i>';
                                                else $temp2[] = "$v -> <i>(no action)</i>";
                                        }
                                        $extra .= implode(', ', $temp2);
                                }
                                $s_html .= '<div class="prompt '.$class.'" data-caption="'.$p['caption'].'" data-options="'.$p['options'].'">' . $title . $extra;
                                $s_html .= '</div>';
                            }
                            $s_html .= '</div>';
                         $s_html .= '</div>';
                            
                }
                else {
                    $s_html .= '<div class="msgstep">' .  $view->element('calltype_schedule_msg', array('idx' => $ak, 'actions' => $actions, 'json' => $data, 'global_options' => $this->global_options)) . '</div>';
                }
    
    //          $s_html .= $this->element('calltype_schedule_msg', array('idx' => $ak, 'actions' => $actions, 'action_id' => $a['id'], 'json' => $data)) . '</div>';
                if ($a['action_type'] == ACTION_TXF || $a['action_type'] == ACTION_BLINDTXF) $required['phone'] = true;
                else if ($a['action_type'] == ACTION_TXTMSG || $a['action_type'] == ACTION_TEXT_DELIVER) $required['text'] = true;
                else if ($a['action_type'] == ACTION_EMAIL || $a['action_type'] == ACTION_EMAIL_DELIVER) $required['email'] = true;
                else if ($a['action_type'] == ACTION_VMOFFER ) $required['vmail'] = true;
                // check if we need to close off current script section
          


                if (isset($sections[$s]['section_action'])) {
                    $section_action = $sections[$s]['section_action'] ;
                    if ($section_action > 0) {
                        $section_num = $sections[$s]['section_num'];
                        $s_html .= '<div class="section_action">Go to '. $sections[$section_num]['title'].'</div>';
                    }
                    else {
                        $s_html .= '<div class="section_action">Stop here</div>';
                        
                    }
                }

                
            // close off section if necessary
            if ($cnt == (sizeof($actions)-1) || ($a['section'] != $actions[$ak+1]['section'])) {
                $s_html .= '</div>';
            }
            $cnt++;
            $old_section = $a['section'];                  

        } 
        return $s_html;
    }
        
    function _instructions($did_id, $test_time=null, $schedule_id = null) { 
        Configure::write('debug', 0);      
        if (!isset($this->DidNumber)) $this->loadModel('DidNumber');
        
        // check if we have the instructions in the cache to save time.
        if (!empty($schedule_id)) $instructions = Cache::read('msg_instructions' . $did_id, 'long');
        if ($instructions === false || !isset($instructions['did']) ) {
            $this->DidNumber->unbindModel(
                array('hasMany' => array('Employee', 'DidNumbersEdit'),
                'belongsTo' => array('Account'))
            );
            $did = $this->DidNumber->findById($did_id);
            $did['DidNumber']['tz'] = $this->global_options['timezone'][$did['DidNumber']['timezone']];
            $did['DidNumber']['answerphrase'] = str_replace('[Company Name]', $did['DidNumber']['company'], $did['DidNumber']['answerphrase']);
//            $account['Account'] = $did['Account'];
            //print_r($account); 
            $client_timezone = $did['DidNumber']['timezone'];

            $employees = array();
            $employees_contacts = array();
            
            // get all employees for this DID indexed by employee id
            $sql = "select * from ".OA_TBL_PREFIX."employees e where did_id='$did_id' and deleted = '0' order by sort, name";     
            $data = $this->DidNumber->query($sql);
            foreach ($data as $row) {
                $employees[$row['e']['id']] = $row['e'];
                $employees[$row['e']['id']]['contacts'] = array();
            }

            // get all contact info for each employee, save it under each employee record $employees[$emp_id]['contacts'] = array(...), 
            // also create a contacts lookup array indexed by contact id
            $sql = "select c.*, s.addr, s.prefix from ".OA_TBL_PREFIX."employees_contacts c left join ".OA_TBL_PREFIX."sms_carriers s on s.id=c.carrier_id where did_id='$did_id' order by sort, contact_type, `primary` desc";     
            $data = $this->DidNumber->query($sql);
            foreach ($data as $row) {
                if (isset($employees[$row['c']['employee_id']])) {
                    $employees[$row['c']['employee_id']]['contacts'][] = array_merge($row['c'], $row['s']);
                    //$employees_contacts_per_eid[$row['c']['employee_id']][] = $row['c'];
                    $employees_contacts[$row['c']['id']] = array_merge($row['c'], $row['s']);                
                }
            }
            // get all actions for the schedule, create array of action rows indexed by schedule id
            // ct_actions[schedule_id] = array(actionrow1,actionrow2, ...)     
        if (empty($schedule_id)) {
                //$sql = "select distinct a.section, a.action_label, a.action_text, a.id, a.sort, a.schedule_id, a.action_type, a.eid, a.helper, a.dispatch_only, a.action_opt, c.name, GROUP_CONCAT(p.caption ORDER BY p.sort SEPARATOR ', ') as prompts from ".OA_TBL_PREFIX."actions a left join ".OA_TBL_PREFIX."schedules s on s.id=a.schedule_id left join ".OA_TBL_PREFIX."prompts p on a.id=p.action_id left join ".OA_TBL_PREFIX."crms c on a.action_opt=c.id where a.did_id='$did_id' and s.deleted='0' group by a.id"; 
                $sql = "select distinct a.section, a.action_label, a.action_text, a.id, a.sort, a.schedule_id, a.action_type, a.eid, a.helper, a.dispatch_only, a.action_opt, c.name, GROUP_CONCAT(p.caption ORDER BY p.sort SEPARATOR ', ') as prompts from ".OA_TBL_PREFIX."actions a left join ".OA_TBL_PREFIX."schedules s on s.id=a.schedule_id left join ".OA_TBL_PREFIX."prompts p on a.id=p.action_id left join ".OA_TBL_PREFIX."crms c on a.action_opt=c.id where a.did_id='$did_id' and s.active='1' and s.deleted='0' and s.active='1' group by a.id order by a.schedule_id, a.sort"; 
            }
             else  {
                 //$sql = "select distinct a.section, a.action_label, a.action_text, a.id, a.sort, a.schedule_id, a.action_type, a.eid, a.helper, a.dispatch_only, a.action_opt, c.name, GROUP_CONCAT(p.caption ORDER BY p.sort SEPARATOR ', ') as prompts from ".OA_TBL_PREFIX."actions a left join ".OA_TBL_PREFIX."schedules s on s.id=a.schedule_id left join ".OA_TBL_PREFIX."prompts p on a.id=p.action_id left join ".OA_TBL_PREFIX."crms c on a.action_opt=c.id where a.did_id='$did_id' and s.deleted='0' group by a.id"; 
                $sql = "select distinct a.section, a.action_label, a.action_text, a.id, a.sort, a.schedule_id, a.action_type, a.eid, a.helper, a.dispatch_only, a.action_opt, c.name, GROUP_CONCAT(p.caption ORDER BY p.sort SEPARATOR ', ') as prompts from ".OA_TBL_PREFIX."actions a left join ".OA_TBL_PREFIX."schedules s on s.id=a.schedule_id left join ".OA_TBL_PREFIX."prompts p on a.id=p.action_id left join ".OA_TBL_PREFIX."crms c on a.action_opt=c.id where a.did_id='$did_id' and ((s.deleted='0' and s.active='1') or a.schedule_id='$schedule_id') group by a.id order by a.schedule_id, a.sort"; 
            }
            $data = $this->DidNumber->query($sql, false);

            $action_ids = array();
            foreach ($data as $row) {
                if (!isset($ct_actions[$row['a']['schedule_id']])) 
                    $ct_actions[$row['a']['schedule_id']] = array();
                $row['a']['prompts'] = $row[0]['prompts'];    
                $row['a']['name'] = $row['c']['name'];
                // check if sort order is specified     
                
                    $ct_actions[$row['a']['schedule_id']][] = $row['a'];
                if (!in_array($row['a']['id'], $action_ids)) $action_ids[] = $row['a']['id'];
            }        
            if (!$data) return false;
            $ct_prompts = array();
            if (sizeof($action_ids) > 0) {
                // get all prompts for the DID, create array of action rows indexed by schedule id
                // ct_actions[schedule_id] = array(actionrow1,actionrow2, ...)     
                $sql = "select * from ".OA_TBL_PREFIX."prompts p where did_id='$did_id' and action_id in ('".implode("','", $action_ids)."') order by action_id asc, sort asc"; 
                
                $data = $this->DidNumber->query($sql, false);
                foreach ($data as $row) {
                    if (!isset($ct_prompts[$row['p']['action_id']])) 
                        $ct_prompts[$row['p']['action_id']] = array();
                     $ct_prompts[$row['p']['action_id']][$row['p']['sort']] = $row['p'];
                }
            }
            
            $calendars = array();
            if (Configure::read('calendar_enabled')) {
                $sql = "select * from ".OA_TBL_PREFIX."ea_services s where did_id='$did_id' and deleted='0'";
                $data = $this->DidNumber->query($sql, false);
                foreach ($data as $k => $val) {
                    $calendars[$val['s']['id']] = $val['s'];
                }
            }
            // fetch agent script sections    
      if (empty($schedule_id)) {
              $sql = "select s.* from ".OA_TBL_PREFIX."sections s left join ".OA_TBL_PREFIX."schedules sc on sc.id=s.schedule_id where sc.deleted='0' and sc.active='1' and sc.did_id='$did_id'";
            }
            else {
              $sql = "select s.* from ".OA_TBL_PREFIX."sections s left join ".OA_TBL_PREFIX."schedules sc on sc.id=s.schedule_id where ((sc.deleted='0' and sc.active='1') or sc.id='$schedule_id')  and sc.did_id='$did_id'";
            }
            
            $res = $this->DidNumber->query($sql, false);
            foreach($res as $k => &$row) {
                $row['s']['visible'] = 0;
                if (!isset($sections[$row['s']['schedule_id']])) $sections[$row['s']['schedule_id']] = array();
                $sections[$row['s']['schedule_id']][$row['s']['sort']] = $row['s'];
            }
            
            $instructions = array('did' => $did['DidNumber'], 'sections' => $sections, 'files' => $did['DidFile'], 'contacts' => $employees_contacts, 'employees' => $employees, 'ct_actions' => $ct_actions, 'prompts' => $ct_prompts, 'calendars' => $calendars);
            
            // cache only if a specified schedule was NOT set
            if (empty($schedule_id)) {
              Cache::write('msg_instructions' . $did_id, $instructions, 'long');
            }

        }                
        else {
            $client_timezone = $instructions['did']['timezone'];
        }
        if ($test_time) {
            $datetime = new DateTime($test_time);
        }
        else {
            $datetime = new DateTime();
            $client_time = new DateTimeZone($client_timezone);
            $datetime->setTimezone($client_time);
        }

        $n_day = $datetime->format('w'); // 0=sun, 6 = Saturday
        if ($n_day == 0) $n_day = 7; // make 7=Sunday
    
        // cannot cache this since account might be time-sensitive
        $n_time = $n_day . $datetime->format('Hi');
        $n2_time = $datetime->format('H:i:s');
        $time_mysql = $datetime->format('Y-m-d H:i:s');
        $time_only_mysql = $datetime->format('H:i:s');
        $day_of_week = strtolower($datetime->format('D'));
        if ($schedule_id) $or_sql = " OR s.id='$schedule_id'";
        else $or_sql = '';

        // retrieve account notes set up to be visible to operators, notes may be time-sensitive
        $sql = "select * from ".OA_TBL_PREFIX."notes as n where did_id='$did_id' and visible='1' and (start_date IS NULL or (start_date <= '$time_mysql' and end_date >= '$time_mysql')) order by start_date desc"; 
        $n = $this->DidNumber->query($sql);
        
        // configure notes to display in specified positions
        $notes = array('left' => '', 'center' => '', 'right' => '');
        $locs = array('left', 'center', 'right');

        foreach ($n as $note) {
            $description = str_replace("\r\n", "<br>", $note['n']['description']);
            if (empty($note['n']['bg_color'])) $bg_color = '#ffff80';
            else $bg_color = trim($note['n']['bg_color']);
            $class = '';
            if (!empty($note['n']['extra_class'])) {
                $class = ' class="'.$note['n']['extra_class'].'" ';
            }
            $notes[$locs[$note['n']['display_location']]] .= '<div '.$class.' style="background-color:'.$bg_color.'">' . $description . '</div>';
        }
            
        $instructions['notes'] = $notes;
        //if ($schedule_id) {
        if (0) { // need to grab all schedules
            $sql = "select t.title, t.id, s.* 
            FROM ".OA_TBL_PREFIX."schedules s 
            LEFT JOIN ".OA_TBL_PREFIX."calltypes t ON t.id=s.calltype_id 
            WHERE s.id = '$schedule_id'" ;
            
        }
        else {
            $sql = "select t.description, t.title, t.id, t.sort, s.* 
            FROM ".OA_TBL_PREFIX."schedules s 
            LEFT JOIN ".OA_TBL_PREFIX."calltypes t ON t.id=s.calltype_id 
            WHERE t.deleted = '0' and s.did_id='$did_id' and active='1' and s.deleted='0' 
            AND ((
                (s.start_date <= '$time_mysql' and s.end_date >= '$time_mysql')       
                OR (s.start_day <= '$n_time' and s.end_day>='$n_time') 
                OR (s.start_day <= '".($n_time+70000)."' and s.end_day>='".($n_time+70000)."')
                OR (check_days='1' and ".$day_of_week."='1' and (start_time is null))          
                OR (check_days='1' and ".$day_of_week."='1' and (start_time < '$time_only_mysql' and end_time > '$time_only_mysql'))          
                OR (start_date is null and start_day is null and check_days='0')
            ) $or_sql) order by t.sort asc, t.title, s.start_date desc, s.start_day desc, check_days desc";
        }
        
        $schedules = array();
        $data = $this->DidNumber->query($sql);
        if (!$data) return false;

        $check_duplicates = array();
        $first = true;
        $msg_schedule_id_found = false;
        $time_sensitive = false;
        
        // keep the most date/time specific schedule for each calltype, discard others if multiple are found
        foreach ($data as $row) {
            if (!in_array($row['t']['title'], $check_duplicates)) {
                if ($row['s']['start_date'] || $row['s']['start_day'] || $row['s']['check_days']) $time_sensitve = true;
                // index schedules by id if schedule_id is provided, otherwise by calltype_id
                $schedules[$row['s']['calltype_id']] = $row['s']; //
                $check_duplicates[] = $row['t']['title'];
                if ($schedule_id == $row['s']['id']) $msg_schedule_id_found = true;
                if ($row['t']['id']) $calltypes[] = array('id' => $row['t']['id'], 'sid' => $row['s']['id'], 'title' => $row['t']['title'], 'sort' => $row['t']['sort'], 'type' => $row['s']['type'], 'desc' => $row['t']['description']);
            }
        }
        
        //check if we're getting instructions for a schedule that is no longer active (has been updated or deleted)
        // DISABLE for nowif (!$msg_schedule_id_found && $schedule_id) {
        //if (0) {
        if (!$msg_schedule_id_found && $schedule_id) {      
            $sql = "select t.id, t.title, t.sort, t.description, s.* 
            FROM ".OA_TBL_PREFIX."schedules s 
            LEFT JOIN ".OA_TBL_PREFIX."calltypes t ON t.id=s.calltype_id 
            WHERE s.did_id='$did_id' AND s.id='$schedule_id'";      
            $data = $this->DidNumber->query($sql);
            if ($data) {
                $row = $data[0];
                $row['t']['title'] = $row['t']['title'] . ' (inactive)';
                $schedules[$row['s']['calltype_id'] . '|inactive'] = $row['s'];          
                $calltypes[] = array('id' => $row['t']['id'] . '|inactive', 'title' => $row['t']['title'], 'sid' => $row['s']['id'], 'sort' => $row['t']['sort'], 'type' => $row['s']['type'],'desc' => $row['t']['description']);
            }
        }
        $sql = "select t.title, t.id, t.hide_from_operator,  s.* 
            FROM ".OA_TBL_PREFIX."call_lists_schedules s 
            LEFT JOIN ".OA_TBL_PREFIX."call_lists t ON t.id=s.call_list_id 
            WHERE t.deleted = '0' and s.did_id='$did_id' and s.deleted='0' 
            AND ((
                (s.start_date <= '$time_mysql' and s.end_date >= '$time_mysql')       
                OR (s.start_day <= '$n_time' and s.end_day>='$n_time') 
                OR (s.start_day <= '".($n_time+70000)."' and s.end_day>='".($n_time+70000)."')
                OR (check_days='1' and ".$day_of_week."='1' and (start_time is null OR (start_time < '$n2_time' and end_time > '$n2_time'))    )      
                OR (start_date is null and start_day is null and check_days='0')
            ) ) order by t.title, s.start_date desc, s.start_day desc, check_days desc";
        $call_lists = array();
        $data = $this->DidNumber->query($sql);
        $check_duplicates = array();
        $first = true;
        $oncall_time_sensitive = false;
        foreach ($data as $row) {
            if (!in_array($row['t']['title'], $check_duplicates)) {
                if ($row['s']['start_date'] || $row['s']['start_day'] || $row['s']['check_days']) $oncall_time_sensitive = true;
                // index schedules by id if schedule_id is provided, otherwise by calltype_id
                $row['s']['hide_from_operator'] = $row['t']['hide_from_operator'];
                $call_lists[$row['t']['title']] = $row['s']; //
                $check_duplicates[] = $row['t']['title'];
                //if ($schedule_id == $row['s']['id']) $msg_schedule_id_found = true;
            }
        }          
        $instructions['calltypes'] = $calltypes;
        $instructions['schedules'] = $schedules;
        $instructions['call_lists'] = $call_lists;
        $instructions['time_sensitive'] = $time_sensitive;
        $instructions['oncall_time_sensitive'] = $oncall_time_sensitive;
        return $instructions;
                
    }  
    
    function clearDidCache($did_id) {
        Cache::delete('msg_instructions' . $did_id, 'long');      
        Cache::delete('msg_instructions' . $did_id, 'short');      
    }
    
    function getLoginRole() {
        if (AuthComponent::user('role')) 
            return AuthComponent::user('role');
        else return false;
    }
    
    
    
    /*
    Load and store the currently assigned permissions for this user, based on their Role ID.
    */
    function loadPermissions($role_id = null) {
        
        $permissions;
        $this->loadModel('Role');
        //load the permissions assigned for this role
        $role = $this->Role->find('all',array('conditions' => array('Role.id' => $role_id),'recursive' => 1));
        
        //for each permission assigned, add it to an easy/quick array for general access.
        if (isset($role[0]['Permission'])) {
            foreach ($role[0]['Permission'] As $key => $permission) {
                $this->permissions[] = $permission['shortname'];
            }
        }
    }
    
    
    
    /*
    Check a permission shortname against the list of allowed user permissions
    return true if permission is allowed, otherwise false.
    */
    function isAuthorized($shortname = '') {
        $check = array_search($shortname,$this->permissions);
        if ($check === false) {
            return false;
        }
        else {
            return true;
        }
    }
    
    
    function timeFormat(&$input) {
        preg_match('/([0-9]{1,2}):([0-9]{2})(am|pm){1}/', $input, $matches);
        if (sizeof($matches) && $matches[3] == 'am') $input = sprintf("%02d", $matches[1]) . ':' . $matches[2];
        else if (sizeof($matches) && $matches[3] == 'pm') $input = sprintf("%02d", ($matches[1]+12)) . ':' . $matches[2];
    }
    
    function formatDuration($t, $f=':', $always_show_hour = false) // t = seconds, f = separator 
    {
        if (empty($t)) return '';
        if (floor($t/3600) || $always_show_hour) {
            return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
        }
        else {
            return sprintf("%02d%s%02d", ($t/60)%60, $f, $t%60);
        }
    }
    
    
    function dateMysqlize($input) {
        preg_match('@([0-9]{1,2})[^0-9]{1,1}([0-9]{1,2})[^0-9]{1,1}([0-9]{4,4})@', $input, $matches);
        if ($matches) return $matches[3] . '-' . $matches[1] . '-' . $matches[2];      
        else return $input;
    }
    
    // $level defines the visibility of the call event.
    public function logEvent($call_id, $text, $event_type='1', $button_data='', $level=EVT_LVL_OPERATOR) {
        if (empty($this->user_extension)) {
            $ext = 0;
            $user_id = 0;
        }
        else {
            $ext = $this->user_extension;
            $user_id =  AuthComponent::user('id');
        }
            $t['CallEvent']['call_id'] = $call_id;                            
            $t['CallEvent']['extension'] = $ext;
            $t['CallEvent']['user_id'] = $user_id;    
            $t['CallEvent']['description'] = $text;      
            $t['CallEvent']['button_data'] = $button_data;      
            $t['CallEvent']['event_type'] = $event_type;      
            $t['CallEvent']['level'] = $text;      
            if (!$this->CallEvent) $this->loadModel('CallEvent');
            $this->CallEvent->create();
            return ($this->CallEvent->save($t['CallEvent']));
    }    

    function _initChanges() {
        $changes = array();
        $changes['label'] = array();
        $changes['old_values'] = array();
        $changes['new_values'] = array();
        return $changes;    
    }
    
    function _setChanges($label, $old_value, $new_value, &$changes) {
        $changes['label'][] = $label;
        $changes['old_values'][] = $old_value;
        $changes['new_values'][] = $new_value;          
    }
    
    function _saveChanges($description, $old_values, $new_values, $account_id, $did_id, $section, $change_type='edit', $data = array()) {
        $this->loadModel('AccountsEdit');
        

            $data['user_id'] = AuthComponent::user('id');
            $data['user_username'] = AuthComponent::user('username');
            $data['old_values'] = $old_values;
            $data['new_values'] = $new_values;
            $data['account_id'] = $account_id;
            $data['did_id'] = $did_id;
            $data['section'] = $section;        
            $data['description'] = $description;
            $data['change_type'] = $change_type;     
            $this->AccountsEdit->create();
            $this->AccountsEdit->save($data);         

    }
    


    // functions to construct easily read schedule times for time-sensitive calltypes, on-calls, and message summary
    function _getSchedule($schedule, $php_daysofweek, $timesensitive=1) {  
        $html = '';
        if (!$timesensitive) return 'Default';
        $day_range = '';
        if (isset($schedule['start_day']) && $schedule['start_day'] && isset($schedule['end_day']) && $schedule['end_day']) {
            $day1 = substr($schedule['start_day'], 0, -4);
            $ts = strtotime("today " . substr($schedule['start_day'], -4, 2) . ":" . substr($schedule['start_day'], -2) . ":00");
            $mytime1 = date('g:ia', $ts);                     
            $day2 = substr($schedule['end_day'], 0, -4);
            if ($day2 > 7) $day2 -= 7;
            $ts = strtotime("today " . substr($schedule['end_day'], -4, 2) . ":" . substr($schedule['end_day'], -2) . ":00");
            $mytime2 = date('g:ia', $ts);                     

            $day_range = $php_daysofweek[$day1] . " " . $mytime1 . " - " . $php_daysofweek[$day2] . " " . $mytime2;
            
        }
        $schedule['days'] = $this->_getDayRanges($schedule, $php_daysofweek);
        $schedule['day_range'] = $day_range;
        $s_array = array();
        if (isset($schedule['start_date']) && $schedule['start_date'] && isset($schedule['end_date']) && $schedule['end_date']) {
            // check if a user friendly day formats are available instead of standard mysql date format
            if (isset($schedule['startdate'])) $s_array[] = $schedule['startdate'] . ' - ' . $schedule['enddate'];
            else $s_array[] = $schedule['start_date'] . ' - ' . $schedule['end_date'];
        }
        /*if ($schedule['start_day'] && $schedule['end_day']) {
            $s_array[] = $daysofweek[$schedule['start_day']] . ' - ' . $daysofweek[$schedule['end_day']] . " " . $schedule['starttime'] . ' - ' . $schedule['endtime'];
        }*/
        $html_array = array();
        if ($schedule['day_range']) $html_array[] = $schedule['day_range'];
        if (sizeof($schedule['days'])) {
            $temp = implode(', ', $schedule['days']);
            if (isset($schedule['start_time']) && $schedule['start_time'] && isset($schedule['end_time']) && $schedule['end_time']) {
                if (!isset($schedule['starttime'])) {
                    $schedule['starttime'] = date('g:i A', strtotime('Today ' . $schedule['start_time']));
                    $schedule['endtime'] = date('g:i A', strtotime('Today ' . $schedule['end_time']));
                }
                $temp .= (' ' . $schedule['starttime'] . '-' . $schedule['endtime']);
            }
            $s_array[] = $temp;
        }
        if (sizeof($s_array)) $html_array[] =  implode('<br>', $s_array);
        else if (!$schedule['day_range']) $html_array[] = 'Default';  

        return implode('<br>', $html_array);
    }    
    
    function _getDayRanges($schedule, $php_daysofweek) {
        // returns easily-read day of the week scheduling, ie 'Mon-Fri', 'M, Tue-Fri', etc...
        $first_day_in_range = '';
        $last_activeday = '';
        if (isset($schedule['mon']) && $schedule['mon']) $thedays[1] = 1;
        else $thedays[1] = 0;
        if (isset($schedule['tue']) && $schedule['tue']) $thedays[2] = 1;
        else $thedays[2] = 0;
        if (isset($schedule['wed']) && $schedule['wed']) $thedays[3] = 1;
        else $thedays[3] = 0;
        if (isset($schedule['thu']) && $schedule['thu']) $thedays[4] = 1;
        else $thedays[4] = 0;
        if (isset($schedule['fri']) && $schedule['fri']) $thedays[5] = 1;
        else $thedays[5] = 0;
        if (isset($schedule['sat']) && $schedule['sat']) $thedays[6] = 1;
        else $thedays[6] = 0;
        if (isset($schedule['sun']) && $schedule['sun']) $thedays[7] = 1;
        else $thedays[7] = 0;
        
        $first_day_in_range = '';
        $last_activeday = '';
        $first = true;
        $days = array();
        foreach ($thedays as $k => $v) {

                if ($v == 0) {
                    if ($last_activeday) {
                        if ($first_day_in_range == $last_activeday)
                            $days[] = substr($php_daysofweek[$first_day_in_range], 0, 3);
                        else $days[] = substr($php_daysofweek[$first_day_in_range],0,3) . '-' . substr($php_daysofweek[$last_activeday],0,3);
                    }
                    $first_day_in_range = '';
                    $last_activeday = '';
                }
                else {
                    //mark the first day in the active range
                    if ($first_day_in_range === '') $first_day_in_range = $k;
                    
                    // do some clean up if we're checking last day of week
                    if ($k == 7) {
                        if ($first_day_in_range == $k) {
                            $days[] = substr($php_daysofweek[$first_day_in_range], 0, 3);
                        }
                        else {
                            $days[] = substr($php_daysofweek[$first_day_in_range], 0, 3) . '-' . substr($php_daysofweek[7], 0, 3);
                        }
                    }
                    else {
                        $last_activeday = $k;
                    }
                }

        }
        return $days;
    }    
    
    
    function _sendemail($subject, $content, $recipient, $format='html', $text_content='', $smtp_profile = null) {
        
        //If the calling function has not specified a specific smtp profile configuration to use (app/Config/email.php)
        //then we use the default profile
        if (!isset($smtp_profile)) {
            $smtp_profile = 'default';
        }
        
        //CakeEmail function can take an array of email addresses, so if we do not have an array
        //we turn a string into one. the string can either be a single email address, or a list
        //of emails seperated by a semicolon
        if (!is_array($recipient)) {
            $recipients = explode(';', $recipient);
        }
        else $recipients = $recipient;
        
        //include the modules for sending emails, and validating email addresses
        App::uses('CakeEmail', 'Network/Email');
        App::uses('Validation', 'Utility');
        
        //check each email address in the array and make sure it is a valid format
        //if not, remove it from the list
        foreach ($recipients as $k => &$val) {
            $val = trim($val);
            if (!Validation::email($val, false)) {
                //TLC - We need to log something here
                unset($recipients[$k]);
            }
        }
        
        //if no email addresses are left after validation checks, fail and return false
        if (sizeof($recipients) < 1) {
            return false;
        }
        
        //if for some reason no text content was generated, just copy the standard content
        if ($text_content == '') $text_content = $content;
        $bad_address = false;
        
        // catch a badly formatted email address
        try {
            $Email = new CakeEmail($smtp_profile);
            $Email->to($recipients);
        }    catch (SocketException $e) {
             return false;
        }
        
        //attempt to send the email
        try {
            $Email->emailFormat($format);
            $Email->template('msg');
            $Email->viewVars(array('content' => $content, 'text_content' => $text_content));
            $Email->subject($subject);
            if ($Email->send()) {
                return "Delivered to ".$smtp_profile." smtp server: " . implode(', ', $recipients);
            }
            else {
                return false;
            }
        }    catch (Exception $e) {
                    //Failed to deliver message to the SMTP server
                    return false;
        }
    }

    public function _authenticate($data) {
        $this->loadModel('User');
            $user = $this->User->find('first', array('conditions' => array('User.username' => $data['username'])));
            $passwordHasher = new SimplePasswordHasher();
            if ($user && $user['User']['password'] !== $passwordHasher->hash($data['password'])) {
                    return false;
            }
            
            unset($user['User']['password']);  // don't forget this part
            $this->Auth->login($user['User']);
            return $user;
            // the reason I return the user is so I can pass it to Authcomponent::login if desired
    }
    
    //TLC -- Moved crypto functions to component
    //leaving redirects here temporarily
    function encrypt($str, $key=ENCRYPTION_KEY){
        $this->Crypto = $this->Components->load('Crypto');
        return $this->Crypto->encrypt($str,$key);
    }

    //TLC -- Moved crypto functions to component
    //leaving redirects here temporarily
    function decrypt($str, $key=ENCRYPTION_KEY){
        $this->Crypto = $this->Components->load('Crypto');
        return $this->Crypto->decrypt($str,$key);
    }
    
    function _dashboard_authenticate($did_id) {
        $subaccount_id = AuthComponent::user('subaccount_id');
        $account_id = AuthComponent::user('account_id');
        if (!empty($subaccount_id)) {
            if ($did_id == $subaccount_id) return true;
            else return false;
        }
        if (!empty($account_id)) {
            $this->loadModel('DidNumber');
            $this->DidNumber->recursive = 0;
            $dids = $this->DidNumber->find('list', array(
                'fields' => array('DidNumber.id'),
                'conditions' => array('deleted' => '0', 'account_id' => $account_id)
            ));
            if (in_array($did_id, $dids)) return true;
            else return false;
        }
    }
    
    public function dashboard_index($did_id) {
        $this->index($did_id);
        $this->render('index');
    }    
    
    public function dashboard_add($did_id) {
        $this->add($did_id);
        $this->render('add');
    }    

    public function dashboard_edit($did_id, $id) {
        $this->edit($did_id, $id);
        $this->render('add');
    }    
    
    public function dashboard_delete($did_id, $id) {
        $this->delete($did_id, $id);
        $this->render('delete');
    }        
}
