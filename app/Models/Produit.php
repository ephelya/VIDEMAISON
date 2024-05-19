<?php
namespace Models;
use \Models\Admin;

class Produit {
    public $id;
    public $nom;
    public $categorie_id;
    public $description;
    public $prix;
    public $etat;
    public $valide;
    public $statut;
    public $client_id;
    private $pdo;

    public function __construct() {
        $this -> pdo = \Models\Admin::db();
    }

    public function create() {
        $sql = "INSERT INTO Produits (nom, categorie_id, description, prix, etat, valide, statut, client_id) 
                VALUES (:nom, :categorie_id, :description, :prix, :etat, :valide, :statut, :client_id)";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->bindParam(':nom', $this->nom, \PDO::PARAM_STR);
        $stmt->bindParam(':categorie_id', $this->categorie_id, \PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description, \PDO::PARAM_STR);
        $stmt->bindParam(':prix', $this->prix);
        $stmt->bindParam(':etat', $this->etat, \PDO::PARAM_STR);
        $stmt->bindParam(':valide', $this->valide, \PDO::PARAM_BOOL);
        $stmt->bindParam(':statut', $this->statut, \PDO::PARAM_STR);
        $stmt->bindParam(':client_id', $this->client_id, \PDO::PARAM_INT);
        $stmt->execute();
        $this->id = $this->pdo->lastInsertId();
        return $this->id;
    }

    public function edit() {
        $sql = "UPDATE Produits SET nom=:nom, categorie_id=:categorie_id, description=:description, prix=:prix, etat=:etat, valide=:valide, statut=:statut, client_id=:client_id WHERE id=:id";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->bindParam(':nom', $this->nom, \PDO::PARAM_STR);
        $stmt->bindParam(':categorie_id', $this->categorie_id, \PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description, \PDO::PARAM_STR);
        $stmt->bindParam(':prix', $this->prix);
        $stmt->bindParam(':etat', $this->etat, \PDO::PARAM_STR);
        $stmt->bindParam(':valide', $this->valide, \PDO::PARAM_BOOL);
        $stmt->bindParam(':statut', $this->statut, \PDO::PARAM_STR);
        $stmt->bindParam(':client_id', $this->client_id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete() {
        $sql = "DELETE FROM Produits WHERE id = :id";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function list() {
        // Debug pour vérifier si la méthode est appelée
        $pdo = \Models\Admin::db();
    
        // Préparer la requête SQL
        $stmt = $pdo->prepare("SELECT * FROM articles");
    
        // Exécution de la requête
        if ($stmt->execute()) {  // Vérifier si l'exécution est réussie
            // Récupérer les données et les retourner
            return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Récupère toutes les lignes en tant que tableau associatif
        } else {
            // Gestion des erreurs ou retourner un tableau vide ou null en cas d'échec
            error_log("Erreur lors de l'exécution de la requête : " . implode(", ", $stmt->errorInfo()));
            return []; // ou null, selon ce que votre application attend en cas d'échec
        }
    }
    public static function listArticlesWithCategories() {
        $pdo = \Models\Admin::db();
        $sql = "SELECT 
                    a.*, c.nom AS catNom, c.catMere AS catMereId, cm.nom AS catMereNom,
                    p.urlPhoto, p.main
                FROM 
                    articles a
                JOIN 
                    categories c ON a.catId = c.id
                LEFT JOIN 
                    categories cm ON c.catMere = cm.id
                LEFT JOIN 
                    articlesPhotos p ON a.id = p.articleId
                ORDER BY 
                    cm.id, c.id, a.id";
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
        // Structuration des données
        $categories = [];
        foreach ($results as $row) {
            // Définition de la catégorie mère
            $catMereId = $row['catMereId'] ?: 0;
            if (!isset($categories[$catMereId])) {
                $categories[$catMereId] = [
                    'id' => $catMereId,
                    'name' => $row['catMereNom'] ?: 'Root',
                    'children' => []
                ];
            }
    
            // Définition de la catégorie
            $catId = $row['catId'];
            if (!isset($categories[$catMereId]['children'][$catId])) {
                $categories[$catMereId]['children'][$catId] = [
                    'id' => $catId,
                    'name' => $row['catNom'],
                    'products' => []
                ];
            }
    
            // Ajout des produits
            $productId = $row['id'];
            if (!isset($categories[$catMereId]['children'][$catId]['products'][$productId])) {
                $categories[$catMereId]['children'][$catId]['products'][$productId] = [
                    'id' => $productId,
                    'nom' => $row['nom'],
                    'prix' => $row['prix'],
                    'photos' => []
                ];
            }
    
            // Ajout des photos de produits
            if ($row['urlPhoto']) {
                $categories[$catMereId]['children'][$catId]['products'][$productId]['photos'][] = [
                    'urlPhoto' => $row['urlPhoto'],
                    'main' => $row['main']
                ];
            }
        }
    
        return $categories;
    }
    

    public function reserve($clientId) {
        if ($this->statut === 'disponible') {
            $this->statut = 'réservé';
            $this->client_id = $clientId;
            $this->updateStatus();
        }
    }
    
    public function sell() {
        if ($this->statut === 'réservé') {
            $this->statut = 'vendu';
            $this->updateStatus();
        }
    }
    
    public function cancelReservation() {
        if ($this->statut === 'réservé') {
            $this->statut = 'disponible';
            $this->client_id = null; // Removing the client ID
            $this->updateStatus();
        }
    }
    
    private function updateStatus() {
        $sql = "UPDATE Produits SET statut = :statut, client_id = :client_id WHERE id = :id";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->bindParam(':statut', $this->statut, \PDO::PARAM_STR);
        $stmt->bindParam(':client_id', $this->client_id, \PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function productsTransfer()
    {
        error_log("transfer products");
        // Définir le chemin relatif du répertoire racine à scanner
        $directory = "/Users/nathalie/Dropbox/CHECY/VENTES";
        self::parcourirRecursivement($directory);
        //error_log("imagestransfer \n"); 
       
    }

    public static function parcourirRecursivement($directory) {
        // Ouvrir le répertoire
        $dir = opendir($directory);
        error_log("parcours recursif");

        // Parcourir le contenu du répertoire
        while (($file = readdir($dir)) !== false) {
            // Ignorer les fichiers spéciaux "." et ".."
            if ($file != "." && $file != "..") {
                // Construire le chemin complet du fichier ou du répertoire
                $filePath = $directory . '/' . $file;
    
                // Si c'est un répertoire, parcourir récursivement
                if ((is_dir($filePath))&&($directory!="MONIQUE")) {
                    error_log("dossier $directory, on va voir à l'intérieur \n"); 

                    self::parcourirRecursivement($filePath);
                } else {
                    // Si c'est un fichier, vérifier s'il s'agit d'une image
                    if (self::is_image($file)) {
                        // Envoyer le nom et l'URL de l'image à descript_api.php
                        $imageUrl = urlencode($filePath);
                        $imageName = urlencode($file);
                       error_log("c'est une image, on l'envoie à l'api - imageUrl $imageUrl imageName $imageName"); 

                       $file_url = self::send_to_api($imageUrl, $imageUrl); // on récupère l'url
                       self::record_file(file_url); // on va extraire les catégories et enregistrer le produit et ses photos dans la bdd
                       exit;
                    }
                }
            }
        }
    
        // Fermer le répertoire
        closedir($dir);
    }
    
    // Fonction pour vérifier si le fichier est une image
    public static function is_image($file) {
        $imageExtensions = array('jpg', 'jpeg', 'png', 'gif');
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($extension, $imageExtensions);
    }

    // Fonction pour envoyer le nom et l'URL de l'image à descript_api.php
    
    public static function send_to_api($imageName, $imageUrl) {
        $imageUrl = urldecode($imageUrl);
        $parts = explode('/VENTES/', $imageName);
        error_log("parts url ".print_r($parts, true));
        $decodedImageName = urldecode($imageName);

        $remote = urldecode($parts[0]);
        error_log("remote $remote");
       $remote = "/".explode("/VENTES/", $remote)[1];
    //error_log("sendapi $imageName, $imageUrl $remote" );
/*     imageName :  %2FUsers%2Fnathalie%2FDropbox%2FCHECY%2FVENTES%2Fpoelesantiadhesives_01.JPG,
    imageUrl :  /Users/nathalie/Dropbox/CHECY/VENTES/poelesantiadhesives_01.JPG  */

        // Extraire les catégories et sous-catégories du nom de l'image
        $categories = self::extract_categories_from_image_name($imageUrl);
        error_log("categories  : " . print_r($categories, true) . " \n");
    
        $parentCategoryId = null; // Initialiser à null
    
        // Traiter chaque catégorie extrait
        foreach ($categories as $category) {
            if (!empty($category['parent'])) {
                $parentCategory = self::getCategoryByName($category['parent']);
                $parentCategoryId = $parentCategory ? $parentCategory['id'] : null;
            }
    
            // Enregistrer la catégorie actuelle et obtenir son ID
            error_log("on enregistre la catégorie si elle n'existe pas");
            $categoryId = self::saveCategory($category['name'], $parentCategoryId);
            $remotelink = "CHECY/UPLOADS/";
            if (!empty($category['parent'])) { $remotelink .= $category['parent']."/";}
            $remotelink .= $category['name'];
            error_log("categroie $categoryId ".$category['name']." REM LINK $remotelink");
            $parentCategoryId = $categoryId; // La catégorie actuelle devient parent de la suivante
        }
    
        // Extraire le nom de l'article à partir du nom de l'image
        $articleName = self::extract_article_name_from_image_name($imageName);
        $decodedImageUrl = urldecode($imageUrl);
        
        // Debug: Vérifier les valeurs extraites
      
        
        $localFilePath = realpath($decodedImageUrl); // Convertit en chemin absolu
        if (!$localFilePath) {
            error_log("Le fichier local n'existe pas: $decodedImageUrl");
            return;
        }
        error_log("Local URL (filename with extension): $localFilePath\n");

        // Générer le nom du fichier pour le chemin distant
        $remoteFileName = basename($localFilePath);
    
        // Appeler la fonction d'upload
        error_log("upload du fichier vers $remotelink");
        $remoteFilePath = "";
        $uploadResult = self::uploadFileViaFTP($localFilePath, $remote);
        error_log("Tentative d'upload: Local = $localFilePath, Remote = $remote, Résultat = " . print_r($uploadResult, true) . "\n");
    
        // Enregistrer l'article dans la base de données
        //$articleId = self::saveArticle($articleName, $categoryId); // Utilisez le dernier categoryId comme catégorie de l'article
    }
    


    
    
    public static function upload($filePath) {
        $uploadDir = '/Users/nathalie/Dropbox/PROJETS/VMAISON/UPLOADS/';  // Chemin absolu vers le dossier d'upload
        $fileName = basename($filePath);  // Extraire le nom de fichier du chemin
    
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Créer le dossier s'il n'existe pas
            error_log("!file_exists  : $uploadDir  \n"); 
        }
        else {
            error_log("file_exists  : $uploadDir  \n"); 
        }
    
        $destination = $uploadDir . $fileName;
    
        // Déplacer le fichier
        if (move_uploaded_file($filePath, $destination)) {
            error_log("File uploaded successfully to $destination\n");
        } else {
            error_log("Failed to upload file to $destination\n");
        } 
    }

    public static function uploadFileViaFTP($localFilePath, $remoteFilePath) {
        error_log("upload de : $localFilePath $remoteFilePath");

        $url = HOMEFTP . "api/uploadFileViaFTP";
    
        if (!file_exists($localFilePath)) {
            error_log("Le fichier local n'existe pas : $localFilePath");
            return ['success' => false, 'message' => "Le fichier local n'existe pas."];
        }
    
        $fileContent = file_get_contents($localFilePath);
        $encodedFile = base64_encode($fileContent);
    
        $localFilePath= explode("/VENTES/", $localFilePath)[1];
        $postData = json_encode([
            'file' => $encodedFile,
            'remoteFilePath' => $remoteFilePath,
            'localFilePath' => $localFilePath
        ]);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('Erreur cURL : ' . curl_error($ch));
            curl_close($ch);
            return ['success' => false, 'message' => 'Failed to upload file.'];
        }
    
        curl_close($ch);
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse && isset($decodedResponse['success']) && $decodedResponse['success']) {
            error_log("réponse ".$decodedResponse["message"]);
            return ['success' => true, 'message' => $decodedResponse['message']];
        } else {
            return ['success' => false, 'message' => 'API error or file upload failure.', 'response' => $decodedResponse];
        }
    }
    


    

    

    public static function extract_categories_from_image_name($imageName) {
       // imageUrl :  /Users/nathalie/Dropbox/CHECY/VENTES/poelesantiadhesives_01.JPG 

        $categories = [];
    
        // Extrait le chemin après 'VENTES'
        $parts = explode('/VENTES/', $imageName);
        //error_log("parts url ".print_r($parts, true));
        if (count($parts) < 2) {
            return $categories; // Si le chemin ne contient pas 'VENTES', retourne un tableau vide
        }

        // Obtient le segment contenant les catégories et le nom de fichier
        $path = $parts[1];
        error_log("parth $path"); //CHEMIN FICHIER REMOTE
        // Fonction pour extraire les catégories et sous-catégories du nom de l'image
        $pathParts = explode('/', $path);
        array_pop($pathParts); 
        error_log("parth arraypop ".print_r($pathParts, true)); // CATEGORIES À ENREGITRER PR ASSOCIATION
 
        // Catégorie parente initialisée à null
        $parent = null;

        // Itération sur chaque partie du chemin pour construire la hiérarchie des catégories
        foreach ($pathParts as $categoryName) {
            if (empty($categoryName)) {
                continue; // Ignore les segments vides
            }

            // Ajoute la catégorie actuelle au tableau des catégories
            $categories[] = [
                'name' => $categoryName,
                'parent' => $parent
            ];
          //  error_log("categories  : ". print_r($categories, true) ." \n"); exit();


            // Met à jour le parent pour la prochaine itération
            $parent = $categoryName;
        }
        error_log ("categories récupérées ".print_r($categories));
        return $categories;
    }

    // Fonction pour extraire le nom de l'article à partir du nom de l'image
    public static function extract_article_name_from_image_name($imageName) {
        // Décoder l'URL pour s'assurer que les chemins sont correctement interprétés
        $decodedImageName = urldecode($imageName);

        // Logique pour extraire le nom de l'article
        // Retourne le nom de l'article
        // Le nom de l'article est le dernier élément du chemin de l'image sans l'extension
        $imageNameWithoutExtension = explode("_", pathinfo($decodedImageName, PATHINFO_FILENAME))[0];
        return $imageNameWithoutExtension;
    }
    

    public static function getCategoryByName($categoryName)
    {
        //error_log("categoryName  : ". print_r($categoryName, true) ." \n"); 

        $pdo = Admin::db(); // Assurez-vous que cette méthode retourne un objet PDO
        $sql = "SELECT * FROM categories WHERE nom = ?";
        $stmt = $pdo->prepare($sql);
        
        // Corrigez le binding du paramètre
        $stmt->bindParam(1, $categoryName);
        
        $stmt->execute();
        
        // FetchAll retourne un tableau de toutes les lignes trouvées, Fetch retourne la première ligne
        $category = $stmt->fetch(\PDO::FETCH_ASSOC); // Utilisez fetch() si vous attendez une seule ligne
       // error_log("category  : ". print_r($category, true) ." \n"); exit();

        return $category;
    }

    public static function saveCategory($categoryName, $parentCategoryId = null)
    {
        $pdo = Admin::db(); // Assurez-vous que cette méthode retourne un objet PDO
        
        // Vérifiez d'abord si la catégorie existe déjà
        $sql = "SELECT id FROM categories WHERE nom = ? ";
        $sql2 = "SELECT id FROM categories WHERE nom = '$categoryName' ";
        //error_log("sql  : $sql2 \n"); exit();

        $stmt = $pdo->prepare($sql);
        
        // Liez les paramètres à la requête
        $stmt->bindParam(1, $categoryName);

        $stmt->execute();
        
        // Récupérer l'ID si la catégorie existe déjà
        $existingId = $stmt->fetchColumn();
        if ($existingId) {
            return $existingId;
        }

        // Si la catégorie n'existe pas, créez-la
        $sql = "INSERT INTO categories (nom, catMere) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Liez les paramètres et exécutez la requête
        $stmt->bindParam(1, $categoryName);
        $stmt->bindParam(2, $parentCategoryId);
        $stmt->execute();
        
        // Retournez l'ID de la nouvelle catégorie
        return $pdo->lastInsertId();
    }

    public static function saveArticle($articleName, $categoryId)
    {
        $pdo = Admin::db(); // Assurez-vous que cette méthode retourne un objet PDO
        
        // Vérifiez d'abord si la catégorie existe déjà
        $sql = "SELECT id FROM articles WHERE nom = ?  AND catId = ? ";
        $sql2 = "SELECT id FROM articles WHERE nom = '$articleName'  AND catId = $categoryId ";

        $stmt = $pdo->prepare($sql);
        
        // Liez les paramètres à la requête
        $stmt->bindParam(1, $articleName);
        $stmt->bindParam(2, $categoryId);

        $stmt->execute();
        
        // Récupérer l'ID si la catégorie existe déjà
        $existingId = $stmt->fetchColumn();
        if ($existingId) {
            return $existingId;
        }

        // Si la catégorie n'existe pas, créez-la
        $sql = "INSERT INTO articles (nom, catId) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Liez les paramètres et exécutez la requête
        $stmt->bindParam(1, $articleName);
        $stmt->bindParam(2, $categoryId);
        $stmt->execute();
        
        // Retournez l'ID de la nouvelle catégorie
        return $pdo->lastInsertId();
    }

        

}
?>