<?php
//ApiController
namespace Controllers;

use \Models\Membres;
use \Models\Admin;
use \Utils\Api;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApiController {
    public static function getApi($api) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //var_dump($_POST);
            // Récupérer directement les données POST et FILES, pas besoin de lire php://input
            error_log("apiValue reçu de post: $api " . print_r($_POST, true)."\n");
            if ($apiValue = \Utils\Api::$api($_POST))// {error_log("err envoi : " . print_r($apiValue, true)."\n");  } // Passer $_POST directement
            //error_log("apiValue recup: " . print_r($apiValue, true)."\n");

            // Envoyer la réponse en JSON
            {echo json_encode($apiValue);}
        }
        else // methode get ou autre, l'api est définie en fonction public statique dans Utils/Api.php
        {
            $result = \Utils\Api::$api($api); 
            error_log("apiValueresult: " . print_r($result, true)."\n");

            return ($result);
        }
    }


    
    public static function get_accounts()
    {
        $get_accounts_api = \Utils\get_accounts;
      //  echo "toupi";
    }
    public function getCSV($type) {
        // Envoyer la réponse en JSON
       // $result = ("on cre le csv $type");
        $data = json_decode(file_get_contents('php://input'), true);
        $tableName = $data['tableName'];
        $filePath = 'UPLOADS/CSV/' . $tableName . '_CSV.csv';        
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Pragma: no-cache');
        // Récupérez le nom de la table à partir de la requête AJAX


        $pdo = \Models\Admin::db();
        try {
            // Récupérer les noms de colonnes de la table
            $stmt = $pdo->query("DESCRIBE $tableName");
            $columns = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            //echo json_encode( $columns); exit();

            // Chemin du fichier CSV à créer
            $path = "../UPLOADS/CSV/";
            $filePath = $path.$tableName."_csv.csv";


            // Créer le fichier CSV
            $file = fopen($filePath, 'w');
            fputcsv($file, $columns);
            fclose($file);

            // Retourner le fichier CSV

            readfile($filePath);
        } catch(\PDOException $e) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
            exit();
        }
        

    }

    public function addSession() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['mode'])) {
                $_SESSION['mode'] = $_POST['mode'];
                echo "Mode de session mis à jour : " . $_SESSION['mode'];
            } else {
                echo "Erreur : Mode non spécifié.";
            }
        } else {
            echo "Erreur : Requête non autorisée.";
        }
    }
}
