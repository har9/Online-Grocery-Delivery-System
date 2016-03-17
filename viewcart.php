<html>
	<head>
		<title>thegrocerystore.com</title>
		<script type="text/javascript">
			function generateList() {
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
				$basketid = $_GET["basketid"];
				$sql = "";

				if ($basketid) {
					$sql = "SELECT * FROM $database.basket b, $database.basketitem bi, $database.product p, $database.customer c WHERE b.basketid = $basketid AND c.customerid = $cid AND b.basketid = bi.basketid AND bi.productid = p.productid";
				} else {
					$sql = "SELECT * FROM $database.basket b, $database.basketitem bi, $database.product p, $database.customer c WHERE c.customerid = $cid AND c.currentbasket = b.basketid AND c.currentbasket = bi.basketid AND bi.productid = p.productid";
				}

				
				$result = mysqli_query($conn, $sql);

				$returnStr = "[";

				if ($result && mysqli_num_rows($result) > 0) {
				    // output data of each row
				    while($row = mysqli_fetch_assoc($result)) {
				        //echo "Customer name: " . $row["name"]. " - basketid: " . $row["basketid"] . " - productname: " . $row["productname"] . " - quantity: " . $row["productquantity"] . "<br>";
	   
				        $returnStr = $returnStr . "{" . "\"name\": " . "\"" . $row["name"] . "\"" . ",\n\"basketid\":" . $row["basketid"] . ",\n\"productid\":" . $row["productid"] . ",\n\"productname\":" . "\"" . $row["productname"] . "\"" . ",\n\"quantity\":" . "\"" . $row["productquantity"] . "\"" . ",\n\"price\":" . $row["price"] . ",\n\"isonsale\":" . $row["isonsale"] . ",\n\"saleprice\":" . $row["saleprice"] . ",\n\"imageurl\":" . "\"" . $row["imageurl"] . "\"" . "}, \n";
				    }


				} else {
				    echo "0 results for finding cart!<br>";
				}

				mysqli_close($conn);

				$returnStr = $returnStr . "]";
				echo $returnStr;
				?>

				var listContainer = document.createElement("div"); 

				for (var i = 0; i < entries.length; i++) {
					var item = entries[i];

		            var listElement = document.createElement("div");
		            listElement.setAttribute("id", item["productid"]);
		            listElement.setAttribute("style", "border: 1px solid black; margin:15px 15px 0px 15px");

		            listContainer.appendChild(listElement);

		            for (var property in item) {
					    if (item.hasOwnProperty(property)) {
			                var listItem = document.createElement("p");
			                var needsTotal = false;

			                if (property === "productname") {
			                	listItem.innerHTML = item[property];
			                } else if (property === "price") {
			                	listItem.innerHTML = "Unit Cost: $" + item[property].toFixed(2);
			                	needsTotal = true;
			                } else if (property === "quantity") {
			                	listItem.innerHTML = "Quantity: " + item[property];
			                } else if (property === "imageurl") {
			                	listItem.innerHTML = "<img src=" + item[property] + ">";
			                }

			                //<a onmouseover=\"document.getElementById('" + property + "').style.display='';\" onclick=\"document.getElementById('" + property + "').style.display='none';\" href=\"#\" id=\"showsubcategory\" >" + property + "</a>";

			                // add listItem to the listElement
			                listElement.appendChild(listItem);
			                if (needsTotal) {
				                var totalPriceItem = document.createElement("p");
				                totalPriceItem.innerHTML = "Total Cost: $" + (item["price"]*item["quantity"]).toFixed(2);
				                listElement.appendChild(totalPriceItem);
				            }
		            	}

		            }

		            var removeButton = document.createElement("form");
					removeButton.setAttribute("id", item["productid"] + "form");
					removeButton.setAttribute("name", item["productid"] + "form");
					removeButton.setAttribute("action", "removefromcart.php");
					removeButton.setAttribute("method", "get");
					listElement.appendChild(removeButton);

					removeButton.innerHTML = "<input type=\"text\" name=\"customerid\" value='<?php echo $_GET['customerid'] ?>' style=\"display:none\"><input type=\"text\" name=\"productid\" value='" + item["productid"] + "' style=\"display:none\"><input type=\"submit\" value=\"Remove\"/>";

		            /*listElement.innerHTML += "<a id=\"" + "remove" + item["name"] + "\" onclick=\"<?php include 'addtocart.php' ?>\" href=\"#\">Remove</a>";*/
				}
	            

	            // add it to the page
	            document.getElementsByTagName("body")[0].appendChild(listContainer);

			}
		</script>
	</head>
    <body background="back.jpg";
    background-size: 1200px 1500px;
    background-repeat: no-repeat;>
   		<?php 
			$cid = $_GET["customerid"];
			echo "Get CID: " . $cid . "<br>";
			$_SESSION["cid"] = $_GET["customerid"];
			$_SESSION["searchtext"] = "";

			include 'loggedinheader.php';
		?>

		<script type="text/javascript">
			generateList();
		</script>

		<form action="checkout.php" method="get">
			<input type="text" name="customerid" value="<?php echo $_SESSION["cid"] ?>">
			<input type="submit" value="Checkout">
		</form>
		

	</body>
</html>
