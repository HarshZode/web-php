<?php
	$server = "db";
	$username = "lamp_demo";
	$pass = "password";
	$db = "lamp_demo";

	//create connection 

	$conn = mysqli_connect($server,$username,$pass,$db);

	//check conncetion

	if($conn->connect_error){

		die ("Connection Failed!". $conn->connect_error);
	}

?>