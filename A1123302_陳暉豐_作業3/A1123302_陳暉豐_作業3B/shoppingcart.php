<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>購物車</title>
</head>
<body>

<h2>購物車內容</h2>

<table border="1">
    <tr>
        <th>商品</th>
        <th>數量</th>
        <th>操作</th>
    </tr>

<?php
if (isset($_COOKIE['cart'])) {
    foreach ($_COOKIE['cart'] as $product => $qty) {
        echo "<tr>";
        echo "<td>$product</td>";
        echo "<td>$qty</td>";
        echo "<td><a href='delete.php?product=$product'>刪除</a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>購物車是空的</td></tr>";
}
?>

</table>

<br>
<a href="catalog.php">繼續購物</a>

</body>
</html>