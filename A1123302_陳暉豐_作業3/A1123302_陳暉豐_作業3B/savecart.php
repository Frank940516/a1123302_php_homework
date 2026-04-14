<?php
session_start();

$product = $_POST['product'];
$qty = $_POST['qty'];

$_SESSION['product'] = $product;
$_SESSION['qty'] = $qty;

if (isset($_COOKIE['cart'][$product])) {
    $qty += $_COOKIE['cart'][$product]; 
}

setcookie("cart[$product]", $qty, time() + 3600);

header("Location: shoppingcart.php");
exit();
?>