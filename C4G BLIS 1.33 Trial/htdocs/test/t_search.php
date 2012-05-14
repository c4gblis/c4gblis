<?php
	echo "---Welcome to Test Search page";
	$con = mysql_connect("127.0.0.1:7188","root","blis123");
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}

	mysql_select_db("blis_129", $con);

	$result = mysql_query("SELECT * FROM patient where name LIKE \"%a%\"");

	echo "<table border='1'>
	<tr>
	<th>patient_id</th>
	<th>addl_id</th>
	<th>name</th>
	<th>sex</th>
	<th>age</th>
	<th>dob</th>
	<th>created_by</th>
	<th>ts</th>
	</tr>";
	
	while($row = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td>" . $row['patient_id'] . "</td>";
		echo "<td>" . $row['addl_id'] . "</td>";
		echo "<td>" . $row['name'] . "</td>";
		echo "<td>" . $row['sex'] . "</td>";
		echo "<td>" . $row['age'] . "</td>";
		echo "<td>" . $row['dob'] . "</td>";
		echo "<td>" . $row['created_by'] . "</td>";
		echo "<td>" . $row['ts'] . "</td>";
		echo "</tr>";
	}
	
	echo "</table>";
	
	echo "<br><br>END";
	
	mysql_close($con);
?>