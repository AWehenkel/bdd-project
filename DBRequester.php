<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 07-04-16
 * Time: 15:44
 */
class DBRequester {

    function bddQuery($query){
        $bdd = mysqli_connect("localhost", "group8", "IR5ovtPs", "group8");
        $result = $bdd->query($query);
        $bdd->close();
        return $result;
    }

    function verifyPassword($id, $pass){
        $id = htmlspecialchars($id);
        $pass = htmlspecialchars($pass);
        $query = "SELECT * FROM users WHERE id = '$id' AND password = '$pass'";
        $result = $this->bddQuery($query);
        return ($result->num_rows > 0);
    }

    function listTableSelect($name, $script){
        $query = "SHOW TABLES";
        $result = $this->bddQuery($query);
        $table_select = "<select onchange = '$script(this.value);' id = '$name' name = '$name'><option></option>";
        while($table = $result->fetch_row())
            $table_select .= "<option value='$table[0]'>$table[0]</option>";
        return $table_select."</select>";
    }

    function printTable($table, $cond = "", $restriction = "*", $doQuery = true, $noQueryResult = NULL){
        if($doQuery){
            $query = "SELECT $restriction FROM $table $cond";
            $result = $this->bddQuery($query);
        }
        else
            $result = $noQueryResult;
        if($result){
            $i = true;            
            while($champs = $result->fetch_assoc()){
                if($i){
                    echo "<table class = 'result_table'><tr>";
                    foreach($champs as $key => $value)
                        echo "<td>$key</td>";
                    echo "<tr/>";
                    $i = false;
                }
                echo "<tr>";
                foreach($champs as $key => $value)
                    echo "<td>$value</td>";
                echo "<tr/>";
            }
            echo "</table>";
        }
        else 
            echo "Pas de resultats trouvés dans la base de données";

    }

    function printForm($table, $script = ""){
        $query = "SELECT * FROM $table LIMIT 0,1";
        $champs = $this->bddQuery($query);
        echo "<table>";
        $val = 0;
        while($champs && $champ = $champs->fetch_assoc()){
            foreach($champ as $key => $value){
                $query = "SELECT DISTINCT $key FROM $table ORDER BY $key";
                $result = $this->bddQuery($query);

                if($val == 0)
                    echo "<tr>";

                if($result) {
                    echo "<td>$key <select onchange = '$script' name = '$key'>";
                    echo "<option value = '*'>*</option>";

                    while ($value = $result->fetch_row()) {
                        echo "<option value = '$value[0]'>$value[0]</option>";
                    }

                    echo "</select></td>";
                    $val = ($val + 1)%2;
                }
                if($val == 0)
                    echo "</tr>";
                }
        }
        echo "</table>";

    }

    function listSelect($table, $champ, $name, $script = "", $cond = ""){
        $query = "SELECT DISTINCT $champ FROM $table $cond";
        $result = $this->bddQuery($query);
        $ret = "";
        if($result){
            $ret .= "<select id = \"$name\" onchange = '$script' name = '$name'><option></option>";
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
        $query = "SELECT * FROM $table WHERE $champ = $val";
        $result = $this->bddQuery($query);
        return ($result->num_rows > 0);
    }

    function makeOptions($table, $champ, $name, $script = "", $cond = ""){
        $query = "SELECT DISTINCT $champ FROM  $table $cond";
        $result = $this->bddQuery($query);
        $ret = "";
        if($result){
            while($champ = $result->fetch_row())
                $ret .= "<input onclick = \" $script \" type = checkbox  name=\"".$name."[]\" value=\"$champ[0]\">$champ[0]</input>";
        }
        return $ret;
    }

    function insertVirtualGame($id_jeu, $id_plateforme, $ids_emulateur, $taille){
        $query = "SELECT id_exemplaire FROM exemplaire WHERE id_jeu = $id_jeu AND id_plateforme = $id_plateforme ORDER BY id_exemplaire DESC LIMIT 0,1";
        $result = $this->bddQuery($query);
        if($result && $result = $result->fetch_row())
            $id_exemplaire = $result[0] + 1;
        else
            $id_exemplaire = 0;        
        $query = "INSERT INTO exemplaire VALUES($id_jeu,$id_exemplaire, $id_plateforme)";
        $this->bddQuery($query);
        $query = "INSERT INTO exemplairevirtuel VALUES($id_jeu,$id_exemplaire, $taille)";

        $this->bddQuery($query);
        foreach($ids_emulateur as $id_em){
            echo $id_em;
            $query = "INSERT INTO peutemuler VALUES($id_jeu,$id_exemplaire, $id_em)";
            echo $query;
            $this->bddQuery($query);
        }
    }

    function insertPhysicalGame($id_jeu, $id_plateforme, $state, $livret, $emballage){
        $query = "SELECT id_exemplaire FROM exemplaire WHERE id_jeu = $id_jeu AND id_plateforme = $id_plateforme ORDER BY id_exemplaire DESC LIMIT 0,1";
        $result = $this->bddQuery($query);
        if($result && $result = $result->fetch_row())
            $id_exemplaire = $result[0] + 1;
        else
            $id_exemplaire = 0;
        $liv = 0;
        $emb = 0;
        if($livret)
            $liv = 1;
        if($emballage)
            $emb = 1;
        $query = "INSERT INTO exemplaire VALUES($id_jeu,$id_exemplaire, $id_plateforme)";
        $this->bddQuery($query);
        $query = "INSERT INTO exemplairephysique VALUES($id_jeu,$id_exemplaire, $state,$emb, $liv)";
        $this->bddQuery($query);
    }

    function sortEmul(){
        $query =
            "(SELECT id_emulateur , nbreVirtuel/nbreVirtuel_Total AS performance
       FROM
          (SELECT id_emulateur, COUNT(id_exemplaire) AS nbreVirtuel
          FROM peutemuler
          GROUP BY id_emulateur
          ) AS T1
          NATURAL JOIN
          (SELECT id_emulateur,COUNT(id_exemplaire) AS nbreVirtuel_Total
           FROM
              emule
              NATURAL JOIN
              (SELECT id_plateforme, id_exemplaire
                  FROM
                    exemplaire
                    NATURAL JOIN
                    (SELECT id_jeu, id_exemplaire
                      FROM exemplairevirtuel) AS T2
                    ) AS T2
           GROUP BY id_emulateur
         ) AS T2
        ORDER BY performance DESC
      )";
        $result = $this->bddQuery($query);
        return $result;
    }

    function suggestionsSelect($id_ami){
        $query = "(SELECT id_jeu, style, note
         FROM jeuvideo
         WHERE id_jeu NOT IN (SELECT id_jeu
                              FROM pret
                              WHERE id_ami = $id_ami)

         AND style IN (SELECT style
                       FROM pret NATURAL JOIN jeuvideo
                       WHERE id_ami = $id_ami)

         AND id_jeu IN (SELECT id_jeu
                        FROM exemplaire NATURAL JOIN exemplairephysique
                        WHERE id_plateforme IN (SELECT id_plateforme
                                                FROM pret NATURAL JOIN exemplaire
                                                WHERE id_ami = $id_ami))

         ORDER BY note DESC
         LIMIT 5)";

        $result = $this->bddQuery($query);
        return $result;
    }

    function amiSelect($nom, $prenom){
        $query = "SELECT id_ami FROM ami WHERE nom = \"$nom\" AND prenom = \"$prenom\" ";
        $result = $this->bddQuery($query);
        $id = NULL;
        if($result){
            $id = $result->fetch_row();
            $id = $id[0];
        }
        return $id;
    }

    function getFonctionnel(){
        $query =
            "(SELECT style, COUNT(id_exemplaire) AS fonctionnel
             FROM jeuvideo
             NATURAL JOIN
             ((SELECT id_jeu, id_exemplaire
               FROM exemplairephysique
               WHERE etat > 1)

               UNION

              (SELECT id_jeu, id_exemplaire
               FROM peutemuler NATURAL JOIN (SELECT DISTINCT id_emulateur
                                               FROM emulateurfonctionnesur) AS T2)
               ) AS T2
             GROUP BY style
            )";
        $result = $this->bddQuery($query);
        return $result;
    }
}

if(isset($_GET['action'])){
    $bdd = new DBRequester();
    //foreach($_GET as $val)
       // echo $val." ";
    switch(htmlspecialchars($_GET['action'])){
        case 0:
            if(isset($_GET['table'])){
                $bdd->printForm(htmlspecialchars($_GET['table']), htmlspecialchars($_GET['script']));}
            else
                echo "Bad request";
            break;
        case 1:
            if(isset($_GET['table']) && isset($_GET['cond']) && !empty($_GET['cond']))
                $bdd->printTable(htmlspecialchars($_GET['table']), "WHERE ".htmlspecialchars($_GET['cond']));
            else if(isset($_GET['table']))
                $bdd->printTable(htmlspecialchars($_GET['table']));
            else
                echo "Bad request";
            break;
        case 2://It is a list_select_request
            if(isset($_GET["table"]) && isset($_GET["champ"]) && isset($_GET["name"]) && isset($_GET["script"]) && isset($_GET["cond"]))
                echo $bdd->listSelect(htmlspecialchars($_GET["table"]), htmlspecialchars($_GET["champ"]), htmlspecialchars($_GET["name"]), htmlspecialchars($_GET["script"]), htmlspecialchars($_GET["cond"]));
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
                $nom = explode(" ", $_GET["name"], 3);
                $id = $nom[0];
                $bdd->printTable(NULL, "", "*", false, $bdd->suggestionsSelect($id));
            }
            break;
        default:
            echo "pas cool";
    }
}

?>
