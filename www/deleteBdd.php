<?php
$id = $_GET['id'] ?? null;
if(!$id) die("ID manquant");
require "connexionBdd.php";
$stmt = $pdo->prepare("DELETE FROM candidat WHERE id = :id");
$stmt->execute(['id' => $id]);
$pdo->exec("ALTER TABLE candidat AUTO_INCREMENT = 1");
$stmt = $pdo->prepare("DELETE FROM adresse WHERE id = :id");
$stmt->execute(['id' => $id]);
$pdo->exec("ALTER TABLE adresse AUTO_INCREMENT = 1");
header('Location: index.php?page=list');
exit;
?>  