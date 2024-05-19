<?php
namespace Models;
use \Models\Admin;
use Utils\Forms;

class Prompts {

    public function __construct($data) {
        // Iterate over each key-value pair in the data array to initialize class properties
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function getPrompt($id)
    { 
        $pdo = \Models\Admin::db(); // Get database connection from the Admin model
        $stmt = $pdo->prepare('SELECT * FROM Prompts WHERE id = :id');
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $prompt = $stmt->fetch(\PDO::FETCH_OBJ);

        // Return a new instance of the Prompts class using the fetched data
        return new Prompts($prompt);
    }

    public static function addPrompt($data)
    {
        $pdo = \Models\Admin::db();
    
        // Préparation de la requête SQL pour insérer seulement si un prompt identique n'existe pas déjà
        $query = "INSERT INTO Prompts (ident, description, prompt)
                  SELECT :ident, :description, :prompt
                  WHERE NOT EXISTS (
                      SELECT 1 FROM Prompts
                      WHERE ident = :ident AND description = :description AND prompt = :prompt
                  )";
    
        // Préparation de la déclaration avec la requête
        $stmt = $pdo->prepare($query);
    
        // Liaison des paramètres pour l'insertion et la vérification
        $stmt->bindParam(':ident', $data['ident']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':prompt', $data['prompt']);
    
        // Exécution de la requête
        $stmt->execute();
    
        // Vérification si la ligne a été insérée
        if ($stmt->rowCount() > 0) {
            return $pdo->lastInsertId(); // Renvoie l'ID de l'entrée insérée
        } else {
            return "No insertion performed, prompt already exists"; // Ou renvoyer false ou un code d'erreur spécifique
        }
    }
    

    public static function deletePrompt($id)
    {
        $pdo = \Models\Admin::db();
        $stmt = $pdo->prepare('DELETE FROM Prompts WHERE id = :id');
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function editPrompt($id, $data)
    {
        $pdo = \Models\Admin::db();
        $stmt = $pdo->prepare('UPDATE Prompts SET ident = :ident, description = :description, prompt = :prompt WHERE id = :id');
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':ident', $data['ident']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':prompt', $data['prompt']);
        return $stmt->execute();
    }

    // FORMS //
    public static function addPromptForm() {
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
