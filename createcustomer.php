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

	$name = $_POST["lastname"] . ", " . $_POST["firstname"];
	$age = $_POST["age"];
	$phonenumber = $_POST["phonenumber"];
	$email = $_POST["email"];
	$password = $_POST["password"];
	$address = $_POST["address"] . ", " . $_POST["city"] . ", " . $_POST["state"] . " " . $_POST["zipcode"];
	$status = "active";
	$sex = $_POST["sex"];
	$paymentflag = false;
	$creditcardnumber = NULL;
	$ccexpiration = NULL;
	echo $_POST["ccexpiration"] . "<br><br>";
	$bankaccountnumber = NULL;
	$bankroutingnumber = NULL;

	if ($_POST["payment"] == "creditcard") {
		$paymentflag = false;
		$creditcardnumber = $_POST["creditcardnumber"];
		$ccexpiration = /*date('Y-m-d',*/ $_POST["ccexpiration"]/*)*/;
	} else  {
		$paymentflag = true;
		//TODO Set relevant fields here
	}

	/* CHECK IF ADDRESS IS OUT OF DELIVERY RANGE!!!!!! */

	//Insert new customer
	$sql = "INSERT INTO $database.customer  VALUES (NULL, '$name', $age, '$sex', '$password', 0, '$paymentflag', '$creditcardnumber', '$ccexpiration', '$bankaccountnumber', '$bankroutingnumber', '$address', '$phonenumber', '$email', '$status', -1)";
	echo $sql . "<br>";
	$result = mysqli_query($conn, $sql);

	if (!$result) {
		echo "Query failed!<br>";
	} else {
		echo "Successfully registered a customer!<br>";

		//Get created customer's ID
		$sqlGet = "SELECT customerid FROM $database.customer c WHERE c.name = '$name'";
		echo $sqlGet . "<br>";
		$resultGet = mysqli_query($conn, $sqlGet);
		$cID = 0;

		if (!$resultGet) {
			echo "Query failed!<br>";
		} elseif (mysqli_num_rows($resultGet) > 0) {
			// output data of each row
			while($row = mysqli_fetch_assoc($resultGet)) {
				$cID = $row["customerid"];

				echo "Successfully found a customer!<br>";
				echo "customerid: " . $row["customerid"] . "<br>";
			}

			//Create new default basket for the customer
			$isStandingOrder = false;
			$haltStandingOrder = false;
			$date = date('Y-m-d H:i:s');
			$sql2 = "INSERT INTO $database.basket  VALUES (NULL, $cID, '$isStandingOrder', '$haltStandingOrder', '$date')";
			echo $sql2 . "<br>";
			$result2 = mysqli_query($conn, $sql2);

			if (!$result2) {
				echo "Query failed!<br>";
			} else {
				echo "Successfully made a basket for the new customer!<br>";

				//Set current customer basket to new basket
				$sqlGetBasket = "SELECT basketid FROM $database.basket b WHERE b.customerid = '$cID'";
				echo $sqlGetBasket . "<br>";
				$resultGetBasket = mysqli_query($conn, $sqlGetBasket);
				$bID = 0;

				if (!$resultGetBasket) {
					echo "Query failed!<br>";
				} elseif (mysqli_num_rows($resultGetBasket) > 0) {
					// output data of each row
					while($row = mysqli_fetch_assoc($resultGetBasket)) {
						$bID = $row["basketid"];

						echo "Successfully found the basket!<br>";
						echo "basketid: " . $row["basketid"] . "<br>";
					}

					$sqlFixCurrBasket = "UPDATE $database.customer SET currentbasket=$bID WHERE customerid=$cID";
					echo $sqlFixCurrBasket . "<br>";
					$resultFix = mysqli_query($conn, $sqlFixCurrBasket);

					if (!$resultFix) {
						echo "Query failed!<br>";
					} else {
						echo "Successfully updated current basket for the new customer!<br>";
						//echo "Customer ID: " . $cID;
						header('Location: loggedinhome.php?customerid=' . $cID);
						
					}
				} else {
					echo "FAIL: basket not found!<br>";
				}
				
			}
		} else {
			echo "FAIL: customer not found!<br>";
		}
	}

	mysqli_close($conn);


?>
