<?php
#
# Adds a new lab configuration to DB
#
include("redirect.php");
include("includes/user_lib.php");
include("includes/db_lib.php");
include("includes/random.php");
include("lang/lang_xml2php.php");

$saved_session = SessionUtil::save();

$lab_config = new LabConfig();
$lab_config->name = $_REQUEST['name'];
$lab_config->location = $_REQUEST['location'];
$lab_admin_id = $_REQUEST['lab_admin'];

$master_test_list = get_test_types_catalog(true);
$selected_test_list = array();
foreach($master_test_list as $test_type_id=>$test_name)
{
	if(isset($_REQUEST['t_type_'.$test_type_id]))
		$selected_test_list[] = $test_type_id;
}
$lab_config->testList = $selected_test_list;
//print_r($lab_config->testList);

$master_specimen_list = get_specimen_types_catalog(true);
$selected_specimen_list = array();
foreach($master_specimen_list as $specimen_type_id=>$specimen_name)
{
	if(isset($_REQUEST['s_type_'.$specimen_type_id]))
		$selected_specimen_list[] = $specimen_type_id;
}
$lab_config->specimenList = $selected_specimen_list;

$lab_config->idMode = $_REQUEST['id_mode'];

# Link admin user id to session variable of selection box value
$lab_config->adminUserId = $lab_admin_id;

# Add new lab configuration entry to DB
$lab_config_id = add_lab_config($lab_config);
$saved_config_id = $lab_config_id;
$user = get_user_by_id($_SESSION['user_id']);
if(is_country_dir($user))
	add_lab_config_access($_SESSION['user_id'], $lab_config_id);
$revamp_db_name = "blis_revamp_".$lab_config_id;
$db_name = "blis_".$lab_config_id;
set_lab_config_db_name($lab_config_id, $db_name);
//echo $db_name;

# Add user accounts
$user_list = $_REQUEST['username'];
$pwd_list = $_REQUEST['password'];
$fullname_list = $_REQUEST['fullname'];
for($i = 0; $i < count($user_list); $i++)
{
	$username = $user_list[$i];
	$pwd = $pwd_list[$i];
	$actual_name = $fullname_list[$i];
	$access_level = $_REQUEST['access_priv_'.$i];
	if($username == "")
	{
		# Empty entry
		continue;
	}
	$user = new User();
	$user->userId = "to be assigned";
	$user->username = $username;
	$user->password = $pwd;
	$user->actualName = $actual_name;
	$user->email = "";
	$user->phone = "";
	$user->level = $access_level;
	$user->createdBy = $_SESSION['user_id'];
	$user->labConfigId = $lab_config_id;
	$user->langId = "default";
	add_user($user);
}

# Create revamp DB instance for this lab
db_create($revamp_db_name);
# Populate
create_lab_config_revamp_tables($lab_config_id, $revamp_db_name);
# Copy selected test types and specimen types to this database
$lab_config->id = $lab_config_id;
add_lab_config_with_id($lab_config);

# Create DB instance for this lab
db_create($db_name);
# Switch to this new instance and create data tables
db_change($db_name);
create_lab_config_tables($lab_config_id, $db_name);
# Generate initial worksheet configs if missing
$lab_config = LabConfig::getById($lab_config_id);
$lab_config->worksheetConfigGenerate();


# TODO:
$saved_id = $_SESSION['lab_config_id'];
$_SESSION['lab_config_id'] = $lab_config_id;
//db_change($GLOBAL_DB_NAME);

## Add new entry for infection (disease) report
# TODO:
$site_settings = new DiseaseReport();
$site_settings->labConfigId = $lab_config_id;
$site_settings->testTypeId = 0;
$site_settings->measureId = 0;
$site_settings->groupByGender = 1;
$site_settings->groupByAge = 0;
$site_settings->ageGroups = "";
$site_settings->measureGroups = "";
$site_settings->addToDb();
foreach($selected_test_list as $test_type_id)
{
	$site_settings->testTypeId = $test_type_id;
	$test_type = TestType::getById($test_type_id);
	$measure_list = $test_type->getMeasures();
	foreach($measure_list as $measure)
	{
		$site_settings->measureId = $measure->measureId;
		$site_settings->measureGroups = $measure->range;
		if(strpos($site_settings->measureGroups, "/") === true)
		{
			# Alhpanumeric options: Do not add new entry
			continue;
		}
		$site_settings->addToDb();
	}
}


###################
# Generate random data (for evaluation phase only)
## Random patient entries

//$num_patients = $_REQUEST['num_patients'];
//add_patients_random($num_patients);
## Random specimen entries
//$num_specimens = $_REQUEST['num_specimens'];
//add_specimens_random($num_specimens);
## Random test result entries
//add_results_random();
//add_results_sequential();

###################

# Create new langdata folder for this lab
chmod($LOCAL_PATH."langdata_revamp", 777);
chmod($LOCAL_PATH."langdata_".$lab_config_id, 777);
mkdir($LOCAL_PATH."langdata_".$lab_config_id);
# Copy contents from langdata_revamp into this new folder
//copy($LOCAL_PATH."langdata_revamp", $LOCAL_PATH."langdata_".$lab_config_id);
$file_list1 = array();
$dir_name1 = $LOCAL_PATH."langdata_revamp";
if ($handle = opendir($dir_name1))
{
	while (false !== ($file = readdir($handle)))
	{
		if($file === "." || $file == "..")
			continue;
		$file_list1[] = $dir_name1."/$file";
	}
}
$destination = $LOCAL_PATH."langdata_".$lab_config_id;
foreach($file_list1 as $file)
{
	$file_name_parts = explode("/", $file);
	$target_file_name = $destination."/".$file_name_parts[4];
	$ourFileHandle = fopen($target_file_name, 'w') or die("can't open file");
	fclose($ourFileHandle);
	if(!copy($file, $target_file_name))
	{
		echo "Error: $file -> $destination.$file <br>";
	};
}

$langdata_path = $LOCAL_PATH."langdata_".$lab_config_id."/";
remarks_db2xml($langdata_path, $lab_config_id);

$_SESSION['lab_config_id'] = $saved_id;
SessionUtil::restore($saved_session);
header("location:lab_config_added.php?id=$saved_config_id");
?>