<?php
namespace Models;

class AdminSite extends Admin  {
    
/*     public static function db()
    {
        $db = "../app/config.php";
        include($db);
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $pdo = new \PDO($dsn, $user, $pass);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                return $pdo;
        }
            catch (\PDOException $e) {
                // Gérez les erreurs de connexion ici
                error_log ('Erreur de connexion à la base de données : ' . $e->getMessage());
                return null;
            }
    }
 */

    //      PARAMS SITE //
    public static function css_theme()
    {
        return "css";
    }

    public static function default_landing()
    {
        //return "landing2";
        return false;
    }

} 