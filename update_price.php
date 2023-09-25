<?php
session_start();

if (isset($_POST['totalPrice'])) {
    $_SESSION['totalPrice'] = $_POST['totalPrice'];
}
