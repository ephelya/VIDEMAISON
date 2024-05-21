<?php
namespace Models;
use \Models\Admin;

class Produit {
    private $id = null;
    private $nom = null;
    private $categorie_id = null;
    private $description = null;
    private $prix = null;
    private $etat = null;
    private $hauteur = null;
    private $largeur = null;
    private $statut = null;
    private $client_id = null; 
    private $annonce;
    private $pdo;
    private $file_url;

    public function __construct($data) {
        $this->pdo = \Models\Admin::db();  // Supposons que cette méthode static retourne une instance de PDO
        $this->id = $data['id'] ?? null;
        $this->nom = $data['nom'] ?? null;
        $this->categorie_id = $data['categorie_id'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->prix = $data['prix'] ?? null;
        $this->hauteur = $data['hauteur'] ?? null;
        $this->largeur = $data['largeur'] ?? null;
        $this->annonce = $data['annonce'] ?? null;
        $this->etat = $data['etat'] ?? null;
        $this->statut = $data['statut'] ?? null;
        $this->client_id = $data['client_id'] ?? null;
        $this->file_url = $data['file_url'] ?? null;  // Assurez-vous que file_url est bien fourni dans $data
    }

    public function setProperty($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
    public static function getProduct($id) {
        $pdo = \Models\Admin::db(); // Assurez-vous que cette méthode retourne un objet PDO
        $sql = "SELECT a.id, a.prix, a.nom, a.description, a.statut, a.clientId, a.hauteur, a.largeur, a.etat,
                c.id AS categorie_id, c.nom AS categorie, cm.nom AS catMere, cm.id AS catMereId , cli.nom cliNom, cli.email cliMail,  cli.telephone cliTel
                FROM articles a 
                LEFT JOIN categories c ON c.id = a.catId
                LEFT  JOIN clients cli ON cli.id = a.clientId
                LEFT JOIN categories cm ON cm.id = c.catMere
                WHERE a.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $id, \PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);
    
        error_log("objet ".print_r($article, true));
    
        if ($article) {
            // Assurez-vous que les clés du tableau correspondent aux attributs attendus par le constructeur
            $productData = [
                'id' => $article['id'],
                'nom' => $article['nom']?? null,
                'categorie_id' => $article['categorie_id']?? null,
                'description' => $article['description']?? null,
                'prix' => $article['prix']?? null,
                'etat' => $article['etat']?? null,
                'statut' => $article['statut']?? null,
                'clientId' => $article['client_id'] ?? null,
                'file_url' => $article['file_url'] ?? null, 
            ];
    
            return new Produit($productData);
        } else {
            return null;
        }
    }
    


    public function addPhoto($articleId, $fileUrl) {
        // Vérifier s'il existe déjà une photo principale pour cet article
        $pdo = \Models\Admin::db();
        $sqlCheckMain = "SELECT COUNT(*) FROM articlesPhotos WHERE articleId = :articleId AND main = 1";
        $stmtCheck = $pdo->prepare($sqlCheckMain);
        $stmtCheck->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        $stmtCheck->execute();
        $mainExists = $stmtCheck->fetchColumn() > 0;

        // Si une photo principale existe déjà, définir $main à 0
        if ($mainExists) {  $main = 0;  } else { $main = 1; }

        // Insérer ou mettre à jour la photo
        $sql = "INSERT INTO articlesPhotos (articleId, urlPhoto, main) 
                VALUES (:articleId, :urlPhoto, :main)
                ON DUPLICATE KEY UPDATE main = VALUES(main)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':articleId', $articleId, \PDO::PARAM_INT);
        $stmt->bindParam(':urlPhoto', $fileUrl, \PDO::PARAM_STR);
        $stmt->bindParam(':main', $main, \PDO::PARAM_INT);
        $stmt->execute();
        $photoId = $pdo->lastInsertId();

        // Si c'est la photo principale, mettre à jour la propriété file_url
        if ($main == 1) {
            $this->setProperty("file_url", $photoId);
        }

        return $photoId;
    }

    public function create() {
        $pdo = \Models\Admin::db();
        $sql = "INSERT INTO articles (nom, catId, description, prix, file_url, etat, statut, client_id) 
                VALUES (:nom, :categorie_id, :description, :prix, :etat,  :statut, :client_id)";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->bindParam(':nom', $this->nom, \PDO::PARAM_STR);
        $stmt->bindParam(':categorie_id', $this->categorie_id, \PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description, \PDO::PARAM_STR);
        $stmt->bindParam(':prix', $this->prix);
        $stmt->bindParam(':file_url', $this->file_url);
        $stmt->bindParam(':etat', $this->etat, \PDO::PARAM_STR);
        $stmt->bindParam(':statut', $this->statut, \PDO::PARAM_STR);
        $stmt->bindParam(':client_id', $this->client_id, \PDO::PARAM_INT);
        $stmt->execute();
        $this->id = $this->pdo->lastInsertId();
        return $this->id;
    }

    public function update($data) {
        foreach ($data as $key => $value) {
            $this->setProperty($key, $value);
        }

        $sql = "UPDATE articles SET nom = :nom, catId = :categorie_id, description = :description, prix = :prix,
                etat = :etat,  statut = :statut, largeur = :largeur, hauteur = :hauteur, clientId = :clientId, file_url = :file_url WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':categorie_id', $this->categorie_id);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':prix', $this->prix);
        $stmt->bindParam(':etat', $this->etat);
        $stmt->bindParam(':hauteur', $this->hauteur);
        $stmt->bindParam(':largeur', $this->largeur);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':clientId', $this->client_id);
        $stmt->bindParam(':file_url', $this->file_url);
        $stmt->execute();
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
        $sql = "DELETE FROM articles WHERE id = :id";
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
        //print_r($results);
    
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
            if (!empty($row["annonce"])) { $annonce = oui; }
            // Ajout des produits
            $productId = $row['id'];
            if (!isset($categories[$catMereId]['children'][$catId]['products'][$productId])) {
                $categories[$catMereId]['children'][$catId]['products'][$productId] = [
                    'id' => $productId,
                    'nom' => $row['nom'],
                    'prix' => $row['prix'],
                    'statut' => $row['statut'],
                    'hauteur' => $row['hauteur'],
                    'largeur' => $row['largeur'],
                    'annonce' => $row['annonce'],
                    'etat' => $row['etat'],
                    'description' => $row['description'],
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
    public static function productsRecord() {
        $directory = "../UPLOADS";
        $files = [];
        $images = self::parcourirRecursivement($directory, $files); // Récupérer les images
        $main = 1;
        foreach ($images as $file) {
            error_log("file ".print_r($file, true));
    
            $url = urldecode($file['imageUrl']);  
            error_log("url $url");  
            $name = $file['imageName'];  
            error_log("name $name");  
            $categories = self::extract_categories_from_image_name($url); 
            error_log("categories ".print_r($categories, true)); 
    
            $parentCategory = $categories[0]; 
            error_log("Pcategorie $parentCategory");
            $parentCategoryId = self::saveCategory($parentCategory, "");
            error_log("Pcategorie $parentCategory $parentCategoryId");
    
            $category = $categories[1]; 
            error_log("categorie $category");
            $categoryId = self::saveCategory($category, $parentCategoryId);
            error_log("Categorie $category $categoryId");
    
            // Utiliser le chemin complet
            $file_url = str_replace('../UPLOADS/', '', $url); 
            $productName = explode(".", self::extract_article_name_from_image_name($name))[0];
            error_log("productName ".print_r($productName, true));
            $articleId = self::saveArticle($productName, $categoryId);
            error_log("articleId $articleId");
    
            $product = self::getProduct($articleId);
            if ($product) {
                error_log("product objet ".print_r($product, true));
                // Appeler la méthode addPhoto avec articleId, file_url et main
                $product->addPhoto($articleId, $file_url, $main);
                $main = 0;
                error_log("product objet ".print_r($product, true));
            } else {
                error_log("Produit non trouvé pour l'ID $articleId");
            }
        }
    }
    
    

    public static function productsTransfer()
    {
        error_log("transfer products");
        // Définir le chemin relatif du répertoire racine à scanner
        $directory = "/Users/nathalie/Dropbox/CHECY/VENTES";
        $files  = self::parcourirRecursivement($directory);
        //error_log("imagestransfer \n"); 
        foreach($files as $file)
        {
            $file_url = self::send_to_api($file["imageUrl"], $file["imageUrl"]); // on récupère l'url
            self::record_file($file_url); // on va extraire les catégories et enregistrer le produit et ses photos dans la bdd
            exit;
        }
       
    }


    
    public static function parcourirRecursivement($directory, &$files = []) {
        $dir = opendir($directory);
        error_log("parcours recursife");
    
        while (($file = readdir($dir)) !== false) {
            if ($file != "." && $file != "..") {
                $filePath = $directory . '/' . $file;
    
                if (is_dir($filePath) && $filePath != "MONIQUE") {
                    self::parcourirRecursivement($filePath, $files);
                } else {
                    if (self::is_image($file)) {
                        $imageUrl = urlencode($filePath);
                        $imageName = urlencode($file);
                        $files[] = ["imageUrl" => $imageUrl, "imageName" => $imageName];
                        error_log("c'est une image, on l'envoie à l'api - $filePath - $file");
                    }
                }
            }
        }
        closedir($dir);
        return $files; // Retour explicite des fichiers accumulés
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
    

    public static function extract_categories_from_image_name($normalizedUrl) {
       // imageUrl :  /Users/nathalie/Dropbox/CHECY/VENTES/poelesantiadhesives_01.JPG 
            error_log("getcategories $normalizedUrl");
        $categories = [];
    
        // Extrait le chemin après 'VENTES'

       // $normalizedUrl = str_replace('\\', '/', $url);
    
        // Cherche la position du dossier clé "VENTES" ou "UPLOADS"
        $ventesPos = strpos($normalizedUrl, '/VENTES/');
        $uploadsPos = strpos($normalizedUrl, '/UPLOADS/');
    
        if ($ventesPos !== false) {
            error_log(" ventes");

            // Extrait la partie de l'URL après "VENTES/"
            $subPath = substr($normalizedUrl, $ventesPos + 8); // +8 pour passer "/VENTES/"
        } elseif ($uploadsPos !== false) {
            error_log("upload");

            // Extrait la partie de l'URL après "UPLOADS/"
            $subPath = substr($normalizedUrl, $uploadsPos + 8); // +8 pour passer "/UPLOADS/"
            error_log("path $subPath");

        } else {
            // Si aucun des deux mots clés n'est trouvé, retourne un tableau vide
            error_log("vide");

            return [];
        }
    
        // Supprime le nom du fichier à la fin du chemin
        $subPath = dirname($subPath);
    
        // Découpe le chemin en segments
        $segments = explode('/', $subPath);
        // Nettoie les segments vides éventuels
        $cleanSegments = array_filter($segments, function($value) {
            return !empty($value) && $value != '..' && $value != '.';
        });
        error_log("parts url ".print_r($cleanSegments, true));
        return array_values($cleanSegments);

       
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
    

    public static function getCategory($categoryId)
    {
        //error_log("categoryName  : ". print_r($categoryName, true) ." \n"); 

        $pdo = Admin::db(); // Assurez-vous que cette méthode retourne un objet PDO
        $sql = "SELECT c.id, c.nom categorie, cm.nom catMere, cm.id catMereId FROM categories c 
        LEFT JOIN categories cm ON cm.id = c.`catMere` 
        WHERE c.id = ?";
        $stmt = $pdo->prepare($sql);
        
        // Corrigez le binding du paramètre
        $stmt->bindParam(1, $categoryId);
        
        $stmt->execute();
        
        // FetchAll retourne un tableau de toutes les lignes trouvées, Fetch retourne la première ligne
        $category = $stmt->fetch(\PDO::FETCH_ASSOC); // Utilisez fetch() si vous attendez une seule ligne
       // error_log("category  : ". print_r($category, true) ." \n"); exit();

        return $category;
    }
       public static function getCategoryByName($categoryName)
    {
        //error_log("categoryName  : ". print_r($categoryName, true) ." \n"); 

        $pdo = Admin::db(); // Assurez-vous que cette méthode retourne un objet PDO
        $sql = "SELECT c.id, c.nom categorie, cm.nom catMere, cm.id catMereId FROM categories c 
        LEFT JOIN categories cm ON cm.id = c.`catMere` WHERE c.nom = ?";
         $sql2 = "SELECT c.id, c.nom categorie, cm.nom catMere, cm.id catMereId FROM categories c 
         LEFT JOIN categories cm ON cm.id = c.`catMere` WHERE c.nom = '$categoryName'";
        $stmt = $pdo->prepare($sql);
        
        // Corrigez le binding du paramètre
        $stmt->bindParam(1, $categoryName);
        
        $stmt->execute();
        
        // FetchAll retourne un tableau de toutes les lignes trouvées, Fetch retourne la première ligne
        $category = $stmt->fetch(\PDO::FETCH_ASSOC); // Utilisez fetch() si vous attendez une seule ligne
       error_log("category $sql2   : ". print_r($category, true) ." \n"); 

        return $category;
    }

    public static function saveCategory($categoryName, $parentCategoryId = null)
    {
        $pdo = Admin::db(); // Assurez-vous que cette méthode retourne un objet PDO
        
        // Vérifiez d'abord si la catégorie existe déjà
        $sql = "SELECT id FROM categories WHERE nom = ? ";
        $sql2 = "SELECT id FROM categories WHERE nom = '$categoryName' ";
        error_log("sql select  : $sql2 \n"); 

        $stmt = $pdo->prepare($sql);
        
        // Liez les paramètres à la requête
        $stmt->bindParam(1, $categoryName);

        $stmt->execute();
        
        // Récupérer l'ID si la catégorie existe déjà
        $existingId = $stmt->fetchColumn();
        error_log("existingId  : $existingId \n"); 

        if ($existingId) {
            error_log("existingId ok \n"); 
            return $existingId;
        }

        // Si la catégorie n'existe pas, créez-la
        $sql = "INSERT INTO categories (nom, catMere) VALUES (?, ?)";
        $sql2 = "INSERT INTO categories (nom, catMere) VALUES ('$categoryName', $parentCategoryId)";
        error_log("sql insert : $sql2 \n"); 

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
        error_log("save $articleName, $categoryId ");
        $pdo = \Models\Admin::db(); // Assurez-vous que cette méthode retourne un objet PDO
        
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
        error_log("sql exist $sql2 $existingId");

        if ($existingId) {
            return $existingId;
        }

        // Si la catégorie n'existe pas, créez-la
        $sql = "INSERT INTO articles (nom, catId) VALUES (?, ?)";
        $sql2 = "INSERT INTO articles (nom, catId) VALUES ('$articleName', $categoryId)";
        $stmt = $pdo->prepare($sql);
        
        // Liez les paramètres et exécutez la requête
        $stmt->bindParam(1, $articleName);
        $stmt->bindParam(2, $categoryId);
        $stmt->execute();
        $idprod = $pdo->lastInsertId();
        error_log("sql add $sql2 prod ".$idprod);
        // Retournez l'ID de la nouvelle catégorie
        return $idprod;
    }

        

}
?>