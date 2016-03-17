<html>
	<head>
		<title>thegrocerystore.com</title>
		<script type="text/javascript">
			function showSearchResults() {
				var results = 
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

					$searchText = $_GET["searchtext"];
					$minprice = $_GET["minprice"];
					$maxprice = $_GET["maxprice"];
					$saleonly = $_GET["saleonly"];
					$sql = "";
					$lowerSearchText = strtolower($searchText);

					if (!$searchText) {
						//echo "No search text!<br>";
						//exit();
					} else {

					}

					if (!$minprice) {
						$minprice = 0;
					}
					if (!$maxprice) {
						$maxprice = PHP_INT_MAX;
					}
					if ($saleonly) {
						$sql = "SELECT * FROM $database.product p, $database.productcategory c WHERE p.categoryid = c.categoryid 
							AND (p.price >= $minprice) AND (p.price <= $maxprice) AND (p.isonsale = 1)
							AND (LOWER(p.productname) LIKE '%$lowerSearchText%' OR LOWER(c.category) LIKE '%$lowerSearchText%' OR LOWER(c.subcategory) LIKE '%$lowerSearchText%')";
					} else {
						$sql = "SELECT * FROM $database.product p, $database.productcategory c WHERE p.categoryid = c.categoryid 
							AND (p.price >= $minprice) AND (p.price <= $maxprice)
							AND (LOWER(p.productname) LIKE '%$lowerSearchText%' OR LOWER(c.category) LIKE '%$lowerSearchText%' OR LOWER(c.subcategory) LIKE '%$lowerSearchText%')";
					}
					
					
					$result = mysqli_query($conn, $sql);

					$returnStr = "[";

					if ($result && mysqli_num_rows($result) > 0) {
					    // output data of each row
					    while($row = mysqli_fetch_assoc($result)) {
					        //echo "Product name: " . $row["name"]. " - price: " . $row["price"] . " - category: " . $row["category"] . " - subcategory: " . $row["subcategory"] . "<br>";
					    	$saleprice = $row["saleprice"];
					    	if (!$saleprice) {
					    		$saleprice = "null";
					    	}

					        $returnStr = $returnStr . "{" . "\"id\": " . $row["productid"] . ",\n\"name\": " . "\"" . $row["productname"] . "\"" . ",\n\"price\":" . $row["price"] . ",\n\"category\":" . "\"" . $row["category"] . "\"" . ",\n\"subcategory\":" . "\"" . $row["subcategory"] . "\"" . ",\n\"isonsale\":" . $row["isonsale"] . ",\n\"saleprice\":" . $saleprice . ",\n\"imageurl\":" . "\"" . $row["imageurl"] . "\"" . "}, \n";
					    }
					} else {
					    //echo "0 results for products";
					}

					mysqli_close($conn);

					$returnStr = $returnStr . "]";
					echo $returnStr;
				?>;

				console.log(results);

				var listContainer = document.createElement("div"); 

				for (var i = 0; i < results.length; i++) {
					var item = results[i];

					// Make the list itself which is a <ul>
		            var listElement = document.createElement("div");
		            listElement.setAttribute("id", item["name"]);
		            listElement.setAttribute("style", "border: 1px solid black; margin:15px 15px 0px 15px");

		            listContainer.appendChild(listElement);

		            /*for (var property in item) {
					    if (item.hasOwnProperty(property)) {
			                var listItem = document.createElement("p");

			                if (property === "productid") {
			                	listItem.innerHTML = "Barcode: " . item[property];
			                } else if (property === "name") {
			                	listItem.innerHTML = item[property];
			                } else if (property === "price") {
			                	listItem.innerHTML = "$" + item[property].toFixed(2);
			                } else if (property === "imageurl") {
			                	listItem.innerHTML = "<img src=" + item[property] + ">";
			                }

			                //<a onmouseover=\"document.getElementById('" + property + "').style.display='';\" onclick=\"document.getElementById('" + property + "').style.display='none';\" href=\"#\" id=\"showsubcategory\" >" + property + "</a>";

			                // add listItem to the listElement
			                listElement.appendChild(listItem);
		            	}

		            }*/

		            listElement.innerHTML += "Barcode: " + item["id"] + "<br>";
			        listElement.innerHTML += "<p>" + item["name"] + "</p><br>";
		            if (item["isonsale"] == 1) {
		            	listElement.innerHTML += "<p>$" + item["saleprice"].toFixed(2) + "</p>";
		            	listElement.innerHTML += "<img src='https://www.bubbablueonline.com.au/wp-content/uploads/sale.png' alt='ON SALE' style='width:100px;height:100px'><br>";
		            } else {
		            	listElement.innerHTML += "<p>$" + item["price"].toFixed(2) + "</p><br>";
		            }
		            listElement.innerHTML += "<p><img src='" + item["imageurl"] + "'></p>";
				}
	            

	            // add it to the page
	            document.getElementsByTagName("body")[0].appendChild(listContainer);

			}
		</script>
	</head>

	<body>

		<?php 
			$_SESSION["searchtext"] = $_GET["searchtext"];

			include 'header.php'; 
		?>

		<form action='productsearch.php' id='productsearchform'>
			Search Term: <input type='text' name='searchtext'><br> 
			Minimum price: <input type='number' name='minprice'>   
			Maximum price: <input type='number' name='maxprice'>   
			<input type='hidden' name='saleonly' value='0'>
			<input type='checkbox' name='saleonly' value='1'> Sale Items Only<br>
			<input type='submit' value='Search Products'><hr><br><br>
		</form>

		<script type="text/javascript">
			showSearchResults();
		</script>		
		  
		<?php include 'footer.php'; ?>

	</body>

</html>