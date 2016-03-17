<!--<div style="background-color: #6495ed">-->
<html>
<body>
	<?php
	    //echo "CID in header: " . $_SESSION['cid'] . "<br>";
	    //echo "SearchText in header: " . $_SESSION['searchtext']; 
	?>

	<a href="loggedinhome.php?customerid=<?php echo $_SESSION['cid'] ?>"><img src="logo.png" alt="Logo" style="width:1900px;height:540px" align="center"></a>
	<br><br>
	<!--<a href="loggedinhome.html?customerid=<?php echo $_SESSION['cid'] ?>">HOME</a>-->
	<div align="center">
		<form action="loginproductsearch.php" method="get">
			<input type="text" name="searchtext" value='<?php echo $_SESSION['searchtext']; ?>'>
			<input type="text" name="customerid" value='<?php echo $_SESSION['cid'] ?>' style="display:none"/>
			<input type='number' name='minprice' style='display:none'>   
			<input type='number' name='maxprice' style='display:none'>   
			<input type='hidden' name='saleonly' value='0' style='display:none'>
			<input type='checkbox' name='saleonly' value='1' style='display:none'>
			<input type="submit" value="Search Products"/>
		</form>

	</div>
	<div align="center">
		<a href="viewcart.php?customerid=<?php echo $_SESSION['cid'] ?>&basketid=">View Cart</a>
		<a href="vieworders.php?customerid=<?php echo $_SESSION['cid'] ?> ">View Orders</a>
		<a href="transactionhistory.php?customerid=<?php echo $_SESSION['cid'] ?>&startdate=&enddate=">Transaction History</a>
	</div>

	<hr>
<!--</div>-->
</body>
</html>
	
