<?php 
require_once "includes/functions.php";
$id = $_GET['id'] ?? null;
if(!$id) die("ID manquant");
$candidats = trouverCandidatParId($id);
if(!$candidats) die("Introuvable");
 if(!empty($candidats[27]) && $candidats[27] !== "NULL"):
  $file="uploads/$candidats[27]";
  $fileSize = filesize($file);
  header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($file) . '"');
    header('Content-Length: ' . $fileSize);
    header('Accept-Ranges: bytes');
    ob_clean();
    flush();
    readfile($file);
    exit;
endif;
?>