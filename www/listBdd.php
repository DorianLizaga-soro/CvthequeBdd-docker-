<?php

$col=$_GET['trie']??1;
$ordre=$_GET['ordre']??"ASC";
$mot = $_GET['S']??"";

$map = [
    1=>"c.nom",
    3=>"c.age",
    8=>"a.ville",
    12=>"c.profil"
];

$champ=$map[$col]??"c.nom";

$sql= "SELECT c.id,c.nom,c.prenom,c.age,c.email,c.tel_portable,c.profil,c.cv,a.ligne1,a.ligne2,a.code_postal,a.ville,
GROUP_CONCAT(DISTINCT co.nom_competence SEPARATOR '|') 
AS competences FROM candidat c 
LEFT JOIN adresse a ON c.adresse_id = a.id 
LEFT JOIN candidat_competence cc ON c.id = cc.candidat_id 
LEFT JOIN competences co ON cc.competence_id = co.id";

$params=[];

if(!empty($mot)){
    $sql .= "
        WHERE 
        c.nom LIKE :mot OR
        c.prenom LIKE :mot OR
        c.profil LIKE :mot OR
        a.ville LIKE :mot OR
        co.nom_competence LIKE :mot  
    ";
    $params['mot']="%$mot%";
} 

$sql .= " GROUP BY c.id ORDER BY $champ $ordre";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$candidats = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="list">
<form method="get" class="Search">
    <button><i class="fa-solid fa-magnifying-glass" style="color: rgb(255, 255, 255);"></i></button>
    <input type="text" name="S" placeholder="Search" value="<?= htmlspecialchars($mot) ?>">  
</form>


<form method="get">

<input type="hidden" name="S" value="<?= htmlspecialchars($mot) ?>">

<select id="choixTri" name="trie">
    <option value="1" <?= $col==1?'selected':''?> >Nom</option>
    <option value="3" <?= $col==3?'selected':''?>>Age</option>
    <option value="8" <?= $col==8?'selected':''?>>Ville</option>
    <option value="12" <?= $col==12?'selected':''?>>Profil</option>
</select>

<select id="choixOrdre" name="ordre">
<option value="ASC" <?= $ordre=="ASC"?'selected':'' ?>>ASC</option>
<option value="DESC" <?= $ordre=="DESC"?'selected':'' ?>>DESC</option>
</select>
<button type="submit">trier</button>

</form>
</section>

<?php if($mot && empty($candidats)): ?>
    <p>Aucun résultat trouvé</p>
<?php endif; ?>


<section class="sectionmain">

<?php foreach($candidats as $c): ?>

<div id="flip-box">
    <article class="flip-box-inner">
        <article class="personne-front">
            <i id="iconFiche" class="fa-solid fa-user " style="color: rgb(3, 137, 98);"></i>
            
            <h3><?= htmlspecialchars($c['prenom']) ?> <?= htmlspecialchars($c['nom']) ?></h3>
            
            <p><b> <?= htmlspecialchars($c['profil']) ?> </b></p>
            
            <?php if(!empty($c['age'])): ?>
                <p> <?= htmlspecialchars($c['age']) ?> </p>
            <?php endif; ?>
             
            <?php if(!empty($c['ville'])): ?>
                <p><b> <?= htmlspecialchars($c['ville']) ?> </b></p>
            <?php endif; ?>

            <article class="articleComp">
                <?php
                $competences = explode('|', $c['competences']??"");
                foreach($competences as $comp){
                    if(!empty($comp)){
                        echo '<p class="ficheComp"><i class="fa-solid fa-tag" style="color: rgb(93, 93, 93);"></i>'.htmlspecialchars($comp)."</p>";
                    }
                }         
                ?> 
            </article>
        </article>

        <article class="personne-back">
            <h3><?= htmlspecialchars($c['prenom']) ?> <?= htmlspecialchars($c['nom']) ?></h3>
            
            <?php if(!empty($c['ligne1'])): ?>
                <p> <?= htmlspecialchars($c['ligne1']) ?> </p>
            <?php endif; ?>
            
            <?php if(!empty($c['code_postal']) ): ?> 
                <p> <?= htmlspecialchars($c['code_postal'] ?? "") ?> 
             <?php endif; ?>
            <?php if(!empty($c['ville']) ): ?> 
                 <?= htmlspecialchars($c['ville'] ?? "") ?> 
            <?php endif; ?>

            <p><?= htmlspecialchars($c['tel_portable']) ?></p>
            <p><?= htmlspecialchars($c['email']) ?></p>
            Mail : <a href="mailto:<?= htmlspecialchars($c['email']) ?>"><i id="iconFiche" class="fa-regular fa-envelope" style="color: rgb(0, 0, 0);"></i></a>

           <?php
                if (!empty($c['cv'])) {
                    $ext = strtolower(pathinfo($c['cv'],PATHINFO_EXTENSION));

                    if($ext === "pdf"){
                    echo 'CV : <a href="afficherCv.php?id='.$c['id'].'"><i id="iconFiche" class="fa-regular fa-file-pdf" style="color: rgb(0, 0, 0);"></i>
                    </a><br><br>';
                    }

                    elseif($ext === "doc" || $ext === "docx" ){
                    echo 'CV : <a href="afficherCv.php?id='.$c['id'].'"><i id="iconFiche" class="fa-regular fa-file-word" style="color: rgb(0, 0, 0);"></i>
                    </a><br><br>';
                    }

                    else{
                        echo 'CV : <a href="afficherCv.php?id='.$c['id'].'"><i id="iconFiche" class="fa-regular fa-file" style="color: rgb(0, 0, 0);"></i>
                    </a><br><br>';
                    }

                } else {
                    echo 'Aucun CV</p>';
                }
?>

            
            <br><br> 
            <button class="btnFiche" onclick="window.location.href='editBdd.php?id=<?= $c['id'] ?>'" >Modifier</button>
            <button class="btnFiche" onclick="if(confirm('Supprimer ce candidat ?')){ window.location.href='deleteBdd.php?id=<?= $c['id'] ?>';}"  >Supprimer</button>
        </article>
    </article>
</div>

<?php endforeach; ?>

</section>

