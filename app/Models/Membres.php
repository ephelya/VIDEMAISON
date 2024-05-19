<?php
namespace Models;
use \Models\Admin;
use Models\Membres as ModelsMembres;
use Utils\Forms;

class Membres {
    public $idMembre;
    public $profile_photos;
    public $portfolio;
    public $active;
    public $prenom;
    public $adresse;
    public $tel;
    public $ville;
    public $pays;
    public $identifiant;
    public $dnaiss;
    public $nom;
    public $user_email;
    public $user_registered;
    public $keyvalid;
    public $siret;
    public $sexe;
    public $nationalite;
    public $subscription;
    public $dateSubscr;


    public function __construct($data) {
        // Initialise les propriétés avec des valeurs par défaut ou à partir de $data
        $this->idMembre = $data->idMembre ?? null;
        $this->active = $data->active ?? null;
        $this->prenom = $data->prenom ?? null;
        $this->adresse = $data->adresse ?? null;
        $this->tel = $data->tel ?? null;
        $this->ville = $data->ville ?? null;
        $this->pays = $data->pays ?? null;
        $this->identifiant = $data->identifiant ?? null;
        $this->dnaiss = $data->dnaiss ?? null;
        $this->nom = $data->nom ?? null;
        $this->user_email = $data->user_email ?? null;
        $this->user_registered = $data->user_registered ?? null;
        $this->keyvalid = $data->keyvalid ?? null;
        $this->siret = $data->siret ?? null;
        $this->sexe = $data->sexe ?? null;
        $this->nationalite = $data->nationalite ?? null;

        // Charge les photos de profil si l'ID membre est disponible
        if (isset($this->idMembre)) {
            $this->profile_photos = $this->getUserPhotos($this->idMembre)["profile_photos"] ?? null;
            $this->subscription = $this->getUserSubscription($this->idMembre) -> abonnement ?? null;
            $this->dateSubscr = $this->getUserSubscription($this->idMembre) -> dateSubscr ?? null;
        }
    }


    public static function getUser_Confirm($keyvalid)
    { //echo "session ".$_SESSION['userId'];
        $pdo = \Models\Admin::db();
        $query = "SELECT * FROM Membres WHERE keyvalid = $keyvalid"; //echo $query;
        $stmt = $pdo->prepare('SELECT * FROM Membres WHERE keyvalid = :keyvalid');
        $stmt->bindParam(':keyvalid', $keyvalid, \PDO::PARAM_INT);
        $stmt->execute();
        $user = new Membres($stmt->fetch(\PDO::FETCH_OBJ)); //print_r($user);
        if (!empty($user))  {return $user;} else { return false; }
    }

    public static function getUser($userId)
    { //echo "session ".$_SESSION['userId'];
        if ((empty($userId)||(!isset($userId))))
        {  
            if ((!isset($_SESSION["userId"]))||($_SESSION["userId"]==''||($_SESSION["userId"]==0)) )
            { return false; }
            else 
            { $userId = $_SESSION["userId"]; }
        }  
        $pdo = \Models\Admin::db();
        $query = "SELECT * FROM Membres WHERE idMembre = $userId"; //echo $query;
        $stmt = $pdo->prepare('SELECT * FROM Membres WHERE idMembre = :userId');
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);

    }

    public function getUserPhotos ($userId)
    {
        $Userphotos = array (
            "profile_photos" => self::getPhotosByUserId($userId), //photos de profil du membre
            "details" => array(), //précisions apportées par le membre pour ses rêves
            "creas"  => array(), // photos créées pour le membre (active ou non)
            "portfolio"  => array(), //photos ajoutées pour construire le rêve
        );
        return $Userphotos;
    }

    public static function getPhotosUser() {
        $photosformConfig = [
            'formId'   =>  'form_userCapture',
            "capture" => 1,
            'method' => "enctype",
            'size' => "large",
            'float' => 1,
            'label' => "down",
            'trait' => "#",
            'fields' => [
                ["id" => "valid_userCapture", 'name' => "valid_userCapture", 'type' => 'hidden'],
                ["id" => "faceCap", 'name' => "faceCap", 'type' => 'text', 'data-instructions' => "1/4 Prenez une photo de face", 'hidden' => 1],
                ["id" => "profilCap", 'name' => "profilCap", 'type' => 'text', 'data-instructions' => "2/4 Prenez une photo de profil", 'hidden' => 1],
                ["id" => "upCap", 'name' => "upCap", 'type' => 'text', 'data-instructions' => "3/4 Prenez une photo tête vers le bas (plongée)",'hidden' => 1],
                ["id" => "dwnCap", 'name' => "dwnCap", 'type' => 'text', 'data-instructions' => "4/4 Prenez une photo tête vers le haut (contre-plongée)", 'hidden' => 1],
                ['label' => 'Photo de face', "id" => "faceUp", 'name' => "faceUp", 'type' => 'file', "required" => 0],
                ['label' => 'Photo de profil', "id" => "profilUp", 'name' => "profilUp", 'type' => 'file', "required" => 0],
                ['label' => 'Photo en plongée (tête tournée vers le bas)', "id" => "upUp", 'name' => "upUp", 'type' => 'file', "required" => 0],
                ['label' => 'Photo en contre-plongée (tête tournée vers haut)', "id" => "dwnUp", 'name' => "dwnUp", 'type' => 'file', "required" => 0],
               ["value" => "Prev", 'class' => 'prev',  'data' =>'data-action =\'prev\'', 'type' => 'submit'],
               ["value" => "Suivant", 'class' => 'next', 'data' =>'data-action =\'next\'', 'type' => 'submit', 'disabled'],           
            ]
        ];

        return Forms::generateForm($photosformConfig);
    }


    public static  function getPhotoUpload() {
        //print_r($_SESSION);
        //print_r($this -> dreamsTitles);
            $formconfig = [
                'formId'   =>  "form_photoCapture",
                'method' => "enctype",
                'size' => "large",
                'float' => 1,
                'label' => "down",
                'trait' => "#",
                'fields' => [
                    ['label' => 'Photo de référence (facultatif)', "id" => "photoass", 'name' => "photoass", 'type' => 'file', "required" => 0],
                    ["value" => "Prev", 'class' => 'prev',  'data' =>'data-action =\'prev\'', 'type' => 'submit'],
                    ["value" => "Ajouter", 'class' => 'validdreamsdetails', 'data' =>'data-action =\'next\'', 'type' => 'submit'],
                ]
            ];
    
            return Forms::generateForm($formconfig);
    }

    public static function getPhotosByUserId($userId)
    {
        $pdo = \Models\Admin::db();  // Utiliser la fonction db() pour obtenir l'objet PDO
        try {
            // Préparer la requête SQL pour sélectionner les photos
            $stmt = $pdo->prepare("SELECT * FROM PhotosMembres WHERE userId = ? AND `status` = 'prop'");
            $stmt->execute([$userId]);

            // Récupérer toutes les lignes retournées par la requête
            $photos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($photos) {
                return $photos; // Retourner les données des photos si trouvées
            } 
        } catch (\PDOException $e) {
            echo "Erreur : " . $e->getMessage(); // Affiche l'erreur en cas d'échec
            return false; // Retourner false en cas d'erreur
        }
    }

    public static function isactive()
    {
        $member = self::getUser(''); //print_r($member);
        if (($member)&&($member -> active ==1  )) { return true; } else { return false; }
    }

    public function memberActive()
    {
        $pdo = \Models\Admin::db();
        $userid = $this -> idMembre ;
        $stmt = $pdo->prepare("UPDATE Membres SET `active` = 1 WHERE idMembre= :userid");
        $stmt->bindParam(':userid', $userid, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function sendmail($mail)
    {
        $destinataire = $this -> user_email; 
        $nomDestinataire =  $this -> identifiant; 
        switch($mail)
        {
            case "welcome":
                $sujet = "sujet du mail (MMembres)";
                $message = "mess";
            break;
        }
        \Models\Admin::envoyerEmail($destinataire, $nomDestinataire, $sujet, $message, $expediteur = 'noreply@votredomaine.com', $nomExpediteur = 'Sublym Attraction');
    }

    public static function add_member($data) {
        $pdo = \Models\Admin::db();
        $errors = [];
    
        // Vérification combinée de l'email et de l'identifiant
        $stmtAccount = $pdo->prepare("SELECT COUNT(*) FROM Membres WHERE user_email = :user_email AND identifiant = :identifiant");
        $stmtAccount->bindParam(':user_email', $data['user_email']);
        $stmtAccount->bindParam(':identifiant', $data['identifiant']);
        $stmtAccount->execute();
        if ($stmtAccount->fetchColumn() > 0) {
            $errors["dbl_account"] = "Ce compte est déjà enregistré, vous pouvez connecter. <a href=''>Mot de pase oublié ?</a>";
        }
    
        // Vérification de l'email
        $stmtEmail = $pdo->prepare("SELECT COUNT(*) FROM Membres WHERE user_email = :user_email");
        $stmtEmail->bindParam(':user_email', $data['user_email']);
        $stmtEmail->execute();
        if ($stmtEmail->fetchColumn() > 0) {
            $errors["dbl_email"] = "Cette adresse e-mail est déjà présente dans la base de données, vous pouvez connecter. <br>
            Vous avez perdu votre identifiant ou votre mot de passe ?<a href=''>Cliquez ici</a> pour créer un nouveau mot de passe</a>";
        }
    
        // Vérification de l'identifiant
        $stmtIdentifiant = $pdo->prepare("SELECT COUNT(*) FROM Membres WHERE identifiant = :identifiant");
        $stmtIdentifiant->bindParam(':identifiant', $data['identifiant']);
        $stmtIdentifiant->execute();
        if ($stmtIdentifiant->fetchColumn() > 0) {
            $errors["dbl_login"] = "Cet identifiant est déjà utilisé, merci d'en choisir un autre.";
        }
    
        // Si des erreurs ont été détectées, retournez-les
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
    
        // Insertion des données dans la base de données
        $sql = "INSERT INTO Membres (identifiant, prenom, nom, user_email, user_registered, keyvalid) 
                VALUES (:identifiant, :prenom, :nom, :user_email, :user_registered, :keyvalid)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':identifiant', $data['identifiant']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':user_email', $data['user_email']);
        $stmt->bindParam(':user_registered', $data['user_registered']);
        $stmt->bindParam(':keyvalid', $data['keyvalid']);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => "Inscription réussie!"];
        } else {
            // Log de l'erreur pour le débogage
            $error = $stmt->errorInfo();
            error_log("Erreur d'insertion PDO : " . $error[2]);
            return ['success' => false, 'message' => "Erreur lors de l'inscription."];
        }
    }
    

    public static function nationalite()
    {
        $pdo = \Models\Admin::db();
        $query = "SELECT * FROM `Caracs_nationalite`  ORDER BY main DESC, valeur ASC";  
        $stmt = $pdo->prepare($query);  // Utiliser la variable $table pour la table
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);  // Retourner les résultats
    }


    public static function getMemberbyToken($token) {
        $pdo = \Models\Admin::db();
        $stmt = $pdo->prepare('SELECT * FROM Membres WHERE keyvalid = :token');
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        
        if ($data) {
            $membre = new Membres($data);
            $_SESSION["userId"] =  $membre -> idMembre;
            $_SESSION["login"] =  $membre -> identifiant;
            return $membre;
        } else {
            return false;
        }
    }
    

    public function isMember ($userId)
    {
        $ismember = false; 
        if ((empty($userId)||(!isset($userId))))
        {  
            if ((!isset($_SESSION["userId"]))||($_SESSION["userId"]==''||($_SESSION["userId"]==0)) )
            { return false; } else { $userId = $_SESSION['userId']; $user = self::getUser($userId); }

        } 
        if ($userId) { $member = $user;  }
        return $ismember;
    }
    
    public static function listusers()
    {
        
    }

    public function getUserOrders ($userId)
    {
        $orders = array();
        return $orders;
    }

    public function getUserSubscription ()
    { 
        $pdo = \Models\Admin::db();
        $userid = $this -> idMembre ;
        $sql = "SELECT abonnement, aboId, dateSubscr
        FROM MembresAbo
        JOIN Abonnements  ON MembresAbo.aboId = Abonnements.id
        WHERE MembresAbo.membreId = :userid;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userid', $userid, \PDO::PARAM_STR);
        $stmt->execute();
        $abon =  $stmt->fetch(\PDO::FETCH_OBJ);
      //  print_r($abon);
        return $abon;
    }


}

