<?php
require 'config.php';



// connect to database
function database_connect() {
  global $host, $user, $pwd, $db;
  $connection = @new mysqli($host, $user, $pwd, $db);
  if($connection->connect_error) {
    die('<h1>Database not working</h1>');
  }
  if(!$connection) {
    die('<h1>Cannot connect to database</h1>');
  }
  unset($host);
  unset($user);
  unset($pwd);
  unset($db);
  $connection->autocommit(false);
  //$connection->autocommit(0);

  return $connection;
}

function clean_input($input) {
	  $input = trim($input);
	  $input = strip_tags($input);
	  $input = htmlspecialchars($input);
	  return $input;
}

function is_logged() {
	if(isset($_SESSION['logged']) && $_SESSION['logged'] == true)
		return true;
		
	return false;
}

function login_user($id) {
	
	$_SESSION['logged'] = true;
	$_SESSION['logged_id'] = $id;
	update_last_activity();
}
function get_user_by_id($id, $connection){
	$result = $connection->query("SELECT * FROM users WHERE uid = '$id'");
	if($result && $result->num_rows != 0) {
		$user = $result->fetch_assoc();
		return $user;
	}
	return null;
}


function logout() {
    session_unset();
    session_destroy();
}

function update_last_activity() {
	$_SESSION['last_activity'] = time();
}

function get_logged_user($connection){
	if(is_logged()) {
		return get_user_by_id($_SESSION['logged_id'], $connection);
	}
	return null;
}

function check_capacity($id, $connection,$passnum){
	$capacity=4;
	$resultnew = $connection->query("SELECT sid,SUM(bookings.Pnum) as TotalNum
			FROM `bookings` where bookings.sid=$id GROUP by bookings.sid ");
	$rownew = mysqli_fetch_array($resultnew,MYSQLI_ASSOC);
	$total=$rownew['TotalNum'];
	$totalall=$total+$passnum;
	if($totalall>$capacity)
	 return false;
	return true;
	
	
}	
function exist_start_end($start,$end,$passnum,$userid,$connection){
	$result = $connection->query("SELECT * FROM `segments`");
	$num    = mysqli_num_rows($result);
	$countint=2;
	for($j=0;$j<$num;++$j){
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$starto=$row['start'];
		$endo=$row['end'];
		if(strncmp($starto,$start,$countint)>=0&&strncmp($endo,$end,$countint)<=0){
			$sid=$row['sid'];
			if(!check_capacity($sid,$connection,$passnum)){
				die('Booking fail, in some segments the number of passangers is more than 4');
				
			}
				
			
		}
	
	
	}//for loop check capacity

	$result = $connection->query("SELECT * FROM `segments`");
	$num    = mysqli_num_rows($result);
	for($j=0;$j<$num;++$j){
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$starto=$row['start'];
		$endo=$row['end'];
		if(strncmp($starto,$start,$countint)>=0&&strncmp($endo,$end,$countint)<=0){
			$sid1=$row['sid'];
			$resultnew1= $connection->query("INSERT INTO bookings(uid, sid, Pnum) VALUES ($userid,$sid1,$passnum)");
			if(!$connection->commit()) {
				die('Error writing database');
			}
		}
	
	}//for loop

}

function New_end($sidold,$startold,$endold,$end,$start,$connection){
	//insert to segments oldstart and newend, get sid
	$result1 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$startold','$end')");
	if (! $result1) {
		die('Error database insert booking 1');
	}
	$result2 = $connection->query("SELECT `sid`, `start`, `end` FROM `segments` WHERE  start='$startold' AND end='$end'");
	if (! $result2) {
		die('Error database insert booking 2');
	}
	$rowb = mysqli_fetch_array($result2,MYSQLI_ASSOC);
	$SID1=$rowb['sid'];
		
	//insert to segment newend,oldend,get sid
	$result3 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$end','$endold')");
	if (! $result3) {
		die('Error database insert booking 3');
	}
	$result4 = $connection->query("SELECT `sid`, `start`, `end` FROM `segments` WHERE  start='$end' AND end='$endold'");
	if (! $result4) {
		die('Error database insert booking 4');
	}
	$rowv = mysqli_fetch_array($result4,MYSQLI_ASSOC);
	$SID2=$rowv['sid'];
	
	//find the lines in bookings about sidold
	$result5 = $connection->query("SELECT `uid`, `sid`, `Pnum` FROM `bookings` WHERE sid=$sidold");
	if (! $result5) {
		die('Error database insert booking 5');
	}
	$numa    = mysqli_num_rows($result5);
	for($j=0;$j<$numa;++$j){
		$rowa = mysqli_fetch_array($result5,MYSQLI_ASSOC);
		$uidold=$rowa['uid'];
		$Pnum=$rowa['Pnum'];
			
		$result6 = $connection->query("INSERT INTO `bookings`(`uid`, `sid`, `Pnum`) VALUES ($uidold,$SID1,$Pnum)");
		if (! $result6) {
			die('Error database insert booking 6');
		}
		$result7 = $connection->query("INSERT INTO `bookings`(`uid`, `sid`, `Pnum`) VALUES ($uidold,$SID2,$Pnum)");
		if (! $result7) {
			die('Error database insert booking 7');
		}
		$result8 = $connection->query("DELETE FROM `bookings` WHERE sid=$sidold and uid=$uidold");
		if (! $result8) {
			die('Error database insert booking 8');
		}
			
	}
	$result9 = $connection->query("DELETE FROM `segments` WHERE sid=$sidold");
	if (! $result9) {
		die('Error database insert booking 9');
	}
}
function New_start($sidold,$startold,$endold,$end,$start,$connection){
	//insert to segments oldstart and newstart, get sid
	$result1 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$startold','$start')");
	if (! $result1) {
		die('Error database  booking 1');
	}
	$result2 = $connection->query("SELECT `sid`, `start`, `end` FROM `segments` WHERE  start='$startold' AND end='$start'");
	if (! $result2) {
		die('Error database  booking 2');
	}
	$rowb = mysqli_fetch_array($result2,MYSQLI_ASSOC);
	$SID1=$rowb['sid'];
	
	//insert to segment newstart,oldend,get sid
	$result3 = $connection->query("INSERT INTO `segments` ( `start`, `end`)VALUES ('$start','$endold')");
	if (! $result3) {
		die('Error database  booking 3');
	}
	$result4 = $connection->query("SELECT `sid`, `start`, `end` FROM `segments` WHERE  start='$start' AND end='$endold'");
	if (! $result4) {
		die('Error database  booking 4');
	}
	$rowv = mysqli_fetch_array($result4,MYSQLI_ASSOC);
	$SID2=$rowv['sid'];
	
	//find the lines in bookings about sidold
	$result5 = $connection->query("SELECT `uid`, `sid`, `Pnum` FROM `bookings` WHERE sid=$sidold");
	if (! $result5) {
		die('Error database  booking 5');
	}
	$numa    = mysqli_num_rows($result5);
	
		for($j=0;$j<$numa;++$j){
			$rowa = mysqli_fetch_array($result5,MYSQLI_ASSOC);
			$uidold=$rowa['uid'];
			$Pnum=$rowa['Pnum'];
				
			$result6 = $connection->query("INSERT INTO `bookings`(`uid`, `sid`, `Pnum`) VALUES ($uidold,$SID1,$Pnum)");
			if (! $result6) {
				die('Error database  booking 6');
			}
			$result7 = $connection->query("INSERT INTO `bookings`(`uid`, `sid`, `Pnum`) VALUES ($uidold,$SID2,$Pnum)");
			if (! $result7) {
				die('Error database  booking 7');
			}
			$result8 = $connection->query("DELETE FROM `bookings` WHERE sid=$sidold and uid=$uidold");
			if (! $result8) {
				die('Error database  booking 8');
			}
				
		}
		


	$result9 = $connection->query("DELETE FROM `segments` WHERE sid=$sidold");
	if (! $result9) {
		die('Error database  booking 9');
	}
	
}


?>