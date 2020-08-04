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
$userid=$user['uid'];
$countint=2;

echo '<h3>Start your booking <span class="username">'.$user['email'].'</span> !</h3></br>';
 
if (isset($_POST['start']) && isset($_POST['end']) && isset($_POST['passnum']))
{
	$start=clean_input($_POST['start']);
	$_SESSION['val']=$start;
	$end=clean_input($_POST['end']);
	$passnum=clean_input($_POST['passnum']);
	
	if(strncmp($start,$end,$countint)>=0)
		echo "<script> alert('destination must larger than source');</script>";
	else {
		$resultq = $connection->query("select address from addresses order by address");
		$num1    = mysqli_num_rows($resultq);
		$array = array ();
		for($j=0;$j<$num1;++$j) {
			$row = mysqli_fetch_array($resultq);
			$array[$j]=$row['address'];
				}
	$bigaddress=$array[$num1-1];
	$smalladdress=$array[0];

		
		if(in_array($start,$array)){
			if(in_array($end,$array)){
				exist_start_end($start,$end,$passnum,$userid,$connection);
				echo'successful1';
			}//both start and end exist in addresses list
			else if(!in_array($end,$array)){
				$result = $connection->query("SELECT * FROM `segments`");
				$num    = mysqli_num_rows($result);
				
			if(strncmp($bigaddress,$end,$countint)>0){
				for($j=0;$j<$num;++$j){
					$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
					$starto=$row['start'];
					$endo=$row['end'];
					if((strncmp($starto,$start,$countint)>=0&&strncmp($endo,$end,$countint)<0)||
							(strncmp($starto,$end,$countint)<0&&strncmp($endo,$end,$countint)>0)){
						$sid=$row['sid'];
						echo ($sid);
						if(!check_capacity($sid,$connection,$passnum))
							die('Booking fail, in some segments the number of passangers is more than 4');
					}
				}//for loop
				$result = $connection->query("SELECT * FROM `segments`");
				$num    = mysqli_num_rows($result);
				for($j=0;$j<$num;++$j){
					$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
					$starto=$row['start'];
					$endo=$row['end'];
				if(strncmp($starto,$end,$countint)<0&&strncmp($endo,$end,$countint)>0){
							$sidold=$row['sid'];
							$startold=$row['start'];
							$endold=$row['end'];
							echo ($sidold);
							echo ($startold);
							echo ($endold);
							New_end($sidold,$startold,$endold,$end,$start,$connection);
							echo ("succeed");
						}
					
				}//for loop
				
					exist_start_end($start,$end,$passnum,$userid,$connection);
					$result3 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$end')");
					if(!$connection->commit()) {
						die('Error writing database');
					}
					if (! $result3) {
						die('Error database insert booking 3');
					}
		}//new end is in the middle of one line in segments
			
					else if(strncmp($bigaddress,$end,$countint)<0){
						for($j=0;$j<$num;++$j){
							$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
							$starto=$row['start'];
							$endo=$row['end'];
							if(strncmp($starto,$start,$countint)>=0&&strncmp($endo,$end,$countint)<0){
								$sid=$row['sid'];
								echo ($sid);
								if(!check_capacity($sid,$connection,$passnum))
									die('Booking fail, in some segments the number of passangers is more than 4');
							}
						}
					
							$result1 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$bigaddress','$end')");
							if (! $result1) {
								die('Error database insert booking 1');
							}
								
							exist_start_end($start,$end,$passnum,$userid,$connection);
							echo'successful99';
							$resultm = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$end')");
							if(!$connection->commit()) {
								die('Error writing database');
							}
							if (! $resultm) {
								die('Error database insert booking y');
							}
							
					}//new end is in the end
				
			
				
				
			}//start exist, end not exist
			
		}
		else if(!in_array($start,$array)){
			if(in_array($end,$array)){
				$result = $connection->query("SELECT * FROM `segments`");
				$num    = mysqli_num_rows($result);
				for($j=0;$j<$num;++$j){
					$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
					$starto=$row['start'];
					$endo=$row['end'];
					if((strncmp($starto,$start,$countint)>0&&strncmp($endo,$end,$countint)<=0)||
							(strncmp($starto,$start,$countint)<0&&strncmp($start,$endo,$countint)<0)){
							$sid=$row['sid'];
							echo ($sid);
							if(!check_capacity($sid,$connection,$passnum))
								die('Booking fail, in some segments the number of passangers is more than 4');	
						}
				
				}//for loop
				
				$result = $connection->query("SELECT * FROM `segments`");
				$num    = mysqli_num_rows($result);
				for($j=0;$j<$num;++$j){
					$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
					$starto=$row['start'];
					$endo=$row['end'];
				
				if(strncmp($starto,$start,$countint)<0&&strncmp($start,$endo,$countint)<0){
					$sidold=$row['sid'];
					$startold=$row['start'];
					$endold=$row['end'];
					echo ($sidold);
					echo ($startold);
					echo ($endold);
					New_start($sidold,$startold,$endold,$end,$start,$connection);
								
						}
							
				
				}//for loop

				exist_start_end($start,$end,$passnum,$userid,$connection);
				echo'successful3';
				$result3 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$start')");
				if(!$connection->commit()) {
					die('Error writing database');
				}
				if (! $result3) {
					die('Error database insert booking 3');
				}
				echo'successful7';
				
				
				
				
			}//start not exist, end exist
			else if(!in_array($end,$array)){
				$result = $connection->query("SELECT * FROM `segments`");
				$num    = mysqli_num_rows($result);
			if($num!=0){
					if(strncmp($bigaddress,$start,$countint)>0&&strncmp($bigaddress,$end,$countint)>0){
						for($j=0;$j<$num;++$j){
							$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
							$starto=$row['start'];
							$endo=$row['end'];
							if((strncmp($starto,$start,$countint)<0&&strncmp($start,$endo,$countint)<0)||
									(strncmp($starto,$end,$countint)<0&&strncmp($end,$endo,$countint)<0)||
									(strncmp($starto,$start,$countint)>0&&strncmp($endo,$end,$countint)<0)){
								$sid=$row['sid'];
								echo ($sid);
								if(!check_capacity($sid,$connection,$passnum))
									die('Booking fail, in some segments the number of passangers is more than 4');
							}
						}//for loop
						$result = $connection->query("SELECT * FROM `segments`");
						$num    = mysqli_num_rows($result);
						for($j=0;$j<$num;++$j){
							$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
							$starto=$row['start'];
							$endo=$row['end'];
						
							if(strncmp($starto,$end,$countint)<0&&strncmp($end,$endo,$countint)<0){
								$sidold=$row['sid'];
								$startold=$row['start'];
								$endold=$row['end'];
								echo ($sidold);
								echo ($startold);
								echo ($endold);
								New_end($sidold,$startold,$endold,$end,$start,$connection);
								echo'successful2';
					
								}
							
						}//for loop
						
						$result = $connection->query("SELECT * FROM `segments`");
						$num    = mysqli_num_rows($result);
						for($j=0;$j<$num;++$j){
							$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
							$starto=$row['start'];
							$endo=$row['end'];
					
							if(strncmp($starto,$start,$countint)<0&&strncmp($start,$endo,$countint)<0){
								$sidold=$row['sid'];
								$startold=$row['start'];
								$endold=$row['end'];
								echo ($sidold);
								echo ($startold);
								echo ($endold);
								New_start($sidold,$startold,$endold,$end,$start,$connection);
								echo'successful3';
						
								}
						
						}//for loop
					
						exist_start_end($start,$end,$passnum,$userid,$connection);
						$result3 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$start')");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						if (! $result3) {
							die('Error database insert booking 3');
						}
						$result4 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$end')");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						if (! $result4) {
							die('Error database insert booking 4');
						}
							
					}//both are inside middle of line
					else if(strncmp($smalladdress,$start,$countint)>0&&strncmp($smalladdress,$end,$countint)>0){
						$result2 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$start','$end')");
						$result1 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$end','$smalladdress')");
						
						$resultz = $connection->query("SELECT `sid`, `start`, `end` FROM `segments` WHERE  start='$start' AND end='$end'");
						if (! $resultz) {
							die('Error database  booking ');
						}
						$rowb = mysqli_fetch_array($resultz,MYSQLI_ASSOC);
						$SID=$rowb['sid'];
						$resultnew1= $connection->query("INSERT INTO bookings(uid, sid, Pnum)
								VALUES ('$userid',  '$SID', '$passnum')");
						$result3 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$start')");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						if (! $result3) {
							die('Error database insert booking 3');
						}
						$result6 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$end')");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						if (! $result6) {
							die('Error database insert booking cc');
						}
							
							
					}
					else if(strncmp($bigaddress,$start,$countint)<0&&strncmp($bigaddress,$end,$countint)<0){
						$result1 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$bigaddress','$start')");
						$result2 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$start','$end')");
						$resultz = $connection->query("SELECT `sid`, `start`, `end` FROM `segments` WHERE  start='$start' AND end='$end'");
						if (! $resultz) {
							die('Error database  booking ');
						}
						$rowb = mysqli_fetch_array($resultz,MYSQLI_ASSOC);
						$SID=$rowb['sid'];
						$resultnew1= $connection->query("INSERT INTO bookings(uid, sid, Pnum)
								VALUES ('$userid',  '$SID', '$passnum')");
						$result3 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$start')");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						if (! $result3) {
							die('Error database insert booking 3');
						}
						$result6 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$end')");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						if (! $result6) {
							die('Error database insert booking cc');
						}
					
							
					}// both are at the end
				
			}else if($num==0){
				$result1 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$start','$end')");
				if (! $result1) {
					die('Error database insert booking 1');
				}
				$result2 = $connection->query("SELECT `sid`, `start`, `end` FROM `segments` WHERE  start='$start' AND end='$end'");
				if (! $result2) {
					die('Error database insert booking 2');
				}
				$rowb = mysqli_fetch_array($result2,MYSQLI_ASSOC);
				$SID1=$rowb['sid'];
				
				$result6 = $connection->query("INSERT INTO `bookings`(`uid`, `sid`, `Pnum`) VALUES ($userid,$SID1,$passnum)");
				if (! $result6) {
					die('Error database insert booking 6');
				}
				$result3 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$start')");
				if(!$connection->commit()) {
					die('Error writing database');
				}
				if (! $result3) {
					die('Error database insert booking 3');
				}
				$result6 = $connection->query("INSERT INTO `addresses`(`address`) VALUES ('$end')");
				if(!$connection->commit()) {
					die('Error writing database');
				}
				if (! $result6) {
					die('Error database insert booking cc');
				}
				
				
			}
			
			}//both start anf end not exist
			
		}
		
		
	
		
	}
	$_SESSION['from'] = $start;
	$_SESSION['to'] = $end;
	$_SESSION['num'] = $passnum;
	header('Location: booksuccess.php');
	die();
		
}



?>
 

 
 	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
	</head>
	<body >
    <form action="" method="POST" enctype="multipart/form-data">
    
   
   
        <br /><br />
        
        <div>From: <input type="text" name="start"></div><br /><br />
        
      <div>To: <input type="text" name="end"></div><br /><br />
      
        
        
        
        
    
    
	Num of Passangers:<select name="passnum">
	<option selected="selected" disabled="disabled"  style='display: none' value=''></option> 
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	</select>
<br /><br />

     <input  type="submit" value="Create new booking" />
     <input  type="reset"  />

</form>


		<script type="text/javascript">
	
		</script>
		
	</body>
	</html>
















 