<?php
session_start();

if(!isset($_SESSION["id"])){
    header("location: connexion.php");
}
if(isset($_GET["disconnect"])){
    session_destroy();
    header("location: connexion.php");
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Interface de la base de données</title>
    <link rel="stylesheet" type="text/css" media="all" href="style.css" />
</head>
<div class = "menu">
    <div class = "link" >
        <a href = "print.php">Question a</a>
    </div>
    <div class = "link" >
        <a href = "add.php">Question b</a>
    </div>
    <div class = "link" >
        <a href = "sort_emul.php">Question d</a>
    </div>
    <div class = "link" >
        <a href = "suggest.php">Question e</a>
    </div>
</div>
</body>
</html>