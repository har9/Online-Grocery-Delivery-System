 <html>
	<head>
		<title>thegrocerystore.com</title>
	</head>
<body background="back.jpg";
    background-size: 1200px 1500px;
    background-repeat: no-repeat;>
		<?php 
			$cid = $_POST["customerid"];
			//echo "Get CID: " . $cid . "<br>";
			$_SESSION["cid"] = $_POST["customerid"];
			$_SESSION["searchtext"] = "";

			include 'loggedinheader.php';


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


			$cid = $_POST["customerid"];
			$bid = $_POST["basketid"];
			//echo "Basket id:". $bid. "<br>";
			$totalitemcost = $_POST["totalitemcost"];
			$costwithtax = $_POST["costwithtax"];
			$deliverycharge = $_POST["deliverycharge"];
			$tripduration = $_POST["tripduration"];
			$bankaccountnumber = $_POST["bankaccountnumber"];
			$bankroutingnumber = $_POST["bankroutingnumber"];
			$standingordertype = $_POST["standingordertype"];
			echo "Standing Order Type: " . $standingordertype . "<br>";

			$totaltransactioncost = $costwithtax + $deliverycharge;

			$date = date('Y-m-d H:i:s', time());
			echo "DATE: " . $date . "<br>";
			echo "DURATION: " . $tripduration . "<br>";
			$timeofarrival = date("Y-m-d H:i:s", time() + $tripduration);
			echo "Estimated Time of Delivery: " . $timeofarrival . "<br>";



			$sql = "UPDATE $database.customer SET bankaccountnumber = '$bankaccountnumber', bankroutingnumber = '$bankroutingnumber' WHERE customerid = $cid";
			$result = mysqli_query($conn, $sql);

			/* Make bill */
			if (!$result) {
				//echo "Update of customer failed!<br>";
			} else {
			    //echo "Customer update success!<br>";

			    if ($standingordertype != "None") {
			    	//echo "IsStandingOrder is true<br>";
			    	$sqlStanding = "UPDATE $database.basket SET isstandingorder = 1 WHERE basketid = $bid";
			    	$resultStanding = mysqli_query($conn, $sqlStanding);

			    	if (!$resultStanding) {
			    		//echo "Update of basket to standing order failed!<br>";
			    	} else {
			    		//echo "Successfully set basket to standing order!<br>";
			    	}
			    }

			    $sqlTrans = "INSERT INTO $database.transaction VALUES (NULL, $bid, $cid, $totaltransactioncost, 1, '$date', '$timeofarrival', NULL)";
			    $resultTrans = mysqli_query($conn, $sqlTrans);

			    if (!$resultTrans) {
			    	//echo "Insertion of transaction failed!<br>";
			    } else {
			    	//echo "Transaction insertion success!<br>";

				    $sqlGetTransID = "SELECT transactionid FROM $database.transaction WHERE basketid = $bid";
				    $resultGetTransID = mysqli_query($conn, $sqlGetTransID);

				    if ($resultGetTransID && mysqli_num_rows($resultGetTransID) > 0) {
				    	$transID = -1;
					    // output data of each row
					    while($row = mysqli_fetch_assoc($resultGetTransID)) {
					    	$transID = $row["transactionid"];
					    	//echo "Found transID: " . $transID . "<br>";
					    }

					    $sqlBill = "INSERT INTO $database.bill VALUES (NULL, $transID, $totalitemcost, $costwithtax, $deliverycharge, NULL, 0)";
					    $resultBill = mysqli_query($conn, $sqlBill);

					    if (!$resultBill) {
					    	//echo "Insertion of bill failed!<br>";
					    } else {
					    	//echo "Bill insertion success!<br>";
					    	//echo mysql_info() . "<br>";

					    	/* Create dispatch ticket */
					    	//$sqlDispatch = "INSERT INTO $database.dispatchticket VALUES ($transID, $cid, $bid, NULL, (SELECT address FROM $database.customer WHERE customerid = $cid), '$date')";
							//$sqldel = "SELECT dpid FROM $database.deliveryperson WHERE flag = 0 ORDER BY dpid LIMIT 1";
							//$resultdel = mysqli_query($conn, $sqldel);
							//echo "the del id i:" .$resultdel. "<br>";
							$sqlDispatch = "INSERT INTO $database.dispatchticket VALUES ($transID, $cid, $bid, (SELECT dpid FROM $database.deliveryperson WHERE flag = 0 ORDER BY dpid LIMIT 1), (SELECT address FROM $database.customer WHERE customerid = $cid), '$date')";
							//$sqldpid = "UPDATE $database.deliveryperson SET flag = 1 WHERE dpid = (SELECT dpid from $database.deliveryperson WHERE flag = 0 ORDER BY dpid LIMIT 1)";
							//$sqldpid = "UPDATE $database.deliveryperson SET flag = 1 WHERE dpid = 1";
						    //$resultdpid = mysqli_query($conn, $sqldpid);
							//$sqlDispatch = "INSERT INTO $database.dispatchticket VALUES ($transID, $cid, $bid, 1, (SELECT address FROM $database.customer WHERE customerid = $cid), '$date')";
							

					    	$resultDispatch = mysqli_query($conn, $sqlDispatch);
							$sqldpid = "UPDATE $database.deliveryperson SET flag = 1 WHERE flag = 0 ORDER BY dpid LIMIT 1";
							$resultdpid = mysqli_query($conn, $sqldpid);

					    	if (!$resultDispatch) {
					    		//echo "Dispatch ticket creation failed!<br>";
					    	} else {
					    		//echo "Dispatch ticket creation success!<br>";
					    		/* Update customer balance and set new current basket */
						    	$sqlBasket = "INSERT INTO $database.basket VALUES (NULL, $cid, 0, NULL, 0, '$date')"; 
						    	$resultBasket = mysqli_query($conn, $sqlBasket);

						    	//making change here
								if ($resultBasket) {
						    		//echo "Basket creation failed!<br>";
						    	} else {
						    		//echo "Basket creation successful!<br>";

						    		$sqlBalance = "UPDATE $database.customer SET currentbasket = $bid WHERE customerid = $cid";
						    		$resultBalance = mysqli_query($conn, $sqlBalance);

						    		if ($resultBalance) {
						    			//echo "Current basket update successful!<br>";

						    			/* update product count */
						    			$sqlProduct = "SELECT productid, productquantity FROM $database.basketitem bi WHERE bi.basketid = $bid";
						    			$resultProduct = mysqli_query($conn, $sqlProduct);

						    			if ($resultProduct && mysqli_num_rows($resultProduct) > 0) {
						    				while ($row = mysqli_fetch_assoc($resultProduct)) {
						    					$sqlUpdateCount = "UPDATE $database.product SET soldcount = soldcount + " . $row['productquantity'] . " WHERE productid = " . $row['productid'];
						    					$resultUpdateCount = mysqli_query($conn, $sqlUpdateCount);

						    					if ($resultUpdateCount) {
						    						//echo "Product " . $row['productid'] . " update successful!<br>";
						    					} else {
						    						//echo "Product " . $row['productid'] . " update FAILED!<br>";
						    					}
						    				}

						    				
						    			} else {
						    				//echo "No products found to update?!<br>";
						    			}

						    		} else {
						    			//echo "Currentbasket update failed!<br>";
						    		}
						    	}

					    	}

					    }
					} else {
						//echo "Finding transaction ID failed!<br>";
					}
				}
			}
			
			
			

			mysqli_close($conn);
		?>


	</body>
</html>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
</body>
</html>