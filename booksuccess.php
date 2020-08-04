<?php

require 'functions.php';

$connection = database_connect();


if(!is_logged()) {
	header('Location: user.php?page=login');
	die();
}

$title = "New Booking";
require('header.php');


$user = get_logged_user($connection);
$start = $_SESSION['from'];
$passnum = $_SESSION['num'];
//echo ($start);

$end = $_SESSION['to'];
//echo ($end);
echo '<h3>Booking is successful!<span class="username">'.$user['email'].'</span> !</h3></br>';
echo ("You have booked from $start to $end, with passanger number $passnum! ");

$resultq = $connection->query("select address from addresses order by address");
$num1    = mysqli_num_rows($resultq);
echo "<h3> Shuttle Full Addresses List :</h3>";
for($j=0;$j<$num1;++$j) {
	$row = mysqli_fetch_array($resultq);
	$address=$row['address'];
	if($address==$start||$address==$end){
	echo "<span style='color:red'>".$address."</span>";
	echo ("    ");
	}
	else {
		echo ($address);
		echo ("    ");
	}
	
}

?>



 	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
	</head>
	<body >
      <br /><br />
<a href="user.php"><input type="button" name="Go to my profile" value="Go to my profile"/></a>     
<a href="newbooking.php"><input type="button" name="Make a new booking" value="Make a new booking"/></a>
	</body>
	</html>





