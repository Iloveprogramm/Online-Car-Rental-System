<?php
session_start(); // start the session

$response = array('success' => false);

parse_str(file_get_contents("php://input"), $_POST);

if (isset($_POST['carId'])) {
    $carId = $_POST['carId'];

    // Check if the car exists in the cart
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $index => $car) {
            if ($car['Id'] == $carId) {
                // Remove the car from the cart
                array_splice($_SESSION['cart'], $index, 1);
                $response['success'] = true;
                break;
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>