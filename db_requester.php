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
        $table_select = "<select onchange = '".$script."(this.value);' id = '".$name."' name = '".$name."'><option>Selectionner la table</option>";
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
}

if(isset($_GET['action'])){
    $bdd = new db_requester();
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
        default:
            echo "pas cool";
    }
}

?>