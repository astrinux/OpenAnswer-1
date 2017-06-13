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

<?php
    $this->extend('/Common/view');
    echo $this->Html->script('app.vn-ver1');
    $this->start('css');

?>
    <style>
    
        .minder_warn_color_1 td {background: <?php echo $settings['minder_warn_color_1']; ?>}
        .minder_warn_color_2 td {background: <?php echo $settings['minder_warn_color_2']; ?>}
        .minder_warn_color_3 td {background: <?php echo $settings['minder_warn_color_3']; ?>}
    
    </style>   
<?php
    $this->end();
?>

    <script type="text/javascript">
        var recentsearch = [];
        var settings = <?php echo json_encode($settings) ?>;
        var agentStatus = 'Off';
        var manualPop = false;
        var CONTACT_PHONE = '<?php echo CONTACT_PHONE ?>';
        var CONTACT_CELL = '<?php echo CONTACT_CELL ?>';
        var CONTACT_TEXT = '<?php echo CONTACT_TEXT ?>';
        var CONTACT_EMAIL = '<?php echo CONTACT_EMAIL ?>';
        var CONTACT_VMAIL = '<?php echo CONTACT_VMAIL ?>';
        var CONTACT_WEB = '<?php echo CONTACT_WEB ?>';
        var CONTACT_PAGER = '<?php echo CONTACT_PAGER ?>';
        var CONTACT_FAX = '<?php echo CONTACT_FAX ?>';
        var BUTTON_DISPATCH = '<?php echo BUTTON_DISPATCH ?>';
        var BUTTON_DELIVER = '<?php echo BUTTON_DELIVER ?>';
        var FAX_EMAIL = '<?php echo FAX_EMAIL; ?>';
        var CALLOUT_SUCCESS = '<?php echo CALLOUT_SUCCESS; ?>';
        var scrollbarWidth;
        scrollbarWidth = getScrollbarWidth();
        var checked_in = false;
        var pause_agent = <?php echo $pause_agent? 'true': 'false'; ?>;
        var error403_count = 0;
        var break_button_pressed = false;
        var breakTimer;
        var break_count_reasons = <?php echo json_encode($break_count_reasons);?>;
        var GlobalVars = {'calltypedata': null, 'prompts': <?php echo json_encode($global_options['prompts']); ?>};
        <?php foreach($global_options['actions_all'] as $k => $a) {?>
            var <?php echo $a['type']?> = '<?php echo $k; ?>';
        <?php
}  
?>
        var EVENT_MINDERCLICK = '<?php echo EVENT_MINDERCLICK; ?>';
        var EVENT_DIALOUT = '<?php echo EVENT_DIALOUT; ?>';
        var EVENT_PATCH = '<?php echo EVENT_PATCH; ?>';
        var EVENT_CALLTYPE = '<?php echo EVENT_CALLTYPE; ?>';
        var EVENT_DEBUG  = '<?php echo EVENT_DEBUG; ?>';
        var EVENT_REPOP  = '<?php echo EVENT_REPOP; ?>';
        var EVENT_FILL_PROMPT    = '<?php echo EVENT_FILL_PROMPT; ?>';
        var break_id = '';
        var buttonClass = new Object();
        buttonClass[CONTACT_FAX] = 'c_fax';
        buttonClass[CONTACT_PHONE] = 'c_txf';
        buttonClass[CONTACT_CELL] = 'c_cell';
        buttonClass[CONTACT_TEXT] = 'c_text';
        buttonClass[CONTACT_EMAIL] = 'c_email';
        buttonClass[CONTACT_VMAIL] = 'c_vmail';
        buttonClass[CONTACT_WEB] = 'c_web';
        var myExtension = '<?php echo $user_extension; ?>';
        var myRole = '<?php echo AuthComponent::user('role'); ?>';
        var myName = "<?php echo trim(AuthComponent::user('firstname')) . ' ' . trim(AuthComponent::user('lastname')); ?>";
        var myFirstName = "<?php echo AuthComponent::user('firstname'); ?>";
        var myId = '<?php echo AuthComponent::user('id'); ?>';
        var myUsername = "<?php echo AuthComponent::user('username'); ?>";
        var myQueues = <?php echo json_encode($queues); ?>;
        var myPenalties = <?php echo json_encode($penalties); ?>;
        var ocServer = '<?php echo $openconnector_server; ?>';
        var callResults = <?php echo json_encode($global_options['callout_results']); ?>;
        
        var clockInterval = null;
        var msgClockInterval = null;
        var lastPopAttempt = null;
        var tabCounter = 8;
        var serverConnectRetry = false;
        var agentCheckTimer;
        var msgCheckTimer;
        var callCheckTimer;
        var reconnectTimer;
        var reconnectCount = 0;
        var callStatus; 
        var callId = null;
        var currentCall = null;
        var currentScheduleId = null;
        var currentInstructions;
        var emptyText = '<i class="empty">empty</i>';
        var emptyUrl = 'enter url';
        var websocketConnected = false;
        var callStart; 
        var timeOfDay;
        var socket;
        var main_offset = 0;
        var client_tz;    
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var callInProgress = false;

        // get browser time offset in msec      
        var maintabs;
        var userprompts = ['Caller refused', 'N/A', 'Individual', 'Caller hung up'];
        var actiondefs = <?php echo json_encode($global_options['actions']); ?>;        
        var bulletins = <?php echo json_encode($bulletins); ?>;
        var required = <?php echo json_encode($required); ?>;
        var oncallTabs = null;
        var dialog_callback = null;     
        var dialogWinCallback = null ;
        var msgDialogWinCallback = null ;
        var incomingUniqueId = '';          
        var outboundId = null;
        var msgListLength;
        var currentIndex;
        var msgArray;
        var msgArrayById;
        var permissions = <?php echo json_encode($permissions); ?>;
        var google_api_key = '<?php echo $google_api_key; ?>';


        var callbox_employees;  // TO BE DELETED
                    


        function getBrowserOffset() {
            var d = new Date();  
            return (d.getTimezoneOffset() * 60000);   
        }
        var browser_offset = getBrowserOffset();
        
        
        
     
        function find_did_go_handler(t) {
            if (didSpecified() ) {
                if ($.isNumeric($('#find_did').val())) {
                    loadPage(this, "<?php echo $this->Html->url(array('controller' => 'DidNumbers', 'action' => 'edit')); ?>/"  + $('#find_did').val(), 'did-content');
                    didLayout.center.children.layout1.close('west');
                    $('.subtabs').find('li').removeClass('active');
                    $('.subtabs li:first-child').addClass('active');
                }
                else {
                    loadPagePost(null, '<?php echo $this->Html->url(array('controller' => 'DidNumbers', 'action' => 'index')); ?>/', 'did-list','term='+$('#find_did').val().replace('search', '') + '&' + $(t).parents('form').serialize(), null);
                    didLayout.center.children.layout1.open('west'); 
                    $('#did-content').html('');
                }
            } 
            else {
                loadPagePost(null, '<?php echo $this->Html->url(array('controller' => 'DidNumbers', 'action' => 'index')); ?>/', 'did-list', $(t).parents('form').serialize(), null);
                didLayout.center.children.layout1.open('west'); 
                $('#did-content').html('');  
            }
        }
        
        //disable the 'back' button on the browser
        history.pushState(null, null);
        window.addEventListener('popstate', function(event) {
            history.pushState(null, null);
        });    
        </script>  
        <div id="resizer">
            <button id="resizer_incr"><?php echo $this->Html->image('icons/larger.png', array('width' => '20', 'height' => '20'))?></button><button id="resizer_decr" href="#" ><?php echo $this->Html->image('icons/smaller.png', array('width' => '20', 'height' => '20'))?></button><?php if (!$admin_only) {
                ?><button href="#" id="resizer_admin"><?php echo $this->Html->image('icons/screens.png', array('width' => '20', 'height' => '20'))?></button>
            <?php
        } ?>
        </div>
        <div id="broadcast_msg" class="is_hidden">
            <a href="#" id="broadcast_close"><?php echo $this->Html->image('icons/icon-alert-close.png')?>
            <div id="broadcast_msg_content">
            </div>
        </div>

<!--<div id="testdiv" style="height: 100%; min-height: 400px; position:fixed; top:0px; left:0px;z-index: 99; background:red;width: 100px;">2
</div>    -->
        <div id="screen">
            <div style="margin: auto; width: 200px; height: 100px;">
                <center><?php echo $this->Html->image('loadera32.gif', array('align' => 'middle'))?> &nbsp;&nbsp;Loading...</center>
            </div>
        </div>

<?php if (!$admin_only && $this->Permissions->isAuthorized("OpenanswerIndexWelcome",$permissions)) {
                    ?>
        <div class="ui-layout-east" style="padding:0px;" id="minders-panel">
            <div style="height:60%">
                <div class="ui-header">
                <?php
                    ?>
                    <div id="undel_cont" class="undel_cont_blink"><label><a href="#" id="load_undelivered">Undelivered:</a></label><span id="undel_msg"></span></div>
                    <label><a href="#" id="load_hold">Hold:</a></label><span id="held_msg"></span><br>
                    <?php
                ?>
                    <h3 align="center" style="margin:5px;">MINDERS</h3>
                </div>
                <div class="ui-layout-content" id="minders-content">
                                
                </div>
            </div>
        </div>
        
<?php
}
?>
        
        <div class="ui-layout-center" >
            <div style="padding:0px;" id="sidebar">
                <div><a href="#" id="sidebar_expander" onclick="checkSidebar(this);"><i class="fa fa-lg fa-fw fa-map-pin fa-rotate-90"></i></a></div>
                    <ul >
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexWelcome",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-welcome"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['welcome']; ?>"></i><span>Welcome</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexAccounts",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-account"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['accounts']; ?>"></i><span>Accounts</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexSetup",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-did"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['setup']; ?>"></i><span>Setup</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexReports",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-report"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['reports']; ?>"></i><span>Reports</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexCalllog",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-calls"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['calls']; ?>"></i><span>Call Log</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexMessages",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-messages" id="tabs-msgs"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['messages']; ?>"></i><span>Messages</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexComplaints",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-complaints"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['complaints']; ?>"></i><span>Complaints</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexMistakes",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-mistakes" id="tabs-mist"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['mistakes']; ?>"></i><span>Mistakes</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexBulletins",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-bulletin"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['bulletins']; ?>"></i><span>Bulletin</span></a></li>
            <?php } ?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexCampaigns",$permissions) && Configure::read('outbound_dialer_enabled')) { ?>
                        <li><a href="#" data-id="tabs-campaigns"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['campaigns']; ?>"></i><span>Campaigns</span></a></li>
            <?php }?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexUsers",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-users"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['users']; ?>"></i><span>Users</span></a></li>
            <?php }?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexRoles",$permissions)) { ?>
                        <li><a href="#" data-id="tabs-roles"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['users']; ?>"></i><span>Roles</span></a></li>
            <?php }?>
            <?php if ($this->Permissions->isAuthorized("OpenanswerIndexAppsettings",$permissions)) { ?>
                        <li><a href="#" id="app_settings"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['settings']; ?>"></i><span>Settings</span></a></li>
            <?php }?>
                        <li><a href="#" id="logout_btn"><i class="fa fa-lg fa-fw <?php echo $global_options['icons']['logout']; ?>"></i><span>Logout</span></a></li>
                    </ul>    
            </div>
                
                <div class="ui-layout-content" id="tabs_content">        
                    <div id="tabs-welcome" class="tab_content" style="height: 100%;">
                     <?php echo $this->element('tab_welcome'); ?>
         
                    </div>
                    <div id="tabs-report" class="tab_content hidden">
                     <?php echo $this->element('tab_reports'); ?>
                    </div>
                    
                    
                    <div id="tabs-account" class="tab_content hidden ">
                        <div class="ui-layout-north panel-content searchbox">
                            <div>
                        
                                <form autocomplete="off" >
<?php if ($this->Permissions->isAuthorized('AccountsFind',$permissions)) { ?>
                            <input type="hidden" id="find_account" style="width:300px;" name="find_account" class="find_account_sel2 auto">
                            <input type="submit" id="find_account_go" value="Go">&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
<?php if ($this->Permissions->isAuthorized('AccountsAdd',$permissions)) { ?>
                            <div class="header_btn fg_green"><a href="#" id="acct_add"><i class="fa fa-plus-circle fa-lg"></i> Add New Account</a></div>
<?php } ?>
<?php if ($this->Permissions->isAuthorized('AccountseditsView',$permissions)) { ?>
                            <div class="header_btn fg_blue"><i class="fa fa-history fa-lg"></i> <a href="#" class="didbtn" id="acct_history">Change History</a></div>
<?php } ?>
                                </form>

                            </div>
                        </div>
                        <div class="ui-layout-center">
                            <div class="ui-layout-west">
                                <div class="ui-layout-content" id="acct-list">
                                </div>
                            </div>
                            <div class="ui-layout-center">
                                <div class="ui-layout-content" id="acct-content">
                                    <div class="empty_content"><i class="fa <?php echo $global_options['icons']['accounts']; ?>"></i> Accounts</div>
                                </div>
                            </div>
                            <div class="ui-layout-east">
                                <div class="header"><input type="button" value="Cancel" id="acct_cancel"/><input type="button" id="acct_save_btn" value="Save" />
                                </div>  
                                <div class="ui-layout-content" id="acct-detail">
                                </div>                  
                            </div>
                            
                        </div>          
                
                    </div>
                    <div id="tabs-did" class="tab_content hidden " >
                        <div class="ui-layout-north  searchbox">
                            <div class="panel-content">
                                <form action="<?php echo $this->Html->url(array('controller' => 'DidNumbers', 'action' => 'index')); ?>" name="didsearch" method="POST" autocomplete="off" ><input type="hidden" id="did_format" name="data[Search][format]" value="">
                                    <div class="wrapper">
                                        <input type="hidden" id="find_did" style="width:400px;" name="data[Search][find_did]" class="find_did_sel2all">
                                        <input type="submit" id="find_did_go" class="didbtn" value="Go"><input type="submit" class="didbtn" value="Export" id="subacct_export">
                                        <div class="header_btn fg_grey"><a href="#" id="subacct_more"><i class="fa fa-plus fa-lg"></i> more</a></div> 
                                        <div class="header_btn fg_grey"><a href="#" data-dropdown="#recentsel"><i class="fa fa-history fa-lg"></i>
                                        recent</a></div>
                                        <div class="header_btn fg_green didbtn"><a href="#" id="show_opscreen"><i class="fa fa-phone-square fa-lg"></i> Operator Screen</a></div>       </div>
                                    <div class="is_hidden" id="adv_filter">
                                    <b>Difficulty:</b> <select name="data[Search][difficulty]">
                                        <option value="">All</option><option value="1">1
                                        <option value="2">2
                                        <option value="3">3
                                        <option value="4">4
                                        <option value="5">5
                                        <option value="6">6
                                        <option value="7">7
                                        <option value="8">8
                                        <option value="9">9
                                        <option value="10">10
                                        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <b>Status:</b> <select name="data[Search][status]"><option value="">All</option><option value="1">Taking calls</option><option value="0">Not taking calls</option></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </div>                                          
                                </form>
                            </div>                              
                                        <ul class="subtabs didbtns">
<?php if ($this->Permissions->isAuthorized('DidnumberIndex',$permissions)) { ?> <li><a href="#" id="subacct_basic">Basic Info</a></li> <?php } ?>
<?php if ($this->Permissions->isAuthorized('CalltypesIndex',$permissions)) { ?> <li><a href="#" id="subacct_ct">Call types</a></li> <?php } ?>
<?php if ($this->Permissions->isAuthorized('EmployeesIndex',$permissions)) { ?> <li><a href="#" id="subacct_emp">Employees</a></li> <?php } ?>
<?php if ($this->Permissions->isAuthorized('EmployeesIndex',$permissions)) { ?> <li><a href="#" id="subacct_oncall">On-Call</a></li> <?php } ?>
<?php if ($this->Permissions->isAuthorized('CrmsIndex'     ,$permissions)) { ?> <li><a href="#" id="subacct_crm">CRM</a></li> <?php } ?>
<?php if ($this->Permissions->isAuthorized('CalllogsIndex' ,$permissions)) { ?> <li><a href="#" id="subacct_calls" >Calls</a></li> <?php } ?>
<?php if ($this->Permissions->isAuthorized('MessagesIndex',$permissions)) { ?> <li><a href="#" id="subacct_msgs">Messages</a></li> <?php } ?>
<?php if ($this->Permissions->isAuthorized('NotesIndex',$permissions)) { ?> <li><a href="#" id="subacct_notes">Notes</a></li> <?php } ?>
<?php if ($this->Permissions->isAuthorized('DidnumberseditsIndex',$permissions)) { ?> <li><a href="#" id="subacct_history">Edit History</a></li> <?php } ?>
            <li id="subacct_more"><a href="#" data-dropdown="#moreoptions"><b>&bullet; &bullet; &bullet;</b></a></li>
                                        </ul>
                        </div>
                        
                        <div class="ui-layout-center">
                            <div class="ui-layout-west">
                                <div class="ui-layout-content" id="did-list">
                                </div>
                            </div>
                            <div class="ui-layout-center">
                                <div class="ui-layout-content" id="did-content">
                                    <div class="empty_content"><i class="fa <?php echo $global_options['icons']['setup']; ?>"></i> Setup</div>                    
                                </div>
                            </div>
                            <div class="ui-layout-east">
                                <div class="header"><input type="button" value="Cancel" id="subacct_cancel"/><input type="button" id="did_save_btn" value="Save" />
                                </div>  
                                <div class="ui-layout-content" id="did-detail">
                                </div>                  
                            </div>
                            
                        </div>          
                    </div>
                    <div id="tabs-messages" class="tab_content hidden ">
                        <div class="ui-layout-north panel-content searchbox">
                            <div>
                                <form autocomplete="off" id="msg-filter">
                                <input id="find_acct" style="width: 300px;" name="data[Search][did_id']" class="find_did_sel2" type="hidden">&nbsp;&nbsp;&nbsp; 
                                &nbsp;&nbsp;&nbsp;
                                <b>Msg#:</b> <input type="text" size="4" name="data[Misc][message_id]">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <b>Operator:</b> <input id="find_msg_user" name="data[Search][user_id]" style="width: 180px;"  class="find_user_sel2" type="hidden">
                                <input type="submit" value="Go" id="msgs_go">                               
                                    <br><br>
                                <input type="checkbox" name="data[Search][m_type][]" value="delivered"> Delivered &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="data[Search][m_type][]" id="chk_undelivered" value="undelivered"> Undelivered &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="data[Search][m_type][]" id="chk_unaudited" value="unaudited"> Not audited &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="data[Search][m_type][]" id="chk_hold" value="hold"> Save Hold&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="data[Search][m_type][]" value="minder"> Minder&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                Start: <input type="text" name="data[Search][m_start_date]" size="10" value="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" class="datepicker"> <input type="text" name="data[Search][m_start_time]" size="5" value="" class="timepicker">&nbsp; to &nbsp;
                                <input type="text" name="data[Search][m_end_date]" size="10" value="<?php echo date('Y-m-d'); ?>" class="datepicker"> <input type="text" name="data[Search][m_end_time]" size="5" value="" class="timepicker">&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="data[Search][m_group]" value="1"> Group results by account                                 
                                </form>
                            </div>
                        </div>
                        <div class="ui-layout-center">
                            <div class="ui-layout-center">              
                                <div class="ui-layout-content" id="msg-content">
                                    <div class="empty_content"><i class="fa <?php echo $global_options['icons']['messages']; ?>"></i> Messages</div>
                                </div>
                            </div>
                            <div class="ui-layout-east" > 
                                <div class="header">
                                    <input type="button" value="Cancel" id="msgs_cancel" />
                                </div>  
                                <div class="ui-layout-content" id="msg-detail">
                                </div>
                            </div>              
                        </div>
                    </div>
                    <div id="tabs-bulletin" class="tab_content hidden ">
                        <div class="ui-layout-north panel-content searchbox">
                                <div class="">
                                        <ul class="subtabs">
                                        <li><a href="#" id="bulletin_index">Bulletins</a></li>                                
                                        <li><a href="#" id="bulletin_create">Create Bulletin</a></li>                             
                                        <li><a href="#" id="bulletin_broadcast">Send Broadcast Msg</a></li>                               
                                        </ul>
                                </div>

                        </div>
                        <div class="ui-layout-center">
                            <div class="ui-layout-center">              
                                <div class="ui-layout-content" id="bb-content">
                                </div>
                            </div>
                            <div class="ui-layout-east"> 
                                <div class="header">
                                    <input type="button" value="Cancel" id="bulletin_cancel" /><input id="bb_save_btn" type="button" value="Save" />
                                </div>  
                                <div class="ui-layout-content panel-content" id="bb-detail">
                                </div>
                            </div>              
                        </div>               
                    </div>          
                    <div id="tabs-users" class="tab_content hidden ">
                        <div class="ui-layout-content ui-layout-north panel-content searchbox">
                            <?php echo $this->element('tab_user'); ?>           
                            
                        </div>
                        <div class="ui-layout-center">
                            <div class="ui-layout-center">              
                                <div class="ui-layout-content" id="user-content">
                                </div>
                            </div>
                            <div class="ui-layout-east"> 
                                <div class="header"><input type="button" value="Cancel" id="user_cancel" /><input id="user_save_btn" type="button" value="Save" />
                                </div>  
                                <div class="ui-layout-content panel-content" id="user-detail">
                                </div>
                            </div>              
                        </div>          
                    </div>          
                    <div id="tabs-roles" class="tab_content hidden ">
                        <div class="ui-layout-content ui-layout-north panel-content searchbox">
                            <?php echo $this->element('tab_role'); ?>           
                            
                        </div>
                        <div class="ui-layout-center">
                            <div class="ui-layout-center">              
                                <div class="ui-layout-content" id="role-content">
                                </div>
                            </div>
                            <div class="ui-layout-east"> 
                                <div class="header"><input type="button" value="Cancel" id="role_cancel" /><input id="role_save_btn" type="button" value="Save" />
                                </div>  
                                <div class="ui-layout-content panel-content" id="role-detail">
                                </div>
                            </div>              
                        </div>          
                    </div>          
                    <div id="tabs-calls" class="tab_content hidden ">
                        <div class="ui-layout-north panel-content searchbox">
                        <form autocomplete="off">
                        <div class="wrapper">

                        <input id="find_acct_call" style="width: 300px;"  class="find_did_sel2" type="hidden">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <label for="find_user2"><b>Operator:</b> </label><input name="data[Search][user_id]" style="width: 180px;"  class="find_user_sel2" type="hidden"><input type="submit" value="Go" id="calls_go"><br><br>
                                Start: <input type="text" name="data[Search][c_start_date]" size="12" value="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" class="datepicker">  <input type="text" name="data[Search][c_start_time]" size="5" value="" class="timepicker">&nbsp; to &nbsp;
                                <input type="text" name="data[Search][c_end_date]" size="12" value="<?php echo date('Y-m-d'); ?>" class="datepicker">  <input type="text" name="data[Search][c_end_time]" size="5" value="" class="timepicker">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                Min. duration (sec): <input type="text" name="data[Search][c_min_duration]" size="4" value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                Wrap-up %: <input type="text" name="data[Search][c_wrapup]" size="3" value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>                              
                        </form>                                         
                        </div>
                        <div class="ui-layout-center">
                            <div class="ui-layout-center">              
                                <div class="ui-layout-content" id="call-content">
                                    <div class="empty_content"><i class="fa <?php echo $global_options['icons']['calls']; ?>"></i> Calls</div>                          
                                </div>
                            </div>
                            <div class="ui-layout-east"> 
                                <div class="header">
                                    <input type="button" value="Cancel" id="calls_cancel"/>
                                </div>  
                                <div class="ui-layout-content" id="call-detail">
                                </div>
                            </div>                
                        </div>
                    </div>          

                 
                    <div id="tabs-complaints" class="tab_content hidden ">
                        <div class="filter ui-layout-north panel-content searchbox">
                            <form autocomplete="off" >
                                    <div class="wrapper">
                            
                                    <input id="find_acct_c" name="data[Search][did_id]" style="width: 300px;"  class="find_did_sel2" type="hidden">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="find_user2">Operator: </label><input id="find_user2" name="data[Search][user_id]" style="width: 180px;"  class="find_user_sel2" type="hidden"><input type="submit" id="complaints_go" value="Go">   
                                    <br><br>
                                    <b>Start Date:&nbsp;&nbsp; </b><input type="text" name="data[Search][start_date]" class="datepicker_pair" dtype="start"><label>End Date:</label> <input type="text" name="data[Search][end_date]" class="datepicker_pair" dtype="end">  &nbsp;&nbsp;<a id="complaints_add" href="#"><i class="fa fa-plus fa-lg"> </i> Add Complaint</a>
                                    </div>
                            </form>                             
        
                            
                        </div>
                        <div class="ui-layout-content ui-layout-center" id="complaints-content">
                                <div class="empty_content"><i class="fa <?php echo $global_options['icons']['complaints']; ?>"></i> Complaints</div>                    
                        </div>          
                    </div>
                    
                    <div id="tabs-mistakes" class="tab_content hidden ">
                        <div class="filter ui-layout-north panel-content searchbox">
                            <form name="mistake_form" autocomplete="off"  id="mistake-filter" action="<?php echo $this->Html->url(array('controller' => 'Mistakes', 'action' => 'index')); ?>" method="post">
                                    <input id="find_acct_m" name="data[Search][did_id]" style="width: 300px;"  class="find_did_sel2" type="hidden">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="find_user3">Operator: </label><input id="find_user3" name="data[Search][user_id]" style="width: 180px;"  class="find_user_sel2" type="hidden"><input type="submit" id="mistakes_go" value="Go" ><br><br>
                                    <b>Start Date:</b>&nbsp;&nbsp; </label><input type="text" name="data[Search][start_date]" class="datepicker_pair" value="<?php echo $start_of_week; ?>" dtype="start">&nbsp;&nbsp;&nbsp; <label>End Date:</label> <input type="text" name="data[Search][end_date]" class="datepicker_pair" dtype="end" value="<?php echo date('Y-m-d', strtotime('today')); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="data[Search][m_group]" value="1"> Group results by operator
                                <input type="checkbox" name="data[Search][summarize]" value="1" id="mistake_summary_check"> View mistake summary
                                <input type="hidden" name="data[Search][format]" value="" id="mistake_format">
                                    <button id="mistake_export" class="is_hidden">Export</button>

                            </form>
                        </div>
                        <div class="ui-layout-center">              
                                <div class="ui-layout-content" id="mistakes-content">
                                    <div class="empty_content"><i class="fa <?php echo $global_options['icons']['mistakes']; ?>"></i> Mistakes</div>                        
                                
                                </div>                  
                        </div>
                    </div>
                    
                    <div id="tabs-campaigns" class="tab_content hidden">
                        <div class="ui-layout-north panel-content searchbox">
                        <form autocomplete="off">
                            <input id="find_campaign" style="width: 300px;"  class="find_did_sel2" type="hidden">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><b>Campaign:</b> </label><input name="data[Search][campaign_id]" style="width: 180px;"  class="find_campaign" type="hidden"><input type="submit" value="Go" id="campaign_go"><br><br>
                        </form>                                         
                        </div>
                        <div class="ui-layout-center">
                            <div class="ui-layout-center">              
                                <div class="ui-layout-content" id="campaigns-content">
                                    <div class="empty_content"><i class="fa <?php echo $global_options['icons']['campaigns']; ?>"></i> Campaigns</div>                          
                                </div>
                            </div>
                            <div class="ui-layout-east"> 
                                <div class="header">
                                    <input type="button" value="Close" id="campaigns-close" onclick="campaignsLayout.center.children.layout1.close('east');"/>
                                </div>  
                                <div class="ui-layout-content" id="campaigns-detail">
                                </div>
                            </div>                
                        </div>
                    </div>
                    
                </div>
            
                
        </div>
        <div class="ui-layout-north " id="oaheader">
            <div class="logo"><?php echo $this->Html->image('/themes/vn/logo-openanswer.png', array('width' => '165', 'height' => '32'))?></div>
            <div id="my_extension">
            </div> 
             

            <div id="welcome"><?php 
                $photo = AuthComponent::user('photo');
                if (!empty($photo)) {
                    echo '<div class="avatarS"><img src="data:'.$photo.'"></div>';
                }
                else {
                    $initials = substr(trim(AuthComponent::user('firstname')), 0, 1) . substr(trim(AuthComponent::user('lastname')), 0, 1);
                    if (!empty($initials)) echo '<div class="avatarS">'.$initials.'</div>'; 
                    else echo '<div class="avatarS"><i class="fa fa-user fa-5x"></i></div>';      
                }
            ?>
                <div class="userBlock"><span>Welcome</span><br><?php if (AuthComponent::user('firstname')) echo AuthComponent::user('firstname'); else echo AuthComponent::user('user_name')?></div>
            </div>     
            <div id="num_on_break"></div>
            <div id="break_timer">
            <input  type="hidden" id="ts_start" value="0">
            <span>BREAK: </span><span id="sw_h">00:</span><span></span><span id="sw_m">00</span><span>:</span><span id="sw_s">00</span>
        </div>
            <div id="headerbtns">
                <?php 
                if (!$admin_only) {
                    echo $this->element('taking_calls_btns');           
                }
                ?>

            </div>
            
                        
        </div>
        <div id="footer" class="ui-layout-south panel-content" style="background: url(/img/bg-logo.png) no-repeat bottom right;">
            <div style="clear:both; margin-top:30px;"><a href="http://www.voicenation.com/openanswer">download openanswer</a> | Server Status: <span id="serverStatus"></span> Server Delay: <span id="serverDelay"></span>
            </div>
            
        </div>

        <div id="dialogWin">

        </div>
        
        <div id="dialogWinSave">

        </div>    
        //Dialog box for interacting with external applications (CRMs, ticketing systems, etc.)
        <div id="dialogIntegration">
        </div>

        <div id="add-did">
        <center><input type="hidden" id="add_did_id" value=""><br><br>Add number: <input id="number-to-add" value="" class="phonenumber" type="text" size="20"><input type="submit" value="Go" id="acct_add_subacct">&nbsp;&nbsp;<button id="acct_add_cancel">Cancel</button></center>
        </div>
        
        <div id="record-did">
        <div id="record-did-content">
        </div>
        
        </div>    
        <div id="broadcastWin"><br><br>
            <center>
            <b>Enter message:</b><br><br> <input type="text" value="" size="60" id="broadcast" maxlength="300"><br><br>
            <i>(NOTE: Message will be broadcasted to <b>all</b> OpenAnswer users)</i>
            </center>
        </div>    
        
        <div id="msgDialogWin">

        </div>
                
        <div id="msgWin">
            <div class="content">
                
            </div>
        </div>    

        <div id="operatorScreen"> 
            <div class="ui-layout-north"> 
                <div class="ui-layout-content ">    
                    <div id="screen_title">
                    </div>
                        <div id="account_info">
                            <input type="hidden" name="test_time_save" id="test_time_save">    

                            <!--<div class="textbox">
                                Phone Number: <input type="text" name="did" id="a_did" value="" size="12" disabled>
                            </div>   -->   
                            <div class="textbox">
                                <i class="fa fa-hourglass-2"></i> Call Timer: <input type="text" value="" id="call_time" size="4" disabled>
                            </div>     
                            <div class="textbox">
                                <i class="fa fa-clock-o"></i> Local Time: <input type="text" id="local_time" value="" size="26" disabled> <i><span id="local_tz"></span></i>
                            </div>
                            <div class="textbox">
                                <i class="fa fa-clock-o"></i> Caller ID: <span id="op_caller_id"></span>
                            </div>  
                            <!--<div class="textbox">
                                <input type="text" id="office_status" value="" size="10" disabled>
                            </div>-->
                            <div class="textbox test_box">
                                <i class="fa fa-gear"></i> Test: <input type="text" style="width: 0; height: 0; top: -1000px; position: absolute;"><input type="text" style="width: 100px;" name="test_time" id="test_time" value="" title="View the operator screen for a different date/time by entering it here" >&nbsp;<input type="submit" value="GO" id="opscreen_test">
                            </div>

                            <div style="clear:both;"></div>         
                        </div>
                        <div id="answer_phrase">
                        </div>    
                </div>
            </div>
            <div class="ui-layout-west"> 
                <!--<div class="pane-header ui-state-active">West Header</div>-->
                <div class="ui-layout-content sleft" style="position:relative;">
                    <div id="op_notes" class="op_notes">
                    </div>          
                    <div id="op_notes_left" class="op_notes">
                    </div>          
                    <div id="calltypes">
                    </div>
                    
                    <?php echo $this->element('button_options'); ?>                 
                </div>
            </div> 
        
            <div class="ui-layout-center ui-corner-all"> 
                <!--<div class="pane-header ui-state-active">Center Header</div>-->
                    <div class="ui-layout-content scenter" id="opscreen_main">
                        <ul>
                            <li><a href="#tab-instructions">Instructions</a></li>
                            <!--<li><a href="#tab-call-events">Call Events</a></li>
                            <li><a href="#tab-deliveries">Deliveries</a></li>-->
                        </ul>
                        <div id="tab-instructions">
                            <div id="op_notes_center" class="op_notes">
                            </div>          
                            <div id="instructions" class="panel-content <?php echo $loginRole; ?>">
                            </div>
                            <div id="sandbox" class="hide"></div>
                        </div>
                        <!--<div id="tab-call-events">
                        </div>
                        <div id="tab-deliveries">
                        </div>-->

                    </div>
                    <div id="cb_emp" class="footer">
                        <div id="show_dispatch" style="float:right; width: 100px;">
                        <?php if (1) {
                            ?>
                            <input type="checkbox" id="show_disp" value="1"> Show Disp.
                            <?php
                        }
                        ?>
                        </div>                  
                        <div id="cb_emppicker">
                        </div>            
                        <div id="cb_empcontacts">
                        </div>

                    </div>                
            </div> 
            <div class="ui-layout-east ui-corner-all sright"> 
                <div class="ui-layout-center ui-corner-all"> 
                    <div class="ui-layout-content" id="oncall_lists">
                    </div>
                    <div class="footer" id="oncall_footer"></div>
                </div>
                <div class="ui-layout-north ui-corner-all"> 
                    <div id="op_notes_right" class="op_notes">
                    </div>        
                    <div class="ui-layout-content" id="company_content">
                        <div id="acct_type" class="cinfo highlight">
                        </div>
                        <div id="acct_addr" class="cinfo">
                        </div>
                        <div id="acct_hours" class="cinfo highlight">
                        </div>
                        <div id="acct_info" class="cinfo">
                        </div>
                        <div style="clear:both;"></div>
                        <div id="acct_files" class="cinfo2">
                        </div>
                    </div>
                </div>
            </div>   
            <div class="ui-layout-south ui-widget-header ui-corner-all" style="text-align:right;"> 
          <button id="edit_msg" class="msg_edit is_hidden" style="float:left;">Edit</button>
          <button id="save_msg" class="msg_edit is_hidden" style="float:left;">Save</button>
          <button id="cancel_msg" class="msg_edit is_hidden" style="float:left;">Cancel</button>
        <div id="msg_dispatch">
                <div style="float:left">
                    <button id="opscreen_security">View Security Questions</button>
                </div>
                <button id="opscreen_acctreview" style="float:left" title="Flag this account as needing review"><i class="fa fa-flag-o fa-lg"></i></button>
                <button id="opscreen_addnote">Add Note</button>
                <button id="opscreen_msgreview">Msg Review</button>
                <button id="opscreen_addevent">Add Call Event</button>
                <button id="opscreen_deliver">Mark Delivered</button>
                <button id="opscreen_dispatch">Dispatch</button>
  
  
                
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="cancel_reason" id="cancel_reason_sel"><option value="" >Select reason
                <?php
                foreach ($global_options['cancel_reasons'] as $k=>$v) {
                    ?>
                    <option value="<?php echo $k;?>"><?php echo $k;?></option>
                <?php
                }
                ?>
                </select>
                <button id="cancel_button" >Cancel</button>
                </div>

            </div>
    
        </div> 
        



    <!-- Call box dialog, contains controls for manipulating the current active call and for performing transfers or outbound dialing. -->
    <!-- Opened through the Operator screen by clicking on a employee who has a phone contact available -->
    <div id="callBox">
        <?php echo $this->element('dialout_options'); ?>
    

        <div id="callBoxResult" class="is_hidden">
            <h1><center>Call Result</center></h1>
            <div style="margin-left:30px;">           
                <?php 
                    foreach ($global_options['callout_results'] as $k=>$v) 
                    {
                ?>
                <input type="radio" name="result" value="<?php echo $k;?>" id="result<?php echo $k;?>">&nbsp;<label for="result<?php echo $k;?>"><?php echo $v;?></label><br>
                <?php
                    }
                ?>
            </div>
            <div id="callbuttons">
                <center><button class="actbtn" id="btn_ret2">Return</button>
                <button class="actbtn" id="btn_cancel2">Cancel</button></center>
            </div>
        </div>                      
    </div>
    <div id="breakDialog">
        <?php 
        foreach ($break_reasons as $b) {
                echo '<input type="radio" name="result" value="'.$b.'">&nbsp;'. $b;
                if ($b == 'Other') echo ' <input type="text" name="break_other" value="" size="20" />';
                echo '<br>';
        }
        ?>
        <br><br>
                <input type="Submit" value="Save" id="break_save">&nbsp;<input type="Submit" value="Cancel" id="break_cancel" />
        </div>
        
        <div id="mistakeDialog" class="vnform">

        </div>
        
        <div id="noteDialog" class="vnform">

        </div>    
        
        <div id="securityDialog">
        </div>    
        
        <div id="dialog-confirm" title="Please confirm">
            <div id="confirm_message"></div>
        </div>    
        
        <div id="dialog-alert" title="Info">
            <div id="alert_message"></div>
        </div>       


        <div id="page-loading" class="is_hidden"><?php echo $this->Html->image('loader.gif', array('width' => '20', 'height' => '20', 'align' => 'absmiddle'))?> Loading...</div>
        <div id="moreoptions" class="dropdown dropdown-tip">
            <ul class="dropdown-menu">
                <?php 
                if (Configure::read('calendar_enabled')) { 
                    ?>
<?php if ($this->Permissions->isAuthorized('CalendarsIndex',$permissions)) { ?> <li><a href="#" id="subacct_cal">Calendars</a></li> <?php }?>
                <?php
                }
                ?>
                <?php 
                if (Configure::read('outbound_dialer_enabled')) { 
                    ?>
<?php if ($this->Permissions->isAuthorized('CampaignsIndex',$permissions)) { ?> <li><a href="#" id="subacct_campaigns">Campaigns</a></li><?php }?>
                <?php
                }
                ?>

<?php if ($this->Permissions->isAuthorized('ComplaintsIndex',$permissions)) { ?> <li><a href="#" id="subacct_complaints" onclick="$('#moreoptions').dropdown('hide');">Complaints</a></li><?php }?>
<?php if ($this->Permissions->isAuthorized('FilesIndex',$permissions)) { ?> <li><a href="#" id="subacct_files" onclick="$('#moreoptions').dropdown('hide');">Files</a></li><?php }?>
<?php if ($this->Permissions->isAuthorized('MessagessummaryIndex',$permissions)) { ?> <li><a href="#" id="subacct_ms" onclick="$('#moreoptions').dropdown('hide');">Msg Summary</a></li><?php }?>
            
            </ul>
        </div>
        <div id="recentsel" class="dropdown dropdown-tip"><ul class="dropdown-menu"><li><a href="#" id="clear_search" >clear search history</a></li></ul></div>

<script type="text/javascript">
    var msgLayout;
    var complaintsLayout;
    var mistakesLayout;
    var callsLayout;
    var didLayout;
    var reportsLayout;
    var acctLayout;
    var userLayout;
    var roleLayout;
    var bbLayout;
    var welcomeLayout;
    var bodyLayout;
    var campaignsLayout;
    
    var dialogLayout;
    var msgWinLayout; 
    var dialogLayout_settings = {
            zIndex:             0       // HANDLE BUG IN CHROME - required if using 'modal' (background mask)
//    , stateManagement__enabled:   true    // enable stateManagement - automatic cookie load & save enabled by default
//      ,   stateManagement__includeChildren: true          
        ,   resizeWithWindow:   false   // resizes with the dialog, not the window
        ,   spacing_open:       6
        ,   spacing_closed:     6
        , closable: false
        ,   north__minSize:         120
        ,   north__maxSize:         160
        ,   south__size:            35 
        , west__size: 150
        , east__size: 200
        , east__children: {
            north__size:            300 
        }
        ,   west__minSize:      150 
        ,   east__minSize:      100 
        ,   west__maxSize:      250 
        //, south__size:        20 
        //, south__initClosed: true
        //, applyDefaultStyles:     true // DEBUGGING
        }
    ;   

    //window.onbeforeunload = checkPageReload;  


    $(function () 
    {
        $('#sidebar li a').on('click', function() 
        {
            var link_id = $(this).attr('data-id');
            $('#sidebar li a').removeClass('active');
            $('#sidebar a[data-id=' + link_id + ']').addClass('active');
            $.cookie('active-tab', link_id);
            $('#'+link_id).siblings().hide();
            $('#'+link_id).show();
            
            if (link_id == 'tabs-messages') 
            {
                if (!msgLayout) 
                {
                    msgLayout = $('#tabs-messages').layout(
                    {
                        north__size:    80,
                        north__enableCursorHotkey: false,
                        north__resizable: false,
                        north__closable: false,
                        center__enableCursorHotkey: false,
                        center__children: 
                        {
                            east__size: 900,
                            east__enableCursorHotkey: false,
                            east__resizable: true,
                            east__closable: true,
                            east__onclose: function() 
                            {
                                $('#msg-detail').html('');
                            },
                            east__initClosed: true,
                            east__fxName: "slide"
                        }
                    });
                }
            }
            
            else if (link_id == 'tabs-welcome') 
            {
                if (!welcomeLayout) 
                {
                    welcomeLayout = $('#tabs-welcome').layout(
                    {
                        north__size:    40,
                        north__enableCursorHotkey: false,
                        north__resizable: false,
                        north__closable: false
                    });
                }
            }
            
            else if (link_id == 'tabs-bulletin') 
            {
                if (!bbLayout) {
                        bbLayout = $('#tabs-bulletin').layout({
                            north__size:    40,
                            north__enableCursorHotkey: false,
                            north__resizable: false,
                            north__closable: false,
                            center__enableCursorHotkey: false,
                            center__children: {
                                east__size: 900,
                                east__enableCursorHotkey: false,
                                east__resizable: true,
                                east__closable: true,
                                east__onclose: function() {
                                    $('#bulletin-detail').html('');
                                },
                                east__initClosed: true,
                                east__fxName: "slide"
                            }

                        });
                    }
                    loadPage(this, '/Bulletins' , 'bb-content');
                    
                }               
                
                else if (link_id == 'tabs-report') {
                    if (!reportsLayout) {
                        reportsLayout = $('#tabs-report').layout({
                            north__size:    40,
                            north__enableCursorHotkey: false,
                            north__resizable: false,
                            north__closable: false
                        });
                    }
                }               
                else if (link_id == 'tabs-complaints') {
                    if (!complaintsLayout) {
                        complaintsLayout = $('#tabs-complaints').layout({
                            north__size:    80,
                            north__enableCursorHotkey: false,
                            north__resizable: false,
                            north__closable: false
                        });
                    }
                }               
                else if (link_id == 'tabs-mistakes') {
                    if (!mistakesLayout) {
                        mistakesLayout = $('#tabs-mistakes').layout({
                            north__size:    80,
                            north__enableCursorHotkey: false,
                            north__resizable: false,
                            north__closable: false
                        });
                    }
                }                   
                
                else if (link_id == 'tabs-did') {
                    if (!didLayout) {
                        didLayout = $('#tabs-did').layout({
                            resizable: true,
                            closable: true,
                            fxName: 'slide',
                            north__size:    80,
                            north__enableCursorHotkey: false,
                            center__children: {
                                west__size: 660,
                                west__enableCursorHotkey: false,
                                west__spacing_open: 0,
                                west__spacing_closed: 0,                                
                                west__initClosed: true,
                                east__size: 900,
                                east__enableCursorHotkey: false,
                                east__onopen: function() {
                                        $('input.didbtn ').prop('disabled', true);
                                        $('.didbtns').hide();
                                    
                                    $(".find_did_sel2").select2("enable", false);
                                },
                                east__onclose: function() {
                                    $('#did-detail').html('');
                                    $('input.didbtn ').prop('disabled', false);
                                    $('.didbtns').show();                           
                                    $(".find_did_sel2").select2("enable", true);
                                    $('#did_save_btn').prop('disabled', false);
                                },
                                east__initClosed: false  // open initially, see note below about zero-height error
                            }
                        });
                    }
                    // JRW - east pane is initially open since it potentially creates another layout.  If not initially 
                    // open, the child layout throws a warning about the parent content having zero height when it is 
                    // initially opened.
                    didLayout.center.children.layout1.close('east');
                    
                }           
                else if (link_id == 'tabs-account') {
                    if (!acctLayout) {
                        acctLayout = $('#tabs-account').layout({
                            resizable: true,
                            closable: true,
                            fxName: 'slide',
                            north__enableCursorHotkey: false,                         
                            north__size:    80,
                            center__children: {
                                east__size: 900,
                                east__resizable: true,
                                east__enableCursorHotkey: false,
                                east__onopen: function() {
                                    //acctLayout.center.children.layout1.close('west');
                                },
                                west__size: 460,
                                west__enableCursorHotkey: false,
                                east__initClosed: true,
                                west__initClosed: true,
                                west__onopen: function() {
                                    //acctLayout.center.children.layout1.close('east');
                                }
                            }
                        });
                    }
                }               
                else if (link_id == 'tabs-users') {
                    if (!userLayout) {
                        userLayout = $('#tabs-users').layout({
                            resizable: true,
                            closable: true,
                            fxName: 'slide',
                            north__size:    80,
                            north__enableCursorHotkey: false,
                            center__children: {
                                east__enableCursorHotkey: false,
                                east__size: 900,
                                east__initClosed: true
                            }
                        });
                    }
                    <?php echo $this->element('tab_user_options'); ?>
        
                }                       
                else if (link_id == 'tabs-roles') {
                    if (!roleLayout) {
                        roleLayout = $('#tabs-roles').layout({
                            resizable: true,
                            closable: true,
                            fxName: 'slide',
                            north__size:    80,
                            north__enableCursorHotkey: false,
                            center__children: {
                                east__enableCursorHotkey: false,
                                east__size: 900,
                                east__initClosed: true
                            }
                        });
                    }
                    loadPage(this, '/Roles' , 'role-content');
                }                       
                else if (link_id == 'tabs-calls') {

                    if (!callsLayout) {
                        callsLayout = $('#tabs-calls').layout({
                            resizable: true,
                            closable: true,
                            fxName: 'slide',                          
                            north__size:    80,
                            north__enableCursorHotkey: false,
                            center__children: {
                                east__enableCursorHotkey: false,
                                east__size: 900,
                                east__onclose: function() {
                                    $('#call-detail').html('');
                                },
                                east__initClosed: true
                            }                       
                        });
                    }
                }                       
                else if (link_id == 'tabs-campaigns') {
                    if (!campaignsLayout) {
                        campaignsLayout = $('#tabs-campaigns').layout({
                            resizable: true,
                            closable: true,
                            fxName: 'slide',                          
                            north__size:    80,
                            north__enableCursorHotkey: false,
                            center__children: {
                                east__enableCursorHotkey: false,
                                east__size: 900,
                                east__onclose: function() {
                                    $('#campaign-detail').html('');
                                },
                                east__initClosed: true
                            }                       
                        });
                    }                 
                    loadPage(this, '/OutboundDialer/Campaigns/active/' , 'campaigns-content');
                }      

                // activate the first sub-tab if there is one
                if (link_id != 'tabs-did') {
                    $('#' + link_id).find('.subtabs li a:first').trigger('click');
                }
        });


        bodyLayout = $('body').layout({
            east__enableCursorHotkey: false,
            west__size: 50,
            west__spacing_open: 0,
            west__spacing_closed: 0,    
            west__animatePaneSizing: true,
            east__size: 200,
            south__size:    90,
            south__enableCursorHotkey: false,
            north__size: 55,
            north__enableCursorHotkey: false,
            north__spacing_open: 0,
            north__spacing_closed: 0,       
            west__initClosed: false,
            east__initClosed: true,
            south__initClosed: true,      
            north__resizable: false,
            center__onresize: function() {
                                                
            }
        });     
        
        <?php
        // check if we need to connect via websocket to OpenConnector
        if (!$admin_only) { 
            ?>
        connectToServer();

        <?php 
        }
        
        // load the last selected section (saved in a cookie) by triggering a click action on the menu link
        if (Configure::read('remember_last_section')) {
            ?>
            if ($.cookie('active-tab') &&  $('#sidebar a[data-id=' + $.cookie('active-tab') + ']').length > 0) {
                $('#sidebar a[data-id=' + $.cookie('active-tab') + ']').trigger('click');
            }
            else $('#sidebar a[data-id=tabs-welcome]').trigger('click');
            <?php
        }   
        else {
            ?>
            $('#sidebar a[data-id=tabs-welcome]').trigger('click');
            <?php
        }
        ?>
            
    var resizeTimer;
    
    $(window).on('resize orientationChange', function(e) {
        <?php 
        // check target since jquery ui dialog resize will trigger the event also 
        ?>
        if (e.target == window && !$('#operatorScreen').dialog('isOpen') ) {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                $('#operatorScreen').dialog('option', 'width', Math.floor($('body').width()  * .95));
                $('#operatorScreen').dialog('option', 'height', Math.floor($('body').height()  * .95));
                if (dialogLayout) dialogLayout.resizeAll();
                $('#msgDialogWin').dialog('option', 'width', Math.floor($(window).width()  * .94));
                $('#msgDialogWin').dialog('option', 'height', Math.floor($(window).height()  * .94));
                
            }, 250);
        }
    });    
    
    $('#operatorScreen').dialog('option', 'width', Math.floor($('body').width()  * .95));
    $('#operatorScreen').dialog('option', 'height', Math.floor($('body').height()  * .95)); 
});

function checkSidebar(t) {
    if (!$('#sidebar').hasClass('pinned')) {
        $('#sidebar').addClass('pinned');
        $('#tabs_content').css('padding-left', '125px');
        $(t).find('i').removeClass('fa-rotate-90');
        localStorage.setItem('oa_sidebar', 'expanded');    
    }
    else {
        $('#sidebar').removeClass('pinned');    
        $('#tabs_content').css('padding-left', '45px');
        $(t).find('i').addClass('fa-rotate-90');
        localStorage.setItem('oa_sidebar', 'collapsed');    
    }
    if (didLayout) didLayout.resizeAll();
    if (dialogLayout) dialogLayout.resizeAll();
    if (msgLayout) msgLayout.resizeAll();
    if (mistakesLayout) mistakesLayout.resizeAll();
    if (callsLayout) callsLayout.resizeAll();
    if (bbLayout) bbLayout.resizeAll();
    if (welcomeLayout) welcomeLayout.resizeAll();
    if (campaignsLayout) campaignsLayout.resizeAll();
    if (userLayout) userLayout.resizeAll();
    if (roleLayout) roleLayout.resizeAll();
    
}
</script>


