  <html>
	<head>
		<title>View Orders</title>
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
			function generateOrders() {
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
				$sql = "SELECT * FROM $database.customer c, $database.basket ba WHERE c.customerid = $cid AND ba.customerid = $cid";
				$result = mysqli_query($conn, $sql);
				$returnStr = "[";
				if ($result && mysqli_num_rows($result) > 0) {
				    // output data of each row
				    while($row = mysqli_fetch_assoc($result)) {
				        //echo "Customer name: " . $row["name"]. " - basketid: " . $row["basketid"] . " - productname: " . $row["productname"] . " - quantity: " . $row["productquantity"] . "<br>";
				        $bid = $row["basketid"];
				        $isstandingorder = $row["isstandingorder"];
				        //$standingordertype = $row["standingordertype"];
				        $haltstandingorder = $row["haltstandingorder"];
				        $sqlCost = "SELECT * FROM $database.basketitem bi, $database.product p WHERE $bid = bi.basketid AND bi.productid = p.productid";
						$resultCost = mysqli_query($conn, $sqlCost);
						$totalPrice = 0;
						$priceWithTax = 0;
						$tax = 0;
						$ordertime = NULL;
						$estimatetimeofarrival = NULL;
						$deliverydate = NULL;
						if ($resultCost && mysqli_num_rows($resultCost) > 0) {
						    // output data of each row
						    while($row = mysqli_fetch_assoc($resultCost)) {
						        //echo "Customer name: " . $row["name"]. " - basketid: " . $row["basketid"] . " - productname: " . $row["productname"] . " - quantity: " . $row["productquantity"] . "<br>";
						        $tempPrice = 0;
						    	if ($row["isonsale"]) {
						    		$tempPrice = $row["saleprice"] * $row["productquantity"];
						    	} else {
						    		$tempPrice = $row["price"] * $row["productquantity"];
						    	}
						    	$totalPrice = $totalPrice + $tempPrice;
						    	if ($row["taxable"]) {
						    		$tax = $tax + ($tempPrice * 0.06);
						    		$priceWithTax = $priceWithTax + ($tempPrice * 1.06);
						    	} else {
						    		$priceWithTax = $priceWithTax + $tempPrice;
						    	}
						    	//$_SESSION["basketid"] = $row["basketid"];
						    }
						    $sqlTime = "SELECT ordertime, estimatetimeofarrival, deliverydate FROM $database.transaction t WHERE t.basketid = $bid";
						    $resultTime = mysqli_query($conn, $sqlTime);
						    if ($resultTime && mysqli_num_rows($resultTime) > 0) {
						    	while($row = mysqli_fetch_assoc($resultTime)) {
						    		$ordertime = $row["ordertime"];
									$estimatetimeofarrival = $row["estimatetimeofarrival"];
									$deliverydate = $row["deliverydate"];
						    	}
						    	$returnStr = $returnStr . "{" . "\"basketid\": " . $bid . ",\n\"ordertime\": " . "\"" . $ordertime . "\"" . ",\n\"estimatetimeofarrival\":" . "\"" . $estimatetimeofarrival . "\"" . ",\n\"deliverydate\":" . "\"" . $deliverydate . "\"" . ",\n\"totalitemcost\":" . round($totalPrice, 2) . ",\n\"costwithtax\":" . round($priceWithTax, 2) . ",\n\"isstandingorder\":" . $isstandingorder . ",\n\"haltstandingorder\":" . $haltstandingorder . "}, \n";
						    } else {
						    	//echo "Transaction date finding failed!<br>";
						    }
						    
						} else {
							//echo "Basket item cost query failed!<br>";
						}
				        
				    }
				} else {
				    echo "0 results for finding orders!<br>";
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
		            listElement.setAttribute("style", "border: 1px solid black; margin:15px 15px 0px 15px; background-color:#FFFFFF");
		            listContainer.appendChild(listElement);
		            var viewItemsButton = document.createElement("form");
		            viewItemsButton.setAttribute("id", item["basketid"] + "viewform");
		            viewItemsButton.setAttribute("name", item["basketid"] + "viewform");
					viewItemsButton.setAttribute("action", "viewcart.php");
					viewItemsButton.setAttribute("method", "get");
					viewItemsButton.innerHTML = "<input type=\"number\" name=\"customerid\" value='<?php echo $_GET['customerid'] ?>' style=\"display:none\"/><input type=\"number\" name=\"basketid\" value='" + item["basketid"] + "'style=\"display:none\"/><input type=\"submit\" value=\"View Order Contents\"/>";
					listElement.appendChild(viewItemsButton);
		            for (var property in item) {
					    if (item.hasOwnProperty(property)) {
			                var listItem = document.createElement("p");
			                var needsTotal = false;
			                if (property === "ordertime") {
			                	listItem.innerHTML = "Time of Order Creation: " + item[property];
			                } else if (property === "deliverydate") {
			                	if (item[property]) {
			                		listItem.innerHTML = "Last Delivery Date: " + item[property];
			                	} else {
			                		listItem.innerHTML = "Estimated Time of Arrival: " + item["estimatetimeofarrival"];
			                	}
			                } else if (property === "totalitemcost") {
			                	listItem.innerHTML = "Total Item Cost: $" + item[property].toFixed(2);
			                } else if (property === "costwithtax") {
			                	listItem.innerHTML = "Cost With Tax: $" + item[property].toFixed(2);
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
					var setStandingButton = document.createElement("select");
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
					if (item["standingordertype"] && item["standingordertype"] != "None") {
						var hiddenBox = document.createElement("input");
						hiddenBox.setAttribute("type", "hidden");
						hiddenBox.setAttribute("name", item["basketid"] + "holdorder");
						hiddenBox.setAttribute("value", "0");
						var holdOrderBox = document.createElement("input");
						holdOrderBox.setAttribute("type", "checkbox");
						holdOrderBox.setAttribute("name", item["basketid"] + "holdorder");
						holdOrderBox.setAttribute("form", item["basketid"] + "orderinformation");
						holdOrderBox.setAttribute("value", "1");
						if (item["haltstandingorder"]) {
							holdOrderBox.setAttribute("checked", "true");
						}
						var label = document.createElement('label');
						label.htmlFor = item["basketid"] + "holdorder";
						label.appendChild(document.createTextNode('Put Order On Hold'));
						listElement.appendChild(hiddenBox);
						listElement.appendChild(holdOrderBox);
						listElement.appendChild(label);
					}
		            var setStandingForm = document.createElement("form");
		            setStandingForm.setAttribute("id", item["basketid"] + "orderinformation");
					setStandingForm.setAttribute("name", item["basketid"] + "orderinformation");
					setStandingForm.setAttribute("action", "updateorder.php");
					setStandingForm.setAttribute("method", "get");
					setStandingForm.innerHTML = "<input type=\"number\" name=\"customerid\" value='<?php echo $_GET['customerid'] ?>' style=\"display:none\"/><input type=\"number\" name=\"basketid\" value='" + item["basketid"] + "'style=\"display:none\"/><input type=\"submit\" value=\"Update Order\"/>";
					listElement.appendChild(setStandingForm);
					
				}
	            
	            // add it to the page
	            document.getElementsByTagName("body")[0].appendChild(listContainer);
			}
		</script>
	</head>
	<body bgcolor="#D1F2A5">
		<?php 
			$cid = $_GET["customerid"];
			//echo "Get CID: " . $cid . "<br>";
			$_SESSION["cid"] = $_GET["customerid"];
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
				$cid = $_GET["customerid"];
				$sql = "SELECT * FROM $database.customer c, $database.basket ba WHERE c.customerid = $cid AND ba.customerid = $cid";
				$result = mysqli_query($conn, $sql);
				$returnStr = "[";
				if ($result && mysqli_num_rows($result) > 0) {
				    // output data of each row
				    while($row = mysqli_fetch_assoc($result)) {
				        //echo "Customer name: " . $row["name"]. " - basketid: " . $row["basketid"] . " - productname: " . $row["productname"] . " - quantity: " . $row["productquantity"] . "<br>";
				        $bid = $row["basketid"];
				        $isstandingorder = $row["isstandingorder"];
				        //$standingordertype = $row["standingordertype"];
				        $haltstandingorder = $row["haltstandingorder"];
				        $sqlCost = "SELECT * FROM $database.basketitem bi, $database.product p WHERE $bid = bi.basketid AND bi.productid = p.productid";
						$resultCost = mysqli_query($conn, $sqlCost);
						$totalPrice = 0;
						$priceWithTax = 0;
						$tax = 0;
						$ordertime = NULL;
						$estimatetimeofarrival = NULL;
						$deliverydate = NULL;
						if ($resultCost && mysqli_num_rows($resultCost) > 0) {
						    // output data of each row
						    while($row = mysqli_fetch_assoc($resultCost)) {
						        //echo "Customer name: " . $row["name"]. " - basketid: " . $row["basketid"] . " - productname: " . $row["productname"] . " - quantity: " . $row["productquantity"] . "<br>";
						        $tempPrice = 0;
						    	if ($row["isonsale"]) {
						    		$tempPrice = $row["saleprice"] * $row["productquantity"];
						    	} else {
						    		$tempPrice = $row["price"] * $row["productquantity"];
						    	}
						    	$totalPrice = $totalPrice + $tempPrice;
						    	if ($row["taxable"]) {
						    		$tax = $tax + ($tempPrice * 0.06);
						    		$priceWithTax = $priceWithTax + ($tempPrice * 1.06);
						    	} else {
						    		$priceWithTax = $priceWithTax + $tempPrice;
						    	}
						    	//$_SESSION["basketid"] = $row["basketid"];
						    }
						    $sqlTime = "SELECT ordertime, estimatetimeofarrival, deliverydate FROM $database.transaction t WHERE t.basketid = $bid";
						    $resultTime = mysqli_query($conn, $sqlTime);
						    if ($resultTime && mysqli_num_rows($resultTime) > 0) {
						    	while($row = mysqli_fetch_assoc($resultTime)) {
						    		$ordertime = $row["ordertime"];
									$estimatetimeofarrival = $row["estimatetimeofarrival"];
									$deliverydate = $row["deliverydate"];
						    	}
						    	$returnStr = $returnStr . "{" . "\"basketid\": " . $bid . ",\n\"ordertime\": " . "\"" . $ordertime . "\"" . ",\n\"estimatetimeofarrival\":" . "\"" . $estimatetimeofarrival . "\"" . ",\n\"deliverydate\":" . "\"" . $deliverydate . "\"" . ",\n\"totalitemcost\":" . round($totalPrice, 2) . ",\n\"costwithtax\":" . round($priceWithTax, 2) . ",\n\"isstandingorder\":" . $isstandingorder . ",\n\"haltstandingorder\":" . $haltstandingorder . "}, \n";
						    } else {
						    	//echo "Transaction date finding failed!<br>";
						    }
						    
						} else {
							//echo "Basket item cost query failed!<br>";
						}
				        
				    }
				} else {
				    echo "0 results for finding orders!<br>";
				}
				mysqli_close($conn);
				$returnStr = $returnStr . "]";
				echo $returnStr;
			
		?>

		<script type="text/javascript">
			//generateOrders();
		</script>
		

	</body>
</html>