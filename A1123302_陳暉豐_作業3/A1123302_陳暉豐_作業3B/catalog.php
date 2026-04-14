<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>商品選購</title>
</head>
<body>

<h2>商品選購</h2>

<form action="savecart.php" method="post">
    商品：
    <select name="product">
        <option value="apple">蘋果</option>
        <option value="banana">香蕉</option>
        <option value="orange">橘子</option>
    </select>

    數量：
    <input type="number" name="qty" value="1" min="1">

    <input type="submit" value="加入購物車">
</form>

<br>
<a href="shoppingcart.php">查看購物車</a>

</body>
</html>