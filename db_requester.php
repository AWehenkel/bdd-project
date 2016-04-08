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

    function list_table_select($name){
        $query = "SHOW TABLES";
        $result = $this->bdd_query($query);
        $table_select = "<select id = '".$name."' name = '".$name."'>";
        while($table = $result->fetch_row()){
            $table_select .= "<option value='".$table[0]."'>".$table[0]."</option>";
        }
        return $table_select."</select>";
    }

    function print_table($table, $start = 0, $number = 25, $restriction = "*"){
        $query = "SELECT ".$restriction." FROM ".$table. " LIMIT ".$start.", ".$number;
        $result = $this->bdd_query($query);
        $result->fetch_fields();
        foreach($result as $val){

            foreach($val as $key => $value)
                echo $key." : ".$value."</br>";
            echo "<br/>";
        }
    }
}
?>