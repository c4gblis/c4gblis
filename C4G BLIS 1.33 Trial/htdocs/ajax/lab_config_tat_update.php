<?php
#
# Updates goal TAT values for tests in a lab configuration
# Called via Ajax from lab_config_home.php
#

include("../includes/db_lib.php");

$lab_config_id = $_REQUEST['lid'];
$lab_config = get_lab_config_by_id($lab_config_id);
$test_type_list = $_REQUEST['ttype'];
$tat_value_list = $_REQUEST['tat'];
$tat_unit_list = $_REQUEST['unit'];

$count = 0;
foreach($test_type_list as $test_type_id)
{
	$curr_tat_value = $tat_value_list[$count];
	if(trim($curr_tat_value) == "")
	{
		# Empty TAT entry
		$count++;
		continue;
	}
	if($tat_unit_list[$count] == 2)
	{
		# Multiply by 24 to store in hours
		$curr_tat_value *= 24;
	}
	$lab_config->updateGoalTatValue($test_type_id, $curr_tat_value);
	$count++;
}
?>