<?php
    namespace Controllers;

     use \Models\Page; // Récupérer le ou les modèles nécessaires pour l'exécution des fonctions 
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    class PageController {
        public static function getPageData($page){                       
            $pageData = array();
            return ($pageData);
        }
    }

?>
