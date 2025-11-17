<?php
require_once "reportController.php";
$rc = new reportController();
$rc->deleteReport($_POST["id"]);
header("Location: ../views/back-office/reports.php");
?>

