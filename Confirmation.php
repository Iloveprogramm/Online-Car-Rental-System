<?php
    // Start session
    session_start();

    // Fetch data from POST
    $firstName = $_POST['first-name'];
    $lastName = $_POST['last-name'];
    $email = $_POST['email'];
    $addressLine1 = $_POST['address-line-1'];
    $addressLine2 = $_POST['address-line-2'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postCode = $_POST['post-code'];
    $paymentType = $_POST['payment-type'];
    $totalPrice = isset($_SESSION['totalPrice']) ? $_SESSION['totalPrice'] : '0';

    // Get the cars data
    $data = file_get_contents('cars.json');
    $cars = json_decode($data, true)['cars'];

    // Mark each automobile in the cart as unavailable in the cars database by iterating over each one.
    foreach ($_SESSION['cart'] as $cartCar) {
        foreach ($cars as $key => $car) {
            if ($cartCar['Id'] == $car['Id']) {
                $cars[$key]['Availability'] = false;
            }
        }
    }

    // Save back to the json file
    file_put_contents('cars.json', json_encode(['cars' => $cars], JSON_PRETTY_PRINT));

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "assignment2";

    $conn = new mysqli($servername, $username, $password, $dbname);


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if user has rented a car in the last three months
    $sqlCheck = "SELECT * FROM Renting_History WHERE user_email = ? AND rent_date > DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    $rentedInLastThreeMonths = $resultCheck->num_rows > 0;
    $stmtCheck->close();

    // Decide the bond amount
    $bondAmount = $rentedInLastThreeMonths ? 0 : 200;

    // Prepare a query to insert the user's car rental history
    $sql = "INSERT INTO Renting_History (user_email, rent_date, bond_amount) VALUES (?, CURDATE(), ?)";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $email, $bondAmount);

    // Execute statement
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    // Empty shopping cart
    $_SESSION['cart'] = array();

    $stmt->close();
    $conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirmation</title>
    <link rel='stylesheet' type='text/css' media='screen' href='confirmation.css'>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="nav-item-left">Hertz-UTS</div>
        <h2><div class="nav-item-center">Car Rental Center</div></h2>
    </nav>
</header>
    <h1>Car rental service has been successfully placed, Thank you for your booking, <?php echo $firstName; ?>!</h1>
    <p>Here's the summary of your details:</p>
    <ul>
        <li>Full Name: <?php echo $firstName . " " . $lastName; ?></li>
        <li>Email: <?php echo $email; ?></li>
        <li>Address Line 1: <?php echo $addressLine1; ?></li>
        <li>Address Line 2: <?php echo $addressLine2; ?></li>
        <li>City: <?php echo $city; ?></li>
        <li>State: <?php echo $state; ?></li>
        <li>Post Code: <?php echo $postCode; ?></li>
        <li>Payment Type: <?php echo $paymentType; ?></li>
        <li>Total Price: $<?php echo $totalPrice; ?></li>
    </ul>
    <p>We've sent a confirmation email to <?php echo $email; ?></p>
    <a href="index.php" class="return-btn">Return to Home</a>
</body>
</html>
