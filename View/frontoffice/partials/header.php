<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config.php';

$assets = BASE_URL . 'View/assets/';
$frontoffice = BASE_URL . 'View/frontoffice/';
$backoffice = BASE_URL . 'View/backoffice/';
$uploads = BASE_URL . 'uploads/';
$controller = BASE_URL . 'Controller/';
$api = BASE_URL . 'api/';
?>
