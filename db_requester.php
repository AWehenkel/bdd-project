<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 07-04-16
 * Time: 15:44
 */

class db_requester {

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
        while($table = $result->fetch_row())
            $table_select .= "<option value='".$table[0]."'>".$table[0]."</option>";
        return $table_select."</select>";
    }

    function print_table($table, $start = 0, $number = 25, $restriction = "*", $doQuery = true, $noQueryResult = NULL){
        if($doQuery){
            $query = "SELECT ".$restriction." FROM ".$table. " LIMIT ".$start.", ".$number;
            $result = $this->bdd_query($query);
        }
        else
            $result = $noQueryResult;
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
        else echo "Pas de resultats trouvés dans la base de données";

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
            while($champ = $result->fetch_row()){
                $ret .= "<option>";
                foreach($champ as $val)
                    $ret .= $val. " ";
                $ret .= "</option>";
            }
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
        echo $query;
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
        $this->bdd_query($query);
    }

    function sortEmul(){
        $query = "SELECT id_emulateur , nbreVirtuel/nbreVirtuel_Total AS performance
       FROM
          (SELECT id_emulateur, COUNT(id_exemplaire) AS nbreVirtuel
          FROM peut_emuler
          GROUP BY id_emulateur
          ) AS T1
          NATURAL JOIN
          (SELECT id_emulateur,COUNT(id_exemplaire) AS nbreVirtuel_Total
           FROM
              emule #(id_emulateur, id_plateforme)
              NATURAL JOIN
              (SELECT id_plateforme, id_exemplaire
                  FROM
                    plateforme_du_jeu #(id_jeu, id_plateforme)
                    NATURAL JOIN
                    (SELECT id_jeu, id_exemplaire
                      FROM exemplaire_virtuel) AS T2
                    ) AS T2
           GROUP BY id_emulateur
         ) AS T2
        ORDER BY performance DESC
      ";
        $result = $this->bdd_query($query);
        return $result;
    }

    function suggestions_select($id_ami){
        $query = "SELECT id_jeu
                  FROM Jeu_Video NATURAL JOIN (SELECT id_exemplaire, id_jeu
							                  FROM Exemplaire
							                  WHERE id_exemplaire (NOT IN (SELECT id_exemplaire
														                  FROM Pret
														                  WHERE date_retour ISNULL) AS P2) AS T1 AND id_plateforme (IN (SELECT id_plateforme
																										                   FROM Pret NATURAL JOIN Exemplaire
																										                   WHERE id_ami = ".$id_ami.") AS P1)) NATURAL JOIN (SELECT id_jeu, style
																																                    	   FROM Jeu_Video
																																                           WHERE style (IN (SELECT style
																																						                   FROM Pret NATURAL JOIN Jeu_Video
																																						                   WHERE id_ami = ".$id_ami.")) AND id_jeu (NOT IN (SELECT id_jeu
																																															            FROM Pret
																																															            WHERE id_ami = ".$id_ami.")))
                  ORDER BY note DESC
                  LIMIT 0,4";
        $result = $this->bdd_query($query);
        return $result;
    }

    function ami_select($nom, $prenom){
        $query = "SELECT id_ami FROM Ami WHERE nom = \"".$nom."\" AND prenom = \"".$prenom."\"";
        $result = $this->bdd_query($query);

        if($result){
            $id = $result->fetch_all();
            $id = $id[0][0];
        }

        return $id;
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
        case 5:
            if (isset($_GET['name'])) {
                $nom = explode(" ", $_GET["name"], 2);
                $id = $bdd->ami_select($nom[1], $nom[0]);
                $bdd->print_table(NULL, 0, 4, "*", false, $bdd->suggestions_select($id));
            }
            break;
        default:
            echo "pas cool";
    }
}

?>