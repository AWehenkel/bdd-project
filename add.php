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
<script>
    function select_style(val){
        var id_jeu = document.getElementById("id_jeu_select"), id_plateforme = document.getElementById("id_plateforme_select"), phy_vir = document.getElementById("physical_virtual");
        phy_vir.innerHTML = "";
        id_jeu.innerHTML = "";
        id_plateforme.innerHTML = "";
        document.getElementById("register").style.display = "none";
        document.getElementById("emulateurs").innerHTML = "";
        document.getElementById("state").style.display = 'none';
        if(val == "")
            return;
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "DBRequester.php?action=2&table=jeuvideo&champ=id_jeu&name=id_jeu&script=select_jeu(this.value)&cond=WHERE style = '"+val+"'", true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                document.getElementById("id_jeu_select").innerHTML = " id jeu : "+xhttp.responseText;
            }
        };
    }
    function select_jeu(val){
        var id_plateforme = document.getElementById("id_plateforme_select"), phy_vir = document.getElementById("physical_virtual");
        phy_vir.innerHTML = "";
        id_plateforme.innerHTML = "";
        document.getElementById("register").style.display = "none";
        document.getElementById("emulateurs").innerHTML = "";
        document.getElementById("state").style.display = 'none';
        if(val == "")
            return;
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "DBRequester.php?action=2&table=plateformejeu&champ=id_plateforme&name=id_plateforme&script=select_support(this.value)&cond=WHERE id_jeu = '"+val+"'", true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                document.getElementById("id_plateforme_select").innerHTML = " id plateforme : "+xhttp.responseText;
            }
        };
    }

    function select_support(val){
        phy_vir = document.getElementById("physical_virtual");
        phy_vir.innerHTML = "";
        document.getElementById("emulateurs").innerHTML = "";
        document.getElementById("register").style.display = "block";
        document.getElementById("state").style.display = 'none';
        if(val == "")
            return;
        get_phys();
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "DBRequester.php?action=3&table=emule&champ=id_plateforme&value="+val+"", true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                var response = xhttp.responseText;
                var tmp = "<br/>type : <ul><li>Physique <input type=\"radio\" name = \"physical_virtual\" onclick = \"get_phys()\" value = \"\" checked></li>";
                if(response == "true")
                    document.getElementById("physical_virtual").innerHTML = tmp + "<li>Virtuel <input onclick = \"get_emulateur("+val+")\"type=\"radio\" name = \"physical_virtual\" value = "+val+"></li></ul>";
                else{
                    document.getElementById("physical_virtual").innerHTML = "Cette console ne possède pas d\"émulateur l'exemplaire est donc physique";
                    document.getElementById("register").style.display = "block";
                }

            }
        };
    }

    function get_emulateur(val){
        document.getElementById("state").style.display = 'none';
        if(val == "") {
            document.getElementById("emulateurs").innerHTML = "";
            return;
        }
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "DBRequester.php?action=4&table=emule&champ=id_emulateur&script=&name=id_emulateur&cond=WHERE id_plateforme = '"+val+"'", true);
        xhttp.send(null);
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && (xhttp.status == 200 || xhttp.status == 0)) {
                document.getElementById("emulateurs").innerHTML = " Emulateur faisant tourner l'exemplaire : "+xhttp.responseText + "<br/> Taille en Ko : <input type = 'text' name = 'taille'/>";
                document.getElementById("register").style.display = "block";
            }
        };
    }

    function get_phys(){
        document.getElementById('emulateurs').innerHTML = '';
        document.getElementById('state').style.display = 'block';
    }

</script>
<body>
<?php
include_once "DBRequester.php";
$bdd = new DBRequester();
if(isset($_POST["id_jeu"]) && isset($_POST["id_plateforme"]) && isset($_POST["physical_virtual"])){
    if($_POST["physical_virtual"] == "" && isset($_POST["state"]) && isset($_POST["livret"]) && isset($_POST["emballage"]))
        $bdd->insertPhysicalGame(htmlspecialchars($_POST["id_jeu"]),htmlspecialchars($_POST["id_plateforme"]), htmlspecialchars($_POST["state"]), htmlspecialchars($_POST["livret"]) == "true", htmlspecialchars($_POST["emballage"]) == "true");
    elseif(isset($_POST["id_emulateur"]) && isset($_POST["taille"]))
        $bdd->insertVirtualGame(htmlspecialchars($_POST["id_jeu"]),htmlspecialchars($_POST["id_plateforme"]), $_POST["id_emulateur"], htmlspecialchars($_POST["taille"]));
}

?>
<div class = "center">
    <div class = "menu-selection">
        <h2>Ajout d'un exemplaire</h2>
        <form action = "add.php" method = "post" >
            <br/><label for = "table_name" >Selectionnez le type de jeu </label>
            <?php echo $bdd->listSelect("jeuvideo", "style", "type", "select_style(this.value);"); ?>
            <span id = "id_jeu_select"></span>
            <span id = "id_plateforme_select"></span>
            <span id = "physical_virtual"></span>
            <span style = "display:none;" id = "state" >
                <label for = "state"> Etat : <select name = "state"><option value = 1>1</option><option value = 2>2</option><option value = 3>3</option><option value = 4>4</option><option value = 5>5</option></select></label>
                <br/> <label for = "livret">Livret : Vrai <input type = "radio" value = "true" name = "livret" checked/> Faux <input type = "radio" value = "false" name = "livret" /></label>
                <br/> <label for = "emballage">Emballage : Vrai <input type = "radio" value = "true" name = "emballage" checked/> Faux <input type = "radio" value = "false" name = "emballage" /></label>
            </span>
            <span id = "emulateurs"></span>
            <span style = "display:none;" id = "register"><input type = "submit" value = "Enregistrer"/></span>
        </form>
    </div>
</div>

</body>
</html>

