<?php  
ob_start();
require_once "connexionBdd.php";
$page = $_GET['page'] ?? 'list';
?>
<link rel="stylesheet" href="/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php
include "includes/header.php";
?>
<section class="header">
<h1>CVthèque</h1>
<a id="btnAdd" href="index.php?page=add"><i id="circlePlus" class="fa-sharp fa-solid fa-circle-plus fa-spin fa-2xl" style="color: rgb(0, 57, 6);"></i></a>
</section>
<?php
switch($page){
    case 'add' :
        include "addBdd.php";
        break;
    case 'edit' :
        include "editBdd.php";
        break;
    case 'delete' :
        include "deleteBdd.php";
        break;
    case 'list' :
        include "listBdd.php"; 
        break;
    case 'afficherCv' :
        include "afficherCv.php";
        break;
}
?>
<?php include "includes/footer.php";?>
<?php ob_end_flush();?>