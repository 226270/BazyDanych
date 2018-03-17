<?php

// Connection parameters
$servername = "127.0.0.1";
$username = "root";
$password = "password";

// User parameters
$dbname = $argv[1];
$customer_id = $argv[2];
$inventory_id = $argv[3];
$staff_id = $argv[4];
$date = date('Y-m-d H:i:s');
$return_date = date('Y-m-d H:i:s', strtotime($date)+(60*60*24*10));


// Create connection
try {
	$conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	echo "Connected successfully\n"; 
}
catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}


// Transaction
try {
	// Begin transaction
	$conn->beginTransaction();
	// Insert data
	$conn->exec("INSERT INTO rental (rental_date, inventory_id, customer_id, staff_id) VALUES ('$date', '$inventory_id', '$customer_id', '$staff_id')");
	$rental_id = $conn->lastInsertId();
	$conn->exec("INSERT INTO payment (customer_id, staff_id, rental_id, amount, payment_date) VALUES ('$customer_id', '$staff_id', '$rental_id', '1', '$date')");
	// Commit transaction
	$conn->commit();
	echo "New records created successfully\n";
}
catch(PDOException $e) {
	// Roll back transaction
	$conn->rollback();
	echo "Error: " . $e->getMessage();
}

$conn->exec("UPDATE rental SET return_date='$return_date' WHERE rental_id=$rental_id");

// Close connection
$conn = null;
echo "Disconnected\n";

?>
