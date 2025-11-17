<?php
require_once "mediatorController.php";
$mc = new mediatorController();
$mc->deleteMediator($_POST["id"]);
header("Location: ../views/back-office/mediators.php");
?>

