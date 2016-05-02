<?php
/**
 * Created by PhpStorm.
 * User: antoinewehenkel
 * Date: 2/05/16
 * Time: 10:52
 */
include_once("DBRequester.php");
class DBCreator {

    function dataInsertion(){
        $nbquery = -1; // compteur de ligne

        $fic = fopen("data.csv", "r");
        $regex = "/:/";
        while(($tab = fgetcsv($fic,1000000,',')))
        {
            $long = count($tab);
            if(preg_match($regex, $tab[0])) {
                $nbquery++;
                if($nbquery > 0){
                    $values[$nbquery - 1]  = rtrim($values[$nbquery - 1] , ",");
                    $values[$nbquery - 1] .= ";";
                }

                $champs = $tab;
                $tmp = explode(':',$champs[0]);
                $champs[0] = $tmp[1];
                $table[$nbquery] = $tmp[0];
                $query[$nbquery] = "(";
                foreach($champs as $ch)
                    $query[$nbquery] .= "$ch,";
                $query[$nbquery]  = rtrim($query[$nbquery] , ",");
                $query[$nbquery] .= ")";
                $values[$nbquery] = "";
            }
            else{
                $values[$nbquery] .= "(";
                for($i=0; $i<$long; $i++)
                {
                    $values[$nbquery] .= "'$tab[$i]',";
                }
                $values[$nbquery] = rtrim($values[$nbquery], ",");
                $values[$nbquery] .= "),";
            }
        }
        $values[$nbquery]  = rtrim($values[$nbquery] , ",");
        $values[$nbquery] .= "";
        fclose($fic);
        $bdd = new DBRequester();
        for($i = 0; $i <= $nbquery; $i++)
            $bdd->bddQuery("INSERT INTO ". strtolower($table[$i])." $query[$i] VALUES $values[$i]");
        $bdd->bddQuery("INSERT INTO users(id, password) VALUES ('group8', 'IR5ovtPs')");


    }

    function tableCreation(){
        $fic = fopen("DBStructure", "r");
        $query = "";
        $bdd = new DBRequester();
        while($line = fgets($fic)){
            $query .= $line;
            if(preg_match("/;/", $line)){
                $bdd->bddQuery(strtolower($query));
                $query = "";
            }
        }
        fclose($fic);

    }
}

$ob = new DBCreator();
$ob->tableCreation();
$ob->dataInsertion();
echo 't';


?>