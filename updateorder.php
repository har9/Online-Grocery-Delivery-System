<?php
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
	// Create connection
	$conn = mysqli_connect($servername, $username, $password);

	// Check connection
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}

	$cid =  $_GET["customerid"];
	$_SESSION["cid"] = $cid;
	$_SESSION["searchtext"] = "";

	include 'loggedinheader.php';

	$basketid = $_GET["basketid"];
	$standingordertype = $_GET[$basketid . "standingordertype"];
	$sql = "";

	if ($standingordertype == "None") {
		$sql = "UPDATE $database.basket SET isstandingorder = 0, standingordertype = '$standingordertype' WHERE basketid = $basketid";
	} else {
		$sql = "UPDATE $database.basket SET isstandingorder = 1, standingordertype = '$standingordertype' WHERE basketid = $basketid";
	}
	echo $sql . "<br>";
	$result = mysqli_query($conn, $sql);

	if (!$result) {
		echo "Update of basket failed!<br>";
	} else {
		echo "Successfully updated basket!<br>";
		header('Location: vieworders.php?customerid=' . $cid);
	}


	mysqli_close($conn);


?>
