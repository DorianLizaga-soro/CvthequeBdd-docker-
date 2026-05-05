<?php
$host = "db";
$username = "root";
$password = "rootpassword";
$dbname = "hrdata";

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
} catch(PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage(). '<i class="fa-solid fa-signal fa-fade" style="color: rgb(255, 0, 0);"></i>');
}
?>