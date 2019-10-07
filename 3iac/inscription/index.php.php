<?php
include 'class/Inscription.php';
include 'dbconnection.php';

$erreur = "";
if (isset($_POST['submit'])) {
    $nom = trim(htmlspecialchars($_POST['nom']));
    $prenom = trim(htmlspecialchars($_POST['prenom']));
    $matricule = trim(htmlspecialchars($_POST['matricule']));
    $annee = trim(htmlspecialchars($_POST['annee']));
    $mois = trim(htmlspecialchars($_POST['mois']));
    $jour = trim(htmlspecialchars($_POST['jour']));
    $filiere = trim(htmlspecialchars($_POST['filiere']));
    $niveau = trim(htmlspecialchars($_POST['niveau']));
    $classe = trim(htmlspecialchars($_POST['classe']));
    $tel = trim(htmlspecialchars($_POST['tel']));
    $mailiuc = trim(htmlspecialchars($_POST['mailiuc']));
    $mailc = trim(htmlspecialchars($_POST['mailc']));
    if($filiere=="TIC"){
        if($niveau!="2"){
            $classe="NULL";
        }else{
            $classe = trim(htmlspecialchars($_POST['classe']));
        }
    }
    else{
             $classe= trim(htmlspecialchars($_POST['classep']));
    }

    $inscription = new Inscription($nom,$prenom,$annee,$mois,$jour,$matricule,$filiere,$niveau,$classe,$tel,$mailiuc,$mailc,$_SERVER["REMOTE_ADDR"]);
    $verif = $inscription->verification();
    if($verif == "ok"){
                $getmat = $inscription->verifmat($matricule);
                if($getmat == "ok"){
                    $getmail = $inscription->verifmails($mailiuc,$mailc);
                    if($getmail == "ok"){
                        $gettel = $inscription->veriftel($tel);
                        if($gettel == 'ok'){
                            $getmat=$inscription->verifmatricule($matricule);
                            if($getmat=='ok'){
                                $getiucmail=$inscription->verifiucmail($mailiuc); 
                                if($getiucmail=='ok'){  
                                    $getmail=$inscription->verifmail($mailc);
                                    if($getmail=='ok'){
                                        $getprofil = $inscription->verifprofil($_FILES['profil']);
                                        if($getprofil == "ok"){
                                         $inscription->insert();
                                         $inscription->insertip();
                                         $inscription->sendmail();
                                         $inscription->notifadmin();
                                         $inscription->redirect("merci.html");
                                        }else{
                                            $erreur = $getprofil;
                                        }
                                    }
                                    else{
                                        $erreur =$getmail;
                                    }
                                }
                                else{
                                    $erreur =$getiucmail;
                                }
                            }else{
                                $erreur = $getmat;
                            }
                            
                        }else{
                            $erreur = $gettel;
                        }
                    }else{
                        $erreur = $getmail;
                    }
                }else{
                    $erreur = $getmat;
                }
    }else {
        $erreur = $verif;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title><?= APP_NAME ?></title>
    <meta charset="utf-8" author="CEDRIC/Emmanuel">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="images/x-icon" href="img/favicon.ico" />
    <script src="js/bootstrap.min.js"></script>
    <script>
        (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
            });
        }, false);
        })();
    </script>
    <script language="JavaScript">
        function afficherAutre() {
            var a = document.getElementById("classe3il");
            var m = document.getElementById("classetic");
            var b = document.getElementById("classe");
            
            if (document.form1.filiere.value == "PREPA 3IL")
            {
                    a.style.display = "block"
                    m.style.display = "none";
                    b.style.display = "block";
            }
            else
            {
                if(document.form1.niveau.value == "2"){
                    a.style.display = "none";
                    m.style.display = "block";
                    b.style.display = "block";
                }else{
                    b.style.display = "none";
                }	
            }
        }
    </script>
</head>
<style>
       .skew{
    margin-top: -100px;
    }
    .img {
    width: auto;
    height: 80px;
    position: absolute;
    top: -3.5%;
    left: 40%;
}
</style>
<body>
       <div class="skew">
           <div class="conteneur"> 
           <?php print_r($_SERVER); ?>
        <section class="container-fluid">
            <section class="row justify-content-center">
                <section class="col-12 col-sm-6 col-md-6">
                    <form class="needs-validation form-container" method="POST" enctype="multipart/form-data" action="" name="form1" novalidate>
                             <img src="img/user.png" alt="default image" class="img" id="test">  
                        <h4 class="text-center font-weight-bold">Fomulaire d'enregistrement</h4>
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" name="nom" class="form-control" id="nom" placeholder="Entrer votre nom" value="<?php if(isset($_POST['nom'])){ echo $_POST['nom']; } ?>" required>
                            <div class="valid-feedback">
                                Bien rempli
                            </div>
                            <div class="invalid-feedback">
                                Veuillez renseigner votre nom
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" name="prenom" class="form-control" id="prenom" placeholder="Entrer votre prénom" value="<?php if(isset($_POST['prenom'])){ echo $_POST['prenom']; } ?>" required>
                            <div class="valid-feedback">
                                Bien rempli
                            </div>
                            <div class="invalid-feedback">
                                Veuillez renseigner votre prénom
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="matricule">Matricule</label>
                            <input type="text" name="matricule" class="form-control" id="matricule" aria-describedby="matriculehelp" placeholder="Entrer votre adresse matricule" value="<?php if(isset($_POST['matricule'])){ echo $_POST['matricule']; } ?>" required>
                            <small id="matriculehelp" class="form-text text-muted">Votre matricule IUC lors de l'inscription (présent sur la carte d'étudiant ou sur la fiche d'appel).</small>
                            <div class="valid-feedback">
                                Bien rempli
                            </div>
                            <div class="invalid-feedback">
                                Veuillez renseigner votre matricule
                            </div>
                        </div>
                        <div class="row">    
                            <label for="datenaissance" style="margin-left: 14px;">Date de naissance</label>
                            <div class="col">
                            <select class="form-control" id="datenaissance" aria-describedby="annehelp" name="annee" required>
                                <?php 
                                    for($i=1990;$i<=2010;$i++){
                                    ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php
                                    }
                                ?>
                            </select>
                            <small id="annehelp" class="form-text text-muted">Année</small>
                            </div>
                            <div class="col">
                            <select class="form-control" id="exampleFormControlSelect1" aria-describedby="moishelp" name="mois">
                                <?php 
                                    for($i=1;$i<=12;$i++){
                                    ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php
                                    }
                                ?>
                            </select>
                            <small id="moishelp" class="form-text text-muted">Mois</small>
                            </div>
                            <div class="col">
                            <select class="form-control" id="exampleFormControlSelect1" aria-describedby="jourhelp" name="jour">
                            <?php 
                                for($i=1;$i<=31;$i++){
                                ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <small id="jourhelp" class="form-text text-muted">Jour</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="filiere">Filiere</label>
                            <select class="form-control" id="filiere" name="filiere" onChange="afficherAutre()">
                                <option value="PREPA 3IL">PREPA 3IL</option>
                                <option value="TIC">TIC</option>
                            </select>
                        </div>
                        <div class="form-group">
                        <label for="niveau">Niveau</label>
                            <select class="form-control" id="niveau" name="niveau" onChange="afficherAutre()">
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                        <div class="form-group" id="classe">
                            <label for="classe">Classe</label>
                            <select class="form-control" id="classe3il" name="classep">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                            <select class="form-control" id="classetic" name="classe" style="display:none;">
                                <option value="PAM">PAM</option>
                                <option value="RSI">RSI</option>
                            </select>
                        </div>
                        <div class="form-group">
                        <label for="bac">Bac</label>
                            <select class="form-control" id="bac" name="bac">
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="TI">TI</option>
                                <option value="Autre">AUTRE</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tel">Numéro de téléphone</label>
                            <input type="text" name="tel" class="form-control" id="tel" placeholder="Entrer votre numéro de téléphone" value="<?php if(isset($_POST['tel'])){ echo $_POST['tel']; } ?>" required>
                            <div class="valid-feedback">
                                Bien rempli
                            </div>
                            <div class="invalid-feedback">
                                Veuillez renseigner votre numéro de téléphone
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mailiuc">Adresse mail IUC</label>
                            <input type="email" name="mailiuc" class="form-control" id="mailiuc" aria-describedby="mailiuchelp" placeholder="Entrer votre adresse mail IUC" value="<?php if(isset($_POST['mailiuc'])){ echo $_POST['mailiuc']; } ?>" required>
                            <small id="mailhelp" class="form-text text-muted">Votre adresse mail créer lors de l'inscription (au format votrenom@myiuc.com).</small>
                            <div class="valid-feedback">
                                Bien rempli
                            </div>
                            <div class="invalid-feedback">
                                Veuillez renseigner votre adresse mail IUC
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mailc">Adresse mail de contact</label>
                            <input type="email" name="mailc" class="form-control" id="mailc" aria-describedby="mailchelp" placeholder="Entrer votre adresse mail de contact" value="<?php if(isset($_POST['mailc'])){ echo $_POST['mailc']; } ?>" required>
                            <small id="mailchelp" class="form-text text-muted">Votre adresse mail personnelle de contact.</small>
                            <div class="valid-feedback">
                                Bien rempli
                            </div>
                            <div class="invalid-feedback">
                                Veuillez renseigner votre adresse de contact
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Photo de profil</label>
                            <input type="file" name="profil" class="form-control-file" id="profil" onChange="change(this);"required>
                            <script>
                              /*file=document.getElementById("profil");
                                image=document.getElementById("test");
                                
                                    function change(fichier){
                                        alert('ok');
                                        image.src=fichier.value;
                                    }*/
                            </script>
                            <small id="mailiuchelp" class="form-text text-muted">Photo qui sera affichée lors du parrainage</small>
                            <div class="valid-feedback">
                                Bien rempli
                            </div>
                            <div class="invalid-feedback">
                                Veuillez choisir une photo de profil
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" name="submit">Envoyer</button>
                        <br>
                        <section class="text-center alert-danger"><?= $erreur ?></section>
                    </form>
                </section>
            </section>
        </section>
       </div>
       </div>
</body>
</html>