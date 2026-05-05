<?php
$id = $_GET['id'] ?? null;

if(!$id) die("ID manquant");

require "connexionBdd.php";

$stmt = $pdo->prepare("DELETE FROM candidat WHERE id = :id");
$stmt->execute(['id' => $id]);

header("Location: index.php");

exit;


?>