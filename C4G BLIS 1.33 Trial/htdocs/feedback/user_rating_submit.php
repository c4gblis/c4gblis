<?php
#
# Adds user rating to DB.
#

include("../includes/db_lib.php");

$user_id = $_REQUEST['user_id'];
$rating = $_REQUEST['rating'];
$skipped=$_REQUEST['skipped'];
if($skipped==-1)
$rating=6;

//$saved_db = DbUtil::switchToGlobal();
$query_string = 
	"INSERT INTO user_rating (user_id, rating) ".
	"VALUES ($user_id, $rating)";
echo $query_string;
query_blind($query_string);

//DbUtil::switchRestore($saved_db);
?>