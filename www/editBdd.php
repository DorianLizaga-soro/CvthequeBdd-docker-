<html>
    <script>
            function ajouterCompetence() {
            const container = document.getElementById("competences-container");
            const nb = container.getElementsByClassName("competence").length;

                if (nb >=10) {
                    alert("Vous ne pouvez ajouter que 10 compétences maximum");
                    return;
                }

            const article = document.createElement("article");

                article.innerHTML = `
                    <input class="competence" data-role="tagsinput" >
                    <button  id="btn_deleteComp" type="button" onclick="supprimerCompetence(this)">
                    <i class="fa-regular fa-circle-xmark fa-2xl" style="color:red;"></i>
                    </button>
                    <br><br>`;

            container.appendChild(article);

            renumeroterCompetences();
            }

        
        
        function supprimerCompetence(btn) {
            const container = document.getElementById("competences-container");
            const nb = container.getElementsByClassName("competence").length;

                if (nb <=5) {
                    alert("Vous devez garder 5 compétences minimum");
                    return;
                }
            btn.parentElement.remove();
            renumeroterCompetences();
            
        }
        
        
        function renumeroterCompetences() {
            const container = document.getElementById("competences-container");
            const competences = container.querySelectorAll("article");

            competences.forEach((article, index) => {
                const numero = index + 1;
                const input = article.querySelector(".competence");
                input.name = "competence" + numero ;});
        }

    </script>
</html>

<?php

$id = $_GET['id'] ?? null;
if(!$id) die("ID manquant");

//on recupere les données du candidat dans les tables de la bdd
$stmt = $pdo->prepare("SELECT * FROM candidat WHERE id = ?");
$stmt->execute([$id]);
$candidat = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM adresse WHERE id = ?");
$stmt->execute([$candidat['adresse_id']]);
$adresse = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare(" SELECT c.nom_competence 
    FROM candidat_competence cc
    JOIN competences c ON cc.competence_id = c.id
    WHERE cc.candidat_id = ?
");
$stmt->execute([$id]);
$competences = $stmt->fetchAll(PDO::FETCH_COLUMN);

for ($i = 0; $i < 10; $i++) {
    $competences[$i] = $competences[$i] ?? '';
}

if(!$candidat) die("Introuvable");
//on recupere les infos du formulaire pour les mettres a jour dans la bdd 
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $cvName = $candidat['cv']; 

    if(isset($_FILES['cv']) && $_FILES['cv']['error'] === 0){

        $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);

        

        if(!empty($cvName) && file_exists(__DIR__ . "/uploads/" . $cvName)){
            unlink(__DIR__ . "/uploads/" . $cvName);
        }
        
        $cvName = "cv_" . $candidat['nom'] . "_" . $candidat['prenom'] . ".pdf";

        if($ext !== "pdf"){
                    $cvName = "cv_" . $candidat['nom'] . "_" . $candidat['prenom'] . ".docx";
                }
        move_uploaded_file($_FILES['cv']['tmp_name'], __DIR__ . "/uploads/" . $cvName);
    }

    $stmt = $pdo->prepare("UPDATE adresse SET 
            ligne1 = :ligne1,
            ligne2 = :ligne2,
            code_postal = :code_postal,
            ville = :ville

        WHERE id = :adresse_id    
    ");

    $stmt->execute([

        'ligne1'          => $_POST['ligne1'],
        'ligne2'          => $_POST['ligne2'],
        'code_postal'     => $_POST['code_postal'],
        'ville'           => $_POST['ville'],
        'adresse_id'      => $candidat['adresse_id']

    ]);


    $stmt = $pdo->prepare("UPDATE candidat SET 
            nom = :nom,
            prenom = :prenom,
            date_naissance = :date_naissance,
            tel_portable = :tel_portable,
            tel_fixe = :tel_fixe,
            email = :email,
            profil = :profil,
            site_web = :site_web,
            profil_linkedin = :profil_linkedin,
            profil_viadeo = :profil_viadeo,
            profil_facebook = :profil_facebook,
            cv = :cv
            
        WHERE id = :id
    ");

    $stmt->execute([
        'nom'            => $_POST['nom'],
        'prenom'          => $_POST['prenom'],
        'date_naissance'  => $_POST['date_naissance'],
        'tel_portable'    => $_POST['tel_portable'],
        'tel_fixe'        => $_POST['tel_fixe'],
        'email'           => $_POST['email'],
        'profil'          => $_POST['profil'],
        'site_web'        => $_POST['site_web'],
        'profil_linkedin' => $_POST['profil_linkedin'],
        'profil_viadeo'   => $_POST['profil_viadeo'],
        'profil_facebook' => $_POST['profil_facebook'],
        'cv'              => $cvName,
        'id'              => $id
    ]);

  
  
        $stmt = $pdo->prepare("DELETE FROM candidat_competence WHERE candidat_id = ?");
        $stmt->execute([$id]);


        for ($i = 1; $i <= 10; $i++) {
            $comp = trim($_POST["competence$i"]);

                if ($comp !== '') {

        
                    $stmt = $pdo->prepare("SELECT id FROM competences WHERE nom_competence = ?");
                    $stmt->execute([$comp]);
                    $compId = $stmt->fetchColumn();

        
                        if (!$compId) {
                            $stmt = $pdo->prepare("INSERT INTO competences (nom_competence) VALUES (?)");
                            $stmt->execute([$comp]);
                            $compId = $pdo->lastInsertId();
                        }

        
                    $stmt = $pdo->prepare("INSERT INTO candidat_competence (candidat_id, competence_id) VALUES (?, ?)");
                    $stmt->execute([$id, $compId]);
                }
        }



    header('Location: index.php?page=list');
    exit;
}
?>

<link rel="stylesheet" href="/css/Edit.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<a href="index.php?page=list"><i id="retourAdd" class="fa-solid fa-circle-chevron-left fa-bounce" style="color: rgb(99, 230, 190);"></i></a>

<section class="sectionEdit">

<article class="formEdit">

<form method="post" enctype="multipart/form-data">
<h2>Modifier candidat</h2>
Nom : <input class="inputAdd" name="nom" value="<?= htmlspecialchars($candidat['nom'] ?? '') ?>"> 
Prénom : <input class="inputAdd" name="prenom" value="<?= htmlspecialchars($candidat['prenom'] ?? '') ?>">
Date naissance : <input class="inputAdd" type="date" name="date_naissance" value="<?= htmlspecialchars($candidat['date_naissance'] ?? '') ?>"><br><br>
Téléphone : <input class="inputAdd" type="tel" name="tel_portable" value="<?= htmlspecialchars($candidat['tel_portable'] ?? '') ?>">
Téléphone fixe : <input class="inputAdd" type="tel" name="tel_fixe" value="<?= htmlspecialchars($candidat['tel_fixe'] ?? '') ?>"><br><br>
Email : <input class="inputAdd" type="email" name="email" value="<?= htmlspecialchars($candidat['email'] ?? '') ?>">
Profil : <input class="inputAdd" name="profil" value="<?= htmlspecialchars($candidat['profil'] ?? '') ?>"><br><br>
Site web : <input class="inputAdd" name="site_web" value="<?= htmlspecialchars($candidat['site_web'] ?? '') ?>">
Profil Linkedin : <input class="inputAdd" name="profil_linkedin" value="<?= htmlspecialchars($candidat['profil_linkedin'] ?? '') ?>"><br><br>
Profil Viadeo : <input class="inputAdd" name="profil_viadeo" value="<?= htmlspecialchars($candidat['profil_viadeo'] ?? '') ?>">
Profil Facebook : <input class="inputAdd" name="profil_facebook" value="<?= htmlspecialchars($candidat['profil_facebook'] ?? '') ?>"><br><br>
Adresse ligne 1 : <input class="inputAdd" name="ligne1" value="<?= htmlspecialchars($adresse['ligne1'] ?? '') ?>">
Adresse ligne 2 : <input class="inputAdd" name="ligne2" value="<?= htmlspecialchars($adresse['ligne2'] ?? '')  ?>"><br><br>
Code postal : <input class="inputAdd" name="code_postal" value="<?= htmlspecialchars($adresse['code_postal'] ?? '') ?>">
Ville : <input class="inputAdd" name="ville" value="<?= htmlspecialchars($adresse['ville'] ?? '') ?>">

<h3>Compétences</h3><button id ="btn_addComp" type="button" onclick="ajouterCompetence()">+</button>
<article id="competences-container">
<?php for($i=0;$i<10;$i++): if (!empty($competences[$i])) : if ($i>=5) : ?>
<article>
<input class="competence" name="competence<?= $i+1 ?>" value="<?=htmlspecialchars($competences[$i])?>">
<button  id="btn_deleteComp" type="button" onclick="supprimerCompetence(this)">
<i class="fa-regular fa-circle-xmark fa-2xl" style="color:red;"></i>
</button><br><br>
</article>
<?php elseif($i<=5) : ?>
<article>
<input class="competence" name="competence<?= $i+1 ?>" value="<?=htmlspecialchars($competences[$i])?>">
<br><br>
</article>
<?php endif;endif;endfor;  ?>
</article>

<h3>CV actuel</h3>

<?php if(!empty($candidat['cv']) && $candidat['cv'] !== "NULL"): ?>
<a href="uploads/<?= $candidat['cv'] ?>" target="_blank">Voir CV</a><br>
<?php endif; ?>

<h3>Remplacer CV</h3>
<input type="file" name="cv" ><br><br>

<button id="btn_save">Enregistrer</button>

</form>
</article>
</section>