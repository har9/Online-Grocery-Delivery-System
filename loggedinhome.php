<!DOCTYPE html>
<html>
<head>
    <title>thegrocerystore.com</title>
    <style>
  h3   {text-align: center; color:blue; font-family:verdana;
    font-size:200%;}
  body    {text-align: center; color:red; font-family:courier; font-size = 200%; }
	</style>
    <script type="text/javascript">
    	var counter = 0;

        // wait for the page to load
        function makeCategoryListLogin(){
        	console.log("HERE");
			//window.alert(5 + 6);
            var categoryArr = 
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

				// Get list of product categories
				$categoryArr = array();
				$subcategoryArr = array();
				$allCategoryArr = array();
				$sql = "SELECT DISTINCT category FROM $database.productcategory";
				//"SELECT category, subcategory FROM $database.productcategory";
				//echo $sql . "<br>";
				$result = mysqli_query($conn, $sql);

				if (!$result) {
					echo "Query failed!<br>";
				} elseif (mysqli_num_rows($result) > 0) {
					// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$category = $row["category"];
						//$subcategory = $row["subcategory"];
						//echo "category: " . $row["category"] . "<br>";
						array_push($categoryArr, $category);
						//array_push($subcategoryArr, $subcategory);

						$tempArr = array();
						$allCategoryArr["$category"] = array();
						//array_push($allCategoryArr, $tempArr);
						//array_push($allCategoryArr["$category"], $subcategory);
					}

					sort($categoryArr, SORT_STRING);
					//echo $categoryArr . "<br>";

					$sqlSub = "SELECT category, subcategory FROM $database.productcategory";
					$resultSub = mysqli_query($conn, $sqlSub);

					if (!$resultSub) {
						echo "Query failed!<br>";
					} elseif (mysqli_num_rows($resultSub) > 0) {
						// output data of each row
						while($row = mysqli_fetch_assoc($resultSub)) {
							$category = $row["category"];
							$subcategory = $row["subcategory"];
							array_push($subcategoryArr, $subcategory);

							array_push($allCategoryArr["$category"], $subcategory);
						}

						$returnStr = "{\n";

						//echo $allCategoryArr;
						foreach ($allCategoryArr as $name => $cat) {
							//print "\t" . $name . "\n";
							$returnStr = $returnStr . "\"$name\": [";
    						foreach ($cat as $key => $value) {
    							//print $value . "\n";
    							$returnStr = $returnStr . "\"$value\", ";
    						}
    						$returnStr = $returnStr . "],\n";
    					}

    					$returnStr = $returnStr . "}";
    					echo $returnStr;
						//echo '["' . implode('", "', $allCategoryArr) . '"]';

					} else {
						echo "FAIL: subcategories not found!<br>";
					}
					
				} else {
					echo "FAIL: categories not found!<br>";
				}
            	
            ?>;

            /*
            console.log(categoryArr.length);
            for (var i = 0; i < categoryArr.length; i++) {
            	console.log(categoryArr[i]);
            }*/

            for (var property in categoryArr) {
			    if (categoryArr.hasOwnProperty(property)) {
			        // do stuff
			        console.log(categoryArr[property]);
			        /* (var i = 0; i < categoryArr.length; i++) {
		            	console.log(categoryArr[i]);
		            }*/
			    }
			}
            

            // Make a container element for the list - which is a <div>
            // You don't actually need this container to make it work
            var listContainer = document.createElement("div"); 

            // Make the list itself which is a <ul>
            var listElement = document.createElement("ul");
            //listElement.setAttribute("style", "list-style-image: url(\"arrowdown.png\")");
			listElement.setAttribute("style", "list-style-type: square");
            // add it to the page
            listContainer.appendChild(listElement);

            //for( var i =  0; i < categoryArr.length; i++){
            for (var property in categoryArr) {
			    if (categoryArr.hasOwnProperty(property)) {
                	// create a <li> for each one.
	                var listItem = document.createElement("li");
	                listItem.setAttribute("id", property + "main");
	                listItem.setAttribute("value", property);


	                listItem.innerHTML = "<a onclick=\"document.getElementById('" + property + "').style.display='';\" ondblclick=\"searchMainHeader('" + property + "');\" id=\"showsubcategory\" >" + property + "</a>";

	                //listItem.innerHTML = "<a onmouseover=\"document.getElementById('" + property + "').style.display='';\" onmouseout=\"document.getElementById('" + property + "').style.display='none';\" href=\"loginproductsearch.html?customerid=" + "<?php echo $_GET['customerid']?>" + "&searchtext=" + property + "&minprice=&maxprice=&saleonly=0\" id=\"showsubcategory\" >" + property + "</a>";
/*	                console.log("Property: " + property);
	                console.log("<a onmouseover=\"onMouseOverCategory(document.getElementById('" + property + "main').value);\" href=\"productsearch.html?searchtext=" + property + "\" id=\"showsubcategory\" >" + property + "</a>");

	                listItem.innerHTML = "<a onmouseover=\"onMouseOverCategory(document.getElementById('" + property + "').value);\" href=\"productsearch.html?searchtext=" + property + "\" id=\"showsubcategory\" >" + property + "</a>";
	                //onMouseOverCategory(property, listItem);

	                // add the item text
	                if (counter === 0) {
	                	listItem.innerHTML = "<a onmouseover=\"document.getElementById('" + property + "').style.display='';\" href=\"productsearch.html?searchtext=" + property + "\" id=\"showsubcategory\" >" + property + "</a>";
	                	counter = 1;
	                } else {
						listItem.innerHTML = "<a onmouseover=\"document.getElementById('" + property + "').style.display='none';\" href=\"productsearch.html?searchtext=" + property + "\" id=\"showsubcategory\" >" + property + "</a>";
	                	counter = 0;
	                }
*/
	                

	                var subcategoryListContainer = document.createElement("div");
	                subcategoryListContainer.setAttribute("id", property );
	                subcategoryListContainer.setAttribute("style", "display:none");

	                var subcategoryListElement = document.createElement("ul");
	                subcategoryListContainer.appendChild(subcategoryListElement);

	                for (var i = 0; i < categoryArr[property].length; i++) {
	                	var arr = categoryArr[property];
	                	var subListItem = document.createElement("li");

		                subListItem.innerHTML = "<a href=\"loginproductsearch.php?customerid=" + "<?php echo $_GET['customerid']?>" + "&searchtext=" + arr[i] + "&minprice=&maxprice=&saleonly=0\" id=\"showsubsubcategory\">" + arr[i] + "</a>";

		                subcategoryListElement.appendChild(subListItem);
	                }

	                listItem.appendChild(subcategoryListContainer);

	                // add listItem to the listElement
	                listElement.appendChild(listItem);
            	}

            }

            // add it to the page
            document.getElementsByTagName("body")[0].appendChild(listContainer);
        }

        function searchMainHeader(property) {
        	//window.open("productsearch.html?searchtext=" + property + "&minprice=&maxprice=&saleonly=0", "_self");
        	window.open("loginproductsearch.php?customerid=" + "<?php echo $_GET['customerid']?>" + "&searchtext=" + property + "&minprice=&maxprice=&saleonly=0", "_self");
        }
        

        function showSubCategory() {
	       	$('#showsubcategory').click(function() {
	           	$('#showsubsubcategory').show();
	           	return false;
	       	});        
	   	}

	   	function onMouseOverCategory(property) {
	   		console.log("Property Main: " + property + "main");

            // add the item text
            if (counter === 0) {
            	document.getElementById(property + "main").onmouseover = function(){
            		document.getElementById(property).style.display='';
            	}
            	//listItem.innerHTML = "<a onmouseover=\"document.getElementById('" + property + "').style.display='';\" href=\"productsearch.html?searchtext=" + property + "\" id=\"showsubcategory\" >" + property + "</a>";
            	counter = 1;
            } else {
            	document.getElementById(property + "main").onmouseover = function(){
            		document.getElementById(property).style.display='none';
            	}
				//listItem.innerHTML = "<a onmouseover=\"document.getElementById('" + property + "').style.display='none';\" href=\"productsearch.html?searchtext=" + property + "\" id=\"showsubcategory\" >" + property + "</a>";
            	counter = 0;
            }
	   	}


    </script>
</head>
<body background="back.jpg";
    background-size: 1200px 1500px;
    background-repeat: no-repeat;>
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
		//echo "CUSTOMER ID: " . $cid;
		$_SESSION['cid']   = $cid;
		//echo "CUSTOMER ID in SESSION: " . $_SESSION['cid'];
		$_SESSION["searchtext"] = "";

		include 'loggedinheader.php';
		//include 'scraper.php';
	?>

	<div class="divContainer">
		<div class="mainScreen" style="color:#751C1C">
			<h3><strong>Choose a Category:</strong></h3>
			<script type="text/javascript">
				//window.alert(5 + 6);
				//console.log(" HERE");
				makeCategoryListLogin();
			</script> 
		</div>

		<div style="clear: both;" class="clear"></div>
	</div>


    <?php include 'footer.php'; ?>
</body>
</html>

