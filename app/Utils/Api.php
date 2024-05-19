<?php 
//Api.php
namespace Utils;


use \Models\Admin;
use \Models\Photos;
use \Models\Files;
use \Models\CSS;


class Api {
/*     private static $username = "nathalie.brigitte@gmail.com"; // Défini comme propriété statique de la classe
    private static $password = "Zorbec_24"; // Défini comme propriété statique de la classe
    private static $base = "https://www.buxfer.com/api"; // Défini ici ou dans buxfer_api.php
 */

 public static function langupdate($langCode)
 {
    header('Content-Type: application/json');

    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method == 'POST') {
        $langCode = $_POST['lang'] ?? 'en'; // Valeur par défaut si rien n'est fourni
        //        error_log("langue choisie : " . $langCode . "\n");

        if ($res = \Models\Admin::setLang($langCode)) // Mettre à jour la langue via la méthode du modèle
       { $response['status'] = 200;}

        echo json_encode($response);


    } else {
        http_response_code(405); // Méthode non autorisée
    }
 }


 public static function getproducts ()
 {
       if ( \Models\Produit::productsTransfer())
        { $response['status'] = 200;}

        echo json_encode($response);
 }

     
        


}
