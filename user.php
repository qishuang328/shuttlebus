<?php
require 'functions.php';

$connection = database_connect();
$page="profile";
if(isset($_GET['page']))
	$page=$_GET['page'];


if($page == "profile") {

	if(!is_logged()) {
		header('Location: user.php?page=login');
		die();
	}
		
	$title = "Profile";
	require('header.php'); 
	
	$user = get_logged_user($connection);
	$uid=$user['uid'];
	echo '<h3>Welcome to the booking <span class="username">'.$user['email'].'</span> !</h3>';
	
	$resultq = $connection->query("select address from addresses order by address");
	$num1    = mysqli_num_rows($resultq);
	echo "<h3> Shuttle Full Addresses List : </h3>";
	for($j=0;$j<$num1;++$j) {
		$row = mysqli_fetch_array($resultq);
	
		echo ($row['address']);
		echo ("    ");
	}
	
	$result = $connection->query("SELECT bookings.uid,bookings.sid,segments.start,segments.end, bookings.Pnum ,
 users.email FROM (bookings LEFT JOIN users ON bookings.uid=users.uid) LEFT JOIN segments ON bookings.sid=segments.sid");
	$num    = mysqli_num_rows($result);
	if($num!=0){
		echo "<h3> Shuttle Booking With Users List :</h3>";
		echo "<table    >
		<tr height=50>
		    <th>Departure</th>
		
			<th>Destination</th>
				<th>User</th>
		<th>User-book-numbers</th>
		
			</tr>";
		for($j=0;$j<$num;++$j) {
			$row = mysqli_fetch_array($result);
			echo "<tr><td >";
			echo($row['start']);
			echo "</td><td>";
		
			echo($row['end']);
			echo "</td><td>";
			
			echo($row['email']);
			echo "</td><td>";
			
			$pnum=$row['Pnum'];
			if($pnum==NULL)
				echo 0;
			echo($row['Pnum']);
			echo "</td></tr>";
		
		
		}
		mysqli_free_result($result);
		
		echo "</table>";
		
		
		
		
	}
	
	if(isset($_POST['submit'])) {
		
		echo ($uid);
		$resul = $connection->query("select address from addresses order by address");
		$num1    = mysqli_num_rows($resul);
		$array = array ();
		for($j=0;$j<$num1;++$j) {
			$row = mysqli_fetch_array($resul);
			$array[$j]=$row['address'];
		}
		$smalladdress=$array[0];
		echo ($smalladdress);
		$bigaddress=$array[$num1-1];
		echo ($bigaddress);
		
		
		$resultq = $connection->query("select * FROM `bookings` WHERE  uid=$uid");
		$numm    = mysqli_num_rows($resultq);
		$array1 = array ();
		$array2 = array ();
		if($numm!=0){
			for($j=0;$j<$numm;++$j) {
				$row = mysqli_fetch_array($resultq);
				$sid=$row['sid'];
				$resultm = $connection->query("select * FROM `segments` WHERE  sid=$sid");
				$rowm = mysqli_fetch_array($resultm);
				$array1[$j]=$rowm['start'];
				$array2[$j]=$rowm['end'];
			}
			$arrays=array_merge($array1,$array2);
			sort($arrays);
			$arrlength=count($arrays);
			global $startll;
			global $endll;
			for($x=0;$x<$arrlength;$x++)
			{
			$startll=$arrays[0];
			if($x=$arrlength-1)
				$endll=$arrays[$arrlength-1];
			
			echo "<br>";
			}
			echo ($startll);
			echo ($endll);
			
			
			$resultq = $connection->query("delete FROM `bookings` WHERE  uid=$uid");
			if(!$connection->commit()) {
				die('Error writing database');
			}
			
			$result = $connection->query("select segments.sid,segments.start,segments.end,
						SUM(bookings.Pnum) as TotalNum FROM segments LEFT JOIN bookings on segments.sid=bookings.sid
						GROUP by segments.sid ORDER BY `segments`.`start` ASC");
			$num    = mysqli_num_rows($result);
			for($j=0;$j<$num;++$j) {
				$row = mysqli_fetch_array($result);
				$sida=$row['sid'];
				$starto=$row['start'];
				$endo=$row['end'];
				$number=$row['TotalNum'];
				echo ($number);
				if($number==null){
					if($starto==$startll&&$smalladdress==$starto){
						$resultq = $connection->query("delete FROM `segments` WHERE  sid=$sida");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						$result = $connection->query("select segments.sid,segments.start,segments.end,
								SUM(bookings.Pnum) as TotalNum FROM segments LEFT JOIN bookings on segments.sid=bookings.sid
								where start='$endo' GROUP by segments.sid ORDER BY `segments`.`start` ASC");
						$numd    = mysqli_num_rows($result);
						for($j=0;$j<$numd;++$j) {
							$row = mysqli_fetch_array($result);
							$sida=$row['sid'];
							$numbe=$row['TotalNum'];
							echo ($number);
							if($numbe==null){
								$resultq = $connection->query("delete FROM `segments` WHERE  sid=$sida");
								if(!$connection->commit()) {
									die('Error writing database');
								}
							}
						}
						
						$resultq = $connection->query("delete FROM `addresses` WHERE  address='$starto'");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						$resultq = $connection->query("delete FROM `addresses` WHERE  address='$endo'");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						
					
					
					
					}
				
					else if($bigaddress=$endo&&$endll==$endo&&$starto==$startll){
					 $resultq = $connection->query("delete FROM `segments` WHERE  sid=$sida");
					 if(!$connection->commit()) {
					 die('Error writing database');
					 }
					 $result = $connection->query("select segments.sid,segments.start,segments.end,
							SUM(bookings.Pnum) as TotalNum FROM segments LEFT JOIN bookings on segments.sid=bookings.sid
							where end='$starto' GROUP by segments.sid ORDER BY `segments`.`start` ASC");
					 $numd    = mysqli_num_rows($result);
					 for($j=0;$j<$numd;++$j) {
					 	$row = mysqli_fetch_array($result);
					 	$sida=$row['sid'];
					 	$numbe=$row['TotalNum'];
					 	echo ($number);
					 	if($numbe==null){
					 		$resultq = $connection->query("delete FROM `segments` WHERE  sid=$sida");
							 if(!$connection->commit()) {
							 die('Error writing database');
							 }
					 	}
					 }
					  	
					 $resultq = $connection->query("delete FROM `addresses` WHERE  address='$starto'");
					 if(!$connection->commit()) {
					 die('Error writing database');
					 }
					 $resultq = $connection->query("delete FROM `addresses` WHERE  address='$endo'");
					 if(!$connection->commit()) {
					 	die('Error writing database');
					 }
					 }
					else if($bigaddress=$endo&&$endll==$endo){
						$resultq = $connection->query("delete FROM `segments` WHERE  sid=$sida");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						
						$resultq = $connection->query("delete FROM `addresses` WHERE  address='$endll'");
						if(!$connection->commit()) {
							die('Error writing database');
						}
					}
					else if($endll==$endo){
						$resultq = $connection->query("delete FROM `segments` WHERE  sid=$sida");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						$resultq = $connection->query("delete FROM `addresses` WHERE  address='$startll'");
						if(!$connection->commit()) {
							die('Error writing database');
						}
					
					
					}
					else if($starto==$startll){
						$resultq = $connection->query("delete FROM `segments` WHERE  sid=$sida");
						if(!$connection->commit()) {
							die('Error writing database');
						}
						$resultq = $connection->query("delete FROM `addresses` WHERE  address='$startll'");
						if(!$connection->commit()) {
							die('Error writing database');
						}
							
							
					}
			
					
				}
				
			
			
			}
		}
		//die('You have no booking!');
		
	
		
			header('Location: user.php');
			
		
		
		
		
	}
	
		
?>
<form action="user.php" method="post">
	 <p>
		<input class="button" type="submit" name="submit" value="Cancel booking" />
	  </p>
 </form>
	 
<?php
	

}// end profile

if($page == "login") {
	$title = "Login";
	require('header.php');
	
	if(isset($_POST['submit'])) {
		if (!isset($_POST['username']) || $_POST['username'] === '')
			$error[] = 'Invalid username';
		if (!isset($_POST['password']) || $_POST['password'] === '')
			$error[] = 'Invalid password';
		
		$username = filter_var($_POST['username'],FILTER_SANITIZE_EMAIL);
		$password = sha1($_POST['password']);// protect password
			
		if(filter_var($username, FILTER_VALIDATE_EMAIL)
			&& !preg_match('/"/', $username) && !preg_match("/'/", $username)) {
			// check if username already exists
			$result = $connection->query("SELECT * FROM users WHERE email = '$username' AND password = '$password'");
			if($result && $result->num_rows != 0) {
				$row = $result->fetch_assoc();
				login_user($row['uid']);
				
				header('Location: user.php');
				die();
			}
			else 
				$error[] = 'Wrong username or password';
		}
		else {
			$error[] = 'Please enter a valid email address';
		}
		if (isset($error))
			foreach ($error as $err)
				echo '<h3 class="error">'.$err.'</h3>';
	}	
	 
	// n.b. some validation is also done using html5
	
?>
<form name="login" action="user.php?page=login" method="post" onsubmit="return validateFormLogin()" >
	  <p>
		<label>Username</label>
		<input type="email" maxlength="100" required="required" name="username" placeholder="your email"/>
	  </p>
	  <p>
		<label>Password</label>
		<input type="password" maxlength="100" required="required" name="password" placeholder="your password"/>
	  </p>
	  <p>
		<input class="button" type="submit" name="submit" value="Login" />
	  </p>
 </form>

<script>
function validateFormLogin() {
	var errors = [];
    var username = document.forms["login"]["username"].value;

    if (!validateEmail(username)) {
        errors.push("Username must be an email");
    }
	
	if(errors.length>0) {
		alert(errors.join("\n"))
		return false;
	}
}
function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
</script>
		 
<?php
}// end login

if($page == "signup") {
	$title = "Signup";
	require('header.php'); 
	
	if(isset($_POST['submit'])) {
		if (!isset($_POST['username']) || $_POST['username'] === '')
			$error[] = 'Invalid username';
		if (!isset($_POST['password']) || $_POST['password'] === '')
			$error[] = 'Invalid password';
		
		
		$username = filter_var($_POST['username'],FILTER_SANITIZE_EMAIL);

		if(strlen($username) > 100)
			$error[] = 'Username is too long';
			
		// check if email contains invalid characters
		if($_POST['username'] === $username) {
			
			if(!filter_var($username, FILTER_VALIDATE_EMAIL)
				|| preg_match('/"/', $username) || preg_match("/'/", $username))
				$error[] = 'Please enter a valid email address';
			else {
				// check if username already exists
				$result = $connection->query("SELECT email FROM users WHERE email = '$username'");
				if($result && $result->num_rows != 0)
					$error[] = 'Username already exists';
				
				$password = $_POST['password'];
				
				if(strlen($password) > 100)
					$error[] = 'Password is too long.';
				
				if (!(preg_match('/[a-z]/', $password) 
					&&( preg_match('/[A-Z]/', $password)
					|| preg_match('/[0-9]/', $password))))
					$error[] = 'Weak password';
			}
		}	
		else 
			$error[] = 'Please enter a valid email address';
		// save user to database
		if (!isset($error)) {
			$password = sha1($password);//protect password
			$result = $connection->query("INSERT INTO users(email, password) VALUES('$username', '$password')");
			if(!$result){
				die('Error signup');
			}
			
			$id = $connection->insert_id;
			
			if(!$connection->commit()) {
				die('Error writing database');
			}
		
			// save session
			login_user($id);

			header('Location: user.php?page=profile');
			die();
		}
		else
			foreach ($error as $err)
				echo '<h3 class="error">'.$err.'</h3>';
	}	
	 
	
	
?>
<form name="signup" action="user.php?page=signup" method="post" onsubmit="return validateForm()">
	  <p>
		<label>Username</label>
		<input type="email" maxlength="100" required="required" name="username" placeholder="your email"/>
	 </p>
	  <p>
		<label>Password</label>
		<input type="password" maxlength="100" required="required" name="password" placeholder="your password"/>
	  </p>
	  <p>
		<input class="button" type="submit" name="submit" value="Signup" />
	  </p>
 </form>
		 
<script>
function validateForm() {
	var errors = [];
    var username = document.forms["signup"]["username"].value;
    var password = document.forms["signup"]["password"].value;

    if (!validateEmail(username)) {
        errors.push("Username must be an email");
    }
	if (!validatePassword(password)) {
        errors.push("Password must contain at least a lowercase letter, an uppercase letter or a number");
    }
	
	if(errors.length>0) {
		alert(errors.join("\n"))
		return false;
	}
}
function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function validatePassword(password) {
    return (hasNumbers(password)||hasUpperLetters(password))&&hasLowerLetters(password);
}
function hasNumbers(t) {
	return /\d/.test(t);
}
function hasUpperLetters(t) {
    return /[A-Z]/.test(t);
}
function hasLowerLetters(t) {
    return /[a-z]/.test(t);
}
</script>
<?php
}// end signup


if($page == "logout") {
	// destroy session and go to login
	logout();
	header('Location: index.php');
	die();
}



?>
