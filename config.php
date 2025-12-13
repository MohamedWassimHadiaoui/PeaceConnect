<?php
$host = 'localhost';
$dbname = 'peaceconnect_reports';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function getConnection() {
    global $pdo;
    return $pdo;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/Git/5_Reports_Sessions/');
