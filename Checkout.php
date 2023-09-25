<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CheckOut Page</title>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <link rel="stylesheet" href="checkout-styles.css" />
  </head>
  <body>
  <header>
    <nav class="navbar">
        <div class="nav-item-left">Hertz-UTS</div>
        <h2><div class="nav-item-center">Car Rental Center</div></h2>
    </nav>
</header>
    <div class="container">
      <h1>Checkout</h1>

      <form id="checkout-form" method="post" action="confirmation.php" onsubmit="checkRentingHistory()">
          <div class="form-group">
            <label for="first-name">First Name<span class="required">*</span></label>
            <input type="text" id="first-name" name="first-name" pattern="[A-Za-z\s]+" required />
          </div>

          <div class="form-group">
            <label for="last-name">Last Name<span class="required">*</span></label>
            <input type="text" id="last-name" name="last-name" pattern="[A-Za-z\s]+" required />
          </div>

          <div class="form-group">
            <label for="email">Email Address<span class="required">*</span></label>
            <input type="email" id="email" name="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required onblur="checkRentingHistory()"/>
            <div id="email-error" class="errorMessage"></div>
          </div>

          <div class="form-group">
            <label for="address-line-1">Address Line 1<span class="required">*</span></label>
            <input type="text" id="address-line-1" name="address-line-1" pattern="[A-Za-z0-9\s]+" required/>
          </div>

          <div class="form-group">
            <label for="address-line-2">Address Line 2</label>
            <input type="text" id="address-line-2" name="address-line-2" pattern="[A-Za-z0-9\s]"/>
          </div>

          <div class="form-group">
            <label for="city">City<span class="required">*</span></label>
            <input type="text" id="city" name="city" pattern="[A-Za-z\s]+" required/>
          </div>

          <div class="form-group">
            <label for="state">State<span class="required">*</span></label>
            <select id="state" name="state" required>
                <option value="" selected>Select</option>
                <option value="New South Wales">New South Wales</option>
                <option value="Queensland">Queensland</option>
                <option value="South Australia">South Australia</option>
                <option value="Tasmania">Tasmania</option>
                <option value="Victoria">Victoria</option>
                <option value="Western Australia">Western Australia</option>
            </select>
          </div>

          <div class="form-group">
            <label for="post-code">Post Code<span class="required">*</span></label>
            <input type="text" id="post-code" name="post-code" pattern="[0-9]{4}" required/>
          </div>

          <div class="form-group">
            <label for="payment-type">Payment Type<span class="required">*</span></label>
            <select id="payment-type" name="payment-type" required>
                <option value="" selected>Select</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
                <option value="PayPal">PayPal</option>
            </select>
          </div>

          <div class="form-group">
            <span id="total-price">You are required to pay $<?php echo $_SESSION['totalPrice']; ?></span>
          </div>

          <button class="PlaceOrderButton" type="submit">Booking</button>
          <button class="PlaceOrderButton" type="button" onclick="window.location.href='index.php'">Continue Selection</button>
        </form>
      </div>

      <div id="deposit-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Notice</h2>
      <p id="modal-text"></p>
    </div>
  </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
//Check input email
function CheckEmail() 
{
  const Input = document.getElementById("email");
  const InvalidInput = document.getElementById("email-error");
  if (Input.validity.patternMismatch) 
  {
    InvalidInput.textContent = "Please enter a valid email addresss.";
  } 
  else 
  {
    InvalidInput.textContent = "";
  }
}

function checkRentingHistory() 
{
  const emailInput = document.getElementById("email");
  const email = emailInput.value;
  const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  
  // Check if email input is empty or doesn't match the pattern
  if (email === "" || !emailPattern.test(email)) {
    return;
  }

  const totalPriceElement = document.getElementById("total-price");
  const modal = document.getElementById("deposit-modal");
  const modalText = document.getElementById("modal-text");
  const span = document.getElementsByClassName("close")[0];
  
  $.ajax({
    url: 'check_renting_history.php',
    type: 'post',
    data: {email: email},
    success: function(data) {
      let response = JSON.parse(data);
      let newTotalPrice;
      let oldTotalPrice = <?php echo $_SESSION['totalPrice']; ?>;
      if (response.deposit) {
        newTotalPrice = 200 + oldTotalPrice;
        modal.style.display = "block";
        modalText.textContent = "The original price was $" + oldTotalPrice + ". Since you haven't rented a car in the last three months, a deposit of $200 has been added. The new total price is $" + newTotalPrice;
      } else {
        newTotalPrice = oldTotalPrice;
      }
      totalPriceElement.textContent = "You are required to pay $" + newTotalPrice;
      // update totalPrice in PHP session
      $.post('update_price.php', {totalPrice: newTotalPrice});
    }
  });

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
}
</script>
</body>
</html>
