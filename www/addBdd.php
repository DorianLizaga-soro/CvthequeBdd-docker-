<html>
    <script>
            function ajouterCompetence() {
            const container = document.getElementById("competences-container");
            const nb = container.getElementsByClassName("competence").length;

                if (nb >= 10) {
                    alert("Vous ne pouvez ajouter que 10 compétences maximum");
                    return;
                }

            const article = document.createElement("article");

                article.innerHTML = `<label class="labelComp"></label>
                    <input class="competence" data-role="tagsinput">
                    <button  id="btn_deleteComp" type="button" onclick="supprimerCompetence(this)">
                    <i class="fa-regular fa-circle-xmark fa-2xl" style="color:red;"></i>
                    </button>
                    <br><br>`;

            container.appendChild(article);

            renumeroterCompetences();
            }

        
        
        function supprimerCompetence(btn) {
            btn.parentElement.remove();
            renumeroterCompetences();
        }
        
        function renumeroterCompetences() {
            const container = document.getElementById("competences-container");
            const competences = container.querySelectorAll("article");

            competences.forEach((article, index) => {
                const numero = index + 6;
                const label = article.querySelector(".labelComp");
                label.textContent = "Compétence " + numero + " : ";

                const input = article.querySelector(".competence");
                input.name = "competence" + numero;});
        }

    </script>
</html>


<?php
// fonction age automatique
function age($birthdate) {
   
    $dateofbirth=DateTime::createFromFormat("Y-m-d",$birthdate);

    $today=new Datetime();
    
    $interval=$today->diff($dateofbirth);

    return $interval->format("%Y");

}
//fonction verif tel
function checktel($tel) {
    if (preg_match("/[+0][0-9]{3}[0-9]/",$tel)) {
        return $tel;
    } else {
        return false;
    }
}


$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['date_naissance']) || empty($_POST['tel_portable']) || empty($_POST['email']) || empty($_POST['profil'])) {

        $erreur = "Champs obligatoires manquants";

    } elseif(checktel($_POST['tel_portable'])==true){

//compte pour les competences
        $count = 0;
        for($i=1;$i<=10;$i++){
            if(!empty($_POST["competence$i"])) $count++;
        }

        if($count < 5){
            $erreur = "Minimum 5 compétences";
        }

        $cvName = null;

        if(isset($_FILES['cv']) && $_FILES['cv']['error'] === 0){
            $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);

            if($ext === "pdf"){
                $cvName = "cv_" . $_POST['nom'] . "_" . $candidat['prenom'] . ".pdf";
                move_uploaded_file($_FILES['cv']['tmp_name'], __DIR__ . "/uploads/" . $cvName);
            } 
            elseif($ext === "docx"){
                $cvName = "cv_" . $_POST['nom'] . "_" . $candidat['prenom'] . ".docx";
                move_uploaded_file($_FILES['cv']['tmp_name'], __DIR__ . "/uploads/" . $cvName);
            } elseif($ext === "doc") {
                $cvName = "cv_" . $_POST['nom'] . "_" . $candidat['prenom'] . ".doc";
                move_uploaded_file($_FILES['cv']['tmp_name'], __DIR__ . "/uploads/" . $cvName);
            }else {$erreur = "Le fichier n'est pas compatible";}

        }
//si pas derreur on insert dans la bdd les infos du formulaire
        if(!$erreur){

            $stmt = $pdo->prepare("INSERT INTO adresse (ligne1, ligne2, code_postal, ville) VALUES (:l1, :l2, :cp, :ville)");

            $stmt->execute([
                'l1'   => $_POST['ligne1'],
                'l2'   => $_POST['ligne2'],
                'cp'   => $_POST['code_postal'],
                'ville'=> $_POST['ville']
            ]);

            $adresse_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO candidat (nom, prenom, age, date_naissance, tel_portable, tel_fixe, email, profil, site_web, profil_linkedin, profil_viadeo, profil_facebook, cv, adresse_id)
                VALUES (:nom, :prenom, :age, :date_naissance, :tel_portable, :tel_fixe, :email, :profil, :site_web, :profil_linkedin, :profil_viadeo, :profil_facebook, :cv, :adresse_id)");

            $stmt->execute([
                'nom'            => $_POST['nom'],
                'prenom'         => $_POST['prenom'],
                'age'            => age($_POST['date_naissance']),
                'date_naissance' => $_POST['date_naissance'],
                'tel_portable'   => checktel($_POST['tel_portable']),
                'tel_fixe'       => $_POST['tel_fixe'],
                'email'          => $_POST['email'],
                'profil'         => $_POST['profil'],
                'site_web'       => $_POST['site_web'],
                'profil_linkedin'=> $_POST['profil_linkedin'],
                'profil_viadeo'  => $_POST['profil_viadeo'],
                'profil_facebook'=> $_POST['profil_facebook'],
                'cv'             => $cvName,
                'adresse_id'     => $adresse_id
            ]);

            $candidat_id = $pdo->lastInsertId();

            for ($i = 1; $i <= 10; $i++) {
                $comp = trim($_POST["competence$i"] ?? '');

                if ($comp !== '') {

                    $stmt = $pdo->prepare("SELECT id FROM competences WHERE nom_competence = :nom");
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

            header('Location: index.php?page=list');
            exit;
        }
    }else{
        $erreur ="le numéros est incorect.";
    }
}

?>


<link rel="stylesheet" href="/css/Add.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<a href="index.php?page=list"><i id="retourAdd" class="fa-solid fa-circle-chevron-left fa-bounce" style="color: rgb(99, 230, 190);"></i></a>

<section class="sectionForm">

<article class="formAdd">
<?php if($erreur): ?>
<p style="color:red"><?= $erreur ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
<h2>Ajouter un candidat</h2>
* champs obligatoires.<br><br>
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


<h3>Compétences (5 minimum)</h3> <button id ="btn_addComp" type="button" onclick="ajouterCompetence()"><i class="fa-regular fa-square-plus" style="color: rgb(0, 177, 7);"></i></button>

<article id="competences-container">
<label class="labelComp">Compétence 1 : </label>
<input class="competence" data-role="tagsinput">
<br><br>
<label class="labelComp">Compétence 2 : </label>
<input class="competence" data-role="tagsinput">
<br><br>
<label class="labelComp">Compétence 3 : </label>
<input class="competence" data-role="tagsinput">
<br><br>
<label class="labelComp">Compétence 4 : </label>
<input class="competence" data-role="tagsinput">
<br><br>
<label class="labelComp">Compétence 5 : </label>
<input class="competence" data-role="tagsinput">
<br><br>
</article>


<h3>CV</h3>
<input type="file" name="cv"><br><br>

<button id="btn_Add">Ajouter</button>

</form>
</article>
</section>