<?php
namespace Models;

use \Models\Admin;

class Photos {
    public static function record($url, $filename) {
        $pdo = \Models\Admin::db();  // Assurez-vous que cette méthode retourne bien une instance de PDO.
    
        // Assurez-vous que les colonnes et les noms des placeholders sont corrects.
        $stmt = $pdo->prepare("INSERT INTO Photos (url, name) VALUES (:url, :name)");
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':name', $filename);
    
        if ($stmt->execute()) {
            // En cas de succès, retourner l'ID du dernier enregistrement.
            return $pdo->lastInsertId();
        } else {
            // En cas d'échec, loguer les erreurs.
            error_log("Erreur SQL : " . implode(";", $stmt->errorInfo()) . "\n");
            return false;
        }
    }
    
}

?>