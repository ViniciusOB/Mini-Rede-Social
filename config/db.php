<?php
$host = 'localhost';
$db = 'mini_social_network';
$user = 'root';
$pass = '';

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro: ' . $e->getMessage();
    die();
}
session_start(); // Iniciar a sessão
?>
