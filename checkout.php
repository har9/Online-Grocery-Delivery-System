<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
	<body background="chkout.png";
    background-size: 1200px 1500px;
    background-repeat: no-repeat;>
	<?php
		$cid = $_GET["customerid"];
		echo "Get CID: " . $cid . "<br>";
		$_SESSION["cid"] = $_GET["customerid"];
		$_SESSION["searchtext"] = "";

		include 'loggedinheader.php';
	?>

	<form id="orderinformation" name= "orderinformation" action="placeorderbefore.php" method="post" align="left">
		<fieldset>
		<legend>Order Information</legend>
		<?php
			/*function getDrivingDistance($address) {
			    $url = 'http://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=Computer+Science+Instructional+Center&';
			 	$url = $url . 'destinations=' . urlencode($address);
			 	//echo "URL: " . $url . "<br>";
			 	$data = file_get_contents($url);

				if(!$data) echo "FAILED!!!!<br>";
			 	//echo $data . "<br>";

			 	$data = json_decode($data);
			 	
			 	$time = 0;
				$distance = 0;
				foreach($data->rows[0]->elements as $road) {
				    $time += $road->duration->value;
				    $distance += $road->distance->value;
				}
				echo "TIME: " . $time . "<br>";

				return $distance;
			}*/

			function combo($thelist, $theinput) {
			  $idx = $thelist.selectedIndex;
			  $content = $thelist.options(idx).innerHTML;
			}

			function comboInit($thelist, $theinput) {
			  $idx = $thelist.selectedIndex;
			  $content = $thelist.options(idx).innerHTML;
			}

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

			$sql = "SELECT * FROM $database.basket b, $database.basketitem bi, $database.product p, $database.customer c WHERE c.customerid = $cid AND c.currentbasket = b.basketid AND c.currentbasket = bi.basketid AND bi.productid = p.productid";
			$result = mysqli_query($conn, $sql);

			//$returnStr = "[";
			$totalPrice = 0;
			$priceWithTax = 0;
			$tax = 0;
			$deliveryCost = 0;
			$isCheck = false;
			$custAddress = "";
			//$basketid = -1;

			if ($result && mysqli_num_rows($result) > 0) {
			    // output data of each row
			    while($row = mysqli_fetch_assoc($result)) {
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

			    	if ($row["paymentflag"]) {
			    		$isCheck = true;
			    	}
			    	$custAddress = $row["address"];
			    	$_SESSION["basketid"] = $row["basketid"];
			    }

			    

			    /* FIND DELIVERY TIME AND DISTANCE */
			    $url = 'http://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=Computer+Science+Instructional+Center&';
			 	$url = $url . 'destinations=' . urlencode($custAddress);
			 	//echo "URL: " . $url . "<br>";
			 	$data = file_get_contents($url);

				if(!$data) echo "FAILED!!!!<br>";
			 	//echo $data . "<br>";

			 	$data = json_decode($data);
			 	
			 	$time = 0;
				$distance = 0;
				foreach($data->rows[0]->elements as $road) {
				    $time += $road->duration->value;
				    $distance += $road->distance->value;
				}

			    $drivingDistMeters = $distance;
			    echo "Driving distance: " . $drivingDistMeters . "<br>";
			    $drivingDistMiles = $drivingDistMeters/1000 * 0.621371;
			    echo "Driving distance in miles: " . $drivingDistMiles . "<br>";

			    $sqlDist = "SELECT price FROM $database.deliverypricing WHERE $drivingDistMiles >= rangestart AND $drivingDistMiles < rangeend";
			    $resultDist = mysqli_query($conn, $sqlDist);

			    if ($resultDist && mysqli_num_rows($resultDist) > 0) {
			    	$price = 0;
			    	// output data of each row
			    	while($row = mysqli_fetch_assoc($resultDist)) {
			    		$price = $row["price"];

			    	}

			    	echo "Total: $" . round($totalPrice, 2) . "<br>";
				    echo "Tax: $" . $tax . "<br>";
				    echo "Total with Tax: $" . round($priceWithTax, 2) . "<br><br>";
				    echo "Delivery Address: " . $custAddress . "<br>";
			    	echo "Delivery price: $" . $price . "<br>";
			    	echo "Total Price: $" . round($priceWithTax + $price, 2) . "<br>";
			    	$_SESSION['totalitemcost'] = round($totalPrice, 2);
			    	$_SESSION['costwithtax'] = round($priceWithTax, 2);
			    	$_SESSION['deliverycharge'] = $price;
			    	$_SESSION['tripduration'] = $time;

			    	echo "<input type=\"number\" name=\"customerid\" value=\"" . $_SESSION['cid'] . "\">";
					echo "<input type=\"number\" name=\"basketid\" value=\"" . $_SESSION['basketid'] . "\">";
					echo "<input type=\"number\" name=\"totalitemcost\" value=\"" . $_SESSION['totalitemcost'] . "\">";
					echo "<input type=\"number\" name=\"costwithtax\" value=\"" . $_SESSION['costwithtax'] . "\">";
					echo "<input type=\"number\" name=\"deliverycharge\" value=\"" . $_SESSION['deliverycharge'] . "\">";
					echo "<input type=\"number\" name=\"tripduration\" value=\"" . $_SESSION['tripduration'] . "\">";
					
					if ($isCheck) {
				    	echo "<div>Check Information<br>";
				    	echo "Bank Account Number: <input type=\"number\" name=\"bankaccountnumber\">";
				    	echo "Routing Number: <input type=\"number\" name=\"bankroutingnumber\">";
				    	echo "</div>";
				    } else {
				    	//echo "<div>Check Information<br>";
				    	echo "<input type=\"number\" name=\"bankaccountnumber\" style=\"display:none\">";
				    	echo "<input type=\"number\" name=\"bankroutingnumber\" style=\"display:none\">";
				    	//echo "</div>";
				    }

				    echo "<br><br>";
				    echo "Set Standing Order: ";
				    echo "<select name=\"standingordertype\" form=\"orderinformation\" onChange=\"combo(this)\" onMouseOut=\"comboInit(this)\">";
				    echo "<option>None</option><option>Daily</option><option>Weekly</option><option>Monthly</option>";
				    echo "</select>";

				    echo "<br><br>";
					echo "<input type=\"submit\" value=\"Place Order\">";
			    } else {
			    	echo "Your address is not within our delivery area!<br>";
			    }
			} else {
			    echo "0 results for finding cart!<br>";
			}

			mysqli_close($conn);
		?>
		<!--<input type="text" name="customerid" value="<?php echo $_SESSION['cid'];?>">
		<input type="text" name="basketid" value="<?php echo $_SESSION['basketid'];?>">
		<input type="text" name="totalitemcost" value="<?php echo $_SESSION['totalitemcost'];?>">
		<input type="text" name="costwithtax" value="<?php echo $_SESSION['costwithtax'];?>">
		<input type="text" name="deliverycharge" value="<?php echo $_SESSION['deliverycharge'];?>">
		<input type="text" name="tripduration" value="<?php echo $_SESSION['tripduration'];?>">

		<br>
		<br>
		<input type="submit" value="Place Order"> -->
		<br>
		</fieldset>
	</form>

	<script type="text/javascript" language="JavaScript"><!--
		function HideContent(d) {
			document.getElementById(d).style.display = "none";
		}
		function ShowContent(d) {
			document.getElementById(d).style.display = "block";
		}
		function ReverseDisplay(d) {
			if(document.getElementById(d).style.display == "none") { 
				document.getElementById(d).style.display = "block"; 
			} else { 
				document.getElementById(d).style.display = "none"; 
			}
		}
	//--></script>

</body>


