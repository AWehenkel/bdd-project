<?php
    session_start();

    if(!isset($_SESSION["id"])){
        echo "test";
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
        include_once "db_requester.php";
        $bdd = new db_requester();
    ?>
    <div class = "center">
        <div class = "menu-selection">
            <h2>Menu de selection</h2>
            <label for = "table_name" >Selection d'une table </label><?php echo $bdd->list_table_select("table_name"); ?>
        </div>
    </div>
    <div id = "result-box" style = "display: block;" class = "center">
        <div class = "result-box">
            <h2>Résultats</h2>
            <?php $bdd->print_table("ami"); ?>
        </div>
    </div>
</body>
</html>