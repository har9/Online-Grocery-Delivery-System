<html>
	<head><title>thegrocerystore.com</title></head>
	<body>
		<a href="index.html"><img src="logo.png" alt="Logo" style="width:530px;height:140px" align="center"></a>
		<br><br>

		<form action="productsearch.php" method="get">
			<input type="text" name="searchtext" value='<?php echo $_SESSION['searchtext']; ?>'>
			<input type='number' name='minprice' style='display:none'>   
			<input type='number' name='maxprice' style='display:none'>   
			<input type='hidden' name='saleonly' value='0' style='display:none'>
			<input type='checkbox' name='saleonly' value='1' style='display:none'>
			<input type="submit" value="Search Products">
		</form>

		<hr>

	<?php
	    

	?>
