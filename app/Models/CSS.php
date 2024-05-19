<?php
namespace Models;

use \PDO;
use \Utils\Forms;

class CSS {
    private $pdo;

    public function __construct() {
        // Connexion à la base de données
        $this->pdo = Admin::db();
    }

    // Méthode pour créer une nouvelle propriété CSS
    public function create($ident, $description, $value) {
        error_log("create $ident, $description, $value\n");

        $sql0 = "INSERT INTO CSS (ident, `description`, `value`) VALUES ('$ident', '$description', '$value')";
        error_log("create $sql0\n");

        $sql = "INSERT INTO CSS (ident, `description`, `value`) VALUES (:ident, :description, :value)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':ident', $ident);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':value', $value);
        return $stmt->execute();
    }

    // Méthode pour récupérer une propriété CSS par son identifiant
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM CSS WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode pour mettre à jour une propriété CSS existante
    public function update($id, $ident, $description, $value) {
        $stmt = $this->pdo->prepare("UPDATE CSS SET ident = :ident, description = :description, value = :value WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ident', $ident);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':value', $value);
        return $stmt->execute();
    }

    // Méthode pour supprimer une propriété CSS par son identifiant
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM CSS WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Méthode pour récupérer toutes les propriétés CSS
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM CSS");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour générer le fichier SCSS
    public function generateSCSS($filename) {
        $css_properties = $this->getAll();
        $scss_content = '';
        foreach ($css_properties as $property) {
            $scss_content .= '$' . $property['ident'] . ': ' . $property['value'] . ";\n";
        }
        file_put_contents($filename, $scss_content);
    }

    // Méthode pour récupérer une propriété CSS par son identifiant
    public function getByIdent($ident) {
        $stmt = $this->pdo->prepare("SELECT * FROM CSS WHERE ident = :ident");
        $stmt->bindParam(':ident', $ident);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
        // FORMS //
        public static function editCSSForm() {
            $editCSSForm = [
                'formId'   =>  'editcss_form',
                'method' => "post",
                'size' => "large",
                'float' => 1,
                'label' => "down",
                'trait' => "#",
                'fields' => [
                    ['label' => 'Identifiant', "id" => "ident", 'name' => "ident", 'type' => 'text', 'placeholder' => "Identifiant"],
                    ['label' => 'Description', "id" => "description", 'name' => "description", 'type' => 'text', "placeholder" => "Description"],
                    ['label' => 'Valeur', "id" => "value", 'name' => "value", 'type' => 'text', 'placeholder' => "Valeur"],
                    ['label' => '', "id" => "addcss", 'name' => "addcss", "value" => "Ajouter",  'type' => 'submit'],
                ],
                "script" => "<script>
                document.addEventListener('DOMContentLoaded', function () {
                    $('#editcss_form').submit(function(event) {
                        event.preventDefault(); // Empêcher le rechargement de la page

                        var postData = {
                            ident: $('#ident').val(),
                            description: $('#description').val(),
                            value: $('#value').val()
                        };

                        $.ajax({
                            type: 'POST',
                            url: " . json_encode(HOME . "api/addCSSData") . ",
                            data: postData,
                            success: function(data) {
                                alert('CSS ajouté avec succès!');
                            },
                            error: function() {
                                alert('Erreur lors de l\'ajout des données CSS.');
                            }
                        });
                    });
                });
            </script>"



                
            ];
            return Forms::generateForm($editCSSForm);
        }
    */

    public static function editCSSForm() {
        $addPromptForm = [
            'formId'   =>  'addprompt_form',
            'method' => "post",
            'size' => "large",
            'float' => 1,
            'label' => "down",
            'trait' => "#",
            'fields' => [
                ['label' => 'Identifiant', "id" => "ident", 'name' => "ident", 'type' => 'text', 'placeholder' => "Identifiant"],
                ['label' => 'Description', "id" => "description", 'name' => "description", 'type' => 'textarea', "msg" => "Contexte et usage du prompt"],
                ['label' => 'Prompt', "id" => "prompt", 'name' => "prompt", 'type' => 'textarea', 'msg' => "Prompt"],
                ['label' => 'Prompt', "id" => "prompt", 'name' => "submit", "value" => "",  'type' => 'submit'],
            ],
            "script" => "<script>document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('addprompt_form').onsubmit = function(event) {
                    event.preventDefault(); // Prevent the page from reloading
            
                    var formData = new FormData(); // Create a new FormData object
                    formData.append('ident', document.querySelector('[name=\"ident\"]').value);
                    formData.append('description', document.querySelector('[name=\"description\"]').value);
                    formData.append('prompt', document.querySelector('[name=\"prompt\"]').value);
            
                    // Create and send the AJAX request
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', " . json_encode(HOME . "api/addPrompt") . ", true);
                    xhr.onload = function () {
                        console.log('resup ',xhr);
                        if (xhr.status === 200) {
                            alert('Prompt added successfully!');
                            console.log(xhr.responseText);
                        } else {
                            alert('Error adding prompt.');
                        }
                    };
                    xhr.send(formData); // Send the request with the data
                };
            });</script>
            "
            
        ];
        return Forms::generateForm($addPromptForm);
    }

}
?>
