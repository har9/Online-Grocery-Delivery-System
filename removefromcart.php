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

	$cid = $_GET['customerid'];
	$productid = $_GET['productid'];

	$_SESSION["cid"] = $cid;
	$_SESSION["searchtext"] = "";

	$sql = "SELECT currentbasket FROM $database.customer c WHERE c.customerid = $cid";
	$result = mysqli_query($conn, $sql);

	$returnStr = "[";

	if ($result && mysqli_num_rows($result) > 0) {
		$currBasketId = -1;

	    // output data of each row
	    while($row = mysqli_fetch_assoc($result)) {
	        $currBasketId = $row["currentbasket"];
	    	echo "Current basket id: " . $currBasketId. "<br>";
	    }

	    //add basketitem
	    $sqlAdd = "DELETE FROM $database.basketitem WHERE basketid = $currBasketId AND productid = $productid";
	    $resultAdd = mysqli_query($conn, $sqlAdd);

	    if (!$result) {
	    	echo "Deletion of basket item failed!!!<br>";
		} else {
			echo "Deletion of basket item successful!<br>";
			header('Location: viewcart.php?customerid=' . $cid);

		}

	} else {
	    echo "0 results of products<br>";
	}

	mysqli_close($conn);

?>