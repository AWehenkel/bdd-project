<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 07-04-16
 * Time: 15:44
 */

class db_requester {
    function __construct(){

    }

    function bdd_query($query){
        $bdd = mysqli_connect("localhost", "group8", "IR5ovtPs", "bdd_projet");
        $result = $bdd->query($query);
        $bdd->close();
        return $result;
    }

    function verify_password($id, $pass){
        $id = htmlspecialchars($id);
        $pass = htmlspecialchars($pass);
        $query = "SELECT * FROM users WHERE id = '".$id."' AND password = '".$pass."'";
        $result = $this->bdd_query($query);
        return ($result->num_rows > 0);
    }

    function list_table_select($name, $script){
        $query = "SHOW TABLES";
        $result = $this->bdd_query($query);
        $table_select = "<select onchange = '".$script."(this.value);' id = '".$name."' name = '".$name."'><option></option>";
        while($table = $result->fetch_row()){
            $table_select .= "<option value='".$table[0]."'>".$table[0]."</option>";
        }
        return $table_select."</select>";
    }

    function print_table($table, $start = 0, $number = 25, $restriction = "*"){
        $query = "SELECT ".$restriction." FROM ".$table. " LIMIT ".$start.", ".$number;
        $result = $this->bdd_query($query);
        if($result){
            $champs = $result->fetch_assoc();
            echo "<table class = 'result_table'><tr>";
            foreach($champs as $key => $value)
                echo "<td>".$key."</td>";
            echo "</tr>";
            foreach($result as $val){
                echo "<tr>";
                foreach($val as $key => $value)
                    echo "<td>".$value."</td>";
                echo "<tr/>";
            }
            echo "</table>";
        }

    }

    function print_form($table){
        $query = "SHOW COLUMNS FROM ".$table;
        $champs = $this->bdd_query($query);
        echo "<table>";
        $val = 0;
        while($champs && $champ = $champs->fetch_assoc()){
            $query = "SELECT DISTINCT ".$champ["Field"]." FROM ".$table;
            $result = $this->bdd_query($query)->fetch_all();
            if($val == 0)
                echo "<tr>";

            if($result) {
                echo "<td>".$champ["Field"]." <select name = '".$champ['Field']."'>";
                foreach ($result as $value) {
                    echo "<option value = '".$value[0]."'>".$value[0]."</option>";
                }
                echo "</select></td>";
                $val = ($val + 1)%2;
            }
            if($val == 0)
                echo "</tr>";
        }
        echo "</table>";

    }

    function list_select($table, $champ, $name, $script = "", $cond = ""){
        $query = "SELECT DISTINCT ".$champ." FROM ".$table." ".$cond;
        $result = $this->bdd_query($query);
        $ret = "";
        if($result){
            $ret .= "<select id = \"".$name."\" onchange = '".$script."' name = ".$name."><option></option>";
            while($champ = $result->fetch_row())
                $ret .= "<option>".$champ[0]."</option>";
            $ret .= "</select>";
        }
        return $ret;
    }

    function isInTable($table, $champ, $val){
        $query = "SELECT * FROM ".$table." WHERE ".$champ." = ".$val;
        $result = $this->bdd_query($query);
        return ($result->num_rows > 0);
    }

    function makeOptions($table, $champ, $name, $script = "", $cond = ""){
        $query = "SELECT DISTINCT ".$champ." FROM ".$table." ".$cond;
        $result = $this->bdd_query($query);
        $ret = "";
        if($result){
            while($champ = $result->fetch_row())
                $ret .= "<input onclick = \"".$script."\"type=\"checkbox\" name=\"".$name."[]\" value=\"".$champ[0]."\">".$champ[0]."</input>";
        }
        return $ret;
    }

    function insertVirtualGame($id_jeu, $id_plateforme, $ids_emulateur, $taille){
        $query = "SELECT id_exemplaire FROM exemplaire WHERE id_jeu = ".$id_jeu." AND id_plateforme = ".$id_plateforme." ORDER BY id_exemplaire DESC LIMIT 0,1";
        $result = $this->bdd_query($query)->fetch_all();
        $id_exemplaire = $result[0][0] + 1;
        $query = "INSERT INTO exemplaire VALUES(".$id_jeu.",".$id_exemplaire.", ".$id_plateforme.")";
        $this->bdd_query($query);
        $query = "INSERT INTO exemplaire_virtuel VALUES(".$id_jeu.",".$id_exemplaire.", ".$taille.")";
        $this->bdd_query($query);
        foreach($ids_emulateur as $id_em){
            $query = "INSERT INTO peut_emuler VALUES(".$id_jeu.",".$id_exemplaire.", ".$id_em.")";
            $this->bdd_query($query);
        }
    }

    function insertPhysicalGame($id_jeu, $id_plateforme, $state, $livret, $emballage){
        $query = "SELECT id_exemplaire FROM exemplaire WHERE id_jeu = ".$id_jeu." AND id_plateforme = ".$id_plateforme." ORDER BY id_exemplaire DESC LIMIT 0,1";
        $result = $this->bdd_query($query)->fetch_all();
        $id_exemplaire = $result[0][0] + 1;
        $liv = 0;
        $emb = 0;
        if($livret)
            $liv = 1;
        if($emballage)
            $emb = 1;
        $query = "INSERT INTO exemplaire VALUES(".$id_jeu.",".$id_exemplaire.", ".$id_plateforme.")";
        $this->bdd_query($query);
        $query = "INSERT INTO exemplaire_physique VALUES(".$id_jeu.",".$id_exemplaire.", ".$state.",".$emb.", ".$liv.")";
    }
}

if(isset($_GET['action'])){
    $bdd = new db_requester();
    //foreach($_GET as $val)
       // echo $val." ";
    switch(htmlspecialchars($_GET['action'])){
        case 0:
            if(isset($_GET['table']))
                $bdd->print_form(htmlspecialchars($_GET['table']));
            else
                echo "Bad request";
            break;
        case 1:
            if(isset($_GET['table']))
                $bdd->print_table(htmlspecialchars($_GET['table']));
            else
                echo "Bad request";
            break;
        case 2://It is a list_select_request
            if(isset($_GET["table"]) && isset($_GET["champ"]) && isset($_GET["name"]) && isset($_GET["script"]) && isset($_GET["cond"]))
                echo $bdd->list_select(htmlspecialchars($_GET["table"]), htmlspecialchars($_GET["champ"]), htmlspecialchars($_GET["name"]), htmlspecialchars($_GET["script"]), htmlspecialchars($_GET["cond"]));
            break;
        case 3://It needs to check if the value of champ is in the table exist or not
            if(isset($_GET["table"]) && isset($_GET["champ"]) && isset($_GET["value"]))
                if($bdd->isInTable(htmlspecialchars($_GET["table"]), htmlspecialchars($_GET["champ"]), htmlspecialchars($_GET["value"])))
                    echo "true";
                else
                    echo "false";
            break;
        case 4://It needs a checkbox
            if(isset($_GET["table"]) && isset($_GET["champ"]) && isset($_GET["name"]) && isset($_GET["script"]) && isset($_GET["cond"]))
                echo $bdd->makeOptions(htmlspecialchars($_GET["table"]), htmlspecialchars($_GET["champ"]), htmlspecialchars($_GET["name"]), htmlspecialchars($_GET["script"]), htmlspecialchars($_GET["cond"]));
            break;
        default:
            echo "pas cool";
    }
}

?>