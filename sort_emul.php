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
<body>
<?php
include_once "DBRequester.php";
$bdd = new DBRequester();
?>
<div class = "center">
    <div class = "menu-selection">
        <h2>Tri des émulateurs par performance</h2>
    </div>
    <div class = "result-box">
        <div id = "result_box">
            <br/><label for = "table_name" ></label><?php $bdd->printTable(NULL, 0, 25, "*", false, $bdd->sortEmul()); ?>
        </div>
    </div>
</div>
</body>
</html>
