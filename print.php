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
    function updateQuery(value){
        if(value == "")
            return;
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "DBRequester.php?action=0&table="+value+"&script=makeChoice()", true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                document.getElementById("selection_box").innerHTML = xhttp.responseText;
            }
        };
        var xhttp2 = new XMLHttpRequest();
        xhttp2.open("GET", "DBRequester.php?action=1&table="+value, true);
        xhttp2.send(null);
        xhttp2.onreadystatechange = function() {
            if (xhttp2.readyState == 4 && (xhttp2.status == 200 || xhttp2.status == 0)) {
                document.getElementById("result-box").style.display = "block";
                document.getElementById("result_box").innerHTML = xhttp2.responseText;
            }
        };
    }

    function makeChoice(){
        var form = document.getElementById("selection_box");
        var restrictions  = findFormEntry(form).substr(4);
        var value = document.getElementById("table_name").value;
        var xhttp2 = new XMLHttpRequest();
        xhttp2.open("GET", "DBRequester.php?action=1&table="+value+"&cond="+restrictions, true);
        xhttp2.send(null);
        xhttp2.onreadystatechange = function() {
            if (xhttp2.readyState == 4 && (xhttp2.status == 200 || xhttp2.status == 0)) {
                document.getElementById("result-box").style.display = "block";
                document.getElementById("result_box").innerHTML = xhttp2.responseText;
            }
        };
    }

    function findFormEntry(node){
       var  to_return = "";
        if(node.hasChildNodes()){
            var children = node.childNodes;
            var i;
            for(i = 0; i < children.length; i++){
                if(children[i].nodeName == "SELECT" && children[i].value != '*')
                    to_return += " AND "+children[i].name + "='" + children[i].value+"'";
                else
                    to_return += findFormEntry(children[i]);
            }
        }
        return to_return;
    }
</script>
<body>
    <?php
        include_once "DBRequester.php";
        $bdd = new DBRequester();
    ?>
    <div class = "center">
        <div class = "menu-selection">
            <h2>Menu de selection</h2>
            <div class = "left">
                <br/><label for = "table_name" >Selection d'une table </label><?php echo $bdd->listTableSelect("table_name", "updateQuery"); ?>
            </div>
            Restrictions :<br/>
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

