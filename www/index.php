<link rel="stylesheet" href="/css/Style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<?php 
include "includes/header.php";

require_once "connexionBdd.php"; 
?>

<section class="header">

<h1>CVthèque</h1>

<a id="btnAdd" href="addBdd.php"><i id="circlePlus" class="fa-sharp fa-solid fa-circle-plus fa-spin fa-2xl" style="color: rgb(0, 57, 6);"></i></a>

</section>

<?php
include "listBdd.php";
?>


<?php include "includes/footer.php";?>