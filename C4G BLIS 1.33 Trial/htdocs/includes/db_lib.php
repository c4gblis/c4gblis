<?php
#
# This file contains entity classes and functions for DB queries
#

# Start session if not already started
if(session_id() == "")
	session_start();


include("defaults.php");
require_once("db_mysql_lib.php");

if(!isset($_SESSION['langdata_path']))
{
	$_SESSION['langdata_path'] = $LOCAL_PATH."langdata_revamp/";
}
# Select appropriate locale file
if(!isset($_SESSION['locale']))
	$_SESSION['locale'] = $DEFAULT_LANG;
$locale_catalog_file = $_SESSION['langdata_path'].$_SESSION['locale']."_catalog.php";
$locale_file = $_SESSION['langdata_path'].$_SESSION['locale'].".php";

include($locale_catalog_file);
include($locale_file);

require_once("debug_lib.php");
require_once("date_lib.php");
//require_once("user_lib.php");


#
# Entity classes for database backend
#

class User
{
	public $userId;
	public $username;
	public $password;
	public $actualName;
	public $email;
	public $phone;
	public $level;
	public $createdBy;
	public $labConfigId;
	public $langId;
	public $country;
	
	public static function getObject($record)
	{
		global $DEFAULT_LANG;
		# Converts a user record in DB into a User object
		if($record == null)
			return null;
		$user = new User();
		$user->userId = $record['user_id'];
		$user->username = $record['username'];
		$user->password = $record['password'];
		$user->actualName = $record['actualname'];
		$user->level = $record['level'];
		$user->email = $record['email'];
		$user->phone = $record['phone'];
		$user->createdBy = $record['created_by'];
		$user->labConfigId = $record['lab_config_id'];
		if(isset($record['lang_id']))
			$user->langId = $record['lang_id'];
		else
			$user->langId = $DEFAULT_LANG;
			
		if( $user->labConfigId == 128 || $user->labConfigId == 129 || $user->labConfig == 131 )
			$user->country = "Cameroon";
		return $user;
	}
	
	public static function onlyOneLabConfig($user_id, $user_level)
	{
		# Checks if only one lab config exists for this admin level user
		global $LIS_ADMIN;
		$lab_config_list = get_lab_configs($user_id, $user_level);
		if(count($lab_config_list) == 1 && $user_level == $LIS_ADMIN)
			return true;
		else
			return false;
	}
}

class LabConfig
{
	public $id;
	public $name;
	public $location;
	public $adminUserId;
	public $specimenList;
	public $testList;
	public $dbName;
	public $patientAddl;
	public $specimenAddl;
	public $dailyNum;
	public $dailyNumReset;
	public $pid;
	public $pname;
	public $sex;
	public $age;
	public $dob;
	public $sid;
	public $refout;
	public $rdate;
	public $comm;
	public $dateFormat;
	public $doctor;
	public $hidePatientName; # Flag to hide patient name at results entry
	public $ageLimit;
	
	public static $ID_AUTOINCR = 1;
	public static $ID_MANUAL = 2;
	
	public static $RESET_DAILY = 1;
	public static $RESET_MONTHLY = 2;
	public static $RESET_YEARLY = 3;
	public static $RESET_WEEKLY = 4;

	public static function getObject($record)
	{
		global $DEFAULT_DATE_FORMAT;
		# Converts a lab_config record in DB into a LabConfig object
		if($record == null)
			return null;
		$lab_config = new LabConfig();
		if(isset($record['lab_config_id']))
			$lab_config->id = $record['lab_config_id'];
		else
			$lab_config->id = null;
		$lab_config->name = $record['name'];
		$lab_config->location = $record['location'];
		$lab_config->adminUserId = $record['admin_user_id'];
		$lab_config->dbName = $record['db_name'];
		if(isset($record['id_mode']))
			$lab_config->idMode = $record['id_mode'];
		else
			$lab_config->idMode = 1;
		## TODO: Reflect the following attribs in DB backend
		$lab_config->collectionDateUsed = false;
		$lab_config->collectionTimeUsed = false;
		if(isset($record['p_addl']))
			$lab_config->patientAddl = $record['p_addl'];
		else
			$lab_config_id->patientAddl = 0;
		if(isset($record['s_addl']))
			$lab_config->specimenAddl = $record['s_addl'];
		else
			$lab_config->specimenAddl = 0;
		if(isset($record['daily_num']))
			$lab_config->dailyNum = $record['daily_num'];
		else
			$lab_config_id->dailyNum = 0;
		if(isset($record['dnum_reset']))
			$lab_config->dailyNumReset = $record['dnum_reset'];
		else
			$lab_config_id->dailyNumReset = LabConfig::$RESET_DAILY;
		if(isset($record['pid']))
			$lab_config->pid = $record['pid'];
		else
			$lab_config->pid = 0;
		if(isset($record['pname']))
			$lab_config->pname = $record['pname'];
		else
			$lab_config_id->pname = 0;
		if(isset($record['sex']))
			$lab_config->sex = $record['sex'];
		else
			$lab_config_id->sex = 0;
		if(isset($record['age']))
			$lab_config->age = $record['age'];
		else
			$lab_config->age = 0;
		if(isset($record['dob']))
			$lab_config->dob = $record['dob'];
		else
			$lab_config_id->dob = 0;
		if(isset($record['sid']))
			$lab_config->sid = $record['sid'];
		else
			$lab_config->sid = 0;
		if(isset($record['refout']))
			$lab_config->refout = $record['refout'];
		else
			$lab_config->refout = 0;
		if(isset($record['rdate']))
			$lab_config->rdate = $record['rdate'];
		else
			$lab_config->rdate = 0;
		if(isset($record['comm']))
			$lab_config->comm = $record['comm'];
		else
			$lab_config->comm = 0;
		if(isset($record['dformat']))
			$lab_config->dateFormat = $record['dformat'];
		else
			$lab_config->dateFormat = $DEFAULT_DATE_FORMAT;
		if(isset($record['doctor']))
			$lab_config->doctor = $record['doctor'];
		else
			$lab_config->doctor = 0;
		if(isset($record['pnamehide']))
			$lab_config->hidePatientName = $record['pnamehide'];
		else
			$lab_config->hidePatientName = 1;
		if(isset($record['ageLimit']))
			$lab_config->ageLimit = $record['ageLimit'];
		else
			$lab_config->ageLimit = 5;
		return $lab_config;
	}
	
	public static function getById($lab_config_id)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_config = "SELECT * FROM lab_config WHERE lab_config_id=$lab_config_id LIMIT 1";
		$record = query_associative_one($query_config);
		DbUtil::switchRestore($saved_db);
		return LabConfig::getObject($record);
	}
	
	public function changeAdmin($new_admin_id)
	{
		$query_string = 
			"UPDATE lab_config ".
			"SET admin_user_id=$new_admin_id ".
			"WHERE lab_config_id=$this->id ";
		$saved_db = DbUtil::switchToGlobal();
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
		
	public function getUsers()
	{
		$saved_db = DbUtil::switchToGlobal();
		$lab_config_id = $this->id;
		$retval = array();
		$query_string = 
			"SELECT u.* FROM user u ".
			"WHERE lab_config_id=$lab_config_id ORDER BY u.username";
		$resultset = query_associative_all($query_string, $row_count);
		if($resultset != null)
		{
			foreach($resultset as $record)
			{
				$retval[] = User::getObject($record);
			}
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public function getSiteName()
	{
		# Returns site-name string
		return $this->name." - ".$this->location;
	}
	
	public function getGoalTatValues()
	{
		# Returns a list of latest goal TAT values for all tests in the lab
		global $DEFAULT_TARGET_TAT;
		$test_type_list = get_test_types_by_site($this->id);
		$saved_db = DbUtil::switchToLabConfig($this->id);
		$retval = array();
		foreach($test_type_list as $test_type)
		{
			/*
			$query_string = 
				"SELECT tat FROM test_type_tat ".
				"WHERE test_type_id=$test_type->testTypeId ORDER BY ts DESC LIMIT 1";
			*/
			$query_string = 
				"SELECT target_tat FROM test_type ".
				"WHERE test_type_id=$test_type->testTypeId ORDER BY ts DESC LIMIT 1";
			$record = query_associative_one($query_string);
			if($record == null)
			{
				# Entry not yet added by lab admin: Show default
				$retval[$test_type->testTypeId] = $DEFAULT_TARGET_TAT*24;
			}
			else
			{
				$retval[$test_type->testTypeId] = $record['target_tat'];
			}
		}
		# Append TAT value for pending tests
		//$query_string = "SELECT tat FROM test_type_tat WHERE test_type_id=0 LIMIT 1";
		$query_string = "SELECT target_tat FROM test_type_tat WHERE test_type_id=0 LIMIT 1";
		$record = query_associative_one($query_string);
		if($record == null)
		{
			$retval[0] = null;
		}
		else if($record['tat'] == null)
		{
			$retval[0] = null;
		}
		else
		{
			# Default value present in table
			$retval[0] = $record['tat'];
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}

	public function getPendingTatValue()
	{
		# Returns default TAT value (in hours) to be assigned for samples that are pending
		# Used while generating TAT report
		# Stored in DB table 'test_type_tat' against test_type_id=0
		global $DEFAULT_PENDING_TAT;
		$saved_db = DbUtil::switchToLabConfig($this->id);
		$query_string = "SELECT tat FROM test_type_tat WHERE test_type_id=0 LIMIT 1";
		$record = query_associative_one($query_string);
		$retval = 0;
		if($record == null)
		{
			$retval = $DEFAULT_PENDING_TAT*24;
		}
		else if($record['tat'] == null)
		{
			$retval = $DEFAULT_PENDING_TAT*24;
		}
		else
		{
			# Entry present in DB
			$retval = $record['tat'];
		}
		DbUtil::switchRestore($saved_db);
		return $retval;		
	}
	
	public function getGoalTatValue($test_type_id, $timestamp="") {
		global $DEFAULT_TARGET_TAT;
		$saved_db = DbUtil::switchToLabConfig($this->id);
		$query_string = "";
		$query_string = "SELECT target_tat FROM test_type ".
					    "WHERE test_type_id=$test_type_id ORDER BY ts DESC LIMIT 1";
		$record = query_associative_one($query_string);
		return $record['target_tat'];
	}
	
	
	/*
	public function getGoalTatValue($test_type_id, $timestamp="")
	{
		# Returns the goal TAT value for the test on a given timestamp
		global $DEFAULT_TARGET_TAT;
		$saved_db = DbUtil::switchToLabConfig($this->id);
		$query_string = "";
		if($timestamp == "")
		{
			# Fetch latest entry
			$query_string = 
				"SELECT target_tat FROM test_type ".
				"WHERE test_type_id=$test_type_id ORDER BY ts DESC LIMIT 1";
		}
		else
		{
			# Fetch entry closest before or at the timestamp value
			$query_string = 
				"SELECT target_tat FROM test_type ttt ".
				"WHERE ttt.test_type_id=$test_type_id ".
				"AND ( ".
					"((UNIX_TIMESTAMP('$timestamp')-UNIX_TIMESTAMP(ttt.ts)) < (".
					"SELECT (UNIX_TIMESTAMP('$timestamp')-UNIX_TIMESTAMP(ttt2.ts)) ".
					"FROM test_type_tat ttt2 ".
					"WHERE ttt2.test_type_id=$test_type_id ".
					"AND ttt2.ts <> ttt.ts ".
					")) ".
					"OR ( ".
					"(SELECT COUNT(*) ".
					"FROM test_type_tat ttt3 ".
					"WHERE ttt3.test_type_id=$test_type_id ".
					"AND ttt3.ts <> ttt.ts) = 0 )".
				")";
		}
		$record = query_associative_one($query_string);
		$retval = 0;
		if($record == null)
			$retval = $DEFAULT_TARGET_TAT;
		else
			$retval = round($record['tat']/24, 2);
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	*/
	
	public function updateGoalTatValue($test_type_id, $tat_value)
	{	
		# Updates goal TAT value for a single test type
		## Adds a new entry for every update to have time-versioned goal TAT values
		$saved_db = DbUtil::switchToLabConfig($this->id);
		# Create new entry
		/*
		$query_string = 
			"SELECT tat FROM test_type_tat ".
			"WHERE test_type_id=$test_type_id ORDER BY ts DESC LIMIT 1";
		*/
		$query_string =
			"SELECT target_tat FROM test_type ".
			"WHERE test_type_id=$test_type_id ORDER BY ts DESC LIMIT 1";
		$existing_record = query_associative_one($query_string);
		if($existing_record != null) {
			if($existing_record['target_tat'] != $tat_value) {
				# Update TAT value
				$query_string = 
					"UPDATE test_type SET target_tat=$tat_value WHERE test_type_id=$test_type_id";
				query_update($query_string);
			}
			/*
			else
			{
				# New record to append for TAT (keeping timestamp wise progression of entries)
				$query_string = 
					"INSERT INTO test_type_tat (test_type_id, tat) ".
					"VALUES ($test_type_id, $tat_value)";
				echo $query_string;
				query_insert_one($query_string);
			}
			*/
		}
		/*
		else
		{
			# New record to add (first entry for this test type)
			$query_string = 
				"INSERT INTO test_type_tat (test_type_id, tat) ".
				"VALUES ($test_type_id, $tat_value)";
			echo $query_string;
			query_insert_one($query_string);
		}
		DbUtil::switchRestore($saved_db);
		*/
	}
	
	public function getTestTypeIds()
	{
		$saved_db = DbUtil::switchToLabConfigRevamp($this->id);
		# Returns a list of all test type IDs added to the lab configuration
		$query_string = 
			"SELECT test_type_id FROM lab_config_test_type ".
			"WHERE lab_config_id=$this->id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
		{
			$retval[] = $record['test_type_id'];
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public function getTestTypes()
	{
		$saved_db = DbUtil::switchToLabConfigRevamp($this->id);
		# Returns a list of all test type objects added to the lab configuration
		$query_string = 
			"SELECT tt.* FROM test_type tt, lab_config_test_type lctt ".
			"WHERE lctt.lab_config_id=$this->id ".
			"AND lctt.test_type_id=tt.test_type_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
		{
			$retval[] = TestType::getObject($record);
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public function changeName($new_name)
	{
		# Changes facility name for this lab configuration
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET name='$new_name' WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function changeLocation($new_location)
	{
		# Changes location value for this lab configuration
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET location='$new_location' WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updatePatientAddl($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET p_addl=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateSpecimenAddl($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET s_addl=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateDailyNum($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET daily_num=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updatePid($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET pid=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateSid($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET sid=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateComm($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET comm=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateRdate($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET rdate=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateRefout($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET refout=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateDoctor($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET doctor=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateHidePatientName($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET pnamehide=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateAge($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET age=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateSex($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET sex=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateDob($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET dob=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updatePname($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET pname=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateDateFormat($new_format)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET dformat='$new_format' WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function updateAgeLimit($ageLimit)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET ageLimit=$ageLimit WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
		return $ageLimit;
	}
	
	public function getReportConfig($report_id)
	{
		# Returns report config parameters for this lab
		return ReportConfig::getById($this->id, $report_id);
	}
	
	public function getWorksheetConfig($test_type_id)
	{
		return ReportConfig::getByTestTypeId($this->id, $test_type_id);
	}
	
	public function getCustomFields($target_entity)
	{
		# Returns list of custom fields being used at this lab
		# $target_entity = 1 for specimen. 2 for patients
		$saved_db = DbUtil::switchToLabConfig($this->id);
		$target_table = "patient_custom_field";
		if($target_entity == 1)
			$target_table = "specimen_custom_field";
		if($target_entity == 3)	
			$target_table = "labtitle_custom_field";
		$query_string = 
			"SELECT * FROM $target_table ORDER BY id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
		{
			$retval[] = CustomField::getObject($record);
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	
	public function getPatientCustomFields()
	{
		# Returns list of patient custom fields being used at this lab
		return $this->getCustomFields(2);
	}
	
	public function getSpecimenCustomFields()
	{
		# Returns list of specimen custom fields being used at this lab
		return $this->getCustomFields(1);
	}
	
	public function getLabTitleCustomFields()
	{
		# Returns list of specimen custom fields being used at this lab
		return $this->getCustomFields(3);
	}
	
	public function updateDailyNumReset($new_value)
	{
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE lab_config SET dnum_reset=$new_value WHERE lab_config_id=$this->id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function worksheetConfigGenerate()
	{
		$test_ids = $this->getTestTypeIds();
		$saved_db = DbUtil::switchToLabConfig($this->id);
		echo $saved_db;
		foreach($test_ids as $test_id)
		{	
			$test_entry = TestType::getById($test_id);
			$query_string = 
				"SELECT report_id FROM report_config WHERE test_type_id='$test_id' LIMIT 1";
			$record = query_associative_one($query_string);
			if($record == null)
			{	
				# Add new entry
				$query_string_add = 
					"INSERT INTO report_config (".
						"test_type_id, header, footer, margins, ".
						"p_fields, s_fields, t_fields, p_custom_fields, s_custom_fields ".
					") VALUES (".
						"'$test_id', 'Worksheet - ".$test_entry->name."', '', '5,0,5,0', ".
						"'0,1,0,1,1,0,0', '0,0,1,1,0,0', '1,0,1,0,0,0,0,1', '', '' ".
					")";
				query_insert_one($query_string_add);
			}
		}
		DbUtil::switchRestore($saved_db);
	}
	
	public function getCustomWorksheets()
	{
		$saved_db = DbUtil::switchToLabConfig($this->id);
		$query_string = 
			"SELECT * FROM worksheet_custom";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		if($resultset != null && count($resultset) != 0)
		{
			foreach($resultset as $record)
			{
				$retval[] = CustomWorksheet::getObject($record);
			}
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
}


class ReportConfig
{
	public $labConfigId;
	public $reportId;
	public $testTypeId;
	public $title;
		
	public $headerText;
	public $titleText;
	public $footerText;
	public $designation;
	public $aglignment_header;
	//alignmen
	//size
	//align?size?text
	public $patientCustomFields;
	public $specimenCustomFields;
	public $margins;
	public static $TOP=0, $BOTTOM=1, $LEFT=2, $RIGHT=3;
	
	public $usePatientId;
	public $useDailyNum;
	public $usePatientAddlId;
	public $useGender;
	public $useAge;
	public $useDob;
	public $usePatientName;
	public $useTest;
	
	public $useSpecimenId;
	public $useSpecimenAddlId;
	public $useDateRecvd;
	public $useComments;
	public $useReferredTo;
	public $useDoctor;
	public $useSpecimenName;
	
	public $useMeasures;
	public $useResults;
	public $useRange;
	public $useRemarks;
	public $useEntryDate;
	public $useEnteredBy;
	public $useVerifiedBy;
	public $useStatus;
	public $useTestName;
	public $useClinicalData;
	
	public $landscape;
	public $logoUrl;
		
	public static function getObject($record, $lab_config_id)
	{
		global $LANG_ARRAY, $LOCAL_PATH;
		
		if($record == null)
			return null;
		
		$report_config = new ReportConfig();
		
		$report_config->labConfigId = $lab_config_id;
		$report_config->reportId = $record['report_id'];
		$report_config->testTypeId = $record['test_type_id'];
		
		switch($report_config->reportId)
		{
			case 1:
				$report_config->name = $LANG_ARRAY["reports"]["MENU_PATIENT"];
				break;
			case 2:
				$report_config->name = $LANG_ARRAY["reports"]["MENU_SPECIMEN"];
				break;
			case 3:
				$report_config->name = $LANG_ARRAY["reports"]["MENU_TESTRECORDS"];
				break;
			case 4:
				$report_config->name = $LANG_ARRAY["reports"]["MENU_DAILYLOGS"];
				break;
		}
		
		$alignment_header=$record['header'];
		
		if(strpos($alignment_header, "??")!=-1)
		{	
			$split_alignment_header=explode("??",$alignment_header);
			$report_config->headerText =$split_alignment_header[0];
			$report_config->alignment_header=$split_alignment_header[1];	
		}
		else
			$report_config->headerText=$alignment_header;
		
		$footer_designation=$record['footer'];
		
		if(strpos($footer_designation, "#")!=-1)
		{
			$split= explode("#", $footer_designation);
			$report_config->footerText = $split[0];
			$report_config->designation =$split[1];
		}
		else
		$report_config->footerText = $record['footer'];
		$report_config->titleText = $record['title'];
		
		$report_config->logoUrl = $LOCAL_PATH."logos/logo_".$lab_config_id;
		$report_config->landscape = false;
		if($record['landscape'] == 1)
			$report_config->landscape = true;
		
		$margins_csv = $record['margins'];
		$report_config->margins = explode(",", $margins_csv);
		
		$patient_custom_csv = $record['p_custom_fields'];
		$report_config->patientCustomFields = explode(",", $patient_custom_csv);
		
		$specimen_custom_csv = $record['s_custom_fields'];
		$report_config->specimenCustomFields = explode(",", $specimen_custom_csv);
		
		# Patient main fields
		$patient_field_list = explode(",", $record['p_fields']);
		if(!isset($patient_field_list[0]))
			$report_config->usePatientId = 0;
		else
			$report_config->usePatientId = $patient_field_list[0];
		if(!isset($patient_field_list[1]))
			$report_config->useDailyNum = 0;
		else
			$report_config->useDailyNum = $patient_field_list[1];
		if(!isset($patient_field_list[2]))
			$report_config->usePatientAddlId = 0;
		else
			$report_config->usePatientAddlId = $patient_field_list[2];
		if(!isset($patient_field_list[3]))
			$report_config->useGender = 0;
		else
			$report_config->useGender = $patient_field_list[3];
		if(!isset($patient_field_list[4]))
			$report_config->useAge = 0;
		else
			$report_config->useAge = $patient_field_list[4];
		if(!isset($patient_field_list[5]))
			$report_config->useDob = 0;
		else
			$report_config->useDob = $patient_field_list[5];
		if(!isset($patient_field_list[6]))
			$report_config->usePatientName = 0;
		else
			$report_config->usePatientName = $patient_field_list[6];
		if(!isset($patient_field_list[7]))
			$report_config->useTest = 0;
		else
			$report_config->useTest = $patient_field_list[7];
			
		# Specimen main fields
		$specimen_field_list = explode(",", $record['s_fields']);
		if(!isset($specimen_field_list[0]))
			$report_config->useSpecimenId = 0;
		else
			$report_config->useSpecimenId = $specimen_field_list[0];
		if(!isset($specimen_field_list[1]))
			$report_config->useSpecimenAddlId = 0;
		else
			$report_config->useSpecimenAddlId = $specimen_field_list[1];
		if(!isset($specimen_field_list[2]))
			$report_config->useDateRecvd = 0;
		else
			$report_config->useDateRecvd = $specimen_field_list[2];
		if(!isset($specimen_field_list[3]))
			$report_config->useComments = 0;
		else
			$report_config->useComments = $specimen_field_list[3];
		if(!isset($specimen_field_list[4]))
			$report_config->useReferredTo = 0;
		else
			$report_config->useReferredTo = $specimen_field_list[4];
		if(!isset($specimen_field_list[5]))
			$report_config->useSpecimenName = 0;
		else
			$report_config->useSpecimenName = $specimen_field_list[5];
		if(!isset($specimen_field_list[6]))
			$report_config->useDoctor = 0;
		else
			$report_config->useDoctor = $specimen_field_list[6];
		
		# Test main fields
		$test_field_list = explode(",", $record['t_fields']);
		if(!isset($test_field_list[8]))
			$report_config->useMeasures = 0;
		else
			$report_config->useMeasures = $test_field_list[8];
		if(!isset($test_field_list[0]))
			$report_config->useResults = 0;
		else
			$report_config->useResults = $test_field_list[0];
		if(!isset($test_field_list[1]))
			$report_config->useRange = 0;
		else
			$report_config->useRange = $test_field_list[1];
		if(!isset($test_field_list[2]))
			$report_config->useRemarks = 0;
		else
			$report_config->useRemarks = $test_field_list[2];
		if(!isset($test_field_list[3]))
			$report_config->useEntryDate = 0;
		else
			$report_config->useEntryDate = $test_field_list[3];
		if(!isset($test_field_list[4]))
			$report_config->useEnteredBy = 0;
		else
			$report_config->useEnteredBy = $test_field_list[4];	
		if(!isset($test_field_list[5]))
			$report_config->useVerifiedBy = 0;
		else
			$report_config->useVerifiedBy = $test_field_list[5];
		if(!isset($test_field_list[6]))
			$report_config->useStatus = 0;
		else
			$report_config->useStatus = $test_field_list[6];
		if(!isset($test_field_list[7]))
			$report_config->useTestName = 0;
		else
			$report_config->useTestName = $test_field_list[7];
		if(!isset($test_field_list[9]))
			$report_config->useClinicalData = 0;
		else
			$report_config->useClinicalData =$test_field_list[9];
		
		# Return data object
		return $report_config;		
	}
	
	public static function getById($lab_config_id, $report_id)
	{
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$query_string = "SELECT * FROM report_config WHERE report_id=$report_id LIMIT 1";
		$record = query_associative_one($query_string);
		$retval = ReportConfig::getObject($record, $lab_config_id);
		DbUtil::switchRestore($saved_db);
		return $retval;		
	}
	
	public static function getByTestTypeId($lab_config_id, $test_type_id)
	{
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$query_string = "SELECT * FROM report_config WHERE test_type_id=$test_type_id LIMIT 1";
		$record = query_associative_one($query_string);
		$retval = ReportConfig::getObject($record, $lab_config_id);
		DbUtil::switchRestore($saved_db);
		return $retval;		
	}
	
	public static function updateToDb(
		$report_config, 
		$margin_csv, 
		$patient_main_field_map, 
		$specimen_main_field_map, 
		$test_main_field_map, 
		$patient_custom_field_map, 
		$specimen_custom_field_map
		//$labtitle_custom_field_map
	)
	{
		$pfield_csv = implode(",", $patient_main_field_map);
		$sfield_csv = implode(",", $specimen_main_field_map);
		$tfield_csv = implode(",", $test_main_field_map);
		$pcustom_csv = implode(",", $patient_custom_field_map);
		$scustom_csv = implode(",", $specimen_custom_field_map);
		$saved_db = DbUtil::switchToLabConfig($report_config->labConfigId);
		$landscape_flag = 0;
		if($report_config->landscape === true)
			$landscape_flag = 1;
		$query_string = 
			"SELECT report_id FROM report_config WHERE report_id=$report_config->reportId LIMIT 1";
		$record = query_associative_one($query_string);
		$footer_designation=implode("#", array($report_config->footerText, $report_config->designation));
		if($record == null || $record['report_id'] == null)
		{
			# New entry to be added
			$query_string = "SELECT max(report_id) AS reportId from report_config";
			$record = query_associative_one($query_string);
			$reportId = $record['reportId'];
			$reportId += 1;
			$query_string = 
				"INSERT INTO report_config (".
					"report_id, test_type_id, header, footer, title, landscape, margins, ".
					"p_fields, s_fields, t_fields, p_custom_fields, s_custom_fields ".
				") VALUES (".
					"$reportId, $report_config->testTypeId, '$report_config->headerText', '$footer_designation', '$report_config->titleText', $landscape_flag, '$margin_csv', ".
					"'$pfield_csv', '$sfield_csv', '$tfield_csv', '$pcustom_csv', '$scustom_csv' ".
				")";
			query_insert_one($query_string);
		}
		else
		{
			# Update existing entry
			$query_string = 
				"UPDATE report_config SET ".
				"header='$report_config->headerText', ".
				"footer='$footer_designation', ".
				"title='$report_config->titleText', ".
				"margins='$margin_csv', ".
				"landscape=$landscape_flag, ".
				"p_fields='$pfield_csv', ".
				"s_fields='$sfield_csv', ".
				"t_fields='$tfield_csv', ".
				"p_custom_fields='$pcustom_csv', ".
				"s_custom_fields='$scustom_csv' ".
				"WHERE report_id=$report_config->reportId";
			query_update($query_string);
		}
		DbUtil::switchRestore($saved_db);
	}
}


class TestType
{
	public $testTypeId;
	public $name;
	public $description;
	public $clinical_data;
	public $testCategoryId;
	public $isPanel;
	public $hidePatientName;
	public $prevalenceThreshold;
	public $targetTat;
	
	public static function getObject($record)
	{
		# Converts a test_type record in DB into a TestType object
		if($record == null)
			return null;
		$test_type = new TestType();
		$test_type->testTypeId = $record['test_type_id'];
		$test_type->name = $record['name'];
		$test_type->description = $record['description'];
		$test_type->clinical_data=  $record['clinical_data'];
		$test_type->testCategoryId = $record['test_category_id'];
		$test_type->hidePatientName = $record['hide_patient_name'];
		$test_type->prevalenceThreshold = $record['prevalence_threshold'];
		$test_type->targetTat = $record['target_tat'];
		if($record['is_panel'] != null && $record['is_panel'] == 1)
		{
			$test_type->isPanel = true;
		}
		else
		{
			$test_type->isPanel = false;
		}
		return $test_type;
	}
	
	public function getName()
	{
		global $CATALOG_TRANSLATION;
		if($CATALOG_TRANSLATION === true)
		{
			return LangUtil::getTestName($this->testTypeId);
		}
		else
		{
			return $this->name;
		}
	}
	
	public function getDescription()
	{
		if(trim($this->description) == "" || $this->description == null)
			return "-";
		else
			return trim($this->description);
	}
	
	public function getClinicalData()
	{
		if(trim($this->clinical_data) == "" || $this->clinical_data == null)
			return "-";
		else
			return trim($this->clinical_data);
	}
	
	public static function getByCategory($cat_code)
	{
		# Returns all test types belonging to a partciular category (aka section)
		if($cat_code == null || $cat_code == "")
			return null;
		$retval = array();
		$query_string = 
			"SELECT * FROM test_type ".
			"WHERE test_category_id=$cat_code AND disabled=0";
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$resultset = query_associative_all($query_string, $row_count);
		foreach($resultset as $record)
		{
			$retval[] = TestType::getObject($record);
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public static function getById($test_type_id)
	{
		# Returns test type record in DB
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string =
			"SELECT * FROM test_type WHERE test_type_id=$test_type_id LIMIT 1";
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		return TestType::getObject($record);
	}
	
	public function getMeasures()
	{
		# Returns list of measures included in a test type
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"SELECT measure_id FROM test_type_measure ".
			"WHERE test_type_id=$this->testTypeId";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
		{
			$measure_obj = Measure::getById($record['measure_id']);
			$retval[] = $measure_obj;
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public function getMeasureIds()
	{
		# Returns list of measure IDs included in a test type
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"SELECT measure_id FROM test_type_measure ".
			"WHERE test_type_id=$this->testTypeId";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
		{
			$retval[] = $record['measure_id'];
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public static function deleteById($test_type_id)
	{
		# Deletes test type from database
		# 1. Delete entries in lab_config_test_type
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"DELETE FROM lab_config_test_type WHERE test_type_id=$test_type_id";
		query_blind($query_string);
		# 2. Delete entries from specimen_test
		$query_string =
			"DELETE FROM specimen_test WHERE test_type_id=$test_type_id";
		query_blind($query_string);
		# 3. Set disabled flag in test_type entry
		$query_string =
			"UPDATE test_type SET disabled=1 WHERE test_type_id=$test_type_id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function toHidePatientName($test_type_id) {
		
		$query_string = 
			"SELECT hide_patient_name FROM test_type WHERE test_type_id=$test_type_id";
		$record = query_associative_one($query_string);
		$retval = $record['hide_patient_name'];
		return $retval;
	}
}

class SpecimenType
{
	public $specimenTypeId;
	public $name;
	public $description;
	
	public static function getObject($record)
	{
		if($record == null)
			return null;
			
		$specimen_type = new SpecimenType();
		
		if(isset($record['specimen_type_id']))
			$specimen_type->specimenTypeId = $record['specimen_type_id'];
		else
			$specimen_type->specimenTypeId = null;
		
		if(isset($record['name']))
			$specimen_type->name = $record['name'];
		else
			$specimen_type->name = null;
			
		if(isset($record['description']))
			$specimen_type->description = $record['description'];
		else
			$specimen_type->description = null;
			
		return $specimen_type;
	}
	
	public function getName()
	{
		global $CATALOG_TRANSLATION;
		if($CATALOG_TRANSLATION === true)
		{
			return LangUtil::getSpecimenName($this->specimenTypeId);
		}
		else
		{
			return $this->name;
		}
	}
	
	public function getDescription()
	{
		if(trim($this->description) == "" || $this->description == null)
			return "-";
		else 
			return trim($this->description);
	}
	
	public static function getById($specimen_type_id)
	{
		# Returns a specimen type entry fetch by ID
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"SELECT * FROM specimen_type ".
			"WHERE specimen_type_id=$specimen_type_id";
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		return SpecimenType::getObject($record);
	}
	
	public static function deleteById($specimen_type_id)
	{
		# Deletes specimen type from database
		# 1. Delete entries in lab_config_specimen_type
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"DELETE FROM lab_config_specimen_type WHERE specimen_type_id=$specimen_type_id";
		query_blind($query_string);
		# 2. Delete entries from specimen_test
		$query_string =
			"DELETE FROM specimen_test WHERE specimen_type_id=$specimen_type_id";
		query_blind($query_string);
		# 3. Set disabled flag in specimen_type entry
		$query_string =
			"UPDATE specimen_type SET disabled=1 WHERE specimen_type_id=$specimen_type_id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
}

class Measure
{
	# For each test indicator in 'measure' table
	
	public $measureId;
	public $name;
	public $unit;
	public $description;
	public $range;
	
	public static $RANGE_ERROR = 0;
	public static $RANGE_OPTIONS = 1;
	public static $RANGE_NUMERIC = 2;
	public static $RANGE_MULTI = 3;
	public static $RANGE_AUTOCOMPLETE = 4;
	
	public static function getObject($record)
	{
		# Converts a measure record in DB into a Measure object
		if($record == null)
			return null;
		$measure = new Measure();
		$measure->measureId = $record['measure_id'];
		$measure->name = $record['name'];
		$measure->unit = $record['unit'];
		$measure->description = $record['description'];
		$measure->range = $record['range'];
		return $measure;
	}
	
	public function getName()
	{
		global $CATALOG_TRANSLATION;
		if($CATALOG_TRANSLATION === true)
		{
			return LangUtil::getMeasureName($this->measureId);
		}
		else
		{
			return $this->name;
		}
	}
	
	public function getRangeType()
	{
		if(strpos($this->range, "_") !== false)
		{
			return Measure::$RANGE_AUTOCOMPLETE;
		}
		else if(strpos($this->range, ":") !== false)
		{
			return Measure::$RANGE_NUMERIC;
		}
		else if(strpos($this->range, "*") !== false)
		{
			return Measure::$RANGE_MULTI;
		}	
		else	if(strpos($this->range, "/") !== false)
		{
			return Measure::$RANGE_OPTIONS;
		}
		
			
		
		else 
		{
			return Measure::$RANGE_ERROR;
		}
	}
	
	public function getRangeValues($patient=null)
	{
		# Returns range values in a list
		
		$range_type = $this->getRangeType();
		$retval = array();
		switch($range_type)
		{
			case Measure::$RANGE_NUMERIC:
				# check if ref range is already configured
				$ref_range = null;
				if($patient != null)
				{	$ref_range = ReferenceRange::getByAgeAndSex($patient->getAgeNumber(), $patient->sex, $this->measureId, $_SESSION['lab_config_id']);
				
				}
				if($ref_range == null)
					# Fetch from default entry in 'measure' table
					$retval = explode(":", $this->range);
				else
					$retval = array($ref_range->rangeLower, $ref_range->rangeUpper);
				break;
			case Measure::$RANGE_OPTIONS:
			
			{
			$retval = explode("/", $this->range);
				
				foreach($retval as $key=>$value)
				{
				
				$retval[$key]=str_replace("#","/",$value);
				}
			break;
			}
			case Measure::$RANGE_AUTOCOMPLETE:
				$retval = explode("_", $this->range);
				foreach($retval as $key=>$value)
				{
				$retval[$key]=str_replace("#","_",$value);
				}
				break;
		}
		return $retval;
	}
	
	public function getRangeString($patient=null)
	{
		# Returns range in string for printing or displaying
		$retval = "";
		if
		(
			$this->getRangeType() == Measure::$RANGE_OPTIONS ||
			$this->getRangeType() == Measure::$RANGE_MULTI ||
			$this->getRangeType() == Measure::$RANGE_AUTOCOMPLETE
		)
		{
			$range_parts = explode("/", $this->range);
			# TODO: Display possible options for result indicator??
			$retval .= "-";
		}
		else if($this->getRangeType() == Measure::$RANGE_NUMERIC)
		{
			$ref_range = null;
			if($patient != null)
				$ref_range = ReferenceRange::getByAgeAndSex($patient->getAgeNumber(), $patient->sex, $this->measureId, $_SESSION['lab_config_id']);
			if($ref_range == null)
				# Fetch from default entry in 'measure' table
				$range_parts = explode(":", $this->range);
			else
				$range_parts = array($ref_range->rangeLower, $ref_range->rangeUpper);
			$retval .= "(".$range_parts[0]."-".$range_parts[1];
			if($this->range != null && trim($this->range) != "")
				$retval .= "  ".$this->unit;
			$retval .= ")";
		}
		
		return $range_parts;
	}
	
	public function getUnits()
	{
		return $this->unit;
	}
	
	public static function getById($measure_id)
	{
		# Returns a test measure by ID
		if($measure_id == null || $measure_id < 0)
			return null;
		$query_string = "SELECT * FROM measure WHERE measure_id=$measure_id LIMIT 1";
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		return Measure::getObject($record);		
	}
	
	public function updateToDb()
	{
		# Updates an existing measure entry in DB
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"UPDATE measure SET name='$this->name', range='$this->range', unit='$this->unit' ".
			"WHERE measure_id=$this->measureId";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function setInterpretation($inter)
	{
		# Updates an existing measure entry in DB
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"UPDATE measure SET description='$inter'".
			"WHERE measure_id=$this->measureId";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	public function setNumericInterpretation($remarks_list,$id_list, $range_l_list, $range_u_list, $age_u_list, $age_l_list, $gender_list)
	{
		# Updates an existing measure entry in DB
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$count = 0;
		if($id_list[0]==-1)
		{
		foreach($range_l_list as $range_value)
				{
			//insert query
			$query_string="INSERT INTO NUMERIC_INTERPRETATION (range_u, range_l, age_u, age_l, gender, description, measure_id) ".
			"VALUES($range_u_list[$count],$range_l_list[$count],$age_u_list[$count],$age_l_list[$count],'$gender_list[$count]','$remarks_list[$count]',$this->measureId)";
			query_insert_one($query_string);
			$count++;
				}
		}
		else
		{
		foreach($range_l_list as $range_value)
			{
				if($id_list[$count]!=-2)
					{
						if($remarks_list[$count]=="")
							{
						//delete
						$query_string="DELETE FROM NUMERIC_INTERPRETATION WHERE id=$id_list[$count]";
						query_delete($query_string);
						}else
							{
							//update
						$query_string = 
						"UPDATE numeric_interpretation SET range_u=$range_u_list[$count], range_l=$range_l_list[$count], age_u=$age_u_list[$count], age_l=$age_l_list[$count], gender='$gender_list[$count]' , description='$remarks_list[$count]' ".
						"WHERE id=$id_list[$count]";
						query_update($query_string);
						
						}
				}else
					{
					$query_string="INSERT INTO numeric_interpretation (range_u, range_l, age_u, age_l, gender, description, measure_id) ".
			"VALUES($range_u_list[$count],$range_l_list[$count],$age_u_list[$count],$age_l_list[$count],'$gender_list[$count]','$remarks_list[$count]',$this->measureId)";
			query_insert_one($query_string);
				}
		
		$count++;
		}
	}
	DbUtil::switchRestore($saved_db);
	}
	
	public function getNumericInterpretation()
	{
	$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = "SELECT * FROM numeric_interpretation WHERE measure_id=$this->measureId";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		if($resultset!=NULL)
			{
			foreach($resultset as $record)
			{
				$range_u=$record['range_u'];
				$range_l=$record['range_l'];
				$age_u=$record['age_u'];
				$age_l=$record['age_l'];
				$gender=$record['gender'];
				$id=$record['id'];
				$description=$record['description'];
				$measure_id=$record['measure_id'];
				$retval[] =array($range_l,$range_u,$age_l,$age_u,$gender,$description,$id,$measure_id);
			}
			
		}else
			{
		//get interpretation ka loop
			}
	DbUtil::switchRestore($saved_db);
	return $retval;
	}
	
	public function addToDb()
	{
		# Updates an existing measure entry in DB
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"INSERT INTO measure (name, range, unit) ".
			"VALUES ('$this->name', '$this->range', '$this->unit')".
		query_insert_one($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function getReferenceRanges($lab_config_id)
	{
		# Fetches reference ranges from database for this measure
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$query_string = "SELECT * FROM reference_range WHERE measure_id=$this->measureId ORDER BY sex DESC";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		if ($resultset!=NULL)
		{
			foreach($resultset as $record)
			{
				$retval[] = ReferenceRange::getObject($record);
			}
		}	
			DbUtil::switchRestore($saved_db);
			return $retval;
	}
	
	public function getInterpretation()
	{	
		$retval= array();
		$numeric_description=array();
		if(trim($this->description) == "" || $this->description == null)
			return $retval;
		else 
		{
		$description=substr(($this->description),2);
		if(strpos($description,"##")===false)
		$retval=explode("//" , $description);
		else
		$retval=explode("##",$description);
		}
		
		return $retval;
	}
	
	public function getDescription()
	{
		if(trim($this->description) == "" || $this->description == null)
			return "-";
		else
			return trim($this->description);
	}

}

class Patient
{
	public $patientId; # db primary key
	public $addlId;
	public $name;
	public $dob;
	public $partialDob;
	public $age;
	public $sex;
	public $surrogateId; # surrogate key (user facing)
	public $createdBy; # user ID who registered this patient
	public $hashValue; # hash value for this patient (based on name, dob, sex)
	public $regDate;
	public static function getObject($record)
	{
		# Converts a patient record in DB into a Patient object
		if($record == null)
			return null;
		$patient = new Patient();
		$patient->patientId = $record['patient_id'];
		$patient->addlId = $record['addl_id'];
		$patient->name = $record['name'];
		$patient->dob = $record['dob'];
		$patient->age = $record['age'];
		$patient->sex = $record['sex'];
		$date_parts = explode(" ", date($record['ts']));
$date_parts_1=explode("-",$date_parts[0]);
		$patient->regDate=$date_parts_1[2]."-".$date_parts_1[1]."-".$date_parts_1[0];
		
		if(isset($record['partial_dob']))
			$patient->partialDob = $record['partial_dob'];
		else
			$patient->partialDob = null;
		if(isset($record['surr_id']))
			$patient->surrogateId = $record['surr_id'];
		else
			$patient->surrogateId = null;
		if(isset($record['created_by']))
			$patient->createdBy = $record['created_by'];
		else
			$patient->createdBy = null;
		if(isset($record['hash_value']))
			$patient->hashValue = $record['hash_value'];
		else
			$patient->hashValue = null;
		return $patient;
	}
	
	public static function checkNameExists($name)
	{
		# Checks if the given patient name (or similar match) already exists
		$query_string = 
			"SELECT COUNT(patient_id) AS val FROM patient WHERE name LIKE '%$name%'";
		$resultset = query_associative_one($query_string);
		if($resultset == null || $resultset['val'] == 0)
			return false;
		else
			return true;
	}
	
	public function getName()
	{
		if(trim($this->name) == "")
			return " - ";
		else
			return $this->name;
	}
	
	public function getAddlId()
	{
		if($this->addlId == "")
			return " - ";
		else
			return $this->addlId;
	}
	
	public function getAssociatedTests() {
		if( $this->patientId == "" )
			return " - ";
		else {
			$query_string = "SELECT t.test_type_id FROM test t, specimen sp ".
							"WHERE t.result <> '' ".
							"AND t.specimen_id=sp.specimen_id ".
							"AND sp.patient_id=$this->patientId";
			$recordset = query_associative_all($query_string, $row_count);
			foreach( $recordset as $record ) {
				$testName = get_test_name_by_id($record['test_type_id']);
				$result .= $testName."<br>";
			}
			return $result;
		}
	}
	
	public function getAge()
	{
		# Returns patient age value
		if($this->partialDob == "" || $this->partialDob == null)
		{
			if($this->dob != null && $this->dob != "")
			{
				# DoB present in patient record
				return DateLib::dobToAge($this->dob);
			}
			else 
			{	$age_value=-1*$this->age;
				if($age_value>100){
					$age_value=200-$age_value;
					$age_value=">".$age_value;
					}
				else
					{
					$diff=$age_value%10;
					$age_range1=$age_value-$diff;
					$age_range2=$age_range1+10;
					$age_value=$age_range1."-".$age_range2;
					}
					if($this->age < 0)
				$this->age=$age_value;
				return $this->age." ".LangUtil::$generalTerms['YEARS'];
			}
		}
		else
		{
			# Calculate age from partial DoB
			$aprrox_dob = "";
			if(strpos($this->partialDob, "-") === false)
			{
				# Year-only specified
				$approx_dob = trim($this->partialDob)."-01-01";
			}
			else
			{
				# Year and month specified
				$approx_dob = trim($this->partialDob)."-01";
			}
			return DateLib::dobToAge($approx_dob);
		}
	}
	
	public function getAgeNumber()
	{
		# Returns patient age value (numeric part alone)
		if($this->partialDob == "" || $this->partialDob == null)
		{
			if($this->dob != null && $this->dob != "")
			{
				# DoB present in patient record
				return DateLib::dobToAgeNumber($this->dob);
			}
			else
			{	if($this->age<100)
					$this->age=200+$this->age;
				else if($this->age<0)
					$this->age=-1*$this->age;
			
				return $this->age;
			}
		}
		else
		{
			# Calculate age from partial DoB
			$aprrox_dob = "";
			if(strpos($this->partialDob, "-") === false)
			{
				# Year-only specified
				$approx_dob = trim($this->partialDob)."-01-01";
			}
			else
			{
				# Year and month specified
				$approx_dob = trim($this->partialDob)."-01";
			}
			return DateLib::dobToAgeNumber($approx_dob);
		}
	}
	
	public function getDob()
	{
		# Returns patient dob value
		if($this->partialDob != null && $this->partialDob != "")
		{
			return $this->partialDob." (".LangUtil::$generalTerms['APPROX'].")";
		}
		else if($this->dob == null || trim($this->dob) == "")
		{
			return " - ";
		}
		else
		{
			return DateLib::mysqlToString($this->dob);
		}
	}
	
	public static function getByAddDate($date)
	{
		# Returns all patient records added on that date
		$query_string = 
			"SELECT * FROM patient ".
			"WHERE ts LIKE '%$date%' ORDER BY patient_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
			$retval[] = Patient::getObject($record);
		return $retval;
	}
	
	public static function getByAddDateRange($date_from, $date_to)
	{
		# Returns all patient records added on that date range
		$query_string = 
			"SELECT * FROM patient ".
			"WHERE UNIX_TIMESTAMP(ts) >= UNIX_TIMESTAMP('$date_from 00:00:00') ".
			"AND UNIX_TIMESTAMP(ts) <= UNIX_TIMESTAMP('$date_to 23:59:59') ".
			"ORDER BY patient_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
			$retval[] = Patient::getObject($record);
		return $retval;
	}
	
	public static function getByRegDateRange($date_from , $date_to)
	{
	$query_string =
			"SELECT DISTINCT patient_id FROM specimen ".
			"WHERE date_collected BETWEEN '$date_from' AND '$date_to'";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		$record_p=array();
			foreach($resultset as $record)
			{
				foreach($record as $key=>$value)
				$query_string = "SELECT * FROM patient WHERE patient_id=$value";
				$record_each= query_associative_one($query_string);
				$record_p[]=Patient::getObject($record_each);
			}
		return $record_p;	
	
	}

	public static function getReportedByRegDateRange($date_from , $date_to)
	{
		$emp="";
		$query_string =
				"SELECT DISTINCT patient_id FROM specimen , test ".
				"WHERE date_collected BETWEEN '$date_from' AND '$date_to' ".
				"AND result!='$emp' ".
				"AND specimen.specimen_id=test.specimen_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		$record_p=array();
		$count = 0;
		foreach($resultset as $record)
		{
			foreach($record as $key=>$value) {
				$query_string = "SELECT * FROM patient WHERE patient_id=$value";
				$record_each= query_associative_one($query_string);
				$record_p[]=Patient::getObject($record_each);
			}
		}
		return $record_p;	
	
	}
	

	public static function getUnReportedByRegDateRange($date_from , $date_to)
	{
		$emp="";
		$query_string =
			"SELECT DISTINCT patient_id FROM specimen , test ".
			"WHERE date_collected BETWEEN '$date_from' AND '$date_to' ".
			"AND result='$emp' ".
			"AND specimen.specimen_id=test.specimen_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		$record_p=array();
		foreach($resultset as $record) {
			foreach($record as $key=>$value)
				$query_string = "SELECT * FROM patient WHERE patient_id=$value";
			$record_each= query_associative_one($query_string);
			$record_p[]=Patient::getObject($record_each);
		}
		return $record_p;
	}

	
	public static function getById($patient_id)
	{
		# Returns patient record by ID
		$query_string = "SELECT * FROM patient WHERE patient_id=$patient_id";
		$record = query_associative_one($query_string);
		//return 1;
		return Patient::getObject($record);
	}
	
	public function getSurrogateId()
	{
		if($this->surrogateId == null || trim($this->surrogateId) == "")
			return "-";
		else
			return $this->surrogateId;
	}
	
	public function getDailyNum()
	{
		# Returns daily number ("patient number")
		# Fetches value from the latest specimen which was assigned to this patient
		$query_string =
			"SELECT s.daily_num FROM specimen s, patient p ".
			"WHERE s.patient_id=p.patient_id ".
			"AND p.patient_id=$this->patientId ".
			"ORDER BY s.date_collected DESC";
		$record = query_associative_one($query_string);		
		$retval = "";
		if($record == null || trim($record['daily_num']) == "")
			$retval = "-";
		else
			$retval = $record['daily_num'];
		return $retval;
	}

	public function generateHashValue()
	{
		# Generates hash value for this patient (based on name, age and date of birth)
		$name_part = strtolower(str_replace(" ", "", $this->name));
		$sex_part = strtolower($this->sex);
		$dob_part = "";
		if($this->partialDob != null && trim($this->partialDob) != "")
		{	
			# Determine unix timestamp based on partial (approximate) date of birth
			$approx_dob = "";
			if(strpos($this->partialDob, "-") === false)
			{
				# Year-only specified
				$approx_dob = trim($this->partialDob)."-01-01";
			}
			else
			{
				# Year and month specified
				$approx_dob = trim($this->partialDob)."-01";
			}
			list($year, $month, $day) = explode('-', $approx_dob);
			$dob_part = mktime(0, 0, 0, $month, $day, $year);
		}
		else
		{
			# Determine unix timestamp based on complete data of birth
			$dob = $this->dob;
			list($year, $month, $day) = explode('-', $dob);
			$dob_part = mktime(0, 0, 0, $month, $day, $year);
		}
		$hash_input = $name_part.$dob_part.$sex_part;
		# TODO: Provide choice of hashing schemes
		$retval = sha1($hash_input);
		return $retval;
	}
	
	public function getHashValue()
	{
		$retval = $this->hashValue;
		return $retval;
	}
	
	public function getSex()
	{
	$sex=$this->sex;
	
	return $sex;
	}
	
	public function setHashValue($hash_value)
	{
		if($hash_value == null || trim($hash_value) == "")
			return;
		$query_string = 
			"UPDATE patient SET hash_value='$hash_value' ".
			"WHERE patient_id=$this->patientId";
		query_update($query_string);
	}
}


class Specimen
{
	public $specimenId;
	public $specimenTypeId;
	public $patientId;
	public $statusCodeId;
	public $referredTo;
	public $comments;
	public $dateRecvd;
	public $dateCollected;
	public $timeCollected;
	public $sessionNum;
	public $auxId;
	public $userId;
	public $reportTo;
	public $doctor;
	public $dateReported;
	public $referredToName;
	public $dailyNum;
	
	public static $STATUS_PENDING = 0;
	public static $STATUS_DONE = 1;
	public static $STATUS_REFERRED = 2;
	public static $STATUS_TOVERIFY = 3;
	public static $STATUS_REPORTED = 4;
	public static $STATUS_RETURNED = 5;
	
	public static function getObject($record)
	{
		# Converts a specimen record in DB into a Specimen object
		if($record == null)
			return null;
		$specimen = new Specimen();
		$specimen->specimenId = $record['specimen_id'];
		$specimen->specimenTypeId = $record['specimen_type_id'];
		$specimen->patientId = $record['patient_id'];
		$specimen->userId = $record['user_id'];
		$specimen->dateCollected = $record['date_collected'];
		if(isset($record['date_recvd']))
			$specimen->dateRecvd = $record['date_recvd'];
		else
			$specimen->dateRecvd = null;
		if(isset($record['time_collected']))
			$specimen->timeCollected = $record['time_collected'];
		else
			$specimen->timeCollected = null;
		if(isset($record['session_num']))
			$specimen->sessionNum = $record['session_num'];
		else
			$specimen->sessionNum = null;
		if(isset($record['status_code_id']))
			$specimen->statusCodeId = $record['status_code_id'];
		else
			$specimen->statusCodeId = null;
		if(isset($record['referred_to']))
			$specimen->referredTo = $record['referred_to'];
		else
			$specimen->referredTo = null;
		if(isset($record['comments']))
			$specimen->comments = $record['comments'];
		else
			$specimen->comments = null;
		if(isset($record['aux_id']))
			$specimen->auxId = $record['aux_id'];
		else
			$specimen->auxId = null;
		if(isset($record['report_to']))
			$specimen->reportTo = $record['report_to'];
		else
			$specimen->reportTo = null;
		if(isset($record['doctor']))
			{
			$specimen->doctor = $record['doctor'];
			}
			
		else
			$specimen->doctor = null;
		if(isset($record['date_reported']))
			$specimen->dateReported = $record['date_reported'];
		else
			$specimen->dateReported = null;
		if(isset($record['referred_to_name']))
			$specimen->referredToName = $record['referred_to_name'];
		else
			$specimen->referredToName = null;
		if(isset($record['daily_num']))
			$specimen->dailyNum = $record['daily_num'];
		else
			$specimen->dailyNum = null;
		return $specimen;
	}
	
	public static function getById($specimen_id)
	{
		$query_string = "SELECT * FROM specimen WHERE specimen_id=$specimen_id LIMIT 1";
		$record = query_associative_one($query_string);
		return Specimen::getObject($record);
	}
	
	public function getComments()
	{
		if(trim($this->comments) == "" || $this->comments == null)
			echo "-";
		else
			echo $this->comments;
	}
	
	public function getAuxId()
	{
		if($this->auxId == "" || $this->auxId == null)
			echo "-";
		else
			echo $this->auxId;
	}
	
	public function getStatus()
	{
		switch($this->statusCodeId)
		{
			case Specimen::$STATUS_PENDING:
				return LangUtil::$generalTerms['PENDING_RESULTS'];
				break;
			case Specimen::$STATUS_DONE:
				return LangUtil::$generalTerms['DONE'];
				break;
			case Specimen::$STATUS_REFERRED:
				return LangUtil::$generalTerms['REF_OUT'];
				break;
			case Specimen::$STATUS_TOVERIFY:
				return LangUtil::$generalTerms['PENDING_VER'];
				break;
			case Specimen::$STATUS_RETURNED:
				return LangUtil::$generalTerms['REF_RETURNED'];
				break;
		}
	}
	
	public function getReportTo()
	{
		if($this->reportTo == null)
			return "-";
		if($this->reportTo == 1)
			return LangUtil::$generalTerms['PATIENT'];
		if(trim($this->doctor) == "")
			return LangUtil::$generalTerms['DOCTOR'];
		return trim($this->doctor);
	}
	
	public function isReported()
	{
		if(($this->dateReported != null || trim($this->dateReported) != ""))
			return true;
		else
			return false;
	}
	
	public function getDateReported()
	{
		if($this->dateReported == null || $this->dateReported == "")
			return null;
		else
		{
			$date_parts = explode(" ", $this->dateReported);
			return DateLib::mysqlToString($date_parts[0])." ".$date_parts[1];
		}
	}
	
	public function setDateReported($date_reported)
	{
		# Sets value for date_reported
		if($date_reported == null)
			return;
		$query_string = 
			"UPDATE specimen SET date_reported='$date_reported' WHERE specimen_id=".$this->specimenId;
		query_blind($query_string);
	}
	
	public static function getUnreported()
	{
		# Returns all test results that have been entered but not reported
		$query_string = 
			"SELECT sp.* FROM specimen sp ".
			"WHERE sp.report_to <> '' ".
			"AND sp.date_reported IS NULL ".
			"AND ( ".
				"SELECT DISTINCT t.specimen_id FROM test t ".
				"WHERE t.specimen_id=sp.specimen_id ".
				"AND t.result = '' ".
				") IS NULL";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		if($resultset == null)
			return $retval;
		foreach($resultset as $record)
		{
			$retval[] = Specimen::getObject($record);
		}
		return $retval;
	}
	
	public static function markAsReported($specimen_id, $timestamp)
	{
		# Marks a given specimen as reported as sets 'date_reported'
		$query_string = 
			"UPDATE specimen ".
			"SET date_reported='$timestamp' ".
			"WHERE specimen_id=$specimen_id";
		query_blind($query_string);
	}
	
	public function getTypeName()
	{
		$specimen_type = SpecimenType::getById($this->specimenTypeId);
		if($specimen_type == null)
			return LangUtil::$generalTerms['NOTKNOWN'];
		return $specimen_type->getName();
	}
	
	public function getSessionNum()
	{
		if(trim($this->sessionNum) == "" || $this->sessionNum == null)
			return " -  ";
		else 
			return trim($this->sessionNum);
	}
	
	public function getTestNames()
	{
		$query_string = "SELECT test_type_id FROM test WHERE specimen_id=$this->specimenId";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = "";
		$count = 0;
		foreach($resultset as $record)
		{
			$count++;
			$test_type_id = $record['test_type_id'];
			$test_name = get_test_name_by_id($test_type_id);
			$retval .= $test_name;
			if($count < count($resultset))
			{
				$retval .= "<br>";
			}
		}
		return $retval;
	}
	
	public function getDailyNum()
	{
		if(trim($this->dailyNum) == "" || $this->dailyNum == 0)
			return "-";
		$dnum_parts = explode("-", $this->dailyNum);
		return $dnum_parts[1];
	}
	
	public function getDailyNumFull()
	{
		if(trim($this->dailyNum) == "" || $this->dailyNum == 0)
			return "-";
		return $this->dailyNum;
	}
	
	public function getDailyNumExpand()
	{
		if(trim($this->dailyNum) == "" || $this->dailyNum == 0)
			return "-";
		$today_string = date("Ymd");
		$dnum_parts = explode("-", $this->dailyNum);
		return $dnum_parts[1];
		if(strpos($this->dailyNum, $today_string."-") === false)
			return $this->dailyNum;
		else
			return $dnum_parts[1];
			
	}
	
	public function getReferredToName()
	{
		if($this->referredToName == null || trim($this->referredToName) == "")
			return "-";
		return trim($this->referredToName);
	}
	
	public function getDoctor()
	{
		if($this->doctor == "" || $this->doctor == null)
			return "-";
		else
			return $this->doctor;
			
			
	}
}

class Test
{
	public $testId;
	public $testTypeId;
	public $specimenId;
	public $result;
	public $comments;
	public $userId;
	public $verifiedBy;
	public $dateVerified;
	public $timestamp;
	
	public static function getObject($record)
	{
		# Converts a test record in DB into a Test object
		if($record == null)
			return null;
		$test = new Test();
		
		if(isset($record['test_id']))
			$test->testId = $record['test_id'];
		else
			$test->testId = null;
			
		if(isset($record['test_type_id']))
			$test->testTypeId = $record['test_type_id'];
		else
			$test->testTypeId = null;
			
		if(isset($record['specimen_id']))
			$test->specimenId = $record['specimen_id'];
		else
			$test->specimenId = null;
		
		if(isset($record['result']))
			$test->result = $record['result'];
		else
			$test->result = null;
		
		if(isset($record['comments']))
			$test->comments = $record['comments'];
		else
			$test->comments = null;
			
		if(isset($record['user_id']))
			$test->userId = $record['user_id'];
		else
			$test->userId = null;
			
		if(isset($record['verified_by']))
			$test->verifiedBy = $record['verified_by'];
		else
			$test->verifiedBy = null;
			
		if(isset($record['date_verified']))
			$test->dateVerified = $record['date_verified'];
		else
			$test->dateVerified = null;
		
		if(isset($record['ts']))
			$test->timestamp = $record['ts'];
		else
			$test->timestamp = null;
		
		return $test;
	}
	
	public static function getById($test_id)
	{
		# Returns a test result entry by test_id field
		if($test_id == null || trim($test_id) == "")
		{
			return null;
		}
		$query_string = "SELECT * FROM test WHERE test_id=$test_id";
		$record = query_associative_one($query_string);
		return Test::getObject($record);
	}
	
	public function isPending()
	{
		# Checks if test results are pending or not
		if($this->result == "")
			return true;
		return false;
	}
	
	public function isVerified()
	{
		# Checks if test results have been verified by a second technician
		if($this->verifiedBy == null || $this->verifiedBy == 0)
			return false;
		return true;
	}
	
	public function isReported()
	{
		# TODO:
		return false;
	}
	
	public function getEnteredBy()
	{
		# Returns username of the technician who entered results
		# Or, "Pending" if results are pending verification
		if($this->isPending())
			return LangUtil::$generalTerms['PENDING_RESULTS'];
		else
		{
			return get_username_by_id($this->userId);
		}
	}
	
	public function getVerifiedBy()
	{
		# Returns username of the technician who verified results
		# Or, "Not verified" if results are pending verification
		if($this->isVerified())
			return get_username_by_id($this->verifiedBy);
		return LangUtil::$generalTerms['PENDING_VER'];
	}
	
	public function setVerifiedBy($verified_by)
	{
		# Sets verified by flag for given test
		$query_string =
			"UPDATE test SET verified_by=$verified_by WHERE test_id=".$this->testId;
		query_blind($query_string);
	}
	
	public function addResult($hash_value)
	{
		# Enters results for this test
		# Adds results for a test entry
		$curent_ts = "";
		$current_ts = date("Y-m-d H:i:s");
		$result_field = $this->result.$hash_value;
		$query_string = 
			"UPDATE test SET result='$result_field', ".
			"comments='$this->comments', ".
			"user_id=$this->userId, ".
			"ts='$current_ts' ".
			"WHERE test_id=$this->testId ";
		query_blind($query_string);
		# If specimen ID was passed, update its status
		$specimen_id = $this->specimenId;
		if($specimen_id != "")
			update_specimen_status($specimen_id);
	}
	
	public function getResultWithoutHash()
	{
		global $PATIENT_HASH_LENGTH;
		if(trim($this->result) == "")
			# Results not yet entered
			return "";
		$retval = substr($this->result, 0, -1*$PATIENT_HASH_LENGTH);
		return $retval;
	}
	
	public function getMeasureList() {
		$testType = TestType::getById($this->testTypeId);
		$measureList = $testType->getMeasures();
		for($i = 0; $i < count($measureList); $i++) {
			$curr_measure = $measureList[$i];
			$retval .= "<br>".$curr_measure->name."<br>";
		}
		return $retval;
	}	
	
	public function decodeResult($show_range=false) {
		# Converts stored result value(s) for showing on front-end
		# Get measure, unit pairs for this test
		$test_type = TestType::getById($this->testTypeId);
		$measure_list = $test_type->getMeasures();
		$result_csv = $this->getResultWithoutHash();
		$result_list = explode(",", $result_csv);
		$retval = "";
		for($i = 0; $i < count($measure_list); $i++) {
			# Pretty print
			$curr_measure = $measure_list[$i];
			if(isset($result_list[$i]))
			{    
				# If matching result value exists (e.g. after a new measure was added to this test type)
				if(count($measure_list) == 1)
				{
					# Only one measure: Do not print measure name
					if($curr_measure->getRangeType() == Measure::$RANGE_AUTOCOMPLETE) {
						$result_string = "";
						$value_list = explode("_", $result_list[$i]);
						foreach($value_list as $value) {
							if(trim($value) == "")
								continue;
							$result_string .= $value."<br>";
						}
						$result_string = substr($result_string, 0, -4);
						$retval .= "<br>".$result_string."&nbsp;";
					}
					else if($curr_measure->getRangeType() == Measure::$RANGE_OPTIONS)
					{
						if($result_list[$i] != $curr_measure->unit)
							$retval .= "<br><b>".$result_list[$i]."</b> &nbsp;";
						else
							$retval .= "<br>".$result_list[$i]."&nbsp;";
					}
					else
					{
						$retval .= "<br>".$result_list[$i]."&nbsp;";
					}
				}
				else
				{
					# Print measure name with each result value
					$retval .= "<br>".$curr_measure->name."&nbsp;";
					if($curr_measure->getRangeType() == Measure::$RANGE_AUTOCOMPLETE)
					{
						$result_string = "";
						$value_list = str_replace("_", ",", $result_list[$i]);
						$retval .= ":<br>".$value_list."<br>";
					}
					else if($curr_measure->getRangeType() == Measure::$RANGE_OPTIONS)
					{
						if($result_list[$i]!=$curr_measure->unit)
							$retval .= "<b>".$result_list[$i]."</b> &nbsp;";
						else
							$retval .= $result_list[$i]."&nbsp;";
					}
					else
						$retval .= $result_list[$i]."&nbsp;";
				}
				if($show_range === true)
				{
					$retval .= $curr_measure->getRangeString();
				}
				if($i != count($measure_list) - 1)
				{
					$retval .= "<br>";
				}
			}
			else
			{
				# Matching result value not found: Show "-"
				if(count($measure_list) == 1)
				{
					$retval .= $curr_measure->name."&nbsp;";
				}
				$retval .= " - <br>";
			}
		}
		$retval = str_replace("_",",",$retval); # Replace all underscores with a comma
		return $retval;
	}
	
	public function decodeResultWithoutMeasures($show_range=false) {
		# Converts stored result value(s) for showing on front-end
		$test_type = TestType::getById($this->testTypeId);
		$measure_list = $test_type->getMeasures();
		$result_csv = $this->getResultWithoutHash();
		$result_list = explode(",", $result_csv);
		$retval = "";
		for($i = 0; $i < count($measure_list); $i++) {
			# Pretty print
			$curr_measure = $measure_list[$i];
			if(isset($result_list[$i]))
			{    
				# If matching result value exists (e.g. after a new measure was added to this test type)
				if(count($measure_list) == 1)
				{
					# Only one measure: Do not print measure name
					//$retval .= $curr_measure->name."&nbsp;";
					if($curr_measure->getRangeType() == Measure::$RANGE_AUTOCOMPLETE) {
						$result_string = "";
						$value_list = explode("_", $result_list[$i]);
						foreach($value_list as $value) {
							if(trim($value) == "")
								continue;
							$result_string .= $value."<br>";
						}
						$result_string = substr($result_string, 0, -4);
						$retval .= "<br>".$result_string."&nbsp;";
					}
					else if($curr_measure->getRangeType() == Measure::$RANGE_OPTIONS)
					{
						if($result_list[$i] != $curr_measure->unit)
							$retval .= "<br><b>".$result_list[$i]."</b> &nbsp;";
						else
							$retval .= "<br>".$result_list[$i]."&nbsp;";
					}
					else
					{
						$retval .= "<br>".$result_list[$i]."&nbsp;";
					}
				}
				else
				{
					# Print measure name with each result value
					// $retval .= $curr_measure->name."&nbsp;";
					if($curr_measure->getRangeType() == Measure::$RANGE_AUTOCOMPLETE)
					{
						$result_string = "";
						$value_list = str_replace("_", ",", $result_list[$i]);
						//$retval .= ":<br>".$value_list."<br>";
						$retval .= "<br>".$value_list."<br>";
					}
					else if($curr_measure->getRangeType() == Measure::$RANGE_OPTIONS)
					{
						if($result_list[$i]!=$curr_measure->unit) {
							//$retval .= "<b>".$result_list[$i]."</b> &nbsp;";
							$retval .= "<br><b>".$result_list[$i]."</b> &nbsp;<br>";
						}
						else {
							//$retval .= $result_list[$i]."&nbsp;";
							$retval .= "<br>".$result_list[$i]."&nbsp;<br>";
						}
					}
					else
					{
						//$retval .= $result_list[$i]."&nbsp;";
						$retval .= "<br>".$result_list[$i]."&nbsp;<br>";
					}
				}
				if($show_range === true)
				{
					$retval .= $curr_measure->getRangeString();
				}
				if($i != count($measure_list) - 1)
				{
					//$retval .= "<br>";
				}
			}
			else
			{
				# Matching result value not found: Show "-"
				if(count($measure_list) == 1)
				{
					$retval .= $curr_measure->name."&nbsp;";
				}
				$retval .= " - <br>";
			}
		}
		$retval = str_replace("_",",",$retval); # Replace all underscores with a comma
		return $retval;
	}
	
	public function getComments()
	{
		if(trim($this->comments) == "" || $this->comments == null)
			return "-";
		else
			return $this->comments;
	}
	
	public static function getByAddDate($date, $test_type_id)
	{
		# Returns all test records added on that day
		$query_string =
			"SELECT * FROM test ".
			"WHERE test_type_id=$test_type_id ".
			"AND ts LIKE '%$date%' ";
			//"AND result<>''";
		$retval = array();
		$resultset = query_associative_all($query_string, $row_count);
		foreach($resultset as $record)
			$retval[] = Test::getObject($record);
		return $retval;
	}
	
	public function verifyAndUpdate($hash_value)
	{
		# Updates changes to DB after verified/corrected result values are submitted
		$specimen_id = $this->specimenId;
		$test_type_id = $this->testTypeId;
		$query_string =
			"SELECT * FROM test ".
			"WHERE specimen_id=$specimen_id ".
			"AND test_type_id=$test_type_id LIMIT 1";
		$record = query_associative_one($query_string);
		$existing_entry = Test::getObject($record);
		$test_id = $existing_entry->testId;
		$new_result_value = $this->result.$hash_value;
		$query_verify = "";
		if	(
				$existing_entry->result == $new_result_value && 
				$existing_entry->comments == $this->comments
			)
		{
			# No changes or corrections after verification
			$query_verify = 
				"UPDATE test ".
				"SET verified_by=$this->verifiedBy, ".
				"date_verified='$this->dateVerified' ".
				"WHERE test_id=$test_id";
		}
		else
		{	
			# Update with corrections and mark as verified
			$query_verify =
				"UPDATE test ".
				"SET result='$new_result_value', ".
				"comments='$this->comments', ".
				"verified_by=$this->verifiedBy, ".
				"date_verified='$this->dateVerified' ".
				"WHERE test_id=$test_id";
		}
		query_blind($query_verify);
	}
	
	public function getDateVerified()
	{
		if($this->dateVerified == null || $this->dateVerified == "")
			return "-";
		else
		{
			$date_parts = explode(" ", $this->dateVerified);
			return DateLib::mysqlToString($date_parts[0])." ".$date_parts[1];
		}
	}
	
	public function getStatus()
	{
		if($this->isPending())
			return LangUtil::$generalTerms['PENDING_RESULTS'];
		else
			return LangUtil::$generalTerms['DONE'];
	}

}

class CustomField
{
	public $id;
	public $fieldName;
	public $fieldOptions;
	public $fieldTypeId;
	public $flag;
	public static $FIELD_FREETEXT = 1;
	public static $FIELD_DATE = 2;
	public static $FIELD_OPTIONS = 3;
	public static $FIELD_NUMERIC = 4;
	public static $FIELD_MULTISELECT = 5;
	
	public static function getObject($record)
	{
		# Converts a custom field record in DB into a CustomField object
		if($record == null)
			return null;
		$custom_field = new CustomField();
		
		if(isset($record['id']))
			$custom_field->id = $record['id'];
		else
			$custom_field->id = null;
			
		if(isset($record['field_name']))
		{
			$name=$record['field_name'];
			$name_string=explode("^^" , $name);
			$custom_field->fieldName=$name_string[0];
			if($name_string[1]!=NULL|| $name_string!="")
			$custom_field->flag=$name_string[1];
			else
			$custom_field->flag=0;
			//$custom_field->fieldName = $record['field_name'];
		}
			else
			$custom_field->fieldName = null;
			
		if(isset($record['field_options']))
			$custom_field->fieldOptions = $record['field_options'];
		else
			$custom_field->fieldOptions = null;
			
		if(isset($record['field_type_id']))
			$custom_field->fieldTypeId = $record['field_type_id'];
		else
			$custom_field->fieldTypeId = null;
			
		return $custom_field;
	}
	
	public static function addNew($new_entry, $lab_config_id, $tabletype)
	{
		# Adds a new custom field entry
		# $tabletype = 1 for specimen custom field
		# $tabletype = 2 for patient custom field
		# $tabletype = 3 for labtitle custom field
		$table_name = "";
		if($tabletype == 1)
			$table_name = "specimen_custom_field";
		else if($tabletype == 2)
			$table_name = "patient_custom_field";
		else if($tabletype == 3)
			$table_name = "labtitle_custom_field";
		else
			return;
		$query_string = 
			"INSERT INTO $table_name (field_name, field_options, field_type_id) ".
			"VALUES ('$new_entry->fieldName', '$new_entry->fieldOptions', $new_entry->fieldTypeId)";
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		query_insert_one($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function setId($field_id, $lab_config_id, $tabletype)
	{
		if($field_id<100)
		$new_id=$field_id+100;
		else
		$new_id=$field_id-100;
		$table_name = "";
		if($tabletype == 1)
			$table_name = "specimen_custom_field";
		else if($tabletype == 2)
			$table_name = "patient_custom_field";
		else if($tabletype == 3)
			$table_name = "labtitle_custom_field";
		else
			return null;
			$query_string =
			" UPDATE $table_name ".
			" SET id=$new_id".
			" WHERE id=$field_id";
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
	
	}
	public static function getById($field_id, $lab_config_id, $tabletype)
	{
		# Returns a custom field entry
		# $tabletype = 1 for specimen custom field
		# $tabletype = 2 for patient custom field
		# $tabletype = 3 for labtitle custom field
		$table_name = "";
		if($tabletype == 1)
			$table_name = "specimen_custom_field";
		else if($tabletype == 2)
			$table_name = "patient_custom_field";
		else if($tabletype == 3)
			$table_name = "labtitle_custom_field";
		else
			return null;
		$query_string =
			"SELECT * FROM $table_name ".
			"WHERE id=$field_id";
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		return CustomField::getObject($record);
	}
	
	public static function deleteById($updated_entry, $lab_config_id, $tabletype)
	{
	if($updated_entry == null)
			return;
		$table_name = "";
		if($tabletype == 1)
			$table_name = "specimen_custom_field";
		else if($tabletype == 2)
			$table_name = "patient_custom_field";
		else if($tabletype == 3)
			$table_name = "labtitle_custom_field";
		else 
			return;
			
		$query_string = 
			"DELETE FROM $table_name ".
			"WHERE id=$updated_entry->id ";
		
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
			
	
	}
	public static function updateById($updated_entry, $lab_config_id, $tabletype ,$offset=0)
	{
		# Updates a custom field entry
		# $tabletype = 1 for specimen custom field
		# $tabletype = 2 for patient custom field
		# $tabletype = 3 for labtitle custom field
		if($updated_entry == null)
			return;
		$table_name = "";
		if($tabletype == 1)
			$table_name = "specimen_custom_field";
		else if($tabletype == 2)
			$table_name = "patient_custom_field";
		else if($tabletype == 3)
			$table_name = "labtitle_custom_field";
		else 
			return;
			if($offset==$updated_entry->id)
			$new_id=intval($updated_entry->id)*13;
			else if($offset==-1)
			{
			$new_id=$updated_entry->id;
			}
			else if($offset==-3)
			$new_id=intval($updated_entry->id)/13;
			else
			$new_id=$updated_entry->id;
			
		$query_string = 
			"UPDATE $table_name ".
			"SET field_name='$updated_entry->fieldName', ".
			"id='$new_id', ".
			"field_options='$updated_entry->fieldOptions '".
			"WHERE id=$updated_entry->id";
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public function getFieldTypeName()
	{
		# Returns a string describing field type of this custom field
		switch($this->fieldTypeId)
		{
			case CustomField::$FIELD_FREETEXT:
				return LangUtil::$generalTerms['FREETEXT'];
			case CustomField::$FIELD_DATE:
				return LangUtil::$generalTerms['DATE'];
			case CustomField::$FIELD_OPTIONS:
				return LangUtil::$generalTerms['DROPDOWN'];
			case CustomField::$FIELD_NUMERIC:
				return LangUtil::$generalTerms['NUMERIC_FIELD'];
			case CustomField::$FIELD_MULTISELECT:
				return LangUtil::$generalTerms['MULTISELECT'];
		}
	}
	
	public function getFieldOptions()
	{
		# Returns list of option values for this custom field
		$retval = array();
		if($this->fieldTypeId != CustomField::$FIELD_OPTIONS && $this->fieldTypeId != CustomField::$FIELD_MULTISELECT)
			return $retval;
		else
		{
			$options_csv = $this->fieldOptions;
			$retval = explode("/", $options_csv);
			return $retval;
		}
	}
	
	public function getFieldRange()
	{
		# Returns range bound values for this custom field
		$retval = array();
		if($this->fieldTypeId != CustomField::$FIELD_NUMERIC)
			return $retval;
		else
		{
			$options_csv = $this->fieldOptions;
			$retval = explode(":", $options_csv);
			return $retval;
		}
	}
	
	
}

class SpecimenCustomData
{
	public $fieldId;
	public $specimenId;
	public $fieldValue;
	
	public static function getObject($record)
	{
		# Converts a specimen_custom_data record in DB into a SpecimenCustomData object
		if($record == null)
			return null;
		$custom_data = new SpecimenCustomData();
		
		if(isset($record['field_id']))
			$custom_data->fieldId = $record['field_id'];
		else
			$custom_data->fieldId = null;
			
		if(isset($record['specimen_id']))
			$custom_data->specimenId = $record['specimen_id'];
		else
			$custom_data->specimenId = null;
			
		if(isset($record['field_value']))
			$custom_data->fieldValue = $record['field_value'];
		else
			$custom_data->fieldValue = null;
		
		return $custom_data;
	}
	
	
	public function getFieldValueString($lab_config_id, $tabletype)
	{
		$field_type = CustomField::getById($this->fieldId, $lab_config_id, $tabletype);
		$field_value = $this->fieldValue;
		if(trim($field_value) == "" || $field_value == null)
		{
			$field_value = "-";
			return $field_value;
		}
		if($field_type->fieldTypeId == CustomField::$FIELD_NUMERIC)
		{
			$range = $field_type->getFieldRange();
			return $field_value." $range[2]";
		}
		else if($field_type->fieldTypeId == CustomField::$FIELD_DATE)
		{
			return DateLib::mysqlToString($field_value);
		}
		else
		{
			return $field_value;
		}
	}
}


class PatientCustomData
{
	public $fieldId;
	public $patientId;
	public $fieldValue;
	
	public static function getObject($record)
	{
		# Converts a patient_custom_data record in DB into a PatientCustomData object
		if($record == null)
			return null;
		$custom_data = new PatientCustomData();
		
		if(isset($record['field_id']))
			$custom_data->fieldId = $record['field_id'];
		else
			$custom_data->fieldId = null;
			
		if(isset($record['patient_id']))
			$custom_data->patientId = $record['patient_id'];
		else
			$custom_data->patientId = null;
			
		if(isset($record['field_value']))
			$custom_data->fieldValue = $record['field_value'];
		else
			$custom_data->fieldValue = null;
		
		return $custom_data;
	}
	
	public function getFieldValueString($lab_config_id, $tabletype)
	{
		$field_type = CustomField::getById($this->fieldId, $lab_config_id, $tabletype);
		$field_value = $this->fieldValue;
		if(trim($field_value) == "" || $field_value == null)
		{
			$field_value = "-";
			return $field_value;
		}
		if($field_type->fieldTypeId == CustomField::$FIELD_NUMERIC)
		{
			$range = $field_type->getFieldRange();
			return $field_value." $range[2]";
		}
		else if($field_type->fieldTypeId == CustomField::$FIELD_DATE)
		{
			return DateLib::mysqlToString($field_value);
		}
		else
		{
			return $field_value;
		}
	}
}

class Report
{
	public $id;
	public $name;
	public $groupByGender;
	public $groupByAge;
	public $ageSlots;
	
	public static function getObject($record)
	{
		# Converts a `report` table record to Report object
		if($record == null)
			return null;
		$report = new Report();
		if(isset($record['report_id']))
			$report->id = $record['report_id'];
		else
			$report->id = null;
		if(isset($record['name']))
			$report->name = $record['name'];
		else
			$report->name = null;
		if(isset($record['group_by_gender']))
			$report->groupByGender = $record['group_by_gender'];
		else
			$report->groupByGender = null;
		if(isset($record['group_by_age']))
			$report->groupByAge = $record['group_by_age'];
		else
			$report->groupByAge = null;
		if(isset($record['age_slots']))
		{
			# Build age slots array
			# Store in DB in the following format: 'lower1:upper1,lower2:upper2,lowern:uppern'
			$report->ageSlots = array();
			$age_slot_list = explode(",", $record['age_slots']);
			foreach($age_slot_list as $age_slot)
			{
				$age_slot_range = explode(":", $age_slot);
				$report->ageSlots[] = $age_slot_range;
			}
		}
		else
			$report->ageSlots = null;
		return $report;
	}
	
	public function addToDb()
	{
		# Adds a new report configuration to DB
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"INSERT INTO report (name, group_by_gender, group_by_age, age_slots) ".
			"VALUES ('$this->name', $this->groupByGender, $this->groupByAge, '$this->ageSlots')";
		query_insert_one($query_string);
		$new_report_id = get_last_insert_id();
		DbUtil::switchRestore($saved_db);
		return $new_report_id;
	}
	
	public static function getById($report_id)
	{
		# Fetches a report record from table
		$saved_db = DbUtil::switchToGlobal();
		$query_string = "SELECT * FROM report WHERE report_id=$report_id LIMIT 1";
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		return Report::getObject($record);
	}
	
	public static function getAllFromDb()
	{
		# Returns all report types stored in DB
		$saved_db = DbUtil::switchToGlobal();
		$query_string = "SELECT * FROM report";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
		{
			$retval[] = Report::getObject($record);
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
}

class DiseaseReport
{
	public $labConfigId;
	public $testTypeId;
	public $measureId;
	public $groupByGender;
	public $groupByAge;
	public $ageGroups;
	public $measureGroups;
	
	public static function getObject($record)
	{
		if($record == null)
			return null;
		$retval = new DiseaseReport();
		$retval->labConfigId = $record['lab_config_id'];
		$retval->testTypeId = $record['test_type_id'];
		$retval->measureId = $record['measure_id'];
		$retval->groupByGender = $record['group_by_gender'];
		$retval->groupByAge = $record['group_by_age'];
		if(isset($record['age_groups']))
			$retval->ageGroups = $record['age_groups'];
		if(isset($record['measure_groups']))
			$retval->measureGroups = $record['measure_groups'];
		return $retval;
	}
	
	public function addToDb()
	{
		$disease_report = $this;
		//$saved_db = DbUtil::switchToGlobal();
		# Remove existing entry
		$query_string =
			"DELETE FROM report_disease ".
			"WHERE lab_config_id=$this->labConfigId ".
			"AND test_type_id=$this->testTypeId ".
			"AND measure_id=$this->measureId";
		query_blind($query_string);
		# Add updated entry
		$query_string = 
			"INSERT INTO report_disease( ".
				"lab_config_id, ".
				"test_type_id, ".
				"measure_id, ".
				"group_by_gender, ".
				"group_by_age, ".
				"age_groups, ".
				"measure_groups ".
			") ".
			"VALUES ( ".
				"$disease_report->labConfigId, ".
				"$disease_report->testTypeId, ".
				"$disease_report->measureId, ".
				"$disease_report->groupByGender, ".
				"$disease_report->groupByAge, ".
				"'$disease_report->ageGroups', ".
				"'$disease_report->measureGroups' ".
			")";
		query_insert_one($query_string);
		//DbUtil::switchRestore($saved_db);
	}
	
	public static function getByKeys($lab_config_id, $test_type_id, $measure_id)
	{
		# Fetches a record by compound key
		$query_string =
			"SELECT * FROM report_disease ".
			"WHERE lab_config_id=$lab_config_id ".
			"AND test_type_id=$test_type_id ".
			"AND measure_id=$measure_id LIMIT 1";
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$record = query_associative_one($query_string);
		$retval = DiseaseReport::getObject($record);
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public function getAgeGroupAsList()
	{
		# Returns the age_group field as a PHP list
		$age_parts = explode(",", $this->ageGroups);
		$retval = array();
		foreach($age_parts as $age_part)
		{
			if(trim($age_part) == "")
				continue;
			$age_bounds = explode(":", $age_part);
			$retval[] = $age_bounds;
		}
		return $retval;
	}
	
	public function getMeasureGroupAsList()
	{
		# Returns the measure_group field as a PHP list
		$measure_parts = explode(",", $this->measureGroups);
		$retval = array();
		foreach($measure_parts as $measure_part)
		{
			if(trim($measure_part) == "")
				continue;
			$measure_bounds = explode(":", $measure_part);
			$retval[] = $measure_bounds;
		}
		return $retval;
	}
}

class CustomWorksheet
{
	public $id;
	public $name;
	
	public $headerText;
	public $footerText;
	public $titleText;
	public $margins;
	
	public $idFields;
	public static $OFFSET_PID = 0;
	public static $OFFSET_DNUM = 1;
	public static $OFFSET_ADDLID = 2;
	
	public $patientFields;
	public $specimenFields;
	public $testFields;
	public $patientCustomFields;
	public $specimenCustomFields;
	public $userFields;
	
	public $testTypes;
	public $columnWidths;
	public $landscape;
	
	public static $DEFAULT_WIDTH = 10; # in %age
	public static $DEFAULT_MARGINS = array(2, 2, 2, 2);
	
	public static function getObject($record)
	{
		if($record == null)
			return null;
		
		$worksheet = new CustomWorksheet();
		
		if(isset($record['id']))
			$worksheet->id = $record['id'];
		else
			$worksheet->id = null;
		if(isset($record['name']))
			$worksheet->name = $record['name'];
		else
			$worksheet->name = "";
		if(isset($record['header']))
			$worksheet->headerText = $record['header'];
		else
			$worksheet->headerText = "";
		if(isset($record['footer']))
			$worksheet->footerText = $record['footer'];
		else
			$worksheet->footerText = "";
		if(isset($record['title']))
			$worksheet->titleText = $record['title'];
		else
			$worksheet->titleText = "";
			
		$worksheet->landscape = false;
		if(isset($record['landscape']) && $record['landscape'] == 1)
			$worksheet->landscape = true;
		
		$margins_csv = $record['margins'];
		$worksheet->margins = explode(",", $margins_csv);
		
		$id_fields_csv = $record['id_fields'];
		if($id_fields_csv == null || trim($id_fields_csv) == "")
		{
			$id_fields_csv = "0,0,0";
		}
		$worksheet->idFields = explode(",", $id_fields_csv);
		
		$patient_custom_csv = $record['p_custom'];
		$worksheet->patientCustomFields = explode(",", $patient_custom_csv);
		
		$specimen_custom_csv = $record['s_custom'];
		$worksheet->specimenCustomFields = explode(",", $specimen_custom_csv);
	
		$query_string =
			"SELECT test_type_id, measure_id, width FROM worksheet_custom_test WHERE worksheet_id=$worksheet->id ORDER BY test_type_id";
		$resultset = query_associative_all($query_string, $row_count);
		# Populate testTypes list
		$worksheet->testTypes = array();
		foreach($resultset as $record)
		{
			if(in_array($record['test_type_id'], $worksheet->testTypes) === false)
			{
				$worksheet->testTypes[] = $record['test_type_id'];
			}
		}
		# Populate columnWidths list
		$worksheet->columnWidths = array();
		foreach($resultset as $record)
		{
			$test_type_id = intval($record['test_type_id']);
			$measure_id = intval($record['measure_id']);
			$width = intval($record['width']);
			if(array_key_exists($test_type_id, $worksheet->columnWidths) === false)
				$worksheet->columnWidths[$test_type_id] = array();
			$worksheet->columnWidths[$test_type_id][$measure_id] = $width;
		}
		
		# Populate list of user-defined fields
		$query_string = 
			"SELECT name,width,field_id FROM worksheet_custom_userfield WHERE worksheet_id=$worksheet->id ORDER BY name";
		$resultset = query_associative_all($query_string, $row_count);
		$worksheet->userFields = array();
		foreach($resultset as $record)
		{
			$field_id = $record['field_id'];
			$field_name = trim($record['name']);
			$field_width = $record['width'];
			$field_entry = array($field_id, $field_name, $field_width);
			$worksheet->userFields[] = $field_entry;
		}
		
		# TODO:
		# Populate patient main field maps
		# Populate specimen main field maps
		# Populate test main field maps
		
		return $worksheet;
	}
	
	public static function getById($worksheet_id, $lab_config)
	{
		if($worksheet_id == null || $lab_config == null)
			return null;
		$saved_db = DbUtil::switchToLabConfig($lab_config->id);
		$query_string = 
			"SELECT * FROM worksheet_custom WHERE id=$worksheet_id";
		$record = query_associative_one($query_string);
		$retval = CustomWorksheet::getObject($record);
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public static function addToDb($worksheet, $lab_config)
	{
		if($worksheet == null || $lab_config == null)
		{
			return;
		}
		$saved_db = DbUtil::switchToLabConfig($lab_config->id);
		$margins_csv = implode(",", $worksheet->margins);
		$id_fields_csv = implode(",", $worksheet->idFields);
		$query_string = 
			"INSERT INTO worksheet_custom (name, header, footer, title, margins, id_fields, p_fields, s_fields, t_fields, p_custom, s_custom) ".
			"VALUES ('$worksheet->name', '$worksheet->headerText', '$worksheet->footerText', '$worksheet->titleText', '$margins_csv', '$id_fields_csv', '', '', '', '', '')";
		query_insert_one($query_string);
		$worksheet_id = get_last_insert_id();
		foreach($worksheet->columnWidths as $key=>$value)
		{
			$test_type_id = $key;
			$width_list = $value;
			foreach($width_list as $key2=>$value2)
			{
				$measure_id = $key2;
				$width = $value2;
				$query_string = 
					"INSERT INTO worksheet_custom_test (worksheet_id, test_type_id, measure_id, width) ".
					"VALUES ($worksheet_id, $test_type_id, $measure_id, '$width')";
				query_insert_one($query_string);				
			}
		}
		foreach($worksheet->userFields as $field_entry)
		{
			$field_name = $field_entry[1];
			$field_width = $field_entry[2];
			$query_string = 
				"INSERT INTO worksheet_custom_userfield (worksheet_id, name, width) ".
				"VALUES ($worksheet_id, '$field_name', $field_width) ";
			query_insert_one($query_string);
		}
		$retval = $worksheet_id;
		DbUtil::switchRestore($saved_db);
		return $worksheet_id;
	}
	
	public static function updateToDb($worksheet, $lab_config)
	{
		if($worksheet == null || $lab_config == null)
		{
			return;
		}
		$saved_db = DbUtil::switchToLabConfig($lab_config->id);
		$margins_csv = implode(",", $worksheet->margins);
		$id_fields_csv = implode(",", $worksheet->idFields);
		$query_string = 
			"UPDATE worksheet_custom SET ".
			"name='$worksheet->name', ".
			"header='$worksheet->headerText', ".
			"footer='$worksheet->footerText', ".
			"title='$worksheet->titleText', ".
			"margins='$margins_csv', ".
			"id_fields='$id_fields_csv' ".
			"WHERE id=$worksheet->id";
		query_insert_one($query_string);
		# Clear all existing width entries
		$query_clear = "DELETE FROM worksheet_custom_test WHERE worksheet_id=$worksheet->id";
		query_blind($query_clear);
		# Add updated set of entries
		foreach($worksheet->columnWidths as $key=>$value)
		{
			$test_type_id = $key;
			$width_list = $value;
			foreach($width_list as $key2=>$value2)
			{
				$measure_id = $key2;
				$width = $value2;
				$query_string = 
					"INSERT INTO worksheet_custom_test (worksheet_id, test_type_id, measure_id, width) ".
					"VALUES ($worksheet->id, $test_type_id, $measure_id, '$width')";
				query_insert_one($query_string);
			}
		}
		foreach($worksheet->userFields as $field_entry)
		{
			$field_id = $field_entry[0];
			$field_name = $field_entry[1];
			$field_width = $field_entry[2];
			if($field_id == 0)
			{
				# New user field
				$query_string = 
					"INSERT INTO worksheet_custom_userfield (worksheet_id, name, width) ".
					"VALUES ($worksheet->id, '$field_name', $field_width) ";
				query_insert_one($query_string);
			}
			else
			{
				# Existing user field to update
				$query_string = 
					"UPDATE worksheet_custom_userfield ".
					"SET name='$field_name', width=$field_width ".
					"WHERE field_id=$field_id";
				query_update($query_string);
			}
		}
		DbUtil::switchRestore($saved_db);
	}
}

class ReferenceRange
{
	public $id;
	public $measureId;
	public $ageMin;
	public $ageMax;
	public $sex;
	public $rangeLower;
	public $rangeUpper;
	
	public static function getObject($record)
	{
		if($record == null)
			return null;
		$reference_range = new ReferenceRange();
		if(isset($record['id']))
			$reference_range->id = $record['id'];
		else
			$reference_range->id = null;
		if(isset($record['measure_id']))
			$reference_range->measureId = $record['measure_id'];
		else
			$reference_range->measureId = null;
		if(isset($record['age_min']))
			$reference_range->ageMin = intval($record['age_min']);
		else
			$reference_range->ageMin = null;
		if(isset($record['age_max']))
			$reference_range->ageMax = intval($record['age_max']);
		else
			$reference_range->ageMax = null;
		if(isset($record['sex']))
			$reference_range->sex = $record['sex'];
		else
			$reference_range->sex = null;
		if(isset($record['range_lower']))
			//$reference_range->rangeLower = intval($record['range_lower']);
		$reference_range->rangeLower = $record['range_lower'];
		else
			$reference_range->rangeLower = null;
		if(isset($record['range_upper']))
			$reference_range->rangeUpper = $record['range_upper'];
			//$reference_range->rangeUpper = intval($record['range_upper']);
		else
			$reference_range->rangeUpper = null;
		return $reference_range;
	}
	
	public function addToDb($lab_config_id)
	{
		# Adds this entry to database
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$query_string = 
			"INSERT INTO reference_range (measure_id, age_min, age_max, sex, range_lower, range_upper) ".
			"VALUES ($this->measureId, '$this->ageMin', '$this->ageMax', '$this->sex', '$this->rangeLower', '$this->rangeUpper')";
		query_insert_one($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public static function deleteByMeasureId($measure_id, $lab_config_id)
	{
		# Deletes all entries for the given measure
		# Used when deleting the measure from catalof
		# Or when resetting ranges (from test_type_edit.php)
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$query_string = "DELETE FROM reference_range WHERE measure_id=$measure_id";
		query_delete($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public static function getByAgeAndSex($age, $sex, $measure_id, $lab_config_id)
	{
		# Fetches the reference range based on supplied age and sex values
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
		$query_string = "SELECT * FROM reference_range WHERE measure_id=$measure_id";
		$retval = null;
		$resultset = query_associative_all($query_string, $row_count);
		if($resultset == null || count($resultset) == 0)
			return $retval;
		foreach($resultset as $record)
		{
			$ref_range = ReferenceRange::getObject($record);
			if($ref_range->ageMin == 0 && $ref_range->ageMax == 0)
			{
				# No agewise split
				if($ref_range->sex == "B" || strtolower($ref_range->sex) == strtolower($sex))
				{
					return $ref_range;
				}
			}
			else if($ref_range->ageMin <= $age && $ref_range->ageMax >= $age)
			{
				# Age wise split exists
				if($ref_range->sex == "B" || strtolower($ref_range->sex) == strtolower($sex))
				{
					return $ref_range;
				}
			}
		}
		DbUtil::switchRestore($saved_db);
	}
}


class DbUtil
{
	public static function switchToGlobal()
	{
		
		# Saves currently selected DB and switches to 
		# global/metadata DB instance
		global $DEBUG;
		if($DEBUG)
		{
			echo "In switchToGlobal()<br>";
			echo DebugLib::getCallerFunctionName(debug_backtrace())."<br>";
		}
		global $GLOBAL_DB_NAME;
		$saved_db_name = db_get_current();
		db_change($GLOBAL_DB_NAME);
		return $saved_db_name;
	}

	public static function switchToCountry($countryName) {
		# Saves currently selected DB and switches to 
		# country specific DB instance
		global $DEBUG;
		if($DEBUG) {
			echo "In switchToCountry()<br>";
			echo DebugLib::getCallerFunctionName(debug_backtrace())."<br>";
		}
		$saved_db_name = db_get_current();
		$dbName = "blis_".$countryName;
		db_change($dbName);
		return $saved_db_name;
	}
	
	public static function switchToLabConfig($lab_config_id)
	{
		# Saves currently selected DB and switches to
		# local/lab-specific DB instance
		# Used on pages that query data from different labs
		global $DEBUG;
		if($DEBUG)
		{
			echo "In switchToLabConfig($lab_config_id)<br>";
			echo DebugLib::getCallerFunctionName(debug_backtrace())."<br>";
		}
		$saved_db_name = db_get_current();
		$lab_config = get_lab_config_by_id($lab_config_id);
		if($lab_config == null)
		{
			# Error: Lab configuration correspinding to $lab_config_id not found in DB
			return;
		}
		$db_name = $lab_config->dbName;
		db_change($db_name);
		return $saved_db_name;
	}
	
	public static function switchToLabConfigRevamp($lab_config_id=null)
	{
		$saved_db_name = db_get_current();
		$lab_config = get_lab_config_by_id($lab_config_id);
		if($lab_config == null)
		{
			# Error: Lab configuration correspinding to $lab_config_id not found in DB
			return;
		}
		$db_name = $lab_config->dbName;
		db_change($db_name);
		return $saved_db_name;
	}
	
	public static function switchRestore($db_name)
	{
		# Reverts back to saved DB instance
		global $DEBUG;
		if($DEBUG)
		{
			echo "In switchRestore($db_name)<br>";
			echo DebugLib::getCallerFunctionName(debug_backtrace())."<br>";
		}
		db_change($db_name);
	}
}

class SessionUtil
{
	# Class for switching context between sessions
	public static function save()
	{
		$saved_session = array();
		foreach($_SESSION as $key=>$value)
		{
			$saved_session[$key] = $value;
		}
		return $saved_session;
	}
	
	public static function restore($saved_session)
	{
		foreach($saved_session as $key=>$value)
		{
			$_SESSION[$key] = $value;
		}
	}
	
	public static function includeIfMissing($include_path, $test_string)
	{
		# Includes a php file if found to be not included already
		$file_included = false;
		$included_list = get_included_files();
		foreach($included_list as $included_file)
		{
			if(strpos($included_file, $test_string) === true)
			{
				$file_included = true;
				break;
			}
		}
		if($file_included === false)
		{
			include($include_path);
		}
	}
}


#
# Functions for managing user profiles and login
#

function encrypt_password($password)
{
	# Encrypts cleartext password before adding to DB or matching passwords
	$salt = "This comment should suffice as salt.";
	return sha1($password.$salt);
}

function check_user_password($username, $password)
{
	# Verifies username and password
	$saved_db = DbUtil::switchToGlobal();
	$password = encrypt_password($password);
	$query_string = 
		"SELECT * FROM user ".
		"WHERE username='$username' ".
		"AND password='$password' LIMIT 1";
	$record = query_associative_one($query_string);
	# Return user profile (null if incorrect username/password)
	DbUtil::switchRestore($saved_db);
	return User::getObject($record);
}

function change_user_password($username, $password)
{
	# Changes user password
	$saved_db = DbUtil::switchToGlobal();
	$password = encrypt_password($password);
	$query_string =
		"UPDATE user ".
		"SET password='$password' ".
		"WHERE username='$username'";
	query_blind($query_string);
	DbUtil::switchRestore($saved_db);
}

function check_user_exists($username)
{
	# Checks if the username exists in DB
	$saved_db = DbUtil::switchToGlobal();
	$query_string = "SELECT username FROM user WHERE username='$username' LIMIT 1";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	if($record == null)
		return false;
	return true;
}

function add_user($user)
{
	# Adds a new user account
	$saved_db = DbUtil::switchToGlobal();
	$password = encrypt_password($user->password);
	$query_string = 
		"INSERT INTO user(username, password, actualname, level, created_by, lab_config_id, email, phone, lang_id) ".
		"VALUES ('$user->username', '$password', '$user->actualName', $user->level, $user->createdBy, $user->labConfigId, '$user->email', '$user->phone', '$user->langId')";
	query_insert_one($query_string);
	DbUtil::switchRestore($saved_db);
}

function update_user_profile($updated_entry)
{
	# Updates user profile information
	$saved_db = DbUtil::switchToGlobal();
	$user_id = $updated_entry->userId;
	$query_string = 
		"UPDATE user ".
		"SET email='$updated_entry->email', ".
		"phone='$updated_entry->phone', ".
		"actualname='$updated_entry->actualName', ".
		"lang_id='$updated_entry->langId' ".
		"WHERE user_id=$user_id";
	query_blind($query_string);
	DbUtil::switchRestore($saved_db);
}

function update_user_level($updated_entry)
{
	# Changes user access level
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"UPDATE user ".
		"SET level=$updated_entry->level ".
		"WHERE user_id=$updated_entry->userId";
	query_blind($query_string);
	DbUtil::switchRestore($saved_db);
}

function update_admin_user($updated_entry)
{
	# Updates lab admin account
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"UPDATE user ".
		"SET actualname='$updated_entry->actualName', ".
		"phone='$updated_entry->phone', ".
		"email='$updated_entry->email', ".
		"lang_id='$updated_entry->langId' ".
		"WHERE user_id=$updated_entry->userId";
	query_blind($query_string);
	if($updated_entry->password != "")
	{
		change_user_password($updated_entry->username, $updated_entry->password);
	}
	DbUtil::switchRestore($saved_db);
}

function update_lab_user($updated_entry)
{
	# Updates lab user (non-admin) account
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"UPDATE user ".
		"SET actualname='$updated_entry->actualName', ".
		"phone='$updated_entry->phone', ".
		"email='$updated_entry->email', ".
		"level=$updated_entry->level, ".
		"lang_id='$updated_entry->langId' ".
		"WHERE user_id=$updated_entry->userId";
	query_blind($query_string);
	if($updated_entry->password != "")
	{
		change_user_password($updated_entry->username, $updated_entry->password);
	}
	DbUtil::switchRestore($saved_db);
}

function delete_user_by_id($user_id)
{
	# Deletes a user from DB
	$saved_db = DbUtil::switchToGlobal();
	# Remove entries from lab_config_access
	$query_string = 
		"DELETE FROM lab_config_access ".
		"WHERE user_id=$user_id";
	query_blind($query_string);
	# Remove user record
	$query_string =
		"DELETE FROM user WHERE user_id=$user_id";
	query_blind($query_string);
	DbUtil::switchRestore($saved_db);
}

function get_user_by_id($user_id)
{
	# Fetches user record by primary key
	$saved_db = DbUtil::switchToGlobal();
	$query_string = "SELECT * FROM user WHERE user_id=$user_id LIMIT 1";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return User::getObject($record);
}

function get_username_by_id($user_id)
{
	# Returns username as string
	$saved_db = DbUtil::switchToGlobal();
	$query_string = "SELECT username FROM user WHERE user_id=$user_id";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	if($record == null)
		return LangUtil::$generalTerms['NOTKNOWN'];
	else
		return $record['username'];
}

function get_user_by_name($username)
{
	# Fetches user record by username
	$saved_db = DbUtil::switchToGlobal();
	$query_string = "SELECT * FROM user WHERE username='$username' LIMIT 1";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return User::getObject($record);
}

function get_admin_users()
{
	# Fetches list (assoc array) of admin users
	# Called from lab_admins.php
	global $LIS_ADMIN, $LIS_SUPERADMIN, $LIS_COUNTRYDIR;
	$saved_db = DbUtil::switchToGlobal();
	$query_string = "";
	if($_SESSION['user_level'] == $LIS_SUPERADMIN)
	{
		# Return all admin accounts
		$query_string = 
			"SELECT * FROM user ".
			"WHERE level=$LIS_ADMIN ORDER BY username";
	}
	else if($_SESSION['user_level'] == $LIS_COUNTRYDIR)
	{
		# Return all admin accounts from that country alone
		$query_string = 
			"SELECT u.* FROM user u ".
			"WHERE u.level=$LIS_ADMIN ".
			"AND (u.user_id IN ( ".
			"SELECT lc.admin_user_id FROM lab_config lc, lab_config_access lca ".
			"WHERE lc.lab_config_id=lca.lab_config_id ".
			"AND lca.user_id=".$_SESSION['user_id']." )) ".
			"OR u.created_by=".$_SESSION['user_id']." ".
			"ORDER BY u.username";
	}
	$retval = array();
	$resultset = query_associative_all($query_string, $row_count);
	foreach($resultset as $record)
	{
		$retval[] = User::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_level_by_id($user_id)
{
$user = get_user_by_id($user_id);
return $user->level;
}
function get_admin_user_list($user_id)
{
	# Fetches list (assoc array) of admin users
	# Called from lab_config_new.php
	global $LIS_ADMIN;
	$saved_db = DbUtil::switchToGlobal();
	$user = get_user_by_id($user_id);
	$retval = array();
	if(true)//is_super_admin($user))
	{
		# Super-admin level user: Return all admin accounts
		$query_string = 
			"SELECT * FROM user ".
			"WHERE level=$LIS_ADMIN";
	}
	else if(is_country_dir($user))
	{
		# Country dir: Return all admin accounts in that country alone
		$query_string = 
			"SELECT u.* FROM user u ".
			"WHERE u.level=$LIS_ADMIN ".
			"AND u.user_id IN ( ".
			"SELECT lc.admin_user_id FROM lab_config lc, lab_config_access lca ".
			"WHERE lc.lab_config_id=lca.lab_config_id ".
			"AND lca.user_id=".$_SESSION['user_id']." ) ".
			"ORDER BY u.username";
	}
	else
	{
		# Only admin level user: Return single option
		$query_string = "SELECT * FROM user WHERE user_id=$user_id";
	}
	$resultset = query_associative_all($query_string, $row_count);
	foreach($resultset as $record)
	{
		$retval[$record['user_id']] = $record['username'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}


#
# Functions for adding patient-related information
#

function add_patient($patient)
{
	# Adds a new patient to DB (called from ajax/patient_add.php)
	$pid = $patient->patientId;
	$addl_id = db_escape($patient->addlId);
	$name = db_escape($patient->name);
	$dob = db_escape($patient->dob);
	$partial_dob = db_escape($patient->partialDob);
	$age = db_escape($patient->age);
	$sex = $patient->sex;
	$receipt_date=db_escape($patient->regDate);
	$surr_id = db_escape($patient->surrogateId);
	$created_by = db_escape($patient->createdBy);
	$hash_value = $patient->generateHashValue();
	$query_string = "";
	
	/* Ensure that no other entry has been added prior to this function being called. If yes, update patientId */
	$maxPid = get_max_patient_id() + 1;
	if( $maxPid != $pid )
		$pid = $maxPid;
	
	if($dob == "" && $partial_dob == "")
	{
		$query_string = 
			"INSERT INTO `patient`(`patient_id`, `addl_id`, `name`, `age`, `sex`, `surr_id`, `created_by`, `hash_value` ,`ts`) ".
			"VALUES ($pid, '$addl_id', '$name', $age, '$sex', '$surr_id', $created_by, '$hash_value', '$receipt_date')";
	}
	else if($partial_dob != "")
	{
		$query_string = 
			"INSERT INTO `patient`(`patient_id`, `addl_id`, `name`, `age`, `sex`, `partial_dob`, `surr_id`, `created_by`, `hash_value`,`ts`) ".
			"VALUES ($pid, '$addl_id', '$name', $age, '$sex', '$partial_dob', '$surr_id', $created_by, '$hash_value', '$receipt_date')";
	}
	else
	{
		$query_string =
			"INSERT INTO `patient`(`patient_id`, `addl_id`, `name`, `dob`, `age`, `sex`, `surr_id`, `created_by`, `hash_value`, `ts`) ".
			"VALUES ($pid, '$addl_id', '$name', '$dob', $age, '$sex', '$surr_id', $created_by, '$hash_value', '$receipt_date')";
	}
	
	print $query_string;
	query_insert_one($query_string);
	return true;
}

function check_patient_id($pid)
{
	# Checks if patient ID already exists in DB, and returns true/false accordingly
	# Called from ajax/patient_check_id.php
	$query_string = "SELECT patient_id FROM patient WHERE patient_id=$pid LIMIT 1";
	$retval = query_associative_one($query_string);
	if($retval == null)
		return false;
	else
		return true;
}
function get_patient_by_sp_id($sid)
{
$query_string="SELECT patient_id FROM specimen WHERE specimen_id=$sid ";
//echo $query_string;
$resultset = query_associative_one($query_string);
$patient_list = array();
	if(count($resultset) > 0)
	{
		
		$id= $resultset['patient_id'];
		Patient::getById($patient_id);
			$patient_list[] = Patient::getById($id);
			//print_r($patient_list);
		}

return $patient_list;


}
function get_patient_by_id($pid)
{
	# Fetches a patient record by patient id
	return Patient::getById($pid);
}

function search_patients_by_id($q)
{
	# Searches for patients with similar PID
	$query_string = 
		"SELECT * FROM patient ".
		"WHERE surr_id='$q'";
	$resultset = query_associative_all($query_string, $row_count);
	$patient_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$patient_list[] = Patient::getObject($record);
		}
	}
	return $patient_list;
}

function search_patients_by_name($q)
{
	# Searches for patients with similar name
	$query_string = 
		"SELECT * FROM patient ".
		"WHERE name LIKE '%$q%'";
	$resultset = query_associative_all($query_string, $row_count);
	$patient_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$patient_list[] = Patient::getObject($record);
		}
	}
	return $patient_list;
}

function search_patients_by_addlid($q)
{
	# Searches for patients with similar addl ID
	$query_string = 
		"SELECT * FROM patient ".
		"WHERE addl_id LIKE '%$q%'";
	$resultset = query_associative_all($query_string, $row_count);
	$patient_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$patient_list[] = Patient::getObject($record);
		}
	}
	return $patient_list;
}

function search_patients_by_dailynum($q)
{
	# Searches for patients with similar daily number
	$query_string = "SELECT DISTINCT patient_id FROM specimen WHERE daily_num LIKE '%".$q."' ORDER BY date_collected DESC LIMIT 20";
	$resultset = query_associative_all($query_string, $row_count);
	$patient_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$patient_list[] = Patient::getById($record['patient_id']);
		}
	}
	return $patient_list;
}

function search_specimens_by_id($q)
{
	# Searches for specimens with similar ID
	$query_string = 
		"SELECT * FROM specimen ".
		"WHERE specimen_id LIKE '%$q%'";
	$resultset = query_associative_all($query_string, $row_count);
	$specimen_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$specimen_list[] = Specimen::getObject($record);
		}
	}
	return $specimen_list;
}

function search_specimens_by_addlid($q)
{
	# Searches for specimens with similar addl ID
	$query_string = 
		"SELECT * FROM specimen ".
		"WHERE aux_id LIKE '%$q%'";
	$resultset = query_associative_all($query_string, $row_count);
	$specimen_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$specimen_list[] = Specimen::getObject($record);
		}
	}
	return $specimen_list;
}

function search_specimens_by_patient_id($patient_id)
{
	# Searches for specimens by patient ID
	$query_string = 
		"SELECT sp.* FROM specimen sp, patient p ".
		"WHERE sp.patient_id=p.patient_id ".
		"AND p.patient_id = '".$patient_id."'";// OR p.patient_id LIKE '%".$patient_id."%'";
	$resultset = query_associative_all($query_string, $row_count);
	$specimen_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$specimen_list[] = Specimen::getObject($record);
		}
	}
	return $specimen_list;
}

function search_specimens_by_patient_name($patient_name)
{
	# Searches for specimens by patient name
	$query_string = 
		"SELECT sp.* FROM specimen sp, patient p ".
		"WHERE sp.patient_id=p.patient_id ".
		"AND p.name LIKE '%$patient_name%' ";
	$resultset = query_associative_all($query_string, $row_count);
	$specimen_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$specimen_list[] = Specimen::getObject($record);
		}
	}
	return $specimen_list;
}

function search_specimens_by_session($q)
{
	# Searched for specimens in a single session
	$query_string =
		"SELECT * FROM specimen ".
		"WHERE session_num LIKE '%$q%'";
	$resultset = query_associative_all($query_string, $row_count);
	$specimen_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$specimen_list[] = Specimen::getObject($record);
		}
	}
	return $specimen_list;
}

function search_specimens_by_session_exact($q)
{
	# Searched for specimens in a single session
	$query_string =
		"SELECT * FROM specimen ".
		"WHERE session_num='$q'";
	$resultset = query_associative_all($query_string, $row_count);
	$specimen_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$specimen_list[] = Specimen::getObject($record);
		}
	}
	return $specimen_list;
}

function search_specimens_by_dailynum($q)
{
	# Searched for specimens in a single session
	$query_string =
		"SELECT * FROM specimen ".
		"WHERE daily_num LIKE '%-$q' ORDER BY daily_num DESC LIMIT 5";
	$resultset = query_associative_all($query_string, $row_count);
	$specimen_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$specimen_list[] = Specimen::getObject($record);
		}
	}
	return $specimen_list;
}

function get_patients_by_name_or_id($search_term)
{
	# Searches for patients with similar PID or Name
	# Called from patient_fetch.php
	$query_string = 
		"SELECT * FROM patient ".
		"WHERE patient_id LIKE '%$search_term%' ".
		"OR name LIKE '%$search_term%'";
	$resultset = query_associative_all($query_string, $row_count);
	$patient_list = array();
	if(count($resultset) > 0)
	{
		foreach($resultset as $record)
		{
			$patient_list[] = Patient::getObject($record);
		}
	}
	return $patient_list;
}

function update_patient($modified_record)
{
	# Updates an existing patient record
	# Called from ajax/patient_update.php
	$myFile = "../../local/myFile.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
$pid = $modified_record->patientId;
	$current_record = get_patient_by_id($pid);
	if($modified_record->name == "")
		$modified_record->name = $current_record->name;
	if(trim($modified_record->age) == "" || is_nan($modified_record->age))
		$modified_record->age=0;
	$query_string = 
		"UPDATE patient SET ".
		"name='$modified_record->name', ".
		"surr_id='$modified_record->surrogateId', ".
		"addl_id='$modified_record->addlId', ".
		"sex='$modified_record->sex', ";
	if($modified_record->age != 0)
	{
		$today = date("Y-m-d");
		$today_parts = explode("-", $today);
		# Find year of birth based on supplied age value
		if($modified_record->age < 0)
		{
			# Age was specified in months
			$timestamp = mktime(0, 0, 0, $today_parts[1]-(-1*$modified_record->age), $today_parts[2], $today_parts[0]);
			$year = date("Y", $timestamp);
			$month = date("m", $timestamp);
			$dob = "";
			$modified_record->partialDob = $year."-".$month;
		}
		else
		{
			# Age specified in years
			$timestamp = mktime(0, 0, 0, $today_parts[1], $today_parts[2], $today_parts[0]-$modified_record->age);
			$year = date("Y", $timestamp);
			$dob = "";
			$modified_record->partialDob = $year;
		}
	}
	$modified_record->age = 0;
	if($modified_record->partialDob != "")
		$query_string .= "age=$modified_record->age, partial_dob='$modified_record->partialDob' ";
	else if($modified_record->dob != "")
		$query_string .= "age=$modified_record->age, partial_dob='', dob='$modified_record->dob' ";
	$query_string .= "WHERE patient_id=$pid";
	fwrite($fh, $query_string);
fclose($fh);
	query_blind($query_string);
	# Addition of custom fields: done from calling function/page	
	return true;
}


#
# Functions for handling specimen/test-related data
#

function get_pending_tests_by_type($test_type_id)
{
	# Returns a list of pending tests for a given test type
	$query_string =
		"SELECT * FROM test WHERE test_type_id=$test_type_id ".
		"AND result=''";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = Test::getObject($record);
	}
	return $retval;
}

function get_pending_tests_by_type_date($test_type_id, $date_from,$date_to)
{
$date_from_array=explode("-",$date_from);
$date_to_array=explode("-",$date_to);
	# Returns a list of pending tests for a given test type
	$query_string =
		"SELECT * FROM test WHERE test_type_id=$test_type_id ".
		"AND ts >= '$date_from_array[0]-$date_from_array[1]-$date_from_array[2] 00:00:00' ".
		"AND ts <='$date_to_array[0]-$date_to_array[1]-$date_to_array[2] 23:59:59'".
		"AND result=''";
		
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = Test::getObject($record);
	}
	return $retval;
}

function get_tests_by_specimen_id($specimen_id)
{
	# Returns list of tests scheduled for this given specimen
	$query_string = "SELECT * FROM test WHERE specimen_id=$specimen_id";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = Test::getObject($record);
	}
	return $retval;
}

function get_completed_tests_by_type($test_type_id, $date_from="", $date_to="")
{
	# Returns list of tests of a particular type,
	# that were registered between date_from and date_to and completed
	$query_string = "";
	if($date_from == "" || $date_to == "")
	{
		if($test_type_id == 0)
		{
			$query_string = 
				"SELECT UNIX_TIMESTAMP(t.ts) as ts, t.specimen_id, UNIX_TIMESTAMP(s.date_collected) as date_collected ".
				"FROM test t, specimen s ".
				"WHERE t.result <> '' ".
				"AND s.specimen_id=t.specimen_id ORDER BY s.date_collected";
		}
		else
		{
			$query_string = 
				"SELECT UNIX_TIMESTAMP(t.ts) as ts, t.specimen_id, UNIX_TIMESTAMP(s.date_collected) as date_collected ".
				"FROM test t, specimen s ".
				"WHERE s.test_type_id=$test_type_id ".
				"AND t.result <> '' ".
				"AND s.specimen_id=t.specimen_id ORDER BY s.date_collected";
		}
	}
	else
	{
		if($test_type_id == 0)
		{
			$query_string = 
				"SELECT UNIX_TIMESTAMP(t.ts) as ts, t.specimen_id, UNIX_TIMESTAMP(s.date_collected) as date_collected ".
				"FROM test t, specimen s ".
				"WHERE t.result <> '' ".
				"AND s.specimen_id=t.specimen_id ".
				"AND s.date_collected between '$date_from' AND '$date_to' ORDER BY s.date_collected";
		}
		else
		{
			$query_string = 
				"SELECT UNIX_TIMESTAMP(t.ts) as ts, t.specimen_id, UNIX_TIMESTAMP(s.date_collected) as date_collected ".
				"FROM test t, specimen s ".
				"WHERE t.test_type_id=$test_type_id ".
				"AND t.result <> '' ".
				"AND s.specimen_id=t.specimen_id ".
				"AND s.date_collected between '$date_from' AND '$date_to' ORDER BY s.date_collected";
		}
	}
	$resultset = query_associative_all($query_string, $row_count);
	# Entries for {ts, specimen_id, date_collected} are returned
	return $resultset;
}

function get_pendingtat_tests_by_type($test_type_id, $date_from="", $date_to="")
{
	# Returns list of pending tests of a particular type,
	# that were registered between date_from and date_to and not completed
	$query_string = "";
	if($date_from == "" || $date_to == "")
	{
		if($test_type_id == 0)
		{
			$query_string = 
				"SELECT UNIX_TIMESTAMP(t.ts) as ts, t.specimen_id, UNIX_TIMESTAMP(s.date_collected) as date_collected ".
				"FROM test t, specimen s ".
				"WHERE t.result = '' ".
				"AND s.specimen_id=t.specimen_id ORDER BY s.date_collected";
		}
		else
		{
			$query_string = 
				"SELECT UNIX_TIMESTAMP(t.ts) as ts, t.specimen_id, UNIX_TIMESTAMP(s.date_collected) as date_collected ".
				"FROM test t, specimen s ".
				"WHERE s.test_type_id=$test_type_id ".
				"AND t.result = '' ".
				"AND s.specimen_id=t.specimen_id ORDER BY s.date_collected";
		}
	}
	else
	{
		if($test_type_id == 0)
		{
			$query_string = 
				"SELECT UNIX_TIMESTAMP(t.ts) as ts, t.specimen_id, UNIX_TIMESTAMP(s.date_collected) as date_collected ".
				"FROM test t, specimen s ".
				"WHERE t.result = '' ".
				"AND s.specimen_id=t.specimen_id ".
				"AND s.date_collected between '$date_from' AND '$date_to' ORDER BY s.date_collected";
		}
		else
		{
			$query_string = 
				"SELECT UNIX_TIMESTAMP(t.ts) as ts, t.specimen_id, UNIX_TIMESTAMP(s.date_collected) as date_collected ".
				"FROM test t, specimen s ".
				"WHERE t.test_type_id=$test_type_id ".
				"AND t.result = '' ".
				"AND s.specimen_id=t.specimen_id ".
				"AND s.date_collected between '$date_from' AND '$date_to' ORDER BY s.date_collected";
		}
	}
	$resultset = query_associative_all($query_string, $row_count);
	# Entries for {ts, specimen_id, date_collected} are returned
	return $resultset;
}

function get_specimens_by_patient_id($patient_id)
{
	# Returns list of specimens registered for the given patient
	$query_string = 
		"SELECT * FROM specimen WHERE patient_id=$patient_id ORDER BY date_collected DESC";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = Specimen::getObject($record);
	}
	return $retval;
}

function add_specimen($specimen)
{
	# Adds a new specimen record in DB
	$query_string = 
		"INSERT INTO specimen (
			specimen_id, 
			patient_id, 
			specimen_type_id, 
			date_collected, 
			date_recvd, 
			user_id, 
			status_code_id, 
			referred_to, 
			comments, 
			aux_id,
			session_num, 
			time_collected, 
			report_to, 
			doctor, 
			referred_to_name, 
			daily_num
		) 
		VALUES (
			$specimen->specimenId, 
			$specimen->patientId, 
			$specimen->specimenTypeId, 
			'$specimen->dateCollected', 
			'$specimen->dateRecvd', 
			$specimen->userId, 
			$specimen->statusCodeId, 
			$specimen->referredTo, 
			'$specimen->comments', 
			'$specimen->auxId', 
			'$specimen->sessionNum', 
			'$specimen->timeCollected', 
			$specimen->reportTo, 
			'$specimen->doctor', 
			'$specimen->referredToName', 
			'$specimen->dailyNum'
		)";
	echo $query_string;
	query_insert_one($query_string);
	return $specimen->specimenId;
}

function check_specimen_id($sid)
{
	# Checks if specimen ID already exists in DB, and returns true/false accordingly
	# Called from ajax/specimen_check_id.php
	$query_string = "SELECT specimen_id FROM specimen WHERE specimen_id=$sid LIMIT 1";
	$retval = query_associative_one($query_string);
	if($retval == null)
		return false;
	else
		return true;
}

function add_test($test)
{
	# Adds a new test record in DB
	$query_string = 
		"INSERT INTO test (
			test_type_id, 
			specimen_id, 
			result, 
			comments,
			verified_by,
			user_id ) 
		VALUES (
			$test->testTypeId,  
			$test->specimenId, 
			'$test->result', 
			'$test->comments', 
			0, 
			$test->userId )";
	query_insert_one($query_string);
	return get_last_insert_id();
}

function get_test_entry($specimen_id, $test_type_id)
{
	# Returns the test_id primary key
	$query_string = 
		"SELECT * FROM test ".
		"WHERE specimen_id=$specimen_id ".
		"AND test_type_id=$test_type_id LIMIT 1";
	$record = query_associative_one($query_string);
	$retval = Test::getObject($record);
	return $retval;
}

function add_test_result($test_id, $result_entry, $comments="", $specimen_id="", $user_id=0, $ts="", $hash_value)
{
	# Adds results for a test entry
	$curent_ts = "";
	if($ts == "")
		$current_ts = date("Y-m-d H:i:s");
	else
		$current_ts = date("Y-m-d H:i:s" , $ts);
	# Append patient hash value to result field
	$result_field = $result_entry.$hash_value;
	# Add entry to DB
	$query_string = 
		"UPDATE test SET result='$result_field', ".
		"comments='$comments', ".
		"user_id=$user_id, ".
		"ts='$current_ts' ".
		"WHERE test_id=$test_id ";
	
	query_blind($query_string);
	# If specimen ID was passed, update its status
	if($specimen_id != "")
		update_specimen_status($specimen_id);
}

function update_specimen_status($specimen_id)
{
	# Checks if all test results for the specimen have been entered,
	# and updates specimen status accordingly
	$test_list = get_tests_by_specimen_id($specimen_id);
	foreach($test_list as $test)
	{
		if($test->isPending() === true)
		{
			# This test result is pending
			return;
		}
	}
	# Update specimen status to complete
	$status_code = Specimen::$STATUS_DONE;
	set_specimen_status($specimen_id, $status_code);
}

function set_specimen_status($specimen_id, $status_code)
{
	# Sets specimen status to specified status code
	# TODO: Link this to customized status codes in 'status_code' table
	$query_string = 
		"UPDATE specimen SET status_code_id=$status_code ".
		"WHERE specimen_id=$specimen_id";
	query_blind($query_string);
}

function get_specimen_status($specimen_id)
{
	# Returns status of the given specimen
	# TODO: Link this to customized status codes in 'status_code' table
	$query_string = 
		"SELECT status_code_id FROM specimen ".
		"WHERE specimen_id=$specimen_id LIMIT 1";
	$record = query_associative_one($query_string);
	return $record['status_code_id'];
}

function get_specimen_by_id($specimen_id)
{
	# Fetches a specimen record by specimen id
	$query_string = 
		"SELECT * FROM specimen WHERE specimen_id=$specimen_id LIMIT 1";
	$record = query_associative_one($query_string);
	return Specimen::getObject($record);
}

function get_specimens_by_session($session_num)
{
	# Returns all specimens registered in this session
	$query_string = 
		"SELECT * FROM specimen ".
		"WHERE session_num='$session_num'";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
		$retval[] = Specimen::getObject($record);
	return $retval;
}


#
# Functions for adding lab configuration (site) related data
#

function add_lab_config($lab_config)
{
	# Adds a new lab configuration to DB
	$saved_db = DbUtil::switchToGlobal();
	$query_add_lab_config = 
		"INSERT INTO lab_config(name, location, admin_user_id, id_mode) ".
		"VALUES ('$lab_config->name', '$lab_config->location', $lab_config->adminUserId, $lab_config->idMode)";
	query_insert_one($query_add_lab_config);
	$lab_config_id = get_last_insert_id();
	foreach($lab_config->testList as $test_type_id)
	{
		# Add entry to 'lab_config_test_type' map table
		add_lab_config_test_type($lab_config_id, $test_type_id);
	}
	foreach($lab_config->specimenList as $specimen_type_id)
	{
		# Add entry to 'lab_config_specimen_type' map table
		add_lab_config_specimen_type($lab_config_id, $specimen_type_id);
	}
	DbUtil::switchRestore($saved_db);
	return $lab_config_id;
}

function add_lab_config_with_id($lab_config)
{
	# Adds a new lab configuration to DB, when ID already known
	$lab_config_id = $lab_config->id;
	$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$query_add_lab_config = 
		"INSERT INTO lab_config(lab_config_id, name, location, admin_user_id, id_mode) ".
		"VALUES ($lab_config->id, '$lab_config->name', '$lab_config->location', $lab_config->adminUserId, $lab_config->idMode)";
	query_insert_one($query_add_lab_config);
	foreach($lab_config->testList as $test_type_id)
	{
		# Add entry to 'lab_config_test_type' map table
		add_lab_config_test_type($lab_config_id, $test_type_id);
	}
	foreach($lab_config->specimenList as $specimen_type_id)
	{
		# Add entry to 'lab_config_specimen_type' map table
		add_lab_config_specimen_type($lab_config_id, $specimen_type_id);
	}
	DbUtil::switchRestore($saved_db);
	return $lab_config_id;
}

function update_lab_config($updated_entry, $updated_specimen_list=null, $updated_test_list=null)
{
	# Updates a lab configuration record
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"UPDATE lab_config ".
		"SET name='$updated_entry->name', ".
		"location='$updated_entry->location', ".
		"admin_user_id=$updated_entry->adminUserId, ".
		"id_mode=$updated_entry->idMode ".
		"WHERE lab_config_id=$updated_entry->id";
	query_blind($query_string);
	DbUtil::switchRestore($saved_db);
	$saved_db = DbUtil::switchToLabConfigRevamp();
	if($updated_specimen_list != null)
	{
		# Update specimen types
		$existing_specimen_list = get_lab_config_specimen_types($updated_entry->id);
		## Add new specimen types
		foreach($updated_specimen_list as $specimen_type_id)
		{
			if(in_array($specimen_type_id, $existing_specimen_list))
			{
				# Entry already present
				# Continue
			}
			else
			{
				# Add specimen type entry in mapping table
				$query_ins= 
					"INSERT INTO lab_config_specimen_type(lab_config_id, specimen_type_id) ".
					"VALUES ($updated_entry->id, $specimen_type_id)";
				query_blind($query_ins);
			}
		}
		## Remove specimen types marked as deleted
		foreach($existing_specimen_list as $specimen_type_id)
		{
			if(in_array($specimen_type_id, $updated_specimen_list))
			{
				# Not marked for removal
				# Continue
			}
			else
			{
				# Remove specimen type entry from mapping table
				$query_del = 
					"DELETE FROM lab_config_specimen_type ".
					"WHERE lab_config_id=$updated_entry->id ".
					"AND specimen_type_id=$specimen_type_id";
				query_blind($query_del);	
			}
		}
	}
	if($updated_test_list != null)
	{
		# Update test types
		$existing_test_list = get_lab_config_test_types($updated_entry->id);
		## Add new test types
		foreach($updated_test_list as $test_type_id)
		{
			if(in_array($test_type_id, $existing_test_list))
			{
				# Entry already present
				# Continue
			}
			else
			{
				# Add test type entry in mapping table
				$query_ins= 
					"INSERT INTO lab_config_test_type(lab_config_id, test_type_id) ".
					"VALUES ($updated_entry->id, $test_type_id)";
				query_blind($query_ins);
			}
		}
		## Remove test types marked as deleted
		foreach($existing_test_list as $test_type_id)
		{
			if(in_array($test_type_id, $updated_test_list))
			{
				# Not marked for removal
				# Continue
			}
			else
			{
				# Remove test type entry from mapping table
				$query_del = 
					"DELETE FROM lab_config_test_type ".
					"WHERE lab_config_id=$updated_entry->id ".
					"AND test_type_id=$test_type_id";
				query_blind($query_del);
				# Remove worksheet config for this test type
				if($test_type_id != 0)
				{
					$saved_db2 = DbUtil::switchToLabConfig($updated_entry->id);
					$query_del2 = 
						"DELETE FROM report_config WHERE test_type_id=$test_type_id";
					query_delete($query_del2);
					DbUtil::switchRestore($saved_db2);
				}				
			}
		}
	}
	DbUtil::switchRestore($saved_db);
}
///////////////////////////////
//This is where the stock module all starts//
///////////////////////////////
function update_stocks_details($name,$lot_number,$expiry_date,$manu,$quant,$supplier,$entry_id, $cost)
{
$saved_db = DbUtil::switchToLabConfigRevamp();
$query_string = 
				"UPDATE stock_details SET name='$name',lot_number='$lot_number',expiry_date='$expiry_date',manufacturer='$manu',supplier='$supplier',current_quantity='$quant' , cost_per_unit='$cost' WHERE entry_id=$entry_id";
				query_update($query_string);
				DbUtil::switchRestore($saved_db);
}

function get_reagent_name()
{
$retval= array();
$saved_db = DbUtil::switchToLabConfigRevamp();
$query_string = "SELECT distinct(name) FROM stock_details ";
$resultset = query_associative_all($query_string, $row_count);
		//print $resultset[0];
		if($resultset!=null)
		{
		foreach($resultset as $record)
		{
		//echo $record['name'];
		$retval[]=$record['name'];
		}		
	}
				DbUtil::switchRestore($saved_db);
return $retval;
}

function get_stock_details($entry_id)
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = "SELECT name,lot_number,expiry_date,manufacturer,supplier,current_quantity,unit ,cost_per_unit FROM stock_details WHERE entry_id='$entry_id' ";
	$resultset = query_associative_one($query_string);
	if($resultset!=null)
	{
		$name=$resultset['name'];
		$lot_number=$resultset['lot_number'];
		$date=$resultset['expiry_date'];
		$manu=$resultset['manufacturer'];
		$supplier=$resultset['supplier'];
		$quant=$resultset['current_quantity'];
		$unit=$resultset['unit'];
		$cost=$resultset['cost_per_unit'];
		$retval=array($name,$lot_number,$manu,$quant,$date,$supplier,$unit,$cost);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}
function add_stocks($name,$lot_number,$expiry_date,$manufacture,$supplier,$quantity_supplied,$unit , $cost_per_unit,$ts) {
	# Adds a new stock or update the quantity of the stock
	
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$length= count($name);
	
	# Find the entry_id (primary key)
	$query_string = "SELECT MAX(entry_id) as'entry_id'FROM stock_details";
	$record = query_associative_one($query_string);
	if($record == null || $record['entry_id'] == null)
	$entry_id=0;
	else
	$entry_id=$record['entry_id'];
	
	for($i=0;$i<$length;$i++) {
		$current_entry_id=$entry_id+1+$i;
		if($name[$i]!="")
		{
			$query_string="SELECT current_quantity FROM stock_details WHERE name='$name[$i]'";
			$resultset = query_associative_all($query_string, $row_count);
			$current_quantity=$quantity_supplied[$i];
			if($resultset!=null) {
				foreach($resultset as $record )
					$current_quantity=$current_quantity+$record['current_quantity'];
				if($record['current_quantity']!=0) {
					$query_string = "UPDATE stock_details SET current_quantity=$current_quantity WHERE name= '$name[$i]'";
					query_update($query_string);
				}
		
			}
			
			# If same lot number then no need to add another entry into stock_details table
			$query_string="SELECT quantity_ordered, quantity_supplied, unit FROM stock_details WHERE name='$name[$i]' AND lot_number='$lot_number[$i]' AND manufacturer='$manufacture[$i]' AND supplier='$supplier[$i]' AND cost_per_unit=$cost_per_unit[$i] LIMIT 1";
			$resultset = query_associative_all($query_string,$row_count);
			$current_ts = date("Y-m-d H:i:s" , $ts[$i]);
			if($resultset==null) {
				$query_string = 
					"INSERT INTO stock_details(name,lot_number,expiry_date, manufacturer, quantity_ordered,quantity_supplied,current_quantity,supplier,unit,entry_id,cost_per_unit,date_of_reception, date_of_supply) ".
					"VALUES ('$name[$i]','$lot_number[$i]','$expiry_date[$i]', '$manufacture[$i]', '$quantity_supplied[$i]' ,'$quantity_supplied[$i]','$current_quantity','$supplier[$i]','$unit[$i]','$current_entry_id','$cost_per_unit[$i]', '$current_ts','$current_ts')";
				query_insert_one($query_string);
				$query_string=
					"INSERT INTO stock_content(name,current_quantity,date_of_use,lot_number,new_balance)".
					"VALUES('$name[$i]',0,'$current_ts','$lot_number[$i]','$current_quantity')";
				query_insert_one($query_string);
			}
			else {
				foreach($resultset as $record) {
					$quantity_ordered = $record['quantity_ordered'] + $current_quantity;
					$quantity_supplied = $record['quantity_supplied'] + $current_quantity;
					$unit= $record['unit'] + $unit[$i];
				}
				$query_string = "UPDATE stock_details SET quantity_ordered =$quantity_ordered, quantity_supplied=$quantity_supplied, unit=$unit, date_of_reception='$current_ts', ".
								"date_of_supply='$current_ts' WHERE name='$name[$i]' AND lot_number='$lot_number[$i]' AND manufacturer='$manufacture[$i]' AND supplier='$supplier[$i]' ".
								"AND cost_per_unit=$cost_per_unit[$i]";
				query_update($query_string);
			}
		}
		DbUtil::switchRestore($saved_db);
	}
}
function get_stock_count()
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = "SELECT name,current_quantity ,unit FROM stock_details GROUP BY name ";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	if($resultset!=null){
		foreach($resultset as $record){
			$name=$record['name'];
			$quant=$record['current_quantity'];
			$unit=$record['unit'];
			$retval[]=array($name,$quant,$unit);
		}
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}
function get_stock_out_details()
{
$saved_db = DbUtil::switchToLabConfigRevamp();
$query_string = 
		"SELECT name,current_quantity,new_balance,date_of_use,lot_number,user_name FROM stock_content";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		if($resultset!=null){
		foreach($resultset as $record)
		{
		$name=$record['name'];
		$lot_number=$record['lot_number'];
		$current_quantity=(int)$record['current_quantity'];
		$new_balance=(int)$record['new_balance'];
		$quantity=$new_balance-$current_quantity;
		$date_of_entry=$record['date_of_use'];
		$user=$record['user_name'];
		if($current_quantity==0)
		{
		$current_quantity=$quantity;
		$quantity=$quantity."(new stock)";
		
		}
		$retval[]= array($name,$lot_number,$quantity,$date_of_entry,$user,$current_quantity);	
		}
		}
		
		return $retval;
DbUtil::switchRestore($saved_db);
}
function get_current_inventory_byName($date_to,$date_from, $name)
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"SELECT name,new_balance,date_of_use, lot_number FROM stock_content WHERE date_of_use<='$date_to' AND date_of_use >= '$date_from' AND name='$name' ";
		$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	if($resultset!=null){
		foreach($resultset as $record) {
			$name=$record['name'];
			$quantity=$record['new_balance'];
			$date_of_entry=$record['date_of_use'];
			$lot_number=$record['lot_number'];
		$retval[]= array( $name,$quantity,$date_of_entry, $lot_number);	
		}
	}
	return $retval;
	DbUtil::switchRestore($saved_db);

}
function get_current_inventory($date_to,$date_from)
{
$saved_db = DbUtil::switchToLabConfigRevamp();
$query_string = 
		"SELECT name,new_balance,date_of_use, lot_number FROM stock_content WHERE date_of_use<='$date_to' AND date_of_use >= '$date_from' ";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		if($resultset!=null){
		foreach($resultset as $record)
		{
		$name=$record['name'];
		$quantity=$record['new_balance'];
		$date_of_entry=$record['date_of_use'];
		$lot_number=$record['lot_number'];
		$retval[]= array( $name,$quantity,$date_of_entry, $lot_number);	
		}
		}
		
		return $retval;
DbUtil::switchRestore($saved_db);

}

#Called from stock_edit to get data from stock_details
function get_stocks()
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"SELECT name,lot_number, manufacturer,current_quantity,expiry_date,supplier,unit,entry_id ,cost_per_unit, date_of_reception FROM stock_details ORDER BY name ASC, manufacturer ASC, lot_number ASC, date_of_reception DESC";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	if($resultset!=null){
		foreach($resultset as $record) {
			$name=$record['name'];
			$manufacture=$record['manufacturer'];
			$lot_number=$record['lot_number'];
			$quantity=$record['current_quantity'];
			$expiry_date=$record['expiry_date'];
			$supplier=$record['supplier'];
			$unit=$record['unit'];
			$entry_id=$record['entry_id'];
			$cost=$record['cost_per_unit'];
			$date_of_reception = $record['date_of_reception'];
			$retval[]= array($name, $lot_number,$manufacture,$quantity,$expiry_date,$supplier,$unit,$entry_id ,$cost, $receive_date);	
		}
	}
	DbUtil::switchRestore($saved_db);		
	return $retval;
}

function get_entry_ids()
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"SELECT entry_id  FROM stock_details ORDER BY entry_id ";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	if($resultset!=null){
		foreach($resultset as $record) {
			$retval[]=$record['entry_id'];
		}
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function update_stocks($name,$lot_number,$quant,$receiver,$remarks, $ts)
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$current_ts = date("Y-m-d H:i:s" , $ts);
	$query_string = 
		"SELECT current_quantity, quantity_supplied, quantity_used, used, user_name, receiver, remarks FROM stock_details WHERE name='$name' and lot_number='$lot_number'";
	$record = query_associative_one($query_string);
	
	if($record!=null) {
		$current_quantity=(int)$record['current_quantity'];
		$quantity_supplied=$record['quantity_supplied'];
		$quantity_used=$record['quantity_used'];
		$use=$record['used'];
		$username=$record['user_name'];
		$receiver_old=$record['receiver'];
		$remarks_old=$record['remarks'];
				
		$quantity_supplied_integer=(int)$quantity_supplied;
		$quantity_used_integer=(int)$quantity_used;
		$quant_integer=(int)$quant;
		
		$quant_finally_used=$quant_integer+$quantity_used_integer;
		$new_current_quant=$current_quantity-$quant_integer;
		$user_name=get_username_by_id($_SESSION['user_id']);
		
		#Checking to see if first entry or not and then appending or creating
		if($use=="") 
			$use_new=$current_ts.":".$quantity_integer;	
		else
			$use_new=$use.",".$current_ts.":".$quantity_integer;
			
		if($receiver=="")
			$receiver_new=$receiver;
		else
			$receiver_new=$receiver_old.",".$receiver;
			
		if($remarks=="")
			$remarks_new=$remarks;
		else
			$remarks_new=$remarks_old.",".$remarks;
			
		#Checking to see that the quantity is present in DB	
		if($quant_finally_used<=$quantity_supplied_integer) { 
			$quant_final=$current_quantity-$quant_integer;
			//$query_string = 
				// "INSERT INTO stock_content(name, current_quantity,lot_number, receiver ,remarks ,new_balance ,date_of_use,user_name) ".
				// "VALUES ('$name', '$quant_final' ,'$lot_number','$receiver', '$remarks','$new_current_quant','$current_ts' , '$user_name')";
			//query_insert_one($query_string);
			
			$query_string = 
				"UPDATE stock_details SET current_quantity=$quant_final WHERE name= '$name'";
			query_update($query_string);
			$query_string = 
				"UPDATE stock_details SET quantity_used=$quant_finally_used, used='$use_new', receiver='$receiver_new', remarks='$remarks_new' ".
				"WHERE name= '$name' and lot_number='$lot_number'";
			query_update($query_string);
	
			DbUtil::switchRestore($saved_db);
			return 1;
		}
		else{
			DbUtil::switchRestore($saved_db);
			return -1;
		}
		
	}
}


/////////////////////////////////
// This is end of stock module //
/////////////////////////////////
function delete_lab_config($lab_config_id)
{
	# Deletes a lab configuration and all related data
	$saved_db = DbUtil::switchToGlobal();
	$lab_config = get_lab_config_by_id($lab_config_id);
	if($lab_config == null)
	{
		# Not found or error
		return;
	}
	# Delete DB instance
	db_delete($lab_config->dbName);
	# Delete DB revamp instance
	$revamp_db_name = "blis_revamp_".$lab_config->id;
	db_delete($revamp_db_name);
	# Delete entries from lab_config_access
	$query_string =
		"DELETE FROM lab_config_access ".
		"WHERE lab_config_id=$lab_config->id";
	query_blind($query_string);
	# Delete technician accounts
	$query_string = 
		"DELETE FROM user ".
		"WHERE lab_config_id=$lab_config->id";
	query_blind($query_string);
	# Delete disease report settings
	$query_string =
		"DELETE FROM report_disease WHERE lab_config_id=$lab_config->id";
	query_blind($query_string);
	# Delete entry from lab_config
	$query_string = 
		"DELETE FROM lab_config ".
		"WHERE lab_config_id=$lab_config->id";
	query_blind($query_string);
	DbUtil::switchRestore($saved_db);
}

function create_lab_config_tables($lab_config_id, $db_name)
{
	# Creates empty tables for a new lab configuration
	db_change($db_name);
	$file_name = '../data/create_tables.sql';
	$sql_file = fopen($file_name, 'r');
	$sql_string = fread($sql_file, filesize($file_name));
	$sql_command_list = explode(";", $sql_string);
	foreach($sql_command_list as $sql_command)
	{
		query_blind($sql_command.";");
	}
}

function create_lab_config_revamp_tables($lab_config_id, $revamp_db_name)
{
	# Creates empty tables for a new lab configuration (revamp)
	db_change($revamp_db_name);
	$file_name = '../data/create_tables_revamp.sql';
	$sql_file = fopen($file_name, 'r');
	$sql_string = fread($sql_file, filesize($file_name));
	$sql_command_list = explode(";", $sql_string);
	foreach($sql_command_list as $sql_command)
	{
		query_blind($sql_command.";");
	}
}

function set_lab_config_db_name($lab_config_id, $db_name)
{
	# Sets database instance name for lab configuration
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"UPDATE lab_config ".
		"SET db_name='$db_name' ".
		"WHERE lab_config_id=$lab_config_id ";
	query_blind($query_string);
	DbUtil::switchRestore($saved_db);
}


#
# Functions for fetching lab configuration (site) related data
#

function get_lab_config_by_id($lab_config_id)
{
	# Returns a lab configuration record by id
	return LabConfig::getById($lab_config_id);
}

function get_lab_config_id_admin($user_id)
{

$query_string = "SELECT lab_config_id FROM lab_config WHERE admin_user_id='$user_id'";
$record = query_associative_one($query_string);
		$id = $record['lab_config_id'];
		
return $id;
}

function get_lab_config_id($user_id)
{
	$saved_db = DbUtil::switchToGlobal();

	$query_string = "SELECT lab_config_id FROM user WHERE user_id='$user_id'";
	$record = query_associative_one($query_string);
			$id = $record['lab_config_id'];
			DbUtil::switchRestore($saved_db);	
	return $id;
}
function get_lab_configs($admin_user_id = "")
{
	# Returns all lab configs present in DB and accessible by admin-level user
	# If admin_user_id not supplied, all stored lab configs are returned
	$saved_db = DbUtil::switchToGlobal();
	$user = null;
	if($admin_user_id != "")
		$user = get_user_by_id($admin_user_id);
	if($admin_user_id == "" || is_super_admin($user))
	{
		# Super admin user: Fetch lab configs stored in DB
		$query_configs = "SELECT * FROM lab_config ORDER BY name";
	}
	else if(is_country_dir($user))
	{
		# Country director: Fetch lab configs from lab_config_access table
		$query_configs = 
			"SELECT * from lab_config lc ".
			"WHERE lc.lab_config_id IN ( ".
			"SELECT lca.lab_config_id from lab_config_access lca ".
			"WHERE lca.user_id=$admin_user_id ) ".
			"ORDER BY lc.name";
	}
	else
	{
		# Fetch all lab configs
		$query_configs = 
			"SELECT * FROM lab_config ".
			"WHERE admin_user_id=$admin_user_id ".
			"OR lab_config_id IN ( ".
			"	SELECT lab_config_id FROM lab_config_access ".
			"	WHERE user_id=$admin_user_id ".
			") ORDER BY name";
	}
	$retval = array();
	$resultset = query_associative_all($query_configs, $row_count);
	if($resultset == null)
	{
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	foreach($resultset as $record)
	{
		$retval[] = LabConfig::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_lab_config_num_patients($lab_config_id)
{
	# Returns total number of patients present in lab configuration
	$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	$retval = query_num_rows("patient");
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_lab_config_num_specimens($lab_config_id)
{
	# Returns total number of specimens present in lab configuration
	$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	$retval = query_num_rows("specimen");
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_lab_config_num_specimens_pending($lab_config_id, $specimen_type_id="")
{
	# Returns total number of pending specimens present in lab configuration
	$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	if($specimen_type_id != "")
	{
		# Narrow down to specimen type
		$query_string = 
			"SELECT COUNT(DISTINCT t.specimen_id) AS val ".
			"FROM test t, specimen sp ".
			"WHERE t.result='' ".
			"AND sp.specimen_id=t.specimen_id ".
			"AND sp.specimen_type_id=$specimen_type_id";
	}
	else
	{
		# Count for all specimen types
		$query_string = 
			"SELECT COUNT(DISTINCT specimen_id) AS val FROM test ".
			"WHERE result=''";
	}
	$record = query_associative_one($query_string);
	$retval = $record['val'];
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_lab_config_num_tests_pending($lab_config_id, $test_type_id="")
{
	# Returns total number of pending tests in lab configuration
	$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	if($test_type_id != "")
	{
		# Narrow down to test type
		$query_string = 
			"SELECT COUNT(*) AS val FROM test ".
			"WHERE result='' ".
			"AND test_type_id=$test_type_id";
	}
	else
	{
		# Count for all test types
		$query_string = 
			"SELECT COUNT(*) AS val FROM test ".
			"WHERE result=''";
	}
	$record = query_associative_one($query_string);
	$retval = $record['val'];
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_tests_done_this_month($lab_config)
{
	# Returns count of tests performed in the current month
	$retval = array();
	$saved_db = DbUtil::switchToLabConfig($lab_config->id);
	$date_to = date("Y-m-d");
	$date_from = $date_to;
	$date_from_parts = explode("-", $date_from);
	$date_from_parts[2] = "01";
	$date_from = implode("-", $date_from_parts);
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_site_list($user_id)
{
	# Returns a list of accessible site names and ids for a given user (admin or technician)
	$saved_db = DbUtil::switchToGlobal();
	$user = get_user_by_id($user_id);
	$retval = array();
	//if($user->isAdmin())
	if(is_admin($user))
	{
		# Admin level user
		# Return all owned/accessible lab configurations
		# If superadmin, return all lab configurations
		if(is_super_admin($user))
			$lab_config_list = get_lab_configs();
		else
			$lab_config_list = get_lab_configs($user_id);
		foreach($lab_config_list as $lab_config)
		{
			$retval[$lab_config->id] = $lab_config->getSiteName();
		}
	}
	else
	{
		# Technician user -> Return local lab configuration
		$lab_config = get_lab_config_by_id($user->labConfigId);
		$retval[$user->labConfigId] = $lab_config->getSiteName();
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_test_types_by_site($lab_config_id="")
{
	# Returns a list of test types configured for a particular site
	$saved_db = "";
	if($lab_config_id == "")
		$saved_db = DbUtil::switchToLabConfigRevamp();
	else
		$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$retval = array();
	if($lab_config_id === "")
		$query_string = "SELECT * FROM test_type ORDER BY name";
	else
		$query_string = 
			"SELECT tt.* FROM test_type tt, lab_config_test_type lctt ".
			"WHERE tt.test_type_id=lctt.test_type_id ".
			"AND lctt.lab_config_id=$lab_config_id ORDER BY tt.name";
	$resultset = query_associative_all($query_string, $row_count);
	foreach($resultset as $record)
	{
		$retval[] = TestType::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_test_types_by_site_category($lab_config_id, $cat_code)
{
	# Returns a list of test types of a particular section (category),
	# configured for a particular site
	$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$retval = array();
	$query_string = 
		"SELECT tt.* FROM test_type tt, lab_config_test_type lctt ".
		"WHERE tt.test_type_id=lctt.test_type_id ".
		"AND lctt.lab_config_id=$lab_config_id ORDER BY tt.name";
	$resultset = query_associative_all($query_string, $row_count);
	foreach($resultset as $record)
	{
		$test_type_entry = TestType::getObject($record);
		if($test_type_entry->testCategoryId == $cat_code)
		{
			# Category code matched: Append to result list.
			$retval[] = TestType::getObject($record);
		}
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_test_types_by_site_map($lab_config_id)
{
	# Returns a list of test types configured for a particular site
	global $CATALOG_TRANSLATION;
	$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$retval = array();
	$query_string = 
		"SELECT tt.* FROM test_type tt, lab_config_test_type lctt ".
		"WHERE tt.test_type_id=lctt.test_type_id ".
		"AND lctt.lab_config_id=$lab_config_id ".
		"ORDER BY tt.name";
	$resultset = query_associative_all($query_string, $row_count);
	foreach($resultset as $record)
	{
		if($CATALOG_TRANSLATION === true)
			$retval[$record['test_type_id']] = LangUtil::getTestName($record['test_type_id']);
		else
			$retval[$record['test_type_id']] = $record['name'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_users_by_site_map($lab_config_id)
{
	# Returns a list of usernames configured for a particular site
	$saved_db = DbUtil::switchToGlobal();
	$retval = array();
	$query_string = 
		"SELECT u.* FROM user u ".
		"WHERE lab_config_id=$lab_config_id ORDER BY u.username";
	$resultset = query_associative_all($query_string, $row_count);
	if($resultset != null)
	{
		foreach($resultset as $record)
		{
			$retval[$record['user_id']] = $record['username'];
		}
	}
	# Append lab admin account
	$lab_config = get_lab_config_by_id($lab_config_id);
	$admin_name = get_username_by_id($lab_config->adminUserId);
	$retval[$lab_config->adminUserId] = $admin_name;
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_tech_users_by_site_map($lab_config_id)
{
	# Returns a list of technician usernames configured for a particular site
	$saved_db = DbUtil::switchToGlobal();
	$retval = array();
	$query_string = 
		"SELECT u.* FROM user u ".
		"WHERE lab_config_id=$lab_config_id ORDER BY u.username";
	$resultset = query_associative_all($query_string, $row_count);
	if($resultset != null)
	{
		foreach($resultset as $record)
		{
			$retval[$record['user_id']] = $record['username'];
		}
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}


function get_specimen_types_by_site($lab_config_id="")
{
	# Returns a list of specimen types configured for a particular site
	$saved_db = "";
	if($lab_config_id == "")
		$saved_db = DbUtil::switchToLabConfigRevamp();
	else
		$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$retval = array();
	if($lab_config_id === "")
		$query_string = "SELECT * FROM specimen_type WHERE disabled=0 ORDER BY NAME";
	else
		$query_string = 
			"SELECT st.* FROM specimen_type st, lab_config_specimen_type lcst ".
			"WHERE st.disabled=0  AND st.specimen_type_id=lcst.specimen_type_id ".
			"AND lcst.lab_config_id=$lab_config_id ORDER BY st.name";
	$resultset = query_associative_all($query_string, $row_count);
	foreach($resultset as $record)
	{
		$retval[] = SpecimenType::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}


#
# Functions for adding data to catalog
#

function add_specimen_type($specimen_name, $specimen_descr, $test_list=array())
{
	# Adds a new specimen type in DB with compatible tests in $test_list
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"INSERT INTO specimen_type(name, description) ".
		"VALUES ('$specimen_name', '$specimen_descr')";
	query_insert_one($query_string);
	$specimen_type_id = get_max_specimen_type_id();
	if(count($test_list) != 0)
	{
		# For each compatible test type, add a new entry in 'specimen_test' map table
		foreach($test_list as $test_type_id)
		{
			$query_string = 
				"INSERT INTO specimen_test(test_type_id, specimen_type_id) ".
				"VALUES ($test_type_id, $specimen_type_id)";
			query_insert_one($query_string);
		}
	}
	# Return primary key of the record just inserted
	DbUtil::switchRestore($saved_db);
	//return get_max_specimen_type_id();
	return get_last_insert_id();
}

function update_specimen_type($updated_entry, $new_test_list)
{
	# Updates specimen type info in DB catalog
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$existing_entry = get_specimen_type_by_id($updated_entry->specimenTypeId);
	if($existing_entry == null)
	{
		# No record found
		DbUtil::switchRestore($saved_db);
		return;
	}
	$query_string =
		"UPDATE specimen_type ".
		"SET name='$updated_entry->name', ".
		"description='$updated_entry->description' ".
		"WHERE specimen_type_id=$updated_entry->specimenTypeId";
	query_blind($query_string);
	# Delete entries for removed compatible tests
	$existing_list = get_compatible_tests($updated_entry->specimenTypeId);
	foreach($existing_list as $test_type_id)
	{
		if(in_array($test_type_id, $new_test_list))
		{
			# Compatible test not removed
			# Do nothing
		}
		else
		{
			# Remove entry from mapping table
			$query_del = 
				"DELETE from specimen_test ".
				"WHERE test_type_id=$test_type_id ".
				"AND specimen_type_id=$updated_entry->specimenTypeId";
			query_blind($query_del);
		}
	}
	# Add entries for new compatible tests
	foreach($new_test_list as $test_type_id)
	{
		if(in_array($test_type_id, $existing_list))
		{
			# Entry already exists
			# Do nothing
		}
		else
		{
			# Add entry in mapping table
			$query_ins = 
				"INSERT INTO specimen_test (specimen_type_id, test_type_id) ".
				"VALUES ($updated_entry->specimenTypeId, $test_type_id)";
			query_blind($query_ins);
		}
	}
	DbUtil::switchRestore($saved_db);
}

function add_test_type($test_name, $test_descr,$clinical_data, $cat_code, $is_panel, $specimen_list=array(), $lab_config_id, $hide_patient_name)
{
	# Adds a new test type in DB with compatible specimens in 'specimen_list'
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$is_panel_num = 1;
	if($is_panel == false)
	{
		$is_panel_num = 0;
	}
	if($clinical_data=="")
	{
	$query_string = 
		"INSERT INTO test_type(name, description,test_category_id, is_panel, hide_patient_name) ".
		"VALUES ('$test_name', '$test_descr','$cat_code', '$is_panel_num', '$hide_patient_name')";
	}
	else
	{
	$query_string = 
		"INSERT INTO test_type(name, description,clinical_data, test_category_id, is_panel, hide_patient_name) ".
		"VALUES ('$test_name', '$test_descr','$clinical_data', '$cat_code', '$is_panel_num', '$hide_patient_name')";
	}
	query_insert_one($query_string);
	$test_type_id = get_max_test_type_id();
	if(count($specimen_list) != 0)
	{
		# For each compatible test type, add a new entry in 'specimen_test' map table
		foreach($specimen_list as $specimen_type_id)
		{
			add_specimen_test($specimen_type_id, $test_type_id);
		}
	}
	# Return primary key of the record just inserted
	DbUtil::switchRestore($saved_db);
	return get_max_test_type_id();
}

function update_test_type($updated_entry, $new_specimen_list,$lab_config_id)
{
	# Updates test type info in DB catalog
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$existing_entry = get_test_type_by_id($updated_entry->testTypeId);
	if($existing_entry == null)
	{
		# No record found
		DbUtil::switchRestore($saved_db);
		return;
	}
	if($lab_config_id=="128") {
	$query_string =
		"UPDATE test_type ".
		"SET name='$updated_entry->name', ".
		"description='$updated_entry->description', ".
		//"clinical_data='$updated_entry->clinical_data', ".
		"test_category_id='$updated_entry->testCategoryId', ".
		"hide_patient_name='$updated_entry->hide_patient_name', ".
		"prevalence_threshold=$updated_entry->prevalenceThreshold, ".
		"target_tat=$updated_entry->targetTat ".
		"WHERE test_type_id=$updated_entry->testTypeId";
	}
	else {
	$query_string =
		"UPDATE test_type ".
		"SET name='$updated_entry->name', ".
		"description='$updated_entry->description', ".
		"clinical_data='$updated_entry->clinical_data', ".
		"test_category_id='$updated_entry->testCategoryId', ".
		"hide_patient_name='$updated_entry->hide_patient_name', ".
		"prevalence_threshold=$updated_entry->prevalenceThreshold, ".
		"target_tat=$updated_entry->targetTat ".
		"WHERE test_type_id=$updated_entry->testTypeId";
	}
	query_blind($query_string);
	# Delete entries for removed compatible specimens
	$existing_list = get_compatible_specimens($updated_entry->testTypeId);
	foreach($existing_list as $specimen_type_id)
	{
		if(in_array($specimen_type_id, $new_specimen_list))
		{
			# Compatible specimen not removed
			# Do nothing
		}
		else
		{
			# Remove entry from mapping table
			$query_del = 
				"DELETE from specimen_test ".
				"WHERE test_type_id=$updated_entry->testTypeId ".
				"AND specimen_type_id=$specimen_type_id";
			query_blind($query_del);
		}
	}
	# Add entries for new compatible specimens
	foreach($new_specimen_list as $specimen_type_id)
	{
		if(in_array($specimen_type_id, $existing_list))
		{
			# Entry already exists
			# Do nothing
		}
		else
		{
			# Add entry in mapping table
			$query_ins = 
				"INSERT INTO specimen_test (specimen_type_id, test_type_id) ".
				"VALUES ($specimen_type_id, $updated_entry->testTypeId)";
			query_blind($query_ins);
		}
	}
	DbUtil::switchRestore($saved_db);
}

function add_test_category($cat_name, $cat_descr="")
{
	# Adds a new test category to catalog
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"INSERT INTO test_category(name, description) ".
		"VALUES ('$cat_name', '$cat_descr')";
	query_insert_one($query_string);
	# Return primary key of the record just inserted
	DbUtil::switchRestore($saved_db);
	return get_max_test_cat_id();
}

function add_measure($measure, $range, $unit)
{
	# Adds a new measure to catalog
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"INSERT INTO measure(name, range, unit) ".
		"VALUES ('$measure', '$range', '$unit')";
	query_insert_one($query_string);
	# Return primary key of the record just inserted
	DbUtil::switchRestore($saved_db);
	return get_max_measure_id();
}


#
# Functions for fetching data from catalog
#

function get_specimen_types_catalog($to_global=false)
{
	# Returns a list of all specimen types available in catalog
	global $CATALOG_TRANSLATION;
	if($to_global === false)
		$saved_db = DbUtil::switchToLabConfigRevamp();
	//else
		//$saved_db = DbUtil::switchToGlobal();	
	$query_stypes =
		"SELECT specimen_type_id, name FROM specimen_type WHERE disabled=0 ORDER BY name";
	$resultset = query_associative_all($query_stypes, $row_count);
	$retval = array();
	//echo "Lab Config Id is $lab_config_id";
	if($resultset) {
		foreach($resultset as $record)
		{
			if($CATALOG_TRANSLATION === true)
				$retval[$record['specimen_type_id']] = LangUtil::getSpecimenName($record['specimen_type_id']);
			else
				$retval[$record['specimen_type_id']] = $record['name'];
		}
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_test_types_catalog($to_global=false)
{
	# Returns a list of all test types available in catalog
	global $CATALOG_TRANSLATION;
	if($to_global === false)
		$saved_db = DbUtil::switchToLabConfigRevamp();
	//else
		//$saved_db = DbUtil::switchToGlobal();	
	$query_ttypes =
		"SELECT test_type_id, name FROM test_type WHERE disabled=0 ORDER BY name";
	$resultset = query_associative_all($query_ttypes, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		if($CATALOG_TRANSLATION === true)
			$retval[$record['test_type_id']] = LangUtil::getTestName($record['test_type_id']);
		else
			$retval[$record['test_type_id']] = $record['name'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}
function getDoctorNames()
{

	$query_string = "SELECT doctor FROM specimen WHERE date_collected >'2010-08-11'";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
	$doc_name=$record['doctor'];
	$pos=strpos($doc_name,".");
	if($pos!=-1 && $pos < 5)
	$doc_name=substr($doc_name,$pos+2);
	$retval[]=$doc_name;
	}

	return $retval;
}

function get_test_categories($lab_config_id=null)
{
	# Returns a list of all test categories available in catalog
	global $CATALOG_TRANSLATION;
	$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$query_string = "SELECT test_category_id, name FROM test_category";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		if($CATALOG_TRANSLATION === true)
			$retval[$record['test_category_id']] = LangUtil::getLabSectionName($record['test_category_id']);
		else
			$retval[$record['test_category_id']] = $record['name'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_test_category_name_by_id($cat_id)
{
	# Returns test category name as string
	global $CATALOG_TRANSLATION;
	if($CATALOG_TRANSLATION === true)
	{
		$saved_db = DbUtil::switchToLabConfig();
		$query_string = 
			"SELECT * FROM test_category ".
			"WHERE test_category_id=$cat_id LIMIT 1";
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		return LangUtil::getLabSectionName($record['test_category_id']);
	}
	else
	{
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"SELECT name FROM test_category ".
			"WHERE test_category_id=$cat_id LIMIT 1";
		$record = query_associative_one($query_string);
		$retval = LangUtil::$generalTerms['NOTKNOWN'];
		if($record != null)
			$retval = $record['name'];
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
}

function get_test_types_wcat_catalog()
{
	# Returns a list of all test types available in catalog, with category name appended
	global $CATALOG_TRANSLATION;
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_ttypes =
		"SELECT test_type_id, name FROM test_type";
	$resultset = query_associative_all($query_ttypes, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		if($CATALOG_TRANSLATION === true)
			$retval[$record['test_type_id']] = LangUtil::getTestName($record['test_type_id']);
		else
			$retval[$record['test_type_id']] = $record['name'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;	
}

function getMeasuresByLab($labConfigId) {
	$saved_db = DbUtil::switchToLabConfig($labConfigId);
	$query_string = "SELECT * FROM measure ORDER BY name";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
		$retval[] = Measure::getObject($record);
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_measures_catalog()
{
	# Returns a list of all measures available in catalog
	global $CATALOG_TRANSLATION;
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_measures =
		"SELECT measure_id, name FROM measure ORDER BY name";
	$resultset = query_associative_all($query_measures, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		if($CATALOG_TRANSLATION === true)
			$retval[$record['measure_id']] = LangUtil::getMeasureName($record['measure_id']);
		else
			$retval[$record['measure_id']] = $record['name'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_measure_by_id($measure_id)
{
	# Returns Measure object from DB
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_measure = 
		"SELECT * FROM measure WHERE measure_id=$measure_id LIMIT 1";
	$record = query_associative_one($query_measure);
	$retval = Measure::getObject($record);
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_test_type_measure($test_type_id)
{
	# Returns list of measures for a given test type
	# Moved to TestType::getMeasures()
	$test_type = TestType::getById($test_type_id);
	if($test_type != null)
	{
		return $test_type->getMeasures();
	}
	else
	{
		$dummy_list = array();
		return $dummy_list;
	}	
}

function get_specimen_type_by_name($specimen_name)
{
	# Returns specimen type record in DB
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"SELECT * FROM specimen_type WHERE name='$specimen_name' AND disabled=0 LIMIT 1";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return SpecimenType::getObject($record);
}

function get_test_type_by_name($test_name)
{
	# Returns test type record in DB
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$test_name = addslashes($test_name);
	$query_string =
		"SELECT * FROM test_type WHERE name='$test_name' AND disabled=0 LIMIT 1";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return TestType::getObject($record);
}

function get_specimen_name_by_id($specimen_type_id)
{
	# Returns specimen type name string
	global $CATALOG_TRANSLATION;
	if($CATALOG_TRANSLATION === true)
		return LangUtil::getSpecimenName($specimen_type_id);
	else
	{
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"SELECT name FROM specimen_type ".
			"WHERE specimen_type_id=$specimen_type_id LIMIT 1";
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		if($record == null)
			return LangUtil::$generalTerms['NOTKNOWN'];
		else
			return $record['name'];
	}
}



function get_test_name_by_id($test_type_id, $lab_config_id=null)
{
	# Returns test type name string
	global $CATALOG_TRANSLATION;
	if($CATALOG_TRANSLATION === true)
	return LangUtil::getTestName($test_type_id);
	else
	{
		$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
		$query_string = 
			"SELECT name FROM test_type ".
			"WHERE test_type_id=$test_type_id LIMIT 1";
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		if($record == null)
			return LangUtil::$generalTerms['NOTKNOWN'];
		else
			return $record['name'];
	}
}

function get_clinical_data_by_id($test_type_id)
{
	# Returns test type name string
	global $CATALOG_TRANSLATION;
	if($CATALOG_TRANSLATION === true)
	return LangUtil::getTestName($test_type_id);
	else
	{
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"SELECT clinical_data FROM test_type ".
			"WHERE test_type_id=$test_type_id LIMIT 1";
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		if($record == null)
			return LangUtil::$generalTerms['NOTKNOWN'];
		else
			return $record['clinical_data'];
	}
}

function get_test_type_by_id($test_type_id)
{
	# Returns test type record in DB
	return TestType::getById($test_type_id);
}

function get_specimen_type_by_id($specimen_type_id)
{
	# Returns specimen type record in DB
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string =
		"SELECT * FROM specimen_type WHERE specimen_type_id=$specimen_type_id LIMIT 1";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return SpecimenType::getObject($record);
}



#
# Functions for jquery.token-input plugin
# Called from pages in ajax/token_*.php
#

function search_measures_catalog($measure_name)
{
	# Returns a list of matching measures available in catalog
	# Called from ajax/token_tmeas.php
	global $CATALOG_TRANSLATION;
	$saved_db = DbUtil::switchToGlobal();
	$query_string =
		"SELECT measure_id, name FROM measure WHERE name LIKE '$measure_name%'";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		if($CATALOG_TRANSLATION === true)
			$retval[$record['measure_id']] = LangUtil::getMeasureName($record['measure_id']);
		else
			$retval[$record['measure_id']] = $record['name'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function search_test_types_catalog($test_name)
{
	# Returns matching test types available in catalog
	# Called from ajax/token_ttypes.php
	global $CATALOG_TRANSLATION;
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"SELECT * FROM test_type WHERE name LIKE '$test_name%' AND disabled=0";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		if($CATALOG_TRANSLATION === true)
			$retval[$record['test_type_id']] = LangUtil::getTestName($record['test_type_id']);
		else
			$retval[$record['test_type_id']] = $record['name'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function search_specimen_types_catalog($specimen_name)
{
	# Returns matching test types available in catalog
	# Called from ajax/token_stypes.php
	global $CATALOG_TRANSLATION;
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"SELECT * FROM specimen_type WHERE disabled=0 AND name LIKE '$specimen_name%'";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		if($CATALOG_TRANSLATION === true)
			$retval[$record['specimen_type_id']] = LangUtil::getSpecimenName($record['specimen_type_id']);
		else
			$retval[$record['specimen_type_id']] = $record['name'];		
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}


#
# Functions to fetch the largest primary key value,
# or primary key of the latest inserted record
#

function get_max_specimen_type_id()
{
	# Returns the largest specimen type ID
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string =
		"SELECT MAX(specimen_type_id) as maxval FROM specimen_type";
	$resultset = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return $resultset['maxval'];
}

function get_max_test_type_id()
{
	# Returns the largest test type ID
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string =
		"SELECT MAX(test_type_id) as maxval FROM test_type";
	$resultset = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return $resultset['maxval'];
}
function getDoctorList()
{

$query_string =
		"SELECT DISTINCT doctor FROM specimen WHERE doctor!=' ' AND ts >'2010-11-11' ORDER BY ts desc  ";
		$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	if($resultset == null)
		return $retval;
	foreach($resultset as $record)
	{
		
		$retval[] = $record['doctor'];
	}
		return $retval;
}
function get_max_test_cat_id()
{
	# Returns the largest test category type ID
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string =
		"SELECT MAX(test_category_id) as maxval FROM test_category";
	$resultset = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return $resultset['maxval'];
}

function get_max_measure_id()
{
	# Returns the largest measure ID
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string =
		"SELECT MAX(measure_id) as maxval FROM measure";
	$resultset = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return $resultset['maxval'];
}

function get_max_lab_config_id()
{
	# Returns the largest lab_config ID
	$saved_db = DbUtil::switchToGlobal();
	$query_string =
		"SELECT MAX(lab_config_id) as maxval FROM lab_config";
	$resultset = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return $resultset['maxval'];
}

function get_max_patient_id()
{
	# Returns the largest patient ID
	$query_string =
		"SELECT MAX(patient_id) as maxval FROM patient";
	$resultset = query_associative_one($query_string);
	return $resultset['maxval'];
}

function get_max_specimen_id()
{
	# Returns the largest specimen ID
	$query_string =
		"SELECT MAX(specimen_id) as maxval FROM specimen";
	$resultset = query_associative_one($query_string);
	return $resultset['maxval'];
}

function getStartDate()
{

$today = date("Ymd");
	$query_string =
		"SELECT ts FROM specimen".
		"WHERE session_id=1";
	//	echo $query_string;

	$record = query_associative_one($query_string);
	//echo "fhi";
	///echo $record['ts'];
//echo "dsd";

}

function get_session_number()
{
	# Generate the next session number for specimen registration
	$today = date("Ymd");
	$query_string =
		"SELECT * FROM specimen_session ".
		"WHERE session_num='$today'";
	$record = query_associative_one($query_string);
	if($record == null) {
		$returnValue = $today."-1";
		update_session_number(date("Ymd"));
	}
	else {
		$returnValue = $today."-".($record['count']+1);
		update_session_number(date("Ymd"));
	}
	return $returnValue;
}

function get_session_current_number()
{

$today = date("Ymd");
	$query_string =
		"SELECT * FROM specimen_session ".
		"WHERE session_num='$today'";
	$record = query_associative_one($query_string);
	if($record == null)
		return $today."-1";
	else
		return $today."-".($record['count']);
}

function update_session_number($session_date_string)
{
	# Updates count values for session numbers
	# Called from ajax/session_num_update.php
	$query_string = 
		"SELECT * FROM specimen_session ".
		"WHERE session_num='$session_date_string'";
	$record = query_associative_one($query_string);
	if($record == null)
	{
		# No entry exists. Add one.
		$query_string = 
			"INSERT INTO specimen_session(session_num, count) ".
			"VALUES ('$session_date_string', 1)";
		query_insert_one($query_string);
	}
	else
	{
		# Update count value
		$new_count = $record['count'] + 1;
		$query_string =
			"UPDATE specimen_session ".
			"SET count=$new_count ".
			"WHERE session_num='$session_date_string'";
		query_blind($query_string);
	}
}

function get_daily_number()
{
	# Generate the next daily number for specimen registration
	$today = date("Ymd");
	switch($_SESSION['dnum_reset'])
	{
		case LabConfig::$RESET_DAILY:
			$today = date("Ymd");
			break;
		case LabConfig::$RESET_WEEKLY:
			$today = date("Y_W");
			break;
		case LabConfig::$RESET_MONTHLY:
			$today = date("Ym");
			break;
		case LabConfig::$RESET_YEARLY:
			$today = date("Y");
			break;
	}
	$query_string =
		"SELECT * FROM patient_daily ".
		"WHERE datestring='$today'";
	$record = query_associative_one($query_string);

	if($record == null) {
		$returnValue = 1;
		$query_string = "INSERT INTO patient_daily (datestring, count) ".
						"VALUES ('$today', $returnValue)";
		query_insert_one($query_string);
		//update_session_number(date("Ymd"));
	}
	else {
		$returnValue = $record['count']+1;
		$query_string = "update patient_daily set count=$returnValue where datestring='$today' ";
		query_blind($query_string);
		//update_session_number(date("Ymd"));
	}
	return $returnValue;
}

function update_daily_number($daily_date_string, $curr_count)
{
	# Updates count values for daily numbers
	# Called from ajax/daily_num_update.php
	# Find current count value
	$count_val = $curr_count;
	$query_string = "SELECT * FROM patient_daily WHERE datestring='$daily_date_string'";
	$record = query_associative_one($query_string);
	if($record == null)
	{
		# No entry exists. Add one.
		$query_string = 
			"INSERT INTO patient_daily(datestring, count) ".
			"VALUES ('$daily_date_string', $count_val)";
		query_insert_one($query_string);
	}
	else
	{
		# Update count value
		$old_count = $record['count'];
		$new_count = $old_count+1;
		$query_string = "UPDATE patient_daily ".
						"SET count=$new_count ".
						"WHERE datestring='$daily_date_string'";
		query_blind($query_string);
	}
}

#
# Functions for adding data to mapping tables in catalog
#

function add_test_type_measure($test_type_id, $measure_id)
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	# Adds a new entry to test_type->measure map table
	$query_check = 
		"SELECT * FROM test_type_measure ".
		"WHERE test_type_id=$test_type_id AND measure_id=$measure_id";
	$flag_exists = query_associative_one($query_check);
	if($flag_exists != null)
	{
		# Mapping already exists
		# TODO: Add error handling?
		DbUtil::switchRestore($saved_db);
		return;
	}
	$query_add =
		"INSERT INTO test_type_measure(test_type_id, measure_id) ".
		"VALUES ($test_type_id, $measure_id)";
	query_insert_one($query_add);
	DbUtil::switchRestore($saved_db);
}

function delete_test_type_measure($test_type_id, $measure_id)
{
	# Deletes the mapping entry between test_type and measure
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"DELETE FROM test_type_measure ".
		"WHERE test_type_id=$test_type_id AND measure_id=$measure_id";
	query_delete($query_string);
	# Check if any other test type uses this measure.
	# If not, delete entry from 'measure' table
	$query_string =
		"SELECT * FROM test_type_measure ".
		"measure_id=$measure_id";
	$resultset = query_associative_all($query_string, $row_count);
	if($resultset == null || count($resultset) == 0)
	{
		$query_delete = "DELETE FROM measure WHERE measure_id=$measure_id";
		query_delete($query_delete);
	}
	DbUtil::switchRestore($saved_db);
}

function add_specimen_test($specimen_type_id, $test_type_id)
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	# Adds a new entry to specimen_type->test_type map table
	$query_check = 
		"SELECT * FROM specimen_test ".
		"WHERE test_type_id=$test_type_id AND specimen_type_id=$specimen_type_id";
	$flag_exists = query_associative_one($query_check);
	if($flag_exists != null)
	{
		# Mapping already exists
		# TODO: Add error handling?
		DbUtil::switchRestore($saved_db);
		return;
	}
	$query_add =
		"INSERT INTO specimen_test(specimen_type_id, test_type_id) ".
		"VALUES ($specimen_type_id, $test_type_id)";
	query_insert_one($query_add);
	DbUtil::switchRestore($saved_db);
}

function add_lab_config_test_type($lab_config_id, $test_type_id)
{
	# Adds a new entry to lab_config->test_type map table
	$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$query_check = 
		"SELECT * FROM lab_config_test_type ".
		"WHERE test_type_id=$test_type_id AND lab_config_id=$lab_config_id";
	$flag_exists = query_associative_one($query_check);
	if($flag_exists != null)
	{
		# Mapping already exists
		# TODO: Add error handling?
		return;
	}
	$query_add =
		"INSERT INTO lab_config_test_type(lab_config_id, test_type_id) ".
		"VALUES ($lab_config_id, $test_type_id)";
	query_insert_one($query_add);
	DbUtil::switchRestore($saved_db);
}

function add_lab_config_specimen_type($lab_config_id, $specimen_type_id)
{
	# Adds a new entry to lab_config->specimen_type map table
	$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$query_check = 
		"SELECT * FROM lab_config_specimen_type ".
		"WHERE specimen_type_id=$specimen_type_id AND lab_config_id=$lab_config_id";
	$flag_exists = query_associative_one($query_check);
	if($flag_exists != null)
	{
		# Mapping already exists
		# TODO: Add error handling?
		return;
	}
	$query_add =
		"INSERT INTO lab_config_specimen_type(lab_config_id, specimen_type_id) ".
		"VALUES ($lab_config_id, $specimen_type_id)";
	query_insert_one($query_add);
	DbUtil::switchRestore($saved_db);
}

function add_lab_config_access($user_id, $lab_config_id)
{
	# Adds access to a new lab config for a country dir user
	$saved_db = DbUtil::switchToGlobal();
	$query_check = 
		"SELECT * FROM lab_config_access ".
		"WHERE user_id=$user_id AND lab_config_id=$lab_config_id";
	$flag_exists = query_associative_one($query_check);
	if($flag_exists != null)
	{
		# Mapping already exists
		# TODO: Add error handling?
		return;
	}
	$query_add =
		"INSERT INTO lab_config_access(user_id, lab_config_id) ".
		"VALUES ($user_id, $lab_config_id)";
	query_insert_one($query_add);
	DbUtil::switchRestore($saved_db);
}

#
# Functions for fetching data from mapping tables
#

function get_compatible_tests($specimen_type_id)
{
	# Returns a list of compatible tests for a given specimen type in catalog
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"SELECT test_type_id FROM specimen_test WHERE specimen_type_id=$specimen_type_id";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	if($resultset == null)
		return $retval;
	foreach($resultset as $record)
	{
		$retval[] = $record['test_type_id'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_compatible_specimens($test_type_id)
{
	# Returns a list of compatible specimens for a given test type in catalog
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$query_string = 
		"SELECT specimen_type_id FROM specimen_test WHERE test_type_id=$test_type_id";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	if($resultset == null)
		return $retval;
	foreach($resultset as $record)
	{
		$retval[] = $record['specimen_type_id'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_compatible_test_types($lab_config_id, $specimen_type_id)
{
	# Returns a list of compatible tests for a given specimen type in lab configuration
	$saved_db = DbUtil::switchToLabConfigRevamp($lab_config_id);
	$query_string = 
		"SELECT tt.* FROM test_type tt, lab_config_test_type lctt, specimen_test st ".
		"WHERE tt.test_type_id=lctt.test_type_id ".
		"AND lctt.lab_config_id=$lab_config_id ".
		"AND st.specimen_type_id=$specimen_type_id ".
		"AND st.test_type_id=tt.test_type_id ".
		"AND tt.disabled=0 ".
		"ORDER BY tt.name";

	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = TestType::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_lab_config_test_types($lab_config_id, $to_global=false)
{
	## Moved to LabConfig::getTestTypeIds();
	$lab_config = LabConfig::getById($lab_config_id);
	return $lab_config->getTestTypeIds();
}

function get_lab_config_specimen_types($lab_config_id, $to_global=false)
{
	
	# Returns a list of all specimen types added to the lab configuration
	if($to_global == false)
		$saved_db = DbUtil::switchToLabConfigRevamp();
	else
		$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"SELECT specimen_type_id FROM lab_config_specimen_type ".
		"WHERE lab_config_id=$lab_config_id";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = $record['specimen_type_id'];
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_test_type_measures($test_type_id)
{
	# Returns list of measure IDs included in a test type
	# Moved to Measure::getMeasureIds()
	$test_type = TestType::getById($test_type_id);
	if($test_type != null)
	{
		return $test_type->getMeasureIds();
	}
	else
	{
		$dummy_list = array();
		return $dummy_list;
	}	
}

function get_measure_range($measure_id)
{
	$saved_db = DbUtil::switchToLabConfigRevamp();
	# Returns range specified for the measure
	$query_string =
		"SELECT range FROM measure WHERE measure_id=$measure_id LIMIT 1";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return $record['range'];
}


#
# Functions for handling custom fields
#

function add_custom_field_specimen($custom_field, $lab_config_id=null)
{
	# Adds a new specimen custom field to lab configuration
	if($lab_config_id != null)
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	$query_string = 
		"INSERT INTO specimen_custom_field (field_name, field_options, field_type_id) ".
		"VALUES ('$custom_field->fieldName', '$custom_field->fieldOptions', $custom_field->fieldTypeId)";
	query_blind($query_string);
	if($lab_config_id != null)
		DbUtil::switchRestore($saved_db);
}

function add_custom_field_patient($custom_field, $lab_config_id=null)
{
	# Adds a new patient custom field to lab configuration
	if($lab_config_id != null)
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	$query_string = 
		"INSERT INTO patient_custom_field (field_name, field_options, field_type_id) ".
		"VALUES ('$custom_field->fieldName', '$custom_field->fieldOptions', $custom_field->fieldTypeId)";
	query_blind($query_string);
	if($lab_config_id != null)
		DbUtil::switchRestore($saved_db);
}

function add_custom_field_labtitle($custom_field, $lab_config_id=null)
{
	# Adds a new lab title custom field to lab configuration
	if($lab_config_id != null)
		$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	$query_string = 
		"INSERT INTO labtitle_custom_field (field_name, field_options, field_type_id) ".
		"VALUES ('$custom_field->fieldName', '$custom_field->fieldOptions', $custom_field->fieldTypeId)";
	query_blind($query_string);
	if($lab_config_id != null)
		DbUtil::switchRestore($saved_db);
}

function get_custom_fields()
{
	# Returns a list of all patient custom fields
	$query_string =
		"SELECT DISTINCT doctor FROM specimen WHERE doctor!=''";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		
		$retval[] = $record;
	}
	
	return $retval;
}
function get_custom_fields_specimen()
{
	# Returns a list of all specimen custom fields
	$query_string =
		"SELECT * FROM specimen_custom_field";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$custom_field = CustomField::getObject($record);
		$retval[] = $custom_field;
	}
	return $retval;
}

function get_custom_fields_patient()
{
	# Returns a list of all patient custom fields
	$query_string =
		"SELECT * FROM patient_custom_field";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$custom_field = CustomField::getObject($record);
		$retval[] = $custom_field;
	}
	return $retval;
}

function get_custom_fields_labtitle($field_id)
{
	# Returns a list of all patient custom fields
	$query_string =
		"SELECT field_options FROM labtitle_custom_field where id = $field_id LIMIT 1";
	$record = query_associative_one($query_string);
	return $record['field_options'];
}

function get_custom_field_name_specimen($field_id)
{
	# Returns name of the specimen custom field
	$query_string = 
		"SELECT field_name FROM specimen_custom_field ".
		"WHERE id=$field_id LIMIT 1";
	$record = query_associative_one($query_string);
	return $record['field_name'];
}

function get_custom_field_name_patient($field_id)
{
	# Returns name of the patient custom field
	$query_string = 
		"SELECT field_name FROM patient_custom_field ".
		"WHERE id=$field_id LIMIT 1";
	$record = query_associative_one($query_string);
	return $record['field_name'];
}

function get_custom_field_name_labtitle($field_id)
{
	# Returns name of the specimen custom field
	$query_string = 
		"SELECT field_name FROM labtitle_custom_field ".
		"WHERE id=$field_id LIMIT 1";
	$record = query_associative_one($query_string);
	return $record['field_name'];
}

function add_custom_data_specimen($custom_data)
{
	# Adds the custom field value to specimen_custom_data table
	$query_string = 
		"INSERT INTO specimen_custom_data (field_id, specimen_id, field_value) ".
		"VALUES ($custom_data->fieldId, $custom_data->specimenId, '$custom_data->fieldValue')";
	query_blind($query_string);
}

function add_custom_data_patient($custom_data)
{
	# Adds the custom field value to patient_custom_data table
	$query_string = 
		"INSERT INTO patient_custom_data (field_id, patient_id, field_value) ".
		"VALUES ($custom_data->fieldId, $custom_data->patientId, '$custom_data->fieldValue')";
	query_blind($query_string);
}


function update_custom_data_patient($custom_data)
{
	# Updates custom field value in patient_custom_data table
	## Check if entry already exists
	$query_string = 
		"SELECT * FROM patient_custom_data ".
		"WHERE field_id=$custom_data->fieldId ".
		"AND patient_id=$custom_data->patientId LIMIT 1";
	$record = query_associative_one($query_string);
	if($record == null)
	{
		## Add a new entry in patient_custom_data table
		$query_string_new = 
			"INSERT INTO patient_custom_data (field_id, patient_id, field_value) ".
			"VALUES ($custom_data->fieldId, $custom_data->patientId, '$custom_data->fieldValue')";
		query_blind($query_string_new);
	}
	else
	{
		## Record already exists, hence update field_value alone
		$query_string_update = 
			"UPDATE patient_custom_data ".
			"SET field_value='$custom_data->fieldValue' ".
			"WHERE patient_id=$custom_data->patientId ".
			"AND field_id=$custom_data->fieldId";
		query_blind($query_string_update);
	}
}

function get_custom_data_specimen($specimen_id)
{
	# Fetches custom data stored for a given specimen ID
	$query_string = 
		"SELECT * FROM specimen_custom_data ".
		"WHERE specimen_id=$specimen_id";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = SpecimenCustomData::getObject($record);
	}
	return $retval;
}

function get_custom_data_specimen_bytype($specimen_id, $field_id)
{
	$query_string = 
		"SELECT * FROM specimen_custom_data ".
		"WHERE specimen_id=$specimen_id AND field_id=$field_id LIMIT 1";
	$record = query_associative_one($query_string);
	$retval = null;
	if($record != null)
		$retval = SpecimenCustomData::getObject($record);
	return $retval;
}

function get_custom_data_patient($patient_id)
{
	# Fetches custom data stored for a given patient ID
	$query_string = 
		"SELECT * FROM patient_custom_data ".
		"WHERE patient_id=$patient_id";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = PatientCustomData::getObject($record);
	}
	return $retval;
}

function get_custom_data_patient_bytype($patient_id, $field_id)
{
	$query_string = 
		"SELECT * FROM patient_custom_data ".
		"WHERE patient_id=$patient_id AND field_id=$field_id LIMIT 1";
	$record = query_associative_one($query_string);
	$retval = null;
	if($record != null)
		$retval = PatientCustomData::getObject($record);
	return $retval;
}

function get_lab_config_specimen_custom_fields($lab_config_id)
{
	# Returns list of specimen custom fields for a lab configuration
	$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	$query_string = 
		"SELECT * FROM specimen_custom_field";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = CustomField::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_lab_config_patient_custom_fields($lab_config_id)
{
	# Returns list of patient custom fields for a lab configuration
	$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	$query_string = 
		"SELECT * FROM patient_custom_field";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = CustomField::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function get_lab_config_labtitle_custom_fields($lab_config_id)
{
	# Returns list of patient custom fields for a lab configuration
	$saved_db = DbUtil::switchToLabConfig($lab_config_id);
	$query_string = 
		"SELECT * FROM labtitle_custom_field";
	$resultset = query_associative_all($query_string, $row_count);
	$retval = array();
	foreach($resultset as $record)
	{
		$retval[] = CustomField::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}


#
# Functions for transaction support
# (Works in InnoDB engine and not in MyISAM)
#

function begin_transaction()
{
	query_blind("BEGIN");
}

function commit_transaction()
{
	query_blind("COMMIT");
}

function rollback_transaction()
{
	query_blind("ROLLBACK");
}


#
# Functions for miscellaneous tasks
#

function add_new_comment($username, $page, $comment)
{
	# Adds a copy of user comment to DB
	$query_string =
		"INSERT INTO comment (username, page, comment) ".
		"VALUES ('$username', '$page', '$comment')";
	$saved_db = DbUtil::switchToGlobal();
	query_blind($query_string);
	DbUtil::switchRestore($saved_db);
}

function getBackupFolders($user_id) {
	$labConfigList = get_lab_configs($user_id);
	foreach($labConfigList as $labConfig) {
		$labConfigId = $labConfig->id;
		$folderName = getLatestBackupFolder($labConfigId);
		if( $folderName != "notFound" )
			$retval[$labConfigId] = $folderName;
	}
	return $retval;
}

function getLatestBackupFolder($labConfigId) {
	$folderList = get_backup_folders($labConfigId);
	if( count($folderList) > 0 ) {
		end($folderList);
		$key = key($folderList);
		return $key;
	}
	else
		return "notFound";
}
function get_backup_folders($lab_config_id)
{
	# Returns a list of all backup folders available on main dir
	$retval = array();
	$start_dir = "../../";
	if($handle = opendir($start_dir)) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			if(strpos($file, "blis_backup_") !== false)
			{
				if(is_file($file))
				{
					# Not a folder
					continue;
				}
				# This is a data backup folder
				$lab_config_match = false;
				if($handle2 = opendir($start_dir.$file))
				{
					# Check if this folder has backup for the given lab_config_id
					while (false !== ($file2 = readdir($handle2)))
					{
						if($file2 == "blis_".$lab_config_id)
						{
							$lab_config_match = true;
							break;
						}
					}					
				}
				if($lab_config_match === false)
					continue;
				# $lab_config_id matched. Add this folder option
				$name_parts = explode("_", $file);
				$timestamp_index = 3;
				$timestamp_parts = explode("-", $name_parts[$timestamp_index]);
				$year = substr($timestamp_parts[0], 0, 4);
				$month = substr($timestamp_parts[0], 4, 2);
				$day = substr($timestamp_parts[0], 6, 2);
				$hour = substr($timestamp_parts[1], 0, 2);
				$min = substr($timestamp_parts[1], 2, 2);
				$date = $year."-".$month."-".$day;
				$option_name = DateLib::mysqlToString($date)." ".$hour.":".$min;
				$option_value = $file;
				$retval[$option_value] = $option_name;
			}
		}
	}
	closedir($handle);
	return $retval;
}

class TestTypeMapping {

	public $testId;
	public $name;
	public $userId;
	public $labIdTestId;
	public $testCategoryId;
	
	public static function getObject($record)
	{
		# Converts a test type mapping record in DB into a TestTypeMapping object
		if($record == null)
			return null;
		$testTypeMapping = new TestTypeMapping();
		$testTypeMapping->testId = $record['test_id'];
		$testTypeMapping->name = $record['test_name'];
		$testTypeMapping->userId = $record['user_id'];
		$testTypeMapping->labIdTestId = $record['lab_id_test_id'];
		$testTypeMapping->testCategoryId = $record['test_category_id'];
		return $testTypeMapping;
	}
	
	public function getName()
	{
		global $CATALOG_TRANSLATION;
		if($CATALOG_TRANSLATION === true)
		{
			return LangUtil::getTestName($this->testId);
		}
		else
		{
			return $this->name;
		}
	}
	
	public function getDescription()
	{
		if(trim($this->description) == "" || $this->description == null)
			return "-";
		else
			return trim($this->description);
	}
	
	public function getClinicalData()
	{
		if(trim($this->clinical_data) == "" || $this->clinical_data == null)
			return "-";
		else
			return trim($this->clinical_data);
	}
	
	public static function getByCategory($catCode)
	{
		# Returns all test types belonging to a particular category (aka section)
		if($catCode == null || $catCode == "")
			return null;
		$retval = array();
		$query_string = 
			"SELECT * FROM test_mapping ".
			"WHERE test_category_id=$catCode";
		$saved_db = DbUtil::switchToGlobal();
		$resultset = query_associative_all($query_string, $row_count);
		foreach($resultset as $record)
			$retval[] = TestTypeMapping::getObject($record);
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public static function getById($testTypeId) {
		# Returns global test type record in DB
		$saved_db = DbUtil::switchToGlobal();
		$query_string =
			"SELECT * FROM test_mapping WHERE test_id=$testTypeId LIMIT 1";
		$record = query_associative_one($query_string);
		return TestTypeMapping::getObject($record);
	}
	
	public function getMeasures()
	{
		# Returns list of measures included in a test type
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"SELECT measure_id FROM global_measures ".
			"WHERE test_id=$this->testId";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
		{
			$measure_obj = GlobalMeasure::getById($record['measure_id']);
			$retval[] = $measure_obj;
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public function getMeasureIds()
	{
		# Returns list of measure IDs included in a test type
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"SELECT measure_id FROM global_measures ".
			"WHERE test_id=$this->testId";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record) {
			$retval[] = $record['measure_id'];
		}
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	/*
	public static function deleteById($test_type_id)
	{
		# Deletes test type from database
		# 1. Delete entries in lab_config_test_type
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"DELETE FROM lab_config_test_type WHERE test_type_id=$test_type_id";
		query_blind($query_string);
		# 2. Delete entries from specimen_test
		$query_string =
			"DELETE FROM specimen_test WHERE test_type_id=$test_type_id";
		query_blind($query_string);
		# 3. Set disabled flag in test_type entry
		$query_string =
			"UPDATE test_type SET disabled=1 WHERE test_type_id=$test_type_id";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	*/
}

class GlobalMeasure
{
	# For each test indicator in 'measure' table
	
	public $measureId;
	public $name;
	public $unit;
	public $description;
	public $range;
	public $testId;
	
	public static $RANGE_ERROR = 0;
	public static $RANGE_OPTIONS = 1;
	public static $RANGE_NUMERIC = 2;
	public static $RANGE_MULTI = 3;
	public static $RANGE_AUTOCOMPLETE = 4;
	
	public static function getObject($record) {
		# Converts a measure record in DB into a Measure object
		if($record == null)
			return null;
		$measure = new GlobalMeasure();
		$measure->measureId = $record['measure_id'];
		$measure->name = $record['name'];
		$measure->unit = $record['unit'];
		//$measure->description = $record['description'];
		$measure->userId = $record['user_id'];
		$measure->testId = $record['test_id'];
		$measure->range = $record['range'];
		return $measure;
	}
	
	public function getName() {
		global $CATALOG_TRANSLATION;
		if($CATALOG_TRANSLATION === true) {
			return LangUtil::getMeasureName($this->measureId);
		}
		else {
			return $this->name;
		}
	}
	
	public function getRangeType()
	{
		if(strpos($this->range, "_") !== false)
		{
			return GlobalMeasure::$RANGE_AUTOCOMPLETE;
		}
		else if(strpos($this->range, ":") !== false)
		{
			return GlobalMeasure::$RANGE_NUMERIC;
		}
		else if(strpos($this->range, "*") !== false)
		{
			return GlobalMeasure::$RANGE_MULTI;
		}	
		else if(strpos($this->range, "/") !== false)
		{
			return GlobalMeasure::$RANGE_OPTIONS;
		}	
		else 
		{
			return GlobalMeasure::$RANGE_ERROR;
		}
	}
	
	/*
	public function getRangeValues($patient=null)
	{
		# Returns range values in a list
		
		$range_type = $this->getRangeType();
		$retval = array();
		switch($range_type)
		{
			case Measure::$RANGE_NUMERIC:
				# check if ref range is already configured
				$ref_range = null;
				if($patient != null)
				{	$ref_range = ReferenceRange::getByAgeAndSex($patient->getAgeNumber(), $patient->sex, $this->measureId, $_SESSION['lab_config_id']);
				
				}
				if($ref_range == null)
					# Fetch from default entry in 'measure' table
					$retval = explode(":", $this->range);
				else
					$retval = array($ref_range->rangeLower, $ref_range->rangeUpper);
				break;
			case Measure::$RANGE_OPTIONS:
			
			{
			$retval = explode("/", $this->range);
				
				foreach($retval as $key=>$value)
				{
				
				$retval[$key]=str_replace("#","/",$value);
				}
			break;
			}
			case Measure::$RANGE_AUTOCOMPLETE:
				$retval = explode("_", $this->range);
				foreach($retval as $key=>$value)
				{
				$retval[$key]=str_replace("#","_",$value);
				}
				break;
		}
		return $retval;
	}
	
	public function getRangeString($patient=null)
	{
		# Returns range in string for printing or displaying
		$retval = "";
		if
		(
			$this->getRangeType() == Measure::$RANGE_OPTIONS ||
			$this->getRangeType() == Measure::$RANGE_MULTI ||
			$this->getRangeType() == Measure::$RANGE_AUTOCOMPLETE
		)
		{
			$range_parts = explode("/", $this->range);
			# TODO: Display possible options for result indicator??
			$retval .= "-";
		}
		else if($this->getRangeType() == Measure::$RANGE_NUMERIC)
		{
			$ref_range = null;
			if($patient != null)
				$ref_range = ReferenceRange::getByAgeAndSex($patient->getAgeNumber(), $patient->sex, $this->measureId, $_SESSION['lab_config_id']);
			if($ref_range == null)
				# Fetch from default entry in 'measure' table
				$range_parts = explode(":", $this->range);
			else
				$range_parts = array($ref_range->rangeLower, $ref_range->rangeUpper);
			$retval .= "(".$range_parts[0]."-".$range_parts[1];
			if($this->range != null && trim($this->range) != "")
				$retval .= "  ".$this->unit;
			$retval .= ")";
		}
		
		return $range_parts;
	}
	
	public function getUnits()
	{
		return $this->unit;
	}
	*/
	
	public static function getById($measure_id)
	{
		# Returns a test measure by ID
		$saved_db = DbUtil::switchToLabConfigRevamp();
		if($measure_id == null || $measure_id < 0)
			return null;
		$query_string = "SELECT * FROM global_measures WHERE measure_id=$measure_id LIMIT 1";
		$record = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		return GlobalMeasure::getObject($record);		
	}
	
	public function updateToDb()
	{
		# Updates an existing global measure entry in DB
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"UPDATE global_measures SET name='$this->name', range='$this->range', unit='$this->unit' ".
			"WHERE measure_id=$this->measureId";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	/*
	public function setInterpretation($inter)
	{
		# Updates an existing measure entry in DB
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"UPDATE measure SET description='$inter'".
			"WHERE measure_id=$this->measureId";
		query_blind($query_string);
		DbUtil::switchRestore($saved_db);
	}
	public function setNumericInterpretation($remarks_list,$id_list, $range_l_list, $range_u_list, $age_u_list, $age_l_list, $gender_list)
	{
		# Updates an existing measure entry in DB
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$count = 0;
		if($id_list[0]==-1)
		{
		foreach($range_l_list as $range_value)
				{
			//insert query
			$query_string="INSERT INTO NUMERIC_INTERPRETATION (range_u, range_l, age_u, age_l, gender, description, measure_id) ".
			"VALUES($range_u_list[$count],$range_l_list[$count],$age_u_list[$count],$age_l_list[$count],'$gender_list[$count]','$remarks_list[$count]',$this->measureId)";
			query_insert_one($query_string);
			$count++;
				}
		}
		else
		{
		foreach($range_l_list as $range_value)
			{
				if($id_list[$count]!=-2)
					{
						if($remarks_list[$count]=="")
							{
						//delete
						$query_string="DELETE FROM NUMERIC_INTERPRETATION WHERE id=$id_list[$count]";
						query_delete($query_string);
						}else
							{
							//update
						$query_string = 
						"UPDATE numeric_interpretation SET range_u=$range_u_list[$count], range_l=$range_l_list[$count], age_u=$age_u_list[$count], age_l=$age_l_list[$count], gender='$gender_list[$count]' , description='$remarks_list[$count]' ".
						"WHERE id=$id_list[$count]";
						query_update($query_string);
						
						}
				}else
					{
					$query_string="INSERT INTO numeric_interpretation (range_u, range_l, age_u, age_l, gender, description, measure_id) ".
			"VALUES($range_u_list[$count],$range_l_list[$count],$age_u_list[$count],$age_l_list[$count],'$gender_list[$count]','$remarks_list[$count]',$this->measureId)";
			query_insert_one($query_string);
				}
		
		$count++;
		}
	}
	DbUtil::switchRestore($saved_db);
	}
	
	public function getNumericInterpretation()
	{
	$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = "SELECT * FROM numeric_interpretation WHERE measure_id=$this->measureId";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		if($resultset!=NULL)
			{
			foreach($resultset as $record)
			{
				$range_u=$record['range_u'];
				$range_l=$record['range_l'];
				$age_u=$record['age_u'];
				$age_l=$record['age_l'];
				$gender=$record['gender'];
				$id=$record['id'];
				$description=$record['description'];
				$measure_id=$record['measure_id'];
				$retval[] =array($range_l,$range_u,$age_l,$age_u,$gender,$description,$id,$measure_id);
			}
			
		}else
			{
		//get interpretation ka loop
			}
	DbUtil::switchRestore($saved_db);
	return $retval;
	}
	
	public function addToDb()
	{
		# Updates an existing measure entry in DB
		$saved_db = DbUtil::switchToLabConfigRevamp();
		$query_string = 
			"INSERT INTO measure (name, range, unit) ".
			"VALUES ('$this->name', '$this->range', '$this->unit')".
		query_insert_one($query_string);
		DbUtil::switchRestore($saved_db);
	}
	*/
	
	public function getReferenceRanges($user_id)
	{
		# Fetches reference ranges from database for this measure
		$saved_db = DbUtil::switchToGlobal();
		$query_string = "SELECT * FROM reference_range_global WHERE measure_id=$this->measureId AND user_id=$user_id ORDER BY sex DESC";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		if ($resultset!=NULL)
		{
			foreach($resultset as $record)
			{
				$retval[] = ReferenceRangeGlobal::getObject($record);
			}
		}	
			DbUtil::switchRestore($saved_db);
			return $retval;
	}
	
	/*
	public function getInterpretation()
	{	
		$retval= array();
		$numeric_description=array();
		if(trim($this->description) == "" || $this->description == null)
			return $retval;
		else 
		{
		$description=substr(($this->description),2);
		if(strpos($description,"##")===false)
		$retval=explode("//" , $description);
		else
		$retval=explode("##",$description);
		}
		
		return $retval;
	}
	
	public function getDescription()
	{
		if(trim($this->description) == "" || $this->description == null)
			return "-";
		else
			return trim($this->description);
	} */
}

class ReferenceRangeGlobal
{
	public $id;
	public $measureId;
	public $ageMin;
	public $ageMax;
	public $sex;
	public $rangeLower;
	public $rangeUpper;
	public $userId;
	
	public static function getObject($record)
	{
		if($record == null)
			return null;
		$reference_range = new ReferenceRangeGlobal();
		if(isset($record['id']))
			$reference_range->id = $record['id'];
		else
			$reference_range->id = null;
		if(isset($record['measure_id']))
			$reference_range->measureId = $record['measure_id'];
		else
			$reference_range->measureId = null;
		if(isset($record['age_min']))
			$reference_range->ageMin = intval($record['age_min']);
		else
			$reference_range->ageMin = null;
		if(isset($record['age_max']))
			$reference_range->ageMax = intval($record['age_max']);
		else
			$reference_range->ageMax = null;
		if(isset($record['sex']))
			$reference_range->sex = $record['sex'];
		else
			$reference_range->sex = null;
		if(isset($record['range_lower']))
			$reference_range->rangeLower = $record['range_lower'];
		else
			$reference_range->rangeLower = null;
		if(isset($record['range_upper']))
			$reference_range->rangeUpper = $record['range_upper'];
		else
			$reference_range->rangeUpper = null;
		return $reference_range;
	}
	
	public function addToDb($user_id)
	{
		# Adds this entry to database
		$saved_db = DbUtil::switchToGlobal();
		$query_string = 
			"INSERT INTO reference_range_global (measure_id, age_min, age_max, sex, range_lower, range_upper, user_id) ".
			"VALUES ($this->measureId, '$this->ageMin', '$this->ageMax', '$this->sex', '$this->rangeLower', '$this->rangeUpper', $user_id)";
		echo $query_string;
		query_insert_one($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public static function deleteByMeasureId($measure_id)
	{
		# Deletes all entries for the given measure
		# Used when deleting the measure from catalof
		# Or when resetting ranges (from test_type_edit.php)
		$saved_db = DbUtil::switchToGlobal();
		$query_string = "DELETE FROM reference_range_global WHERE measure_id=$measure_id";
		query_delete($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public static function getByAgeAndSex($age, $sex, $measure_id, $user_id)
	{
		# Fetches the reference range based on supplied age and sex values
		$saved_db = DbUtil::switchToGlobal();
		$query_string = "SELECT * FROM reference_range_global WHERE measure_id=$measure_id AND user_id=$user_id";
		$retval = null;
		$resultset = query_associative_all($query_string, $row_count);
		if($resultset == null || count($resultset) == 0)
			return $retval;
		foreach($resultset as $record)
		{
			$ref_range = ReferenceRangeGlobal::getObject($record);
			if($ref_range->ageMin == 0 && $ref_range->ageMax == 0)
			{
				# No agewise split
				if($ref_range->sex == "B" || strtolower($ref_range->sex) == strtolower($sex))
				{
					return $ref_range;
				}
			}
			else if($ref_range->ageMin <= $age && $ref_range->ageMax >= $age)
			{
				# Age wise split exists
				if($ref_range->sex == "B" || strtolower($ref_range->sex) == strtolower($sex))
				{
					return $ref_range;
				}
			}
		}
		DbUtil::switchRestore($saved_db);
	}
}

class GlobalInfectionReport
{
	public $id;
	public $testId;
	public $measureId;
	public $groupByGender;
	public $groupByAge;
	public $ageGroups;
	public $measureGroups;
	public $userId;
	
	public static function getObject($record)
	{
		if($record == null)
			return null;
		$retval = new GlobalInfectionReport();
		$retval->userId = $record['user_id'];
		$retval->testId = $record['test_id'];
		$retval->measureId = $record['measure_id'];
		$retval->groupByGender = $record['group_by_gender'];
		$retval->groupByAge = $record['group_by_age'];
		if(isset($record['age_groups']))
			$retval->ageGroups = $record['age_groups'];
		if(isset($record['measure_groups']))
			$retval->measureGroups = $record['measure_groups'];
		return $retval;
	}
	
	public function addToDb()
	{
		$infectionReportSettings = $this;
		$saved_db = DbUtil::switchToGlobal();
		# Remove existing entry
		$query_string =
			"DELETE FROM infection_report_settings ".
			"WHERE user_id=$this->userId ".
			"AND test_id=$this->testId ".
			"AND measure_id=$this->measureId";
		query_blind($query_string);
		# Add updated entry
		$query_string = 
			"INSERT INTO infection_report_settings( ".
				"id, ".
				"user_id, ".
				"test_id, ".
				"measure_id, ".
				"group_by_gender, ".
				"group_by_age, ".
				"age_groups, ".
				"measure_groups ".
			") ".
			"VALUES ( ".
				"$infectionReportSettings->id, ".
				"$infectionReportSettings->userId, ".
				"$infectionReportSettings->testId, ".
				"$infectionReportSettings->measureId, ".
				"$infectionReportSettings->groupByGender, ".
				"$infectionReportSettings->groupByAge, ".
				"'$infectionReportSettings->ageGroups', ".
				"'$infectionReportSettings->measureGroups' ".
			")";
		query_insert_one($query_string);
		DbUtil::switchRestore($saved_db);
	}
	
	public static function getByKeys($user_id, $test_type_id, $measure_id)
	{
		# Fetches a record by compound key
		$saved_db = DbUtil::switchToGlobal();
		$query_string =
			"SELECT * FROM infection_report_settings ".
			"WHERE user_id=$user_id ".
			"AND test_id=$test_type_id ".
			"AND measure_id=$measure_id LIMIT 1";
		$record = query_associative_one($query_string);
		$retval = GlobalInfectionReport::getObject($record);
		DbUtil::switchRestore($saved_db);
		return $retval;
	}
	
	public function getAgeGroupAsList()
	{
		# Returns the age_group field as a PHP list
		$age_parts = explode(",", $this->ageGroups);
		$retval = array();
		foreach($age_parts as $age_part)
		{
			if(trim($age_part) == "")
				continue;
			$age_bounds = explode(":", $age_part);
			$retval[] = $age_bounds;
		}
		return $retval;
	}
	
	public function getMeasureGroupAsList()
	{
		# Returns the measure_group field as a PHP list
		$measure_parts = explode(",", $this->measureGroups);
		$retval = array();
		foreach($measure_parts as $measure_part)
		{
			if(trim($measure_part) == "")
				continue;
			$measure_bounds = explode(":", $measure_part);
			$retval[] = $measure_bounds;
		}
		return $retval;
	}
}

class GlobalPatient
{
	public $patientId; 
	public $addlId;
	public $name;
	public $dob;
	public $partialDob;
	public $age;
	public $sex;
	public $surrogateId; # surrogate key (user facing)
	public $createdBy; # user ID who registered this patient
	public $hashValue; # hash value for this patient (based on name, dob, sex)
	public $regDate;
	public static function getObject($record)
	{
		# Converts a patient record in DB into a Patient object
		if($record == null)
			return null;
		$patient = new Patient();
		$patient->patientId = $record['patient_id'];
		$patient->addlId = $record['addl_id'];
		$patient->name = $record['name'];
		$patient->dob = $record['dob'];
		$patient->age = $record['age'];
		$patient->sex = $record['sex'];
		$date_parts = explode(" ", date($record['ts']));
		$date_parts_1=explode("-",$date_parts[0]);
		$patient->regDate=$date_parts_1[2]."-".$date_parts_1[1]."-".$date_parts_1[0];
		
		if(isset($record['partial_dob']))
			$patient->partialDob = $record['partial_dob'];
		else
			$patient->partialDob = null;
		if(isset($record['surr_id']))
			$patient->surrogateId = $record['surr_id'];
		else
			$patient->surrogateId = null;
		if(isset($record['created_by']))
			$patient->createdBy = $record['created_by'];
		else
			$patient->createdBy = null;
		if(isset($record['hash_value']))
			$patient->hashValue = $record['hash_value'];
		else
			$patient->hashValue = null;
		return $patient;
	}
	
	public static function checkNameExists($name, $country)
	{
		# Checks if the given patient name (or similar match) already exists
		$saved_db = DbUtil::switchToCountry($country);
		$query_string = 
			"SELECT COUNT(patient_id) AS val FROM patient WHERE name LIKE '%$name%'";
		$resultset = query_associative_one($query_string);
		DbUtil::switchRestore($saved_db);
		if($resultset == null || $resultset['val'] == 0)
			return false;
		else
			return true;
	}
	
	public function getName()
	{
		if(trim($this->name) == "")
			return " - ";
		else
			return $this->name;
	}
	
	public function getAddlId()
	{
		if($this->addlId == "")
			return " - ";
		else
			return $this->addlId;
	}
	
	public function getAssociatedTests() {
		if( $this->patientId == "" )
			return " - ";
		else {
			$query_string = "SELECT t.test_type_id FROM test t, specimen sp ".
							"WHERE t.result <> '' ".
							"AND t.specimen_id=sp.specimen_id ".
							"AND sp.patient_id=$this->patientId";
			$recordset = query_associative_all($query_string, $row_count);
			foreach( $recordset as $record ) {
				$testName = get_test_name_by_id($record['test_type_id']);
				$result .= $testName."<br>";
			}
			return $result;
		}
	}
	
	public function getAge()
	{
		# Returns patient age value
		if($this->partialDob == "" || $this->partialDob == null)
		{
			if($this->dob != null && $this->dob != "")
			{
				# DoB present in patient record
				return DateLib::dobToAge($this->dob);
			}
			else 
			{	$age_value=-1*$this->age;
				if($age_value>100){
					$age_value=200-$age_value;
					$age_value=">".$age_value;
					}
				else
					{
					$diff=$age_value%10;
					$age_range1=$age_value-$diff;
					$age_range2=$age_range1+10;
					$age_value=$age_range1."-".$age_range2;
					}
					if($this->age < 0)
				$this->age=$age_value;
				return $this->age." ".LangUtil::$generalTerms['YEARS'];
			}
		}
		else
		{
			# Calculate age from partial DoB
			$aprrox_dob = "";
			if(strpos($this->partialDob, "-") === false)
			{
				# Year-only specified
				$approx_dob = trim($this->partialDob)."-01-01";
			}
			else
			{
				# Year and month specified
				$approx_dob = trim($this->partialDob)."-01";
			}
			return DateLib::dobToAge($approx_dob);
		}
	}
	
	public function getAgeNumber()
	{
		# Returns patient age value (numeric part alone)
		if($this->partialDob == "" || $this->partialDob == null)
		{
			if($this->dob != null && $this->dob != "")
			{
				# DoB present in patient record
				return DateLib::dobToAgeNumber($this->dob);
			}
			else
			{	if($this->age<100)
					$this->age=200+$this->age;
				else if($this->age<0)
					$this->age=-1*$this->age;
			
				return $this->age;
			}
		}
		else
		{
			# Calculate age from partial DoB
			$aprrox_dob = "";
			if(strpos($this->partialDob, "-") === false)
			{
				# Year-only specified
				$approx_dob = trim($this->partialDob)."-01-01";
			}
			else
			{
				# Year and month specified
				$approx_dob = trim($this->partialDob)."-01";
			}
			return DateLib::dobToAgeNumber($approx_dob);
		}
	}
	
	public function getDob()
	{
		# Returns patient dob value
		if($this->partialDob != null && $this->partialDob != "")
		{
			return $this->partialDob." (".LangUtil::$generalTerms['APPROX'].")";
		}
		else if($this->dob == null || trim($this->dob) == "")
		{
			return " - ";
		}
		else
		{
			return DateLib::mysqlToString($this->dob);
		}
	}
	
	public static function getByAddDate($date)
	{
		# Returns all patient records added on that date
		$query_string = 
			"SELECT * FROM patient ".
			"WHERE ts LIKE '%$date%' ORDER BY patient_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
			$retval[] = Patient::getObject($record);
		return $retval;
	}
	
	public static function getByAddDateRange($date_from, $date_to)
	{
		# Returns all patient records added on that date range
		$query_string = 
			"SELECT * FROM patient ".
			"WHERE UNIX_TIMESTAMP(ts) >= UNIX_TIMESTAMP('$date_from 00:00:00') ".
			"AND UNIX_TIMESTAMP(ts) <= UNIX_TIMESTAMP('$date_to 23:59:59') ".
			"ORDER BY patient_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		foreach($resultset as $record)
			$retval[] = Patient::getObject($record);
		return $retval;
	}
	
	public static function getByRegDateRange($date_from , $date_to)
	{
	$query_string =
			"SELECT DISTINCT patient_id FROM specimen ".
			"WHERE date_collected BETWEEN '$date_from' AND '$date_to'";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		$record_p=array();
			foreach($resultset as $record)
			{
				foreach($record as $key=>$value)
				$query_string = "SELECT * FROM patient WHERE patient_id=$value";
				$record_each= query_associative_one($query_string);
				$record_p[]=Patient::getObject($record_each);
			}
		return $record_p;	
	
	}

	public static function getReportedByRegDateRange($date_from , $date_to)
	{
		$emp="";
		$query_string =
				"SELECT DISTINCT patient_id FROM specimen , test ".
				"WHERE date_collected BETWEEN '$date_from' AND '$date_to' ".
				"AND result!='$emp' ".
				"AND specimen.specimen_id=test.specimen_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		$record_p=array();
		$count = 0;
		foreach($resultset as $record)
		{
			foreach($record as $key=>$value) {
				$query_string = "SELECT * FROM patient WHERE patient_id=$value";
				$record_each= query_associative_one($query_string);
				$record_p[]=Patient::getObject($record_each);
			}
		}
		return $record_p;	
	
	}
	

	public static function getUnReportedByRegDateRange($date_from , $date_to)
	{
		$emp="";
		$query_string =
			"SELECT DISTINCT patient_id FROM specimen , test ".
			"WHERE date_collected BETWEEN '$date_from' AND '$date_to' ".
			"AND result='$emp' ".
			"AND specimen.specimen_id=test.specimen_id";
		$resultset = query_associative_all($query_string, $row_count);
		$retval = array();
		$record_p=array();
		foreach($resultset as $record) {
			foreach($record as $key=>$value)
				$query_string = "SELECT * FROM patient WHERE patient_id=$value";
			$record_each= query_associative_one($query_string);
			$record_p[]=Patient::getObject($record_each);
		}
		return $record_p;
	}

	
	public static function getById($patient_id)
	{
		# Returns patient record by ID
		$query_string = "SELECT * FROM patient WHERE patient_id=$patient_id";
		$record = query_associative_one($query_string);
		//return 1;
		return Patient::getObject($record);
	}
	
	public function getSurrogateId()
	{
		if($this->surrogateId == null || trim($this->surrogateId) == "")
			return "-";
		else
			return $this->surrogateId;
	}
	
	public function getDailyNum()
	{
		# Returns daily number ("patient number")
		# Fetches value from the latest specimen which was assigned to this patient
		$query_string =
			"SELECT s.daily_num FROM specimen s, patient p ".
			"WHERE s.patient_id=p.patient_id ".
			"AND p.patient_id=$this->patientId ".
			"ORDER BY s.date_collected DESC";
		$record = query_associative_one($query_string);		
		$retval = "";
		if($record == null || trim($record['daily_num']) == "")
			$retval = "-";
		else
			$retval = $record['daily_num'];
		return $retval;
	}

	public function generateHashValue()
	{
		# Generates hash value for this patient (based on name, age and date of birth)
		$name_part = strtolower(str_replace(" ", "", $this->name));
		$sex_part = strtolower($this->sex);
		$dob_part = "";
		if($this->partialDob != null && trim($this->partialDob) != "")
		{	
			# Determine unix timestamp based on partial (approximate) date of birth
			$approx_dob = "";
			if(strpos($this->partialDob, "-") === false)
			{
				# Year-only specified
				$approx_dob = trim($this->partialDob)."-01-01";
			}
			else
			{
				# Year and month specified
				$approx_dob = trim($this->partialDob)."-01";
			}
			list($year, $month, $day) = explode('-', $approx_dob);
			$dob_part = mktime(0, 0, 0, $month, $day, $year);
		}
		else
		{
			# Determine unix timestamp based on complete data of birth
			$dob = $this->dob;
			list($year, $month, $day) = explode('-', $dob);
			$dob_part = mktime(0, 0, 0, $month, $day, $year);
		}
		$hash_input = $name_part.$dob_part.$sex_part;
		# TODO: Provide choice of hashing schemes
		$retval = sha1($hash_input);
		return $retval;
	}
	
	public function getHashValue()
	{
		$retval = $this->hashValue;
		return $retval;
	}
	
	public function getSex()
	{
	$sex=$this->sex;
	
	return $sex;
	}
	
	public function setHashValue($hash_value)
	{
		if($hash_value == null || trim($hash_value) == "")
			return;
		$query_string = 
			"UPDATE patient SET hash_value='$hash_value' ".
			"WHERE patient_id=$this->patientId";
		query_update($query_string);
	}
}

/** 
	Aggregation Helper Functions 
*/
function addAggregateMeasure($measure, $range, $testId, $userId, $unit)
{
	# Adds a new measure to catalog
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"SELECT MAX(measure_id) AS measure_id FROM global_measures ".
		"WHERE user_id=$userId";
	$record = query_associative_one($query_string);
	$measureId = intval($record['measure_id']) + 1;
	$query_string = 
		"INSERT INTO global_measures (name, range, test_id, user_id, unit, measure_id ) ".
		"VALUES ('$measure', '$range', $testId, $userId, '$unit', $measureId)";
	query_insert_one($query_string);
	DbUtil::switchRestore($saved_db);
	# Return measure id of the record just inserted
	return $measureId;
}

function getAggregateTestTypeById($testTypeId) {
	return TestTypeMapping::getById($testTypeId);
}

function updateAggregateTestType($updated_entry, $userId) {
	# Updates test type mapping info in DB catalog
	$saved_db = DbUtil::switchToGlobal();
	$existing_entry = getAggregateTestTypeById($updated_entry->testTypeId);
	if($existing_entry == null) {
		# No record found
		DbUtil::switchRestore($saved_db);
		return;
	}
	$query_string =
		"UPDATE test_mapping ".
		"SET name='$updated_entry->name' ".
		"WHERE user_id=$userId ";
	query_blind($query_string);
	/*
	# Delete entries for removed compatible specimens
	$existing_list = get_compatible_specimens($updated_entry->testTypeId);
	foreach($existing_list as $specimen_type_id)
	{
		if(in_array($specimen_type_id, $new_specimen_list))
		{
			# Compatible specimen not removed
			# Do nothing
		}
		else
		{
			# Remove entry from mapping table
			$query_del = 
				"DELETE from specimen_test ".
				"WHERE test_type_id=$updated_entry->testTypeId ".
				"AND specimen_type_id=$specimen_type_id";
			query_blind($query_del);
		}
	}
	# Add entries for new compatible specimens
	foreach($new_specimen_list as $specimen_type_id)
	{
		if(in_array($specimen_type_id, $existing_list))
		{
			# Entry already exists
			# Do nothing
		}
		else
		{
			# Add entry in mapping table
			$query_ins = 
				"INSERT INTO specimen_test (specimen_type_id, test_type_id) ".
				"VALUES ($specimen_type_id, $updated_entry->testTypeId)";
			query_blind($query_ins);
		}
	}
	*/
	DbUtil::switchRestore($saved_db);
}

function getAggregateTestTypeByName($testName)
{
	# Returns test type record in DB
	$saved_db = DbUtil::switchToGlobal();
	$test_name = addslashes($test_name);
	$query_string =
		"SELECT * FROM test_mapping WHERE test_name='$testName' LIMIT 1";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	return TestTypeMapping::getObject($record);
}

function getAggregateMeasureById($measureId)
{
	# Returns Measure object from DB
	$saved_db = DbUtil::switchToGlobal();
	$query_measure = 
		"SELECT * FROM global_measures WHERE measure_id=$measureId LIMIT 1";
	$record = query_associative_one($query_measure);
	$retval = GlobalMeasure::getObject($record);
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function getTestTypesCountryLevel() {
	$saved_db = DbUtil::switchToLabConfigRevamp();
	$user_id = $_SESSION['user_id'];
	$retval = array();
	$query = "SELECT * FROM test_mapping where user_id =".$user_id;
	$resultset = query_associative_all($query, $count);
	foreach($resultset as $record) {
		$retval[] = TestTypeMapping::getObject($record);
	}
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function addTestCategoryAgg($cat_name, $cat_descr="")
{
	# Adds a new test category to catalog
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"INSERT INTO test_category(name, description) ".
		"VALUES ('$cat_name', '$cat_descr')";
	query_insert_one($query_string);
	# Return primary key of the record just inserted
	DbUtil::switchRestore($saved_db);
	return get_max_test_cat_id();
}

function updateTestMappingWithCategory($testId, $catCode) {
	$saved_db = DbUtil::switchToGlobal();
	/*
	$query_string = 
		"SELECT test_category_id ".
		"FROM test_category_mapping ".
		"WHERE lab_id_test_category_id='$catCode' ";
	$record = query_associative_one($query_string);
	if($record != null)
		$testCategoryId = $record['test_category_id'];
	*/
	$query_string = 
		"UPDATE test_mapping ".
		"SET test_category_id=$catCode ".
		"WHERE test_id=$testId ";
	query_update($query_string);
	DbUtil::switchRestore($saved_db);
}

function getTestCategoryAggNameById($cat_id)
{
	# Returns test category name as string
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"SELECT test_category_name FROM test_category_mapping ".
		"WHERE test_category_id=$cat_id LIMIT 1";
	$record = query_associative_one($query_string);
	$retval = LangUtil::$generalTerms['NOTKNOWN'];
	if($record != null)
		$retval = $record['test_category_name'];
	DbUtil::switchRestore($saved_db);
	return $retval;
}

function getLabMeasureIdFromGlobalMeasureId($labConfigId, $globalTestType, $currentMeasureCount) {
	$saved_db = DbUtil::switchToGlobal();
	$query_string = 
		"SELECT lab_id_test_id FROM test_mapping ".
		"WHERE test_id=$globalTestType->testId ";
	$record = query_associative_one($query_string);
	$labIdTestIds = explode(";",$record['lab_id_test_id']);
	foreach( $labIdTestIds as $labIdTestId ) {
		$labIdTestIdsSeparated = explode(":",$labIdTestId);
		$labId = $labIdTestIdsSeparated[0];
		$testId = $labIdTestIdsSeparated[1];
		$testIds[$labId] = $testId;
	}
	$testTypeId = $testIds[$labConfigId];
	$svdb = DbUtil::switchToLabConfig($labConfigId);
	$query_string =
		"SELECT * from test_type_measure ".
		"WHERE test_type_id=$testTypeId";
	$resultset = query_associative_all($query_string, $count);
	$measureCount = 0;
	foreach($resultset as $record) {
		if($measureCount == $currentMeasureCount) {
			$measureId = $record['measure_id'];
			DbUtil::switchRestore($saved_db);
			return $measureId;
		}
		++$measureCount;
	}
	DbUtil::switchRestore($saved_db);
}

function getLabMeasureIdFromGlobalName($measureName, $labConfigId) {
	$saved_db = DbUtil::switchToGlobal();
	$userId= $_SESSION['user_id'];
	$query_string = 
		"SELECT lab_id_measure_id FROM measure_mapping ".
		"WHERE measure_name='$measureName' ".
		"AND user_id=$userId LIMIT 1";
	$record = query_associative_one($query_string);
	$measureIds = array();
	$labIdMeasureIds = explode(";",$record['lab_id_measure_id']);
	foreach( $labIdMeasureIds as $labIdMeasureId ) {
		$labIdMeasureIdsSeparated = explode(":",$labIdMeasureId);
		$labId = $labIdMeasureIdsSeparated[0];
		$measureId = $labIdMeasureIdsSeparated[1];
		$measureIds[$labId] = $measureId;
	}
	DbUtil::switchRestore($saved_db);
	return $measureIds[$labConfigId];
}

/** 
	DB Merging Helper Functions Begin Here
*/
function searchPatientByName($q) {
	$country = $_SESSION['country'];
	$saved_db = DbUtil::switchToCountry($country);
	# Searches for patients with exact name in the global database
	$query_string = 
		"SELECT * FROM patient ".
		"WHERE name LIKE '$q'";
	$record = query_associative_one($query_string);
	DbUtil::switchRestore($saved_db);
	if( $record )
		$patient = Patient::getObject($record);
	else 
		$patient = null;
	return $patient;
	
}

function getLabIdFromGlobalPatientId($patientId, $country) {
	if( $country == 'Cameroon') {
		if( strstr($patientId, "128"))
			return 128;
		else if ( strstr($patientId, "129") )
			return 129;
		else
			return 131;
	}
}

?>