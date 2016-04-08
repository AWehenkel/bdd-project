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
    var id_jeu = document.getElementById("id_jeu_select"), id_plateforme = document.getElementById("id_plateforme_select");
    function select_style(val){
        var id_jeu = document.getElementById("id_jeu_select"), id_plateforme = document.getElementById("id_plateforme_select");
        id_jeu.innerHTML = "";
        id_plateforme.innerHTML = "";
        if(val == "")
            return;
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "db_requester.php?action=2&table=jeu_video&champ=id_jeu&name=id_jeu&script=select_jeu(this.value)&cond=WHERE style = '"+val+"'", true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                document.getElementById("id_jeu_select").innerHTML = " id jeu : "+xhttp.responseText;
            }
        };
    }
    function select_jeu(val){
        var id_plateforme = document.getElementById("id_plateforme_select");
        id_plateforme.innerHTML = "";
        if(val == "")
            return;
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "db_requester.php?action=2&table=plateforme_du_jeu&champ=id_plateforme&name=id_plateforme&script=select_support&cond=WHERE id_jeu = '"+val+"'", true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                document.getElementById("id_plateforme_select").innerHTML = " id plateforme : "+xhttp.responseText;
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
        <h2>Ajout d'un exemplaire</h2>
            </br><label for = "table_name" >Selectionnez le type de jeu </label>
        <?php echo $bdd->list_select("jeu_video", "style", "type", "select_style(this.value);"); ?>
        <span id = "id_jeu_select"></span>
        <span id = "id_plateforme_select"></span>

    </div>
</div>

</body>
</html>

