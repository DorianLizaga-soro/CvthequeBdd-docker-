<?php
require "connexionBdd.php";

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['date_naissance']) || empty($_POST['tel_portable']) || empty($_POST['email']) || empty($_POST['profil'])) 
        {
        $erreur = "Champs obligatoires manquants";
    } else {
        
        $stmt = $pdo->prepare(" INSERT INTO adresse (ligne1, ligne2, code_postal, ville) VALUES (:l1, :l2, :cp, :ville)");

        $stmt->execute([
            'l1'   => $_POST['ligne1'],
            'l2'   => $_POST['ligne2'],
            'cp'   => $_POST['code_postal'],
            'ville'=> $_POST['ville']
]);


        $adresse_id = $pdo->lastInsertId();
   

        $stmt = $pdo->prepare(" INSERT INTO candidat (nom, prenom, date_naissance, tel_portable, tel_fixe, email, profil, site_web, profil_linkedin, profil_viadeo, profil_facebook, adresse_id)
            VALUES (:nom, :prenom, :date_naissance, :tel_portable, :tel_fixe, :email, :profil, :site_web, :profil_linkedin, :profil_viadeo, :profil_facebook, :adresse_id)");

        $stmt->execute([
            'nom'            => $_POST['nom'],
            'prenom'         => $_POST['prenom'],
            'date_naissance' => $_POST['date_naissance'],
            'tel_portable'   => $_POST['tel_portable'],
            'tel_fixe'       => $_POST['tel_fixe'],
            'email'          => $_POST['email'],
            'profil'         => $_POST['profil'],
            'site_web'       => $_POST['site_web'],
            'profil_linkedin'=> $_POST['profil_linkedin'],
            'profil_viadeo'  => $_POST['profil_viadeo'],
            'profil_facebook'=> $_POST['profil_facebook'],
            'adresse_id'     => $adresse_id
        ]);

$candidat_id = $pdo->lastInsertId();

for ($i = 1; $i <= 10; $i++) {
    $comp = trim($_POST["competence$i"] ?? '');

    if ($comp !== '') {

       
        $stmt = $pdo->prepare("SELECT Id FROM competences WHERE nom_competence = :nom");
        $stmt->execute(['nom' => $comp]);
        $competence_id = $stmt->fetchColumn();

        if (!$competence_id) {
                $stmt = $pdo->prepare("INSERT INTO competences (nom_competence) VALUES (?)");
                $stmt->execute([$comp]);
                $competence_id = $pdo->lastInsertId();
        }
        
        $stmt = $pdo->prepare("INSERT INTO candidat_competence (candidat_id, competence_id) VALUES (:id, :comp_id)");
        $stmt->execute([
            'id'      => $candidat_id,
            'comp_id' => $competence_id
        ]);

    }
}


        header("Location: index.php");
        exit;
    }
}
?>


<link rel="stylesheet" href="/css/styleForm.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<a href="index.php"><i id="retourAdd" class="fa-solid fa-circle-chevron-left fa-bounce" style="color: rgb(99, 230, 190);"></i></a>

<section class="sectionForm">

<article class="formAdd">
<?php if($erreur): ?>
<p style="color:red"><?= $erreur ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
<h1>Ajouter un candidat</h1><br>
Nom* : <input class="inputAdd" name="nom"> 
Prénom* : <input class="inputAdd" name="prenom">
Date naissance* : <input class="inputAdd" type="date" name="date_naissance"><br><br>
Téléphone* : <input class="inputAdd" type="tel" name="tel_portable">
Téléphone fixe : <input class="inputAdd" type="tel" name="tel_fixe"><br><br>
Email* : <input class="inputAdd" type="email" name="email">
Profil* : <input class="inputAdd" name="profil"><br><br>
Site web : <input class="inputAdd" name="site_web">
Profil Linkedin : <input class="inputAdd" name="profil_linkedin"><br><br>
Profil Viadeo : <input class="inputAdd" name="profil_viadeo">
Profil Facebook : <input class="inputAdd" name="profil_facebook"><br><br>
Adresse ligne 1 : <input class="inputAdd" name="ligne1">
Adresse ligne 2 : <input class="inputAdd" name="ligne2"><br><br>
Code postal : <input class="inputAdd" name="code_postal">
Ville : <input class="inputAdd" name="ville">


<h3>Compétences</h3>

<?php for($i=1;$i<=10;$i++): ?>
Compétence <?= $i ?> : <input class="inputAdd" name="competence<?= $i ?>"><br><br>
<?php endfor; ?>


<button>Ajouter</button>

</form>
</article>
</section>