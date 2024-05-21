<?php 
//Api.php
namespace Utils;


use \Models\Admin;
use \Models\Photos;
use \Models\Files;
use \Models\CSS;
use \Models\Produit;


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

 public static function  update_products ()
 {
    $response = [];
       if ( \Models\Produit::productsRecord()) 
        { $response['status'] = 200;}

        echo json_encode($response);
 }
 

 public static function deleteProduct() {
    $data = $_POST;

    error_log("data reçures dans api deleteProduct ".print_r($data));
    $productId = $data['productId'];

    $product =   \Models\Produit::getProduct($productId);
    error_log("profuit ".print_r($product, true));

    if ($product) {
        $product->delete($productId);
        $response['status'] = 200;
    } else {
        $response = ['success' => false, 'message' => 'Product not found'];
    }
    //error_log("response ".print_r($response, true));
    //header('Content-Type: application/json');
    echo json_encode($response); exit();
}
    public static function saveProductChanges() {
        $data = $_POST;
        error_log("data reçures dans api saveProductChanges ".print_r($data));
        $productId = $data['productId'];
        unset($data['productId']);

        $product = \Models\Produit::getProduct($productId); // Suppose que getProduct renvoie un objet Produit
        if ($product) {
            $product->update($data);
        // $response = ['success' => true, 'message' => 'Product updated successfully'];
            $response['status'] = 200;
        } else {
            $response = ['success' => false, 'message' => 'Product not found'];
        }
        //error_log("response ".print_r($response, true));
        //header('Content-Type: application/json');
        echo json_encode($response); exit();
    }

    public static function descriptionGen() {
        $data = $_POST;
    
        error_log("data reçures dans api deleteProduct ".print_r($data));
        $productId = $data['productId'];
    
        $product =  new Produit($productId);
        if ($product) {
            $product->delete($productId);
            $response['status'] = 200;
        } else {
            $response = ['success' => false, 'message' => 'Product not found'];
        }
        //error_log("response ".print_r($response, true));
        //header('Content-Type: application/json');
        echo json_encode($response); exit();
    }

}
