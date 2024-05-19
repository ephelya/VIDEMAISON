<?php
namespace Models;
use \Models\Admin;

class Classe_base {
    public $propriete;
    private $pdo;

    public function __construct($apiKey, $apiUrl) {
        $this -> pdo = \Models\Admin::db();
    }


    public static function function ($data)
    {
        $pdo = $this -> pdo;
        return $data;
    }
}

?>
