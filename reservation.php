<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Car Reservation</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='reservation.css'>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="nav-item-left">Hertz-UTS</div>
        <h2><div class="nav-item-center">Car Rental Center</div></h2>
    </nav>
</header>
<section class="car-container">
    <table class="car-table">
        <thead>
            <tr>
                <th>Thumbnail</th>
                <th>Vehicle</th>
                <th>Price per Day</th>
                <th>Rental Days</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        session_start(); // start the session
        $totalPrice = 0;
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            foreach ($_SESSION['cart'] as $car) {
                echo "<tr data-price-per-day='{$car['PricePerDay']}'>";
                echo "<td><img class='car-image' src='./image/{$car['image']}' alt='{$car['Brand']}'></td>";
                echo "<td>{$car['Brand']}</td>";
                echo "<td>{$car['PricePerDay']}</td>";
                echo "<td><input class='rental-days' type='number' min='1' value='" . (isset($car['RentalDays']) ? $car['RentalDays'] : '1') . "' data-car-id='{$car['Id']}'></td>";
                echo "<td><button class='btn-delete-reserve' data-car-id='{$car['Id']}' onclick='removeCar(this)'>Remove</button></td>";
                echo "</tr>";
                $totalPrice += $car['PricePerDay'] * (isset($car['RentalDays']) ? $car['RentalDays'] : 1);
            }
        }
        ?>
        </tbody>
    </table>
    <div class="total-price">Total Price: <?php echo $totalPrice; ?></div>
    <div class='empty-cart-message'>Your reservation cart is currently empty. Start adding vehicles to your reservation.</div>
    <button class="btn-proceed-checkout" onclick="recalculateTotalPrice(); window.location.href='Checkout.php';">Proceeding to Checkout</button>
</section>

<script>
    function removeCar(button) {
        let carId = button.dataset.carId;

        fetch('deleteCar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'carId=' + carId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row from the table
                button.parentNode.parentNode.remove();
                // Recalculate total price
                recalculateTotalPrice();
            }
        });
    }

    document.querySelectorAll('.rental-days').forEach(item => {
    item.addEventListener('change', function() {
        if (this.value < 1 || this.value > 50) {
            alert('Invalid input. Rental days should be between 1 and 50.');
            this.value = 1;
        }

        let carId = this.dataset.carId;
        let days = this.value;

        fetch('updateDays.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'carId=' + carId + '&days=' + days
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recalculate total price
                recalculateTotalPrice();
            }
        });
    });
});

    function recalculateTotalPrice() {
        let totalPrice = 0;
        let rows = document.querySelectorAll('.car-table tbody tr');
        rows.forEach(row => {
            let pricePerDay = row.dataset.pricePerDay;
            let days = row.querySelector('.rental-days').value;
            totalPrice += pricePerDay * days;
        });
        document.querySelector('.total-price').textContent = 'Total Price: ' + totalPrice;

        fetch('updateTotalPrice.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'totalPrice=' + totalPrice
        });

        if (rows.length === 0) {
            document.querySelector('.total-price').style.display = 'none';
            document.querySelector('.btn-proceed-checkout').style.display = 'none';
            document.querySelector('.empty-cart-message').style.display = 'block';
        } else {
            document.querySelector('.total-price').style.display = 'block';
            document.querySelector('.btn-proceed-checkout').style.display = 'block';
            document.querySelector('.empty-cart-message').style.display = 'none';
        }
    }

    // Call the function initially to set the correct display
    window.onload = function() {
        recalculateTotalPrice();
    };

    
</script>
</body>
</html>
