<?php
require 'functions.php';

$title = 'Shuttle Booking Homepage';
require('header.php'); 


$connection = database_connect();

$resultq = $connection->query("select address from addresses order by address");
$num1    = mysqli_num_rows($resultq);
echo "<h3> Shuttle Full Addresses List :</h3>";
for($j=0;$j<$num1;++$j) {
	$row = mysqli_fetch_array($resultq);

	echo ($row['address']);
	echo ("    ");
}

$result = $connection->query("select segments.sid,segments.start,segments.end,
		SUM(bookings.Pnum) as TotalNum FROM segments LEFT JOIN bookings on segments.sid=bookings.sid 
		GROUP by segments.sid ORDER BY `segments`.`start` ASC");
$num    = mysqli_num_rows($result);
if($num!=0){
	

echo "<h3> Shuttle Booking List :</h3>";
echo "<table    >
		<tr height=50>
		    <th>Departure</th>
		
			<th>Destination</th>
		<th>Passanger-numbers</th>
		
			</tr>";
for($j=0;$j<$num;++$j) {
	$row = mysqli_fetch_array($result);
	echo "<tr><td>";
	echo($row['start']);
	echo "</td><td>";
	
	echo($row['end']);
	echo "</td><td>";
	$pnum=$row['TotalNum'];
	if($pnum==NULL)
		echo 0;
	echo($row['TotalNum']);
	echo "</td></tr>";

	
}
mysqli_free_result($result);

echo "</table>";
}


?>