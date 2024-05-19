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
            $uploadbutton = self::formupload();             
            $pageData["active_section"] = "admin_def.twig";
            $pageData["page"]="admin";     
            $pageData["uploadbutton"]= $uploadbutton;     


            //print_r ($pageData);
            


            return ($pageData);
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
    
            return Forms::generateForm($formconfig);    
        }

        public static function getpreconisationsData(){    
            $pageData["active_section"] = "preconisations.twig";
            $pageData["page"]="admin";                  
            $pageData["precos"] = array("todos" => "prospection");
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getmembresData(){     
            $pageData["active_section"] = "membres.twig";
            $pageData["page"]="admin";                  
            $pageData["membres"] = \Models\Membres::listusers();
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getrapportssData(){     
            $pageData["active_section"] = "rapports.twig";
            $pageData["page"]="admin";                  
            $pageData["precos"] = array("todos" => "liste todos");
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getemailingData(){     
            $pageData["active_section"] = "emailing.twig";
            $pageData["page"]="admin";                  
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getparamsData(){     
            $pageData["active_section"] = "params.twig";
            $pageData["page"]="admin";                  
            $pageData["langs"] = \Models\Admin::languageList();
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getstatsData(){     
            $pageData["active_section"] = "stats.twig";
            $pageData["page"]="admin";                  
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getadvertData(){     
            $pageData["active_section"] = "advert.twig";
            $pageData["page"]="admin";                  
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getseoData(){     
            $pageData["active_section"] = "seo.twig";
            $pageData["page"]="admin";                  
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getpressData(){      
            $pageData["active_section"] = "press.twig";
            $pageData["page"]="admin";                  
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getsuiviData(){     
            $pageData["active_section"] = "suivi.twig";
            $pageData["page"]="admin";                  
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getTresoData(){     
            $pageData["active_section"] = "treso.twig";
            $pageData["page"]="admin";                  
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getcomptaData(){     
            $pageData["active_section"] = "compta.twig";
            $pageData["page"]="admin";                  
            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
        public static function getsiteData(){     
            $pageData["active_section"] = "site.twig";
            $pageData["page"]=$pageName = "admin";    
            $pageData["forms"] = ["upload" => \Models\Pages::addcontent_form()];
            //$combinedData = array_merge($baseData, $pageData);
            //print_r($pageData);

            return ($pageData);
        }

        public static function getpromptsData(){     
            $pageData["active_section"] = "prompts.twig";
            $pageData["page"]="admin";    
            $pageData["forms"] = ["adprompt" => \Models\Prompts::addPromptForm()];

            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }

        public static function getcssData(){     
            $pageData["active_section"] = "css.twig";
            $pageData["page"]="admin";    
            $pageData["forms"] = ["addcss" => \Models\CSS::editCSSForm()];

            $baseData = self::getdefaultData();
            $combinedData = array_merge($baseData, $pageData);

            return ($combinedData);
        }
    }

?>
