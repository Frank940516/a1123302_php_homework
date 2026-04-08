<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<html>
    <title>夏令營活動表單</title>
    <head>
        <meta charset="utf-8">
    </head>

    <body bgcolor="#98FB98">
        <center>
        <h1>夏令營活動行程與表單<br></h1>
        <TABLE border="2" width="500">
            <tr><td><h3><font color="#0000FF">活動日期</font></td><td><h3>8/7</td></tr>
            <tr><td><h3><font color="#0000FF">活動時間</font></td><td><h3>19:00~22:00</td></tr>
            <tr><td><h3><font color="#0000FF">活動地點</font></td><td><h3>高雄大學操場旁烤肉場</td></tr>
            <tr><td><h3><font color="#0000FF">活動內容</font></td><td><h3>烤肉暢聊</td></tr>
            <tr><td></td><td><h3>團康破冰</td></tr>
            <tr><td></td><td><h3>真心話大冒險</td></tr>
            <tr><td></td><td><h3>獎品抽獎</td></tr>
            </TABLE>
        </TABLE>
        <h3><font color="#0000FF">填寫表單</font><br></h3>
        <form action="info.php" method="post">
            <h2>姓名: <input type="text" name="uName" required><br></h2> 
            <h2>學號: <input type="text" name="uID" required></h2> 
            <h2>電話: <input type="text" name="uPhone" required></h2> 
            <h2>信箱: <input type="text" name="uEmail" required></h2>
            <h2>性別: 男<input type="radio" name="uGender" value="male">
            女<input type="radio" name="uGender" value="female"><br></h2>
            <h2>是否參加: 是<input type="radio" name="uJoin" value="yes">
            否<input type="radio" name="uJoin" value="no"><br></h2>

            <input type="submit"><input type="reset">
        </form>
        </center>
    </body>
</html>