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
<script>
    function update_query(value){
        if(value == "")
            return;
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "db_requester.php?action=0&table="+value, true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                document.getElementById("selection_box").innerHTML = xhttp.responseText;
            }
        };
        var xhttp2 = new XMLHttpRequest();
        xhttp2.open("GET", "db_requester.php?action=1&table="+value, true);
        xhttp2.send(null);
        xhttp2.onreadystatechange = function() {
            if (xhttp2.readyState == 4 && (xhttp2.status == 200 || xhttp2.status == 0)) {
                document.getElementById("result-box").style.display = "block";
                document.getElementById("result_box").innerHTML = xhttp2.responseText;
            }
        };
    }
</script>
<body>
    <?php
        include_once "db_requester.php";
        $bdd = new db_requester();
    ?>
    <div class = "center">
        <div class = "menu-selection">
            <h2>Menu de selection</h2>
            <div class = "left">
                </br><label for = "table_name" >Selection d'une table </label><?php echo $bdd->list_table_select("table_name", "update_query"); ?>
            </div>
            Restrictions :</br>
            <div id = "selection_box" class = "right">

            </div>
        </div>
    </div>
    <div id = "result-box" style = "display: none;" class = "center">
        <div class = "result-box">
            <h2>Résultats</h2>
            <div id = "result_box">

            </div>
        </div>
    </div>
</body>
</html>

