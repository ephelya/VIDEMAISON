<?php
namespace Models;

use \Models\Admin;

class Files {
    public static function record($name, $url) {
        $pdo = \Models\Admin::db();

        // Préparation de la requête d'insertion
        $stmt = $pdo->prepare("INSERT IGNORE INTO Files (url, name, description) VALUES (:url, :name)");
        $stmt->bindParam(':url', $url);

        // Exécution de la requête
        if ($stmt->execute()) {
            return $pdo->lastInsertId(); // Retourne l'ID du fichier enregistré
        } else {
            return false; // En cas d'échec
        }
    }
}

?>