<?php
$product = $_GET['product'];

setcookie("cart[$product]", "", time() - 3600);

header("Location: shoppingcart.php");
exit();
?>