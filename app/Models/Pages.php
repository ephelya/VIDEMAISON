<?php

namespace Models;
use \Models\Admin;
use \Models\OpenAIClient; // Récupérer le ou les modèles nécessaires pour l'exécution des fonctions 
use \Models\Membres; // Récupérer le ou les modèles nécessaires pour l'exécution des fonctions 
use \Utils\Forms; // Récupérer le ou les modèles nécessaires pour l'exécution des fonctions 

class Pages {
    public $id;
    public $propriete;

    public static function getpage($pageName)
    {
        $pdo = \Models\Admin::db();
        $sql = "SELECT * FROM Pages WHERE name = '$pageName'"; //echo $sql;
        $sql = "SELECT * FROM Pages WHERE name = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $pageName, \PDO::PARAM_STR);
        $stmt->execute();
        $result =  $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (!empty($result))
        {$page =$result[0]; }
        else { $page = self::getpage("page"); }
        return $page;
    }

    public static function baseData ($pageName)
    {
        error_log("pages base");
       // echo "home ".$appPath = dirname(realpath(__DIR__));
       $lang = $_SESSION["lang"] = isset($_SESSION["lang"]) ? $_SESSION["lang"] : "FR";
       $langselect = self::langselect($lang); //echo "lang $langselect";
        $page = self::getpage($pageName );
      //  $userId = $_SESSION["userId"];
       // $username = \Models\Membres::getUser($userId) -> identifiant;        
       // $welcomeMessage = \Models\Admin::getTranslationWithVariable("bienvenue", $lang, ["username" => $username]);
        $contentsList = self::getPageContents($pageName, $lang); //print_r($contentsList);
        error_log("contente ".print_r($contentsList, true));
        //echo "welcomeMessage $welcomeMessage";

        $pageTitle = $page -> title;
        $pageFollow = $page -> follow;
        $pageDescription = $page -> description;
        $pageName = $page -> name; //echo "page $pageName";
        //$menus_links = \Models\Menus::getMenusLinks($pageName); print_r($menu_links);

        $pageMenus = \Models\Menus::getMenus($pageName); //print_r($pageMenus);
        foreach ($pageMenus as $menu)
        {
            $menuName = $menu ->name;
            $menus_links[$menuName] = \Models\Menus::getMenusLinks($menuName);// 
            //print_r($menus_links);
        }
        $css_theme = \Models\AdminSite::css_theme();

        $headerFilePath = APPDIR . "/views/" . $pageName . "_header.twig"; //echo $headerFilePath;
        $twigFilePath= APPDIR . "/views/" . $pageName . "_content.twig";
        $sectionTemplatePath =  APPDIR . "/views/" .$pageName . "_sections.twig";

        $headerExists = file_exists($headerFilePath);
        $twigExists = file_exists($twigFilePath); //if ($twigExists) { echo "tw "; } else {echo "no";}
        $sectionTemplateExists = file_exists($sectionTemplatePath);//if ($sectionTemplateExists) { echo "st "; } else {echo "no";}

         
        $headertwig = $headerExists ? $pageName . "_header.twig" : "_header.twig";
        $pagetwig = $twigExists ? $pageName . "_content.twig" : "page_content.twig";
        $sectionTwig = $sectionTemplateExists ? $pageName . "_sections.twig" : "_sections.twig";

        $sections = self::getsectionList($page -> name); //print_r($sections);
        $listsections = [];
        foreach ($sections as $section)
        {
            $name = $section["name"];
            $content = $section["section_content"];
            $id = $section["id"];

            $section['contents'] = self::sectionContents($id, $lang);
            $listsections[] = $section;
        }
        //print_r($section);

        $google_ident = "G-XXXX";
        $protocol = 'https://';
        $pageurl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $pageData = [
            'home' => HOME,
            'page_name' => $pageName ,
            'langselect' => $langselect,
            'css_theme' => $css_theme,
            'page_title' => $pageTitle ,
            'page_description' => $pageDescription ,
            'page_follow' => $pageFollow,
            'share_img' => '', //url de l'image à partager
            "headertwig" => $headertwig,
            "twig" => $pagetwig,
            "sectionTemplateName" => $sectionTwig,
            'google_ident' => $google_ident,
            'page_url' => $pageurl,
            "TemplatePath" => $twigFilePath,
            "sections" => $listsections,
            "logo_svg_url" => HOME."img/logo_".LANG.".svg",
            "logo_jpg_url" => HOME."img/logo_".LANG.".jpg",
           // "texts" => $texts,

        ];
     
        $pageData["contents"] = self::getPageContents($pageName, $lang);

        return $pageData;
    }

    
    public static function pageCreate($name, $title, $description, $status)
    {
        $pdo = \Models\Admin::db();
        $sql = "INSERT INTO Pages (name, title, description, status) VALUES (:name, :title, :description, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, \PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
        $stmt->execute();

        return $pdo->lastInsertId();
    }

    public static function pageDelete($pageId)
    {
        $pdo = \Models\Admin::db(); 
        $sql = "DELETE FROM Pages WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $pageId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount(); // Retourne le nombre de lignes affectées
    }

    public static function pageEdit($pageId, $name, $title, $description, $status)
    {
        $pdo = \Models\Admin::db();
        $sql = "UPDATE Pages SET name = :name, title = :title, description = :description, status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $pageId, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, \PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount(); // Retourne le nombre de lignes affectées
    }

    public static function getsectionList($pageIdent)
    {
        $pdo = \Models\Admin::db();
        $sql = "SELECT Sections.id, Sections.sectionName name, CONCAT('_S_', Sections.sectionName, '_content.twig') section_content  
        FROM Sections JOIN Pages ON Sections.pageId = Pages.id WHERE Pages.name = '$pageIdent' AND Sections.status = 'published'"; //echo $sql;
        $sql = "SELECT Sections.id, Sections.sectionName name, CONCAT('_S_', Sections.sectionName, '_content.twig') section_content  
        FROM Sections JOIN Pages ON Sections.pageId = Pages.id WHERE Pages.name = :pageIdent AND Sections.status = 'published' ORDER BY `position` DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':pageIdent', $pageIdent, \PDO::PARAM_STR);
        $stmt->execute();

        $sections = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        return $sections;
    }

    public static function sectionContents($id, $lang)
    {
        $pdo = \Models\Admin::db();
        $sql = "SELECT 
        subquery.*,
        t.value AS translated_content
        FROM (
            SELECT *, ROW_NUMBER() OVER (PARTITION BY contentIdent ORDER BY contentIdent ASC) as rn
            FROM sectionContents
            WHERE sectionId = :sectionId AND abtest IS NULL
        ) AS subquery
        LEFT JOIN translations AS t ON subquery.content = t.`key` AND t.lang = :lang
        WHERE subquery.rn = 1
        ORDER BY subquery.contentIdent ASC;";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':sectionId', $id, \PDO::PARAM_INT);  // Assuming `sectionId` is an integer
        $stmt->bindParam(':lang', $lang, \PDO::PARAM_STR);     // Assuming `lang` is a string
        $stmt->execute();

        $contents = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        return $contents;
    }


    public static function home_content()
    {
        //on récupère la version à utiliser, au cas où on serait en abtest
        if ($default_landing = \Models\AdminSite::default_landing())
        {
            $homecontent = $default_landing.".twig";
            return $homecontent;
        }
        else return false;
    }

    public static function formupload()
    {   
        $formconfig = [
            'formId'   =>  "formId",
            'enctype' => "multipart/form-data",
            'size' => "large",
            'float' => 1,
            'label' => "down",
            'trait' => "#",
            'fields' => [
                ['label' => 'label', "id" => "label", 'name' => "upload", 'type' => 'file', "required" => 0],
                ['name' => "table", "type" => "hidden", "value" => "Photos"], // supprimer ou ajuster ce champ - value est le nom de la tale dans laquelle on enregistrera le nom et l'url du fichier 
                ['label' => "Nom", 'name' => "name", "type" => "text", "placeholder" => "Nom"],
                ["value" => "Ajouter", 'class' => 'uploadClass',  "id" => "valid", 'type' => 'submit'],
            ],
            "script" => "<script>document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('formId').onsubmit = function(event) {
                    event.preventDefault(); // Empêcher le rechargement de la page
            
                    var formData = new FormData(); // Création d'un objet FormData
                    var fileInput = document.getElementById('label'); // Accéder à l'input du fichier
                    var file = fileInput.files[0]; // Prendre le premier fichier
                    formData.append('upload', file); // Ajouter le fichier à l'objet FormData
                    formData.append('name', document.querySelector('[name=\"name\"]').value);
                    formData.append('table', document.querySelector('[name=\"table\"]').value);
            
            
            
                    // Création et envoi de la requête AJAX
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', " . Homme. ", true);
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            alert('Fichier uploadé avec succès !');
                            console.log(xhr.responseText);
                        } else {
                            alert('Erreur lors de l\'upload du fichier.');
                        }
                    };
                    xhr.send(formData); // Envoyer la requête avec le fichier
                };
            });</script>
            "
        ];

        return Forms::generateForm($formconfig);    
    }


    public static function addcontent_form()
    {   
        $pagelist = self::pageList(); //print_r($pagelist);
        $list = new \stdClass(); // Initialisation de $list comme un objet
        foreach ($pagelist as $page) {
            $item = new \stdClass(); // Créer un nouvel objet pour chaque page
            $item->id = $page['name']; // Assigner l'id à la propriété id de l'objet
            $item->valeur = $page['title']; // Assigner le titre à la propriété valeur de l'objet

            $list->{$page['id']} = $item; // Ajouter l'objet à $list en utilisant l'id comme clé
        }

        $actiontypes = \Models\Admin::getOAIActionTypes(); //print_r($pagelist);
/*         $typesList = new \stdClass();
        foreach ($actiontypes as $actiontype) {
            print_r($actiontype);
            $item = new \stdClass(); // Créer un nouvel objet pour chaque page
            $item->id = $actiontype['actionType']; // Assigner l'id à la propriété id de l'objet
            $item->valeur = $actiontype['actionType']; // Assigner le titre à la propriété valeur de l'objet

            $typesList->{$actiontype['actionType']} = $item; // Ajouter l'objet à $list en utilisant l'id comme clé
        } */
        $formconfig = [
            'formId'   =>  "formId",
            'enctype' => "multipart/form-data",
            'size' => "large",
            'float' => 1,
            'label' => "down",
            'trait' => "#",
            'fields' => [
                ["id" => "page", 'name' => "page", "type" => 'select', "options" => $list, "firstopt"=>"", "optdef"=>""],
                ["id" => "abtest", 'name' => "abtest", 'type' => 'checkbox', "label"=> "abtest"],
                ["id" => "type", 'name' => "type", 'type' => 'radio', "required" => 1, "optdef"=>"text", "options" => [["name" => "type", "id" => "img", "value" => "img", "label" => "image"], ["name" => "type","id" => "text", "value"=> "text", "label" => "texte"]]],
                ["id" => "action", 'name' => " action", "type" => 'hidden', "value" => "addcontent"], 
                ["id" => "contType", 'name' => "contType", "type" => 'hidden', "value" => "4"], 
                ['label' => 'label', "id" => "file", 'name' => "upload", 'type' => 'file', "required" => 0],
                ['label' => "action", "id"=>'action', 'name' => "action", "type" => "text", "placeholder" => "Action", "required" => 1],
                ['label' => "clé", "id"=>'key', 'name' => "key", "type" => "text", "placeholder" => "Identifiant"],
                ['label' => "Description", "id"=>'description', 'name' => "description", "type" => "text", "placeholder" => "Description"],
                ["id"=>'content', 'name' => "content", "label" => "Contenu", "type" => "textarea", "msg" => ""],
                ["value" => "Ajouter", 'class' => 'uploadClass',  "id" => "valid", 'type' => 'submit'],
            ],

            "script" => "<script>
            
            document.addEventListener('DOMContentLoaded', function () {
                var elementsToHide = document.querySelectorAll('.uploadform .form-floating, .uploadform textarea, .uploadform button');

                // Vérifier si les éléments existent et les masquer
                elementsToHide.forEach(function(element) {
                    if (element) {
                        element.style.display = 'none';
                    }
                }); 

                const radios = document.querySelectorAll('input[name=\"type\"]');

                // Ajouter un écouteur d'événements à chaque bouton radio
                radios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        // Si un bouton radio est sélectionné, mettre à jour la valeur de l'input caché
                        if (this.checked) {
                            document.getElementById('typelist').value = this.value;
                            console.log ('vleur input'+   document.getElementById('typelist').value );
                        }
                    });
                });
            
                // S'assurer que les boutons radio sont visibles
                var radioButtons = document.querySelectorAll('.uploadform input[type=\'radio\']');
                radioButtons.forEach(function(radio) {
                    if (radio) {
                        radio.style.display = '';
                    }
                });

                
                document.getElementById('formId').onsubmit = function(event) {
                    event.preventDefault(); // Empêcher le rechargement de la page
                    var formData = new FormData(); // Création d'un objet FormData
                    var fileInput = document.getElementById('file'); // Accéder à l'input du fichier
                    var file = fileInput.files[0]; // Prendre le premier fichier
                    formData.append('upload', file); // Ajouter le fichier à l'objet FormData
                    formData.append('page', document.querySelector('[name=\"page\"]').value);
                    formData.append('action', document.querySelector('[name=\"action\"]').value);
                    formData.append('name', document.querySelector('[name=\"key\"]').value);
                    var isChecked = $('#abtest').prop('checked');
                    if (isChecked) { var abtest = 1;} else {var abtest = 0; }
                    formData.append('abtest',abtest);
                    formData.append('table','Images');
                    formData.append('contType', document.querySelector('[name=\"contType\"]').value);
                    formData.append('type', document.querySelector('[name=\"type\"]').value);
                    if (description!==undefined)
                   { formData.append('description', document.querySelector('[name=\"description\"]').value);}
                    formData.append('content', document.querySelector('[name=\"content\"]').value);

            
                    // Création et envoi de la requête AJAX
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', " . json_encode(HOME . "api/addcontent") . ", true);
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            alert('Fichier uploadé avec succès !');
                            console.log(xhr.responseText);
                        } else {
                            alert('Erreur lors de l\'upload du fichier.');
                        }
                    };
                    xhr.send(formData); // Envoyer la requête avec le fichier
                };
            });</script>
            "
        ];

        return Forms::generateForm($formconfig);    
    }
 
    public static function langselect($langdef)
    {
        $langs = \Models\Admin::languageList(); //print_r($langs);
        $formconfig = [
            'size' => "large",
            'float' => 1,
            "method" => "post",
            'label' => "down",
            'trait' => "#",
            'formId'   =>  "languageForm",
            'name'  => "langselect",
            "fields" => [ [
                "type" => "select",
                "id" => "languageSelect",
                'optdef' => strtoupper($langdef),
                'options' => $langs,
            ]],
            "script" => "<script>
            document.addEventListener('DOMContentLoaded', function () {            
                document.getElementById('languageSelect').onchange = function() {
                    var selectedLang = this.value;
                    console.log('select '+selectedLang);
                    var formData = new FormData();
                    formData.append('lang', selectedLang);
            
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', " . json_encode(HOME . "api/langupdate") . ", true);
                    xhr.onload = function () {
                        if (xhr.status) {
                            // Recharger la page pour que les changements prennent effet
                            window.location.reload();
                        } else {
                            alert('Erreur lors de la mise à jour de la langue.');
                        }
                    };
                    xhr.send(formData);
                };
            });
            </script>"];
            $form = \Utils\Forms::generateForm($formconfig);  //echo "form $form ";
            return   $form ;
    }

    public static function pageList()
    {
        $pdo = Admin::db(); 
        $sql = "SELECT * FROM Pages ORDER BY title ASC";
        $stmt = $pdo->prepare($sql);


        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC); //
        foreach($results as $page)
        {
            $pages[$page['id']]= $page;
            $pages[$page['id']]['url'] = HOME.$page["name"]; 
        }
        return $pages;
    }
    public static function addContent($message, $key, $lang, $abtest, $page, $contType, $description)
    {
       error_log("pages  $key  : $message $lang abtest $abtest\n");

       $pdo = Admin::db(); // Utilisez votre méthode existante pour obtenir une connexion PDO
       $checkSql = " SELECT c.id FROM pageContents pc 
       LEFT JOIN  contents c  ON  c.id=pc.contentId  
       WHERE`key` = '$key'  AND pageName= '$page'";
       error_log("sql  $key  : $checkSql\n");

       $checkSql = "SELECT c.id FROM pageContents pc 
       LEFT JOIN  contents c  ON  c.id=pc.contentId  WHERE `key` = ? AND pageName= ?";
       $checkStmt = $pdo->prepare($checkSql);
       $checkStmt->execute([$key, $page]);
       $row = $checkStmt->fetch(\PDO::FETCH_ASSOC); // Fetch the row as an associative array
      // error_log("sql res  : ". print_r($row, true)."\n");

       if ($row) {
           $contentID = $row['id'];
           error_log("$lang existe  $new  $contentID  \n");
           if ($abtest == 1)
           {
            $sql = "UPDATE pageContents SET abtest = 1 WHERE  pageName = ? AND contentId = ?";
            $checkStmt = $pdo->prepare($sql);
            $checkStmt->execute([$page, $contentID]);
           }
 
       } else 
        { $new = "new";

           // Si l'enregistrement n'existe pas, insérer un nouveau
           $insertSql = "INSERT INTO contents (`key`, description) VALUES (?, ?)";
           $insertStmt = $pdo->prepare($insertSql);
           try {
               $insertStmt->execute([$key, $description]);
               $contentID = $pdo->lastInsertId();  // Retourner l'ID du nouvel enregistrement
               error_log("$lang content ajouté $new $contentId \n");

           } catch (PDOException $e) {
               // Gestion des erreurs
               error_log("Error in ContentManager::addOrUpdateContent - " . $e->getMessage()."\n");
               return false;  // Retourner false en cas d'erreur
           }

           $sql = "INSERT IGNORE INTO pageContents (pageName, contentId, contType, abtest) VALUES (?, ?, ? , ?);";
           $checkStmt = $pdo->prepare($sql);
           $checkStmt->execute([$page, $contentID, $contType, $abtest]);
       };
       

       try {
        $pdo = Admin::db();  // Assurez-vous que cette fonction retourne un objet PDO valide
    
        // Vérification initiale pour voir si la combinaison keyId/lang existe
        $stmt = $pdo->prepare("SELECT value FROM translations WHERE `keyId` = ? AND lang = ?");
        error_log("sql verif SELECT value FROM translations WHERE `keyId` = $contentID AND lang = $lang .\n");
        $stmt->execute([$contentID, $lang]);
        $existingValue = $stmt->fetchColumn();
        error_log("existingValue: '$existingValue', message: '$message', existingValue === false: " . ($existingValue === false ? 'true' : 'false') . ", existingValue !== message: " . ($existingValue !== $message ? 'true' : 'false') . "\n");

        if ($abtest == 1) {
            // En mode abtest, vérifiez et insérez seulement si la combinaison keyId/lang/value n'existe pas
            if ($existingValue === false || $existingValue !== $message) {
                $sql = "INSERT INTO translations (lang, `keyId`, value) VALUES ('$lang', $contentID,' $message')";
                error_log("sql $sql\n");

                $stmt = $pdo->prepare("INSERT INTO translations (lang, `keyId`, value) VALUES (?, ?, ?)");
                $stmt->execute([$lang, $contentID, $message]);
                error_log("Insertion unique en mode abtest pour $lang, $contentID, $message.\n");
            }
        } else {
            // Si abtest est désactivé, on met à jour value si keyId/lang existe déjà, sinon on insère
            if ($existingValue !== false) {
                $stmt = $pdo->prepare("UPDATE translations SET value = ? WHERE `keyId` = ? AND lang = ?");
                $stmt->execute([$message, $contentID, $lang]);
                error_log("abtest =0 et la valeur existe déjà pour $lang, $contentID, on met à jour .\n");
            } else {
                $stmt = $pdo->prepare("INSERT INTO translations (lang, `keyId`, value) VALUES (?, ?, ?)");
                $stmt->execute([$lang, $contentID, $message]);
                error_log("abtest 0 et value n'existe pas, Insertion pour nouvelle combinaison $lang, $contentID.\n");
            }
        }
    
        return $contentID; // Retourner la valeur de la clé
    } catch (Exception $e) {
        error_log("Error in addContent with key: $key, message: $message, lang: $lang. Error message: " . $e->getMessage() . "\n");
            return false; // Retourner false ou gérer l'erreur comme vous préférez
        }
    }
    

    public static function affectPageContent($textKey, $pageId, $contType, $abtest)
    {
        $pdo = Admin::db(); 

        $sql1 = "INSERT INTO pageContents (textKey, pageName, contType, abtest) VALUES ('$textKey', '$pageId', '$contType', '$abtest')";

        $stmt = $pdo->prepare("INSERT INTO pageContents (textKey, pageName, contType, abtest) VALUES (?, ?, ?, ?)");
        $stmt->execute([$textKey, $pageId, $contType, $abtest]);

        return $pdo->lastInsertId(); 
    }
/*
    public static function getPageContent($pageName, $lang) {
        try {
            $pdo = Admin::db();  // Assurez-vous que cette fonction retourne un objet PDO valide
            $sql1 = " 
            SET @row_num := 0; 
            SELECT * FROM (
                SELECT
                    pc.id,
                    pc.pageName,
                    pc.contType,
                    pc.abtest,
                    t.value,
                    @row_num := IF(@prev_page = pc.pageName AND @prev_lang = lang, @row_num + 1, 1) AS rowNum,
                    @prev_page := pc.pageName,
                    @prev_lang := lang
                FROM
                    pageContents pc
                    LEFT JOIN contents c ON c.id = pc.contentId
                    LEFT JOIN translations t ON t.keyId = c.id
                WHERE
                    pc.pageName ='$pageName' AND lang='$lang'
                ORDER BY
                    pc.id, lang -- Assurez-vous de trier par les colonnes qui définissent un nouvel enregistrement
            ) AS numberedResults
            WHERE
                rowNum = abtest;";
           // echo $sql1;
            error_log("key getPageContents\n $sql1 \n");
            // Préparation de la requête pour récupérer l'ID de page correspondant au nom de la page
    
            // Préparation de la requête pour récupérer les contenus de la page spécifiée et de la langue donnée
            $pdo->exec("SET @row_num := 0;");
            $pdo->exec("SET @prev_page := '';");  // Assurez-vous que cette variable est initialisée
            $pdo->exec("SET @prev_lang := '';");
            $sql = " 
            SELECT * FROM (
                SELECT
                    pc.id,c.key,
                    pc.pageName,
                    pc.contType,
                    pc.abtest,
                    t.value,
                    @row_num := IF(@prev_page = pc.pageName AND @prev_lang = lang, @row_num + 1, 1) AS rowNum,
                    @prev_page := pc.pageName,
                    @prev_lang := lang
                FROM
                    pageContents pc
                    LEFT JOIN contents c ON c.id = pc.contentId
                    LEFT JOIN translations t ON t.keyId = c.id
                WHERE
                    pc.pageName = ? AND lang= ?
                ORDER BY
                    pc.id, lang -- Assurez-vous de trier par les colonnes qui définissent un nouvel enregistrement
            ) AS numberedResults
            WHERE
                rowNum = abtest;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$pageName, $lang]);
    
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);// print_r($results);
            
            foreach ($results as $content) 
            { 
                $stmt = $pdo->prepare($sql)
            }
            error_log("datas pour pageName: $pageName".print_r($results, true)."\n",  3, "error_log.log");

            // Retourner les résultats
            return $results;
        } catch (PDOException $e) {
            // Enregistrer l'erreur et retourner false ou gérer l'erreur différemment selon les besoins de votre application
            error_log("Database error in getPageContents for pageName: $pageName, lang: $lang - " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            // Enregistrer les erreurs non liées à la base de données
            error_log("Error in getPageContents: " . $e->getMessage());
            return false;
        }
    }
    */
    
    public static function getPageContents($pageName, $userLang) {
        try {
            error_log("pagecontent");
            $pdo = \Models\Admin::db();  
            $sql = "SELECT pc.id, pc.contentId, pc.lastShownVersion, COUNT(t.keyId) as totalVersions
            FROM pageContents pc
            JOIN contents c ON c.id = pc.contentId
            JOIN translations t ON t.keyId = c.id
            WHERE pc.pageName =  '$pageName' AND t.lang = '$userLang'
            GROUP BY pc.contentId";
            error_log("sql $sql");

            $stmt = $pdo->prepare("
            SELECT pc.id, pc.contentId, pc.lastShownVersion, COUNT(t.keyId) as totalVersions
            FROM pageContents pc
            JOIN contents c ON c.id = pc.contentId
            JOIN translations t ON t.keyId = c.id
            WHERE pc.pageName = ? AND t.lang = ?
            GROUP BY pc.id, pc.contentId, pc.lastShownVersion
            
            ");

             // Enregistrer les erreurs non liées à la base de données
           //  error_log("sl: $sql");
            $stmt->execute([$pageName, $userLang]);
            $contents = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("pagecontent ".print_r($contents, true));

    
            foreach ($contents as $content) {
                $nextVersion = ($content['lastShownVersion'] + 1) % $content['totalVersions'];
                error_log("nextVersion: $nextVersion");

                if ($nextVersion == 0) { $nextVersion = 1; } // Assurer que la version commence à 1
                error_log("nextVersion: $nextVersion");
    
                // Mise à jour de la version affichée pour ce contenu
                $updateStmt = $pdo->prepare("UPDATE pageContents SET lastShownVersion = ? WHERE id = ?");
                $updateStmt->execute([$nextVersion, $content['contentId']]);
    
                error_log("sql UPDATE pageContents SET lastShownVersion = $nextVersion WHERE id = '".$content['contentId']."'\n" );

                // Récupérer le contenu réel à afficher
                $contentStmt = $pdo->prepare("SELECT t.* FROM translations t WHERE t.keyId = ? AND t.lang = ? AND t.value = ?");
                error_log("sql SELECT t.* FROM translations t WHERE t.keyId = " .$content['contentId']." AND t.lang = '$userLang' AND t.value =  $nextVersion \n" );

                $contentStmt->execute([$content['contentId'], $userLang, $nextVersion]);
                $contentDetails = $contentStmt->fetchAll(\PDO::FETCH_ASSOC);
    
                // Ici, vous pouvez logiquement traiter ou renvoyer le contenu
                error_log( "Contenu à afficher pour le contenu ID " . $content['contentId'] . ": " . json_encode($contentDetails) );
            }
    
            return true;
        } catch (PDOException $e) {
            error_log("Database error in getPageContent: " . $e->getMessage());
            return false;
        }
    }
    
}

?>
