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
	$numItems = $_GET[$productid . "list"];
	echo "NUM ITEMS: " . $numItems . "<br>";
	$_SESSION["cid"] = $_GET["customerid"];
	$_SESSION["searchtext"] = "";

	include 'loggedinheader.php';

	$sql = "SELECT currentbasket FROM $database.customer c WHERE c.customerid = $cid";
	$result = mysqli_query($conn, $sql);

	$returnStr = "[";

	if ($result && mysqli_num_rows($result) > 0) {
		$currBasketId = -1;

	    // output data of each row
	    while($row = mysqli_fetch_assoc($result)) {
	        $currBasketId = $row["currentbasket"];
	    	//echo "Current basket id: " . $currBasketId. "<br>";
	    }

	    //add basketitem
	    $sqlAdd = "INSERT INTO $database.basketitem VALUES ($currBasketId, $productid, $numItems)";
	    $resultAdd = mysqli_query($conn, $sqlAdd);

	    if (!$result) {
	    	//echo "Insertion of basket item failed!!!<br>";
		} else {
			//echo "Insertion of basket item successful!<br>";
			//header('Location: http://localhost/cmsc424website/viewcart.html?customerid=' . $cid);
		}

	} else {
	    echo "0 results of products<br>";
	}

echo "Press View Cart Option<br>";
	mysqli_close($conn);

?>