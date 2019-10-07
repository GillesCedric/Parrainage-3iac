<?php

class Inscription
{
    private $nom;
    private $prenom;
    private $matricule;
    private $datenaissance;
    private $filiere;
    private $niveau;
    private $classe;
    private $tel;
    private $mailiuc;
    private $mailc;
    private $profil;
    private $db;
    private $ip;

    public function __construct($no,$p,$a,$m,$j,$mat,$f,$n,$c,$t,$mi,$ma,$s)
    {
        $date = array($a,$m,$j);
        $this->nom = $no;
        $this->prenom = $p;
        $this->datenaissance = implode('-',$date);
        $this->matricule = md5($mat);
        $this->filiere = $f;
        $this->niveau = $n;
        $this->classe = $c;
        $this->tel = $t;
        $this->mailiuc = $mi;
        $this->mailc = $ma;
        $this->db = dbconnection();
        $this->ip = $s;

        //$msg = $this->verification();
        //$msg = $this->verifprofil($pp);
        //$this->insert();
    }

    public function verification()
    {
        if (!empty($this->nom) && !empty($this->prenom) && !empty($this->matricule) && !empty($this->filiere) && !empty($this->niveau) && !empty($this->tel) && !empty($this->mailiuc) && !empty($this->mailc)) {
            if(filter_var($this->mailiuc,FILTER_VALIDATE_EMAIL)){
                if(filter_var($this->mailc,FILTER_VALIDATE_EMAIL)){
                    return "ok";

                }
                else
                {
                    return "Veuiller entrer une adresse mail de contact valide";
                }
            }
            else
            {
                return "Veuiller entrer une adresse mail IUC valide";
            }
        }else 
        {
            return "Veuillez remplir tous les test beta champs";
        }
       
    }
    public function insert()
    {
        $requete = $this->db->query("INSERT INTO etudiants (id, nom, prenom, matricule, date_naissance, filiere, niveau, classe, tel, mail_iuc, mail, profil) VALUES (NULL, '$this->nom', '$this->prenom', '$this->matricule', '$this->datenaissance', '$this->filiere', '$this->niveau', '$this->classe', '$this->tel', '$this->mailiuc', '$this->mailc', '$this->profil')");
    }
    public function insertip(){
        $requete = $this->db->query("INSERT INTO visiteurs (id, ip, matricule) VALUES (NULL, '$this->ip', '$this->matricule')");
    }
    public function verifprofil($pp){
        $taillemax=6291456;
		$extensionsvalides=array('jpg','jpeg','png',);
		if($pp['size']<=$taillemax){
			$extensionsupload=strtolower(substr(strrchr($pp['name'], '.'), 1));
			if(in_array($extensionsupload, $extensionsvalides)){
				$chemin="etudiants/profil/".$this->matricule.".".$extensionsupload;
				$deplacement=move_uploaded_file($pp['tmp_name'], $chemin);
				if($deplacement){
                    $this->profil = $this->matricule.".".$extensionsupload;
                    return 'ok';
				}else{
                    $msg="Une erreure inconnue s\'est produite durant l\'importation de l\'avatar";
                    return $msg;
				}
			}else{
				$msg="Votre avatar doit être au format jpg, jpeg ou png";
                return $msg;
			}
		}else{
			$msg="L\'avatar ne doit pas dépasser 6MO";
            return $msg;
		}
    }
    public function redirect(String $var)
    {
        header("location:".$var."");
    }
    public function verifmails($mailiuc,$mailc)
    {
        $req = $this->db->prepare("SELECT * FROM etudiants WHERE mail_iuc=?");
        $req->execute(array($mailiuc));
        $count = $req->rowcount();
        if ($count == 0) {
            $req = $this->db->prepare("SELECT * FROM etudiants WHERE mail=?");
            $req->execute(array($mailc));
            $count = $req->rowcount();
            if($count == 0){
                return 'ok';
            }else{
                return "L'adresse mail de contact existe déja dans la base de données";
            }
        } else {
            return "L'adresse mail de l'IUC existe déja dans la base de données";
        }
        
    }
    public function verifmat($mat)
    {
        if(strlen($mat) == 13){
            $req = $this->db->prepare("SELECT * FROM etudiants WHERE matricule=?");
            $req->execute(array($mat));
            $count = $req->rowcount();
            if($count == 0){
                return 'ok';
            }else{
                return 'Votre matricule existe déjà dans la base de données';
            }
        }else{
            return 'Veuillez entrer un matricule valide (13 caractères)';
        }
    }
    public function veriftel($tel){
        if(strlen($tel) >=8){
            if(is_numeric($tel)){
                return 'ok';
            }else{
                return 'Le numéro de téléphone ne doit contenir que des chiffres';
            }
        }else{
            return 'Le numéro de téléphone doit contenir au moins 9 caracteres';
        }
    }
    public function verifmatricule($matricule){
        if(preg_match("#^(IUC1)[6-9]{1}E[0-9]{7}$#", $matricule)){
            return 'ok';
        }
        else{
            return 'votre matricule ne respecte pas le format de l\'iuc ';
        }
    }

    public function verifiucmail($mailiuc){
        if(preg_match("#^[a-z]+\.[a-z]+1[6-9]{1}@myiuc\.com$#", $mailiuc)){
            return 'ok';
        }else{
            return 'votre adresse email iuc doit être dans un format correct';
        }
    }

    public function verifmail($mailc){
        if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $mailc)){
            return 'ok';
        }else{
            return 'votre adresse email doit être dans un format correct';
        }
    }
    public function sendmail(){
        $header="MIME-version: 1.0\r\n";
        $header.='From:"parainage-3iac.neway-agency.com"<gillescedric@neway-agency.com>'."\n";
        $header.='content-Type:text/html;charset="utf-8"'."\n";
        $header.='content-Transfer-Encoding: 8bit';
        $message='
        <html>
          <head>
            <title>Enregistrement effectué
            </title>
            <meta charset="utf-8">
          </head>
          <body>
            <font color="#303030";>
              <div align="center">
                <table width="600px">
                  <tr>
                    <td background="https://parainage-3iac.neway-agency.com/img/favicon.ico">
                  </tr>
                  <tr>
                    <td>
                      <br>
                      <div align="center">
                        Bonjour <b>'.$this->nom.' '.$this->prenom.'</b>,
                      </div><br>
                      Nous vous remercions de vous être enregistré sur notre service de récupération des données en ligne.</b><br><br>
                      A bientôt sur <a href="https://parainage-3iac.neway-agency.com/">parainage-3iac.neway-agency.com</a> !<br><br><br><br><br>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <hr>
                    </td>
                  </tr>
                  <tr>
                    <td align="center">
                      <font size="2">
                        Ceci est un email automatique, merci de ne pas y répondre.<br>Si vous avez un soucis? contactez nous à l\'adresse <b>gillescedric@neway-agency.com</b>
                      </font>
                    </td>
                  </tr>
                </table>
              </div>
            </font>
          </body>
        </html>';
        mail($this->mailc, 'Enregistrement éffectué', $message, $header);
    }
    public function notifadmin(){
        $header="MIME-version: 1.0\r\n";
        $header.='From:"parainage-3iac.neway-agency.com"<gillescedric@neway-agency.com>'."\n";
        $header.='content-Type:text/html;charset="utf-8"'."\n";
        $header.='content-Transfer-Encoding: 8bit';
        $message='
        <html>
          <head>
            <title>Notification d\'enregistrement
            </title>
            <meta charset="utf-8">
          </head>
          <body>
            <font color="#303030";>
              <div align="center">
                <table width="600px">
                  <tr>
                    <td background="https://parainage-3iac.neway-agency.com/img/favicon.ico">
                  </tr>
                  <tr>
                    <td>
                      <br>
                      L\'étudiant '.$this->nom.' '.$this->prenom.' de '.$this->filiere.' '.$this->niveau.' '.$this->classe.' vient de s\'enregistrer avec succès.
                     <br><br><br><br><br>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <hr>
                    </td>
                  </tr>
                  <tr>
                    <td align="center">
                      <font size="2">
                        Ceci est un email automatique, merci de ne pas y répondre.
                      </font>
                    </td>
                  </tr>
                </table>
              </div>
            </font>
          </body>
        </html>';
        mail('nguefackgilles@gmail.com','Enregistrement éffectué', $message, $header);
        mail('renensiaf@gmail.com', 'Enregistrement éffectué', $message, $header);
        mail('', 'Enregistrement éffectué', $message, $header);
    }
}

?>