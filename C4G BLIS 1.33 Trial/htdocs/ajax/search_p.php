<?php
#
# Returns list of matched patients
# Called via Ajax from /search.php
#

include("../includes/db_lib.php");
LangUtil::setPageId("find_patient");

$saved_session = SessionUtil::save();
$q = $_REQUEST['q'];
$a = $_REQUEST['a'];
$saved_db = "";
$lab_config = null;

if(isset($_REQUEST['l']))
{
	# Save context
	$lab_config = LabConfig::getById($_REQUEST['l']);
	$saved_db = DbUtil::switchToLabConfig($_REQUEST['l']);
}
else
{
	$lab_config = LabConfig::getById($_SESSION['lab_config_id']);
}
$patient_list = array();
# Fetch list from DB
if($a == 0)
{
	# Fetch by patient ID
	$patient_list = search_patients_by_id($q);
}
else if($a == 1)
{
	# Fetch by patient name
	$patient_list = search_patients_by_name($q);
	# See if there's a patient by the exact same name in another lab
	$patient = searchPatientByName($q);
}
else if($a == 2)
{
	# Fetch by additional ID
	$patient_list = search_patients_by_addlid($q);
}
else if($a == 3)
{
	# Fetch by daily number
	$patient_list = search_patients_by_dailynum("-".$q);
}
if( (count($patient_list) == 0 || $patient_list[0] == null) && ($patient == null) )
{
	?>
	<br>
	<div class='sidetip_nopos'>
	<?php
	echo LangUtil::$pageTerms['MSG_NOMATCH']." -";
	if($a == 0)
		echo " ".LangUtil::$generalTerms['PATIENT_ID']." ";
	else if($a == 1)
		echo " ".LangUtil::$generalTerms['NAME']." ";
	else if($a == 2)
		echo " ".LangUtil::$generalTerms['ADDL_ID']." ";
	?>
	<b><?php echo $q; ?></b>
	<?php
	//if(strpos($_SERVER['HTTP_REFERER'], "find_patient.php") !== false)
	if(false)
	{
		?>
		&nbsp;&nbsp;<a href='new_patient.php'><?php echo LangUtil::$pageTerms['ADD_NEW_PATIENT']; ?> &raquo;</a>
		<?php
	}
	?>
	</div>
	<?php
	SessionUtil::restore($saved_session);
	return;
}
else if( (count($patient_list) == 0 || $patient_list[0] == null) && ($patient != null) ) {
	?>
	<br>
	<div class='sidetip_nopos'>
	<?php
		echo "A record of the patient has been found in another hospital. Import patient record and continue?";
	SessionUtil::restore($saved_session);
	return;
}
# Build HTML table
?>
<table class='hor-minimalist-c'>
	<thead>
		<tr valign='top'>
			<?php
			if($lab_config->pid != 0)
			{
				?>
				<th><?php echo LangUtil::$generalTerms['PATIENT_ID']; ?></th>
				<?php
			}
			if($lab_config->dailyNum != 0)
			{
				?>
				<th><?php echo LangUtil::$generalTerms['PATIENT_DAILYNUM']; ?></th>
				<?php
			}
			if($lab_config->patientAddl != 0)
			{
				?>
				<th><?php echo LangUtil::$generalTerms['ADDL_ID']; ?></th>
				<?php
			}
			?>
			<?php #TODO: Add check if user has patient name/private data access here ?>
			<th><?php echo LangUtil::$generalTerms['NAME']; ?></th>
			<th><?php echo LangUtil::$generalTerms['GENDER']; ?></th>
			<th><?php echo LangUtil::$generalTerms['AGE']; ?></th>
			<?php
			if(strpos($_SERVER["HTTP_REFERER"], "search.php") !== false)
			{
				# Show status of most recently registered specimens
				echo "<th>".LangUtil::$generalTerms['SP_STATUS']."</th>";
			}
			?>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach($patient_list as $patient)
	{
	?>
		<tr valign='top'>
			<?php
			if($lab_config->pid != 0)
			{
				?>
				<td>
					<?php echo $patient->getSurrogateId(); ?>
				</td>
				<?php
			}
			if($lab_config->dailyNum != 0)
			{
				$daily_num = "-";
				//if($a == 3)
				if(true)
				{
					# Fetch specimen corresponding to this patient and daily_num
					$query_string =
						"SELECT * FROM specimen WHERE patient_id=$patient->patientId ".
						"ORDER BY date_collected DESC";
					$resultset = query_associative_all($query_string, $row_count);
					if($resultset == null || count($resultset) == 0)
						$daily_num = "-";
					else
					{
						$specimen = Specimen::getObject($resultset[0]);
						$daily_num = $specimen->getDailyNumFull();
					}
				}
				?>
				<td>
					<?php echo $daily_num; ?>
				</td>
				<?php
			}
			if($lab_config->patientAddl != 0)
			{
				?>
				<td>
					<?php echo $patient->getAddlId(); ?>
				</td>
				<?php
			}
			?>
			<td>
				<?php echo $patient->name; ?>
			</td>
			<td>
				<?php echo $patient->sex; ?>
			</td>
			<td>
				<?php echo $patient->getAge(); ?>
			</td>
			<?php
			if(strpos($_SERVER["HTTP_REFERER"], "search.php") !== false)
			{
				# Show status of most recently registered specimens
				$today = date("Y-m-d");
				$query_string = "SELECT * FROM specimen WHERE patient_id=$patient->patientId and date_collected='$today'";
				$resultset = query_associative_all($query_string, $row_count);
				$status = LangUtil::$generalTerms['DONE'];
				foreach($resultset as $record)
				{
					$specimen = Specimen::getObject($record);
					if
					(
						$specimen->statusCodeId == Specimen::$STATUS_PENDING ||
						$specimen->statusCodeId == Specimen::$STATUS_REFERRED
					)
					{
						$status = LangUtil::$generalTerms['PENDING_RESULTS'];
						break;
					}
				}
				echo "<td>$status</td>";
			}
			?>
			<td>
				<?php 
				if(strpos($_SERVER["HTTP_REFERER"], "find_patient.php") !== false)
				{
					# Called from find_patient.php. Show 'profile' and 'register specimen' link
					?>
					<a href='new_specimen.php?pid=<?php echo $patient->patientId; ?>' title='Click to Register New Specimen for this Patient'><?php echo LangUtil::$pageTerms['CMD_REGISTERSPECIMEN']; ?></a>
					</td><td>
					<a href='patient_profile.php?pid=<?php echo $patient->patientId; ?>' title='Click to View Patient Profile'><?php echo LangUtil::$pageTerms['CMD_VIEWPROFILE']; ?></a>
					<?php
				}
				else if(strpos($_SERVER["HTTP_REFERER"], "reports.php") !== false || strpos($_SERVER["HTTP_REFERER"], "reports2.php") !== false)
				{
					# Called from reports.php. Show 'Test History' link
					# Default to today for date range
					$today = date("Y-m-d");
					$today_parts = explode("-", $today);
					$url_string = "reports_testhistory.php?patient_id=".$patient->patientId."&location=".$_REQUEST['l']."&yf=".$today_parts[0]."&mf=".$today_parts[1]."&df=".$today_parts[2]."&yt=".$today_parts[0]."&mt=".$today_parts[1]."&dt=".$today_parts[2]."&ip=0";
					?>
					<a href='<?php echo $url_string; ?>' title='Click to View Report for this Patient' target='_blank'><?php echo LangUtil::$generalTerms['CMD_VIEW']; ?> Report</a>
					</td>
					<td>
					<a href='select_test_profile.php?pid=<?php echo $patient->patientId; ?>' title='Click to View Patient Profile'>Select Tests</a>
										</td>
					<td>					
					<?php
				}
				else
				{
					# Called from search.php. Show only 'profile' link
					?>
					<a href='patient_profile.php?pid=<?php echo $patient->patientId; ?>' title='Click to View Patient Profile'><?php echo LangUtil::$pageTerms['CMD_VIEWPROFILE']; ?></a>
					</td><td>
					<?php
				}
				?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
<?php
# Switch back context
if(isset($_REQUEST['l']))
{
	# Save context
	DbUtil::switchRestore($saved_db);
}
SessionUtil::restore($saved_session);
?>