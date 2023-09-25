<?php
session_start(); // start the session

$response = array('success' => false);

parse_str(file_get_contents("php://input"), $_POST);

if (isset($_POST['carId']) && isset($_POST['days'])) {
    $carId = $_POST['carId'];
    $days = intval($_POST['days']);

    // Check if the car exists in the cart
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $index => $car) {
            if ($car['Id'] == $carId && $days > 0) {
                // Update the rental days of the car
                $_SESSION['cart'][$index]['RentalDays'] = $days;
                $response['success'] = true;
                break;
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>