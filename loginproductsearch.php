<html>
	<head>
		<title>thegrocerystore.com</title>
		<script type="text/javascript">
			var numItems = 0;

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
					    //echo "0 results of products";
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
		            listElement.setAttribute("id", item["id"]);
		            listElement.setAttribute("style", "border: 1px solid black; margin:15px 15px 0px 15px");

		            listContainer.appendChild(listElement);

		            /*for (var property in item) {
					    if (item.hasOwnProperty(property)) {
			                var listItem = document.createElement("p");

			                if (property === "name") {
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

		            //ONLY FOR LOGGED IN CUSTOMERS
		            listElement.innerHTML += "Quantity: ";

					var selectBox = document.createElement("select");
					selectBox.setAttribute("name", item["id"] + "list");
					selectBox.setAttribute("onChange", "combo(this)");
					selectBox.setAttribute("onMouseOut", "comboInit(this)");
					selectBox.setAttribute("form", item["id"] + "form");
					selectBox.innerHTML = "<option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>";
					listElement.appendChild(selectBox);

					var addButton = document.createElement("form");
					addButton.setAttribute("id", item["id"] + "form");
					addButton.setAttribute("name", item["id"] + "form");
					addButton.setAttribute("action", "addtocart.php");
					addButton.setAttribute("method", "get");
					listElement.appendChild(addButton);

					/**/addButton.innerHTML = "<input type=\"text\" name=\"customerid\" value='<?php echo $_GET['customerid'] ?>'/><input type=\"text\" name=\"productid\" value='" + item["id"] + "'/><input type=\"submit\" value=\"Add To Cart\"/>";
					

		            /*listElement.innerHTML += "Quantity: <select name=\"list\" onChange=\"combo(this, 'theinput')\" onMouseOut=\"comboInit(this, 'theinput')\"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select> ";
		            listElement.innerHTML += "<a id=\"" + "add" + item["name"] + "\" onclick=\"<?php include 'addtocart.php' ?>\" href=\"#\">Add To Cart</a>";*/
				}
	            

	            // add it to the page
	            document.getElementsByTagName("body")[0].appendChild(listContainer);

			}

			function addToCart() {
				console.log("NumItems: " + numItems);
			}

			function combo(thelist, theinput)
			{
			  //theinput = document.getElementById(theinput);  
			  var idx = thelist.selectedIndex;
			  var content = thelist.options[idx].innerHTML;
			  //if (theinput) {
			  //	theinput.value = content;	
			  //}
			  numItems = content;
			  console.log("Content in combo: " + content);
			  return content;
			}

			function comboInit(thelist, theinput)
			{
			  //theinput = document.getElementById(theinput);  
			  var idx = thelist.selectedIndex;
			  var content = thelist.options[idx].innerHTML;
			  numItems = content;
			  console.log("Content in init: " + content);
			  //if(theinput && theinput.value == "")
			  //  theinput.value = content;	
			  return content;
			}

		</script>
	</head>

<body background="back.jpg";
    background-size: 1200px 1500px;
    background-repeat: no-repeat;>

		<?php  

			echo "Get CID: " . $_GET["customerid"] . "<br>";
			$_SESSION["cid"] = $_GET["customerid"];
			$_SESSION["searchtext"] = $_GET["searchtext"];

			include 'loggedinheader.php';
		?>

		<form action='loginproductsearch.php'>
			Search Term: <input type='text' name='searchtext'><br> 
			Minimum price: <input type='number' name='minprice'>   
			Maximum price: <input type='number' name='maxprice'>   
			<input type='hidden' name='saleonly' value='0'>
			<input type='checkbox' name='saleonly' value='1'> Sale Items Only<br>
			<input type='submit' value='Search Products'><hr><br><br>
			<input type='number' name='customerid' value='<?php echo $_SESSION["cid"]?>' style='display:none'>
		</form>

		<script type="text/javascript">
			showSearchResults();
		</script>	
	  
		<?php include 'footer.php'; ?>

	</body>

</html>