<?php
    namespace Controllers;

    use \Models\Admin; 
     use \Models\Page; // Récupérer le ou les modèles nécessaires pour l'exécution des fonctions 
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    class ProfilController {
        public static function getPageData($page){     
            $user = \Models\Membres::getUser($_SESSION["userId"]); //print_r($user);
            $pageData = array();
            if (is_object($user)) {
                // Parcourir chaque propriété de l'objet $user
                foreach ($user as $propName => $propValue) {
                    // Formater la clé et ajouter la propriété au tableau $pageData
                    $pageData[$propName] = $propValue;
                }
                $pageData['photoUser'] = "UPLOADS/MEMBRES/".$user -> idMembre.".jpg";
                $pageData["dateSubscr"] =  \Models\Admin::formatDateFullFrench($user -> dateSubscr);
                $pageData["abonSince"] = \Models\Admin::formatRelativeDate($user -> dateSubscr);
            }
            return ($pageData);
        }

        
        
        
    }

?>
