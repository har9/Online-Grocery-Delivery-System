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

	$email = $_POST["email"];
	$password = $_POST["password"];

	echo $email . "; " . $password . '<br>';

	$sql = "SELECT * FROM $database.customer c WHERE c.email = '$email' AND c.password = '$password'";
	echo $sql . '<br>';
	$result = mysqli_query($conn, $sql);

	if ($result == FALSE) {
		echo "Query failed!";
	} elseif (mysqli_num_rows($result) > 0) {
		// output data of each row
		while($row = mysqli_fetch_assoc($result)) {
			//echo "lastname: " . $row["LastName"]. " - firstname: " . $row["FirstName"] . "<br>";
			echo "Welcome!";
			$cid = $row["customerid"];
			//localStorage.setItem('label', 'value');
			header('Location: loggedinhome.php?customerid=' . $cid);
		}
	} else {
		echo "0 results; bad login";
	}

	mysqli_close($conn);
?>