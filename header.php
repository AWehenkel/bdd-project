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
<div class = "top-menu">
    <div class = "link-top" >
        <a href = "print.php">Question a</a>
    </div>
    <div class = "link-top" >
        <a href = "add.php">Question b</a>
    </div>
    <div class = "link-top" >
        <a href = "fonctionnel.php">Question c</a>
    </div>
    <div class = "link-top" >
        <a href = "sort_emul.php">Question d</a>
    </div>
    <div class = "link-top" >
        <a href = "suggest.php">Question e</a>
    </div>
    <div class = "link-top" >
        <a href = "index.php?disconnect=1">Deconnexion</a>
    </div>
</div>