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

    function select_ami(value){

        var id_ami = document.getElementById("id_ami_select");
        id_ami.innerHTML = "";
        if(value == "")
            return;
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "db_requester.php?action=5&name="+value, true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                document.getElementById("id_ami_select").innerHTML = " id ami : "+xhttp.responseText;
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
        <h2>Suggestion pour un ami</h2>
        </br><label for = "table_name" >Selectionnez l'ami </label>
        <?php echo  $bdd->list_select("ami", "prenom, nom", "type", "select_ami(this.value);"); ?>

    </div>

    <div id= "id_ami_select">


    </div>

</div>

</body>
</html>
