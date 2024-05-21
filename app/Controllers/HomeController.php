<?php
namespace Controllers;

use \Models\Pages;
use \Models\Sublym_Membres;
use \Models\Membres;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController { 

    public function getHomePageData() {
        $user = \Models\Sublym_Membres::getUser($_SESSION["userId"]);
        $pageData["page"] = "accueil";
        if ($twig = \Models\Pages::home_content())
       { $pageData["twig"] = $twig; }
        // on vérifie dans la bdd : si une landing est définie, on l'affiche, s'il y en a plusieurs, 
        //on les active à tour de rôle en A/B test, sinon on affiche l'accueil classique 
        $botFilePath= APPDIR . "/views/bot.twig";
        $botExists = file_exists($botFilePath); //if ($botExists) { echo "tw "; } else {echo "no";}
        $chatbot = $botExists ? "bot.twig" : "chatbot.twig";
        $categories = \Models\Produit::listArticlesWithCategories();
        $pageData["categories"] = $categories;
        //print_r($pageData);

        return ($pageData);
    }
}
