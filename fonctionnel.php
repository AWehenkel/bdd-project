<?php
include("header.php");
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Interface de la base de données</title>
    <link rel="stylesheet" type="text/css" media="all" href="style.css" />
</head>
<body>
<?php
include_once "DBRequester.php";
$bdd = new DBRequester();
?>
<div class = "center">
    <div class = "menu-selection">
        <h2>Exemplaires fonctionnels</h2>
    </div>
    <div class = "result-box">
        <div id = "result_box">
        <br/><label for = "table_name" ></label><?php $bdd->printTable(NULL, "", "*", false, $bdd->getFonctionnel()); ?>
        </div>
    </div>
</div>
</body>
</html>
