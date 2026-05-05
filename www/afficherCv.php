<?php 
//prend l'id du candidat
$id = $_GET['id'] ?? null;
if(!$id) die("ID manquant");

require "connexionBdd.php";

$stmt = $pdo->prepare("SELECT * FROM candidat WHERE id = ?");
$stmt->execute([$id]);
$candidat = $stmt->fetch(PDO::FETCH_ASSOC);


$cvName = $candidat['cv'] ?? null;
//si il a un cv on laffiche en fonction de son extension
if (!empty($cvName) && $cvName !== "NULL") {

    $file = __DIR__ . "/uploads/" . $cvName;

    if (!file_exists($file)) {
        die("Fichier CV introuvable");
    }

    $ext = strtolower(pathinfo($file,PATHINFO_EXTENSION));
    $fileSize = filesize($file);

if ($ext ==="pdf"){
      header("Content-Type: application/pdf");
    }elseif($ext === "doc" || $ext === "docx"){
      header("Content-Type: application/msword");
    }
    else{
      header("Content-Type: application/octet-stream");
    }
    header('Content-Disposition: inline; filename="' . basename($file) . '"');
    header("Content-Length: " . $fileSize);
    header("Accept-Ranges: bytes");
    if (ob_get_length()) {
        ob_clean();
    }
    flush();
    readfile($file);
    exit;
}

?>


