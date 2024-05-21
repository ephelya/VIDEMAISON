<?php
    namespace Controllers;

    use \Models\OpenAIClient; 
    use \Models\Page;
    use \Utils\Forms;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    class AdminController {
        public static function getdefaultData(){     
            $categories = \Models\Produit::listArticlesWithCategories();
            //print_r($categories);
            $pageData = [
                'home' => HOME, // Supposons que vous voulez aussi passer d'autres données
                'categories' => $categories // Données hiérarchiques des catégories avec les articles
            ];     
            $updatebutton = self::formupdate();             
            $uploadbutton = self::formupload();             
            $pageData["active_section"] = "admin_def.twig";
            $pageData["page"]="admin";     
            $pageData["uploadbutton"]= $uploadbutton;     
            $pageData["updatebutton"]= $updatebutton;     


            //print_r ($pageData);
            


            return ($pageData);
        }

        public static function formupdate()
        {   
            $formconfig = [
                'formId'   =>  "formupdate",
                'size' => "large",
                'method' => "post",
                'float' => 1,
                'label' => "down",
                'trait' => "#",
                'fields' => [
                    ["value" => "Mettre à jour", 'class' => 'update',  "id" => "update", 'type' => 'submit'],
                ],
                "script" => "<script>document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('formupdate').onsubmit = function(event) {
                        event.preventDefault(); // Empêcher le rechargement de la page
                        var formData = new FormData(); // Création d'un objet FormData
                        formData.append('update', 1);
                        // Création et envoi de la requête AJAX
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', " . json_encode(HOME . "api/update_products") . ", true);
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                console.log(xhr.responseText);
                            } else {
                                alert('Erreur lors de l\'update.');
                            }
                        };
                        xhr.send(formData); // Envoyer la requête avec le fichier
                    };
                });</script>
                "
            ];
    
            if ($_ENV["ENV"] != "local")
           { return Forms::generateForm($formconfig); }
        }

        public static function formupload()
        {   
            $formconfig = [
                'formId'   =>  "formId",
                'size' => "large",
                'method' => "post",
                'float' => 1,
                'label' => "down",
                'trait' => "#",
                'fields' => [
                    ["value" => "Mettre à jour", 'class' => 'update',  "id" => "update", 'type' => 'submit'],
                ],
                "script" => "<script>document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('formId').onsubmit = function(event) {
                        console.log('update');
                        event.preventDefault(); // Empêcher le rechargement de la page
                        var formData = new FormData(); // Création d'un objet FormData
                        formData.append('upload', 1);
                        // Création et envoi de la requête AJAX
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', " . json_encode(HOME . "api/getproducts") . ", true);
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                console.log(xhr.responseText);
                            } else {
                                alert('Erreur lors de l\'update.');
                            }
                        };
                        xhr.send(formData); // Envoyer la requête avec le fichier
                    };
                });</script>
                "
            ];
            if ($_ENV["ENV"] == "local")
            {return Forms::generateForm($formconfig);   } 
        }

        public static function getsiteData(){     
            $pageData["active_section"] = "site.twig";
            $pageData["page"]=$pageName = "admin";    
            $pageData["forms"] = ["upload" => \Models\Pages::addcontent_form()];
            //$combinedData = array_merge($baseData, $pageData);
            //print_r($pageData);

            return ($pageData);
        }

    }

?>
