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
        $bdd = mysqli_connect("localhost", "root", "", "bdd_projet");
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
            $ret .= "<select onchange = '".$script."' name = ".$name."><option></option>";
            while($champ = $result->fetch_row())
                $ret .= "<option>".$champ[0]."</option>";
            $ret .= "</select>";
        }
        return $ret;
    }


    function isInTable($val, $table, $champ){
        $query = "SELECT * FROM ".$table." WHERE ".$champ." = '".$val;
        $result = $this->bdd_query($query);
        return ($result->num_rows > 0);
    }
}

if(isset($_GET['action'])){
    $bdd = new db_requester();
    //foreach($_GET as $val)
      //  echo $val." ";
    switch(htmlspecialchars($_GET['action'])){
        case 0:
            if(isset($_GET['table']))
                echo $bdd->print_form(htmlspecialchars($_GET['table']));
            else
                echo "Bad request";
            break;
        case 1:
            if(isset($_GET['table']))
                echo $bdd->print_table(htmlspecialchars($_GET['table']));
            else
                echo "Bad request";
            break;
        case 2://It is a list_select_requesttable=jeu_video&champ=id&cond=WHERE style = "+val
            if(isset($_GET["table"]) && isset($_GET["champ"]) && isset($_GET["name"]) && isset($_GET["script"]) && isset($_GET["cond"]))
                echo $bdd->list_select(htmlspecialchars($_GET["table"]), htmlspecialchars($_GET["champ"]), htmlspecialchars($_GET["name"]), htmlspecialchars($_GET["script"]), htmlspecialchars($_GET["cond"]));
            break;
        case 3://It needs to check if physique ou virtuel
            break;
        default:
            echo "pas cool";
    }
}

?>