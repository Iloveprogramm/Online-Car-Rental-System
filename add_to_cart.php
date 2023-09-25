<?php
session_start(); // start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $id = $_POST['id'];
    // get the cars data
    $data = file_get_contents('cars.json');
    $cars = json_decode($data, true)['cars'];

    foreach ($cars as $car) 
    {
        if ($car['Id'] == $id)
        {
            // add the car to the session cart
            $_SESSION['cart'][] = $car;
            echo 'Car added to reservation';
            break;
        }
    }
}
?>
