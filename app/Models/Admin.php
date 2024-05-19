<?php
namespace Models;

class Admin {
    


    public static function db()
    {
        $host =  $_ENV['HOST'] ?? getenv('HOST'); 
        $db   = $_ENV['DB'] ?? getenv('DB');
        $user = $_ENV['USER'] ?? getenv('USER');
        $pass =  $_ENV['PASS'] ?? getenv('PASS');
        $charset =  $_ENV['CHARSET'] ?? getenv('CHARSET');

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        //error_log(date("d/m/y H:i")." Attempting to connect: Host=$host, DB=$db, User=$user");
//        echo  "Host=$host, DB=$db, User=$user";

        try {
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $pdo = new \PDO($dsn, $user, $pass, $options);
            error_log("Connection successful\n");
            return $pdo;
        } catch (\PDOException $e) {
            error_log("Connection error: " . $e->getMessage() . "\n");
            return null;
        }
    }



    public static  function envoyerEmail($destinataire, $nomDestinataire, $sujet, $message, $expediteur = 'noreply@votredomaine.com', $nomExpediteur = 'Nom par défaut') {
        // En-têtes
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: ' . $nomExpediteur . ' <' . $expediteur . '>' . "\r\n";

        // Envoi de l'email
        if(mail($nomDestinataire . ' <' . $destinataire . '>', $sujet, $message, $headers)) {
            return true;
        } else {
            return false;
        }

    }

    public static function navigation_links()
    {
       
        $array = [
            [  "id" => "accueil",  "label" => "Accueil" ,  "url"=> HOME ] ,
            [ "id" => "contacts" , "label" => "Contacts" ,  "url"=> HOME."contacts"  ],
            [ "id" => "finances" ,  "label" => "Finances" ,  "url"=> HOME."finances"  ],
            ["id" => "projets" , "label" => "Projets" ,  "url"=> HOME ."projets" ]
        ];
        
        return $array;
    }


    #       DATES       #
    public static function getdate($day, $format, $lang)
    {   
        date_default_timezone_set('Europe/Paris');
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr', 'fra', 'french');
       // echo "time ".date('d F Y');
        
        $date = new \DateTime($day);
    
        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN,
            $format  // Utilisez le format ici
        );
    
        // Formattez et affichez la date
        $formattedDate = $formatter->format($date);
       // echo "date formatée: $format $formattedDate";
    
        return $formattedDate;
    }
    
    public static function getweekdate($date)
    {
        // Trouver le lundi de cette semaine
        $monday = clone $date;
        $monday->modify('Monday this week');
        $mondayString = $monday->format('d/m');

        // Trouver le vendredi de cette semaine
        $friday = clone $monday;
        $friday->modify('Friday this week');
        $fridayString = $friday->format('d/m');

        // Obtenir le numéro de la semaine
        $weekNumber = $date->format('W');

        // Formatter la chaîne de sortie
        $output["week"] = "Lun $mondayString - Ven $fridayString";
        $output["weeknb"] = $weekNumber;
        return $output;
    }

    public static function formatRelativeDate($dateString) {
        $aboDate = new \DateTime($dateString);
        $now = new \DateTime(); // la date actuelle
        $interval = $now->diff($aboDate);
    
        if ($interval->y > 0) {
            $since = $interval->y . ($interval->y > 1 ? " ans" : " an");
        } elseif ($interval->m > 0) {
            $since = $interval->m . " mois";
        } elseif ($interval->d > 0) {
            $since = $interval->d . ($interval->d > 1 ? " jours" : " jour");
        } else {
            $since = "aujourd'hui";
        }
    
        return $since;
    }

    public static function formatDateFullFrench($dateString) {
        $aboDate = new \DateTime($dateString);
        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN,
            'EEEE d MMMM yyyy'
        );
        return $formatter->format($aboDate);
    }

    //LANG 

    public static function getDefaultUserLanguage() {
        $lang = "en"; // Langue par défaut
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
        return $lang;
    }

    
    public static function loadTranslations($lang) {
        $pdo = self::db();
        // Utilisez votre connexion à la base de données pour exécuter la requête
        $stmt = $pdo->prepare("SELECT `key`, value FROM translations WHERE lang = ?");
        $stmt->execute([$lang]);
        $translations = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR); // Récupère les résultats comme un tableau associatif clé => valeur
        return $translations;
    }
    
    public static function getTranslationWithVariable($key, $lang, $variables) {
        $translation = self::loadTranslationByKey($key, $lang); // Charger la traduction de la base de données
        foreach ($variables as $placeholder => $value) {
            $translation = str_replace("{" . $placeholder . "}", $value, $translation);
        }
        return $translation;
    }
    
/*     public static function importTranslationsFromCSV($filePath) {
        if ($filePath === "") {
            $filePath = __DIR__ . '/../../translations/languages.csv';
        }
        $pdo = self::db();
    
        // Préparer la requête SQL pour insérer ou mettre à jour les traductions
        //$sql = "INSERT INTO translations (lang, `key`, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value=VALUES(value)";
        $sql = "INSERT INTO translations (lang, `key`, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value=VALUES(value)";
        $stmt = $pdo->prepare($sql);
    
        // Ouvrir le fichier CSV
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $header = fgetcsv($handle); // Ignorer les en-têtes
            while (($data = fgetcsv($handle)) !== FALSE) {
                // $data est un tableau avec [lang, key, value]
                $stmt->execute($data);
            }
            fclose($handle);
        }
    }
 */
    public static function loadTranslationByKey($key, $lang) {
        $pdo = self::db();  // Obtenez votre instance PDO, adapter selon votre méthode de connexion à la base de données
    
        // Préparer la requête pour sélectionner la traduction
        $sql = "SELECT value FROM translations WHERE `keyId` = '$key' AND lang = '$lang'"; //echo "sql $sql";
        $sql = "SELECT value FROM translations WHERE `keyId` = ? AND lang = ?";
        $stmt = $pdo->prepare($sql);
    
        // Exécuter la requête
        $stmt->execute([$key, $lang]);
    
        // Récupérer le résultat
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    
        // Retourner la valeur de la traduction, ou une chaîne vide si rien n'est trouvé
        return $result ? $result['value'] : '';
    }
    
    

    
    
    public static function languageList($status = null) {
            $pdo = \Models\Admin::db();
            $query = "SELECT l.*, l.id as langId, `code` as `id`, `code` as `valeur` FROM languages l JOIN siteLangs s ON l.id = s.langId ";
            if (isset($status)) {
                $query .= " WHERE s.status = :status";
            }
            $query .= " ORDER BY l.code ASC ";
            $stmt = $pdo->prepare($query);
            if (isset($status)) {
                $stmt->bindParam(':status', $status, \PDO::PARAM_INT);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);
            //print_r($result);
            return $result;
        }
        
    public static function setLang($lang) {

       $_SESSION['lang']=$lang;
    }    
    
    public static function okLang($lang) {
        $pdo = self::db();  // Récupérer l'instance PDO de la connexion à la base de données
        $query = "SELECT status FROM siteLangs JOIN languages ON siteLangs.langId = languages.id WHERE languages.code = :lang";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':lang', $lang, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result && $result['status'] = 1) {
            return $lang; 
        } else {
            return "EN"; // Retourner "EN" si la langue n'est pas active ou n'est pas trouvée
        }
    }


    public static function translate ($message, $lang)
    {
        $pdo = self::db();  // Récupérer l'instance PDO de la connexion à la base de données
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY');
       // error_log("apiKey admin $apiKey\n");
        $conversationHistory= [
                ['role' => 'user', 'content' => "Traduis le texte suivant en $lang: '$message' et ne traduis surtout pas le texte contenu entre accolades comme {{user}} par exemple. Donne moi uniquement la traduction sans guillemets et  n'ajoute aucun texte ni avant ni après"]
            ];
        //        print_r($data);

        $result = \Models\OpenAIClient::translateText($conversationHistory, $apiKey ,"");
       // error_log("POST Data: " . print_r($result, true) . "\n");
        return($result);
    }

    public static function addOAIaction($actionType, $date, $action)
    {
        $pdo = self::db(); 
       // error_log("ok add addOAIaction admin $actionType, $date, $action $sql1\n");

        $sql = "INSERT INTO OAIActions (actionType, dateAction, `action`) VALUES (:actionType, :dateAction, :action)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':actionType', $actionType);
        $stmt->bindParam(':dateAction', $date);
        $stmt->bindParam(':action', $action);
        $stmt->execute();


        return $pdo->lastInsertId();
    }

    public static function getOAIActionTypes()
    {
        $pdo = \Models\Admin::db(); 
        
        $stmt = $pdo->query("SELECT * FROM OAIActionTypes");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function addOAIconso($dateaction, $actionId, $conso, $eval)
    {
        $pdo = self::db(); 

        $sql1 = "INSERT INTO OAIconsos (dateaction, actionId, conso, eval) VALUES ('$dateaction', '$actionId', '$conso', '$eval')"; 
        $sql = "INSERT INTO OAIconsos (dateaction, actionId, conso, eval) VALUES (:dateaction, :actionId, :conso, :eval)";
        error_log("ok addOAIconso $actionId translate, $dateaction, $conso $eval $sql1 \n"); //ok 

        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);

        // Lier les paramètres à la requête
        $stmt->bindParam(':dateaction', $dateaction);
        $stmt->bindParam(':actionId', $actionId);
        $stmt->bindParam(':conso', $conso);
        $stmt->bindParam(':eval', $eval);

        // Exécuter la requête
        $stmt->execute();

        // Retourner l'ID de la dernière insertion
        return $pdo->lastInsertId();

    }

}
    
    

