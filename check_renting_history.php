<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "assignment2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Detecting connections
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user's email
$userEmail = $_POST['email'];

// Prepare a query statement
$sql = "SELECT * FROM Renting_History WHERE user_email = ? AND rent_date >= DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)";

// Prepare statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);

// Execute statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// If the search result is empty, the user has not rented a car in the last three months and a deposit of 200 will be added
if ($result->num_rows == 0) {
    echo json_encode(array('deposit' => true));
} else {
    echo json_encode(array('deposit' => false));
}

$stmt->close();
$conn->close();
?>
