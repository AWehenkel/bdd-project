<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Page de connection à l'interface de la base de données</title>
    <link rel="stylesheet" type="text/css" media="all" href="style.css" />

</head>
<body>
        <?php
            include_once "db_requester.php";
            if(isset($_SESSION["id"]))
                header('location: index.php');
            if(isset($_POST["id"]) && isset($_POST["mdp"])){
                $test = new db_requester();
                if($test->verify_password($_POST["id"], $_POST["mdp"])) {
                    $_SESSION["id"] = htmlspecialchars($_POST["id"]);
                    header('location: index.php');
                }
                else
                    echo "<h1 style = 'color: red;'>Mauvais mot de passe ou nom d'utilisateur.</h1>";
            }

        ?>
    <div class = "center">
        <div class = "little-box">
            <h3>Veuillez entrer vos identifiants</h3>
            <form action = "connexion.php" method = "post">
                <table>
                    <tr>
                        <td> <label for = "id">Nom : </label></td>
                        <td><input type = "text" id = "id" name  = "id" ></td>
                    </tr>
                    <tr>
                        <td><label for = "mdp">Mot de passe : </label></td>
                        <td><input type = "password" id = "mdp" name  = "mdp" ></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class = "submit" type = "submit" value = "Entrer"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </body>
</html>