<html>
	<head>
		<title>Transaction History</title>
		<script type="text/javascript">
			function combo(thelist, theinput)
			{
			  var idx = thelist.selectedIndex;
			  var content = thelist.options[idx].innerHTML;
			  numItems = content;
			  console.log("Content in combo: " + content);
			  return content;
			}

			function comboInit(thelist, theinput)
			{
			  var idx = thelist.selectedIndex;
			  var content = thelist.options[idx].innerHTML;
			  numItems = content;
			  console.log("Content in init: " + content);
			  return content;
			}

			function generateTransactions() {
				var entries = 
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

				$cid = $_GET["customerid"];
				$startdate = $_GET["startdate"];
				$enddate = $_GET["enddate"];
				$sql = "";

				if ($startdate && $enddate) {
					$sql = "SELECT * FROM $database.transaction t, $database.bill b, $database.customer c, $database.basket ba WHERE c.customerid = $cid AND c.customerid = t.customerid AND t.transactionid = b.transactionid AND ba.basketid = t.basketid AND t.ordertime >= '$startdate' AND t.ordertime <= '$enddate'";
				} else if ($startdate && !$enddate) {
					$sql = "SELECT * FROM $database.transaction t, $database.bill b, $database.customer c, $database.basket ba WHERE c.customerid = $cid AND c.customerid = t.customerid AND t.transactionid = b.transactionid AND ba.basketid = t.basketid AND t.ordertime >= '$startdate'";
				} else if (!$startdate && $enddate) {
					$sql = "SELECT * FROM $database.transaction t, $database.bill b, $database.customer c, $database.basket ba WHERE c.customerid = $cid AND c.customerid = t.customerid AND t.transactionid = b.transactionid AND ba.basketid = t.basketid AND t.ordertime <= '$enddate'";
				} else {
					$sql = "SELECT * FROM $database.transaction t, $database.bill b, $database.customer c, $database.basket ba WHERE c.customerid = $cid AND c.customerid = t.customerid AND t.transactionid = b.transactionid AND ba.basketid = t.basketid";
				}
				
				$result = mysqli_query($conn, $sql);

				$returnStr = "[";

				if ($result && mysqli_num_rows($result) > 0) {
				    // output data of each row
				    while($row = mysqli_fetch_assoc($result)) {
				        //echo "Customer name: " . $row["name"]. " - basketid: " . $row["basketid"] . " - productname: " . $row["productname"] . " - quantity: " . $row["productquantity"] . "<br>";
	   
				        $returnStr = $returnStr . "{" . "\"transactionid\": " . $row["transactionid"] . ",\n\"basketid\": " . $row["basketid"] . ",\n\"ordertime\": " . "\"" . $row["ordertime"] . "\"" . ",\n\"estimatetimeofarrival\":" . "\"" . $row["estimatetimeofarrival"] . "\"" . ",\n\"deliverydate\":" . "\"" . $row["deliverydate"] . "\"" . ",\n\"paymentflag\": " . $row["paymentflag"] . ",\n\"totalitemcost\":" . $row["totalitemcost"] . ",\n\"costwithtax\":" . $row["costwithtax"] . ",\n\"deliverycharge\":" . $row["deliverycharge"] . ",\n\"tip\":" . "\"" . $row["tip"] . "\"" . ",\n\"transactiontotal\":" . $row["transactiontotal"] . ",\n\"paidflag\":" . $row["paidflag"] . ",\n\"isstandingorder\":" . $row["isstandingorder"] . ",\n\"standingordertype\":" . "\"" . $row["standingordertype"] . "\"" . "}, \n";
				    }


				} else {
				    echo "0 results for finding transactions!<br>";
				}

				mysqli_close($conn);

				$returnStr = $returnStr . "]";
				echo $returnStr;
				?>

				var listContainer = document.createElement("div"); 

				for (var i = 0; i < entries.length; i++) {
					var item = entries[i];

		            var listElement = document.createElement("div");
		            listElement.setAttribute("id", item["transactionid"]);
		            listElement.setAttribute("style", "border: 1px solid black; margin:15px 15px 0px 15px");

		            listContainer.appendChild(listElement);

		            for (var property in item) {
					    if (item.hasOwnProperty(property)) {
			                var listItem = document.createElement("p");
			                var needsTotal = false;

			                if (property === "ordertime") {
			                	listItem.innerHTML = "Time of Order: " + item[property];
			                } else if (property === "deliverydate") {
			                	if (item[property]) {
			                		listItem.innerHTML = "Delivery date: " + item[property];
			                	} else {
			                		listItem.innerHTML = "Estimated Time of Arrival: " + item["estimatetimeofarrival"];
			                	}
			                }else if (property === "paidflag") {
			                	if (item[property] == 1) {
			                		listItem.innerHTML = "PAID";
			                	} else {
			                		listItem.innerHTML = "NOT PAID";
			                	}
			                } else if (property === "paymentflag") {
			                	if (item[property] == 0) {
			                		listItem.innerHTML = "Method of payment: Credit Card";
			                	} else {
			                		listItem.innerHTML = "Method of payment: Check";
			                	}
			                } else if (property === "totalitemcost") {
			                	listItem.innerHTML = "Total Item Cost: $" + item[property].toFixed(2);
			                } else if (property === "costwithtax") {
			                	listItem.innerHTML = "Cost With Tax: $" + item[property].toFixed(2);
			                } else if (property === "deliverycharge") {
			                	listItem.innerHTML = "Delivery Charge: $" + item[property].toFixed(2);
			                } else if (property === "transactiontotal") {
			                	listItem.innerHTML = "Transaction Total: $" + item[property].toFixed(2);
			                } else if (property === "tip") {
			                	if (item[property]) {
			                		listItem.innerHTML = "Tip: $" + item[property].toFixed(2);
			                	} else {
			                		listItem.innerHTML = "Tip: $0.00";
			                	}
			                }

			                //<a onmouseover=\"document.getElementById('" + property + "').style.display='';\" onclick=\"document.getElementById('" + property + "').style.display='none';\" href=\"#\" id=\"showsubcategory\" >" + property + "</a>";

			                // add listItem to the listElement
			                listElement.appendChild(listItem);
			                /*if (needsTotal) {
				                var totalPriceItem = document.createElement("p");
				                totalPriceItem.innerHTML = "Total Cost: $" + (item["price"]*item["quantity"]).toFixed(2);
				                listElement.appendChild(totalPriceItem);
				            }*/
		            	}

		            }

					/*var setStandingButton = document.createElement("select");
		            setStandingButton.setAttribute("onChange", "combo(this)");
		            setStandingButton.setAttribute("onMouseOut", "comboInit(this)");
					setStandingButton.setAttribute("name", item["basketid"] + "standingordertype");
					setStandingButton.setAttribute("form", item["basketid"] + "orderinformation");
					setStandingButton.innerHTML = "Standing Order: <option>None</option><option>Daily</option><option>Weekly</option><option>Monthly</option>";
					if (item["standingordertype"] == "None") {
						setStandingButton.options.selectedIndex = 0;
					} else if (item["standingordertype"] == "Daily") {
						setStandingButton.options.selectedIndex = 1;
					} else if (item["standingordertype"] == "Weekly") {
						setStandingButton.options.selectedIndex = 2;
					} else if (item["standingordertype"] == "Monthly") {
						setStandingButton.options.selectedIndex = 3;
					} 
					//setStandingButton.options[setStandingButton.options.selectedIndex].selected = true;
					listElement.appendChild(setStandingButton);
					

		            var setStandingForm = document.createElement("form");
		            setStandingForm.setAttribute("id", item["basketid"] + "orderinformation");
					setStandingForm.setAttribute("name", item["basketid"] + "orderinformation");
					setStandingForm.setAttribute("action", "updateorder.php");
					setStandingForm.setAttribute("method", "get");
					setStandingForm.innerHTML = "<input type=\"number\" name=\"customerid\" value='<?php echo $_GET['customerid'] ?>'/><input type=\"number\" name=\"basketid\" value='" + item["basketid"] + "'/><input type=\"submit\" value=\"Update Order\"/>";

					listElement.appendChild(setStandingForm);
					*/
				}
	            

	            // add it to the page
	            document.getElementsByTagName("body")[0].appendChild(listContainer);

			}
		</script>
	</head>
	<body>
		<?php 
			$cid = $_GET["customerid"];
			echo "Get CID: " . $cid . "<br>";
			$_SESSION["cid"] = $_GET["customerid"];
			$_SESSION["startdate"] = $_GET["startdate"];
			$_SESSION["enddate"] = $_GET["enddate"];
			$_SESSION["searchtext"] = "";

			include 'loggedinheader.php';
		?>

		<form action="transactionhistory.html">
			Start Date: <input type="date" name="startdate" value="<?php echo $_SESSION["startdate"] ?>">
			End Date: <input type="date" name="enddate" value="<?php echo $_SESSION["enddate"] ?>">
			<input type="number" name="customerid" value="<?php echo $_SESSION["cid"] ?>" style="display:none">
			<input type="submit" value="Search Transactions"><br><br>
		</form>
        
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

				$cid = $_GET["customerid"];
				$startdate = $_GET["startdate"];
				$enddate = $_GET["enddate"];
				$sql = "";

				if ($startdate && $enddate) {
					$sql = "SELECT * FROM $database.transaction t, $database.bill b, $database.customer c, $database.basket ba WHERE c.customerid = $cid AND c.customerid = t.customerid AND t.transactionid = b.transactionid AND ba.basketid = t.basketid AND t.ordertime >= '$startdate' AND t.ordertime <= '$enddate'";
				} else if ($startdate && !$enddate) {
					$sql = "SELECT * FROM $database.transaction t, $database.bill b, $database.customer c, $database.basket ba WHERE c.customerid = $cid AND c.customerid = t.customerid AND t.transactionid = b.transactionid AND ba.basketid = t.basketid AND t.ordertime >= '$startdate'";
				} else if (!$startdate && $enddate) {
					$sql = "SELECT * FROM $database.transaction t, $database.bill b, $database.customer c, $database.basket ba WHERE c.customerid = $cid AND c.customerid = t.customerid AND t.transactionid = b.transactionid AND ba.basketid = t.basketid AND t.ordertime <= '$enddate'";
				} else {
					$sql = "SELECT * FROM $database.transaction t, $database.bill b, $database.customer c, $database.basket ba WHERE c.customerid = $cid AND c.customerid = t.customerid AND t.transactionid = b.transactionid AND ba.basketid = t.basketid";
				}
				
				$result = mysqli_query($conn, $sql);

				$returnStr = "[";

				if ($result && mysqli_num_rows($result) > 0) {
				    // output data of each row
				    while($row = mysqli_fetch_assoc($result)) {
				        //echo "Customer name: " . $row["name"]. " - basketid: " . $row["basketid"] . " - productname: " . $row["productname"] . " - quantity: " . $row["productquantity"] . "<br>";
	   
				       // $returnStr = $returnStr . "{" . "\"Transaction #\": " . $row["transactionid"] . ",\n\"OrderTime\": " . "\"" . $row["ordertime"] . "\"" . ",\n\"EstimatedTimeofArrival\":" . "\"" . $row["estimatetimeofarrival"] . "\"" .  ",\n\"TotalItemCost\":" . $row["totalitemcost"] . ",\n\"CostwithTax\":" . $row["costwithtax"] . ",\n\"DeliveryCharge\":" . $row["deliverycharge"] . ",\n\"Tip\":" . "\"" . $row["tip"] . "\"" . ",\n\"Transactiontotal\":" . $row["transactiontotal"] . "}, \n";
						
						
						 $returnStr = $returnStr . "{" . "\n\"transactionid\": " . $row["transactionid"] .  ",\n\"ordertime\": " . "\"" . $row["ordertime"] . "\"" . ",\n\"estimatetimeofarrival\":" . "\"" . $row["estimatetimeofarrival"] . "\"" . ",\n\"deliverydate\":" . "\"" . $row["deliverydate"] . "\"" . ",\n\"paymentflag\": " . $row["paymentflag"] . ",\n\"totalitemcost\":" . $row["totalitemcost"] . ",\n\"costwithtax\":" . $row["costwithtax"] . ",\n\"deliverycharge\":" . $row["deliverycharge"] . ",\n\"tip\":" . "\"" . $row["tip"] . "\"" . ",\n\"transactiontotal\":" . $row["transactiontotal"] . ",\n\"paidflag\":" . $row["paidflag"] . ",\n\"isstandingorder\":" . $row["isstandingorder"] . "}, \n";
						
						
				    }


				} else {
				    echo "0 results for finding transactions!<br>";
				}

				mysqli_close($conn);

				$returnStr = $returnStr . "]";
				echo $returnStr;
				?>

	</body>
</html>
