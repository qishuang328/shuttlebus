<?php
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 2*60)) {
    logout(); //session expires after 2 minutes
	header('Location: user.php?page=login');
}
update_last_activity();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php if(isset($title)){ echo $title; }?></title>
	<style>

	h1 {
		font-size: 40px;
	}
	html {
		background-color: #CC99FF;
		height:100%;
	}
	#wrapper {
		width:1000px;
		overflow:hidden;
		padding:10px;
	}
	#sidebar {
		float:left;
		width:140px;
	}
	#sidebar ul {
		font-family: Arial, Helvetica, sans-serif;
		margin:0;
		padding:0;
	}
	#sidebar ul li a {
		text-decoration: none;
		font-size: 14px;
		display: block;
		padding: 3px;
		background-color: #CC00FF;
		border-bottom: 1px solid #eee;
		padding: 7px 14px;
		border-radius: 4px;
		margin: 5px 0;
	}
	#sidebar ul li a:link, #sidebar ul li a:visited {
		color: #EEE;
		text-decoration: none;

	}

	#content {
		margin: 0 0 0 200px;
		box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);
		padding: 30px;
		background-color: #CCCCFF;
		min-height: 400px;
	}
	.button {
		background-color: #CC00FF;
		border: none;
		color: white;
		padding: 7px 25px;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		font-size: 16px;
		border-radius: 4px;
	}
	table, td, th { border: 1px solid black}
	label {
		font-weight: bold;
		padding-right: 11px;
	}
	.booking, .username {
		color: #336699;
		font-style: italic;
	}

	</style>
</head>
<body>
<noscript><h1 class="error">Your browser does not support JavaScript!</h1></noscript>

<div id="wrapper">
	<h1><?php if(isset($title)){ echo $title; }?></h1>
	<div id="sidebar">
	<ul>
		<li><a href="index.php">Home</a>
		<?php if(is_logged()) echo '<li><a href="user.php">Profile</a>'; ?>
		<?php if(is_logged()) echo '<li><a href="newbooking.php">New Booking</a>'; ?>
		<?php if(is_logged()) echo '<li><a href="user.php?page=logout">Logout</a>'; ?>
		<?php if(!is_logged()) echo '<li><a href="user.php?page=login">Login</a>'; ?>
		<?php if(!is_logged()) echo '<li><a href="user.php?page=signup">Signup</a>'; ?>
	</ul>
	</div>
	<div id="content">
	