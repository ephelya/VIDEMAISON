<?php
namespace Models;
use \Models\Admin;

class DreamProcessor {
    protected $db; // Instance de connexion à la base de données
    protected $openAiClient; // Client pour l'API OpenAI
    protected $dalleClient; // Client pour l'API DALL-E

    public function __construct($db, $openAiClient, $dalleClient) {
        $this->db = $db;
        $this->openAiClient = $openAiClient;
        $this->dalleClient = $dalleClient;
    }

    public function processAvatar($userId, $photoPath) {
        // Enregistrer ou mettre à jour l'avatar dans la base de données
        $this->db->insertOrUpdate('avatars', ['user_id' => $userId, 'path' => $photoPath, 'state' => 'completed']);
        // Vérifier s'il y a des rêves en attente pour cet utilisateur
        $this->checkAndProcessDreams($userId);
    }

    public function processDream($userId, $dreamDescription) {
        // Vérifier si l'avatar est prêt
        $avatar = $this->db->select('avatars', ['user_id' => $userId, 'state' => 'completed']);
        if (!$avatar) {
            $this->db->insert('dreams', ['user_id' => $userId, 'description' => $dreamDescription, 'state' => 'pending']);
            return;
        }

        // Enregistrer la description du rêve
        $this->db->insertOrUpdate('dreams', ['user_id' => $userId, 'description' => $dreamDescription, 'state' => 'completed']);
        // Vérifier si les images finales peuvent être générées
        $this->checkAndGenerateImages($userId);
    }

    public function checkAndProcessDreams($userId) {
        $avatar = $this->db->select('avatars', ['user_id' => $userId, 'state' => 'completed']);
        if ($avatar) {
            $pendingDreams = $this->db->select('dreams', ['user_id' => $userId, 'state' => 'pending']);
            foreach ($pendingDreams as $dream) {
                // Mettre à jour le statut du rêve et générer le prompt
                $prompt = $this->openAiClient->generateText($dream['description']);
                $this->db->update('dreams', ['state' => 'completed', 'prompt' => $prompt], ['id' => $dream['id']]);
                // Vérifier et générer les images finales
                $this->checkAndGenerateImages($userId);
            }
        }
    }

    public function checkAndGenerateImages($userId) {
        $dream = $this->db->select('dreams', ['user_id' => $userId, 'state' => 'completed']);
        if (!$dream) {
            return;
        }

        $existingImages = $this->db->select('images', ['dream_id' => $dream['id'], 'state' => 'completed']);
        if ($existingImages) {
            return; // Les images sont déjà générées pour ce rêve
        }

        // Générer les images finales
        $generatedImages = $this->dalleClient->generateImage($dream['prompt']);
        foreach ($generatedImages as $image) {
            $this->db->insert('images', ['dream_id' => $dream['id'], 'path' => $image, 'state' => 'completed']);
        }
    }
}

?>