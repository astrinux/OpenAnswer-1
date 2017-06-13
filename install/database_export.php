<?php
//This script updates mysql database/table structures to match what the current version of the software requires.



$table_structure = array();

//program arguments
$database_host = "";
$database_user = "";
$database_pass = "";
$database_name = "";
$action = "";


//help text that is displayed if the program is executed with missing or incorrect arguments.
function display_help()
{
	echo "Exports table structures from an existing installation of OpenAnswer.\n";
	echo "\n";
	echo "Arguments:\n";
	echo "host=yourhost    specifies the ip/hostname of your mysql server\n";
	echo "user=username    specifies the login name of your mysql user, this user MUST have rights to SHOW CREATE TABLE\n";
	echo "pass=password    specifies the password of the above mysql user\n";
	echo "db=database      specifies the database that contains the openanswer tables.\n";
	echo "\n";
	echo "Usage example:\n";
	echo "php ./database_setup.php host=localhost user=root pass=itscomplicated db=openanswer\n";
	exit();
}





//Parse the arguments
foreach ($argv as $arg)
{
	$params = explode("=",$arg);
	if (strtoupper(trim($params[0])) =="HOST")
	{
		if ($database_host == "") $database_host=$params[1];
		else
		{
			echo "Error in arguments, Cannot set Host twice!\n";
			display_help();
		}
	}
	elseif (strtoupper(trim($params[0])) =="USER")
	{
		if ($database_user == "") $database_user=$params[1];
		else
		{
			echo "Error in arguments, Cannot set User twice!\n";
			display_help();
		}
	}
	elseif (strtoupper(trim($params[0])) =="PASS")
	{
		if ($database_pass == "") $database_pass=$params[1];
		else
		{
			echo "Error in arguments, Cannot set Pass twice!\n";
			display_help();
		}
	}
	elseif (strtoupper(trim($params[0])) =="DB")
	{
		if ($database_name == "") $database_name=$params[1];
		else
		{
			echo "Error in arguments, Cannot set DB twice!\n";
			display_help();
		}
	}
}


//Verify that we had valid arguments passed

$arguments_ok=TRUE;

if (!$database_host)
{
	echo "Error in arguments, missing Host\n";
	$arguments_ok=FALSE;
}
if (!$database_user)
{
	echo "Error in arguments, missing User\n";
	$arguments_ok=FALSE;
}
if (!$database_pass)
{
	echo "Error in arguments, missing Pass\n";
	$arguments_ok=FALSE;
}
if (!$database_name)
{
	echo "Error in arguments, missing DB\n";
	$arguments_ok=FALSE;
}

if (!$arguments_ok)
{
	display_help();
}



//array of tables to dump when dumping an existing configuration
$tables = array(
	"ccact_accounts"=>"",
	"ccact_accounts_edits"=>"",
	"ccact_actions"=>"",
	"ccact_applications"=>"",
	"ccact_application_parameters"=>"",
	"ccact_app_settings"=>"",
	"ccact_breaks"=>"",
	"ccact_bulletin_recipients"=>"",
	"ccact_bulletins"=>"",
	"ccact_call_events"=>"",
	"ccact_call_lists"=>"",
	"ccact_call_lists_schedules"=>"",
	"ccact_call_logs"=>"",
	"ccact_call_logs_old"=>"",
	"ccact_call_sipcallids"=>"",
	"ccact_calltypes"=>"",
	"ccact_cell_carriers"=>"",
	"ccact_cids"=>"",
	"ccact_company_audio"=>"",
	"ccact_complaints"=>"",
	"ccact_complaints_operators"=>"",
	"ccact_contacts"=>"",
	"ccact_dashboard_users"=>"",
	"ccact_dialer_calls"=>"",
	"ccact_dialer_campaigns"=>"",
	"ccact_dialer_contacts"=>"",
	"ccact_did_numbers"=>"",
	"ccact_did_numbers_entries"=>"",
	"ccact_ea_appointment_prompts"=>"",
	"ccact_ea_appointments"=>"",
	"ccact_ea_providers"=>"",
	"ccact_ea_service_prompts"=>"",
	"ccact_ea_services"=>"",
	"ccact_ea_services_providers"=>"",
	"ccact_emails"=>"",
	"ccact_employees"=>"",
	"ccact_employees_contacts"=>"",
	"ccact_faxes"=>"",
	"ccact_files"=>"",
	"ccact_keys"=>"",
	"ccact_logins"=>"",
	"ccact_messages"=>"",
	"ccact_messages_delivery"=>"",
	"ccact_messages_prompts"=>"",
	"ccact_messages_prompts_edits"=>"",
	"ccact_messages_summary"=>"",
	"ccact_messages_summary_log"=>"",
	"ccact_messages_summary_sent"=>"",
	"ccact_mistakes"=>"",
	"ccact_mms_carriers"=>"",
	"ccact_notes"=>"",
	"ccact_outbound"=>"",
	"ccact_overflow_centers"=>"",
	"ccact_prompts"=>"",
	"ccact_time_trackers"=>"",
	"ccact_queues"=>"",
	"ccact_schedules"=>"",
	"ccact_sms_carriers"=>"",
	"ccact_user_groups"=>"",
	"ccact_user_log"=>"",
	"ccact_users"=>"",
	"ccact_users_queues"=>"",
	"ccact_welcome_msgs"=>"",
	"ccact_sections" =>""
	);


echo "Connecting to mysql.\n";
$mysqli = new mysqli($database_host, $database_user, $database_pass, $database_name);


if ($mysqli->connect_errno) {
	echo("Error: Unable to connect to the mysql server using the provided arguments.\n".$mysqli->connect_error);
	exit();
}



	foreach ($tables as $table_name => $key)
	{
		echo "Pulling structure for existing table: ".$database_name.".".$table_name."\n";
		if ($result = $mysqli->query("SHOW CREATE TABLE `".$database_name."`.`".$table_name."`;")) 
		{
			$row = mysqli_fetch_assoc($result);
			$table_structure[$table_name] = $row["Create Table"];
			$result->close();
		}
		else 
		{
			echo "Error pulling structure for table!\n";
			exit();
		}
	}
	
	$structure_file="structure.php";
	$fhandle = fopen($structure_file,"w");
	
	if ($fhandle)
	{
		fwrite($fhandle, '<?php'."\n".'$table_structure = ');
		fwrite($fhandle,var_export($table_structure,TRUE));
		fwrite($fhandle, ';'."\n".'?>');
		fclose($fhandle);
		echo "Structure exported successfully into structure.php\n";
	}
	else
	{
		echo "Unable to create the structure file structure.php, make sure you have permissions to write/create this file\n";
		exit();
	}

?>
